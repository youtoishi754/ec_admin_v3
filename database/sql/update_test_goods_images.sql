-- ===================================
-- テストデータに画像パスを追加
-- 作成日: 2025-12-07
-- 説明: 全商品にデフォルト画像パス（main.png）を設定
-- ===================================

-- 既存の全商品に画像パスを設定
UPDATE t_goods 
SET image_path = CONCAT('public/images/products/', goods_number, '/main.png')
WHERE image_path IS NULL OR image_path = '';

-- 確認
SELECT 
  goods_number,
  goods_name,
  image_path
FROM t_goods 
ORDER BY goods_number
LIMIT 20;

-- ===================================
-- 注意事項
-- ===================================
-- このSQLを実行した後、実際の画像ファイルは手動で配置する必要があります
-- または、ダミー画像を一括コピーするスクリプトを実行してください

-- ダミー画像作成用のPowerShellコマンド例:
-- $products = @('A01_000001', 'A01_000002', 'A02_000001', 'B01_000001', etc...)
-- foreach ($p in $products) {
--   $dir = "F:\windows11_user\xampp\htdocs\ec_admin\public\images\products\$p"
--   New-Item -Path $dir -ItemType Directory -Force
--   Copy-Item "path\to\dummy.png" "$dir\main.png"
-- }
