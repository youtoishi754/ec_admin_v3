<?php

namespace App\Http\Controllers\Goods\Edit;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Goods\Edit\GoodsEditViewRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsEditViewController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsEditViewRequest $request)
    {
        return view('goods.edit.view');
    }
}
