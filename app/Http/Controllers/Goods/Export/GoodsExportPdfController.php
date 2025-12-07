<?php

namespace App\Http\Controllers\Goods\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;
use TCPDF;

class GoodsExportPdfController extends BaseController
{
    /**
     * 商品一覧をPDF形式でエクスポート
     * Export goods list in PDF format
     */
    public function __invoke(Request $request)
    {
        try {
            // 検索条件を取得
            $search_options = $request->all();
            
            // 商品データを取得
            $query = DB::table('t_goods')
                ->leftJoin('m_categories', 't_goods.category_id', '=', 'm_categories.id')
                ->select(
                    't_goods.goods_number',
                    't_goods.goods_name',
                    'm_categories.category_name',
                    't_goods.goods_price',
                    't_goods.tax_rate',
                    't_goods.goods_stock',
                    't_goods.min_stock_level',
                    't_goods.reorder_point',
                    't_goods.disp_flg',
                    't_goods.ins_date',
                    't_goods.up_date'
                )
                ->where('t_goods.delete_flg', 0);

            // 検索条件を適用
            if (!empty($search_options['goods_number'])) {
                $query->where('t_goods.goods_number', 'LIKE', '%' . $search_options['goods_number'] . '%');
            }

            if (!empty($search_options['goods_id'])) {
                $query->where('t_goods.id', $search_options['goods_id']);
            }

            if (!empty($search_options['min_price'])) {
                $query->where('t_goods.goods_price', '>=', $search_options['min_price']);
            }

            if (!empty($search_options['max_price'])) {
                $query->where('t_goods.goods_price', '<=', $search_options['max_price']);
            }

            if (!empty($search_options['min_stock'])) {
                $query->where('t_goods.goods_stock', '>=', $search_options['min_stock']);
            }

            if (!empty($search_options['max_stock'])) {
                $query->where('t_goods.goods_stock', '<=', $search_options['max_stock']);
            }

            if (!empty($search_options['category_id'])) {
                $query->where('t_goods.category_id', $search_options['category_id']);
            }

            // データを取得
            $goods = $query->orderBy('t_goods.goods_number')->get();

            // PDFオブジェクトを作成
            $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

            // ドキュメント情報を設定
            $pdf->SetCreator('EC管理システム');
            $pdf->SetAuthor('EC Admin System');
            $pdf->SetTitle('商品一覧');
            $pdf->SetSubject('商品マスタデータ');

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
            $pdf->Cell(0, 10, '商品一覧', 0, 1, 'C');
            $pdf->Ln(5);

            // 出力日時
            $pdf->SetFont('kozminproregular', '', 9);
            $pdf->Cell(0, 5, '出力日時: ' . date('Y年m月d日 H:i:s'), 0, 1, 'R');
            $pdf->Ln(3);

            // テーブルヘッダー
            $pdf->SetFont('kozminproregular', 'B', 8);
            $pdf->SetFillColor(68, 114, 196);
            $pdf->SetTextColor(255, 255, 255);

            $pdf->Cell(25, 7, '商品番号', 1, 0, 'C', true);
            $pdf->Cell(50, 7, '商品名', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'カテゴリ', 1, 0, 'C', true);
            $pdf->Cell(20, 7, '価格', 1, 0, 'C', true);
            $pdf->Cell(15, 7, '税率', 1, 0, 'C', true);
            $pdf->Cell(20, 7, '税込価格', 1, 0, 'C', true);
            $pdf->Cell(15, 7, '在庫', 1, 0, 'C', true);
            $pdf->Cell(20, 7, '最小在庫', 1, 0, 'C', true);
            $pdf->Cell(20, 7, '発注点', 1, 0, 'C', true);
            $pdf->Cell(20, 7, '表示', 1, 0, 'C', true);
            $pdf->Cell(35, 7, '更新日時', 1, 1, 'C', true);

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
                    $pdf->SetFillColor(68, 114, 196);
                    $pdf->SetTextColor(255, 255, 255);
                    
                    $pdf->Cell(25, 7, '商品番号', 1, 0, 'C', true);
                    $pdf->Cell(50, 7, '商品名', 1, 0, 'C', true);
                    $pdf->Cell(30, 7, 'カテゴリ', 1, 0, 'C', true);
                    $pdf->Cell(20, 7, '価格', 1, 0, 'C', true);
                    $pdf->Cell(15, 7, '税率', 1, 0, 'C', true);
                    $pdf->Cell(20, 7, '税込価格', 1, 0, 'C', true);
                    $pdf->Cell(15, 7, '在庫', 1, 0, 'C', true);
                    $pdf->Cell(20, 7, '最小在庫', 1, 0, 'C', true);
                    $pdf->Cell(20, 7, '発注点', 1, 0, 'C', true);
                    $pdf->Cell(20, 7, '表示', 1, 0, 'C', true);
                    $pdf->Cell(35, 7, '更新日時', 1, 1, 'C', true);
                    
                    $pdf->SetFont('kozminproregular', '', 7);
                    $pdf->SetTextColor(0, 0, 0);
                }

                $pdf->Cell(25, 6, $item->goods_number, 1, 0, 'L', $fill);
                $pdf->Cell(50, 6, mb_substr($item->goods_name, 0, 20), 1, 0, 'L', $fill);
                $pdf->Cell(30, 6, mb_substr($item->category_name ?? '', 0, 12), 1, 0, 'L', $fill);
                $pdf->Cell(20, 6, number_format($item->goods_price), 1, 0, 'R', $fill);
                $pdf->Cell(15, 6, $item->tax_rate . '%', 1, 0, 'C', $fill);
                $pdf->Cell(20, 6, number_format($price_with_tax), 1, 0, 'R', $fill);
                $pdf->Cell(15, 6, $item->goods_stock, 1, 0, 'R', $fill);
                $pdf->Cell(20, 6, $item->min_stock_level ?? '-', 1, 0, 'R', $fill);
                $pdf->Cell(20, 6, $item->reorder_point ?? '-', 1, 0, 'R', $fill);
                $pdf->Cell(20, 6, $disp_flg, 1, 0, 'C', $fill);
                $pdf->Cell(35, 6, $item->up_date, 1, 1, 'C', $fill);

                $fill = !$fill;
            }

            // 件数を表示
            $pdf->Ln(5);
            $pdf->SetFont('kozminproregular', 'B', 9);
            $pdf->Cell(0, 5, '総件数: ' . number_format($goods->count()) . '件', 0, 1, 'L');

            Log::info('PDF export executed', [
                'record_count' => $goods->count(),
                'filename' => 'goods_export_' . date('YmdHis') . '.pdf',
                'search_options' => $search_options
            ]);

            // PDFを出力
            $filename = 'goods_export_' . date('YmdHis') . '.pdf';
            return response($pdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('PDF export error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'PDFエクスポートに失敗しました: ' . $e->getMessage());
        }
    }
}
