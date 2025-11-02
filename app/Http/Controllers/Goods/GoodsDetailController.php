<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use App\Http\Requests\Goods\GoodsDetailRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class GoodsDetailController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsDetailRequest $request)
    {
        //商品情報を取得
        $goods_data = getGoods($request->un_id);

        if(!$goods_data)
        {
            return redirect('/');    
        }
        
        return view('goods.detail',[
            'goods_data' => $goods_data
        ]);
    }
}
