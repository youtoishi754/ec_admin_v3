<?php

namespace App\Http\Controllers\Goods\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;
use TCPDF;

class GoodsExportDetailedPdfController extends BaseController
{
    /**
     * 在庫情報を含む詳細PDF出力
     * Export detailed PDF with inventory information
     */
    public function __invoke(Request $request)
    {
        try {
            // 商品データと在庫情報を結合して取得
            $query = DB::table('t_goods')
                ->leftJoin('m_categories', 't_goods.category_id', '=', 'm_categories.id')
                ->leftJoin('t_inventories', 't_goods.id', '=', 't_inventories.goods_id')
                ->leftJoin('m_warehouses', 't_inventories.warehouse_id', '=', 'm_warehouses.id')
                ->leftJoin('m_locations', 't_inventories.location_id', '=', 'm_locations.id')
                ->select(
                    't_goods.goods_number',
                    't_goods.goods_name',
                    'm_categories.category_name',
                    't_goods.goods_price',
                    't_goods.tax_rate',
                    't_goods.goods_stock as total_stock',
                    'm_warehouses.warehouse_name',
                    'm_locations.location_code',
                    't_inventories.quantity as location_quantity',
                    't_inventories.reserved_quantity',
                    't_inventories.available_quantity',
                    't_goods.disp_flg'
                )
                ->where('t_goods.delete_flg', 0);

            // データを取得
            $goods = $query->orderBy('t_goods.goods_number')
                          ->orderBy('m_warehouses.warehouse_name')
                          ->get();

            // PDFオブジェクトを作成
            $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

            // ドキュメント情報を設定
            $pdf->SetCreator('EC管理システム');
            $pdf->SetAuthor('EC Admin System');
            $pdf->SetTitle('商品在庫詳細一覧');
            $pdf->SetSubject('商品在庫データ');

            // ヘッダー・フッターを設定
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(true);
            $pdf->setFooterFont(Array('kozminproregular', '', 10));

            // マージンを設定
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 15);

            // フォントを設定（日本語対応）
            $pdf->SetFont('kozminproregular', '', 8);

            // ページを追加
            $pdf->AddPage();

            // タイトル
            $pdf->SetFont('kozminproregular', 'B', 14);
            $pdf->Cell(0, 10, '商品在庫詳細一覧', 0, 1, 'C');
            $pdf->Ln(5);

            // 出力日時
            $pdf->SetFont('kozminproregular', '', 9);
            $pdf->Cell(0, 5, '出力日時: ' . date('Y年m月d日 H:i:s'), 0, 1, 'R');
            $pdf->Ln(3);

            // テーブルヘッダー
            $pdf->SetFont('kozminproregular', 'B', 8);
            $pdf->SetFillColor(112, 173, 71);
            $pdf->SetTextColor(255, 255, 255);

            $pdf->Cell(25, 7, '商品番号', 1, 0, 'C', true);
            $pdf->Cell(45, 7, '商品名', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'カテゴリ', 1, 0, 'C', true);
            $pdf->Cell(18, 7, '価格', 1, 0, 'C', true);
            $pdf->Cell(18, 7, '税込価格', 1, 0, 'C', true);
            $pdf->Cell(15, 7, '総在庫', 1, 0, 'C', true);
            $pdf->Cell(30, 7, '倉庫', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'ロケーション', 1, 0, 'C', true);
            $pdf->Cell(20, 7, 'ロケ在庫', 1, 0, 'C', true);
            $pdf->Cell(18, 7, '引当数', 1, 0, 'C', true);
            $pdf->Cell(18, 7, '利用可能', 1, 0, 'C', true);
            $pdf->Cell(18, 7, '表示', 1, 1, 'C', true);

            // テーブルデータ
            $pdf->SetFont('kozminproregular', '', 7);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(240, 240, 240);

            $fill = false;
            foreach ($goods as $item) {
                $price_with_tax = round($item->goods_price * (1 + $item->tax_rate / 100));
                $disp_flg = $item->disp_flg ? '表示' : '非表示';

                // ページが足りない場合は改ページ
                if ($pdf->GetY() > 180) {
                    $pdf->AddPage();
                    
                    // ヘッダーを再出力
                    $pdf->SetFont('kozminproregular', 'B', 8);
                    $pdf->SetFillColor(112, 173, 71);
                    $pdf->SetTextColor(255, 255, 255);
                    
                    $pdf->Cell(25, 7, '商品番号', 1, 0, 'C', true);
                    $pdf->Cell(45, 7, '商品名', 1, 0, 'C', true);
                    $pdf->Cell(25, 7, 'カテゴリ', 1, 0, 'C', true);
                    $pdf->Cell(18, 7, '価格', 1, 0, 'C', true);
                    $pdf->Cell(18, 7, '税込価格', 1, 0, 'C', true);
                    $pdf->Cell(15, 7, '総在庫', 1, 0, 'C', true);
                    $pdf->Cell(30, 7, '倉庫', 1, 0, 'C', true);
                    $pdf->Cell(25, 7, 'ロケーション', 1, 0, 'C', true);
                    $pdf->Cell(20, 7, 'ロケ在庫', 1, 0, 'C', true);
                    $pdf->Cell(18, 7, '引当数', 1, 0, 'C', true);
                    $pdf->Cell(18, 7, '利用可能', 1, 0, 'C', true);
                    $pdf->Cell(18, 7, '表示', 1, 1, 'C', true);
                    
                    $pdf->SetFont('kozminproregular', '', 7);
                    $pdf->SetTextColor(0, 0, 0);
                }

                $pdf->Cell(25, 6, $item->goods_number, 1, 0, 'L', $fill);
                $pdf->Cell(45, 6, mb_substr($item->goods_name, 0, 18), 1, 0, 'L', $fill);
                $pdf->Cell(25, 6, mb_substr($item->category_name ?? '', 0, 10), 1, 0, 'L', $fill);
                $pdf->Cell(18, 6, number_format($item->goods_price), 1, 0, 'R', $fill);
                $pdf->Cell(18, 6, number_format($price_with_tax), 1, 0, 'R', $fill);
                $pdf->Cell(15, 6, $item->total_stock, 1, 0, 'R', $fill);
                $pdf->Cell(30, 6, mb_substr($item->warehouse_name ?? '未割当', 0, 12), 1, 0, 'L', $fill);
                $pdf->Cell(25, 6, mb_substr($item->location_code ?? '未割当', 0, 10), 1, 0, 'C', $fill);
                $pdf->Cell(20, 6, $item->location_quantity ?? 0, 1, 0, 'R', $fill);
                $pdf->Cell(18, 6, $item->reserved_quantity ?? 0, 1, 0, 'R', $fill);
                $pdf->Cell(18, 6, $item->available_quantity ?? 0, 1, 0, 'R', $fill);
                $pdf->Cell(18, 6, $disp_flg, 1, 1, 'C', $fill);

                $fill = !$fill;
            }

            // 件数を表示
            $pdf->Ln(5);
            $pdf->SetFont('kozminproregular', 'B', 9);
            $pdf->Cell(0, 5, '総件数: ' . number_format($goods->count()) . '件', 0, 1, 'L');

            Log::info('Detailed PDF export executed', [
                'record_count' => $goods->count(),
                'filename' => 'goods_inventory_export_' . date('YmdHis') . '.pdf'
            ]);

            // PDFを出力
            $filename = 'goods_inventory_export_' . date('YmdHis') . '.pdf';
            return response($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Detailed PDF export error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with('error', '詳細PDFエクスポートに失敗しました: ' . $e->getMessage());
        }
    }
}
