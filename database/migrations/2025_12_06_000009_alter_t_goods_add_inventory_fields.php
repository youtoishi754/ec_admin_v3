<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTGoodsAddInventoryFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_goods', function (Blueprint $table) {
            $table->integer('min_stock_level')->nullable()->comment('最低在庫数')->after('goods_stock');
            $table->integer('max_stock_level')->nullable()->comment('最大在庫数')->after('min_stock_level');
            $table->integer('reorder_point')->nullable()->comment('発注点')->after('max_stock_level');
            $table->integer('lead_time_days')->nullable()->comment('リードタイム日数')->after('reorder_point');
            $table->boolean('is_lot_managed')->default(0)->comment('ロット管理フラグ')->after('lead_time_days');
            $table->boolean('is_serial_managed')->default(0)->comment('シリアル管理フラグ')->after('is_lot_managed');
            $table->integer('expiry_alert_days')->nullable()->comment('有効期限アラート日数')->after('is_serial_managed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_goods', function (Blueprint $table) {
            $table->dropColumn([
                'min_stock_level',
                'max_stock_level',
                'reorder_point',
                'lead_time_days',
                'is_lot_managed',
                'is_serial_managed',
                'expiry_alert_days'
            ]);
        });
    }
}
