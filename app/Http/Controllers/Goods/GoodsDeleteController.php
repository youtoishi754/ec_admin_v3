<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use App\Http\Requests\Goods\GoodsDeleteRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsDeleteController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsDeleteRequest $request)
    {
        //商品情報を取得
        $goods_data = getGoods($request->un_id);

        if(!$goods_data)
        {
            return redirect('/');    
        }

        return view('goods.delete',[
            'goods_data'=> $goods_data
        ]);
    }
}
