<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class OrderSuggestionController extends BaseController
{
    /**
     * 発注提案一覧を表示
     * Display order suggestion list based on inventory levels
     */
    public function index(Request $request)
    {
        // 検索条件を取得
        $search_options = $request->all();

        // 発注が必要な商品を取得（在庫数が最低在庫数以下）
        $query = DB::table('t_goods')
            ->leftJoin('m_categories', 't_goods.category_id', '=', 'm_categories.id')
            ->leftJoin('m_suppliers', 't_goods.supplier_id', '=', 'm_suppliers.id')
            ->leftJoin(DB::raw('(SELECT goods_id, SUM(quantity) as total_stock, SUM(available_quantity) as total_available FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
            ->select(
                't_goods.id',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.goods_price',
                't_goods.min_stock_level',
                't_goods.reorder_point',
                't_goods.reorder_quantity',
                'm_categories.category_name',
                'm_suppliers.supplier_name',
                'm_suppliers.id as supplier_id',
                DB::raw('COALESCE(inv.total_stock, 0) as current_stock'),
                DB::raw('COALESCE(inv.total_available, 0) as available_stock'),
                DB::raw('GREATEST(0, t_goods.reorder_quantity - COALESCE(inv.total_stock, 0)) as suggested_quantity')
            )
            ->where('t_goods.delete_flg', 0)
            ->where(function($q) {
                $q->whereRaw('COALESCE(inv.total_stock, 0) <= t_goods.min_stock_level')
                  ->orWhereRaw('COALESCE(inv.total_available, 0) <= t_goods.reorder_point');
            });

        // 検索条件: 商品番号
        if (!empty($search_options['goods_number'])) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $search_options['goods_number'] . '%');
        }

        // 検索条件: 商品名
        if (!empty($search_options['goods_name'])) {
            $query->where('t_goods.goods_name', 'LIKE', '%' . $search_options['goods_name'] . '%');
        }

        // 検索条件: カテゴリ
        if (!empty($search_options['category_id'])) {
            $query->where('t_goods.category_id', $search_options['category_id']);
        }

        // 検索条件: 仕入先
        if (!empty($search_options['supplier_id'])) {
            $query->where('t_goods.supplier_id', $search_options['supplier_id']);
        }

        // 検索条件: 緊急度
        if (!empty($search_options['urgency'])) {
            switch ($search_options['urgency']) {
                case 'critical':
                    $query->whereRaw('COALESCE(inv.total_available, 0) = 0');
                    break;
                case 'high':
                    $query->whereRaw('COALESCE(inv.total_available, 0) > 0')
                          ->whereRaw('COALESCE(inv.total_available, 0) <= t_goods.min_stock_level');
                    break;
                case 'medium':
                    $query->whereRaw('COALESCE(inv.total_available, 0) > t_goods.min_stock_level')
                          ->whereRaw('COALESCE(inv.total_available, 0) <= t_goods.reorder_point');
                    break;
            }
        }

        // ソート
        $sort_by = $request->get('sort_by', 'available_stock');
        $sort_direction = $request->get('sort_direction', 'asc');
        
        switch ($sort_by) {
            case 'goods_number':
                $query->orderBy('t_goods.goods_number', $sort_direction);
                break;
            case 'available_stock':
                $query->orderBy('available_stock', $sort_direction);
                break;
            case 'suggested_quantity':
                $query->orderBy('suggested_quantity', $sort_direction);
                break;
            default:
                $query->orderBy('available_stock', 'asc');
        }

        // ページネーション
        $suggestions = $query->paginate(20);

        // カテゴリリスト取得
        $categories = DB::table('m_categories')
            ->where('is_active', 1)
            ->orderBy('category_name')
            ->get();

        // 仕入先リスト取得
        $suppliers = DB::table('m_suppliers')
            ->where('is_active', 1)
            ->orderBy('supplier_name')
            ->get();

        // 統計情報
        $stats = [
            'critical_count' => DB::table('t_goods')
                ->leftJoin(DB::raw('(SELECT goods_id, SUM(available_quantity) as total_available FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
                ->where('t_goods.delete_flg', 0)
                ->whereRaw('COALESCE(inv.total_available, 0) = 0')
                ->count(),
            'high_count' => DB::table('t_goods')
                ->leftJoin(DB::raw('(SELECT goods_id, SUM(available_quantity) as total_available FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
                ->where('t_goods.delete_flg', 0)
                ->whereRaw('COALESCE(inv.total_available, 0) > 0')
                ->whereRaw('COALESCE(inv.total_available, 0) <= t_goods.min_stock_level')
                ->count(),
            'medium_count' => DB::table('t_goods')
                ->leftJoin(DB::raw('(SELECT goods_id, SUM(available_quantity) as total_available FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
                ->where('t_goods.delete_flg', 0)
                ->whereRaw('COALESCE(inv.total_available, 0) > t_goods.min_stock_level')
                ->whereRaw('COALESCE(inv.total_available, 0) <= t_goods.reorder_point')
                ->count(),
            'total_suggestions' => $suggestions->total(),
        ];

        return view('purchase.suggestion', compact('suggestions', 'categories', 'suppliers', 'stats'));
    }

    /**
     * 発注提案から発注書を作成
     */
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'goods_ids' => 'required|array',
            'goods_ids.*' => 'exists:t_goods,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1',
            'supplier_id' => 'required|exists:m_suppliers,id',
        ]);

        try {
            DB::beginTransaction();

            // 発注書を作成
            $orderNumber = 'PO-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $orderId = DB::table('t_purchase_orders')->insertGetId([
                'order_number' => $orderNumber,
                'supplier_id' => $validated['supplier_id'],
                'order_date' => now(),
                'status' => 'draft',
                'total_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $totalAmount = 0;
            foreach ($validated['goods_ids'] as $index => $goodsId) {
                $goods = DB::table('t_goods')->where('id', $goodsId)->first();
                $quantity = $validated['quantities'][$index];
                $subtotal = $goods->goods_price * $quantity;
                $totalAmount += $subtotal;

                DB::table('t_purchase_order_details')->insert([
                    'purchase_order_id' => $orderId,
                    'goods_id' => $goodsId,
                    'quantity' => $quantity,
                    'unit_price' => $goods->goods_price,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 合計金額を更新
            DB::table('t_purchase_orders')
                ->where('id', $orderId)
                ->update(['total_amount' => $totalAmount, 'updated_at' => now()]);

            DB::commit();

            return redirect()->route('purchase_order_edit', ['id' => $orderId])
                ->with('success', '発注書を作成しました。（発注番号: ' . $orderNumber . '）');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '発注書の作成に失敗しました: ' . $e->getMessage())->withInput();
        }
    }
}
