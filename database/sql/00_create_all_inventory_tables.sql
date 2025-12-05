-- ===================================
-- 在庫管理システム 全テーブル作成SQL
-- 作成日: 2025-12-06
-- 説明: ECサイトに在庫管理機能を追加するための全テーブルを作成
-- 実行順序: このファイルを実行するだけで全て作成
-- ===================================

-- 1. 倉庫マスタ
CREATE TABLE IF NOT EXISTS m_warehouses (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  warehouse_code VARCHAR(20) NOT NULL UNIQUE COMMENT '倉庫コード',
  warehouse_name VARCHAR(100) NOT NULL COMMENT '倉庫名',
  postal_code VARCHAR(8) NULL COMMENT '郵便番号',
  prefecture_id BIGINT UNSIGNED NULL COMMENT '都道府県ID',
  city VARCHAR(100) NULL COMMENT '市区町村',
  address_line VARCHAR(255) NULL COMMENT '番地・建物',
  manager_name VARCHAR(100) NULL COMMENT '管理者名',
  phone VARCHAR(20) NULL COMMENT '電話番号',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_warehouse_code (warehouse_code),
  INDEX idx_is_active (is_active),
  FOREIGN KEY (prefecture_id) REFERENCES m_prefectures(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='倉庫マスタ';

-- 2. ロケーションマスタ
CREATE TABLE IF NOT EXISTS m_locations (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '倉庫ID',
  location_code VARCHAR(30) NOT NULL COMMENT 'ロケーションコード',
  aisle VARCHAR(10) NULL COMMENT '通路番号',
  rack VARCHAR(10) NULL COMMENT '棚番号',
  shelf VARCHAR(10) NULL COMMENT '段番号',
  capacity INT NULL COMMENT '収容可能数',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY unique_location (warehouse_id, location_code),
  INDEX idx_warehouse (warehouse_id),
  INDEX idx_location_code (location_code),
  INDEX idx_is_active (is_active),
  FOREIGN KEY (warehouse_id) REFERENCES m_warehouses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ロケーションマスタ';

-- 3. 在庫マスタ
CREATE TABLE IF NOT EXISTS t_inventories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  goods_id BIGINT UNSIGNED NOT NULL COMMENT '商品ID',
  warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '倉庫ID',
  location_id BIGINT UNSIGNED NULL COMMENT 'ロケーションID',
  lot_number VARCHAR(50) NULL COMMENT 'ロット番号',
  serial_number VARCHAR(100) NULL COMMENT 'シリアル番号',
  quantity INT NOT NULL DEFAULT 0 COMMENT '在庫数',
  reserved_quantity INT NOT NULL DEFAULT 0 COMMENT '引当済み数量',
  available_quantity INT GENERATED ALWAYS AS (quantity - reserved_quantity) STORED COMMENT '利用可能在庫数',
  expiry_date DATE NULL COMMENT '有効期限',
  manufacturing_date DATE NULL COMMENT '製造日',
  received_date DATE NULL COMMENT '入荷日',
  alert_threshold INT NULL COMMENT 'アラート閾値',
  status ENUM('normal', 'low_stock', 'out_of_stock', 'excess', 'expired') NOT NULL DEFAULT 'normal' COMMENT 'ステータス',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_goods (goods_id),
  INDEX idx_warehouse (warehouse_id),
  INDEX idx_location (location_id),
  INDEX idx_lot (lot_number),
  INDEX idx_serial (serial_number),
  INDEX idx_expiry (expiry_date),
  INDEX idx_status (status),
  INDEX idx_available (available_quantity),
  UNIQUE KEY unique_inventory (goods_id, warehouse_id, location_id, lot_number, serial_number),
  FOREIGN KEY (goods_id) REFERENCES t_goods(id) ON DELETE CASCADE,
  FOREIGN KEY (warehouse_id) REFERENCES m_warehouses(id) ON DELETE RESTRICT,
  FOREIGN KEY (location_id) REFERENCES m_locations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='在庫マスタ';

-- 4. 入出庫履歴
CREATE TABLE IF NOT EXISTS t_stock_movements (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  goods_id BIGINT UNSIGNED NOT NULL COMMENT '商品ID',
  warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '倉庫ID',
  location_id BIGINT UNSIGNED NULL COMMENT 'ロケーションID',
  lot_number VARCHAR(50) NULL COMMENT 'ロット番号',
  serial_number VARCHAR(100) NULL COMMENT 'シリアル番号',
  movement_type ENUM('in', 'out', 'adjust', 'transfer', 'return', 'reserve', 'release') NOT NULL COMMENT '入出庫区分',
  quantity INT NOT NULL COMMENT '数量(±)',
  before_quantity INT NOT NULL COMMENT '変更前在庫数',
  after_quantity INT NOT NULL COMMENT '変更後在庫数',
  reference_type VARCHAR(50) NULL COMMENT '参照元',
  reference_id BIGINT UNSIGNED NULL COMMENT '参照元ID',
  notes TEXT NULL COMMENT '備考',
  user_id BIGINT UNSIGNED NULL COMMENT '処理者',
  movement_date DATETIME NOT NULL COMMENT '入出庫日時',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_goods (goods_id),
  INDEX idx_warehouse (warehouse_id),
  INDEX idx_movement_type (movement_type),
  INDEX idx_movement_date (movement_date),
  INDEX idx_reference (reference_type, reference_id),
  INDEX idx_user (user_id),
  FOREIGN KEY (goods_id) REFERENCES t_goods(id) ON DELETE CASCADE,
  FOREIGN KEY (warehouse_id) REFERENCES m_warehouses(id) ON DELETE RESTRICT,
  FOREIGN KEY (location_id) REFERENCES m_locations(id) ON DELETE SET NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='入出庫履歴';

-- 5. 在庫アラート
CREATE TABLE IF NOT EXISTS t_stock_alerts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  goods_id BIGINT UNSIGNED NOT NULL COMMENT '商品ID',
  warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '倉庫ID',
  alert_type ENUM('low_stock', 'out_of_stock', 'excess', 'expiry_warning', 'expiry_critical') NOT NULL COMMENT 'アラート種別',
  current_quantity INT NOT NULL COMMENT '現在在庫数',
  threshold_quantity INT NULL COMMENT '閾値',
  expiry_date DATE NULL COMMENT '有効期限',
  alert_date DATETIME NOT NULL COMMENT 'アラート発生日時',
  is_resolved TINYINT(1) NOT NULL DEFAULT 0 COMMENT '解決済みフラグ',
  resolved_at DATETIME NULL COMMENT '解決日時',
  resolved_by BIGINT UNSIGNED NULL COMMENT '解決者',
  notes TEXT NULL COMMENT '備考',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_goods (goods_id),
  INDEX idx_warehouse (warehouse_id),
  INDEX idx_alert_type (alert_type),
  INDEX idx_is_resolved (is_resolved),
  INDEX idx_alert_date (alert_date),
  FOREIGN KEY (goods_id) REFERENCES t_goods(id) ON DELETE CASCADE,
  FOREIGN KEY (warehouse_id) REFERENCES m_warehouses(id) ON DELETE CASCADE,
  FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='在庫アラート';

-- 6. 棚卸ヘッダー
CREATE TABLE IF NOT EXISTS t_inventory_counts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  count_number VARCHAR(30) NOT NULL UNIQUE COMMENT '棚卸番号',
  warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '倉庫ID',
  count_date DATE NOT NULL COMMENT '棚卸日',
  status ENUM('planning', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'planning' COMMENT 'ステータス',
  user_id BIGINT UNSIGNED NULL COMMENT '実施者',
  total_items INT NULL COMMENT '対象商品数',
  checked_items INT NULL DEFAULT 0 COMMENT 'チェック済み数',
  notes TEXT NULL COMMENT '備考',
  started_at DATETIME NULL COMMENT '開始日時',
  completed_at DATETIME NULL COMMENT '完了日時',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_count_number (count_number),
  INDEX idx_warehouse (warehouse_id),
  INDEX idx_count_date (count_date),
  INDEX idx_status (status),
  INDEX idx_user (user_id),
  FOREIGN KEY (warehouse_id) REFERENCES m_warehouses(id) ON DELETE RESTRICT,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='棚卸ヘッダー';

-- 7. 棚卸明細
CREATE TABLE IF NOT EXISTS t_inventory_count_details (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  inventory_count_id BIGINT UNSIGNED NOT NULL COMMENT '棚卸ID',
  goods_id BIGINT UNSIGNED NOT NULL COMMENT '商品ID',
  location_id BIGINT UNSIGNED NULL COMMENT 'ロケーションID',
  lot_number VARCHAR(50) NULL COMMENT 'ロット番号',
  system_quantity INT NOT NULL COMMENT 'システム在庫数',
  counted_quantity INT NULL COMMENT '実地棚卸数',
  difference INT GENERATED ALWAYS AS (counted_quantity - system_quantity) STORED COMMENT '差異',
  adjustment_reason TEXT NULL COMMENT '調整理由',
  is_adjusted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '調整済みフラグ',
  counted_at DATETIME NULL COMMENT '棚卸実施日時',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_inventory_count (inventory_count_id),
  INDEX idx_goods (goods_id),
  INDEX idx_is_adjusted (is_adjusted),
  FOREIGN KEY (inventory_count_id) REFERENCES t_inventory_counts(id) ON DELETE CASCADE,
  FOREIGN KEY (goods_id) REFERENCES t_goods(id) ON DELETE CASCADE,
  FOREIGN KEY (location_id) REFERENCES m_locations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='棚卸明細';

-- 8. ロットマスタ
CREATE TABLE IF NOT EXISTS m_lots (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  lot_number VARCHAR(50) NOT NULL UNIQUE COMMENT 'ロット番号',
  goods_id BIGINT UNSIGNED NOT NULL COMMENT '商品ID',
  manufacturing_date DATE NULL COMMENT '製造日',
  expiry_date DATE NULL COMMENT '有効期限',
  received_date DATE NOT NULL COMMENT '入荷日',
  quantity_received INT NOT NULL COMMENT '入荷数量',
  quantity_remaining INT NOT NULL COMMENT '残数量',
  supplier_name VARCHAR(100) NULL COMMENT '仕入先名',
  notes TEXT NULL COMMENT '備考',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_lot_number (lot_number),
  INDEX idx_goods (goods_id),
  INDEX idx_expiry_date (expiry_date),
  INDEX idx_is_active (is_active),
  FOREIGN KEY (goods_id) REFERENCES t_goods(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ロットマスタ';

-- 9. 既存t_goodsテーブル拡張
ALTER TABLE t_goods 
ADD COLUMN IF NOT EXISTS min_stock_level INT NULL COMMENT '最低在庫数' AFTER goods_stock,
ADD COLUMN IF NOT EXISTS max_stock_level INT NULL COMMENT '最大在庫数' AFTER min_stock_level,
ADD COLUMN IF NOT EXISTS reorder_point INT NULL COMMENT '発注点' AFTER max_stock_level,
ADD COLUMN IF NOT EXISTS lead_time_days INT NULL COMMENT 'リードタイム日数' AFTER reorder_point,
ADD COLUMN IF NOT EXISTS is_lot_managed TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'ロット管理フラグ' AFTER lead_time_days,
ADD COLUMN IF NOT EXISTS is_serial_managed TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'シリアル管理フラグ' AFTER is_lot_managed,
ADD COLUMN IF NOT EXISTS expiry_alert_days INT NULL COMMENT '有効期限アラート日数' AFTER is_serial_managed;

-- 10. 初期データ挿入
-- デフォルト倉庫
INSERT INTO m_warehouses (warehouse_code, warehouse_name, postal_code, prefecture_id, city, address_line, manager_name, phone, is_active, created_at, updated_at) 
VALUES 
('WH-001', '本社倉庫', '100-0001', 13, '千代田区', '千代田1-1-1', '倉庫管理者', '03-1234-5678', 1, NOW(), NOW()),
('WH-002', '第二倉庫', '530-0001', 27, '大阪市北区', '梅田1-1-1', NULL, NULL, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE warehouse_name = warehouse_name;

-- サンプルロケーション
INSERT INTO m_locations (warehouse_id, location_code, aisle, rack, shelf, capacity, is_active, created_at, updated_at)
VALUES
(1, 'A-01-01', 'A', '01', '01', 100, 1, NOW(), NOW()),
(1, 'A-01-02', 'A', '01', '02', 100, 1, NOW(), NOW()),
(1, 'A-02-01', 'A', '02', '01', 100, 1, NOW(), NOW()),
(1, 'B-01-01', 'B', '01', '01', 150, 1, NOW(), NOW()),
(1, 'B-01-02', 'B', '01', '02', 150, 1, NOW(), NOW()),
(2, 'A-01-01', 'A', '01', '01', 200, 1, NOW(), NOW()),
(2, 'A-01-02', 'A', '01', '02', 200, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE location_code = location_code;

-- 既存商品のデフォルト値設定
UPDATE t_goods 
SET 
  min_stock_level = COALESCE(min_stock_level, 10),
  max_stock_level = COALESCE(max_stock_level, 200),
  reorder_point = COALESCE(reorder_point, 20),
  lead_time_days = COALESCE(lead_time_days, 7),
  is_lot_managed = COALESCE(is_lot_managed, 0),
  is_serial_managed = COALESCE(is_serial_managed, 0)
WHERE delete_flg = 0;

-- ===================================
-- 完了メッセージ
-- ===================================
SELECT '在庫管理システム テーブル作成完了' AS message;
