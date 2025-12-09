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

/********** 
 * 在庫管理
 **********/
Route::get('/inventory', 'Inventory\InventoryController')->name('inventory');                    //リアルタイム在庫
Route::get('/inventory/alert', 'Inventory\InventoryAlertController')->name('inventory_alert');            //在庫アラート
Route::post('/inventory/alert/check-now', 'Inventory\InventoryAlertController@checkNow')->name('inventory_alert_check_now'); //アラート今すぐチェック
Route::post('/inventory/alert/{id}/resolve', 'Inventory\InventoryAlertController@resolve')->name('inventory_alert_resolve');  //アラート解決
Route::post('/inventory/alert/bulk-resolve', 'Inventory\InventoryAlertController@bulkResolve')->name('inventory_alert_bulk_resolve'); //アラート一括解決
Route::get('/inventory/location', 'Inventory\LocationController@index')->name('inventory_location');         //ロケーション一覧
Route::get('/inventory/location/create', 'Inventory\LocationController@create')->name('inventory_location_create');  //ロケーション登録
Route::post('/inventory/location/store', 'Inventory\LocationController@store')->name('inventory_location_store');   //ロケーション登録実行
Route::get('/inventory/location/{id}/edit', 'Inventory\LocationController@edit')->name('inventory_location_edit');   //ロケーション編集
Route::post('/inventory/location/{id}/update', 'Inventory\LocationController@update')->name('inventory_location_update'); //ロケーション更新
Route::post('/inventory/location/{id}/delete', 'Inventory\LocationController@destroy')->name('inventory_location_delete'); //ロケーション削除
Route::get('/inventory/lot', 'Inventory\LotSerialController@index')->name('inventory_lot');             //ロット管理
Route::get('/inventory/serial', 'Inventory\LotSerialController@serialIndex')->name('inventory_serial');        //シリアル番号管理
Route::get('/inventory/lot/create', 'Inventory\LotSerialController@create')->name('inventory_lot_create');       //ロット登録
Route::post('/inventory/lot/store', 'Inventory\LotSerialController@store')->name('inventory_lot_store');        //ロット登録実行
Route::get('/inventory/lot/{id}/edit', 'Inventory\LotSerialController@edit')->name('inventory_lot_edit');        //ロット編集
Route::post('/inventory/lot/{id}/update', 'Inventory\LotSerialController@update')->name('inventory_lot_update');     //ロット更新
Route::get('/inventory/expiry', 'Inventory\ExpiryController')->name('inventory_expiry');              //有効期限管理
Route::get('/inventory/stocktaking', 'Inventory\StocktakingController@index')->name('inventory_stocktaking');     //在庫棚卸
Route::get('/inventory/stocktaking/create', 'Inventory\StocktakingController@create')->name('inventory_stocktaking_create'); //棚卸登録
Route::post('/inventory/stocktaking/store', 'Inventory\StocktakingController@store')->name('inventory_stocktaking_store');  //棚卸登録実行
Route::get('/inventory/stocktaking/history', 'Inventory\StocktakingController@history')->name('inventory_stocktaking_history'); //棚卸履歴

/********** 
 * 入出庫管理
 **********/
Route::get('/stock-movement/in', 'StockMovement\\StockInController@index')->name('stock_in');                //入庫登録
Route::post('/stock-movement/in/store', 'StockMovement\\StockInController@store')->name('stock_in_store');         //入庫登録実行
Route::get('/stock-movement/out', 'StockMovement\\StockOutController@index')->name('stock_out');               //出庫登録
Route::post('/stock-movement/out/store', 'StockMovement\\StockOutController@store')->name('stock_out_store');        //出庫登録実行
Route::get('/stock-movement/return', 'StockMovement\\StockReturnController@index')->name('stock_return');          //返品入庫
Route::post('/stock-movement/return/store', 'StockMovement\\StockReturnController@store')->name('stock_return_store');   //返品入庫実行
Route::get('/stock-movement/transfer', 'StockMovement\\StockTransferController@index')->name('stock_transfer');       //移動在庫
Route::post('/stock-movement/transfer/store', 'StockMovement\\StockTransferController@store')->name('stock_transfer_store'); //移動在庫実行
Route::get('/stock-movement/history', 'StockMovement\\StockMovementHistoryController')->name('stock_movement_history');   //入出庫履歴

/********** 
 * 発注管理
 **********/
Route::get('/purchase/suggestion', 'Purchase\\OrderSuggestionController@index')->name('order_suggestion');              //発注提案
Route::post('/purchase/suggestion/create-order', 'Purchase\\OrderSuggestionController@createOrder')->name('order_suggestion_create'); //発注提案から発注書作成
Route::get('/purchase/order', 'Purchase\\PurchaseOrderController@index')->name('purchase_order_list');                 //発注書一覧
Route::get('/purchase/order/create', 'Purchase\\PurchaseOrderController@create')->name('purchase_order_create');            //発注書作成
Route::post('/purchase/order/store', 'Purchase\\PurchaseOrderController@store')->name('purchase_order_store');             //発注書保存
Route::get('/purchase/order/{id}/edit', 'Purchase\\PurchaseOrderController@edit')->name('purchase_order_edit');             //発注書編集
Route::put('/purchase/order/{id}/update', 'Purchase\\PurchaseOrderController@update')->name('purchase_order_update');          //発注書更新
Route::post('/purchase/order/{id}/status', 'Purchase\\PurchaseOrderController@updateStatus')->name('purchase_order_status');       //発注書ステータス更新
Route::delete('/purchase/order/{id}/delete', 'Purchase\\PurchaseOrderController@destroy')->name('purchase_order_delete');        //発注書削除
Route::get('/purchase/tracking', 'Purchase\\OrderTrackingController@index')->name('purchase_tracking');                //発注状況追跡
Route::get('/purchase/tracking/{id}', 'Purchase\\OrderTrackingController@show')->name('purchase_tracking_detail');           //発注詳細
Route::post('/purchase/tracking/{id}/receive', 'Purchase\\OrderTrackingController@receive')->name('purchase_receive');         //入荷処理
Route::get('/purchase/supplier', 'Purchase\\SupplierController@index')->name('supplier_list');                     //仕入先一覧
Route::get('/purchase/supplier/create', 'Purchase\\SupplierController@create')->name('supplier_create');                //仕入先登録
Route::post('/purchase/supplier/store', 'Purchase\\SupplierController@store')->name('supplier_store');                 //仕入先登録実行
Route::get('/purchase/supplier/{id}/edit', 'Purchase\\SupplierController@edit')->name('supplier_edit');                 //仕入先編集
Route::put('/purchase/supplier/{id}/update', 'Purchase\\SupplierController@update')->name('supplier_update');              //仕入先更新
Route::delete('/purchase/supplier/{id}/delete', 'Purchase\\SupplierController@destroy')->name('supplier_delete');            //仕入先削除
Route::get('/purchase/analytics', 'Purchase\\OrderAnalyticsController@index')->name('order_analytics');                //発注実績分析
Route::get('/purchase/analytics/export', 'Purchase\\OrderAnalyticsController@export')->name('order_analytics_export');         //発注実績CSVエクスポート

