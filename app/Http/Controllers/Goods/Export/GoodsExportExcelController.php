<?php

namespace App\Http\Controllers\Goods\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GoodsExportExcelController extends BaseController
{
    /**
     * 商品一覧をExcel形式でエクスポート
     * Export goods list in Excel format
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
                    't_goods.id',
                    't_goods.un_id',
                    't_goods.goods_number',
                    't_goods.goods_name',
                    't_goods.category_id',
                    'm_categories.category_name',
                    'm_categories.category_code',
                    't_goods.goods_price',
                    't_goods.tax_rate',
                    't_goods.goods_stock',
                    't_goods.min_stock_level',
                    't_goods.max_stock_level',
                    't_goods.reorder_point',
                    't_goods.lead_time_days',
                    't_goods.is_lot_managed',
                    't_goods.is_serial_managed',
                    't_goods.expiry_alert_days',
                    't_goods.image_path',
                    't_goods.intro_txt',
                    't_goods.goods_detail',
                    't_goods.disp_flg',
                    't_goods.sales_start_at',
                    't_goods.sales_end_at',
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

            // Spreadsheetオブジェクトを作成
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('商品一覧');

            // ヘッダー行を設定
            $headers = [
                'A1' => 'ID',
                'B1' => 'UUID',
                'C1' => '商品番号',
                'D1' => '商品名',
                'E1' => 'カテゴリID',
                'F1' => 'カテゴリ名',
                'G1' => 'カテゴリコード',
                'H1' => '商品価格',
                'I1' => '税率(%)',
                'J1' => '税込価格',
                'K1' => '在庫数',
                'L1' => '最小在庫レベル',
                'M1' => '最大在庫レベル',
                'N1' => '発注点',
                'O1' => 'リードタイム(日)',
                'P1' => 'ロット管理',
                'Q1' => 'シリアル管理',
                'R1' => '有効期限アラート(日)',
                'S1' => '画像パス',
                'T1' => '紹介文',
                'U1' => '商品詳細',
                'V1' => '表示フラグ',
                'W1' => '販売開始日時',
                'X1' => '販売終了日時',
                'Y1' => '登録日時',
                'Z1' => '更新日時'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // ヘッダー行のスタイルを設定
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A1:Z1')->applyFromArray($headerStyle);

            // データ行を設定
            $row = 2;
            foreach ($goods as $item) {
                $price_with_tax = round($item->goods_price * (1 + $item->tax_rate / 100));
                $is_lot_managed = $item->is_lot_managed ? 'あり' : 'なし';
                $is_serial_managed = $item->is_serial_managed ? 'あり' : 'なし';
                $disp_flg = $item->disp_flg ? '表示' : '非表示';

                $sheet->setCellValue('A' . $row, $item->id);
                $sheet->setCellValue('B' . $row, $item->un_id);
                $sheet->setCellValue('C' . $row, $item->goods_number);
                $sheet->setCellValue('D' . $row, $item->goods_name);
                $sheet->setCellValue('E' . $row, $item->category_id ?? '');
                $sheet->setCellValue('F' . $row, $item->category_name ?? '');
                $sheet->setCellValue('G' . $row, $item->category_code ?? '');
                $sheet->setCellValue('H' . $row, $item->goods_price);
                $sheet->setCellValue('I' . $row, $item->tax_rate);
                $sheet->setCellValue('J' . $row, $price_with_tax);
                $sheet->setCellValue('K' . $row, $item->goods_stock);
                $sheet->setCellValue('L' . $row, $item->min_stock_level ?? '');
                $sheet->setCellValue('M' . $row, $item->max_stock_level ?? '');
                $sheet->setCellValue('N' . $row, $item->reorder_point ?? '');
                $sheet->setCellValue('O' . $row, $item->lead_time_days ?? '');
                $sheet->setCellValue('P' . $row, $is_lot_managed);
                $sheet->setCellValue('Q' . $row, $is_serial_managed);
                $sheet->setCellValue('R' . $row, $item->expiry_alert_days ?? '');
                $sheet->setCellValue('S' . $row, $item->image_path ?? '');
                $sheet->setCellValue('T' . $row, $item->intro_txt ?? '');
                $sheet->setCellValue('U' . $row, $item->goods_detail ?? '');
                $sheet->setCellValue('V' . $row, $disp_flg);
                $sheet->setCellValue('W' . $row, $item->sales_start_at ?? '');
                $sheet->setCellValue('X' . $row, $item->sales_end_at ?? '');
                $sheet->setCellValue('Y' . $row, $item->ins_date);
                $sheet->setCellValue('Z' . $row, $item->up_date);

                $row++;
            }

            // データ行のスタイルを設定（罫線）
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];
            $lastRow = $row - 1;
            if ($lastRow >= 2) {
                $sheet->getStyle('A2:Z' . $lastRow)->applyFromArray($dataStyle);
            }

            // 列幅を自動調整
            foreach (range('A', 'Z') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Excelファイルを生成
            $filename = 'goods_export_' . date('YmdHis') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFile);

            Log::info('Excel export executed', [
                'record_count' => $goods->count(),
                'filename' => $filename,
                'search_options' => $search_options
            ]);

            // ダウンロードレスポンスを返す
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Excel export error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Excelエクスポートに失敗しました: ' . $e->getMessage());
        }
    }
}
