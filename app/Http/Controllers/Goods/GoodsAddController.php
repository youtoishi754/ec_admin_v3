<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Goods\GoodsAddRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsAddController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsAddRequest $request)
    {
        // カテゴリ一覧を取得（階層構造）
        $categories = getCategoriesHierarchy();
        
        return view('goods.add', [
            'categories' => $categories
        ]);
    }
}
