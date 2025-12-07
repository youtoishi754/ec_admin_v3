-- ===================================
-- 画像パスを修正
-- 作成日: 2025-12-07
-- 説明: パスを public/ から始まる形式に統一
-- ===================================

-- 画像パスの修正
UPDATE t_goods 
SET image_path = CONCAT('public/', SUBSTRING_INDEX(image_path, 'public/', -1))
WHERE image_path LIKE '%public/%' AND image_path NOT LIKE 'public/%';

-- 確認
SELECT 
  goods_number,
  goods_name,
  image_path
FROM t_goods 
WHERE image_path IS NOT NULL
ORDER BY id DESC
LIMIT 10;

-- 期待される結果: public/images/products/A01_000001/main.jpg
