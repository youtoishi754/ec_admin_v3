<?php

namespace App\Http\Controllers\Goods\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;

class GoodsExportDetailedCsvController extends BaseController
{
    /**
     * 在庫情報を含む詳細CSVエクスポート
     * Export detailed CSV with inventory information
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

            // CSVファイル名を生成
            $filename = 'goods_inventory_export_' . date('YmdHis') . '.csv';

            // レスポンスヘッダーを設定
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            // ストリームでCSVを出力
            $callback = function() use ($goods) {
                $file = fopen('php://output', 'w');
                
                // BOM出力
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // CSVヘッダー行
                $header = [
                    '商品番号',
                    '商品名',
                    'カテゴリ名',
                    '単価',
                    '税率(%)',
                    '税込価格',
                    '総在庫数',
                    '倉庫名',
                    'ロケーション',
                    'ロケーション在庫',
                    '引当数',
                    '利用可能数',
                    '最小在庫レベル',
                    '発注点',
                    '表示状態'
                ];
                fputcsv($file, $header);

                // データ行を出力
                foreach ($goods as $item) {
                    $price_with_tax = round($item->goods_price * (1 + $item->tax_rate / 100));
                    $disp_flg = $item->disp_flg ? '表示' : '非表示';
                    
                    $row = [
                        $item->goods_number,
                        $item->goods_name,
                        $item->category_name ?? '',
                        $item->goods_price,
                        $item->tax_rate,
                        $price_with_tax,
                        $item->total_stock,
                        $item->warehouse_name ?? '未割当',
                        $item->location_code ?? '未割当',
                        $item->location_quantity ?? 0,
                        $item->reserved_quantity ?? 0,
                        $item->available_quantity ?? 0,
                        $item->min_stock_level ?? '',
                        $item->reorder_point ?? '',
                        $disp_flg
                    ];
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            Log::info('Detailed CSV export executed', [
                'record_count' => $goods->count(),
                'filename' => $filename
            ]);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Detailed CSV export error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with('error', '詳細CSVエクスポートに失敗しました: ' . $e->getMessage());
        }
    }
}
