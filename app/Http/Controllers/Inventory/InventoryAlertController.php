<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class InventoryAlertController extends BaseController
{
    /**
     * 在庫アラート一覧を表示
     * Display inventory alerts
     */
    public function __invoke(Request $request)
    {
        // 検索条件を取得
        $search_options = $request->all();

        // アラートデータを取得
        $query = DB::table('t_stock_alerts')
            ->leftJoin('t_goods', 't_stock_alerts.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_stock_alerts.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('m_categories', 't_goods.category_id', '=', 'm_categories.id')
            ->select(
                't_stock_alerts.id',
                't_stock_alerts.goods_id',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.image_path',
                'm_categories.category_name',
                'm_warehouses.warehouse_name',
                't_stock_alerts.alert_type',
                't_stock_alerts.current_quantity',
                't_stock_alerts.threshold_quantity',
                't_stock_alerts.expiry_date',
                't_stock_alerts.alert_date',
                't_stock_alerts.is_resolved',
                't_stock_alerts.resolved_at',
                't_stock_alerts.notes',
                't_stock_alerts.created_at',
                't_goods.min_stock_level',
                't_goods.reorder_point'
            )
            ->where('t_goods.delete_flg', 0);

        // 検索条件: 解決済みを除外
        if (empty($search_options['show_resolved'])) {
            $query->where('t_stock_alerts.is_resolved', 0);
        }

        // 検索条件: アラート種別
        if (!empty($search_options['alert_type'])) {
            $query->where('t_stock_alerts.alert_type', $search_options['alert_type']);
        }

        // 検索条件: 倉庫
        if (!empty($search_options['warehouse_id'])) {
            $query->where('t_stock_alerts.warehouse_id', $search_options['warehouse_id']);
        }

        // 検索条件: 商品番号
        if (!empty($search_options['goods_number'])) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $search_options['goods_number'] . '%');
        }

        // ソート（重要度順: out_of_stock > expiry_critical > low_stock > expiry_warning > excess）
        $query->orderByRaw(
            "FIELD(t_stock_alerts.alert_type, 'out_of_stock', 'expiry_critical', 'low_stock', 'expiry_warning', 'excess') DESC"
        )->orderBy('t_stock_alerts.alert_date', 'desc');

        // ページネーション
        $alerts = $query->paginate(20);

        // 倉庫リスト取得
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        // 統計情報
        $stats = [
            'low_stock' => DB::table('t_stock_alerts')
                ->where('alert_type', 'low_stock')
                ->where('is_resolved', 0)
                ->count(),
            'out_of_stock' => DB::table('t_stock_alerts')
                ->where('alert_type', 'out_of_stock')
                ->where('is_resolved', 0)
                ->count(),
            'expiry_warning' => DB::table('t_stock_alerts')
                ->where('alert_type', 'expiry_warning')
                ->where('is_resolved', 0)
                ->count(),
            'expiry_critical' => DB::table('t_stock_alerts')
                ->where('alert_type', 'expiry_critical')
                ->where('is_resolved', 0)
                ->count(),
            'excess' => DB::table('t_stock_alerts')
                ->where('alert_type', 'excess')
                ->where('is_resolved', 0)
                ->count(),
            'total' => DB::table('t_stock_alerts')
                ->where('is_resolved', 0)
                ->count(),
        ];

        return view('inventory.alert', compact('alerts', 'warehouses', 'stats'));
    }

    /**
     * アラート解決
     * Resolve alert
     */
    public function resolve(Request $request, $id)
    {
        try {
            DB::table('t_stock_alerts')
                ->where('id', $id)
                ->update([
                    'is_resolved' => 1,
                    'resolved_at' => now(),
                    'resolved_by' => auth()->id(),
                    'updated_at' => now()
                ]);

            return redirect()->back()->with('success', 'アラートを解決済みにしました。');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'アラート解決に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * アラート一括解決
     * Bulk resolve alerts
     */
    public function bulkResolve(Request $request)
    {
        try {
            $alert_ids = $request->input('alert_ids', []);
            
            if (empty($alert_ids)) {
                return redirect()->back()->with('error', '解決するアラートを選択してください。');
            }

            DB::table('t_stock_alerts')
                ->whereIn('id', $alert_ids)
                ->update([
                    'is_resolved' => 1,
                    'resolved_at' => now(),
                    'resolved_by' => auth()->id(),
                    'updated_at' => now()
                ]);

            return redirect()->back()->with('success', count($alert_ids) . '件のアラートを解決済みにしました。');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'アラート一括解決に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * 在庫アラートを今すぐチェック
     */
    public function checkNow()
    {
        try {
            $service = new \App\Services\StockAlertService();
            $service->checkAllStockAlerts();

            return redirect()->back()->with('success', '在庫アラートのチェックが完了しました。');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'アラートチェックに失敗しました: ' . $e->getMessage());
        }
    }
}
