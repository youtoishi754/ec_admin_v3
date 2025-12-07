<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/********** 
 * 商品管理
 **********/
Route::get('/', 'GoodsController')->name('index');                         //一覧
Route::get('/goods/add', 'Goods\GoodsAddController')->name('goods_add');              //新規登録
Route::get('/goods/generate-number', 'Goods\GoodsGenerateNumberController')->name('goods_generate_number');  //商品番号生成API
Route::post('/goods/add/view', 'Goods\Add\GoodsAddViewController')->name('goods_add_view');     //登録確認
Route::post('/goods/add/do', 'Goods\Add\GoodsAddDoController')->name('goods_add_do');         //登録完了
Route::get('/goods/edit', 'Goods\GoodsEditController')->name('goods_edit');            //編集登録
Route::post('/goods/edit/view', 'Goods\Edit\GoodsEditViewController')->name('goods_edit_view');   //編集確認
Route::post('/goods/edit/do', 'Goods\Edit\GoodsEditDoController')->name('goods_edit_do');       //編集完了
Route::get('/goods/detail', 'Goods\GoodsDetailController')->name('goods_detail');        //詳細
Route::get('/goods/delete', 'Goods\GoodsDeleteController')->name('goods_delete');        //削除確認
Route::post('/goods/delete/do', 'Goods\Delete\GoodsDeleteDoController')->name('goods_delete_do');   //削除
Route::get('/goods/export/csv', 'Goods\Export\GoodsExportCsvController')->name('goods_export_csv');      //CSV出力
Route::get('/goods/export/detailed-csv', 'Goods\Export\GoodsExportDetailedCsvController')->name('goods_export_detailed_csv'); //在庫詳細CSV出力
Route::get('/goods/export/excel', 'Goods\Export\GoodsExportExcelController')->name('goods_export_excel');    //Excel出力
Route::get('/goods/export/detailed-excel', 'Goods\Export\GoodsExportDetailedExcelController')->name('goods_export_detailed_excel'); //在庫詳細Excel出力
Route::get('/goods/export/pdf', 'Goods\Export\GoodsExportPdfController')->name('goods_export_pdf');      //PDF出力
Route::get('/goods/export/detailed-pdf', 'Goods\Export\GoodsExportDetailedPdfController')->name('goods_export_detailed_pdf');   //在庫詳細PDF出力

