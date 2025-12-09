<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class OrderTrackingController extends BaseController
{
    /**
     * 発注状況追跡画面を表示
     */
    public function index(Request $request)
    {
        $search_options = $request->all();

        $query = DB::table('t_purchase_orders')
            ->leftJoin('m_suppliers', 't_purchase_orders.supplier_id', '=', 'm_suppliers.id')
            ->select(
                't_purchase_orders.*',
                'm_suppliers.supplier_name',
                'm_suppliers.supplier_code',
                'm_suppliers.contact_email',
                'm_suppliers.contact_phone'
            )
            ->whereIn('t_purchase_orders.status', ['pending', 'ordered']);

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

        // 検索条件: 納期
        if (!empty($search_options['delivery_status'])) {
            $today = date('Y-m-d');
            switch ($search_options['delivery_status']) {
                case 'overdue':
                    $query->where('t_purchase_orders.expected_delivery_date', '<', $today);
                    break;
                case 'due_today':
                    $query->where('t_purchase_orders.expected_delivery_date', '=', $today);
                    break;
                case 'due_this_week':
                    $query->whereBetween('t_purchase_orders.expected_delivery_date', [$today, date('Y-m-d', strtotime('+7 days'))]);
                    break;
            }
        }

        $orders = $query->orderBy('t_purchase_orders.expected_delivery_date', 'asc')
                       ->orderBy('t_purchase_orders.order_date', 'asc')
                       ->paginate(20);

        // 各発注の明細を取得
        foreach ($orders as $order) {
            $order->details = DB::table('t_purchase_order_details')
                ->leftJoin('t_goods', 't_purchase_order_details.goods_id', '=', 't_goods.id')
                ->select(
                    't_purchase_order_details.*',
                    't_goods.goods_number',
                    't_goods.goods_name'
                )
                ->where('t_purchase_order_details.purchase_order_id', $order->id)
                ->get();
        }

        // 仕入先リスト
        $suppliers = DB::table('m_suppliers')->where('is_active', 1)->orderBy('supplier_name')->get();

        // 統計情報
        $today = date('Y-m-d');
        $stats = [
            'pending_count' => DB::table('t_purchase_orders')->where('status', 'pending')->count(),
            'ordered_count' => DB::table('t_purchase_orders')->where('status', 'ordered')->count(),
            'overdue_count' => DB::table('t_purchase_orders')
                ->whereIn('status', ['pending', 'ordered'])
                ->where('expected_delivery_date', '<', $today)
                ->count(),
            'due_today_count' => DB::table('t_purchase_orders')
                ->whereIn('status', ['pending', 'ordered'])
                ->where('expected_delivery_date', '=', $today)
                ->count(),
            'total_amount' => DB::table('t_purchase_orders')
                ->whereIn('status', ['pending', 'ordered'])
                ->sum('total_amount'),
        ];

        return view('purchase.tracking', compact('orders', 'suppliers', 'stats'));
    }

    /**
     * 発注詳細を表示
     */
    public function show($id)
    {
        $order = DB::table('t_purchase_orders')
            ->leftJoin('m_suppliers', 't_purchase_orders.supplier_id', '=', 'm_suppliers.id')
            ->select(
                't_purchase_orders.*',
                'm_suppliers.supplier_name',
                'm_suppliers.supplier_code',
                'm_suppliers.contact_email',
                'm_suppliers.contact_phone',
                'm_suppliers.address'
            )
            ->where('t_purchase_orders.id', $id)
            ->first();

        if (!$order) {
            return redirect()->route('purchase_tracking')->with('error', '発注書が見つかりません。');
        }

        $details = DB::table('t_purchase_order_details')
            ->leftJoin('t_goods', 't_purchase_order_details.goods_id', '=', 't_goods.id')
            ->select(
                't_purchase_order_details.*',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.image_path'
            )
            ->where('t_purchase_order_details.purchase_order_id', $id)
            ->get();

        // 発注履歴を取得
        $history = DB::table('t_purchase_order_history')
            ->where('purchase_order_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('purchase.tracking_detail', compact('order', 'details', 'history'));
    }

    /**
     * 入荷処理
     */
    public function receive(Request $request, $id)
    {
        $validated = $request->validate([
            'received_items' => 'required|array',
            'received_items.*.detail_id' => 'required|exists:t_purchase_order_details,id',
            'received_items.*.received_quantity' => 'required|integer|min:0',
            'warehouse_id' => 'required|exists:m_warehouses,id',
            'location_id' => 'nullable|exists:m_locations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order = DB::table('t_purchase_orders')->where('id', $id)->first();
        
        if (!$order) {
            return redirect()->route('purchase_tracking')->with('error', '発注書が見つかりません。');
        }

        if ($order->status !== 'ordered') {
            return redirect()->route('purchase_tracking_detail', ['id' => $id])
                ->with('error', '発注済みの発注書のみ入荷処理ができます。');
        }

        try {
            DB::beginTransaction();

            $allReceived = true;
            foreach ($validated['received_items'] as $item) {
                $detail = DB::table('t_purchase_order_details')->where('id', $item['detail_id'])->first();
                $receivedQuantity = $item['received_quantity'];

                if ($receivedQuantity > 0) {
                    // 在庫を更新
                    $inventory = DB::table('t_inventories')
                        ->where('goods_id', $detail->goods_id)
                        ->where('warehouse_id', $validated['warehouse_id'])
                        ->where(function($q) use ($validated) {
                            if (!empty($validated['location_id'])) {
                                $q->where('location_id', $validated['location_id']);
                            } else {
                                $q->whereNull('location_id');
                            }
                        })
                        ->whereNull('lot_number')
                        ->whereNull('serial_number')
                        ->first();

                    $beforeQuantity = $inventory ? $inventory->quantity : 0;

                    if ($inventory) {
                        DB::table('t_inventories')
                            ->where('id', $inventory->id)
                            ->update([
                                'quantity' => $inventory->quantity + $receivedQuantity,
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('t_inventories')->insert([
                            'goods_id' => $detail->goods_id,
                            'warehouse_id' => $validated['warehouse_id'],
                            'location_id' => $validated['location_id'] ?? null,
                            'quantity' => $receivedQuantity,
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
                        'warehouse_id' => $validated['warehouse_id'],
                        'location_id' => $validated['location_id'] ?? null,
                        'movement_type' => 'in',
                        'quantity' => $receivedQuantity,
                        'before_quantity' => $beforeQuantity,
                        'after_quantity' => $beforeQuantity + $receivedQuantity,
                        'reference_type' => 'purchase_order',
                        'reference_id' => $id,
                        'notes' => '発注入荷: ' . $order->order_number . ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // 入荷数量を更新
                    DB::table('t_purchase_order_details')
                        ->where('id', $item['detail_id'])
                        ->update([
                            'received_quantity' => DB::raw('COALESCE(received_quantity, 0) + ' . $receivedQuantity),
                            'updated_at' => now(),
                        ]);
                }

                // 全数入荷かチェック
                $updatedDetail = DB::table('t_purchase_order_details')->where('id', $item['detail_id'])->first();
                if (($updatedDetail->received_quantity ?? 0) < $updatedDetail->quantity) {
                    $allReceived = false;
                }
            }

            // 全数入荷なら完了ステータスに
            if ($allReceived) {
                DB::table('t_purchase_orders')
                    ->where('id', $id)
                    ->update([
                        'status' => 'received',
                        'received_date' => now(),
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return redirect()->route('purchase_tracking_detail', ['id' => $id])
                ->with('success', '入荷処理が完了しました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '入荷処理に失敗しました: ' . $e->getMessage());
        }
    }
}
