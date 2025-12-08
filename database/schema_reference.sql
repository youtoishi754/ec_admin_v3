-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-12-08 01:57:32
-- サーバのバージョン： 10.4.13-MariaDB
-- PHP のバージョン: 7.2.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `laravel`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `m_categories`
--

CREATE TABLE `m_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_code` varchar(20) NOT NULL COMMENT 'カテゴリコード',
  `category_name` varchar(100) NOT NULL COMMENT 'カテゴリ名',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '親カテゴリID',
  `level` tinyint(4) NOT NULL DEFAULT 1 COMMENT '階層レベル(1:大, 2:中, 3:小)',
  `display_order` int(11) NOT NULL DEFAULT 0 COMMENT '表示順',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  `description` text DEFAULT NULL COMMENT 'カテゴリ説明',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='カテゴリマスタ';

--
-- テーブルのデータのダンプ `m_categories`
--

INSERT INTO `m_categories` (`id`, `category_code`, `category_name`, `parent_id`, `level`, `display_order`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 'A', '食品', NULL, 1, 1, 1, '食品全般', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(2, 'B', '飲料', NULL, 1, 2, 1, '飲料全般', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(3, 'C', '雑貨', NULL, 1, 3, 1, '生活雑貨', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(4, 'D', '電化製品', NULL, 1, 4, 1, '家電製品', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(5, 'E', '衣類', NULL, 1, 5, 1, '衣類・アパレル', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(6, 'F', '書籍', NULL, 1, 6, 1, '書籍・雑誌', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(7, 'G', 'スポーツ用品', NULL, 1, 7, 1, 'スポーツ・アウトドア', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(8, 'H', '美容・健康', NULL, 1, 8, 1, '美容・健康用品', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(9, 'A01', '野菜', 1, 2, 1, 1, '新鮮な野菜', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(10, 'A02', '果物', 1, 2, 2, 1, '季節の果物', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(11, 'A03', '肉類', 1, 2, 3, 1, '各種肉類', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(12, 'A04', '魚類', 1, 2, 4, 1, '鮮魚・海産物', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(13, 'A05', '加工食品', 1, 2, 5, 1, '缶詰・レトルトなど', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(14, 'A06', '調味料', 1, 2, 6, 1, '調味料・スパイス', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(15, 'A07', '米・穀物', 1, 2, 7, 1, '米・麺類・パン', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(16, 'A08', '乳製品', 1, 2, 8, 1, '牛乳・チーズ・ヨーグルト', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(17, 'A09', '冷凍食品', 1, 2, 9, 1, '冷凍食品', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(18, 'A10', '菓子類', 1, 2, 10, 1, 'お菓子・スイーツ', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(19, 'B01', 'お茶', 2, 2, 1, 1, '緑茶・紅茶など', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(20, 'B02', 'コーヒー', 2, 2, 2, 1, 'コーヒー豆・インスタント', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(21, 'B03', 'ジュース', 2, 2, 3, 1, 'フルーツジュース', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(22, 'B04', '炭酸飲料', 2, 2, 4, 1, '炭酸飲料', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(23, 'B05', 'アルコール', 2, 2, 5, 1, 'ビール・ワイン・日本酒', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(24, 'B06', 'ミネラルウォーター', 2, 2, 6, 1, '天然水・炭酸水', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(25, 'B07', 'スポーツドリンク', 2, 2, 7, 1, 'スポーツドリンク', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(26, 'C01', 'キッチン用品', 3, 2, 1, 1, 'キッチン雑貨', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(27, 'C02', 'バス用品', 3, 2, 2, 1, 'バスルーム用品', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(28, 'C03', '文房具', 3, 2, 3, 1, '文房具・オフィス用品', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(29, 'C04', 'インテリア', 3, 2, 4, 1, 'インテリア雑貨', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(30, 'C05', '掃除用品', 3, 2, 5, 1, '清掃・洗剤', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(31, 'C06', '収納用品', 3, 2, 6, 1, '収納・整理', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(32, 'D01', '調理家電', 4, 2, 1, 1, '炊飯器・電子レンジなど', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(33, 'D02', '生活家電', 4, 2, 2, 1, '掃除機・洗濯機など', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(34, 'D03', 'AV機器', 4, 2, 3, 1, 'テレビ・オーディオ', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(35, 'D04', 'パソコン', 4, 2, 4, 1, 'PC・周辺機器', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(36, 'D05', 'スマホ・タブレット', 4, 2, 5, 1, 'モバイル端末', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(37, 'E01', 'メンズ', 5, 2, 1, 1, '紳士服', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(38, 'E02', 'レディース', 5, 2, 2, 1, '婦人服', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(39, 'E03', 'キッズ', 5, 2, 3, 1, '子供服', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(40, 'E04', '下着・靴下', 5, 2, 4, 1, 'インナー・靴下', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(41, 'E05', 'アクセサリー', 5, 2, 5, 1, 'ファッション小物', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(42, 'A0101', '葉物野菜', 9, 3, 1, 1, 'レタス・キャベツなど', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(43, 'A0102', '根菜類', 9, 3, 2, 1, 'にんじん・大根など', '2025-12-06 12:50:44', '2025-12-06 12:50:44'),
(44, 'A0103', 'トマト・きゅうり', 9, 3, 3, 1, '果菜類', '2025-12-06 12:50:44', '2025-12-06 12:50:44');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_locations`
--

CREATE TABLE `m_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL COMMENT '倉庫ID',
  `location_code` varchar(30) NOT NULL COMMENT 'ロケーションコード',
  `aisle` varchar(10) DEFAULT NULL COMMENT '通路番号',
  `rack` varchar(10) DEFAULT NULL COMMENT '棚番号',
  `shelf` varchar(10) DEFAULT NULL COMMENT '段番号',
  `capacity` int(11) DEFAULT NULL COMMENT '収容可能数',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ロケーションマスタ';

--
-- テーブルのデータのダンプ `m_locations`
--

INSERT INTO `m_locations` (`id`, `warehouse_id`, `location_code`, `aisle`, `rack`, `shelf`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'A-01-01', 'A', '01', '01', 100, 1, '2025-12-05 15:06:36', '2025-12-05 15:06:36'),
(2, 1, 'A-01-02', 'A', '01', '02', 100, 1, '2025-12-05 15:06:36', '2025-12-05 15:06:36'),
(3, 1, 'A-02-01', 'A', '02', '01', 100, 1, '2025-12-05 15:06:36', '2025-12-05 15:06:36'),
(4, 1, 'B-01-01', 'B', '01', '01', 150, 1, '2025-12-05 15:06:36', '2025-12-05 15:06:36'),
(5, 2, 'A-01-01', 'A', '01', '01', 200, 1, '2025-12-05 15:06:36', '2025-12-05 15:06:36');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_lots`
--

CREATE TABLE `m_lots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lot_number` varchar(50) NOT NULL COMMENT 'ロット番号',
  `goods_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品ID',
  `manufacturing_date` date DEFAULT NULL COMMENT '製造日',
  `expiry_date` date DEFAULT NULL COMMENT '有効期限',
  `received_date` date NOT NULL COMMENT '入荷日',
  `quantity_received` int(11) NOT NULL COMMENT '入荷数量',
  `quantity_remaining` int(11) NOT NULL COMMENT '残数量',
  `supplier_name` varchar(100) DEFAULT NULL COMMENT '仕入先名',
  `notes` text DEFAULT NULL COMMENT '備考',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ロットマスタ';

-- --------------------------------------------------------

--
-- テーブルの構造 `m_order_statuses`
--

CREATE TABLE `m_order_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT 'ステータス名',
  `rank` int(11) NOT NULL COMMENT '表示順'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `m_order_statuses`
--

INSERT INTO `m_order_statuses` (`id`, `name`, `rank`) VALUES
(1, '注文確定', 1),
(2, '入金確認済', 3),
(3, '発送準備中', 4),
(4, '発送済み', 5),
(5, '配達完了', 6),
(6, 'キャンセル', 99),
(7, '返品受付中', 100),
(8, '返品完了', 101),
(9, '決済未完了', 0),
(10, '入金未完了', 2);

-- --------------------------------------------------------

--
-- テーブルの構造 `m_payment_methods`
--

CREATE TABLE `m_payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '支払方法名',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `m_payment_methods`
--

INSERT INTO `m_payment_methods` (`id`, `name`, `is_active`) VALUES
(1, 'クレジットカード', 1),
(2, '代金引換', 1),
(3, '銀行振込', 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `m_prefectures`
--

CREATE TABLE `m_prefectures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(10) NOT NULL COMMENT '都道府県名',
  `code` char(2) NOT NULL COMMENT 'JISコード'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `m_prefectures`
--

INSERT INTO `m_prefectures` (`id`, `name`, `code`) VALUES
(1, '北海道', '01'),
(2, '青森県', '02'),
(3, '岩手県', '03'),
(4, '宮城県', '04'),
(5, '秋田県', '05'),
(6, '山形県', '06'),
(7, '福島県', '07'),
(8, '茨城県', '08'),
(9, '栃木県', '09'),
(10, '群馬県', '10'),
(11, '埼玉県', '11'),
(12, '千葉県', '12'),
(13, '東京都', '13'),
(14, '神奈川県', '14'),
(15, '新潟県', '15'),
(16, '富山県', '16'),
(17, '石川県', '17'),
(18, '福井県', '18'),
(19, '山梨県', '19'),
(20, '長野県', '20'),
(21, '岐阜県', '21'),
(22, '静岡県', '22'),
(23, '愛知県', '23'),
(24, '三重県', '24'),
(25, '滋賀県', '25'),
(26, '京都府', '26'),
(27, '大阪府', '27'),
(28, '兵庫県', '28'),
(29, '奈良県', '29'),
(30, '和歌山県', '30'),
(31, '鳥取県', '31'),
(32, '島根県', '32'),
(33, '岡山県', '33'),
(34, '広島県', '34'),
(35, '山口県', '35'),
(36, '徳島県', '36'),
(37, '香川県', '37'),
(38, '愛媛県', '38'),
(39, '高知県', '39'),
(40, '福岡県', '40'),
(41, '佐賀県', '41'),
(42, '長崎県', '42'),
(43, '熊本県', '43'),
(44, '大分県', '44'),
(45, '宮崎県', '45'),
(46, '鹿児島県', '46'),
(47, '沖縄県', '47');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_warehouses`
--

CREATE TABLE `m_warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_code` varchar(20) NOT NULL COMMENT '倉庫コード',
  `warehouse_name` varchar(100) NOT NULL COMMENT '倉庫名',
  `postal_code` varchar(8) DEFAULT NULL COMMENT '郵便番号',
  `prefecture_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '都道府県ID',
  `city` varchar(100) DEFAULT NULL COMMENT '市区町村',
  `address_line` varchar(255) DEFAULT NULL COMMENT '番地・建物',
  `manager_name` varchar(100) DEFAULT NULL COMMENT '管理者名',
  `phone` varchar(20) DEFAULT NULL COMMENT '電話番号',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='倉庫マスタ';

--
-- テーブルのデータのダンプ `m_warehouses`
--

INSERT INTO `m_warehouses` (`id`, `warehouse_code`, `warehouse_name`, `postal_code`, `prefecture_id`, `city`, `address_line`, `manager_name`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'WH-001', '本社倉庫', '100-0001', 13, '千代田区', '千代田1-1-1', '倉庫管理者', '03-1234-5678', 1, '2025-12-05 15:05:16', '2025-12-05 15:05:16'),
(2, 'WH-002', '第二倉庫', '530-0001', 27, '大阪市北区', '梅田1-1-1', NULL, NULL, 1, '2025-12-05 15:05:16', '2025-12-05 15:05:16');

-- --------------------------------------------------------

--
-- テーブルの構造 `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `stripe_payment_intent_id` varchar(255) DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `error_message` text DEFAULT NULL,
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_data`)),
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `pre_registrations`
--

CREATE TABLE `pre_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `t_favorites`
--

CREATE TABLE `t_favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `goods_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `t_favorites`
--

INSERT INTO `t_favorites` (`id`, `user_id`, `goods_id`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2025-11-28 15:34:18', '2025-11-28 15:34:18'),
(2, 3, 5, '2025-11-28 15:34:19', '2025-11-28 15:34:19');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_goods`
--

CREATE TABLE `t_goods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `un_id` varchar(255) DEFAULT NULL,
  `goods_number` varchar(255) DEFAULT NULL,
  `goods_name` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL COMMENT '商品画像パス',
  `goods_price` int(11) DEFAULT NULL,
  `tax_rate` int(11) NOT NULL DEFAULT 10 COMMENT '税率',
  `goods_stock` int(11) DEFAULT NULL,
  `min_stock_level` int(11) DEFAULT NULL COMMENT '最低在庫数',
  `max_stock_level` int(11) DEFAULT NULL COMMENT '最大在庫数',
  `reorder_point` int(11) DEFAULT NULL COMMENT '発注点',
  `lead_time_days` int(11) DEFAULT NULL COMMENT 'リードタイム日数',
  `is_lot_managed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ロット管理フラグ',
  `is_serial_managed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'シリアル管理フラグ',
  `expiry_alert_days` int(11) DEFAULT NULL COMMENT '有効期限アラート日数',
  `category_id` int(11) DEFAULT NULL COMMENT 'カテゴリID',
  `goods_detail` text DEFAULT NULL,
  `intro_txt` text DEFAULT NULL,
  `disp_flg` tinyint(4) NOT NULL DEFAULT 1,
  `delete_flg` tinyint(4) NOT NULL DEFAULT 0,
  `sales_start_at` datetime DEFAULT NULL COMMENT '販売開始日時',
  `sales_end_at` datetime DEFAULT NULL COMMENT '販売終了日時',
  `ins_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `up_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `t_goods`
--

INSERT INTO `t_goods` (`id`, `un_id`, `goods_number`, `goods_name`, `image_path`, `goods_price`, `tax_rate`, `goods_stock`, `min_stock_level`, `max_stock_level`, `reorder_point`, `lead_time_days`, `is_lot_managed`, `is_serial_managed`, `expiry_alert_days`, `category_id`, `goods_detail`, `intro_txt`, `disp_flg`, `delete_flg`, `sales_start_at`, `sales_end_at`, `ins_date`, `up_date`) VALUES
(52, '3e853f49-d2b9-11f0-94ea-fc3497b62067', 'A01_000001', '有機栽培レタス', 'public/images/products/A01_000001/main.png', 298, 8, 50, 10, 100, 20, 3, 1, 0, 3, 9, '朝採れの有機栽培レタスです。シャキシャキの食感をお楽しみください。', '農薬不使用の新鮮レタス', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-07 11:09:32'),
(53, '3e855d32-d2b9-11f0-94ea-fc3497b62067', 'A01_000002', '新鮮キャベツ', 'public/images/products/A01_000002/main.png', 198, 8, 80, 15, 150, 30, 3, 1, 0, 3, 9, '千葉県産の新鮮なキャベツです。', '甘みたっぷりのキャベツ', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-07 11:10:53'),
(54, '3e855f7d-d2b9-11f0-94ea-fc3497b62067', 'A01_000003', '国産にんじん 3本入', 'public/images/products/A01_000003/main.png', 178, 8, 120, 20, 200, 40, 5, 1, 0, 7, 9, '北海道産のにんじん3本セットです。', 'βカロテン豊富なにんじん', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-07 12:35:52'),
(55, '3e8560d6-d2b9-11f0-94ea-fc3497b62067', 'A01_000004', '大根 1本', 'public/images/products/A01_000004/main.png', 168, 8, 60, 10, 100, 20, 3, 1, 0, 5, 9, '煮物にも漬物にも最適です。', 'みずみずしい大根', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-07 12:36:18'),
(56, '3e85621f-d2b9-11f0-94ea-fc3497b62067', 'A02_000001', '青森県産りんご ふじ 5個入', 'public/images/products/A02_000001/main.png', 980, 8, 45, 10, 80, 20, 5, 1, 0, 7, 10, '青森県産のふじりんごです。蜜がたっぷり入っています。', '蜜入りの甘いりんご', 1, 0, '2025-01-01 00:00:00', '2025-03-31 23:59:59', '2025-12-06 15:23:12', '2025-12-07 12:36:35'),
(57, '3e856366-d2b9-11f0-94ea-fc3497b62067', 'A02_000002', 'バナナ フィリピン産 1房', 'public/images/products/A02_000002/main.png', 298, 8, 100, 20, 150, 40, 3, 1, 0, 3, 10, '朝食やおやつに最適なバナナです。', '甘くて栄養豊富', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-06 15:23:12'),
(58, '3e85648f-d2b9-11f0-94ea-fc3497b62067', 'A02_000003', 'いちご 1パック', '/images/products/A02_000003/main.jpg', 598, 8, 30, 5, 50, 10, 2, 1, 0, 2, 10, '福岡県産のあまおうです。', '甘酸っぱい完熟いちご', 1, 0, '2025-01-01 00:00:00', '2025-05-31 23:59:59', '2025-12-06 15:23:12', '2025-12-06 15:23:12'),
(59, '3e8565cc-d2b9-11f0-94ea-fc3497b62067', 'A03_000001', '国産豚バラスライス 300g', 'public/images/products/A03_000001/main.png', 698, 8, 40, 10, 80, 20, 2, 1, 0, 3, 11, '国産豚肉のバラスライスです。しゃぶしゃぶや炒め物に。', '柔らかくジューシー', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-06 15:23:12'),
(60, '3e8566e6-d2b9-11f0-94ea-fc3497b62067', 'A03_000002', '鶏もも肉 500g', 'public/images/products/A03_000002/main.png', 598, 8, 55, 15, 100, 30, 2, 1, 0, 3, 11, '唐揚げや照り焼きに最適です。', 'ジューシーな鶏もも肉', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-06 15:23:12'),
(61, '3e85682c-d2b9-11f0-94ea-fc3497b62067', 'A03_000003', '和牛サーロインステーキ 200g', '/images/products/A03_000003/main.jpg', 2980, 8, 15, 3, 30, 5, 3, 1, 0, 5, 11, '特別な日のディナーに。', '最高級A5ランク和牛', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-06 15:23:12'),
(62, '3e856da8-d2b9-11f0-94ea-fc3497b62067', 'A05_000001', 'レトルトカレー 中辛 10個セット', 'public/images/products/A05_000001/main.png', 1980, 8, 80, 20, 200, 40, 7, 1, 0, 30, 13, '温めるだけで本格的なカレーが楽しめます。', '本格派カレー', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-06 15:23:12'),
(63, '3e856ec3-d2b9-11f0-94ea-fc3497b62067', 'A05_000002', 'ツナ缶 70g×4缶', 'public/images/products/A05_000002/main.png', 498, 8, 150, 30, 300, 60, 10, 1, 0, 90, 13, '良質なマグロを使用したツナ缶です。', '保存食に最適', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-06 15:23:12'),
(64, '3e856fe8-d2b9-11f0-94ea-fc3497b62067', 'A08_000001', '牛乳 1L', 'public/images/products/A08_000001/main.png', 218, 8, 200, 50, 500, 100, 2, 1, 0, 3, 16, 'カルシウムたっぷりの牛乳です。', '北海道産生乳100%', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-06 15:23:12'),
(65, '3e857114-d2b9-11f0-94ea-fc3497b62067', 'A08_000002', 'プレーンヨーグルト 400g', 'public/images/products/A08_000002/main.png', 168, 8, 180, 40, 400, 80, 2, 1, 0, 5, 16, '腸内環境を整えるヨーグルトです。', '生きた乳酸菌', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:12', '2025-12-06 15:23:12'),
(66, '3e857211-d2b9-11f0-94ea-fc3497b62067', 'A10_000001', 'ポテトチップス うすしお味', 'public/images/products/A10_000001/main.png', 128, 10, 300, 50, 500, 100, 5, 1, 0, 60, 18, '定番のうすしお味です。', 'サクサク食感', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(67, '3e85730c-d2b9-11f0-94ea-fc3497b62067', 'A10_000002', 'チョコレート 板チョコ', 'public/images/products/A10_000002/main.png', 198, 10, 250, 40, 400, 80, 7, 1, 0, 90, 18, 'ビターな大人の味わい。', 'カカオ72%', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(68, '3e857403-d2b9-11f0-94ea-fc3497b62067', 'B01_000001', '緑茶ペットボトル 500ml×24本', 'public/images/products/B01_000001/main.png', 2380, 8, 100, 20, 200, 40, 7, 1, 0, 180, 19, '静岡県産茶葉使用の緑茶です。', 'すっきり爽やか', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(69, '3e857502-d2b9-11f0-94ea-fc3497b62067', 'B01_000002', '麦茶ティーバッグ 52袋入', 'public/images/products/B01_000002/main.png', 398, 8, 150, 30, 300, 60, 10, 1, 0, 365, 19, '家族みんなで飲める麦茶です。', 'ノンカフェイン', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(70, '3e857602-d2b9-11f0-94ea-fc3497b62067', 'B02_000001', 'インスタントコーヒー 瓶 100g', 'public/images/products/B02_000001/main.png', 698, 8, 80, 15, 150, 30, 7, 1, 0, 180, 20, '手軽に本格コーヒーが楽しめます。', '深煎り豊かな香り', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(71, '3e857646-d2b9-11f0-94ea-fc3497b62067', 'B02_000002', 'ドリップコーヒー 10袋入', 'public/images/products/B02_000002/main.png', 498, 8, 120, 20, 200, 40, 7, 1, 0, 270, 20, 'オフィスやご家庭で手軽にドリップコーヒー。', '1杯ずつドリップ', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(72, '3e857671-d2b9-11f0-94ea-fc3497b62067', 'B03_000001', 'オレンジジュース 1L', 'public/images/products/B03_000001/main.png', 298, 8, 90, 20, 200, 40, 5, 1, 0, 30, 21, 'ビタミンC豊富なオレンジジュース。', '果汁100%', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(73, '3e8576a0-d2b9-11f0-94ea-fc3497b62067', 'B03_000002', 'りんごジュース 1L', 'public/images/products/B03_000002/main.png', 298, 8, 85, 20, 200, 40, 5, 1, 0, 30, 21, 'すっきりとした甘さのりんごジュース。', '青森県産りんご使用', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(74, '3e8576cb-d2b9-11f0-94ea-fc3497b62067', 'B06_000001', '天然水 2L×6本', 'public/images/products/B06_000001/main.png', 798, 8, 200, 50, 500, 100, 7, 1, 0, 365, 24, 'まろやかな軟水です。', '富士山の天然水', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(75, '3e8576f8-d2b9-11f0-94ea-fc3497b62067', 'B06_000002', '炭酸水 500ml×24本', 'public/images/products/B06_000002/main.png', 1680, 8, 150, 30, 300, 60, 7, 1, 0, 365, 24, '割り材にも最適な炭酸水。', '強炭酸', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(76, '3e857725-d2b9-11f0-94ea-fc3497b62067', 'C01_000001', 'フライパン 26cm', 'public/images/products/C01_000001/main.png', 2980, 10, 40, 8, 80, 15, 10, 0, 0, NULL, 26, '焦げ付きにくいフライパンです。', 'テフロン加工', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(77, '3e857753-d2b9-11f0-94ea-fc3497b62067', 'C01_000002', '包丁 三徳包丁', 'public/images/products/C01_000002/main.png', 3980, 10, 30, 5, 50, 10, 14, 0, 1, NULL, 26, '切れ味抜群の包丁です。', 'ステンレス刃', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(78, '3e857781-d2b9-11f0-94ea-fc3497b62067', 'C01_000003', '鍋 両手鍋 20cm', 'public/images/products/C01_000003/main.png', 4980, 10, 25, 5, 50, 10, 14, 0, 0, NULL, 26, 'ガス・IHどちらでも使えます。', 'IH対応', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(79, '3e8577ba-d2b9-11f0-94ea-fc3497b62067', 'C03_000001', 'ボールペン 黒 10本セット', 'public/images/products/C03_000001/main.png', 498, 10, 200, 40, 400, 80, 7, 1, 0, NULL, 28, 'オフィスや学校に最適。', 'なめらかな書き心地', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(80, '3e8577e6-d2b9-11f0-94ea-fc3497b62067', 'C03_000002', 'ノート A4 5冊セット', 'public/images/products/C03_000002/main.png', 598, 10, 150, 30, 300, 60, 7, 1, 0, NULL, 28, '使いやすい方眼ノートです。', '方眼罫', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(81, '3e857828-d2b9-11f0-94ea-fc3497b62067', 'C05_000001', '食器用洗剤 本体 300ml', 'public/images/products/C05_000001/main.png', 198, 10, 250, 50, 500, 100, 7, 1, 0, NULL, 30, '手肌にやさしい洗剤です。', '油汚れもスッキリ', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(82, '3e857854-d2b9-11f0-94ea-fc3497b62067', 'C05_000002', 'トイレ用洗剤 400ml', 'public/images/products/C05_000002/main.png', 298, 10, 180, 40, 400, 80, 7, 1, 0, NULL, 30, 'トイレをピカピカに。', '除菌・消臭', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(83, '3e857888-d2b9-11f0-94ea-fc3497b62067', 'D01_000001', '炊飯器 5.5合炊き', 'public/images/products/D01_000001/main.png', 12800, 10, 20, 3, 30, 5, 14, 0, 1, NULL, 32, 'ふっくら美味しいご飯が炊けます。', 'IH炊飯器', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(84, '3e8578b4-d2b9-11f0-94ea-fc3497b62067', 'D01_000002', '電子レンジ 22L', 'public/images/products/D01_000002/main.png', 9800, 10, 15, 3, 20, 5, 14, 0, 1, NULL, 32, 'シンプルで使いやすい電子レンジ。', 'ターンテーブル式', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(85, '3e8578de-d2b9-11f0-94ea-fc3497b62067', 'D01_000003', 'トースター 2枚焼き', 'public/images/products/D01_000003/main.png', 4980, 10, 25, 5, 40, 10, 14, 0, 1, NULL, 32, 'コンパクトで場所を取りません。', 'パンがサクッと', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(86, '3e857909-d2b9-11f0-94ea-fc3497b62067', 'D02_000001', '掃除機 サイクロン式', 'public/images/products/D02_000001/main.png', 15800, 10, 18, 3, 25, 5, 14, 0, 1, NULL, 33, '軽量でコードレスタイプ。', '吸引力が落ちない', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(87, '3e857934-d2b9-11f0-94ea-fc3497b62067', 'D02_000002', '扇風機 DCモーター', 'public/images/products/D02_000002/main.png', 7980, 10, 35, 5, 50, 10, 14, 0, 1, NULL, 33, '省エネで電気代も安心。', '静音設計', 1, 0, '2025-04-01 00:00:00', '2025-09-30 23:59:59', '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(88, '3e857962-d2b9-11f0-94ea-fc3497b62067', 'E01_000001', 'メンズTシャツ Mサイズ 白', 'public/images/products/E01_000001/main.png', 1980, 10, 60, 10, 100, 20, 10, 0, 0, NULL, 37, '着心地抜群のTシャツです。', '綿100%', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(89, '3e857992-d2b9-11f0-94ea-fc3497b62067', 'E01_000002', 'メンズジーンズ Lサイズ', 'public/images/products/E01_000002/main.png', 4980, 10, 40, 8, 80, 15, 14, 0, 0, NULL, 37, '動きやすいジーンズです。', 'ストレッチデニム', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(90, '3e8579c0-d2b9-11f0-94ea-fc3497b62067', 'E02_000001', 'レディースワンピース Mサイズ', 'public/images/products/E02_000001/main.png', 5980, 10, 35, 5, 50, 10, 14, 0, 0, NULL, 38, '軽やかな素材で涼しげに。', '春夏向けワンピース', 1, 0, '2025-03-01 00:00:00', '2025-08-31 23:59:59', '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(91, '3e8579fb-d2b9-11f0-94ea-fc3497b62067', 'E02_000002', 'レディーススカート Mサイズ', 'public/images/products/E02_000002/main.png', 3980, 10, 50, 8, 80, 15, 14, 0, 0, NULL, 38, 'どんなトップスにも合います。', 'フレアスカート', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13'),
(92, '3e857a28-d2b9-11f0-94ea-fc3497b62067', 'E04_000001', '靴下 3足セット メンズ', 'public/images/products/E04_000001/main.png', 980, 10, 150, 30, 300, 60, 7, 0, 0, NULL, 40, 'ビジネスにもカジュアルにも。', '抗菌防臭', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-07 12:38:01'),
(93, '3e857a53-d2b9-11f0-94ea-fc3497b62067', 'E04_000002', 'レディース靴下 5足セット', 'public/images/products/E04_000002/main.png', 1280, 10, 120, 25, 250, 50, 7, 0, 0, NULL, 40, 'カラフルな5色セットです。', 'かわいい柄', 1, 0, '2025-01-01 00:00:00', NULL, '2025-12-06 15:23:13', '2025-12-06 15:23:13');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_goods_sequence`
--

CREATE TABLE `t_goods_sequence` (
  `category_code` varchar(20) NOT NULL COMMENT 'カテゴリコード',
  `last_number` int(11) NOT NULL DEFAULT 0 COMMENT '最終採番番号',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品番号採番管理';

--
-- テーブルのデータのダンプ `t_goods_sequence`
--

INSERT INTO `t_goods_sequence` (`category_code`, `last_number`, `updated_at`) VALUES
('A01', 4, '2025-12-06 15:23:13'),
('A02', 3, '2025-12-06 15:23:13'),
('A03', 3, '2025-12-06 15:23:13'),
('A04', 0, '2025-12-06 12:50:44'),
('A05', 2, '2025-12-06 15:23:13'),
('A06', 0, '2025-12-06 12:50:44'),
('A07', 0, '2025-12-06 12:50:44'),
('A08', 2, '2025-12-06 15:23:13'),
('A09', 0, '2025-12-06 12:50:44'),
('A10', 2, '2025-12-06 15:23:13'),
('B01', 2, '2025-12-06 15:23:13'),
('B02', 2, '2025-12-06 15:23:13'),
('B03', 2, '2025-12-06 15:23:13'),
('B04', 0, '2025-12-06 12:50:44'),
('B05', 0, '2025-12-06 12:50:44'),
('B06', 2, '2025-12-06 15:23:13'),
('B07', 0, '2025-12-06 12:50:44'),
('C01', 3, '2025-12-06 15:23:13'),
('C02', 0, '2025-12-06 12:50:44'),
('C03', 2, '2025-12-06 15:23:13'),
('C04', 0, '2025-12-06 12:50:44'),
('C05', 2, '2025-12-06 15:23:13'),
('C06', 0, '2025-12-06 12:50:44'),
('D01', 3, '2025-12-06 15:23:13'),
('D02', 2, '2025-12-06 15:23:13'),
('D03', 0, '2025-12-06 12:50:44'),
('D04', 0, '2025-12-06 12:50:44'),
('D05', 0, '2025-12-06 12:50:44'),
('E01', 2, '2025-12-06 15:23:13'),
('E02', 2, '2025-12-06 15:23:13'),
('E03', 0, '2025-12-06 12:50:44'),
('E04', 2, '2025-12-06 15:23:13'),
('E05', 0, '2025-12-06 12:50:44');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_inventories`
--

CREATE TABLE `t_inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `goods_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品ID',
  `warehouse_id` bigint(20) UNSIGNED NOT NULL COMMENT '倉庫ID',
  `location_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ロケーションID',
  `lot_number` varchar(50) DEFAULT NULL COMMENT 'ロット番号',
  `serial_number` varchar(100) DEFAULT NULL COMMENT 'シリアル番号',
  `quantity` int(11) NOT NULL DEFAULT 0 COMMENT '在庫数',
  `reserved_quantity` int(11) NOT NULL DEFAULT 0 COMMENT '引当済み数量',
  `available_quantity` int(11) GENERATED ALWAYS AS (`quantity` - `reserved_quantity`) STORED COMMENT '利用可能在庫数',
  `expiry_date` date DEFAULT NULL COMMENT '有効期限',
  `manufacturing_date` date DEFAULT NULL COMMENT '製造日',
  `received_date` date DEFAULT NULL COMMENT '入荷日',
  `alert_threshold` int(11) DEFAULT NULL COMMENT 'アラート閾値',
  `status` enum('normal','low_stock','out_of_stock','excess','expired') NOT NULL DEFAULT 'normal' COMMENT 'ステータス',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='在庫マスタ';

--
-- テーブルのデータのダンプ `t_inventories`
--

INSERT INTO `t_inventories` (`id`, `goods_id`, `warehouse_id`, `location_id`, `lot_number`, `serial_number`, `quantity`, `reserved_quantity`, `expiry_date`, `manufacturing_date`, `received_date`, `alert_threshold`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL, NULL, 41, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(2, 2, 1, NULL, NULL, NULL, 28, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(3, 3, 1, NULL, NULL, NULL, 0, 0, NULL, NULL, '2025-12-06', 10, 'out_of_stock', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(4, 4, 1, NULL, NULL, NULL, 19, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(5, 5, 1, NULL, NULL, NULL, 13, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(6, 6, 1, NULL, NULL, NULL, 7, 0, NULL, NULL, '2025-12-06', 10, 'low_stock', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(7, 7, 1, NULL, NULL, NULL, 100, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(8, 8, 1, NULL, NULL, NULL, 24, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(9, 9, 1, NULL, NULL, NULL, 40, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(10, 10, 1, NULL, NULL, NULL, 59, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(11, 11, 1, NULL, NULL, NULL, 35, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(12, 12, 1, NULL, NULL, NULL, 10, 0, NULL, NULL, '2025-12-06', 10, 'low_stock', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(13, 13, 1, NULL, NULL, NULL, 55, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(14, 14, 1, NULL, NULL, NULL, 22, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(15, 15, 1, NULL, NULL, NULL, 28, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(16, 16, 1, NULL, NULL, NULL, 15, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(17, 17, 1, NULL, NULL, NULL, 40, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(18, 18, 1, NULL, NULL, NULL, 30, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(19, 19, 1, NULL, NULL, NULL, 200, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(20, 20, 1, NULL, NULL, NULL, 30, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(21, 21, 1, NULL, NULL, NULL, 25, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(22, 22, 1, NULL, NULL, NULL, 40, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(23, 23, 1, NULL, NULL, NULL, 60, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(24, 24, 1, NULL, NULL, NULL, 20, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(25, 25, 1, NULL, NULL, NULL, 10, 0, NULL, NULL, '2025-12-06', 10, 'low_stock', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(26, 26, 1, NULL, NULL, NULL, 50, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(27, 27, 1, NULL, NULL, NULL, 80, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(28, 28, 1, NULL, NULL, NULL, 60, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(29, 29, 1, NULL, NULL, NULL, 30, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(30, 30, 1, NULL, NULL, NULL, 40, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(31, 31, 1, NULL, NULL, NULL, 100, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(32, 32, 1, NULL, NULL, NULL, 20, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(33, 33, 1, NULL, NULL, NULL, 15, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(34, 34, 1, NULL, NULL, NULL, 25, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(35, 35, 1, NULL, NULL, NULL, 10, 0, NULL, NULL, '2025-12-06', 10, 'low_stock', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(36, 36, 1, NULL, NULL, NULL, 30, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(37, 37, 1, NULL, NULL, NULL, 20, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(38, 38, 1, NULL, NULL, NULL, 15, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(39, 39, 1, NULL, NULL, NULL, 10, 0, NULL, NULL, '2025-12-06', 10, 'low_stock', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(40, 40, 1, NULL, NULL, NULL, 100, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(41, 41, 1, NULL, NULL, NULL, 30, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(42, 42, 1, NULL, NULL, NULL, 20, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(43, 43, 1, NULL, NULL, NULL, 40, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(44, 44, 1, NULL, NULL, NULL, 35, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(45, 45, 1, NULL, NULL, NULL, 50, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(46, 46, 1, NULL, NULL, NULL, 10, 0, NULL, NULL, '2025-12-06', 10, 'low_stock', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(47, 47, 1, NULL, NULL, NULL, 60, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(48, 48, 1, NULL, NULL, NULL, 40, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09'),
(49, 49, 1, NULL, NULL, NULL, 80, 0, NULL, NULL, '2025-12-06', 10, 'normal', '2025-12-05 15:09:09', '2025-12-05 15:09:09');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_inventory_counts`
--

CREATE TABLE `t_inventory_counts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `count_number` varchar(30) NOT NULL COMMENT '棚卸番号',
  `warehouse_id` bigint(20) UNSIGNED NOT NULL COMMENT '倉庫ID',
  `count_date` date NOT NULL COMMENT '棚卸日',
  `status` enum('planning','in_progress','completed','cancelled') NOT NULL DEFAULT 'planning' COMMENT 'ステータス',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '実施者',
  `total_items` int(11) DEFAULT NULL COMMENT '対象商品数',
  `checked_items` int(11) DEFAULT 0 COMMENT 'チェック済み数',
  `notes` text DEFAULT NULL COMMENT '備考',
  `started_at` datetime DEFAULT NULL COMMENT '開始日時',
  `completed_at` datetime DEFAULT NULL COMMENT '完了日時',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='棚卸ヘッダー';

-- --------------------------------------------------------

--
-- テーブルの構造 `t_inventory_count_details`
--

CREATE TABLE `t_inventory_count_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_count_id` bigint(20) UNSIGNED NOT NULL COMMENT '棚卸ID',
  `goods_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品ID',
  `location_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ロケーションID',
  `lot_number` varchar(50) DEFAULT NULL COMMENT 'ロット番号',
  `system_quantity` int(11) NOT NULL COMMENT 'システム在庫数',
  `counted_quantity` int(11) DEFAULT NULL COMMENT '実地棚卸数',
  `difference` int(11) GENERATED ALWAYS AS (`counted_quantity` - `system_quantity`) STORED COMMENT '差異',
  `adjustment_reason` text DEFAULT NULL COMMENT '調整理由',
  `is_adjusted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '調整済みフラグ',
  `counted_at` datetime DEFAULT NULL COMMENT '棚卸実施日時',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='棚卸明細';

-- --------------------------------------------------------

--
-- テーブルの構造 `t_orders`
--

CREATE TABLE `t_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(30) NOT NULL COMMENT '注文番号',
  `total_price` decimal(10,0) NOT NULL COMMENT '合計金額',
  `shipping_fee` decimal(10,0) NOT NULL COMMENT '送料',
  `status_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ステータスID',
  `payment_id` bigint(20) UNSIGNED NOT NULL COMMENT '支払方法ID',
  `shipping_name` varchar(255) NOT NULL COMMENT '配送先宛名(履歴)',
  `shipping_address` text NOT NULL COMMENT '配送先住所(履歴)',
  `ordered_at` datetime NOT NULL COMMENT '注文日時',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stripe_payment_intent_id` varchar(255) DEFAULT NULL COMMENT 'Stripe PaymentIntent ID',
  `stripe_charge_id` varchar(255) DEFAULT NULL COMMENT 'Stripe Charge ID',
  `payment_status` tinyint(4) DEFAULT 0 COMMENT '0:未決済, 1:決済完了, 2:決済失敗, 3:返金済み',
  `payment_error_message` text DEFAULT NULL COMMENT '決済エラーメッセージ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `t_orders`
--

INSERT INTO `t_orders` (`id`, `user_id`, `order_number`, `total_price`, `shipping_fee`, `status_id`, `payment_id`, `shipping_name`, `shipping_address`, `ordered_at`, `created_at`, `updated_at`, `stripe_payment_intent_id`, `stripe_charge_id`, `payment_status`, `payment_error_message`) VALUES
(1, 3, 'ORD-20251129-DAC45DC0', '17500', '0', 2, 1, 'テスト', '〒111-1111 大分県無市夢夢無１１１番地1', '2025-11-29 00:20:12', '2025-11-28 15:20:12', '2025-12-04 13:58:03', 'MANUAL-20251204225803', NULL, 1, NULL),
(2, 3, 'ORD-20251129-E42BE6EF', '39500', '0', 2, 2, 'テスト', '〒111-1111 大分県無市夢夢無１１１番地1', '2025-11-29 00:22:42', '2025-11-28 15:22:42', '2025-12-04 13:59:35', 'MANUAL-20251204225935', NULL, 1, NULL),
(3, 3, 'ORD-20251129-0C47D842', '3500', '500', 2, 1, 'テスト', '〒111-1111 大分県無市夢夢無１１１番地1', '2025-11-29 00:33:24', '2025-11-28 15:33:24', '2025-12-04 13:59:41', 'MANUAL-20251204225941', NULL, 1, NULL),
(4, 3, 'ORD-20251129-7B475820', '12800', '0', 2, 3, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-11-29 20:23:32', '2025-11-29 11:23:32', '2025-12-04 13:59:18', 'MANUAL-20251204225918', NULL, 1, NULL),
(5, 3, 'ORD-20251130-EAC8EFC6', '45400', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-11-30 20:46:52', '2025-11-30 11:46:52', '2025-11-30 11:46:57', 'pi_3SZ95dPE3IinzQzF0Aqr3SzC', NULL, 1, NULL),
(6, 3, 'ORD-20251130-659C690E', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-11-30 22:27:53', '2025-11-30 13:27:53', '2025-11-30 13:27:56', 'pi_3SZAfOPE3IinzQzF0XX24pXa', NULL, 1, NULL),
(7, 3, 'ORD-20251203-0D23E337', '54500', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-03 21:45:06', '2025-12-03 12:45:06', '2025-12-03 12:45:10', 'pi_3SaFQdPE3IinzQzF21JRoZEW', NULL, 1, NULL),
(8, 3, 'ORD-20251203-0D6D605B', '54500', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-03 21:45:10', '2025-12-03 12:45:10', '2025-12-03 12:45:11', 'pi_3SaFQhPE3IinzQzF0Pk9x169', NULL, 1, NULL),
(9, 3, 'ORD-20251203-B0FDAE9D', '3500', '500', 2, 2, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-03 22:28:47', '2025-12-03 13:28:47', '2025-12-04 13:59:08', 'MANUAL-20251204225908', NULL, 1, NULL),
(10, 3, 'ORD-20251203-B5864C88', '16300', '0', 2, 3, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-03 22:30:00', '2025-12-03 13:30:00', '2025-12-04 13:58:26', 'MANUAL-20251204225826', NULL, 1, NULL),
(11, 3, 'ORD-20251203-0768ED0F', '12800', '0', 9, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-03 22:51:50', '2025-12-03 13:51:50', '2025-12-03 13:51:53', 'pi_3SaGTDPE3IinzQzF04AQq0Cu', NULL, 1, NULL),
(12, 3, 'ORD-20251203-7583AAD8', '7000', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-03 23:21:12', '2025-12-03 14:21:12', '2025-12-04 13:57:07', 'MANUAL-20251204225707', NULL, 1, NULL),
(13, 3, 'ORD-20251203-7FA90595', '27800', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-03 23:23:54', '2025-12-03 14:23:54', '2025-12-04 13:58:11', 'MANUAL-20251204225811', NULL, 1, NULL),
(14, 3, 'ORD-20251204-107B8EA1', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 00:02:31', '2025-12-03 15:02:31', '2025-12-04 13:58:19', 'MANUAL-20251204225819', NULL, 1, NULL),
(15, 3, 'ORD-20251204-47CAC0DD', '1800', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 00:17:16', '2025-12-03 15:17:16', '2025-12-04 13:58:32', 'MANUAL-20251204225832', NULL, 1, NULL),
(16, 3, 'ORD-20251204-DCBC55A0', '16300', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 20:17:31', '2025-12-04 11:17:31', '2025-12-04 13:58:39', 'MANUAL-20251204225839', NULL, 1, NULL),
(17, 3, 'ORD-20251204-EFD1C1BD', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 20:22:37', '2025-12-04 11:22:37', '2025-12-04 13:59:01', 'MANUAL-20251204225901', NULL, 1, NULL),
(18, 3, 'ORD-20251204-2846BA28', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 20:37:40', '2025-12-04 11:37:40', '2025-12-04 13:58:54', 'MANUAL-20251204225854', NULL, 1, NULL),
(19, 3, 'ORD-20251204-556BFC91', '8900', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 20:49:42', '2025-12-04 11:49:42', '2025-12-04 13:58:47', 'MANUAL-20251204225847', NULL, 1, NULL),
(20, 3, 'ORD-20251204-6EF86676', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 20:56:31', '2025-12-04 11:56:31', '2025-12-04 13:57:53', 'MANUAL-20251204225753', NULL, 1, NULL),
(21, 3, 'ORD-20251204-84FB25D1', '12800', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 21:02:23', '2025-12-04 12:02:23', '2025-12-04 13:55:17', 'MANUAL-20251204225517', NULL, 1, NULL),
(22, 3, 'ORD-20251204-3E7EED6A', '12800', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 23:00:07', '2025-12-04 14:00:07', '2025-12-04 14:06:41', 'MANUAL-20251204230641', NULL, 1, NULL),
(23, 3, 'ORD-20251204-533B5899', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 23:05:39', '2025-12-04 14:05:39', '2025-12-04 14:43:15', 'MANUAL-20251204234315', NULL, 1, NULL),
(24, 3, 'ORD-20251204-549996E1', '16300', '0', 2, 3, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-04 23:06:01', '2025-12-04 14:06:01', '2025-12-04 14:06:57', 'MANUAL-20251204230657', NULL, 1, NULL),
(25, 3, 'ORD-20251205-A4343ECE', '21700', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 00:35:31', '2025-12-04 15:35:31', '2025-12-04 16:14:34', 'MANUAL-20251205011434', NULL, 1, NULL),
(26, 3, 'ORD-20251205-A61ABC68', '6500', '500', 2, 3, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 00:36:01', '2025-12-04 15:36:01', '2025-12-04 15:48:24', 'MANUAL-20251205004824', NULL, 1, NULL),
(27, 3, 'ORD-20251205-D5F5697A', '12800', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 00:48:47', '2025-12-04 15:48:47', '2025-12-04 16:14:31', 'MANUAL-20251205011431', NULL, 1, NULL),
(28, 3, 'ORD-20251205-DA4BC1BD', '19300', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 00:49:56', '2025-12-04 15:49:56', '2025-12-04 16:14:28', 'MANUAL-20251205011428', NULL, 1, NULL),
(29, 3, 'ORD-20251205-DCE75CC0', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 00:50:38', '2025-12-04 15:50:38', '2025-12-04 16:14:25', 'MANUAL-20251205011425', NULL, 1, NULL),
(30, 3, 'ORD-20251205-E3A5D92B', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 00:52:26', '2025-12-04 15:52:26', '2025-12-04 16:14:22', 'MANUAL-20251205011422', NULL, 1, NULL),
(31, 3, 'ORD-20251205-E62698B1', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 00:53:06', '2025-12-04 15:53:06', '2025-12-04 16:14:19', 'MANUAL-20251205011419', NULL, 1, NULL),
(32, 3, 'ORD-20251205-EBF8B492', '12800', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 00:54:39', '2025-12-04 15:54:39', '2025-12-04 16:14:15', 'MANUAL-20251205011415', NULL, 1, NULL),
(33, 3, 'ORD-20251205-15C2B9BD', '12800', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 01:05:48', '2025-12-04 16:05:48', '2025-12-04 16:14:12', 'MANUAL-20251205011412', NULL, 1, NULL),
(34, 3, 'ORD-20251205-18275715', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 01:06:26', '2025-12-04 16:06:26', '2025-12-04 16:14:09', 'MANUAL-20251205011409', NULL, 1, NULL),
(35, 3, 'ORD-20251205-2027CC0F', '12800', '0', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 01:08:34', '2025-12-04 16:08:34', '2025-12-04 16:14:04', 'MANUAL-20251205011404', NULL, 1, NULL),
(36, 3, 'ORD-20251205-307B697F', '3500', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 01:12:55', '2025-12-04 16:12:55', '2025-12-04 16:12:56', 'pi_3Saf9HPE3IinzQzF2SrP59MC', 'ch_3Saf9HPE3IinzQzF2y20Ciza', 1, NULL),
(37, 3, 'ORD-20251205-32EF1F53', '3500', '500', 2, 3, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 01:13:34', '2025-12-04 16:13:34', '2025-12-04 16:13:56', 'MANUAL-20251205011356', NULL, 1, NULL),
(38, 3, 'ORD-20251205-39946F06', '7800', '500', 2, 1, 'テスト（注文確認で追加）', '〒222-1111 島根県島根市島根番地3', '2025-12-05 01:15:21', '2025-12-04 16:15:21', '2025-12-04 16:15:22', 'pi_3SafBdPE3IinzQzF19V6m2Sg', 'ch_3SafBdPE3IinzQzF171rpT35', 1, NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `t_order_details`
--

CREATE TABLE `t_order_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `goods_id` bigint(20) UNSIGNED NOT NULL,
  `goods_name` varchar(255) NOT NULL COMMENT '商品名(履歴)',
  `price` decimal(10,0) NOT NULL COMMENT '購入単価',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `t_order_details`
--

INSERT INTO `t_order_details` (`id`, `order_id`, `goods_id`, `goods_name`, `price`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'クラシックロゴTシャツ ホワイト', '3500', 3, '2025-11-28 15:20:12', '2025-11-28 15:20:12'),
(2, 1, 2, 'クラシックロゴTシャツ ブラック', '3500', 2, '2025-11-28 15:20:12', '2025-11-28 15:20:12'),
(3, 2, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-11-28 15:22:42', '2025-11-28 15:22:42'),
(4, 2, 4, 'スリムフィットチノパン ベージュ', '8900', 3, '2025-11-28 15:22:42', '2025-11-28 15:22:42'),
(5, 3, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-11-28 15:33:24', '2025-11-28 15:33:24'),
(6, 4, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-11-29 11:23:32', '2025-11-29 11:23:32'),
(7, 5, 1, 'クラシックロゴTシャツ ホワイト', '3500', 1, '2025-11-30 11:46:52', '2025-11-30 11:46:52'),
(8, 5, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-11-30 11:46:52', '2025-11-30 11:46:52'),
(9, 5, 3, 'ヴィンテージデニムパンツ', '12800', 3, '2025-11-30 11:46:52', '2025-11-30 11:46:52'),
(10, 6, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-11-30 13:27:53', '2025-11-30 13:27:53'),
(11, 7, 6, 'レザーショルダーバッグ', '15000', 1, '2025-12-03 12:45:06', '2025-12-03 12:45:06'),
(12, 7, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-03 12:45:06', '2025-12-03 12:45:06'),
(13, 7, 4, 'スリムフィットチノパン ベージュ', '8900', 3, '2025-12-03 12:45:06', '2025-12-03 12:45:06'),
(14, 8, 6, 'レザーショルダーバッグ', '15000', 1, '2025-12-03 12:45:10', '2025-12-03 12:45:10'),
(15, 8, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-03 12:45:10', '2025-12-03 12:45:10'),
(16, 8, 4, 'スリムフィットチノパン ベージュ', '8900', 3, '2025-12-03 12:45:10', '2025-12-03 12:45:10'),
(17, 9, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-03 13:28:47', '2025-12-03 13:28:47'),
(18, 10, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-03 13:30:00', '2025-12-03 13:30:00'),
(19, 10, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-03 13:30:00', '2025-12-03 13:30:00'),
(20, 11, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-03 13:51:50', '2025-12-03 13:51:50'),
(21, 12, 1, 'クラシックロゴTシャツ ホワイト', '3500', 2, '2025-12-03 14:21:12', '2025-12-03 14:21:12'),
(22, 13, 6, 'レザーショルダーバッグ', '15000', 1, '2025-12-03 14:23:54', '2025-12-03 14:23:54'),
(23, 13, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-03 14:23:54', '2025-12-03 14:23:54'),
(24, 14, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-03 15:02:31', '2025-12-03 15:02:31'),
(25, 15, 10, 'ニットキャップ ネイビー', '1800', 1, '2025-12-03 15:17:16', '2025-12-03 15:17:16'),
(26, 16, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-04 11:17:31', '2025-12-04 11:17:31'),
(27, 16, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 11:17:31', '2025-12-04 11:17:31'),
(28, 17, 1, 'クラシックロゴTシャツ ホワイト', '3500', 1, '2025-12-04 11:22:37', '2025-12-04 11:22:37'),
(29, 18, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-04 11:37:40', '2025-12-04 11:37:40'),
(30, 19, 4, 'スリムフィットチノパン ベージュ', '8900', 1, '2025-12-04 11:49:42', '2025-12-04 11:49:42'),
(31, 20, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-04 11:56:31', '2025-12-04 11:56:31'),
(32, 21, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 12:02:23', '2025-12-04 12:02:23'),
(33, 22, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 14:00:07', '2025-12-04 14:00:07'),
(34, 23, 1, 'クラシックロゴTシャツ ホワイト', '3500', 1, '2025-12-04 14:05:39', '2025-12-04 14:05:39'),
(35, 24, 1, 'クラシックロゴTシャツ ホワイト', '3500', 1, '2025-12-04 14:06:01', '2025-12-04 14:06:01'),
(36, 24, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 14:06:01', '2025-12-04 14:06:01'),
(37, 25, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 15:35:31', '2025-12-04 15:35:31'),
(38, 25, 4, 'スリムフィットチノパン ベージュ', '8900', 1, '2025-12-04 15:35:31', '2025-12-04 15:35:31'),
(39, 26, 5, 'オーバーサイズパーカー グレー', '6500', 1, '2025-12-04 15:36:01', '2025-12-04 15:36:01'),
(40, 27, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 15:48:47', '2025-12-04 15:48:47'),
(41, 28, 5, 'オーバーサイズパーカー グレー', '6500', 1, '2025-12-04 15:49:56', '2025-12-04 15:49:56'),
(42, 28, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 15:49:56', '2025-12-04 15:49:56'),
(43, 29, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-04 15:50:38', '2025-12-04 15:50:38'),
(44, 30, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-04 15:52:26', '2025-12-04 15:52:26'),
(45, 31, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-04 15:53:06', '2025-12-04 15:53:06'),
(46, 32, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 15:54:39', '2025-12-04 15:54:39'),
(47, 33, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 16:05:48', '2025-12-04 16:05:48'),
(48, 34, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-04 16:06:26', '2025-12-04 16:06:26'),
(49, 35, 3, 'ヴィンテージデニムパンツ', '12800', 1, '2025-12-04 16:08:34', '2025-12-04 16:08:34'),
(50, 36, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-04 16:12:55', '2025-12-04 16:12:55'),
(51, 37, 2, 'クラシックロゴTシャツ ブラック', '3500', 1, '2025-12-04 16:13:35', '2025-12-04 16:13:35'),
(52, 38, 8, 'ハイカットスニーカー 赤', '7800', 1, '2025-12-04 16:15:21', '2025-12-04 16:15:21');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_shipping_addresses`
--

CREATE TABLE `t_shipping_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ユーザーID',
  `name` varchar(255) NOT NULL COMMENT '宛名',
  `postal_code` varchar(8) NOT NULL COMMENT '郵便番号',
  `prefecture_id` bigint(20) UNSIGNED NOT NULL COMMENT '都道府県ID',
  `city` varchar(255) NOT NULL COMMENT '市区町村',
  `address_line` varchar(255) NOT NULL COMMENT '番地・建物',
  `phone` varchar(20) NOT NULL COMMENT '電話番号',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'デフォルトフラグ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `t_shipping_addresses`
--

INSERT INTO `t_shipping_addresses` (`id`, `user_id`, `name`, `postal_code`, `prefecture_id`, `city`, `address_line`, `phone`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 3, 'テスト', '111-1111', 44, '無市夢夢無１１１', '番地1', '01011111111', 0, '2025-11-28 15:19:12', '2025-11-29 11:22:07'),
(2, 3, 'テスト（注文確認で追加）', '222-1111', 32, '島根市島根', '番地3', '1111222', 1, '2025-11-29 11:22:07', '2025-11-29 11:22:07');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_stock_alerts`
--

CREATE TABLE `t_stock_alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `goods_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品ID',
  `warehouse_id` bigint(20) UNSIGNED NOT NULL COMMENT '倉庫ID',
  `alert_type` enum('low_stock','out_of_stock','excess','expiry_warning','expiry_critical') NOT NULL COMMENT 'アラート種別',
  `current_quantity` int(11) NOT NULL COMMENT '現在在庫数',
  `threshold_quantity` int(11) DEFAULT NULL COMMENT '閾値',
  `expiry_date` date DEFAULT NULL COMMENT '有効期限',
  `alert_date` datetime NOT NULL COMMENT 'アラート発生日時',
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '解決済みフラグ',
  `resolved_at` datetime DEFAULT NULL COMMENT '解決日時',
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT '解決者',
  `notes` text DEFAULT NULL COMMENT '備考',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='在庫アラート';

-- --------------------------------------------------------

--
-- テーブルの構造 `t_stock_movements`
--

CREATE TABLE `t_stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `goods_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品ID',
  `warehouse_id` bigint(20) UNSIGNED NOT NULL COMMENT '倉庫ID',
  `location_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ロケーションID',
  `lot_number` varchar(50) DEFAULT NULL COMMENT 'ロット番号',
  `serial_number` varchar(100) DEFAULT NULL COMMENT 'シリアル番号',
  `movement_type` enum('in','out','adjust','transfer','return','reserve','release') NOT NULL COMMENT '入出庫区分',
  `quantity` int(11) NOT NULL COMMENT '数量(±)',
  `before_quantity` int(11) NOT NULL COMMENT '変更前在庫数',
  `after_quantity` int(11) NOT NULL COMMENT '変更後在庫数',
  `reference_type` varchar(50) DEFAULT NULL COMMENT '参照元',
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '参照元ID',
  `notes` text DEFAULT NULL COMMENT '備考',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '処理者',
  `movement_date` datetime NOT NULL COMMENT '入出庫日時',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='入出庫履歴';

--
-- テーブルのデータのダンプ `t_stock_movements`
--

INSERT INTO `t_stock_movements` (`id`, `goods_id`, `warehouse_id`, `location_id`, `lot_number`, `serial_number`, `movement_type`, `quantity`, `before_quantity`, `after_quantity`, `reference_type`, `reference_id`, `notes`, `user_id`, `movement_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL, NULL, 'in', 41, 0, 41, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(2, 2, 1, NULL, NULL, NULL, 'in', 28, 0, 28, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(3, 4, 1, NULL, NULL, NULL, 'in', 19, 0, 19, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(4, 5, 1, NULL, NULL, NULL, 'in', 13, 0, 13, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(5, 6, 1, NULL, NULL, NULL, 'in', 7, 0, 7, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(6, 7, 1, NULL, NULL, NULL, 'in', 100, 0, 100, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(7, 8, 1, NULL, NULL, NULL, 'in', 24, 0, 24, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(8, 9, 1, NULL, NULL, NULL, 'in', 40, 0, 40, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(9, 10, 1, NULL, NULL, NULL, 'in', 59, 0, 59, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(10, 11, 1, NULL, NULL, NULL, 'in', 35, 0, 35, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(11, 12, 1, NULL, NULL, NULL, 'in', 10, 0, 10, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(12, 13, 1, NULL, NULL, NULL, 'in', 55, 0, 55, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(13, 14, 1, NULL, NULL, NULL, 'in', 22, 0, 22, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(14, 15, 1, NULL, NULL, NULL, 'in', 28, 0, 28, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(15, 16, 1, NULL, NULL, NULL, 'in', 15, 0, 15, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(16, 17, 1, NULL, NULL, NULL, 'in', 40, 0, 40, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(17, 18, 1, NULL, NULL, NULL, 'in', 30, 0, 30, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(18, 19, 1, NULL, NULL, NULL, 'in', 200, 0, 200, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(19, 20, 1, NULL, NULL, NULL, 'in', 30, 0, 30, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(20, 21, 1, NULL, NULL, NULL, 'in', 25, 0, 25, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(21, 22, 1, NULL, NULL, NULL, 'in', 40, 0, 40, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(22, 23, 1, NULL, NULL, NULL, 'in', 60, 0, 60, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(23, 24, 1, NULL, NULL, NULL, 'in', 20, 0, 20, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(24, 25, 1, NULL, NULL, NULL, 'in', 10, 0, 10, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(25, 26, 1, NULL, NULL, NULL, 'in', 50, 0, 50, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(26, 27, 1, NULL, NULL, NULL, 'in', 80, 0, 80, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(27, 28, 1, NULL, NULL, NULL, 'in', 60, 0, 60, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(28, 29, 1, NULL, NULL, NULL, 'in', 30, 0, 30, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(29, 30, 1, NULL, NULL, NULL, 'in', 40, 0, 40, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(30, 31, 1, NULL, NULL, NULL, 'in', 100, 0, 100, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(31, 32, 1, NULL, NULL, NULL, 'in', 20, 0, 20, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(32, 33, 1, NULL, NULL, NULL, 'in', 15, 0, 15, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(33, 34, 1, NULL, NULL, NULL, 'in', 25, 0, 25, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(34, 35, 1, NULL, NULL, NULL, 'in', 10, 0, 10, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(35, 36, 1, NULL, NULL, NULL, 'in', 30, 0, 30, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(36, 37, 1, NULL, NULL, NULL, 'in', 20, 0, 20, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(37, 38, 1, NULL, NULL, NULL, 'in', 15, 0, 15, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(38, 39, 1, NULL, NULL, NULL, 'in', 10, 0, 10, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(39, 40, 1, NULL, NULL, NULL, 'in', 100, 0, 100, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(40, 41, 1, NULL, NULL, NULL, 'in', 30, 0, 30, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(41, 42, 1, NULL, NULL, NULL, 'in', 20, 0, 20, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(42, 43, 1, NULL, NULL, NULL, 'in', 40, 0, 40, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(43, 44, 1, NULL, NULL, NULL, 'in', 35, 0, 35, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(44, 45, 1, NULL, NULL, NULL, 'in', 50, 0, 50, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(45, 46, 1, NULL, NULL, NULL, 'in', 10, 0, 10, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(46, 47, 1, NULL, NULL, NULL, 'in', 60, 0, 60, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(47, 48, 1, NULL, NULL, NULL, 'in', 40, 0, 40, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10'),
(48, 49, 1, NULL, NULL, NULL, 'in', 80, 0, 80, 'initial', NULL, '初期在庫登録（既存データ移行）', NULL, '2025-12-06 00:09:10', '2025-12-05 15:09:10', '2025-12-05 15:09:10');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL COMMENT '電話番号',
  `birthday` date DEFAULT NULL COMMENT '生年月日',
  `gender` tinyint(4) DEFAULT NULL COMMENT '性別(1:男,2:女,9:その他)',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0 COMMENT '管理者フラグ',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '会員ステータス(1:通常,2:退会)',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delete_flg` tinyint(1) NOT NULL DEFAULT 0 COMMENT '削除フラグ 0:有効 1:削除済み'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `birthday`, `gender`, `is_admin`, `status`, `remember_token`, `created_at`, `updated_at`, `delete_flg`) VALUES
(3, 'youtaishi754', 'youtaishi754@gmail.com', NULL, '$2y$10$ks.DEH3QzyUH4Jz6ugynAOqHxotQudqMv3n5hAZ3YK4fampFryrD.', NULL, NULL, NULL, 0, 1, NULL, '2025-11-24 14:46:04', '2025-11-24 14:46:04', 0);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_categories`
--
ALTER TABLE `m_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_code` (`category_code`),
  ADD KEY `idx_category_code` (`category_code`),
  ADD KEY `idx_parent_id` (`parent_id`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_level` (`level`);

--
-- テーブルのインデックス `m_locations`
--
ALTER TABLE `m_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_location` (`warehouse_id`,`location_code`),
  ADD KEY `idx_warehouse` (`warehouse_id`),
  ADD KEY `idx_location_code` (`location_code`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- テーブルのインデックス `m_lots`
--
ALTER TABLE `m_lots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lot_number` (`lot_number`),
  ADD KEY `idx_lot_number` (`lot_number`),
  ADD KEY `idx_goods` (`goods_id`),
  ADD KEY `idx_expiry_date` (`expiry_date`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- テーブルのインデックス `m_order_statuses`
--
ALTER TABLE `m_order_statuses`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_payment_methods`
--
ALTER TABLE `m_payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_prefectures`
--
ALTER TABLE `m_prefectures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `m_prefectures_code_unique` (`code`);

--
-- テーブルのインデックス `m_warehouses`
--
ALTER TABLE `m_warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouse_code` (`warehouse_code`),
  ADD KEY `idx_warehouse_code` (`warehouse_code`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `prefecture_id` (`prefecture_id`);

--
-- テーブルのインデックス `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- テーブルのインデックス `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- テーブルのインデックス `pre_registrations`
--
ALTER TABLE `pre_registrations`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `t_favorites`
--
ALTER TABLE `t_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `t_favorites_user_id_goods_id_unique` (`user_id`,`goods_id`),
  ADD KEY `t_favorites_goods_id_foreign` (`goods_id`);

--
-- テーブルのインデックス `t_goods`
--
ALTER TABLE `t_goods`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `t_goods_sequence`
--
ALTER TABLE `t_goods_sequence`
  ADD PRIMARY KEY (`category_code`),
  ADD UNIQUE KEY `unique_category` (`category_code`);

--
-- テーブルのインデックス `t_inventories`
--
ALTER TABLE `t_inventories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inventory` (`goods_id`,`warehouse_id`,`location_id`,`lot_number`,`serial_number`),
  ADD KEY `idx_goods` (`goods_id`),
  ADD KEY `idx_warehouse` (`warehouse_id`),
  ADD KEY `idx_location` (`location_id`),
  ADD KEY `idx_lot` (`lot_number`),
  ADD KEY `idx_serial` (`serial_number`),
  ADD KEY `idx_expiry` (`expiry_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_available` (`available_quantity`);

--
-- テーブルのインデックス `t_inventory_counts`
--
ALTER TABLE `t_inventory_counts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `count_number` (`count_number`),
  ADD KEY `idx_count_number` (`count_number`),
  ADD KEY `idx_warehouse` (`warehouse_id`),
  ADD KEY `idx_count_date` (`count_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user` (`user_id`);

--
-- テーブルのインデックス `t_inventory_count_details`
--
ALTER TABLE `t_inventory_count_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventory_count` (`inventory_count_id`),
  ADD KEY `idx_goods` (`goods_id`),
  ADD KEY `idx_is_adjusted` (`is_adjusted`),
  ADD KEY `location_id` (`location_id`);

--
-- テーブルのインデックス `t_orders`
--
ALTER TABLE `t_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `t_orders_order_number_unique` (`order_number`),
  ADD KEY `t_orders_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `t_order_details`
--
ALTER TABLE `t_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_order_details_order_id_foreign` (`order_id`),
  ADD KEY `t_order_details_goods_id_foreign` (`goods_id`);

--
-- テーブルのインデックス `t_shipping_addresses`
--
ALTER TABLE `t_shipping_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_shipping_addresses_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `t_stock_alerts`
--
ALTER TABLE `t_stock_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_goods` (`goods_id`),
  ADD KEY `idx_warehouse` (`warehouse_id`),
  ADD KEY `idx_alert_type` (`alert_type`),
  ADD KEY `idx_is_resolved` (`is_resolved`),
  ADD KEY `idx_alert_date` (`alert_date`),
  ADD KEY `resolved_by` (`resolved_by`);

--
-- テーブルのインデックス `t_stock_movements`
--
ALTER TABLE `t_stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_goods` (`goods_id`),
  ADD KEY `idx_warehouse` (`warehouse_id`),
  ADD KEY `idx_movement_type` (`movement_type`),
  ADD KEY `idx_movement_date` (`movement_date`),
  ADD KEY `idx_reference` (`reference_type`,`reference_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `location_id` (`location_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `m_categories`
--
ALTER TABLE `m_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- テーブルのAUTO_INCREMENT `m_locations`
--
ALTER TABLE `m_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルのAUTO_INCREMENT `m_lots`
--
ALTER TABLE `m_lots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `m_order_statuses`
--
ALTER TABLE `m_order_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- テーブルのAUTO_INCREMENT `m_payment_methods`
--
ALTER TABLE `m_payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルのAUTO_INCREMENT `m_prefectures`
--
ALTER TABLE `m_prefectures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- テーブルのAUTO_INCREMENT `m_warehouses`
--
ALTER TABLE `m_warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルのAUTO_INCREMENT `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `pre_registrations`
--
ALTER TABLE `pre_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルのAUTO_INCREMENT `t_favorites`
--
ALTER TABLE `t_favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `t_goods`
--
ALTER TABLE `t_goods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- テーブルのAUTO_INCREMENT `t_inventories`
--
ALTER TABLE `t_inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- テーブルのAUTO_INCREMENT `t_inventory_counts`
--
ALTER TABLE `t_inventory_counts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `t_inventory_count_details`
--
ALTER TABLE `t_inventory_count_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `t_orders`
--
ALTER TABLE `t_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- テーブルのAUTO_INCREMENT `t_order_details`
--
ALTER TABLE `t_order_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- テーブルのAUTO_INCREMENT `t_shipping_addresses`
--
ALTER TABLE `t_shipping_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `t_stock_alerts`
--
ALTER TABLE `t_stock_alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `t_stock_movements`
--
ALTER TABLE `t_stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- テーブルのAUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `m_categories`
--
ALTER TABLE `m_categories`
  ADD CONSTRAINT `m_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `m_categories` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `m_locations`
--
ALTER TABLE `m_locations`
  ADD CONSTRAINT `m_locations_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `m_warehouses` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `m_lots`
--
ALTER TABLE `m_lots`
  ADD CONSTRAINT `m_lots_ibfk_1` FOREIGN KEY (`goods_id`) REFERENCES `t_goods` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `m_warehouses`
--
ALTER TABLE `m_warehouses`
  ADD CONSTRAINT `m_warehouses_ibfk_1` FOREIGN KEY (`prefecture_id`) REFERENCES `m_prefectures` (`id`) ON DELETE SET NULL;

--
-- テーブルの制約 `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD CONSTRAINT `payment_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `t_orders` (`id`);

--
-- テーブルの制約 `t_favorites`
--
ALTER TABLE `t_favorites`
  ADD CONSTRAINT `t_favorites_goods_id_foreign` FOREIGN KEY (`goods_id`) REFERENCES `t_goods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `t_goods_sequence`
--
ALTER TABLE `t_goods_sequence`
  ADD CONSTRAINT `t_goods_sequence_ibfk_1` FOREIGN KEY (`category_code`) REFERENCES `m_categories` (`category_code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `t_inventories`
--
ALTER TABLE `t_inventories`
  ADD CONSTRAINT `t_inventories_ibfk_1` FOREIGN KEY (`goods_id`) REFERENCES `t_goods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_inventories_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `m_warehouses` (`id`),
  ADD CONSTRAINT `t_inventories_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `m_locations` (`id`) ON DELETE SET NULL;

--
-- テーブルの制約 `t_inventory_counts`
--
ALTER TABLE `t_inventory_counts`
  ADD CONSTRAINT `t_inventory_counts_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `m_warehouses` (`id`),
  ADD CONSTRAINT `t_inventory_counts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- テーブルの制約 `t_inventory_count_details`
--
ALTER TABLE `t_inventory_count_details`
  ADD CONSTRAINT `t_inventory_count_details_ibfk_1` FOREIGN KEY (`inventory_count_id`) REFERENCES `t_inventory_counts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_inventory_count_details_ibfk_2` FOREIGN KEY (`goods_id`) REFERENCES `t_goods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_inventory_count_details_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `m_locations` (`id`) ON DELETE SET NULL;

--
-- テーブルの制約 `t_orders`
--
ALTER TABLE `t_orders`
  ADD CONSTRAINT `t_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `t_order_details`
--
ALTER TABLE `t_order_details`
  ADD CONSTRAINT `t_order_details_goods_id_foreign` FOREIGN KEY (`goods_id`) REFERENCES `t_goods` (`id`),
  ADD CONSTRAINT `t_order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `t_orders` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `t_shipping_addresses`
--
ALTER TABLE `t_shipping_addresses`
  ADD CONSTRAINT `t_shipping_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `t_stock_alerts`
--
ALTER TABLE `t_stock_alerts`
  ADD CONSTRAINT `t_stock_alerts_ibfk_1` FOREIGN KEY (`goods_id`) REFERENCES `t_goods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_stock_alerts_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `m_warehouses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_stock_alerts_ibfk_3` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- テーブルの制約 `t_stock_movements`
--
ALTER TABLE `t_stock_movements`
  ADD CONSTRAINT `t_stock_movements_ibfk_1` FOREIGN KEY (`goods_id`) REFERENCES `t_goods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_stock_movements_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `m_warehouses` (`id`),
  ADD CONSTRAINT `t_stock_movements_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `m_locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `t_stock_movements_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
