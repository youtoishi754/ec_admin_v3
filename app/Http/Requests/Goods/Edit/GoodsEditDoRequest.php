<?php

namespace App\Http\Requests\Goods\Edit;

use Illuminate\Foundation\Http\FormRequest;

class GoodsEditDoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'goods_number'   => 'required|max:30',
            'goods_name'     => 'required|string|max:100',
            'goods_price'    => 'required|integer',
            'goods_stock'    => 'required|integer',
            'intro_txt'      => 'required|string|max:1000',
            'disp_flg'       => 'required|boolean'
        ];
    }

    public function messages() {
        return [
            'goods_number.max' => '商品番号は30文字以下で入力してください',
            'goods_number.required'=> '商品番号は必須項目なので入力してください',
            'goods_name.required'=> '商品名は必須項目なので入力してください',
            'goods_name.string'    => '商品名は文字列を入力してください',
            'goods_name.max'  => '商品名は100文字以下で入力してください',
            'goods_price.required' => '金額は必須項目なので入力してください',
            'goods_price.integer' => '金額は数値を入力してください',
            'goods_stock.required' => '個数は必須項目なので入力してください',
            'goods_stock.integer' => '個数は数値を入力してください',
            'intro_txt.required' => '紹介文は必須項目なので入力してください',
            'intro_txt.string' => '紹介文は文字列なので入力してください',
            'intro_txt.max' => '紹介文は1000文字以下で入力してください',
            'disp_flg.required' => '表示は必須項目なので入力してください',
            'disp_flg.boolean' => '表示はどちらかをチェックしてください'
        ];
    }
}
