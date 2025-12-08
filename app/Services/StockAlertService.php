<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockAlertService
{
    /**
     * 全在庫をチェックしてアラートを生成
     */
    public function checkAllStockAlerts()
    {
        $this->checkLowStockAlerts();
        $this->checkExpiryAlerts();
        $this->checkExcessStockAlerts();
    }

    /**
     * 低在庫・欠品アラートをチェック
     */
    public function checkLowStockAlerts()
    {
        // 在庫数が最低在庫数以下の商品を取得
        $lowStockItems = DB::table('t_inventories')
            ->join('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->join('m_warehouses', 't_inventories.warehouse_id', '=', 'm_warehouses.id')
            ->select(
                't_inventories.goods_id',
                't_inventories.warehouse_id',
                't_inventories.available_quantity',
                't_goods.min_stock_level',
                't_goods.reorder_point',
                't_goods.goods_number',
                't_goods.goods_name',
                'm_warehouses.warehouse_name'
            )
            ->where('t_goods.delete_flg', 0)
            ->where('m_warehouses.is_active', 1)
            ->whereNotNull('t_goods.min_stock_level')
            ->where('t_goods.min_stock_level', '>', 0)
            ->get();

        foreach ($lowStockItems as $item) {
            $alertType = null;
            $threshold = null;

            // 在庫0の場合は欠品アラート
            if ($item->available_quantity <= 0) {
                $alertType = 'out_of_stock';
                $threshold = 0;
            }
            // 在庫が最低在庫数以下の場合は低在庫アラート
            elseif ($item->available_quantity <= $item->min_stock_level) {
                $alertType = 'low_stock';
                $threshold = $item->min_stock_level;
            }
            // 発注点以下の場合も低在庫アラート
            elseif ($item->reorder_point && $item->available_quantity <= $item->reorder_point) {
                $alertType = 'low_stock';
                $threshold = $item->reorder_point;
            }

            if ($alertType) {
                $this->createOrUpdateAlert(
                    $item->goods_id,
                    $item->warehouse_id,
                    $alertType,
                    $item->available_quantity,
                    $threshold
                );
            }
        }
    }

    /**
     * 有効期限アラートをチェック
     */
    public function checkExpiryAlerts()
    {
        $now = Carbon::now();

        // 有効期限が設定されている在庫を取得
        $expiryItems = DB::table('t_inventories')
            ->join('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->select(
                't_inventories.goods_id',
                't_inventories.warehouse_id',
                't_inventories.available_quantity',
                't_inventories.expiry_date',
                't_goods.expiry_alert_days'
            )
            ->where('t_goods.delete_flg', 0)
            ->whereNotNull('t_inventories.expiry_date')
            ->where('t_inventories.available_quantity', '>', 0)
            ->get();

        foreach ($expiryItems as $item) {
            $expiryDate = Carbon::parse($item->expiry_date);
            $daysUntilExpiry = $now->diffInDays($expiryDate, false);
            
            $alertType = null;
            
            // 期限切れまたは7日以内
            if ($daysUntilExpiry < 0 || $daysUntilExpiry <= 7) {
                $alertType = 'expiry_critical';
            }
            // 30日以内または設定された日数以内
            elseif ($daysUntilExpiry <= 30 || ($item->expiry_alert_days && $daysUntilExpiry <= $item->expiry_alert_days)) {
                $alertType = 'expiry_warning';
            }

            if ($alertType) {
                $this->createOrUpdateAlert(
                    $item->goods_id,
                    $item->warehouse_id,
                    $alertType,
                    $item->available_quantity,
                    null,
                    $item->expiry_date
                );
            }
        }
    }

    /**
     * 過剰在庫アラートをチェック
     */
    public function checkExcessStockAlerts()
    {
        // 最大在庫数を超えている商品を取得
        $excessItems = DB::table('t_inventories')
            ->join('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->select(
                't_inventories.goods_id',
                't_inventories.warehouse_id',
                't_inventories.quantity',
                't_goods.max_stock_level'
            )
            ->where('t_goods.delete_flg', 0)
            ->whereNotNull('t_goods.max_stock_level')
            ->where('t_goods.max_stock_level', '>', 0)
            ->whereRaw('t_inventories.quantity > t_goods.max_stock_level')
            ->get();

        foreach ($excessItems as $item) {
            $this->createOrUpdateAlert(
                $item->goods_id,
                $item->warehouse_id,
                'excess',
                $item->quantity,
                $item->max_stock_level
            );
        }
    }

    /**
     * 特定の商品・倉庫の在庫アラートをチェック
     */
    public function checkStockAlert($goodsId, $warehouseId)
    {
        // 在庫情報を取得
        $inventory = DB::table('t_inventories')
            ->join('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->select(
                't_inventories.goods_id',
                't_inventories.warehouse_id',
                't_inventories.quantity',
                't_inventories.available_quantity',
                't_inventories.expiry_date',
                't_goods.min_stock_level',
                't_goods.max_stock_level',
                't_goods.reorder_point',
                't_goods.expiry_alert_days'
            )
            ->where('t_inventories.goods_id', $goodsId)
            ->where('t_inventories.warehouse_id', $warehouseId)
            ->where('t_goods.delete_flg', 0)
            ->first();

        if (!$inventory) {
            return;
        }

        // 既存のアラートを解決済みにする
        $this->resolveExistingAlerts($goodsId, $warehouseId);

        // 低在庫・欠品チェック
        if ($inventory->available_quantity <= 0) {
            $this->createOrUpdateAlert(
                $goodsId,
                $warehouseId,
                'out_of_stock',
                $inventory->available_quantity,
                0
            );
        } elseif ($inventory->min_stock_level && $inventory->available_quantity <= $inventory->min_stock_level) {
            $this->createOrUpdateAlert(
                $goodsId,
                $warehouseId,
                'low_stock',
                $inventory->available_quantity,
                $inventory->min_stock_level
            );
        } elseif ($inventory->reorder_point && $inventory->available_quantity <= $inventory->reorder_point) {
            $this->createOrUpdateAlert(
                $goodsId,
                $warehouseId,
                'low_stock',
                $inventory->available_quantity,
                $inventory->reorder_point
            );
        }

        // 過剰在庫チェック
        if ($inventory->max_stock_level && $inventory->quantity > $inventory->max_stock_level) {
            $this->createOrUpdateAlert(
                $goodsId,
                $warehouseId,
                'excess',
                $inventory->quantity,
                $inventory->max_stock_level
            );
        }

        // 有効期限チェック
        if ($inventory->expiry_date && $inventory->available_quantity > 0) {
            $now = Carbon::now();
            $expiryDate = Carbon::parse($inventory->expiry_date);
            $daysUntilExpiry = $now->diffInDays($expiryDate, false);

            if ($daysUntilExpiry < 0 || $daysUntilExpiry <= 7) {
                $this->createOrUpdateAlert(
                    $goodsId,
                    $warehouseId,
                    'expiry_critical',
                    $inventory->available_quantity,
                    null,
                    $inventory->expiry_date
                );
            } elseif ($daysUntilExpiry <= 30 || ($inventory->expiry_alert_days && $daysUntilExpiry <= $inventory->expiry_alert_days)) {
                $this->createOrUpdateAlert(
                    $goodsId,
                    $warehouseId,
                    'expiry_warning',
                    $inventory->available_quantity,
                    null,
                    $inventory->expiry_date
                );
            }
        }
    }

    /**
     * アラートを作成または更新
     */
    private function createOrUpdateAlert($goodsId, $warehouseId, $alertType, $currentQuantity, $thresholdQuantity = null, $expiryDate = null)
    {
        // 同じ商品・倉庫・アラート種別の未解決アラートが既に存在するかチェック
        $existingAlert = DB::table('t_stock_alerts')
            ->where('goods_id', $goodsId)
            ->where('warehouse_id', $warehouseId)
            ->where('alert_type', $alertType)
            ->where('is_resolved', 0)
            ->first();

        $now = Carbon::now();

        if ($existingAlert) {
            // 既存のアラートを更新
            DB::table('t_stock_alerts')
                ->where('id', $existingAlert->id)
                ->update([
                    'current_quantity' => $currentQuantity,
                    'threshold_quantity' => $thresholdQuantity,
                    'expiry_date' => $expiryDate,
                    'updated_at' => $now,
                ]);
        } else {
            // 新しいアラートを作成
            DB::table('t_stock_alerts')->insert([
                'goods_id' => $goodsId,
                'warehouse_id' => $warehouseId,
                'alert_type' => $alertType,
                'current_quantity' => $currentQuantity,
                'threshold_quantity' => $thresholdQuantity,
                'expiry_date' => $expiryDate,
                'alert_date' => $now,
                'is_resolved' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * 既存のアラートを解決済みにする（条件が改善された場合）
     */
    private function resolveExistingAlerts($goodsId, $warehouseId)
    {
        DB::table('t_stock_alerts')
            ->where('goods_id', $goodsId)
            ->where('warehouse_id', $warehouseId)
            ->where('is_resolved', 0)
            ->update([
                'is_resolved' => 1,
                'resolved_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
    }

    /**
     * 解決済みの古いアラートを削除
     */
    public function cleanupOldAlerts($daysToKeep = 30)
    {
        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        DB::table('t_stock_alerts')
            ->where('is_resolved', 1)
            ->where('resolved_at', '<', $cutoffDate)
            ->delete();
    }
}
