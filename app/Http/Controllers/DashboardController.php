<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    /**
     * 在庫管理ダッシュボードを表示
     */
    public function __invoke(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // ========== 主要KPI ==========
        
        // 総在庫数・総在庫金額
        $totalInventory = DB::table('t_inventories')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->where('t_goods.delete_flg', 0)
            ->selectRaw('SUM(t_inventories.quantity) as total_quantity, SUM(t_inventories.quantity * t_goods.goods_price) as total_value')
            ->first();

        // 総商品数
        $totalProducts = DB::table('t_goods')->where('delete_flg', 0)->count();

        // 在庫切れ商品数（available_quantity = 0）
        $outOfStock = DB::table('t_goods')
            ->leftJoin(DB::raw('(SELECT goods_id, SUM(available_quantity) as total_available FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
            ->where('t_goods.delete_flg', 0)
            ->where(function($q) {
                $q->whereNull('inv.total_available')
                  ->orWhere('inv.total_available', '<=', 0);
            })
            ->count();

        // 発注点以下の商品数
        $lowStock = DB::table('t_goods')
            ->leftJoin(DB::raw('(SELECT goods_id, SUM(available_quantity) as total_available FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
            ->where('t_goods.delete_flg', 0)
            ->whereNotNull('t_goods.reorder_point')
            ->whereRaw('COALESCE(inv.total_available, 0) <= t_goods.reorder_point')
            ->whereRaw('COALESCE(inv.total_available, 0) > 0')
            ->count();

        // 過剰在庫商品数
        $overStock = DB::table('t_goods')
            ->leftJoin(DB::raw('(SELECT goods_id, SUM(quantity) as total_quantity FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
            ->where('t_goods.delete_flg', 0)
            ->whereNotNull('t_goods.max_stock_level')
            ->whereRaw('COALESCE(inv.total_quantity, 0) > t_goods.max_stock_level')
            ->count();

        // 本日の入荷件数
        $todayInbound = DB::table('t_stock_movements')
            ->whereIn('movement_type', ['in', 'return'])
            ->whereDate('movement_date', $today)
            ->count();

        // 本日の出荷件数
        $todayOutbound = DB::table('t_stock_movements')
            ->whereIn('movement_type', ['out', 'transfer_out'])
            ->whereDate('movement_date', $today)
            ->count();

        $kpi = [
            'total_quantity' => $totalInventory->total_quantity ?? 0,
            'total_value' => $totalInventory->total_value ?? 0,
            'total_products' => $totalProducts,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'over_stock' => $overStock,
            'today_inbound' => $todayInbound,
            'today_outbound' => $todayOutbound,
        ];

        // ========== アラート情報 ==========
        
        // 未解決アラート
        $alerts = DB::table('t_stock_alerts')
            ->leftJoin('t_goods', 't_stock_alerts.goods_id', '=', 't_goods.id')
            ->where('t_stock_alerts.is_resolved', 0)
            ->select(
                't_stock_alerts.*',
                't_goods.goods_number',
                't_goods.goods_name'
            )
            ->orderBy('t_stock_alerts.created_at', 'desc')
            ->limit(10)
            ->get();

        // 有効期限切れ間近（7日以内）
        $expiringLots = DB::table('t_inventories')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_inventories.warehouse_id', '=', 'm_warehouses.id')
            ->where('t_goods.delete_flg', 0)
            ->where('t_inventories.quantity', '>', 0)
            ->whereNotNull('t_inventories.expiry_date')
            ->where('t_inventories.expiry_date', '<=', Carbon::now()->addDays(7))
            ->where('t_inventories.expiry_date', '>=', $today)
            ->select(
                't_inventories.*',
                't_goods.goods_number',
                't_goods.goods_name',
                'm_warehouses.warehouse_name'
            )
            ->orderBy('t_inventories.expiry_date', 'asc')
            ->limit(10)
            ->get();

        // 期限切れ在庫
        $expiredLots = DB::table('t_inventories')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->where('t_goods.delete_flg', 0)
            ->where('t_inventories.quantity', '>', 0)
            ->whereNotNull('t_inventories.expiry_date')
            ->where('t_inventories.expiry_date', '<', $today)
            ->count();

        // ========== グラフデータ ==========
        
        // 過去7日間の入出庫推移
        $movementTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateLabel = Carbon::now()->subDays($i)->format('m/d');
            
            $inbound = DB::table('t_stock_movements')
                ->whereIn('movement_type', ['in', 'return'])
                ->whereDate('movement_date', $date)
                ->sum('quantity');
            
            $outbound = DB::table('t_stock_movements')
                ->whereIn('movement_type', ['out', 'transfer_out'])
                ->whereDate('movement_date', $date)
                ->sum('quantity');
            
            $movementTrend[] = [
                'date' => $dateLabel,
                'inbound' => $inbound ?? 0,
                'outbound' => $outbound ?? 0,
            ];
        }

        // カテゴリ別在庫構成比
        $categoryStock = DB::table('t_inventories')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->leftJoin('m_categories', 't_goods.category_id', '=', 'm_categories.id')
            ->where('t_goods.delete_flg', 0)
            ->groupBy('m_categories.id', 'm_categories.category_name')
            ->selectRaw('m_categories.category_name, SUM(t_inventories.quantity * t_goods.goods_price) as total_value')
            ->orderByDesc('total_value')
            ->limit(8)
            ->get();

        // 倉庫別在庫状況
        $warehouseStock = DB::table('t_inventories')
            ->leftJoin('m_warehouses', 't_inventories.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->where('t_goods.delete_flg', 0)
            ->groupBy('m_warehouses.id', 'm_warehouses.warehouse_name')
            ->selectRaw('m_warehouses.warehouse_name, SUM(t_inventories.quantity) as total_quantity, SUM(t_inventories.quantity * t_goods.goods_price) as total_value')
            ->get();

        // ========== 最近の入出荷 ==========
        
        // 最近の入荷
        $recentInbound = DB::table('t_stock_movements')
            ->leftJoin('t_goods', 't_stock_movements.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_stock_movements.warehouse_id', '=', 'm_warehouses.id')
            ->whereIn('t_stock_movements.movement_type', ['in', 'return'])
            ->where('t_goods.delete_flg', 0)
            ->select(
                't_stock_movements.*',
                't_goods.goods_number',
                't_goods.goods_name',
                'm_warehouses.warehouse_name'
            )
            ->orderBy('t_stock_movements.movement_date', 'desc')
            ->limit(5)
            ->get();

        // 最近の出荷
        $recentOutbound = DB::table('t_stock_movements')
            ->leftJoin('t_goods', 't_stock_movements.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_stock_movements.warehouse_id', '=', 'm_warehouses.id')
            ->whereIn('t_stock_movements.movement_type', ['out', 'transfer_out'])
            ->where('t_goods.delete_flg', 0)
            ->select(
                't_stock_movements.*',
                't_goods.goods_number',
                't_goods.goods_name',
                'm_warehouses.warehouse_name'
            )
            ->orderBy('t_stock_movements.movement_date', 'desc')
            ->limit(5)
            ->get();

        // ========== 発注関連 ==========
        
        // 未処理の発注
        $pendingOrders = DB::table('t_purchase_orders')
            ->whereIn('status', ['pending', 'ordered'])
            ->count();

        // 発注提案数
        $orderSuggestions = DB::table('t_goods')
            ->leftJoin(DB::raw('(SELECT goods_id, SUM(available_quantity) as total_available FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
            ->where('t_goods.delete_flg', 0)
            ->where(function($q) {
                $q->whereRaw('COALESCE(inv.total_available, 0) <= t_goods.min_stock_level')
                  ->orWhereRaw('COALESCE(inv.total_available, 0) <= t_goods.reorder_point');
            })
            ->count();

        return view('dashboard', compact(
            'kpi',
            'alerts',
            'expiringLots',
            'expiredLots',
            'movementTrend',
            'categoryStock',
            'warehouseStock',
            'recentInbound',
            'recentOutbound',
            'pendingOrders',
            'orderSuggestions'
        ));
    }
}
