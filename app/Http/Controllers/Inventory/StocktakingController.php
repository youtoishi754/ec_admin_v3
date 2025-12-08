<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class StocktakingController extends BaseController
{
    /**
     * 在庫棚卸一覧を表示
     * Display stocktaking list
     */
    public function index(Request $request)
    {
        // 検索条件を取得
        $search_options = $request->all();

        // 在庫データを取得（棚卸用）
        $query = DB::table('t_inventories')
            ->leftJoin('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_inventories.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('m_locations', 't_inventories.location_id', '=', 'm_locations.id')
            ->select(
                't_inventories.id',
                't_inventories.goods_id',
                't_goods.goods_number',
                't_goods.goods_name',
                't_goods.image_path',
                'm_warehouses.warehouse_name',
                'm_warehouses.warehouse_code',
                'm_locations.location_code',
                't_inventories.lot_number',
                't_inventories.serial_number',
                't_inventories.quantity as system_quantity',
                't_inventories.reserved_quantity'
            )
            ->where('t_goods.delete_flg', 0);

        // 検索条件: 倉庫
        if (!empty($search_options['warehouse_id'])) {
            $query->where('t_inventories.warehouse_id', $search_options['warehouse_id']);
        }

        // 検索条件: ロケーション
        if (!empty($search_options['location_id'])) {
            $query->where('t_inventories.location_id', $search_options['location_id']);
        }

        // 検索条件: 商品番号
        if (!empty($search_options['goods_number'])) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $search_options['goods_number'] . '%');
        }

        // 検索条件: 未棚卸のみ（将来実装）
        // if (!empty($search_options['not_counted_only'])) {
        //     $days = intval($search_options['not_counted_days'] ?? 90);
        //     // TODO: 棚卸履歴テーブルから最終棚卸日を確認
        // }

        // ソート
        $query->orderBy('m_warehouses.warehouse_code')
              ->orderBy('m_locations.location_code')
              ->orderBy('t_goods.goods_number');

        // ページネーション
        $inventories = $query->paginate(20);

        // 倉庫リスト取得
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        // ロケーションリスト取得
        $locations = DB::table('m_locations')
            ->where('is_active', 1)
            ->orderBy('location_code')
            ->get();

        // 統計情報
        $stats = [
            'total_items' => DB::table('t_inventories')->count(),
            'total_stocktaking_today' => DB::table('t_stock_movements')
                ->where('movement_type', 'adjust')
                ->where('reference_type', 'stocktaking')
                ->whereDate('movement_date', now())
                ->count(),
            'total_adjustments' => DB::table('t_stock_movements')
                ->where('movement_type', 'adjust')
                ->where('reference_type', 'stocktaking')
                ->count(),
        ];

        return view('inventory.stocktaking.index', compact('inventories', 'warehouses', 'locations', 'stats'));
    }

    /**
     * 棚卸登録画面表示
     * Show stocktaking form
     */
    public function create(Request $request)
    {
        $inventory_id = $request->get('inventory_id');
        
        if (!$inventory_id) {
            return redirect()->route('inventory_stocktaking')->with('error', '在庫IDが指定されていません。');
        }

        $inventory = DB::table('t_inventories')
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
            ->where('t_inventories.id', $inventory_id)
            ->first();

        if (!$inventory) {
            return redirect()->route('inventory_stocktaking')->with('error', '在庫情報が見つかりません。');
        }

        return view('inventory.stocktaking.create', compact('inventory'));
    }

    /**
     * 棚卸登録処理（差異調整）
     * Store stocktaking result and adjust inventory
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:t_inventories,id',
            'counted_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // 在庫情報取得
            $inventory = DB::table('t_inventories')->where('id', $request->inventory_id)->first();
            
            if (!$inventory) {
                throw new \Exception('在庫情報が見つかりません。');
            }

            $system_quantity = $inventory->quantity;
            $counted_quantity = $request->counted_quantity;
            $difference = $counted_quantity - $system_quantity;

            // 差異がある場合のみ調整
            if ($difference != 0) {
                // 在庫数を更新
                DB::table('t_inventories')
                    ->where('id', $request->inventory_id)
                    ->update([
                        'quantity' => $counted_quantity,
                        'available_quantity' => $counted_quantity - $inventory->reserved_quantity,
                        'updated_at' => now()
                    ]);

                // 商品マスタの在庫も更新
                $total_inventory = DB::table('t_inventories')
                    ->where('goods_id', $inventory->goods_id)
                    ->sum('quantity');

                DB::table('t_goods')
                    ->where('id', $inventory->goods_id)
                    ->update([
                        'goods_stock' => $total_inventory,
                        'up_date' => now()
                    ]);

                // 入出庫履歴に記録
                DB::table('t_stock_movements')->insert([
                    'goods_id' => $inventory->goods_id,
                    'warehouse_id' => $inventory->warehouse_id,
                    'location_id' => $inventory->location_id,
                    'lot_number' => $inventory->lot_number,
                    'serial_number' => $inventory->serial_number,
                    'movement_type' => 'adjust',
                    'quantity' => $difference,
                    'before_quantity' => $system_quantity,
                    'after_quantity' => $counted_quantity,
                    'reference_type' => 'stocktaking',
                    'reference_id' => $request->inventory_id,
                    'notes' => '棚卸差異調整: ' . $request->notes,
                    'user_id' => auth()->id(),
                    'movement_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            // 差異がない場合も更新日時を記録
            // last_counted_atは将来的に棚卸履歴テーブルで管理

            DB::commit();

            $message = $difference == 0 
                ? '棚卸を完了しました。（差異なし）' 
                : sprintf('棚卸を完了しました。（差異: %+d）', $difference);

            return redirect()->route('inventory_stocktaking')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', '棚卸登録に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * 棚卸履歴表示
     * Display stocktaking history
     */
    public function history(Request $request)
    {
        // 棚卸履歴（adjustment タイプの入出庫履歴）を取得
        $query = DB::table('t_stock_movements')
            ->leftJoin('t_goods', 't_stock_movements.goods_id', '=', 't_goods.id')
            ->leftJoin('m_warehouses', 't_stock_movements.warehouse_id', '=', 'm_warehouses.id')
            ->leftJoin('m_locations', 't_stock_movements.location_id', '=', 'm_locations.id')
            ->leftJoin('users', 't_stock_movements.user_id', '=', 'users.id')
            ->select(
                't_stock_movements.*',
                't_goods.goods_number',
                't_goods.goods_name',
                'm_warehouses.warehouse_name',
                'm_locations.location_code',
                'users.name as user_name'
            )
            ->where('t_stock_movements.movement_type', 'adjust')
            ->where('t_stock_movements.reference_type', 'stocktaking');

        // 検索条件: 商品番号
        if (!empty($request->goods_number)) {
            $query->where('t_goods.goods_number', 'LIKE', '%' . $request->goods_number . '%');
        }

        // 検索条件: 倉庫
        if (!empty($request->warehouse_id)) {
            $query->where('t_stock_movements.warehouse_id', $request->warehouse_id);
        }

        // ソート
        $query->orderBy('t_stock_movements.movement_date', 'desc');

        // ページネーション
        $history = $query->paginate(20);

        // 倉庫リスト
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        return view('inventory.stocktaking.history', compact('history', 'warehouses'));
    }
}
