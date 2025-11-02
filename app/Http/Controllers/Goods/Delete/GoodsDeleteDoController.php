<?php

namespace App\Http\Controllers\Goods\Delete;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Goods\Delete\GoodsDeleteRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsDeleteDoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsDeleteRequest $request)
    {
        //二十送信対策
        $request->session()->regenerateToken();

        //商品情報を論理削除
        DB::table('t_goods')->where('un_id',$request->un_id)
        ->update([
            'delete_flg'    => 1,
            'up_date'       => now() 
        ]);
        
        //商品情報をロギング   
        Log::channel('t_goods')->info(
            'page = goods_delete_do'.
            ' ユニークID = '.$request->un_id.
            ' 削除フラグ = 1'.
            ' 更新日時 = '.now()
        );
        
        return view('goods.delete.do');
    }
}
