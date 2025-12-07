-- ===================================
-- データベーススキーマ参照用SQL
-- 作成日: 2025-12-07
-- 説明: AI・開発者がDB構造を理解するための参照ファイル
-- ===================================

-- このファイルは実行用ではなく、参照用です
-- 実際のテーブル作成は各マイグレーションファイルを使用してください

-- ===================================
-- 1. ユーザー管理
-- ===================================

-- ユーザーテーブル
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'ユーザー名',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'メールアドレス',
    email_verified_at TIMESTAMP NULL COMMENT 'メール確認日時',
    password VARCHAR(255) NOT NULL COMMENT 'パスワード',
    remember_token VARCHAR(100) NULL COMMENT 'ログイン維持トークン',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ユーザー';

-- パスワードリセット
CREATE TABLE IF NOT EXISTS password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='パスワードリセット';

-- ===================================
-- 2. カテゴリマスタ
-- ===================================

-- カテゴリマスタ（階層構造）
CREATE TABLE IF NOT EXISTS m_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category_code VARCHAR(20) NOT NULL UNIQUE COMMENT 'カテゴリコード（例: A01, B02）',
    category_name VARCHAR(100) NOT NULL COMMENT 'カテゴリ名',
    parent_id BIGINT UNSIGNED NULL COMMENT '親カテゴリID',
    level INT NOT NULL DEFAULT 1 COMMENT '階層レベル（1:大, 2:中, 3:小）',
    display_order INT NOT NULL DEFAULT 0 COMMENT '表示順',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
    description TEXT NULL COMMENT 'カテゴリ説明',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_category_code (category_code),
    INDEX idx_parent_id (parent_id),
    INDEX idx_level (level),
    INDEX idx_is_active (is_active),
    INDEX idx_display_order (display_order),
    FOREIGN KEY (parent_id) REFERENCES m_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='カテゴリマスタ';

-- 商品番号シーケンス管理
CREATE TABLE IF NOT EXISTS t_goods_sequence (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category_code VARCHAR(20) NOT NULL UNIQUE COMMENT 'カテゴリコード',
    last_number INT NOT NULL DEFAULT 0 COMMENT '最終採番',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_category_code (category_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品番号採番管理';

-- ===================================
-- 3. 商品管理
-- ===================================

-- 商品マスタ
CREATE TABLE IF NOT EXISTS t_goods (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    un_id CHAR(36) NOT NULL UNIQUE COMMENT 'UUID（商品一意識別子）',
    goods_number VARCHAR(255) NOT NULL UNIQUE COMMENT '商品番号（例: A01_000001）',
    goods_name VARCHAR(255) NOT NULL COMMENT '商品名',
    category_id BIGINT UNSIGNED NULL COMMENT 'カテゴリID',
    image_path VARCHAR(500) NULL COMMENT '商品画像パス',
    goods_price INT NOT NULL COMMENT '商品価格',
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00 COMMENT '税率(%)',
    goods_stock INT NOT NULL DEFAULT 0 COMMENT '在庫数',
    min_stock_level INT NULL DEFAULT 10 COMMENT '最小在庫レベル',
    max_stock_level INT NULL DEFAULT 100 COMMENT '最大在庫レベル',
    reorder_point INT NULL DEFAULT 20 COMMENT '発注点',
    lead_time_days INT NULL DEFAULT 7 COMMENT 'リードタイム(日)',
    is_lot_managed TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'ロット管理フラグ',
    is_serial_managed TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'シリアル管理フラグ',
    expiry_alert_days INT NULL COMMENT '有効期限アラート日数',
    intro_txt TEXT NULL COMMENT '紹介文',
    goods_detail TEXT NULL COMMENT '商品詳細',
    disp_flg TINYINT(1) NOT NULL DEFAULT 1 COMMENT '表示フラグ（0:非表示, 1:表示）',
    sales_start_at DATETIME NULL COMMENT '販売開始日時',
    sales_end_at DATETIME NULL COMMENT '販売終了日時',
    delete_flg TINYINT(1) NOT NULL DEFAULT 0 COMMENT '削除フラグ',
    ins_date DATETIME NOT NULL COMMENT '登録日時',
    up_date DATETIME NOT NULL COMMENT '更新日時',
    INDEX idx_un_id (un_id),
    INDEX idx_goods_number (goods_number),
    INDEX idx_goods_name (goods_name),
    INDEX idx_category_id (category_id),
    INDEX idx_disp_flg (disp_flg),
    INDEX idx_delete_flg (delete_flg),
    INDEX idx_ins_date (ins_date),
    INDEX idx_up_date (up_date),
    FOREIGN KEY (category_id) REFERENCES m_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品マスタ';

-- ===================================
-- 4. 倉庫・ロケーション管理
-- ===================================

-- 倉庫マスタ
CREATE TABLE IF NOT EXISTS m_warehouses (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    warehouse_code VARCHAR(20) NOT NULL UNIQUE COMMENT '倉庫コード',
    warehouse_name VARCHAR(100) NOT NULL COMMENT '倉庫名',
    postal_code VARCHAR(10) NULL COMMENT '郵便番号',
    prefecture_id INT NULL COMMENT '都道府県ID',
    city VARCHAR(100) NULL COMMENT '市区町村',
    address_line VARCHAR(255) NULL COMMENT '住所',
    manager_name VARCHAR(100) NULL COMMENT '責任者名',
    phone VARCHAR(20) NULL COMMENT '電話番号',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_warehouse_code (warehouse_code),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='倉庫マスタ';

-- ロケーションマスタ
CREATE TABLE IF NOT EXISTS m_locations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '倉庫ID',
    location_code VARCHAR(50) NOT NULL COMMENT 'ロケーションコード',
    location_name VARCHAR(100) NULL COMMENT 'ロケーション名',
    aisle VARCHAR(10) NULL COMMENT '通路',
    rack VARCHAR(10) NULL COMMENT '棚',
    shelf VARCHAR(10) NULL COMMENT '段',
    location_type ENUM('normal', 'inspection', 'defective', 'shipping') DEFAULT 'normal' COMMENT 'ロケーション種別',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY unique_warehouse_location (warehouse_id, location_code),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_location_code (location_code),
    INDEX idx_is_active (is_active),
    FOREIGN KEY (warehouse_id) REFERENCES m_warehouses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ロケーションマスタ';

-- ===================================
-- 5. 在庫管理
-- ===================================

-- 在庫テーブル
CREATE TABLE IF NOT EXISTS t_inventories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    goods_id BIGINT UNSIGNED NOT NULL COMMENT '商品ID',
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '倉庫ID',
    location_id BIGINT UNSIGNED NOT NULL COMMENT 'ロケーションID',
    lot_number VARCHAR(50) NULL COMMENT 'ロット番号',
    serial_number VARCHAR(50) NULL COMMENT 'シリアル番号',
    quantity INT NOT NULL DEFAULT 0 COMMENT '在庫数',
    reserved_quantity INT NOT NULL DEFAULT 0 COMMENT '引当数',
    available_quantity INT NOT NULL DEFAULT 0 COMMENT '利用可能数',
    unit_cost DECIMAL(10,2) NULL COMMENT '単価',
    expiry_date DATE NULL COMMENT '有効期限',
    received_date DATE NULL COMMENT '入庫日',
    last_counted_at DATETIME NULL COMMENT '最終棚卸日時',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY unique_inventory (goods_id, warehouse_id, location_id, lot_number, serial_number),
    INDEX idx_goods_id (goods_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_location_id (location_id),
    INDEX idx_lot_number (lot_number),
    INDEX idx_serial_number (serial_number),
    INDEX idx_expiry_date (expiry_date),
    FOREIGN KEY (goods_id) REFERENCES t_goods(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES m_warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES m_locations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='在庫テーブル';

-- ロットマスタ
CREATE TABLE IF NOT EXISTS t_lots (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    goods_id BIGINT UNSIGNED NOT NULL COMMENT '商品ID',
    lot_number VARCHAR(50) NOT NULL COMMENT 'ロット番号',
    production_date DATE NULL COMMENT '製造日',
    expiry_date DATE NULL COMMENT '有効期限',
    supplier_name VARCHAR(100) NULL COMMENT '仕入先名',
    inspection_status ENUM('pending', 'passed', 'failed') DEFAULT 'pending' COMMENT '検品状態',
    notes TEXT NULL COMMENT '備考',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY unique_goods_lot (goods_id, lot_number),
    INDEX idx_goods_id (goods_id),
    INDEX idx_lot_number (lot_number),
    INDEX idx_expiry_date (expiry_date),
    FOREIGN KEY (goods_id) REFERENCES t_goods(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ロットマスタ';

-- 入出庫履歴
CREATE TABLE IF NOT EXISTS t_stock_movements (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    goods_id BIGINT UNSIGNED NOT NULL COMMENT '商品ID',
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '倉庫ID',
    location_id BIGINT UNSIGNED NULL COMMENT 'ロケーションID',
    lot_number VARCHAR(50) NULL COMMENT 'ロット番号',
    serial_number VARCHAR(50) NULL COMMENT 'シリアル番号',
    movement_type ENUM('in', 'out', 'adjustment', 'transfer', 'return') NOT NULL COMMENT '入出庫区分',
    quantity INT NOT NULL COMMENT '数量',
    unit_cost DECIMAL(10,2) NULL COMMENT '単価',
    reference_number VARCHAR(100) NULL COMMENT '参照番号（発注番号等）',
    notes TEXT NULL COMMENT '備考',
    user_id BIGINT UNSIGNED NULL COMMENT '担当者ID',
    movement_date DATETIME NOT NULL COMMENT '入出庫日時',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_goods_id (goods_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_location_id (location_id),
    INDEX idx_movement_type (movement_type),
    INDEX idx_movement_date (movement_date),
    INDEX idx_reference_number (reference_number),
    FOREIGN KEY (goods_id) REFERENCES t_goods(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES m_warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES m_locations(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='入出庫履歴';

-- 在庫アラート
CREATE TABLE IF NOT EXISTS t_stock_alerts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    goods_id BIGINT UNSIGNED NOT NULL COMMENT '商品ID',
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '倉庫ID',
    alert_type ENUM('low_stock', 'out_of_stock', 'expiry_soon', 'overstock') NOT NULL COMMENT 'アラート種別',
    alert_level ENUM('info', 'warning', 'critical') NOT NULL DEFAULT 'warning' COMMENT 'アラートレベル',
    current_quantity INT NOT NULL COMMENT '現在数量',
    threshold_quantity INT NULL COMMENT '閾値',
    expiry_date DATE NULL COMMENT '有効期限',
    message TEXT NULL COMMENT 'メッセージ',
    is_resolved TINYINT(1) NOT NULL DEFAULT 0 COMMENT '解決済みフラグ',
    resolved_at DATETIME NULL COMMENT '解決日時',
    resolved_by BIGINT UNSIGNED NULL COMMENT '解決者ID',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_goods_id (goods_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_alert_type (alert_type),
    INDEX idx_alert_level (alert_level),
    INDEX idx_is_resolved (is_resolved),
    FOREIGN KEY (goods_id) REFERENCES t_goods(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES m_warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='在庫アラート';

-- ===================================
-- 主要なリレーション構造
-- ===================================

/*
【カテゴリ階層】
m_categories (親)
  ├─ m_categories (子: 中カテゴリ)
  │   └─ m_categories (孫: 小カテゴリ)
  └─ t_goods (商品)

【商品番号採番】
m_categories (カテゴリコード) → t_goods_sequence (最終採番) → t_goods (商品番号)
例: カテゴリA01 → last_number: 5 → 商品番号: A01_000005

【商品・在庫管理】
t_goods (商品)
  ├─ t_inventories (在庫: 倉庫・ロケーション別)
  ├─ t_stock_movements (入出庫履歴)
  ├─ t_stock_alerts (在庫アラート)
  └─ t_lots (ロット情報)

【倉庫・ロケーション】
m_warehouses (倉庫)
  ├─ m_locations (ロケーション)
  ├─ t_inventories (在庫)
  ├─ t_stock_movements (入出庫履歴)
  └─ t_stock_alerts (アラート)

【商品画像パス】
- 保存先: public/images/products/{商品番号}/main.{拡張子}
- DB保存: public/images/products/A01_000001/main.png
- 表示: {{ asset('public/images/products/A01_000001/main.png') }}
*/

-- ===================================
-- 主要なインデックス戦略
-- ===================================

/*
【検索頻度の高いカラム】
- t_goods: goods_number, goods_name, category_id, disp_flg, delete_flg
- t_inventories: goods_id, warehouse_id, location_id, lot_number
- t_stock_movements: goods_id, movement_date, movement_type
- m_categories: category_code, parent_id, level

【複合インデックスの候補】
- t_goods: (category_id, disp_flg, delete_flg)
- t_inventories: (goods_id, warehouse_id, location_id)
- t_stock_movements: (goods_id, movement_date)
*/

-- ===================================
-- サンプルクエリ
-- ===================================

-- 1. カテゴリ別商品一覧
/*
SELECT 
    g.goods_number,
    g.goods_name,
    c.category_name,
    g.goods_price,
    g.goods_stock
FROM t_goods g
LEFT JOIN m_categories c ON g.category_id = c.id
WHERE g.delete_flg = 0 AND g.disp_flg = 1
ORDER BY c.display_order, g.goods_number;
*/

-- 2. 倉庫別在庫一覧
/*
SELECT 
    g.goods_number,
    g.goods_name,
    w.warehouse_name,
    l.location_code,
    i.quantity,
    i.reserved_quantity,
    i.available_quantity
FROM t_inventories i
JOIN t_goods g ON i.goods_id = g.id
JOIN m_warehouses w ON i.warehouse_id = w.id
JOIN m_locations l ON i.location_id = l.id
WHERE g.delete_flg = 0
ORDER BY w.warehouse_code, l.location_code, g.goods_number;
*/

-- 3. 低在庫アラート
/*
SELECT 
    g.goods_number,
    g.goods_name,
    g.goods_stock,
    g.min_stock_level,
    g.reorder_point
FROM t_goods g
WHERE g.delete_flg = 0
  AND g.goods_stock <= g.min_stock_level
ORDER BY g.goods_stock ASC;
*/

-- 4. 入出庫履歴
/*
SELECT 
    sm.movement_date,
    g.goods_number,
    g.goods_name,
    w.warehouse_name,
    sm.movement_type,
    sm.quantity,
    u.name as user_name
FROM t_stock_movements sm
JOIN t_goods g ON sm.goods_id = g.id
JOIN m_warehouses w ON sm.warehouse_id = w.id
LEFT JOIN users u ON sm.user_id = u.id
WHERE sm.movement_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY sm.movement_date DESC;
*/

-- ===================================
-- 注意事項
-- ===================================

/*
1. このファイルは参照用です。実行しないでください。
2. 実際のマイグレーションは database/migrations/ を使用してください。
3. テストデータは database/sql/ 内の各SQLファイルを使用してください。
4. 外部キー制約は開発環境では無効化することも検討してください。
5. 本番環境では適切なインデックスとパーティショニングを検討してください。
*/
