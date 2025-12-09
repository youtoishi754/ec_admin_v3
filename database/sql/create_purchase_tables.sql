-- ========================================
-- 発注管理テーブル作成SQL
-- ========================================

-- 仕入先マスタ
CREATE TABLE IF NOT EXISTS `m_suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(20) NOT NULL COMMENT '仕入先コード',
  `supplier_name` varchar(100) NOT NULL COMMENT '仕入先名',
  `contact_person` varchar(50) DEFAULT NULL COMMENT '担当者名',
  `contact_email` varchar(100) DEFAULT NULL COMMENT 'メールアドレス',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '電話番号',
  `fax` varchar(20) DEFAULT NULL COMMENT 'FAX',
  `postal_code` varchar(10) DEFAULT NULL COMMENT '郵便番号',
  `address` varchar(255) DEFAULT NULL COMMENT '住所',
  `payment_terms` varchar(100) DEFAULT NULL COMMENT '支払条件',
  `lead_time_days` int(11) DEFAULT NULL COMMENT 'リードタイム（日数）',
  `minimum_order_amount` decimal(12,2) DEFAULT NULL COMMENT '最低発注金額',
  `notes` text DEFAULT NULL COMMENT '備考',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `m_suppliers_supplier_code_unique` (`supplier_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='仕入先マスタ';

-- 発注書テーブル
CREATE TABLE IF NOT EXISTS `t_purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL COMMENT '発注番号',
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '仕入先ID',
  `order_date` date NOT NULL COMMENT '発注日',
  `expected_delivery_date` date DEFAULT NULL COMMENT '納期予定日',
  `ordered_date` date DEFAULT NULL COMMENT '発注確定日',
  `received_date` date DEFAULT NULL COMMENT '入荷日',
  `status` enum('draft','pending','ordered','received','cancelled') NOT NULL DEFAULT 'draft' COMMENT 'ステータス',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '合計金額',
  `notes` text DEFAULT NULL COMMENT '備考',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `t_purchase_orders_order_number_unique` (`order_number`),
  KEY `t_purchase_orders_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `t_purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `m_suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='発注書テーブル';

-- 発注書明細テーブル
CREATE TABLE IF NOT EXISTS `t_purchase_order_details` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL COMMENT '発注書ID',
  `goods_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品ID',
  `quantity` int(11) NOT NULL COMMENT '発注数量',
  `unit_price` decimal(10,2) NOT NULL COMMENT '単価',
  `subtotal` decimal(12,2) NOT NULL COMMENT '小計',
  `received_quantity` int(11) DEFAULT 0 COMMENT '入荷済数量',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `t_purchase_order_details_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `t_purchase_order_details_goods_id_foreign` (`goods_id`),
  CONSTRAINT `t_purchase_order_details_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `t_purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `t_purchase_order_details_goods_id_foreign` FOREIGN KEY (`goods_id`) REFERENCES `t_goods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='発注書明細テーブル';

-- 発注履歴テーブル
CREATE TABLE IF NOT EXISTS `t_purchase_order_history` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL COMMENT '発注書ID',
  `action` varchar(50) NOT NULL COMMENT 'アクション',
  `old_status` varchar(20) DEFAULT NULL COMMENT '変更前ステータス',
  `new_status` varchar(20) DEFAULT NULL COMMENT '変更後ステータス',
  `notes` text DEFAULT NULL COMMENT '備考',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT '作成者ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `t_purchase_order_history_purchase_order_id_foreign` (`purchase_order_id`),
  CONSTRAINT `t_purchase_order_history_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `t_purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='発注履歴テーブル';

-- ========================================
-- サンプルデータ投入
-- ========================================

-- 仕入先サンプルデータ
INSERT INTO `m_suppliers` (`supplier_code`, `supplier_name`, `contact_person`, `contact_email`, `contact_phone`, `postal_code`, `address`, `payment_terms`, `lead_time_days`, `minimum_order_amount`, `is_active`, `created_at`, `updated_at`) VALUES
('SUP001', '株式会社サンプル商事', '山田太郎', 'yamada@sample.co.jp', '03-1234-5678', '100-0001', '東京都千代田区丸の内1-1-1', '月末締め翌月末払い', 7, 10000, 1, NOW(), NOW()),
('SUP002', '有限会社テスト物産', '佐藤花子', 'sato@test.co.jp', '06-9876-5432', '530-0001', '大阪府大阪市北区梅田2-2-2', '月末締め翌々月10日払い', 5, 5000, 1, NOW(), NOW()),
('SUP003', '合同会社デモ卸売', '鈴木一郎', 'suzuki@demo.co.jp', '052-1111-2222', '460-0001', '愛知県名古屋市中区栄3-3-3', '月末締め翌月15日払い', 10, 20000, 1, NOW(), NOW()),
('SUP004', '株式会社サンプル食品', '田中美咲', 'tanaka@sample-food.co.jp', '092-3333-4444', '810-0001', '福岡県福岡市中央区天神4-4-4', '月末締め翌月末払い', 3, 3000, 1, NOW(), NOW()),
('SUP005', '有限会社テスト雑貨', '高橋健二', 'takahashi@test-zakka.co.jp', '011-5555-6666', '060-0001', '北海道札幌市中央区大通5-5-5', '月末締め翌々月末払い', 14, 15000, 1, NOW(), NOW());

-- t_goodsテーブルにsupplier_idカラムが無い場合は追加
-- ALTER TABLE `t_goods` ADD COLUMN `supplier_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '仕入先ID' AFTER `category_id`;
-- ALTER TABLE `t_goods` ADD COLUMN `reorder_quantity` int(11) DEFAULT 10 COMMENT '発注数量' AFTER `reorder_point`;

-- 商品にランダムで仕入先を紐づけ（既存データがある場合）
-- UPDATE `t_goods` SET `supplier_id` = FLOOR(1 + RAND() * 5) WHERE `supplier_id` IS NULL;
