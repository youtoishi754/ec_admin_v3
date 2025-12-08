<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class LocationController extends BaseController
{
    /**
     * ロケーション一覧を表示
     * Display location list
     */
    public function index(Request $request)
    {
        // 検索条件を取得
        $search_options = $request->all();

        // ロケーションデータを取得
        $query = DB::table('m_locations')
            ->leftJoin('m_warehouses', 'm_locations.warehouse_id', '=', 'm_warehouses.id')
            ->select(
                'm_locations.*',
                'm_warehouses.warehouse_name',
                'm_warehouses.warehouse_code'
            );

        // 検索条件: 倉庫
        if (!empty($search_options['warehouse_id'])) {
            $query->where('m_locations.warehouse_id', $search_options['warehouse_id']);
        }

        // 検索条件: ロケーションコード
        if (!empty($search_options['location_code'])) {
            $query->where('m_locations.location_code', 'LIKE', '%' . $search_options['location_code'] . '%');
        }

        // 検索条件: 有効フラグ
        if (isset($search_options['is_active']) && $search_options['is_active'] !== '') {
            $query->where('m_locations.is_active', $search_options['is_active']);
        }

        // ソート
        $query->orderBy('m_warehouses.warehouse_code')
              ->orderBy('m_locations.location_code');

        // ページネーション
        $locations = $query->paginate(20);

        // 倉庫リスト取得
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        // 各ロケーションの在庫数を取得
        $location_ids = $locations->pluck('id')->toArray();
        $inventory_counts = DB::table('t_inventories')
            ->select('location_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('COUNT(DISTINCT goods_id) as goods_count'))
            ->whereIn('location_id', $location_ids)
            ->groupBy('location_id')
            ->get()
            ->keyBy('location_id');

        return view('inventory.location.index', compact('locations', 'warehouses', 'inventory_counts'));
    }

    /**
     * ロケーション登録画面表示
     * Show location create form
     */
    public function create()
    {
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        return view('inventory.location.create', compact('warehouses'));
    }

    /**
     * ロケーション登録処理
     * Store new location
     */
    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:m_warehouses,id',
            'location_code' => 'required|max:50',
            'aisle' => 'nullable|max:20',
            'rack' => 'nullable|max:20',
            'shelf' => 'nullable|max:20',
            'capacity' => 'nullable|integer|min:0',
        ]);

        try {
            // 重複チェック
            $exists = DB::table('m_locations')
                ->where('warehouse_id', $request->warehouse_id)
                ->where('location_code', $request->location_code)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', '同じ倉庫内に既に同じロケーションコードが存在します。');
            }

            DB::table('m_locations')->insert([
                'warehouse_id' => $request->warehouse_id,
                'location_code' => $request->location_code,
                'aisle' => $request->aisle,
                'rack' => $request->rack,
                'shelf' => $request->shelf,
                'capacity' => $request->capacity,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('inventory_location')->with('success', 'ロケーションを登録しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'ロケーション登録に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * ロケーション編集画面表示
     * Show location edit form
     */
    public function edit($id)
    {
        $location = DB::table('m_locations')->where('id', $id)->first();
        
        if (!$location) {
            return redirect()->route('inventory_location')->with('error', 'ロケーションが見つかりません。');
        }

        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        return view('inventory.location.edit', compact('location', 'warehouses'));
    }

    /**
     * ロケーション更新処理
     * Update location
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:m_warehouses,id',
            'location_code' => 'required|max:50',
            'aisle' => 'nullable|max:20',
            'rack' => 'nullable|max:20',
            'shelf' => 'nullable|max:20',
            'capacity' => 'nullable|integer|min:0',
        ]);

        try {
            // 重複チェック（自分自身を除く）
            $exists = DB::table('m_locations')
                ->where('warehouse_id', $request->warehouse_id)
                ->where('location_code', $request->location_code)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', '同じ倉庫内に既に同じロケーションコードが存在します。');
            }

            DB::table('m_locations')
                ->where('id', $id)
                ->update([
                    'warehouse_id' => $request->warehouse_id,
                    'location_code' => $request->location_code,
                    'aisle' => $request->aisle,
                    'rack' => $request->rack,
                    'shelf' => $request->shelf,
                    'capacity' => $request->capacity,
                    'is_active' => $request->has('is_active') ? 1 : 0,
                    'updated_at' => now()
                ]);

            return redirect()->route('inventory_location')->with('success', 'ロケーションを更新しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'ロケーション更新に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * ロケーション削除
     * Delete location
     */
    public function destroy($id)
    {
        try {
            // 在庫が存在するかチェック
            $has_inventory = DB::table('t_inventories')
                ->where('location_id', $id)
                ->where('quantity', '>', 0)
                ->exists();

            if ($has_inventory) {
                return redirect()->back()->with('error', 'このロケーションには在庫が存在するため削除できません。');
            }

            DB::table('m_locations')->where('id', $id)->delete();

            return redirect()->route('inventory_location')->with('success', 'ロケーションを削除しました。');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'ロケーション削除に失敗しました: ' . $e->getMessage());
        }
    }
}
