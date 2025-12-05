<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_stock_alerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->unsignedBigInteger('warehouse_id')->comment('倉庫ID');
            $table->enum('alert_type', ['low_stock', 'out_of_stock', 'excess', 'expiry_warning', 'expiry_critical'])
                  ->comment('アラート種別');
            $table->integer('current_quantity')->comment('現在在庫数');
            $table->integer('threshold_quantity')->nullable()->comment('閾値');
            $table->date('expiry_date')->nullable()->comment('有効期限');
            $table->dateTime('alert_date')->comment('アラート発生日時');
            $table->boolean('is_resolved')->default(0)->comment('解決済みフラグ');
            $table->dateTime('resolved_at')->nullable()->comment('解決日時');
            $table->unsignedBigInteger('resolved_by')->nullable()->comment('解決者');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();

            // インデックス
            $table->index('goods_id');
            $table->index('warehouse_id');
            $table->index('alert_type');
            $table->index('is_resolved');
            $table->index('alert_date');

            // 外部キー
            $table->foreign('goods_id')
                  ->references('id')
                  ->on('t_goods')
                  ->onDelete('cascade');
            $table->foreign('warehouse_id')
                  ->references('id')
                  ->on('m_warehouses')
                  ->onDelete('cascade');
            $table->foreign('resolved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_stock_alerts');
    }
}
