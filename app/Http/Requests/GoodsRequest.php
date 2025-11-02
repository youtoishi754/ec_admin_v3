<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoodsRequest extends FormRequest
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
            'goods_number'   => 'nullable|max:30',
            'goods_id'       => 'nullable|integer',
            'min_price'      => 'nullable|integer|min:0',
            'max_price'      => 'nullable|integer',
            's_up_year'      => 'nullable|numeric',
            'e_up_year'      => 'nullable|numeric',
            's_up_month'     => 'nullable|numeric',
            'e_up_month'     => 'nullable|numeric',
            's_up_day'       => 'nullable|numeric',
            'e_up_day'       => 'nullable|numeric',
            's_ins_year'     => 'nullable|numeric',
            'e_ins_year'     => 'nullable|numeric',
            's_ins_month'    => 'nullable|numeric',
            'e_ins_month'    => 'nullable|numeric',
            's_ins_day'      => 'nullable|numeric',
            'e_ins_day'      => 'nullable|numeric'
        ];
    }

    public function messages() {
        return [
            'goods_number.max' => '30文字以下で入力してください',
            'min_price.integer'=> '金額(以上)に数値を入力してください',
            'max_price.integer'=> '金額(以下)に数値を入力してください',
            'min_price.min'    => '0以上で入力してください'
        ];
    }
}
