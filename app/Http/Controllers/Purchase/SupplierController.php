<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class SupplierController extends BaseController
{
    /**
     * 仕入先一覧を表示
     */
    public function index(Request $request)
    {
        $search_options = $request->all();

        $query = DB::table('m_suppliers');

        // 検索条件: 仕入先コード
        if (!empty($search_options['supplier_code'])) {
            $query->where('supplier_code', 'LIKE', '%' . $search_options['supplier_code'] . '%');
        }

        // 検索条件: 仕入先名
        if (!empty($search_options['supplier_name'])) {
            $query->where('supplier_name', 'LIKE', '%' . $search_options['supplier_name'] . '%');
        }

        // 検索条件: ステータス
        if (!empty($search_options['is_active'])) {
            $query->where('is_active', $search_options['is_active'] == 'active' ? 1 : 0);
        }

        $suppliers = $query->orderBy('supplier_code')->paginate(20);

        // 各仕入先の発注統計を取得
        foreach ($suppliers as $supplier) {
            $supplier->order_count = DB::table('t_purchase_orders')
                ->where('supplier_id', $supplier->id)
                ->count();
            $supplier->total_amount = DB::table('t_purchase_orders')
                ->where('supplier_id', $supplier->id)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            $supplier->goods_count = DB::table('t_goods')
                ->where('supplier_id', $supplier->id)
                ->where('delete_flg', 0)
                ->count();
        }

        // 統計情報
        $stats = [
            'total_suppliers' => DB::table('m_suppliers')->count(),
            'active_suppliers' => DB::table('m_suppliers')->where('is_active', 1)->count(),
            'inactive_suppliers' => DB::table('m_suppliers')->where('is_active', 0)->count(),
        ];

        return view('purchase.supplier_list', compact('suppliers', 'stats'));
    }

    /**
     * 仕入先登録画面を表示
     */
    public function create()
    {
        return view('purchase.supplier_create');
    }

    /**
     * 仕入先を保存
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_code' => 'required|string|max:20|unique:m_suppliers,supplier_code',
            'supplier_name' => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'postal_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:100',
            'lead_time_days' => 'nullable|integer|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::table('m_suppliers')->insert([
                'supplier_code' => $validated['supplier_code'],
                'supplier_name' => $validated['supplier_name'],
                'contact_person' => $validated['contact_person'] ?? null,
                'contact_email' => $validated['contact_email'] ?? null,
                'contact_phone' => $validated['contact_phone'] ?? null,
                'fax' => $validated['fax'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'address' => $validated['address'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'lead_time_days' => $validated['lead_time_days'] ?? null,
                'minimum_order_amount' => $validated['minimum_order_amount'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'is_active' => $validated['is_active'] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('supplier_list')
                ->with('success', '仕入先を登録しました。');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '仕入先の登録に失敗しました: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 仕入先編集画面を表示
     */
    public function edit($id)
    {
        $supplier = DB::table('m_suppliers')->where('id', $id)->first();

        if (!$supplier) {
            return redirect()->route('supplier_list')->with('error', '仕入先が見つかりません。');
        }

        // 取扱商品
        $goods = DB::table('t_goods')
            ->where('supplier_id', $id)
            ->where('delete_flg', 0)
            ->orderBy('goods_number')
            ->get();

        // 発注履歴
        $orders = DB::table('t_purchase_orders')
            ->where('supplier_id', $id)
            ->orderBy('order_date', 'desc')
            ->limit(10)
            ->get();

        return view('purchase.supplier_edit', compact('supplier', 'goods', 'orders'));
    }

    /**
     * 仕入先を更新
     */
    public function update(Request $request, $id)
    {
        $supplier = DB::table('m_suppliers')->where('id', $id)->first();

        if (!$supplier) {
            return redirect()->route('supplier_list')->with('error', '仕入先が見つかりません。');
        }

        $validated = $request->validate([
            'supplier_code' => 'required|string|max:20|unique:m_suppliers,supplier_code,' . $id,
            'supplier_name' => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'postal_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:100',
            'lead_time_days' => 'nullable|integer|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::table('m_suppliers')
                ->where('id', $id)
                ->update([
                    'supplier_code' => $validated['supplier_code'],
                    'supplier_name' => $validated['supplier_name'],
                    'contact_person' => $validated['contact_person'] ?? null,
                    'contact_email' => $validated['contact_email'] ?? null,
                    'contact_phone' => $validated['contact_phone'] ?? null,
                    'fax' => $validated['fax'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'payment_terms' => $validated['payment_terms'] ?? null,
                    'lead_time_days' => $validated['lead_time_days'] ?? null,
                    'minimum_order_amount' => $validated['minimum_order_amount'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'is_active' => $validated['is_active'] ?? 1,
                    'updated_at' => now(),
                ]);

            return redirect()->route('supplier_edit', ['id' => $id])
                ->with('success', '仕入先を更新しました。');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '仕入先の更新に失敗しました: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 仕入先を削除
     */
    public function destroy($id)
    {
        $supplier = DB::table('m_suppliers')->where('id', $id)->first();

        if (!$supplier) {
            return redirect()->route('supplier_list')->with('error', '仕入先が見つかりません。');
        }

        // 関連する発注があるか確認
        $orderCount = DB::table('t_purchase_orders')->where('supplier_id', $id)->count();
        $goodsCount = DB::table('t_goods')->where('supplier_id', $id)->where('delete_flg', 0)->count();

        if ($orderCount > 0 || $goodsCount > 0) {
            return redirect()->route('supplier_list')
                ->with('error', 'この仕入先は発注履歴または関連商品があるため削除できません。無効化をお勧めします。');
        }

        try {
            DB::table('m_suppliers')->where('id', $id)->delete();

            return redirect()->route('supplier_list')
                ->with('success', '仕入先を削除しました。');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '仕入先の削除に失敗しました: ' . $e->getMessage());
        }
    }
}
