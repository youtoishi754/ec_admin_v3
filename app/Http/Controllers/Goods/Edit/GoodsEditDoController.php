<?php

namespace App\Http\Controllers\Goods\Edit;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Goods\Edit\GoodsEditDoRequest;
use Illuminate\Routing\Controller as BaseController;

class GoodsEditDoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(GoodsEditDoRequest $request)
    {
        //二十送信対策
        $request->session()->regenerateToken();
        
        // 画像処理
        $imagePath = null;
        $updateData = [
            'goods_number'  => $request->goods_number,
            'goods_name'    => $request->goods_name,
            'goods_price'   => $request->goods_price,
            'goods_stock'   => $request->goods_stock,
            'category_id'   => $request->category_id,
            'intro_txt'     => $request->intro_txt,
            'disp_flg'      => $request->disp_flg,
            'up_date'       => now() 
        ];
        
        // 画像削除チェック
        if ($request->has('delete_image') && $request->delete_image == '1') {
            // 現在の画像を削除
            $goods = DB::table('t_goods')->where('un_id', $request->un_id)->first();
            if ($goods && $goods->image_path) {
                $oldImagePath = public_path($goods->image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $updateData['image_path'] = null;
        }
        
        // 新しい画像をアップロード
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
            $updateData['image_path'] = $imagePath;
        }
        
        //編集した商品情報を更新
        DB::table('t_goods')->where('un_id',$request->un_id)
        ->update($updateData);
        
        //商品編集情報をロギング    
        Log::channel('t_goods')->info(
            'page = goods_edit_do'.
            ' ユニークID = '.$request->un_id.
            ' 商品番号 = '.$request->goods_number.
            ' 商品名 = '.$request->goods_name.
            ' 金額 = '.$request->goods_price.
            ' 個数 = '.$request->goods_stock.
            ' カテゴリID = '.$request->category_id.
            ' 紹介文 = '.$request->intro_txt.
            ' 表示 = '.$request->disp_flg.
            ' 更新日時 = '.now()
        );

        // 更新完了後、詳細ページへリダイレクト
        return redirect()->route('goods_detail', ['un_id' => $request->un_id])
            ->with('success', '商品情報を更新しました');
    }
}
