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

class GoodsExportDetailedExcelController extends BaseController
{
    /**
     * 在庫情報を含む詳細Excel出力
     * Export detailed Excel with inventory information
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
                    't_goods.min_stock_level',
                    't_goods.reorder_point',
                    't_goods.disp_flg'
                )
                ->where('t_goods.delete_flg', 0);

            // データを取得
            $goods = $query->orderBy('t_goods.goods_number')
                          ->orderBy('m_warehouses.warehouse_name')
                          ->get();

            // Spreadsheetオブジェクトを作成
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('商品在庫詳細');

            // ヘッダー行を設定
            $headers = [
                'A1' => '商品番号',
                'B1' => '商品名',
                'C1' => 'カテゴリ名',
                'D1' => '単価',
                'E1' => '税率(%)',
                'F1' => '税込価格',
                'G1' => '総在庫数',
                'H1' => '倉庫名',
                'I1' => 'ロケーション',
                'J1' => 'ロケーション在庫',
                'K1' => '引当数',
                'L1' => '利用可能数',
                'M1' => '最小在庫レベル',
                'N1' => '発注点',
                'O1' => '表示状態'
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
                    'startColor' => ['rgb' => '70AD47'],
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
            $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);

            // データ行を設定
            $row = 2;
            foreach ($goods as $item) {
                $price_with_tax = round($item->goods_price * (1 + $item->tax_rate / 100));
                $disp_flg = $item->disp_flg ? '表示' : '非表示';

                $sheet->setCellValue('A' . $row, $item->goods_number);
                $sheet->setCellValue('B' . $row, $item->goods_name);
                $sheet->setCellValue('C' . $row, $item->category_name ?? '');
                $sheet->setCellValue('D' . $row, $item->goods_price);
                $sheet->setCellValue('E' . $row, $item->tax_rate);
                $sheet->setCellValue('F' . $row, $price_with_tax);
                $sheet->setCellValue('G' . $row, $item->total_stock);
                $sheet->setCellValue('H' . $row, $item->warehouse_name ?? '未割当');
                $sheet->setCellValue('I' . $row, $item->location_code ?? '未割当');
                $sheet->setCellValue('J' . $row, $item->location_quantity ?? 0);
                $sheet->setCellValue('K' . $row, $item->reserved_quantity ?? 0);
                $sheet->setCellValue('L' . $row, $item->available_quantity ?? 0);
                $sheet->setCellValue('M' . $row, $item->min_stock_level ?? '');
                $sheet->setCellValue('N' . $row, $item->reorder_point ?? '');
                $sheet->setCellValue('O' . $row, $disp_flg);

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
                $sheet->getStyle('A2:O' . $lastRow)->applyFromArray($dataStyle);
            }

            // 列幅を自動調整
            foreach (range('A', 'O') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Excelファイルを生成
            $filename = 'goods_inventory_export_' . date('YmdHis') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFile);

            Log::info('Detailed Excel export executed', [
                'record_count' => $goods->count(),
                'filename' => $filename
            ]);

            // ダウンロードレスポンスを返す
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Detailed Excel export error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with('error', '詳細Excelエクスポートに失敗しました: ' . $e->getMessage());
        }
    }
}
