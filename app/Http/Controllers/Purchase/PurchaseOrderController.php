<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class PurchaseOrderController extends BaseController
{
    /**
     * 発注書一覧を表示
     */
    public function index(Request $request)
    {
        $search_options = $request->all();

        $query = DB::table('t_purchase_orders')
            ->leftJoin('m_suppliers', 't_purchase_orders.supplier_id', '=', 'm_suppliers.id')
            ->select(
                't_purchase_orders.*',
                'm_suppliers.supplier_name',
                'm_suppliers.supplier_code'
            );

        // 検索条件: 発注番号
        if (!empty($search_options['order_number'])) {
            $query->where('t_purchase_orders.order_number', 'LIKE', '%' . $search_options['order_number'] . '%');
        }

        // 検索条件: 仕入先
        if (!empty($search_options['supplier_id'])) {
            $query->where('t_purchase_orders.supplier_id', $search_options['supplier_id']);
        }

        // 検索条件: ステータス
        if (!empty($search_options['status'])) {
            $query->where('t_purchase_orders.status', $search_options['status']);
        }

        // 検索条件: 発注日
        if (!empty($search_options['order_date_from'])) {
            $query->where('t_purchase_orders.order_date', '>=', $search_options['order_date_from']);
        }
        if (!empty($search_options['order_date_to'])) {
            $query->where('t_purchase_orders.order_date', '<=', $search_options['order_date_to']);
        }

        $orders = $query->orderBy('t_purchase_orders.created_at', 'desc')->paginate(20);

        // 仕入先リスト
        $suppliers = DB::table('m_suppliers')->where('is_active', 1)->orderBy('supplier_name')->get();

        // 統計情報
        $stats = [
            'draft_count' => DB::table('t_purchase_orders')->where('status', 'draft')->count(),
            'pending_count' => DB::table('t_purchase_orders')->where('status', 'pending')->count(),
            'ordered_count' => DB::table('t_purchase_orders')->where('status', 'ordered')->count(),
            'received_count' => DB::table('t_purchase_orders')->where('status', 'received')->count(),
        ];

        return view('purchase.order_list', compact('orders', 'suppliers', 'stats'));
    }

    /**
     * 発注書作成画面を表示
     */
    public function create()
    {
        $goods = DB::table('t_goods')
            ->leftJoin('m_categories', 't_goods.category_id', '=', 'm_categories.id')
            ->leftJoin(DB::raw('(SELECT goods_id, SUM(quantity) as total_stock FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
            ->select(
                't_goods.id',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.goods_price',
                't_goods.reorder_quantity',
                'm_categories.category_name',
                DB::raw('COALESCE(inv.total_stock, 0) as current_stock')
            )
            ->where('t_goods.delete_flg', 0)
            ->orderBy('t_goods.goods_number')
            ->get();

        $suppliers = DB::table('m_suppliers')->where('is_active', 1)->orderBy('supplier_name')->get();

        return view('purchase.order_create', compact('goods', 'suppliers'));
    }

    /**
     * 発注書を保存
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:m_suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
            'items.*.goods_id' => 'required|exists:t_goods,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $orderNumber = 'PO-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $orderId = DB::table('t_purchase_orders')->insertGetId([
                'order_number' => $orderNumber,
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'status' => 'draft',
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                DB::table('t_purchase_order_details')->insert([
                    'purchase_order_id' => $orderId,
                    'goods_id' => $item['goods_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('purchase_order_list')
                ->with('success', '発注書を作成しました。（発注番号: ' . $orderNumber . '）');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '発注書の作成に失敗しました: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 発注書編集画面を表示
     */
    public function edit($id)
    {
        $order = DB::table('t_purchase_orders')
            ->leftJoin('m_suppliers', 't_purchase_orders.supplier_id', '=', 'm_suppliers.id')
            ->select('t_purchase_orders.*', 'm_suppliers.supplier_name', 'm_suppliers.supplier_code')
            ->where('t_purchase_orders.id', $id)
            ->first();

        if (!$order) {
            return redirect()->route('purchase_order_list')->with('error', '発注書が見つかりません。');
        }

        $orderDetails = DB::table('t_purchase_order_details')
            ->leftJoin('t_goods', 't_purchase_order_details.goods_id', '=', 't_goods.id')
            ->select(
                't_purchase_order_details.*',
                't_goods.goods_number',
                't_goods.goods_name'
            )
            ->where('t_purchase_order_details.purchase_order_id', $id)
            ->get();

        $goods = DB::table('t_goods')
            ->where('delete_flg', 0)
            ->orderBy('goods_number')
            ->get();

        $suppliers = DB::table('m_suppliers')->where('is_active', 1)->orderBy('supplier_name')->get();

        return view('purchase.order_edit', compact('order', 'orderDetails', 'goods', 'suppliers'));
    }

    /**
     * 発注書を更新
     */
    public function update(Request $request, $id)
    {
        $order = DB::table('t_purchase_orders')->where('id', $id)->first();
        
        if (!$order) {
            return redirect()->route('purchase_order_list')->with('error', '発注書が見つかりません。');
        }

        if ($order->status !== 'draft') {
            return redirect()->route('purchase_order_edit', ['id' => $id])
                ->with('error', '下書き以外の発注書は編集できません。');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:m_suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
            'items.*.goods_id' => 'required|exists:t_goods,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            DB::table('t_purchase_orders')
                ->where('id', $id)
                ->update([
                    'supplier_id' => $validated['supplier_id'],
                    'order_date' => $validated['order_date'],
                    'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                    'total_amount' => $totalAmount,
                    'notes' => $validated['notes'] ?? null,
                    'updated_at' => now(),
                ]);

            // 既存の明細を削除して再作成
            DB::table('t_purchase_order_details')->where('purchase_order_id', $id)->delete();

            foreach ($validated['items'] as $item) {
                DB::table('t_purchase_order_details')->insert([
                    'purchase_order_id' => $id,
                    'goods_id' => $item['goods_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('purchase_order_edit', ['id' => $id])
                ->with('success', '発注書を更新しました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '発注書の更新に失敗しました: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 発注書のステータスを更新
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,pending,ordered,received,cancelled',
        ]);

        $order = DB::table('t_purchase_orders')->where('id', $id)->first();
        
        if (!$order) {
            return redirect()->route('purchase_order_list')->with('error', '発注書が見つかりません。');
        }

        try {
            DB::beginTransaction();

            $updateData = [
                'status' => $validated['status'],
                'updated_at' => now(),
            ];

            // ステータスに応じて日付を更新
            if ($validated['status'] === 'ordered') {
                $updateData['ordered_date'] = now();
            } elseif ($validated['status'] === 'received') {
                $updateData['received_date'] = now();
                
                // 入荷時に在庫を更新
                $details = DB::table('t_purchase_order_details')
                    ->where('purchase_order_id', $id)
                    ->get();

                foreach ($details as $detail) {
                    // 既存の在庫を探す
                    $inventory = DB::table('t_inventories')
                        ->where('goods_id', $detail->goods_id)
                        ->where('warehouse_id', 1) // デフォルト倉庫
                        ->whereNull('lot_number')
                        ->whereNull('serial_number')
                        ->first();

                    if ($inventory) {
                        DB::table('t_inventories')
                            ->where('id', $inventory->id)
                            ->update([
                                'quantity' => $inventory->quantity + $detail->quantity,
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('t_inventories')->insert([
                            'goods_id' => $detail->goods_id,
                            'warehouse_id' => 1,
                            'quantity' => $detail->quantity,
                            'reserved_quantity' => 0,
                            'status' => 'normal',
                            'received_date' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // 入庫履歴を記録
                    DB::table('t_stock_movements')->insert([
                        'goods_id' => $detail->goods_id,
                        'warehouse_id' => 1,
                        'movement_type' => 'in',
                        'quantity' => $detail->quantity,
                        'before_quantity' => $inventory->quantity ?? 0,
                        'after_quantity' => ($inventory->quantity ?? 0) + $detail->quantity,
                        'reference_type' => 'purchase_order',
                        'reference_id' => $id,
                        'notes' => '発注書入荷: ' . $order->order_number,
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::table('t_purchase_orders')
                ->where('id', $id)
                ->update($updateData);

            DB::commit();

            $statusLabels = [
                'draft' => '下書き',
                'pending' => '承認待ち',
                'ordered' => '発注済み',
                'received' => '入荷完了',
                'cancelled' => 'キャンセル',
            ];

            return redirect()->route('purchase_order_edit', ['id' => $id])
                ->with('success', 'ステータスを「' . $statusLabels[$validated['status']] . '」に更新しました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'ステータスの更新に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * 発注書を削除
     */
    public function destroy($id)
    {
        $order = DB::table('t_purchase_orders')->where('id', $id)->first();
        
        if (!$order) {
            return redirect()->route('purchase_order_list')->with('error', '発注書が見つかりません。');
        }

        if ($order->status !== 'draft') {
            return redirect()->route('purchase_order_list')
                ->with('error', '下書き以外の発注書は削除できません。');
        }

        try {
            DB::beginTransaction();

            DB::table('t_purchase_order_details')->where('purchase_order_id', $id)->delete();
            DB::table('t_purchase_orders')->where('id', $id)->delete();

            DB::commit();

            return redirect()->route('purchase_order_list')
                ->with('success', '発注書を削除しました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '発注書の削除に失敗しました: ' . $e->getMessage());
        }
    }
}
