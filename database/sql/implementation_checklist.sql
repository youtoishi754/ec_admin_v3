-- ===================================
-- 実装完了チェックリスト
-- 作成日: 2025-12-07
-- ===================================

-- ✅ 実装済み項目

-- 1. データベース修正
--    ✅ update_goods_un_id.sql 作成（既存商品にUUID設定）

-- 2. プログラム修正
--    ✅ GoodsAddDoController: UUID生成、カテゴリID、画像パス保存追加
--    ✅ GoodsEditDoController: カテゴリID、画像処理追加
--    ✅ DBManager: 商品一覧にun_id明示的に含める
--    ✅ DBManager: getUnIdByGoodsNumber()追加
--    ✅ DBManager: getGoodsByNumber()追加

-- 3. ビュー確認
--    ✅ index.blade.php: すでに$value->un_id使用中

-- ===================================
-- 実行手順
-- ===================================

-- ステップ1: 既存商品にUUIDを設定
-- 以下のSQLを実行してください:
SOURCE f:/windows11_user/xampp/htdocs/ec_admin/database/sql/update_goods_un_id.sql;

-- または直接実行:
UPDATE t_goods 
SET un_id = UUID() 
WHERE un_id IS NULL OR un_id = '';

-- ステップ2: 確認
SELECT 
  id,
  un_id,
  goods_number,
  goods_name,
  LENGTH(un_id) as un_id_length
FROM t_goods 
ORDER BY id
LIMIT 10;

-- ステップ3: 重複チェック
SELECT 
  un_id, 
  COUNT(*) as count 
FROM t_goods 
GROUP BY un_id 
HAVING COUNT(*) > 1;

-- ステップ4: 統計確認
SELECT 
  COUNT(*) as total_goods,
  SUM(CASE WHEN un_id IS NOT NULL AND un_id != '' THEN 1 ELSE 0 END) as with_un_id,
  SUM(CASE WHEN un_id IS NULL OR un_id = '' THEN 1 ELSE 0 END) as without_un_id,
  SUM(CASE WHEN LENGTH(un_id) = 36 THEN 1 ELSE 0 END) as valid_uuid_format
FROM t_goods;

-- ===================================
-- 動作確認
-- ===================================

-- テスト1: 新規商品登録
-- 1. 商品登録画面で商品を追加
-- 2. un_idが36文字のUUIDで登録されているか確認
SELECT un_id, goods_number, goods_name, LENGTH(un_id) as uuid_length
FROM t_goods 
ORDER BY ins_date DESC 
LIMIT 1;

-- テスト2: 商品詳細ページ
-- 1. 商品一覧から詳細ボタンをクリック
-- 2. URLにun_id（UUID）が含まれているか確認
-- 例: /goods/detail?un_id=a7b3c9d2-e4f5-6789-abcd-ef0123456789

-- テスト3: 商品編集ページ
-- 1. 商品一覧から編集ボタンをクリック
-- 2. カテゴリが正しく選択されているか確認
-- 3. 画像アップロード・削除が動作するか確認

-- テスト4: 商品番号からun_id取得
-- PHPコンソールまたはtinkerで実行:
-- php artisan tinker
-- getUnIdByGoodsNumber('A01_000001');

-- ===================================
-- 注意事項
-- ===================================

-- ⚠️ 本番環境では必ずバックアップを取ってから実行してください
-- ⚠️ UUIDは一度設定したら変更しないでください
-- ⚠️ un_idカラムにUNIQUE制約があることを確認してください

-- un_idにUNIQUE制約を追加（まだの場合）
-- ALTER TABLE t_goods ADD UNIQUE KEY idx_un_id_unique (un_id);

-- ===================================
-- トラブルシューティング
-- ===================================

-- 問題1: un_idがNULLのままの商品がある
-- 対処: UPDATE文を再実行
UPDATE t_goods 
SET un_id = UUID() 
WHERE un_id IS NULL OR un_id = '';

-- 問題2: un_idが短い（UUID形式でない）
-- 対処: 古い形式のun_idを上書き
UPDATE t_goods 
SET un_id = UUID() 
WHERE LENGTH(un_id) < 36;

-- 問題3: 詳細ページにアクセスできない
-- 対処: ルートとコントローラーでun_idパラメータを確認
-- routes/web.php:
-- Route::get('/goods/detail', [DetailController::class, '__invoke'])->name('goods_detail');

-- 問題4: 商品番号からカテゴリが選択されない
-- 対処: GoodsEditControllerでカテゴリコード抽出処理を確認
-- 商品番号形式: カテゴリコード_連番（例: A01_000001）

-- ===================================
-- 完了確認
-- ===================================

-- □ すべての商品にUUIDが設定されている
-- □ 新規商品登録時にUUIDが自動生成される
-- □ 商品詳細ページがun_idで表示される
-- □ 商品編集ページがun_idで表示される
-- □ 商品削除ページがun_idで表示される
-- □ カテゴリが正しく選択される
-- □ 画像アップロードが動作する
