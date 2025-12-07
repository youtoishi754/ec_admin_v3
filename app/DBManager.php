<?php

/*******************************************
 * 商品情報を一覧を取得する（在庫管理対応版）
 *******************************************/
function getGoodsList($search_options = null)
{
    $params = array();

    if($search_options != null)
    {
        $sql = " delete_flg = 0 ";

        //商品番号
        if(array_key_exists('goods_number',$search_options) && $search_options['goods_number'] != "" )
        {
            $sql .= "AND goods_number LIKE ? ";
            $params[] = "%".$search_options['goods_number']."%"; 
        }

        //商品名
        if(array_key_exists('goods_id',$search_options) && $search_options['goods_id'] != "" )
        {
            $sql .= "AND id = ? ";
            $params[] = $search_options['goods_id'];
        }

        //金額(以下)
        if(array_key_exists('min_price',$search_options) && $search_options['min_price'] != "" )
        {
            $sql .= "AND goods_price >= ? ";
            $params[] = $search_options['min_price'];
        }

        //金額(以上)
        if(array_key_exists('max_price',$search_options) && $search_options['max_price'] != "" )
        {
            $sql .= "AND goods_price <= ? ";
            $params[] = $search_options['max_price'];
        }

        //在庫数(最小)
        if(array_key_exists('min_stock',$search_options) && $search_options['min_stock'] != "" )
        {
            $sql .= "AND goods_stock >= ? ";
            $params[] = $search_options['min_stock'];
        }

        //在庫数(最大)
        if(array_key_exists('max_stock',$search_options) && $search_options['max_stock'] != "" )
        {
            $sql .= "AND goods_stock <= ? ";
            $params[] = $search_options['max_stock'];
        }

        //在庫ステータス
        if(array_key_exists('stock_status',$search_options) && $search_options['stock_status'] != "" )
        {
            switch($search_options['stock_status']) {
                case 'out_of_stock':
                    $sql .= "AND goods_stock = 0 ";
                    break;
                case 'low_stock':
                    $sql .= "AND goods_stock > 0 AND goods_stock <= min_stock_level ";
                    break;
                case 'normal':
                    $sql .= "AND goods_stock > min_stock_level ";
                    break;
            }
        }

        //更新日時(開始)
        if(array_key_exists('s_up_date',$search_options) && $search_options['s_up_date'] != "" )
        {
            $sql .= "AND Date(up_date) >= ? ";
            $params[] = $search_options['s_up_date'];
        }
        
        //更新日時(終了)
        if(array_key_exists('e_up_date',$search_options) && $search_options['e_up_date'] != "" )
        {
            $sql .= "AND Date(up_date) <= ? ";
            $params[] = $search_options['e_up_date'];
        }

        //追加日時(開始)
        if(array_key_exists('s_ins_date',$search_options) && $search_options['s_ins_date'] != "" )
        {
            $sql .= "AND Date(ins_date) >= ? ";
            $params[] = $search_options['s_ins_date'];
        }

        //追加日時(終了)
        if(array_key_exists('e_ins_date',$search_options) && $search_options['e_ins_date'] != "" )
        {
            $sql .= "AND Date(ins_date) <= ? ";
            $params[] = $search_options['e_ins_date'];
        }
    }

    // 在庫情報を結合して取得
    $data = DB::table('t_goods')
    ->leftJoin(DB::raw('(SELECT goods_id, SUM(quantity) as total_quantity, SUM(reserved_quantity) as total_reserved, SUM(available_quantity) as total_available FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
    ->select(
        't_goods.id',
        't_goods.un_id',
        't_goods.goods_number',
        't_goods.goods_name',
        't_goods.goods_price',
        't_goods.goods_stock',
        't_goods.category_id',
        't_goods.intro_txt',
        't_goods.image_path',
        't_goods.disp_flg',
        't_goods.min_stock_level',
        't_goods.max_stock_level',
        't_goods.reorder_point',
        't_goods.up_date',
        't_goods.ins_date',
        DB::raw('COALESCE(inv.total_quantity, t_goods.goods_stock) as total_inventory'),
        DB::raw('COALESCE(inv.total_reserved, 0) as total_reserved'),
        DB::raw('COALESCE(inv.total_available, t_goods.goods_stock) as total_available')
    )
    ->whereraw($sql,$params);
    
    // ソートオプションの適用
    if(array_key_exists('sort_by', $search_options) && array_key_exists('sort_direction', $search_options)) {
        $sortColumn = '';
        
        // ソート対象のカラムマッピング
        switch($search_options['sort_by']) {
            case 'price':
                $sortColumn = 'goods_price';
                break;
            case 'stock':
                $sortColumn = 'goods_stock';
                break;
            case 'total_inventory':
                $sortColumn = 'total_inventory';
                break;
            case 'total_available':
                $sortColumn = 'total_available';
                break;
            case 'update':
                $sortColumn = 'up_date';
                break;
            case 'insert':
                $sortColumn = 'ins_date';
                break;
            default:
                $sortColumn = 'id'; // デフォルトソート
        }
        
        $sortDirection = $search_options['sort_direction'] === 'desc' ? 'desc' : 'asc';
        $data = $data->orderBy($sortColumn, $sortDirection);
    } else {
        // デフォルトのソート順（IDの昇順）
        $data = $data->orderBy('id', 'asc');
    }
    
    $data = $data->paginate(10);
    
    return $data;
}

/*******************************************
 * 商品情報を一件取得する（在庫情報付き）
 *******************************************/
function getGoods($un_id)
{
    if($un_id != "")
    {
        $data = DB::table('t_goods')
            ->leftJoin(DB::raw('(SELECT goods_id, SUM(quantity) as total_quantity, SUM(reserved_quantity) as total_reserved, SUM(available_quantity) as total_available FROM t_inventories GROUP BY goods_id) as inv'), 't_goods.id', '=', 'inv.goods_id')
            ->select(
                't_goods.*',
                DB::raw('COALESCE(inv.total_quantity, t_goods.goods_stock) as total_inventory'),
                DB::raw('COALESCE(inv.total_reserved, 0) as total_reserved'),
                DB::raw('COALESCE(inv.total_available, t_goods.goods_stock) as total_available')
            )
            ->where('un_id','=',$un_id)
            ->where('delete_flg','=', 0)
            ->first();
    }

    return $data;
}

/*******************************************
 * 在庫一覧を取得する（商品ID指定）
 *******************************************/
function getInventoriesByGoodsId($goods_id)
{
    $data = DB::table('t_inventories as inv')
        ->leftJoin('m_warehouses as wh', 'inv.warehouse_id', '=', 'wh.id')
        ->leftJoin('m_locations as loc', 'inv.location_id', '=', 'loc.id')
        ->select(
            'inv.*',
            'wh.warehouse_name',
            'wh.warehouse_code',
            'loc.location_code',
            'loc.aisle',
            'loc.rack',
            'loc.shelf'
        )
        ->where('inv.goods_id', '=', $goods_id)
        ->get();

    return $data;
}

/*******************************************
 * 在庫アラート一覧を取得する
 *******************************************/
function getStockAlerts($options = null)
{
    $query = DB::table('t_stock_alerts as alert')
        ->leftJoin('t_goods as g', 'alert.goods_id', '=', 'g.id')
        ->leftJoin('m_warehouses as wh', 'alert.warehouse_id', '=', 'wh.id')
        ->select(
            'alert.*',
            'g.goods_number',
            'g.goods_name',
            'g.image_path',
            'wh.warehouse_name'
        )
        ->where('alert.is_resolved', 0);

    // アラート種別フィルター
    if($options && array_key_exists('alert_type', $options) && $options['alert_type'] != "") {
        $query->where('alert.alert_type', '=', $options['alert_type']);
    }

    // 倉庫フィルター
    if($options && array_key_exists('warehouse_id', $options) && $options['warehouse_id'] != "") {
        $query->where('alert.warehouse_id', '=', $options['warehouse_id']);
    }

    $data = $query->orderBy('alert.alert_date', 'desc')->get();

    return $data;
}

/*******************************************
 * 倉庫一覧を取得する
 *******************************************/
function getWarehouses($active_only = true)
{
    $query = DB::table('m_warehouses');
    
    if($active_only) {
        $query->where('is_active', 1);
    }
    
    $data = $query->orderBy('warehouse_code', 'asc')->get();

    return $data;
}

/*******************************************
 * ロケーション一覧を取得する（倉庫ID指定）
 *******************************************/
function getLocationsByWarehouse($warehouse_id, $active_only = true)
{
    $query = DB::table('m_locations')
        ->where('warehouse_id', '=', $warehouse_id);
    
    if($active_only) {
        $query->where('is_active', 1);
    }
    
    $data = $query->orderBy('location_code', 'asc')->get();

    return $data;
}

/*******************************************
 * 入出庫履歴を取得する（商品ID指定）
 *******************************************/
function getStockMovementsByGoodsId($goods_id, $limit = 20)
{
    $data = DB::table('t_stock_movements as sm')
        ->leftJoin('m_warehouses as wh', 'sm.warehouse_id', '=', 'wh.id')
        ->leftJoin('m_locations as loc', 'sm.location_id', '=', 'loc.id')
        ->leftJoin('users as u', 'sm.user_id', '=', 'u.id')
        ->select(
            'sm.*',
            'wh.warehouse_name',
            'loc.location_code',
            'u.name as user_name'
        )
        ->where('sm.goods_id', '=', $goods_id)
        ->orderBy('sm.movement_date', 'desc')
        ->limit($limit)
        ->get();

    return $data;
}

/*******************************************
 * 在庫ステータスバッジHTMLを取得
 *******************************************/
function getStockStatusBadge($current_stock, $min_stock_level = null)
{
    if($current_stock == 0) {
        return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> 欠品</span>';
    } elseif($min_stock_level && $current_stock <= $min_stock_level) {
        return '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> 低在庫</span>';
    } else {
        return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> 正常</span>';
    }
}

/*******************************************
 * アラート種別バッジHTMLを取得
 *******************************************/
function getAlertTypeBadge($alert_type)
{
    $badges = [
        'low_stock' => '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> 低在庫</span>',
        'out_of_stock' => '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> 欠品</span>',
        'excess' => '<span class="badge badge-info"><i class="fas fa-boxes"></i> 過剰在庫</span>',
        'expiry_warning' => '<span class="badge badge-warning"><i class="fas fa-clock"></i> 期限警告</span>',
        'expiry_critical' => '<span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i> 期限切迫</span>',
    ];

    return $badges[$alert_type] ?? '<span class="badge badge-secondary">' . $alert_type . '</span>';
}

/*******************************************
 * カテゴリ一覧を取得する
 *******************************************/
function getCategories($parent_id = null, $active_only = true)
{
    $query = DB::table('m_categories');
    
    if ($parent_id === null) {
        // 親カテゴリ（大カテゴリ）のみ取得
        $query->whereNull('parent_id');
    } elseif ($parent_id === 'all') {
        // 全カテゴリ取得
    } else {
        // 指定された親カテゴリの子カテゴリを取得
        $query->where('parent_id', $parent_id);
    }
    
    if ($active_only) {
        $query->where('is_active', 1);
    }
    
    $data = $query->orderBy('display_order', 'asc')->get();
    
    return $data;
}

/*******************************************
 * カテゴリ情報を取得する（ID指定）
 *******************************************/
function getCategoryById($id)
{
    $data = DB::table('m_categories')
        ->where('id', $id)
        ->first();
    
    return $data;
}

/*******************************************
 * カテゴリ情報を取得する（コード指定）
 *******************************************/
function getCategoryByCode($category_code)
{
    $data = DB::table('m_categories')
        ->where('category_code', $category_code)
        ->first();
    
    return $data;
}

/*******************************************
 * 商品番号を自動生成する
 * フォーマット: {カテゴリコード}_{6桁の連番}
 * 例: A01_000001, B02_000123
 *******************************************/
function generateGoodsNumber($category_code)
{
    // トランザクション開始
    DB::beginTransaction();
    
    try {
        // シーケンステーブルから現在の番号を取得（行ロック）
        $sequence = DB::table('t_goods_sequence')
            ->where('category_code', $category_code)
            ->lockForUpdate()
            ->first();
        
        if (!$sequence) {
            // シーケンスが存在しない場合は作成
            DB::table('t_goods_sequence')->insert([
                'category_code' => $category_code,
                'last_number' => 1,
                'updated_at' => now()
            ]);
            $next_number = 1;
        } else {
            // 次の番号を計算
            $next_number = $sequence->last_number + 1;
            
            // シーケンスを更新
            DB::table('t_goods_sequence')
                ->where('category_code', $category_code)
                ->update([
                    'last_number' => $next_number,
                    'updated_at' => now()
                ]);
        }
        
        // 商品番号を生成（カテゴリコード_6桁の連番）
        $goods_number = $category_code . '_' . str_pad($next_number, 6, '0', STR_PAD_LEFT);
        
        DB::commit();
        
        return $goods_number;
        
    } catch (\Exception $e) {
        DB::rollback();
        throw $e;
    }
}

/*******************************************
 * 全カテゴリを階層構造で取得する
 *******************************************/
function getCategoriesHierarchy($active_only = true)
{
    // 親カテゴリを取得
    $parents = getCategories(null, $active_only);
    
    $hierarchy = [];
    foreach ($parents as $parent) {
        $parent->children = getCategories($parent->id, $active_only);
        $hierarchy[] = $parent;
    }
    
    return $hierarchy;
}

/*******************************************
 * 商品番号からun_idを取得する
 *******************************************/
function getUnIdByGoodsNumber($goods_number)
{
    $goods = DB::table('t_goods')
        ->where('goods_number', $goods_number)
        ->where('delete_flg', 0)
        ->first();
    
    return $goods ? $goods->un_id : null;
}

/*******************************************
 * 商品番号で商品情報を取得する
 *******************************************/
function getGoodsByNumber($goods_number)
{
    $data = DB::table('t_goods')
        ->where('goods_number', $goods_number)
        ->where('delete_flg', 0)
        ->first();
    
    return $data;
}
