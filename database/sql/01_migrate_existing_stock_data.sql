-- ===================================
-- 既存在庫データ移行SQL
-- 作成日: 2025-12-06
-- 説明: t_goods.goods_stock を t_inventories に移行
-- 実行タイミング: 在庫管理テーブル作成後に1回だけ実行
-- ===================================

-- 既存のgoods_stockデータをt_inventoriesに移行
-- デフォルト倉庫(ID=1: 本社倉庫)に全在庫を登録
INSERT INTO t_inventories (
  goods_id,
  warehouse_id,
  location_id,
  lot_number,
  serial_number,
  quantity,
  reserved_quantity,
  expiry_date,
  manufacturing_date,
  received_date,
  alert_threshold,
  status,
  created_at,
  updated_at
)
SELECT 
  id AS goods_id,
  1 AS warehouse_id,
  NULL AS location_id,
  NULL AS lot_number,
  NULL AS serial_number,
  goods_stock AS quantity,
  0 AS reserved_quantity,
  NULL AS expiry_date,
  NULL AS manufacturing_date,
  NOW() AS received_date,
  min_stock_level AS alert_threshold,
  CASE 
    WHEN goods_stock = 0 THEN 'out_of_stock'
    WHEN goods_stock <= COALESCE(min_stock_level, 10) THEN 'low_stock'
    WHEN goods_stock > COALESCE(max_stock_level, 200) THEN 'excess'
    ELSE 'normal'
  END AS status,
  NOW() AS created_at,
  NOW() AS updated_at
FROM t_goods
WHERE delete_flg = 0
ON DUPLICATE KEY UPDATE 
  quantity = VALUES(quantity),
  status = VALUES(status),
  updated_at = NOW();

-- 初期在庫登録を履歴に記録
INSERT INTO t_stock_movements (
  goods_id,
  warehouse_id,
  location_id,
  lot_number,
  serial_number,
  movement_type,
  quantity,
  before_quantity,
  after_quantity,
  reference_type,
  reference_id,
  notes,
  user_id,
  movement_date,
  created_at,
  updated_at
)
SELECT 
  id AS goods_id,
  1 AS warehouse_id,
  NULL AS location_id,
  NULL AS lot_number,
  NULL AS serial_number,
  'in' AS movement_type,
  goods_stock AS quantity,
  0 AS before_quantity,
  goods_stock AS after_quantity,
  'initial' AS reference_type,
  NULL AS reference_id,
  '初期在庫登録（既存データ移行）' AS notes,
  NULL AS user_id,
  NOW() AS movement_date,
  NOW() AS created_at,
  NOW() AS updated_at
FROM t_goods
WHERE delete_flg = 0 AND goods_stock > 0;

-- 低在庫・欠品アラートの自動生成
INSERT INTO t_stock_alerts (
  goods_id,
  warehouse_id,
  alert_type,
  current_quantity,
  threshold_quantity,
  expiry_date,
  alert_date,
  is_resolved,
  created_at,
  updated_at
)
SELECT 
  g.id AS goods_id,
  1 AS warehouse_id,
  CASE 
    WHEN g.goods_stock = 0 THEN 'out_of_stock'
    WHEN g.goods_stock <= COALESCE(g.min_stock_level, 10) THEN 'low_stock'
  END AS alert_type,
  g.goods_stock AS current_quantity,
  COALESCE(g.min_stock_level, 10) AS threshold_quantity,
  NULL AS expiry_date,
  NOW() AS alert_date,
  0 AS is_resolved,
  NOW() AS created_at,
  NOW() AS updated_at
FROM t_goods g
WHERE g.delete_flg = 0 
  AND (g.goods_stock = 0 OR g.goods_stock <= COALESCE(g.min_stock_level, 10));

-- 完了メッセージ
SELECT 
  '移行完了' AS status,
  COUNT(*) AS migrated_products,
  SUM(goods_stock) AS total_stock
FROM t_goods
WHERE delete_flg = 0;

SELECT 
  '在庫アラート生成完了' AS status,
  COUNT(*) AS alert_count
FROM t_stock_alerts
WHERE is_resolved = 0;
