<?php

namespace App\Http\Controllers\StockMovement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class StockTransferController extends BaseController
{
    /**
     * 移動在庫画面を表示
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

        return view('stock_movement.transfer', compact('goods', 'warehouses', 'locations'));
    }

    /**
     * 移動在庫を実行
     */
    public function store(Request $request)
    {
        $request->validate([
            'goods_id' => 'required|exists:t_goods,id',
            'from_warehouse_id' => 'required|exists:m_warehouses,id',
            'to_warehouse_id' => 'required|exists:m_warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|integer|min:1',
            'movement_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $goodsId = $request->goods_id;
            $fromWarehouseId = $request->from_warehouse_id;
            $toWarehouseId = $request->to_warehouse_id;
            $fromLocationId = $request->from_location_id;
            $toLocationId = $request->to_location_id;
            $quantity = $request->quantity;
            $lotNumber = $request->lot_number;
            $serialNumber = $request->serial_number;
            $movementDate = $request->movement_date;
            $notes = $request->notes;

            // 移動元の在庫レコードを取得
            $fromInventory = DB::table('t_inventories')
                ->where('goods_id', $goodsId)
                ->where('warehouse_id', $fromWarehouseId)
                ->where(function($query) use ($fromLocationId) {
                    if ($fromLocationId) {
                        $query->where('location_id', $fromLocationId);
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

            if (!$fromInventory) {
                return redirect()->back()->with('error', '移動元の在庫が見つかりません。')->withInput();
            }

            // 利用可能在庫数をチェック
            if ($fromInventory->available_quantity < $quantity) {
                return redirect()->back()->with('error', '移動元の利用可能在庫数が不足しています。（利用可能: ' . $fromInventory->available_quantity . '）')->withInput();
            }

            $fromBeforeQuantity = $fromInventory->quantity;
            $fromAfterQuantity = $fromBeforeQuantity - $quantity;

            // 移動元の在庫を減らす
            DB::table('t_inventories')
                ->where('id', $fromInventory->id)
                ->update([
                    'quantity' => DB::raw('quantity - ' . $quantity),
                    'available_quantity' => DB::raw('available_quantity - ' . $quantity),
                    'updated_at' => now(),
                ]);

            // 移動先の在庫レコードを取得または作成
            $toInventory = DB::table('t_inventories')
                ->where('goods_id', $goodsId)
                ->where('warehouse_id', $toWarehouseId)
                ->where(function($query) use ($toLocationId) {
                    if ($toLocationId) {
                        $query->where('location_id', $toLocationId);
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

            $toBeforeQuantity = $toInventory ? $toInventory->quantity : 0;
            $toAfterQuantity = $toBeforeQuantity + $quantity;

            if ($toInventory) {
                // 既存在庫を更新
                DB::table('t_inventories')
                    ->where('id', $toInventory->id)
                    ->update([
                        'quantity' => DB::raw('quantity + ' . $quantity),
                        'available_quantity' => DB::raw('available_quantity + ' . $quantity),
                        'updated_at' => now(),
                    ]);
            } else {
                // 新規在庫レコードを作成（移動元の情報を引き継ぐ）
                DB::table('t_inventories')->insert([
                    'goods_id' => $goodsId,
                    'warehouse_id' => $toWarehouseId,
                    'location_id' => $toLocationId,
                    'lot_number' => $lotNumber,
                    'serial_number' => $serialNumber,
                    'quantity' => $quantity,
                    'reserved_quantity' => 0,
                    'available_quantity' => $quantity,
                    'expiry_date' => $fromInventory->expiry_date,
                    'manufacturing_date' => $fromInventory->manufacturing_date,
                    'received_date' => $movementDate,
                    'status' => 'normal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 移動元の出庫履歴を記録
            DB::table('t_stock_movements')->insert([
                'goods_id' => $goodsId,
                'warehouse_id' => $fromWarehouseId,
                'location_id' => $fromLocationId,
                'lot_number' => $lotNumber,
                'serial_number' => $serialNumber,
                'movement_type' => 'transfer',
                'quantity' => -$quantity,
                'before_quantity' => $fromBeforeQuantity,
                'after_quantity' => $fromAfterQuantity,
                'reference_type' => 'transfer_out',
                'reference_id' => $toWarehouseId,
                'notes' => '移動出庫（移動先: 倉庫ID=' . $toWarehouseId . '） ' . $notes,
                'user_id' => auth()->id(),
                'movement_date' => $movementDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 移動先の入庫履歴を記録
            DB::table('t_stock_movements')->insert([
                'goods_id' => $goodsId,
                'warehouse_id' => $toWarehouseId,
                'location_id' => $toLocationId,
                'lot_number' => $lotNumber,
                'serial_number' => $serialNumber,
                'movement_type' => 'transfer',
                'quantity' => $quantity,
                'before_quantity' => $toBeforeQuantity,
                'after_quantity' => $toAfterQuantity,
                'reference_type' => 'transfer_in',
                'reference_id' => $fromWarehouseId,
                'notes' => '移動入庫（移動元: 倉庫ID=' . $fromWarehouseId . '） ' . $notes,
                'user_id' => auth()->id(),
                'movement_date' => $movementDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 在庫アラートをチェック（両方の倉庫）
            $alertService = new \App\Services\StockAlertService();
            $alertService->checkStockAlert($goodsId, $fromWarehouseId);
            $alertService->checkStockAlert($goodsId, $toWarehouseId);

            DB::commit();

            return redirect()->route('stock_transfer')->with('success', '移動在庫が完了しました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '移動在庫に失敗しました: ' . $e->getMessage())->withInput();
        }
    }
}
