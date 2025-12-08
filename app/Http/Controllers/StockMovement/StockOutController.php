<?php

namespace App\Http\Controllers\StockMovement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

class StockOutController extends BaseController
{
    /**
     * 出庫登録画面を表示
     */
    public function index(Request $request)
    {
        // 商品一覧を取得
        $goods = DB::table('t_goods')
            ->where('delete_flg', 0)
            ->orderBy('goods_number')
            ->get();

        // 倉庫一覧を取得
        $warehouses = DB::table('m_warehouses')
            ->where('is_active', 1)
            ->orderBy('warehouse_code')
            ->get();

        // ロケーション一覧を取得
        $locations = DB::table('m_locations')
            ->join('m_warehouses', 'm_locations.warehouse_id', '=', 'm_warehouses.id')
            ->select('m_locations.id', 'm_locations.warehouse_id', 'm_locations.location_code', 'm_warehouses.warehouse_name')
            ->where('m_locations.is_active', 1)
            ->orderBy('m_warehouses.warehouse_code')
            ->orderBy('m_locations.location_code')
            ->get()
            ->groupBy('warehouse_id');

        return view('stock_movement.out', compact('goods', 'warehouses', 'locations'));
    }

    /**
     * 出庫登録を実行
     */
    public function store(Request $request)
    {
        $request->validate([
            'goods_id' => 'required|exists:t_goods,id',
            'warehouse_id' => 'required|exists:m_warehouses,id',
            'quantity' => 'required|integer|min:1',
            'movement_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $goodsId = $request->goods_id;
            $warehouseId = $request->warehouse_id;
            $locationId = $request->location_id;
            $quantity = $request->quantity;
            $lotNumber = $request->lot_number;
            $serialNumber = $request->serial_number;
            $movementDate = $request->movement_date;
            $notes = $request->notes;

            // 在庫レコードを取得
            $inventory = DB::table('t_inventories')
                ->where('goods_id', $goodsId)
                ->where('warehouse_id', $warehouseId)
                ->where(function($query) use ($locationId) {
                    if ($locationId) {
                        $query->where('location_id', $locationId);
                    } else {
                        $query->whereNull('location_id');
                    }
                })
                ->where(function($query) use ($lotNumber) {
                    if ($lotNumber) {
                        $query->where('lot_number', $lotNumber);
                    } else {
                        $query->whereNull('lot_number');
                    }
                })
                ->where(function($query) use ($serialNumber) {
                    if ($serialNumber) {
                        $query->where('serial_number', $serialNumber);
                    } else {
                        $query->whereNull('serial_number');
                    }
                })
                ->first();

            if (!$inventory) {
                return redirect()->back()->with('error', '指定された在庫が見つかりません。')->withInput();
            }

            // 利用可能在庫数をチェック
            if ($inventory->available_quantity < $quantity) {
                return redirect()->back()->with('error', '利用可能在庫数が不足しています。（利用可能: ' . $inventory->available_quantity . '）')->withInput();
            }

            $beforeQuantity = $inventory->quantity;
            $afterQuantity = $beforeQuantity - $quantity;

            // 在庫を減らす
            DB::table('t_inventories')
                ->where('id', $inventory->id)
                ->update([
                    'quantity' => DB::raw('quantity - ' . $quantity),
                    'available_quantity' => DB::raw('available_quantity - ' . $quantity),
                    'updated_at' => now(),
                ]);

            // 入出庫履歴を記録
            DB::table('t_stock_movements')->insert([
                'goods_id' => $goodsId,
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
                'lot_number' => $lotNumber,
                'serial_number' => $serialNumber,
                'movement_type' => 'out',
                'quantity' => -$quantity,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $afterQuantity,
                'reference_type' => 'manual_out',
                'reference_id' => null,
                'notes' => $notes,
                'user_id' => auth()->id(),
                'movement_date' => $movementDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // t_goods.goods_stockを更新（互換性のため）
            DB::table('t_goods')
                ->where('id', $goodsId)
                ->update([
                    'goods_stock' => DB::raw('goods_stock - ' . $quantity),
                    'up_date' => now(),
                ]);

            // 在庫アラートをチェック
            $alertService = new \App\Services\StockAlertService();
            $alertService->checkStockAlert($goodsId, $warehouseId);

            DB::commit();

            return redirect()->route('stock_out')->with('success', '出庫登録が完了しました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '出庫登録に失敗しました: ' . $e->getMessage())->withInput();
        }
    }
}
