<?php

namespace App\Http\Controllers\Goods\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;

class GoodsExportCsvController extends BaseController
{
    /**
     * 商品一覧をCSV形式でエクスポート
     * Export goods list in CSV format
     */
    public function __invoke(Request $request)
    {
        try {
            // 検索条件を取得
            $search_options = $request->all();
            
            // 商品データを取得（削除フラグが立っていないもの）
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

            // CSVファイル名を生成（タイムスタンプ付き）
            $filename = 'goods_export_' . date('YmdHis') . '.csv';

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
                
                // BOM（UTF-8 BOM）を出力してExcelで文字化けを防ぐ
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // CSVヘッダー行を出力
                $header = [
                    'ID',
                    'UUID',
                    '商品番号',
                    '商品名',
                    'カテゴリID',
                    'カテゴリ名',
                    'カテゴリコード',
                    '商品価格',
                    '税率(%)',
                    '税込価格',
                    '在庫数',
                    '最小在庫レベル',
                    '最大在庫レベル',
                    '発注点',
                    'リードタイム(日)',
                    'ロット管理',
                    'シリアル管理',
                    '有効期限アラート(日)',
                    '画像パス',
                    '紹介文',
                    '商品詳細',
                    '表示フラグ',
                    '販売開始日時',
                    '販売終了日時',
                    '登録日時',
                    '更新日時'
                ];
                fputcsv($file, $header);

                // データ行を出力
                foreach ($goods as $item) {
                    // 税込価格を計算
                    $price_with_tax = round($item->goods_price * (1 + $item->tax_rate / 100));
                    
                    // フラグを文字列に変換
                    $is_lot_managed = $item->is_lot_managed ? 'あり' : 'なし';
                    $is_serial_managed = $item->is_serial_managed ? 'あり' : 'なし';
                    $disp_flg = $item->disp_flg ? '表示' : '非表示';
                    
                    $row = [
                        $item->id,
                        $item->un_id,
                        $item->goods_number,
                        $item->goods_name,
                        $item->category_id ?? '',
                        $item->category_name ?? '',
                        $item->category_code ?? '',
                        $item->goods_price,
                        $item->tax_rate,
                        $price_with_tax,
                        $item->goods_stock,
                        $item->min_stock_level ?? '',
                        $item->max_stock_level ?? '',
                        $item->reorder_point ?? '',
                        $item->lead_time_days ?? '',
                        $is_lot_managed,
                        $is_serial_managed,
                        $item->expiry_alert_days ?? '',
                        $item->image_path ?? '',
                        $item->intro_txt ?? '',
                        $item->goods_detail ?? '',
                        $disp_flg,
                        $item->sales_start_at ?? '',
                        $item->sales_end_at ?? '',
                        $item->ins_date,
                        $item->up_date
                    ];
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            Log::info('CSV export executed', [
                'record_count' => $goods->count(),
                'filename' => $filename,
                'search_options' => $search_options
            ]);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('CSV export error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'CSVエクスポートに失敗しました: ' . $e->getMessage());
        }
    }
}
