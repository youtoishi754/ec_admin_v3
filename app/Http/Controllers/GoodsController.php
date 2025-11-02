<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use App\Http\Requests\GoodsRequest;
use Illuminate\Routing\Controller as BaseController;

//index.blade.phpに商品情報を表示するためのコントローラー
//商品情報の検索条件を受け取り、商品情報一覧を取得する
//商品情報一覧をビューに渡す
class GoodsController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsRequest $request)
    {
        // 検索オプションの作成
        $search_options = array(
            'goods_number' => $request->goods_number,
            'goods_id'     => $request->goods_id,
            'min_price'    => $request->min_price,
            'max_price'    => $request->max_price
        );

        // ソートオプションの設定
        if ($request->has('sort_by') && $request->has('sort_direction')) {
            $search_options['sort_by'] = $request->sort_by;
            $search_options['sort_direction'] = $request->sort_direction;
        }

        // 日付存在チェック
        if ($request->s_up_year != "" && $request->s_up_month != "" && $request->s_up_month != "")
        {
            $search_options['s_up_date'] = $request->s_up_year . '-' . $request->s_up_month . '-' . $request->s_up_day;
        }
        if ($request->e_up_year != "" && $request->e_up_month != "" && $request->e_up_month != "")
        {
            $search_options['e_up_date'] = $request->e_up_year . '-' . $request->e_up_month . '-' . $request->e_up_day;
        }
        if ($request->s_ins_year != "" && $request->s_ins_month != "" && $request->s_ins_month != "")
        {
            $search_options['s_ins_date'] = $request->s_ins_year . '-' . $request->s_ins_month . '-' . $request->s_ins_day;
        }
        if ($request->e_ins_year != "" && $request->e_ins_month != "" && $request->e_ins_month != "") 
        {
            $search_options['e_ins_date'] = $request->e_ins_year . '-' . $request->e_ins_month . '-' . $request->e_ins_day;
        }

        // 商品情報一覧取得
        $goods_list = getGoodsList($search_options);

        return view('index', [
            'goods_list' => $goods_list
        ]);
    }
}
