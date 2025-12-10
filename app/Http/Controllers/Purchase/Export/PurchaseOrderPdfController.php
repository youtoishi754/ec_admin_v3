<?php

namespace App\Http\Controllers\Purchase\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use PDF;

class PurchaseOrderPdfController extends BaseController
{
    /**
     * 発注書をPDF形式でエクスポート
     */
    public function __invoke(Request $request, $id)
    {
        // 発注書データを取得
        $order = DB::table('t_purchase_orders')
            ->leftJoin('m_suppliers', 't_purchase_orders.supplier_id', '=', 'm_suppliers.id')
            ->select(
                't_purchase_orders.*',
                'm_suppliers.supplier_name',
                'm_suppliers.supplier_code',
                'm_suppliers.postal_code',
                'm_suppliers.address',
                'm_suppliers.tel',
                'm_suppliers.fax',
                'm_suppliers.contact_person'
            )
            ->where('t_purchase_orders.id', $id)
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', '発注書が見つかりません。');
        }

        // 発注書明細を取得
        $orderDetails = DB::table('t_purchase_order_details')
            ->leftJoin('t_goods', 't_purchase_order_details.goods_id', '=', 't_goods.id')
            ->select(
                't_purchase_order_details.*',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.unit'
            )
            ->where('t_purchase_order_details.purchase_order_id', $id)
            ->get();

        // 会社情報（実際のシステムでは設定から取得）
        $company = (object)[
            'name' => '株式会社サンプル',
            'postal_code' => '〒100-0001',
            'address' => '東京都千代田区1-1-1',
            'tel' => '03-1234-5678',
            'fax' => '03-1234-5679',
        ];

        // PDF用ビューを読み込み
        $pdf = PDF::loadView('purchase.export.order_pdf', [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'company' => $company,
        ]);

        // A4サイズ、縦向き
        $pdf->setPaper('A4', 'portrait');

        // PDFファイル名
        $filename = 'purchase_order_' . $order->order_number . '.pdf';

        // ダウンロード
        return $pdf->download($filename);
    }
}
