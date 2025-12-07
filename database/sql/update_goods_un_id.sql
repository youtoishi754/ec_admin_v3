-- ===================================
-- t_goodsテーブルのun_idにUUIDを設定
-- 作成日: 2025-12-07
-- 説明: 既存商品データにUUIDを生成して設定
-- ===================================

-- un_idが空の商品にUUIDを設定
UPDATE t_goods 
SET un_id = UUID() 
WHERE un_id IS NULL OR un_id = '';

-- 確認: un_idが設定されたか確認
SELECT 
  id,
  un_id,
  goods_number,
  goods_name,
  LENGTH(un_id) as un_id_length
FROM t_goods 
ORDER BY id
LIMIT 20;

-- un_idの重複チェック
SELECT 
  un_id, 
  COUNT(*) as count 
FROM t_goods 
GROUP BY un_id 
HAVING COUNT(*) > 1;

-- 統計情報
SELECT 
  COUNT(*) as total_goods,
  SUM(CASE WHEN un_id IS NOT NULL AND un_id != '' THEN 1 ELSE 0 END) as with_un_id,
  SUM(CASE WHEN un_id IS NULL OR un_id = '' THEN 1 ELSE 0 END) as without_un_id
FROM t_goods;
