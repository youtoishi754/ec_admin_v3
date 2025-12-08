<?php

namespace App\Http\Controllers\StockMovement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class StockMovementHistoryController extends BaseController
{
    /**
     * 入出庫履歴を表示
     */
    public function __invoke(Request $request)
    {
        // 検索条件を取得
        $search_options = $request->all();

        // 履歴データを取得
        $query = DB::table('t_stock_movements')
            ->leftJoin('t_goods', 't_stock_movements.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_stock_movements.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('m_locations', 't_stock_movements.location_id', '=', 'm_locations.id')
            ->select(
                't_stock_movements.*',
                't_goods.goods_number',
                't_goods.goods_name',
                'm_warehouses.warehouse_name',
                'm_warehouses.warehouse_code',
                'm_locations.location_code'
            )
            ->where('t_goods.delete_flg', 0);

        // 検索条件: 商品番号
        if (!empty($search_options['goods_number'])) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $search_options['goods_number'] . '%');
        }

        // 検索条件: 商品名
        if (!empty($search_options['goods_name'])) {
            $query->where('t_goods.goods_name', 'LIKE', '%' . $search_options['goods_name'] . '%');
        }

        // 検索条件: 倉庫
        if (!empty($search_options['warehouse_id'])) {
            $query->where('t_stock_movements.warehouse_id', $search_options['warehouse_id']);
        }

        // 検索条件: 入出庫区分
        if (!empty($search_options['movement_type'])) {
            $query->where('t_stock_movements.movement_type', $search_options['movement_type']);
        }

        // 検索条件: ロット番号
        if (!empty($search_options['lot_number'])) {
            $query->where('t_stock_movements.lot_number', 'LIKE', '%' . $search_options['lot_number'] . '%');
        }

        // 検索条件: シリアル番号
        if (!empty($search_options['serial_number'])) {
            $query->where('t_stock_movements.serial_number', 'LIKE', '%' . $search_options['serial_number'] . '%');
        }

        // 検索条件: 日付範囲
        if (!empty($search_options['start_date'])) {
            $query->whereDate('t_stock_movements.movement_date', '>=', $search_options['start_date']);
        }
        if (!empty($search_options['end_date'])) {
            $query->whereDate('t_stock_movements.movement_date', '<=', $search_options['end_date']);
        }

        // ソート
        $query->orderBy('t_stock_movements.movement_date', 'desc')
              ->orderBy('t_stock_movements.created_at', 'desc');

        // ページネーション
        $history = $query->paginate(50);

        // 倉庫リスト取得
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        // 統計情報
        $stats = [
            'total_in' => DB::table('t_stock_movements')
                ->where('movement_type', 'in')
                ->whereDate('movement_date', '>=', now()->subDays(30))
                ->count(),
            'total_out' => DB::table('t_stock_movements')
                ->where('movement_type', 'out')
                ->whereDate('movement_date', '>=', now()->subDays(30))
                ->count(),
            'total_transfer' => DB::table('t_stock_movements')
                ->where('movement_type', 'transfer')
                ->whereDate('movement_date', '>=', now()->subDays(30))
                ->count(),
            'total_return' => DB::table('t_stock_movements')
                ->where('movement_type', 'return')
                ->whereDate('movement_date', '>=', now()->subDays(30))
                ->count(),
            'total_adjust' => DB::table('t_stock_movements')
                ->where('movement_type', 'adjust')
                ->whereDate('movement_date', '>=', now()->subDays(30))
                ->count(),
        ];

        return view('stock_movement.history', compact('history', 'warehouses', 'stats'));
    }
}
