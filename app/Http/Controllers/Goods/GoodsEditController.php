<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use App\Http\Requests\Goods\GoodsEditRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsEditController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsEditRequest $request)
    {
        //初回アクセスではないとき
        if($request->first_flg)
        {
            $goods_data = (object) array();

            $goods_data->goods_number = $request->goods_number;
            $goods_data->goods_name = $request->goods_name;
            $goods_data->goods_price = $request->goods_price;
            $goods_data->goods_stock = $request->goods_stock;
            $goods_data->intro_txt = $request->intro_txt;
            $goods_data->disp_flg = $request->disp_flg; 
        }
        else
        {
            $goods_data = getGoods($request->un_id);

            if(!$goods_data)
            {
                return redirect('/');    
            }
        }
        
        return view('goods.edit',[
            'goods_data' => $goods_data,
        ]);
    }
}
