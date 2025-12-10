<?php

namespace App\Http\Controllers\Inventory\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PDF;

class StocktakingExportController extends BaseController
{
    /**
     * 棚卸シートをExcel形式でエクスポート
     */
    public function exportExcel(Request $request)
    {
        $inventories = $this->getInventoryData($request);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('棚卸シート');

        // タイトル
        $sheet->setCellValue('A1', '棚卸シート - ' . date('Y年m月d日'));
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ヘッダー行（行3から）
        $headers = [
            '倉庫', 'ロケーション', '商品番号', '商品名',
            'ロット番号', 'シリアル番号', 'システム在庫', '実棚数（記入欄）', '差異'
        ];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '3', $header);
            $column++;
        }

        // ヘッダースタイル
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2c3e50']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A3:I3')->applyFromArray($headerStyle);

        // データ行（行4から）
        $row = 4;
        foreach ($inventories as $inv) {
            $sheet->setCellValue('A' . $row, $inv->warehouse_name);
            $sheet->setCellValue('B' . $row, $inv->location_code ?? '');
            $sheet->setCellValue('C' . $row, $inv->goods_number);
            $sheet->setCellValue('D' . $row, $inv->goods_name);
            $sheet->setCellValue('E' . $row, $inv->lot_number ?? '');
            $sheet->setCellValue('F' . $row, $inv->serial_number ?? '');
            $sheet->setCellValue('G' . $row, $inv->system_quantity);
            $sheet->setCellValue('H' . $row, ''); // 実棚数記入欄
            $sheet->setCellValue('I' . $row, ''); // 差異（=H-G）
            
            // 数式: 差異 = 実棚数 - システム在庫
            $sheet->setCellValue('I' . $row, '=IF(H' . $row . '<>"", H' . $row . '-G' . $row . ', "")');
            
            $row++;
        }

        // データ行スタイル
        $lastRow = $row - 1;
        if ($lastRow >= 4) {
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];
            $sheet->getStyle('A4:I' . $lastRow)->applyFromArray($dataStyle);
            
            // 数値列の右寄せ
            $sheet->getStyle('G4:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // 実棚数記入欄に黄色背景
            $sheet->getStyle('H4:H' . $lastRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFFFCC');
        }

        // 列幅自動調整
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 署名欄
        $signRow = $lastRow + 3;
        $sheet->setCellValue('A' . $signRow, '棚卸日: ____年____月____日');
        $sheet->setCellValue('E' . $signRow, '担当者: ________________');
        $sheet->setCellValue('G' . $signRow, '承認者: ________________');

        // ファイル出力
        $filename = 'stocktaking_sheet_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * 棚卸シートをPDF形式でエクスポート
     */
    public function exportPdf(Request $request)
    {
        $inventories = $this->getInventoryData($request);
        
        // 倉庫ごとにグループ化
        $groupedInventories = [];
        foreach ($inventories as $inv) {
            $key = $inv->warehouse_name;
            if (!isset($groupedInventories[$key])) {
                $groupedInventories[$key] = [];
            }
            $groupedInventories[$key][] = $inv;
        }

        $pdf = PDF::loadView('inventory.export.stocktaking_pdf', [
            'groupedInventories' => $groupedInventories,
            'exportDate' => now()->format('Y年m月d日 H:i'),
        ]);

        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'stocktaking_sheet_' . date('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * 棚卸用在庫データを取得
     */
    private function getInventoryData(Request $request)
    {
        $query = DB::table('t_inventories')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_inventories.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('m_locations', 't_inventories.location_id', '=', 'm_locations.id')
            ->select(
                't_inventories.id',
                't_goods.goods_number',
                't_goods.goods_name',
                'm_warehouses.warehouse_name',
                'm_warehouses.warehouse_code',
                'm_locations.location_code',
                't_inventories.lot_number',
                't_inventories.serial_number',
                't_inventories.quantity as system_quantity'
            )
            ->where('t_goods.delete_flg', 0);

        // 検索条件: 倉庫
        if ($request->filled('warehouse_id')) {
            $query->where('t_inventories.warehouse_id', $request->warehouse_id);
        }

        // 検索条件: ロケーション
        if ($request->filled('location_id')) {
            $query->where('t_inventories.location_id', $request->location_id);
        }

        // 検索条件: 商品番号
        if ($request->filled('goods_number')) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $request->goods_number . '%');
        }

        return $query
            ->orderBy('m_warehouses.warehouse_code')
            ->orderBy('m_locations.location_code')
            ->orderBy('t_goods.goods_number')
            ->get();
    }
}
