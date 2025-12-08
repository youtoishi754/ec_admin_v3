<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        // 1. 倉庫マスタの作成（既存データがなければ作成）
        if (DB::table('m_warehouses')->count() == 0) {
            DB::table('m_warehouses')->insert([
                [
                    'warehouse_code' => 'WH-001',
                    'warehouse_name' => '東京倉庫',
                    'postal_code' => '135-0000',
                    'city' => '江東区',
                    'address_line' => '豊洲1-1-1',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'warehouse_code' => 'WH-002',
                    'warehouse_name' => '大阪倉庫',
                    'postal_code' => '530-0000',
                    'city' => '大阪市北区',
                    'address_line' => '梅田1-1-1',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'warehouse_code' => 'WH-003',
                    'warehouse_name' => '福岡倉庫',
                    'postal_code' => '810-0000',
                    'city' => '福岡市中央区',
                    'address_line' => '天神1-1-1',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }

        echo "Warehouses created.\n";

        // 2. ロケーションマスタの作成
        $warehouses = DB::table('m_warehouses')->get();
        $locationData = [];

        if (DB::table('m_locations')->count() == 0 && count($warehouses) > 0) {
            foreach ($warehouses as $warehouse) {
                // 各倉庫に15個のロケーションを作成
                for ($i = 1; $i <= 5; $i++) {
                    for ($j = 1; $j <= 3; $j++) {
                        $locationData[] = [
                            'warehouse_id' => $warehouse->id,
                            'location_code' => 'A-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . str_pad($j, 2, '0', STR_PAD_LEFT),
                            'aisle' => 'A',
                            'rack' => str_pad($i, 2, '0', STR_PAD_LEFT),
                            'shelf' => str_pad($j, 2, '0', STR_PAD_LEFT),
                            'capacity' => rand(50, 200),
                            'is_active' => 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            DB::table('m_locations')->insert($locationData);
            echo "Locations created.\n";
        }

        // 3. ロットマスタの作成（テーブルが存在する場合のみ）
        $goods = DB::table('t_goods')->where('delete_flg', 0)->limit(20)->get();
        $lotData = [];
        $lots = collect(); // 空のコレクション

        try {
            $lotCount = DB::table('t_lots')->count();
            
            if ($lotCount == 0 && count($goods) > 0) {
                foreach ($goods as $index => $good) {
                    $lotData[] = [
                        'goods_id' => $good->id,
                        'lot_number' => 'LOT' . date('Ymd') . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                        'production_date' => $now->copy()->subDays(rand(5, 30)),
                        'expiry_date' => $now->copy()->addDays(rand(30, 365)),
                        'supplier_name' => '仕入先' . (($index % 3) + 1),
                        'inspection_status' => 'passed',
                        'notes' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (count($lotData) > 0) {
                    DB::table('t_lots')->insert($lotData);
                    echo "Lots created.\n";
                }
            }
            
            $lots = DB::table('t_lots')->get();
        } catch (\Exception $e) {
            echo "Lots table does not exist. Skipping lot creation.\n";
        }

        // 4. 在庫データの作成
        $locations = DB::table('m_locations')->get();
        $inventoryData = [];

        if (DB::table('t_inventories')->count() == 0 && count($goods) > 0 && count($locations) > 0) {
            foreach ($goods as $good) {
                // 各商品を2-4ロケーションに配置
                $numLocations = rand(2, min(4, $locations->count()));
                $selectedLocations = $locations->random($numLocations);
                
                // その商品のロットを取得（ロットテーブルが存在する場合）
                $goodLot = $lots->where('goods_id', $good->id)->first();

                foreach ($selectedLocations as $location) {
                    $quantity = rand(10, 500);
                    $reserved = rand(0, min(50, $quantity));

                    $inventoryData[] = [
                        'goods_id' => $good->id,
                        'warehouse_id' => $location->warehouse_id,
                        'location_id' => $location->id,
                        'lot_number' => $goodLot ? $goodLot->lot_number : 'LOT-DEFAULT-' . str_pad($good->id, 4, '0', STR_PAD_LEFT),
                        'serial_number' => null,
                        'quantity' => $quantity,
                        'reserved_quantity' => $reserved,
                        'available_quantity' => $quantity - $reserved,
                        'expiry_date' => $goodLot && $goodLot->expiry_date ? $goodLot->expiry_date : null,
                        'last_counted_at' => $now->copy()->subDays(rand(1, 90)),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (count($inventoryData) > 0) {
                // バッチで挿入（大量データ対応）
                foreach (array_chunk($inventoryData, 100) as $chunk) {
                    DB::table('t_inventories')->insert($chunk);
                }
                echo "Inventories created: " . count($inventoryData) . " records.\n";
            }
        }

        // 5. 在庫アラートの作成
        $alertData = [];
        $inventories = DB::table('t_inventories')
            ->join('t_goods', 't_inventories.goods_id', '=', 't_goods.id')
            ->select('t_inventories.*', 't_goods.min_stock_level', 't_goods.max_stock_level')
            ->get();

        foreach ($inventories as $inventory) {
            // 低在庫アラート
            if ($inventory->available_quantity <= ($inventory->min_stock_level ?? 20)) {
                $alertData[] = [
                    'goods_id' => $inventory->goods_id,
                    'warehouse_id' => $inventory->warehouse_id,
                    'location_id' => $inventory->location_id,
                    'alert_type' => 'low_stock',
                    'alert_level' => $inventory->available_quantity == 0 ? 'critical' : 'warning',
                    'current_quantity' => $inventory->available_quantity,
                    'threshold_quantity' => $inventory->min_stock_level ?? 20,
                    'message' => '在庫が最低水準以下です',
                    'is_resolved' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (count($alertData) > 0) {
            DB::table('t_stock_alerts')->insert($alertData);
            echo "Stock alerts created: " . count($alertData) . " records.\n";
        }

        // 6. 在庫移動履歴のサンプル作成
        $inventories = DB::table('t_inventories')->limit(10)->get();
        $movementData = [];
        
        if (DB::table('t_stock_movements')->count() == 0 && count($inventories) > 0) {
            foreach ($inventories as $inventory) {
                $movementData[] = [
                    'goods_id' => $inventory->goods_id,
                    'warehouse_id' => $inventory->warehouse_id,
                    'location_id' => $inventory->location_id,
                    'lot_number' => $inventory->lot_number,
                    'serial_number' => null,
                    'movement_type' => 'in',
                    'quantity' => rand(50, 200),
                    'movement_date' => $now->copy()->subDays(rand(1, 30)),
                    'reference_type' => 'purchase_order',
                    'reference_id' => rand(1000, 9999),
                    'performed_by' => 'システム管理者',
                    'notes' => '初期入庫',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (count($movementData) > 0) {
                DB::table('t_stock_movements')->insert($movementData);
                echo "Stock movements created: " . count($movementData) . " records.\n";
            }
        }

        echo "\nInventory seeding completed successfully!\n";
    }
}
