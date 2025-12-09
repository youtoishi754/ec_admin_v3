<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class OrderAnalyticsController extends BaseController
{
    /**
     * 発注実績分析画面を表示
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // 期間に応じた日付範囲を設定
        switch ($period) {
            case 'year':
                $startDate = $year . '-01-01';
                $endDate = $year . '-12-31';
                break;
            case 'quarter':
                $quarter = $request->get('quarter', ceil(date('n') / 3));
                $startMonth = (($quarter - 1) * 3) + 1;
                $startDate = $year . '-' . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . '-01';
                $endDate = date('Y-m-t', strtotime($year . '-' . str_pad($startMonth + 2, 2, '0', STR_PAD_LEFT) . '-01'));
                break;
            case 'month':
            default:
                $startDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));
                break;
        }

        // 発注サマリー
        $summary = [
            'total_orders' => DB::table('t_purchase_orders')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->count(),
            'total_amount' => DB::table('t_purchase_orders')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'received_orders' => DB::table('t_purchase_orders')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', 'received')
                ->count(),
            'cancelled_orders' => DB::table('t_purchase_orders')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', 'cancelled')
                ->count(),
            'avg_order_amount' => DB::table('t_purchase_orders')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->avg('total_amount'),
        ];

        // 仕入先別発注実績
        $supplierStats = DB::table('t_purchase_orders')
            ->leftJoin('m_suppliers', 't_purchase_orders.supplier_id', '=', 'm_suppliers.id')
            ->select(
                'm_suppliers.id',
                'm_suppliers.supplier_code',
                'm_suppliers.supplier_name',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(t_purchase_orders.total_amount) as total_amount'),
                DB::raw('AVG(t_purchase_orders.total_amount) as avg_amount')
            )
            ->whereBetween('t_purchase_orders.order_date', [$startDate, $endDate])
            ->where('t_purchase_orders.status', '!=', 'cancelled')
            ->groupBy('m_suppliers.id', 'm_suppliers.supplier_code', 'm_suppliers.supplier_name')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

        // 商品別発注実績
        $goodsStats = DB::table('t_purchase_order_details')
            ->leftJoin('t_purchase_orders', 't_purchase_order_details.purchase_order_id', '=', 't_purchase_orders.id')
            ->leftJoin('t_goods', 't_purchase_order_details.goods_id', '=', 't_goods.id')
            ->select(
                't_goods.id',
                't_goods.goods_number',
                't_goods.goods_name',
                DB::raw('SUM(t_purchase_order_details.quantity) as total_quantity'),
                DB::raw('SUM(t_purchase_order_details.subtotal) as total_amount'),
                DB::raw('COUNT(DISTINCT t_purchase_orders.id) as order_count')
            )
            ->whereBetween('t_purchase_orders.order_date', [$startDate, $endDate])
            ->where('t_purchase_orders.status', '!=', 'cancelled')
            ->groupBy('t_goods.id', 't_goods.goods_number', 't_goods.goods_name')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

        // 月別推移データ（過去12ヶ月）
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $trendMonth = date('Y-m', strtotime("-{$i} months"));
            $trendStart = $trendMonth . '-01';
            $trendEnd = date('Y-m-t', strtotime($trendStart));
            
            $monthData = DB::table('t_purchase_orders')
                ->whereBetween('order_date', [$trendStart, $trendEnd])
                ->where('status', '!=', 'cancelled')
                ->select(
                    DB::raw('COUNT(*) as order_count'),
                    DB::raw('SUM(total_amount) as total_amount')
                )
                ->first();

            $monthlyTrend[] = [
                'month' => $trendMonth,
                'label' => date('Y年m月', strtotime($trendStart)),
                'order_count' => $monthData->order_count ?? 0,
                'total_amount' => $monthData->total_amount ?? 0,
            ];
        }

        // ステータス別集計
        $statusStats = DB::table('t_purchase_orders')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as amount')
            )
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // リードタイム分析（発注日から入荷日までの日数）
        $leadTimeStats = DB::table('t_purchase_orders')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->where('status', 'received')
            ->whereNotNull('received_date')
            ->select(
                DB::raw('AVG(DATEDIFF(received_date, order_date)) as avg_lead_time'),
                DB::raw('MIN(DATEDIFF(received_date, order_date)) as min_lead_time'),
                DB::raw('MAX(DATEDIFF(received_date, order_date)) as max_lead_time')
            )
            ->first();

        return view('purchase.analytics', compact(
            'summary', 
            'supplierStats', 
            'goodsStats', 
            'monthlyTrend', 
            'statusStats',
            'leadTimeStats',
            'period', 
            'year', 
            'month',
            'startDate',
            'endDate'
        ));
    }

    /**
     * CSVエクスポート
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));

        $orders = DB::table('t_purchase_orders')
            ->leftJoin('m_suppliers', 't_purchase_orders.supplier_id', '=', 'm_suppliers.id')
            ->select(
                't_purchase_orders.order_number',
                't_purchase_orders.order_date',
                'm_suppliers.supplier_code',
                'm_suppliers.supplier_name',
                't_purchase_orders.status',
                't_purchase_orders.total_amount',
                't_purchase_orders.expected_delivery_date',
                't_purchase_orders.received_date'
            )
            ->whereBetween('t_purchase_orders.order_date', [$startDate, $endDate])
            ->orderBy('t_purchase_orders.order_date', 'desc')
            ->get();

        $statusLabels = [
            'draft' => '下書き',
            'pending' => '承認待ち',
            'ordered' => '発注済み',
            'received' => '入荷完了',
            'cancelled' => 'キャンセル',
        ];

        $csv = "発注番号,発注日,仕入先コード,仕入先名,ステータス,合計金額,納期予定日,入荷日\n";
        foreach ($orders as $order) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%d,%s,%s\n",
                $order->order_number,
                $order->order_date,
                $order->supplier_code,
                $order->supplier_name,
                $statusLabels[$order->status] ?? $order->status,
                $order->total_amount,
                $order->expected_delivery_date ?? '',
                $order->received_date ?? ''
            );
        }

        $filename = 'purchase_orders_' . date('YmdHis') . '.csv';
        
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
