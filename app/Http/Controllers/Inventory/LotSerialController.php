<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class LotSerialController extends BaseController
{
    /**
     * ロット/シリアル番号管理一覧を表示
     * Display lot/serial number management list
     */
    public function index(Request $request)
    {
        // 検索条件を取得
        $search_options = $request->all();

        // ロットデータを取得（t_inventoriesから重複なしで取得）
        $query = DB::table('t_inventories')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->leftJoin('m_categories', 't_goods.category_id', '=', 'm_categories.id')
            ->select(
                't_inventories.lot_number',
                't_inventories.goods_id',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.image_path',
                'm_categories.category_name',
                't_inventories.expiry_date',
                't_inventories.manufacturing_date',
                DB::raw('MIN(t_inventories.received_date) as received_date'),
                DB::raw('SUM(t_inventories.quantity) as total_quantity'),
                DB::raw('SUM(t_inventories.available_quantity) as available_quantity')
            )
            ->whereNotNull('t_inventories.lot_number')
            ->where('t_goods.delete_flg', 0)
            ->groupBy('t_inventories.lot_number', 't_inventories.goods_id', 't_goods.goods_number', 't_goods.goods_name', 't_goods.image_path', 'm_categories.category_name', 't_inventories.expiry_date', 't_inventories.manufacturing_date');

        // 検索条件: 商品番号
        if (!empty($search_options['goods_number'])) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $search_options['goods_number'] . '%');
        }

        // 検索条件: ロット番号
        if (!empty($search_options['lot_number'])) {
            $query->where('t_inventories.lot_number', 'LIKE', '%' . $search_options['lot_number'] . '%');
        }

        // 検索条件: 有効期限（接近）
        if (!empty($search_options['expiry_alert'])) {
            $days = intval($search_options['expiry_alert']);
            $query->whereNotNull('t_inventories.expiry_date')
                  ->where('t_inventories.expiry_date', '<=', now()->addDays($days))
                  ->where('t_inventories.expiry_date', '>=', now());
        }

        // ソート
        $query->orderBy('t_inventories.expiry_date', 'asc')
              ->orderBy('t_inventories.lot_number', 'asc');

        // ページネーション
        $lots = $query->paginate(20);

        // inventory_totalsは既に含まれているので空配列
        $inventory_totals = [];

        // 統計情報
        $stats = [
            'total_lots' => DB::table('t_inventories')
                ->whereNotNull('lot_number')
                ->distinct('lot_number')
                ->count('lot_number'),
            'expiring_soon' => DB::table('t_inventories')
                ->whereNotNull('expiry_date')
                ->whereNotNull('lot_number')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>=', now())
                ->distinct('lot_number')
                ->count('lot_number'),
            'expired' => DB::table('t_inventories')
                ->whereNotNull('expiry_date')
                ->whereNotNull('lot_number')
                ->where('expiry_date', '<', now())
                ->distinct('lot_number')
                ->count('lot_number'),
            'pending_inspection' => 0, // 検品機能は将来実装
        ];

        return view('inventory.lot.index', compact('lots', 'inventory_totals', 'stats'));
    }

    /**
     * シリアル番号一覧を表示
     * Display serial number list
     */
    public function serialIndex(Request $request)
    {
        // 検索条件を取得
        $search_options = $request->all();

        // シリアル番号付き在庫データを取得
        $query = DB::table('t_inventories')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_inventories.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('m_locations', 't_inventories.location_id', '=', 'm_locations.id')
            ->select(
                't_inventories.*',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.image_path',
                'm_warehouses.warehouse_name',
                'm_locations.location_code'
            )
            ->whereNotNull('t_inventories.serial_number')
            ->where('t_goods.delete_flg', 0);

        // 検索条件: 商品番号
        if (!empty($search_options['goods_number'])) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $search_options['goods_number'] . '%');
        }

        // 検索条件: シリアル番号
        if (!empty($search_options['serial_number'])) {
            $query->where('t_inventories.serial_number', 'LIKE', '%' . $search_options['serial_number'] . '%');
        }

        // 検索条件: 倉庫
        if (!empty($search_options['warehouse_id'])) {
            $query->where('t_inventories.warehouse_id', $search_options['warehouse_id']);
        }

        // ソート
        $query->orderBy('t_goods.goods_number')
              ->orderBy('t_inventories.serial_number');

        // ページネーション
        $serials = $query->paginate(20);

        // 倉庫リスト
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        return view('inventory.lot.serial', compact('serials', 'warehouses'));
    }

    /**
     * ロット登録画面表示
     * Show lot create form
     */
    public function create()
    {
        $goods_list = DB::table('t_goods')
            ->where('delete_flg', 0)
            ->orderBy('goods_number')
            ->get();

        return view('inventory.lot.create', compact('goods_list'));
    }

    /**
     * ロット登録処理
     * Store new lot
     */
    public function store(Request $request)
    {
        $request->validate([
            'goods_id' => 'required|exists:t_goods,id',
            'lot_number' => 'required|max:50',
            'warehouse_id' => 'required|exists:m_warehouses,id',
            'quantity' => 'required|integer|min:0',
            'manufacturing_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // 重複チェック
            $exists = DB::table('t_inventories')
                ->where('goods_id', $request->goods_id)
                ->where('lot_number', $request->lot_number)
                ->where('warehouse_id', $request->warehouse_id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'この商品・倉庫で同じロット番号が既に存在します。');
            }

            // 在庫レコードを作成
            DB::table('t_inventories')->insert([
                'goods_id' => $request->goods_id,
                'warehouse_id' => $request->warehouse_id,
                'location_id' => $request->location_id,
                'lot_number' => $request->lot_number,
                'quantity' => $request->quantity,
                'reserved_quantity' => 0,
                'expiry_date' => $request->expiry_date,
                'manufacturing_date' => $request->manufacturing_date,
                'received_date' => now(),
                'status' => 'normal',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()->route('inventory_lot')->with('success', 'ロット番号を登録しました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'ロット登録に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * ロット編集画面表示
     * Show lot edit form
     */
    public function edit($id)
    {
        // ロット編集は在庫編集と統合
        return redirect()->route('inventory')->with('info', 'ロット情報の編集はリアルタイム在庫画面から行ってください。');
    }

    /**
     * ロット更新処理
     * Update lot
     */
    public function update(Request $request, $id)
    {
        // ロット編集は在庫編集と統合
        return redirect()->route('inventory')->with('info', 'ロット情報の編集はリアルタイム在庫画面から行ってください。');
    }
}
