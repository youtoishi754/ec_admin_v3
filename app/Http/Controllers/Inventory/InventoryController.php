<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class InventoryController extends BaseController
{
    /**
     * リアルタイム在庫一覧を表示
     * Display real-time inventory list
     */
    public function __invoke(Request $request)
    {
        // 検索条件を取得
        $search_options = $request->all();

        // 在庫データを取得
        $query = DB::table('t_inventories')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_inventories.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('m_locations', 't_inventories.location_id', '=', 'm_locations.id')
            ->leftJoin('m_categories', 't_goods.category_id', '=', 'm_categories.id')
            ->select(
                't_inventories.id',
                't_inventories.goods_id',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.image_path',
                'm_categories.category_name',
                'm_warehouses.warehouse_name',
                'm_warehouses.warehouse_code',
                'm_locations.location_code',
                'm_locations.aisle',
                'm_locations.rack',
                'm_locations.shelf',
                't_inventories.lot_number',
                't_inventories.serial_number',
                't_inventories.quantity',
                't_inventories.reserved_quantity',
                't_inventories.available_quantity',
                't_inventories.expiry_date',
                't_inventories.received_date',
                't_inventories.manufacturing_date',
                't_inventories.status',
                't_goods.min_stock_level',
                't_goods.reorder_point',
                't_goods.goods_stock as total_stock'
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
            $query->where('t_inventories.warehouse_id', $search_options['warehouse_id']);
        }

        // 検索条件: ロケーション
        if (!empty($search_options['location_id'])) {
            $query->where('t_inventories.location_id', $search_options['location_id']);
        }

        // 検索条件: ロット番号
        if (!empty($search_options['lot_number'])) {
            $query->where('t_inventories.lot_number', 'LIKE', '%' . $search_options['lot_number'] . '%');
        }

        // 検索条件: 在庫状態
        if (!empty($search_options['stock_status'])) {
            switch ($search_options['stock_status']) {
                case 'out_of_stock':
                    $query->where('t_inventories.available_quantity', '<=', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('t_inventories.available_quantity', '<=', 't_goods.min_stock_level')
                          ->where('t_inventories.available_quantity', '>', 0);
                    break;
                case 'normal':
                    $query->whereColumn('t_inventories.available_quantity', '>', 't_goods.min_stock_level');
                    break;
            }
        }

        // ソート
        $sort_by = $request->get('sort_by', 'goods_number');
        $sort_direction = $request->get('sort_direction', 'asc');
        
        switch ($sort_by) {
            case 'quantity':
                $query->orderBy('t_inventories.quantity', $sort_direction);
                break;
            case 'available':
                $query->orderBy('t_inventories.available_quantity', $sort_direction);
                break;
            case 'warehouse':
                $query->orderBy('m_warehouses.warehouse_name', $sort_direction);
                break;
            default:
                $query->orderBy('t_goods.goods_number', $sort_direction);
        }

        // ページネーション
        $inventories = $query->paginate(20);

        // 倉庫リスト取得
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        // ロケーションリスト取得
        $locations = DB::table('m_locations')
            ->leftJoin('m_warehouses', 'm_locations.warehouse_id', '=', 'm_warehouses.id')
            ->select('m_locations.*', 'm_warehouses.warehouse_name')
            ->where('m_locations.is_active', 1)
            ->orderBy('m_locations.location_code')
            ->get();

        // 統計情報
        $stats = [
            'total_items' => DB::table('t_inventories')->count(),
            'out_of_stock' => DB::table('t_inventories')->where('available_quantity', '<=', 0)->count(),
            'low_stock' => DB::table('t_inventories')
                ->join('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
                ->whereColumn('t_inventories.available_quantity', '<=', 't_goods.min_stock_level')
                ->where('t_inventories.available_quantity', '>', 0)
                ->count(),
            'total_quantity' => DB::table('t_inventories')->sum('quantity'),
        ];

        return view('inventory.index', compact('inventories', 'warehouses', 'locations', 'stats'));
    }
}
