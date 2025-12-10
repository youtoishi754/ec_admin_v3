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

class InventoryExportExcelController extends BaseController
{
    /**
     * リアルタイム在庫一覧をExcel形式でエクスポート
     */
    public function __invoke(Request $request)
    {
        // 在庫データを取得
        $inventories = $this->getInventoryData($request);

        // スプレッドシートを作成
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('在庫一覧');

        // ヘッダー行
        $headers = [
            'A1' => '商品番号',
            'B1' => '商品名',
            'C1' => 'カテゴリ',
            'D1' => '倉庫',
            'E1' => 'ロケーション',
            'F1' => 'ロット番号',
            'G1' => 'シリアル番号',
            'H1' => '在庫数',
            'I1' => '引当数',
            'J1' => '利用可能数',
            'K1' => '有効期限',
            'L1' => '入荷日',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // ヘッダースタイル
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

        // データ行
        $row = 2;
        foreach ($inventories as $inventory) {
            $sheet->setCellValue('A' . $row, $inventory->goods_number);
            $sheet->setCellValue('B' . $row, $inventory->goods_name);
            $sheet->setCellValue('C' . $row, $inventory->category_name ?? '');
            $sheet->setCellValue('D' . $row, $inventory->warehouse_name);
            $sheet->setCellValue('E' . $row, $inventory->location_code ?? '');
            $sheet->setCellValue('F' . $row, $inventory->lot_number ?? '');
            $sheet->setCellValue('G' . $row, $inventory->serial_number ?? '');
            $sheet->setCellValue('H' . $row, $inventory->quantity);
            $sheet->setCellValue('I' . $row, $inventory->reserved_quantity);
            $sheet->setCellValue('J' . $row, $inventory->available_quantity);
            $sheet->setCellValue('K' . $row, $inventory->expiry_date ?? '');
            $sheet->setCellValue('L' . $row, $inventory->received_date ?? '');
            $row++;
        }

        // データ部分のスタイル
        $dataStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        if ($row > 2) {
            $sheet->getStyle('A2:L' . ($row - 1))->applyFromArray($dataStyle);
        }

        // 列幅自動調整
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 数値列を右寄せ
        $sheet->getStyle('H2:J' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // ファイル出力
        $filename = 'inventory_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * 在庫データを取得
     */
    private function getInventoryData(Request $request)
    {
        $query = DB::table('t_inventories')
            ->join('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->join('m_warehouses', 't_inventories.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('m_locations', 't_inventories.location_id', '=', 'm_locations.id')
            ->leftJoin('m_categories', 't_goods.category_id', '=', 'm_categories.id')
            ->select(
                't_inventories.*',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.min_stock_level',
                'm_warehouses.warehouse_name',
                'm_locations.location_code',
                'm_categories.category_name'
            )
            ->where('t_goods.delete_flg', 0);

        // 検索条件
        if ($request->filled('goods_number')) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $request->goods_number . '%');
        }
        if ($request->filled('goods_name')) {
            $query->where('t_goods.goods_name', 'LIKE', '%' . $request->goods_name . '%');
        }
        if ($request->filled('warehouse_id')) {
            $query->where('t_inventories.warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('location_id')) {
            $query->where('t_inventories.location_id', $request->location_id);
        }
        if ($request->filled('lot_number')) {
            $query->where('t_inventories.lot_number', 'LIKE', '%' . $request->lot_number . '%');
        }

        return $query->orderBy('t_goods.goods_number')->get();
    }
}
