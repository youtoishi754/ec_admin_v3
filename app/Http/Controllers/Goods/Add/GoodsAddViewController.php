<?php

namespace App\Http\Controllers\Goods\Add;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Goods\Add\GoodsAddViewRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsAddViewController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsAddViewRequest $request)
    {
        return view('goods.add.view');
    }
}
