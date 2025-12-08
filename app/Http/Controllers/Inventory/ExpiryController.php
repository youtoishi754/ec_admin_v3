<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class ExpiryController extends BaseController
{
    /**
     * 有効期限管理一覧を表示
     * Display expiry date management list
     */
    public function __invoke(Request $request)
    {
        // 検索条件を取得
        $search_options = $request->all();

        // 有効期限付き在庫データを取得
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
                't_goods.expiry_alert_days',
                'm_categories.category_name',
                'm_warehouses.warehouse_name',
                'm_locations.location_code',
                't_inventories.lot_number',
                't_inventories.quantity',
                't_inventories.available_quantity',
                't_inventories.expiry_date',
                't_inventories.received_date',
                DB::raw('DATEDIFF(t_inventories.expiry_date, NOW()) as days_until_expiry')
            )
            ->whereNotNull('t_inventories.expiry_date')
            ->where('t_goods.delete_flg', 0);

        // 検索条件: 期限状態
        if (!empty($search_options['expiry_status'])) {
            switch ($search_options['expiry_status']) {
                case 'expired':
                    $query->where('t_inventories.expiry_date', '<', now());
                    break;
                case 'critical':
                    $query->where('t_inventories.expiry_date', '>=', now())
                          ->where('t_inventories.expiry_date', '<=', now()->addDays(7));
                    break;
                case 'warning':
                    $query->where('t_inventories.expiry_date', '>', now()->addDays(7))
                          ->where('t_inventories.expiry_date', '<=', now()->addDays(30));
                    break;
                case 'normal':
                    $query->where('t_inventories.expiry_date', '>', now()->addDays(30));
                    break;
            }
        }

        // 検索条件: 商品番号
        if (!empty($search_options['goods_number'])) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $search_options['goods_number'] . '%');
        }

        // 検索条件: 倉庫
        if (!empty($search_options['warehouse_id'])) {
            $query->where('t_inventories.warehouse_id', $search_options['warehouse_id']);
        }

        // 検索条件: ロット番号
        if (!empty($search_options['lot_number'])) {
            $query->where('t_inventories.lot_number', 'LIKE', '%' . $search_options['lot_number'] . '%');
        }

        // ソート (期限日昇順 = 古い順)
        $query->orderBy('t_inventories.expiry_date', 'asc');

        // ページネーション
        $inventories = $query->paginate(20);

        // 倉庫リスト取得
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        // 統計情報
        $stats = [
            'expired' => DB::table('t_inventories')
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<', now())
                ->where('quantity', '>', 0)
                ->count(),
            'critical_7days' => DB::table('t_inventories')
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '>=', now())
                ->where('expiry_date', '<=', now()->addDays(7))
                ->where('quantity', '>', 0)
                ->count(),
            'warning_30days' => DB::table('t_inventories')
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '>', now()->addDays(7))
                ->where('expiry_date', '<=', now()->addDays(30))
                ->where('quantity', '>', 0)
                ->count(),
            'total_with_expiry' => DB::table('t_inventories')
                ->whereNotNull('expiry_date')
                ->where('quantity', '>', 0)
                ->count(),
        ];

        return view('inventory.expiry', compact('inventories', 'warehouses', 'stats'));
    }
}
