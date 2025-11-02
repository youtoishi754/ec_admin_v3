<?php

namespace App\Http\Controllers\Goods\Add;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Goods\Add\GoodsAddDoRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsAddDoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsAddDoRequest $request)
    { 
        //二十送信対策
        $request->session()->regenerateToken();
        
        //新規商品情報追加
        $unid = uniqid(sprintf('%02x', random_int(0, 255)));

        DB::table('t_goods')->insert([
            'un_id'         => $unid,
            'goods_number'  => $request->goods_number,
            'goods_name'    => $request->goods_name,
            'goods_price'   => $request->goods_price,
            'goods_stock'   => $request->goods_stock,
            'intro_txt'     => $request->intro_txt,
            'disp_flg'      => $request->disp_flg,
            'up_date'       => now(), 
            'ins_date'      => now()
        ]);
         
        //商品追加ログ
        Log::channel('t_goods')->info(
            'page = goods_add_do'.
            ' ユニークID = '.$unid.
            ' 商品番号 = '.$request->goods_number.
            ' 商品名 = '.$request->goods_name.
            ' 金額 = '.$request->price.
            ' 個数 = '.$request->goods_stock.
            ' 紹介文 = '.$request->intro_txt.
            ' 表示 = '.$request->disp_flg.
            ' 更新日時 = '.now().
            ' 追加日時 = '.now()
        );

        return view('goods.add.do');
    }
}
