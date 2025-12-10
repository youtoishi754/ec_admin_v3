<?php

namespace App\Http\Controllers\StockMovement\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class StockMovementExportCsvController extends BaseController
{
    /**
     * 入出庫履歴をCSV形式でエクスポート
     */
    public function __invoke(Request $request)
    {
        // 入出庫データを取得
        $movements = $this->getMovementData($request);

        // CSVファイル名
        $filename = 'stock_movement_' . date('Ymd_His') . '.csv';

        // CSVヘッダー
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($movements) {
            $file = fopen('php://output', 'w');
            
            // BOMを出力（Excel対応）
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // ヘッダー行
            fputcsv($file, [
                '入出庫日時',
                '入出庫区分',
                '商品番号',
                '商品名',
                '倉庫',
                'ロケーション',
                'ロット番号',
                '数量',
                '変更前在庫',
                '変更後在庫',
                '参照元',
                '備考'
            ]);

            // 区分の日本語変換
            $movementTypes = [
                'in' => '入庫',
                'out' => '出庫',
                'adjust' => '調整',
                'transfer' => '移動',
                'return' => '返品',
                'reserve' => '引当',
                'release' => '引当解除'
            ];

            // データ行
            foreach ($movements as $movement) {
                fputcsv($file, [
                    $movement->movement_date,
                    $movementTypes[$movement->movement_type] ?? $movement->movement_type,
                    $movement->goods_number,
                    $movement->goods_name,
                    $movement->warehouse_name,
                    $movement->location_code ?? '',
                    $movement->lot_number ?? '',
                    $movement->quantity,
                    $movement->before_quantity,
                    $movement->after_quantity,
                    $movement->reference_type ?? '',
                    $movement->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 入出庫データを取得
     */
    private function getMovementData(Request $request)
    {
        $query = DB::table('t_stock_movements')
            ->join('t_goods', 't_stock_movements.goods_id', '=', 't_goods.id')
            ->join('m_warehouses', 't_stock_movements.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('m_locations', 't_stock_movements.location_id', '=', 'm_locations.id')
            ->select(
                't_stock_movements.*',
                't_goods.goods_number',
                't_goods.goods_name',
                'm_warehouses.warehouse_name',
                'm_locations.location_code'
            );

        // 検索条件
        if ($request->filled('goods_number')) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $request->goods_number . '%');
        }
        if ($request->filled('goods_name')) {
            $query->where('t_goods.goods_name', 'LIKE', '%' . $request->goods_name . '%');
        }
        if ($request->filled('warehouse_id')) {
            $query->where('t_stock_movements.warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('movement_type')) {
            $query->where('t_stock_movements.movement_type', $request->movement_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('t_stock_movements.movement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('t_stock_movements.movement_date', '<=', $request->date_to);
        }

        return $query->orderBy('t_stock_movements.movement_date', 'desc')->get();
    }
}
