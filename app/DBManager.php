<?php

/*******************************************
 * 商品情報を一覧を取得する
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

    $data = DB::table('t_goods')
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
 * 商品情報を一件取得する
 *******************************************/
function getGoods($un_id)
{
    if($un_id != "")
    {
        $data = DB::table('t_goods')
            ->where('un_id','=',$un_id)
            ->where('delete_flg','=', 0)
            ->first();
    }

    return $data;
}
