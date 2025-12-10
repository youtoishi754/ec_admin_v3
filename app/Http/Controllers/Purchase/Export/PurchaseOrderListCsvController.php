<?php

namespace App\Http\Controllers\Purchase\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class PurchaseOrderListCsvController extends BaseController
{
    /**
     * 発注書一覧をCSV形式でエクスポート
     */
    public function __invoke(Request $request)
    {
        // 発注書データを取得
        $orders = $this->getOrderData($request);

        // CSVファイル名
        $filename = 'purchase_order_list_' . date('Ymd_His') . '.csv';

        // CSVヘッダー
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // BOMを出力（Excel対応）
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // ヘッダー行
            fputcsv($file, [
                '発注番号',
                '仕入先コード',
                '仕入先名',
                '発注日',
                '納品希望日',
                'ステータス',
                '合計金額',
                '税込金額',
                '作成日時',
                '備考'
            ]);

            // ステータスの日本語変換
            $statusLabels = [
                'draft' => '下書き',
                'pending' => '承認待ち',
                'ordered' => '発注済み',
                'received' => '入荷済み',
                'cancelled' => 'キャンセル'
            ];

            // データ行
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->supplier_code ?? '',
                    $order->supplier_name ?? '',
                    $order->order_date,
                    $order->expected_delivery_date ?? '',
                    $statusLabels[$order->status] ?? $order->status,
                    $order->total_amount,
                    round($order->total_amount * 1.1),
                    $order->created_at,
                    $order->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 発注書データを取得
     */
    private function getOrderData(Request $request)
    {
        $query = DB::table('t_purchase_orders')
            ->leftJoin('m_suppliers', 't_purchase_orders.supplier_id', '=', 'm_suppliers.id')
            ->select(
                't_purchase_orders.*',
                'm_suppliers.supplier_name',
                'm_suppliers.supplier_code'
            );

        // 検索条件: 発注番号
        if ($request->filled('order_number')) {
            $query->where('t_purchase_orders.order_number', 'LIKE', '%' . $request->order_number . '%');
        }

        // 検索条件: 仕入先
        if ($request->filled('supplier_id')) {
            $query->where('t_purchase_orders.supplier_id', $request->supplier_id);
        }

        // 検索条件: ステータス
        if ($request->filled('status')) {
            $query->where('t_purchase_orders.status', $request->status);
        }

        // 検索条件: 発注日
        if ($request->filled('order_date_from')) {
            $query->where('t_purchase_orders.order_date', '>=', $request->order_date_from);
        }
        if ($request->filled('order_date_to')) {
            $query->where('t_purchase_orders.order_date', '<=', $request->order_date_to);
        }

        return $query->orderBy('t_purchase_orders.created_at', 'desc')->get();
    }
}
