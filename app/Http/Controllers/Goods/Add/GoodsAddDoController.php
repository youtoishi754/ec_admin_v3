<?php

namespace App\Http\Controllers\Goods\Add;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\Goods\Add\GoodsAddDoRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsAddDoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsAddDoRequest $request)
    { 
        //二十送信対策
        $request->session()->regenerateToken();
        
        //新規商品情報追加
        // UUID（ハッシュ値）を生成
        $unid = (string) Str::uuid();
        
        // 画像アップロード処理
        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $goodsNumber = $request->goods_number;
            
            // 商品番号ごとのディレクトリ作成
            $directory = public_path('images/products/' . $goodsNumber);
            
            Log::info('画像保存ディレクトリ: ' . $directory);
            
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
                Log::info('ディレクトリを作成しました: ' . $directory);
            }
            
            // ファイル名生成（main.jpg）
            $extension = $file->getClientOriginalExtension();
            $filename = 'main.' . $extension;
            
            try {
                $file->move($directory, $filename);
                Log::info('画像ファイル保存成功: ' . $directory . '/' . $filename);
            } catch (\Exception $e) {
                Log::error('画像ファイル保存失敗: ' . $e->getMessage());
            }
            
            // DB保存用パス（publicからのパス）
            $imagePath = 'public/images/products/' . $goodsNumber . '/' . $filename;
        } else {
            Log::info('画像ファイルがアップロードされていません');
        }

        DB::table('t_goods')->insert([
            'un_id'         => $unid,
            'goods_number'  => $request->goods_number,
            'goods_name'    => $request->goods_name,
            'goods_price'   => $request->goods_price,
            'goods_stock'   => $request->goods_stock,
            'category_id'   => $request->category_id,
            'image_path'    => $imagePath,
            'intro_txt'     => $request->intro_txt,
            'disp_flg'      => $request->disp_flg,
            'up_date'       => now(), 
            'ins_date'      => now()
        ]);
         
        //商品追加ログ
        Log::channel('t_goods')->info(
            'page = goods_add_do'.
            ' ユニークID = '.$unid.
            ' 商品番号 = '.$request->goods_number.
            ' 商品名 = '.$request->goods_name.
            ' 金額 = '.$request->goods_price.
            ' 個数 = '.$request->goods_stock.
            ' カテゴリID = '.$request->category_id.
            ' 画像パス = '.$imagePath.
            ' 紹介文 = '.$request->intro_txt.
            ' 表示 = '.$request->disp_flg.
            ' 更新日時 = '.now().
            ' 追加日時 = '.now()
        );

        // 登録完了後、詳細ページへリダイレクト
        return redirect()->route('goods_detail', ['un_id' => $unid])
            ->with('success', '商品を登録しました');
    }
}
