-- ===================================
-- カテゴリマスタテーブル作成SQL
-- 作成日: 2025-12-06
-- 説明: 商品カテゴリの階層管理
-- ===================================

-- カテゴリマスタテーブル
CREATE TABLE IF NOT EXISTS m_categories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_code VARCHAR(20) NOT NULL UNIQUE COMMENT 'カテゴリコード',
  category_name VARCHAR(100) NOT NULL COMMENT 'カテゴリ名',
  parent_id BIGINT UNSIGNED NULL COMMENT '親カテゴリID',
  display_order INT NOT NULL DEFAULT 0 COMMENT '表示順',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  description TEXT NULL COMMENT 'カテゴリ説明',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_category_code (category_code),
  INDEX idx_parent_id (parent_id),
  INDEX idx_is_active (is_active),
  INDEX idx_display_order (display_order),
  FOREIGN KEY (parent_id) REFERENCES m_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='カテゴリマスタ';

-- 初期データ挿入
INSERT INTO m_categories (category_code, category_name, parent_id, display_order, is_active, description, created_at, updated_at) 
VALUES 
-- 大カテゴリ
('A', '食品', NULL, 1, 1, '食品全般', NOW(), NOW()),
('B', '飲料', NULL, 2, 1, '飲料全般', NOW(), NOW()),
('C', '雑貨', NULL, 3, 1, '生活雑貨', NOW(), NOW()),
('D', '電化製品', NULL, 4, 1, '家電製品', NOW(), NOW()),
('E', '衣類', NULL, 5, 1, '衣類・アパレル', NOW(), NOW()),

-- 中カテゴリ（食品）
('A01', '野菜', 1, 1, 1, '野菜類', NOW(), NOW()),
('A02', '果物', 1, 2, 1, '果物類', NOW(), NOW()),
('A03', '肉類', 1, 3, 1, '肉類', NOW(), NOW()),
('A04', '魚類', 1, 4, 1, '魚類', NOW(), NOW()),
('A05', '加工食品', 1, 5, 1, '加工食品', NOW(), NOW()),

-- 中カテゴリ（飲料）
('B01', 'お茶', 2, 1, 1, 'お茶類', NOW(), NOW()),
('B02', 'コーヒー', 2, 2, 1, 'コーヒー類', NOW(), NOW()),
('B03', 'ジュース', 2, 3, 1, 'ジュース類', NOW(), NOW()),
('B04', 'アルコール', 2, 4, 1, 'アルコール飲料', NOW(), NOW()),

-- 中カテゴリ（雑貨）
('C01', 'キッチン用品', 3, 1, 1, 'キッチン雑貨', NOW(), NOW()),
('C02', 'バス用品', 3, 2, 1, 'バスルーム用品', NOW(), NOW()),
('C03', '文房具', 3, 3, 1, '文房具・オフィス用品', NOW(), NOW())

ON DUPLICATE KEY UPDATE category_name = category_name;

-- 商品番号採番用シーケンステーブル
CREATE TABLE IF NOT EXISTS t_goods_sequence (
  category_code VARCHAR(20) NOT NULL PRIMARY KEY COMMENT 'カテゴリコード',
  last_number INT NOT NULL DEFAULT 0 COMMENT '最終採番番号',
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_category (category_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品番号採番管理';

-- カテゴリごとの初期シーケンス
INSERT INTO t_goods_sequence (category_code, last_number, updated_at)
SELECT category_code, 0, NOW()
FROM m_categories
WHERE parent_id IS NOT NULL
ON DUPLICATE KEY UPDATE last_number = last_number;
