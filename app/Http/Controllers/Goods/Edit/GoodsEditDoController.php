<?php

namespace App\Http\Controllers\Goods\Edit;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Goods\Edit\GoodsEditDoRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsEditDoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsEditDoRequest $request)
    {
        //二十送信対策
        $request->session()->regenerateToken();
        
        //編集した商品情報を更新
        DB::table('t_goods')->where('un_id',$request->un_id)
        ->update([
            'goods_number'  => $request->goods_number,
            'goods_name'    => $request->goods_name,
            'goods_price'   => $request->goods_price,
            'goods_stock'   => $request->goods_stock,
            'intro_txt'     => $request->intro_txt,
            'disp_flg'      => $request->disp_flg,
            'up_date'       => now() 
        ]);
        
        //商品編集情報をロギング    
        Log::channel('t_goods')->info(
            'page = goods_edit_do'.
            ' ユニークID = '.$request->un_id.
            ' 商品番号 = '.$request->goods_number.
            ' 商品名 = '.$request->goods_name.
            ' 金額 = '.$request->goods_price.
            ' 個数 = '.$request->goods_stock.
            ' 紹介文 = '.$request->intro_txt.
            ' 表示 = '.$request->disp_flg.
            ' 更新日時 = '.now()
        );

        return view('goods.edit.do');
    }
}
