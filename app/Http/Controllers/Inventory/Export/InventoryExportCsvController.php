<?php

namespace App\Http\Controllers\Inventory\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class InventoryExportCsvController extends BaseController
{
    /**
     * リアルタイム在庫一覧をCSV形式でエクスポート
     */
    public function __invoke(Request $request)
    {
        // 在庫データを取得
        $inventories = $this->getInventoryData($request);

        // CSVファイル名
        $filename = 'inventory_' . date('Ymd_His') . '.csv';

        // CSVヘッダー
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($inventories) {
            $file = fopen('php://output', 'w');
            
            // BOMを出力（Excel対応）
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // ヘッダー行
            fputcsv($file, [
                '商品番号',
                '商品名',
                'カテゴリ',
                '倉庫',
                'ロケーション',
                'ロット番号',
                'シリアル番号',
                '在庫数',
                '引当数',
                '利用可能数',
                '有効期限',
                '入荷日',
                '更新日時'
            ]);

            // データ行
            foreach ($inventories as $inventory) {
                fputcsv($file, [
                    $inventory->goods_number,
                    $inventory->goods_name,
                    $inventory->category_name ?? '',
                    $inventory->warehouse_name,
                    $inventory->location_code ?? '',
                    $inventory->lot_number ?? '',
                    $inventory->serial_number ?? '',
                    $inventory->quantity,
                    $inventory->reserved_quantity,
                    $inventory->available_quantity,
                    $inventory->expiry_date ?? '',
                    $inventory->received_date ?? '',
                    $inventory->updated_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
