<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class GoodsGenerateNumberController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(Request $request)
    {
        $categoryCode = $request->input('category_code');
        
        if (!$categoryCode) {
            return response()->json([
                'success' => false,
                'message' => 'カテゴリコードが指定されていません'
            ], 400);
        }
        
        try {
            // 商品番号を生成
            $goodsNumber = generateGoodsNumber($categoryCode);
            
            return response()->json([
                'success' => true,
                'goods_number' => $goodsNumber,
                'category_code' => $categoryCode
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '商品番号の生成に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }
}
