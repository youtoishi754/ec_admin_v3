<?php

namespace App\Http\Controllers\Purchase\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class SupplierExportCsvController extends BaseController
{
    /**
     * 仕入先一覧をCSV形式でエクスポート
     */
    public function __invoke(Request $request)
    {
        // 仕入先データを取得
        $suppliers = $this->getSupplierData($request);

        // CSVファイル名
        $filename = 'supplier_list_' . date('Ymd_His') . '.csv';

        // CSVヘッダー
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($suppliers) {
            $file = fopen('php://output', 'w');
            
            // BOMを出力（Excel対応）
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // ヘッダー行
            fputcsv($file, [
                '仕入先コード',
                '仕入先名',
                '郵便番号',
                '住所',
                '電話番号',
                'FAX番号',
                '担当者',
                'メールアドレス',
                '支払条件',
                'ステータス',
                '取扱商品数',
                '発注件数',
                '累計発注額',
                '備考',
                '登録日'
            ]);

            // データ行
            foreach ($suppliers as $supplier) {
                fputcsv($file, [
                    $supplier->supplier_code,
                    $supplier->supplier_name,
                    $supplier->postal_code ?? '',
                    $supplier->address ?? '',
                    $supplier->tel ?? '',
                    $supplier->fax ?? '',
                    $supplier->contact_person ?? '',
                    $supplier->contact_email ?? '',
                    $supplier->payment_terms ?? '',
                    $supplier->is_active ? '有効' : '無効',
                    $supplier->goods_count ?? 0,
                    $supplier->order_count ?? 0,
                    $supplier->total_amount ?? 0,
                    $supplier->notes ?? '',
                    $supplier->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 仕入先データを取得
     */
    private function getSupplierData(Request $request)
    {
        $query = DB::table('m_suppliers')
            ->select('m_suppliers.*')
            ->selectRaw('(SELECT COUNT(*) FROM t_goods WHERE t_goods.supplier_id = m_suppliers.id AND t_goods.delete_flg = 0) as goods_count')
            ->selectRaw('(SELECT COUNT(*) FROM t_purchase_orders WHERE t_purchase_orders.supplier_id = m_suppliers.id) as order_count')
            ->selectRaw('(SELECT COALESCE(SUM(total_amount), 0) FROM t_purchase_orders WHERE t_purchase_orders.supplier_id = m_suppliers.id AND t_purchase_orders.status != "cancelled") as total_amount');

        // 検索条件: 仕入先コード
        if ($request->filled('supplier_code')) {
            $query->where('m_suppliers.supplier_code', 'LIKE', '%' . $request->supplier_code . '%');
        }

        // 検索条件: 仕入先名
        if ($request->filled('supplier_name')) {
            $query->where('m_suppliers.supplier_name', 'LIKE', '%' . $request->supplier_name . '%');
        }

        // 検索条件: ステータス
        if ($request->filled('is_active')) {
            $query->where('m_suppliers.is_active', $request->is_active == 'active' ? 1 : 0);
        }

        return $query->orderBy('m_suppliers.supplier_code')->get();
    }
}
