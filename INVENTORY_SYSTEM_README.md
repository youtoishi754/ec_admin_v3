# 在庫管理システム 実装ガイド

## 概要
ECサイトの商品管理システムに在庫管理機能を追加しました。
リアルタイム在庫追跡、在庫アラート、ロケーション管理、ロット/シリアル番号管理、有効期限管理、在庫棚卸機能が含まれています。

## 実装内容

### 1. データベース設計
- **倉庫マスタ** (`m_warehouses`): 倉庫情報管理
- **ロケーションマスタ** (`m_locations`): 倉庫内の棚番号・ロケーション管理
- **在庫マスタ** (`t_inventories`): リアルタイム在庫数、引当数、利用可能在庫数
- **入出庫履歴** (`t_stock_movements`): 全ての在庫変動履歴
- **在庫アラート** (`t_stock_alerts`): 低在庫・欠品・過剰在庫・有効期限アラート
- **棚卸ヘッダー/明細** (`t_inventory_counts`, `t_inventory_count_details`): 棚卸機能
- **ロットマスタ** (`m_lots`): ロット番号・有効期限管理
- **t_goods拡張**: 最低在庫数、最大在庫数、発注点などのフィールド追加

### 2. 作成ファイル一覧

#### SQLファイル
- `database/sql/00_create_all_inventory_tables.sql` - 全テーブル一括作成SQL
- `database/sql/01_migrate_existing_stock_data.sql` - 既存データ移行SQL

#### マイグレーションファイル
- `database/migrations/2025_12_06_000001_create_warehouses_table.php`
- `database/migrations/2025_12_06_000002_create_locations_table.php`
- `database/migrations/2025_12_06_000003_create_inventories_table.php`
- `database/migrations/2025_12_06_000004_create_stock_movements_table.php`
- `database/migrations/2025_12_06_000005_create_stock_alerts_table.php`
- `database/migrations/2025_12_06_000006_create_inventory_counts_table.php`
- `database/migrations/2025_12_06_000007_create_inventory_count_details_table.php`
- `database/migrations/2025_12_06_000008_create_lots_table.php`
- `database/migrations/2025_12_06_000009_alter_t_goods_add_inventory_fields.php`

#### Eloquentモデル
- `app/Models/Warehouse.php` - 倉庫モデル
- `app/Models/Location.php` - ロケーションモデル
- `app/Models/Inventory.php` - 在庫モデル
- `app/Models/StockMovement.php` - 入出庫履歴モデル
- `app/Models/StockAlert.php` - 在庫アラートモデル
- `app/Models/Lot.php` - ロットモデル
- `app/Models/Goods.php` - 商品モデル（拡張版）

#### 機能拡張
- `app/DBManager.php` - 在庫管理対応版に更新
  - 在庫情報を含む商品一覧取得
  - 在庫アラート取得
  - 倉庫・ロケーション取得
  - 入出庫履歴取得
  - ヘルパー関数追加

- `resources/views/index.blade.php` - 商品一覧画面改修
  - 在庫数/利用可能在庫数表示
  - 在庫ステータスバッジ表示
  - 在庫ステータスフィルター追加
  - 在庫数ソート機能追加

## 実装手順

### ステップ1: データベーステーブル作成

#### 方法A: SQLファイルで一括作成（推奨）
```bash
# XAMPPのphpMyAdminまたはMySQLクライアントで実行
mysql -u root -p laravel < database/sql/00_create_all_inventory_tables.sql
```

#### 方法B: Laravelマイグレーション実行
```bash
cd f:\windows11_user\xampp\htdocs\ec_admin
php artisan migrate
```

### ステップ2: 既存在庫データの移行

既存の`t_goods.goods_stock`データを新しい在庫管理システムに移行します。

```bash
# SQLファイルを実行
mysql -u root -p laravel < database/sql/01_migrate_existing_stock_data.sql
```

**実行内容:**
- 既存の商品在庫を`t_inventories`に登録（本社倉庫=ID:1）
- 初期在庫登録履歴を`t_stock_movements`に記録
- 低在庫・欠品アラートを自動生成

### ステップ3: 商品マスタのデフォルト値設定

既存商品に在庫管理用のデフォルト値が設定されます（マイグレーションで自動実行）:
- `min_stock_level`: 10
- `max_stock_level`: 200
- `reorder_point`: 20
- `lead_time_days`: 7

### ステップ4: 動作確認

1. **商品一覧画面の確認**
   - `http://localhost/ec_admin/` にアクセス
   - 在庫数、利用可能在庫、在庫ステータスが表示されることを確認
   - 在庫ステータスフィルター（欠品/低在庫/正常）が動作することを確認

2. **在庫データの確認**
   ```sql
   -- 在庫マスタ確認
   SELECT * FROM t_inventories LIMIT 10;
   
   -- 在庫アラート確認
   SELECT * FROM t_stock_alerts WHERE is_resolved = 0;
   
   -- 倉庫確認
   SELECT * FROM m_warehouses;
   ```

## 主要機能の説明

### 1. リアルタイム在庫数表示
- `t_inventories.quantity`: 物理在庫数
- `t_inventories.reserved_quantity`: 注文引当済み数量
- `t_inventories.available_quantity`: 利用可能在庫数（自動計算カラム）

### 2. 在庫アラート
自動的に以下のアラートを生成:
- **欠品アラート**: 在庫数 = 0
- **低在庫アラート**: 在庫数 ≤ 最低在庫数
- **過剰在庫アラート**: 在庫数 > 最大在庫数
- **有効期限警告**: 有効期限まで30日以内
- **有効期限切迫**: 有効期限まで7日以内

### 3. ロケーション管理
倉庫内の保管場所を管理:
- 倉庫コード例: `WH-001`, `WH-002`
- ロケーションコード例: `A-01-01` (通路-棚-段)

### 4. 入出庫履歴
全ての在庫変動を記録:
- `in`: 入庫
- `out`: 出庫
- `adjust`: 在庫調整
- `transfer`: 倉庫間移動
- `return`: 返品入庫
- `reserve`: 引当
- `release`: 引当解除

### 5. ロット/シリアル番号管理
- ロット番号による管理
- シリアル番号による個別管理
- 有効期限管理（FIFO: First In First Out対応）

### 6. 在庫棚卸機能
- 棚卸計画作成
- 実地棚卸数入力
- システム在庫との差異確認
- 在庫調整

## 次のステップ（今後の実装）

### 1. 在庫管理画面の作成
- 在庫一覧画面
- 在庫詳細画面
- 入出庫登録画面
- 在庫調整画面

### 2. 在庫アラート画面
- アラート一覧画面
- アラート通知機能
- アラート解決処理

### 3. 棚卸機能画面
- 棚卸計画作成画面
- 棚卸実施画面
- 差異確認・調整画面

### 4. レポート機能
- 在庫回転率レポート
- ABC分析
- デッドストック検出
- 在庫推移グラフ

### 5. 注文連携機能
- 注文確定時の自動在庫引当
- 出荷完了時の在庫出庫
- キャンセル時の引当解除

## トラブルシューティング

### マイグレーション実行時のエラー

**エラー: `SQLSTATE[42S01]: Base table or view already exists`**
```bash
# 既にテーブルが存在する場合はロールバック
php artisan migrate:rollback

# または、特定のマイグレーションのみやり直し
php artisan migrate:refresh --path=database/migrations/2025_12_06_000003_create_inventories_table.php
```

**エラー: `SQLSTATE[42000]: Syntax error or access violation`**
- MySQL/MariaDBのバージョンを確認してください
- GENERATED COLUMNはMySQL 5.7以上、MariaDB 10.2以上が必要です

### データ移行時のエラー

**エラー: `Duplicate entry for key 'unique_inventory'`**
- 既にデータ移行済みの可能性があります
- `t_inventories`テーブルを確認してください:
  ```sql
  SELECT COUNT(*) FROM t_inventories;
  ```

### 画面表示の問題

**在庫情報が表示されない**
1. データ移行が完了しているか確認
2. `DBManager.php`の`getGoodsList()`関数が更新されているか確認
3. ブラウザのキャッシュをクリア

## 開発者向けメモ

### モデルの使用例

```php
use App\Models\Goods;
use App\Models\Inventory;
use App\Models\StockMovement;

// 商品の合計在庫取得
$goods = Goods::find(1);
$totalInventory = $goods->total_inventory;
$availableInventory = $goods->total_available_inventory;

// 在庫一覧取得
$inventories = Inventory::where('goods_id', 1)
    ->with(['warehouse', 'location'])
    ->get();

// 低在庫商品の取得
$lowStockItems = Inventory::lowStock()->get();

// 入出庫履歴の記録
StockMovement::create([
    'goods_id' => 1,
    'warehouse_id' => 1,
    'movement_type' => 'in',
    'quantity' => 100,
    'before_quantity' => 50,
    'after_quantity' => 150,
    'reference_type' => 'purchase_order',
    'reference_id' => 12345,
    'notes' => '入荷処理',
    'user_id' => auth()->id(),
    'movement_date' => now(),
]);
```

### ヘルパー関数の使用例

```php
// 商品情報取得（在庫情報付き）
$goods = getGoods($un_id);
echo $goods->total_inventory; // 合計在庫数
echo $goods->total_available; // 利用可能在庫数

// 在庫アラート取得
$alerts = getStockAlerts(['alert_type' => 'low_stock']);

// 在庫ステータスバッジ取得
echo getStockStatusBadge($current_stock, $min_stock_level);

// 倉庫一覧取得
$warehouses = getWarehouses(true); // 有効な倉庫のみ
```

## サポート・お問い合わせ

実装に関する質問やバグ報告は、開発チームまでご連絡ください。

---
**作成日**: 2025-12-06  
**バージョン**: 1.0.0  
**作成者**: AI Development Assistant
