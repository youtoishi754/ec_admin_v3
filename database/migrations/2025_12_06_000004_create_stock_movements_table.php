<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_stock_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->unsignedBigInteger('warehouse_id')->comment('倉庫ID');
            $table->unsignedBigInteger('location_id')->nullable()->comment('ロケーションID');
            $table->string('lot_number', 50)->nullable()->comment('ロット番号');
            $table->string('serial_number', 100)->nullable()->comment('シリアル番号');
            $table->enum('movement_type', ['in', 'out', 'adjust', 'transfer', 'return', 'reserve', 'release'])
                  ->comment('入出庫区分');
            $table->integer('quantity')->comment('数量(±)');
            $table->integer('before_quantity')->comment('変更前在庫数');
            $table->integer('after_quantity')->comment('変更後在庫数');
            $table->string('reference_type', 50)->nullable()->comment('参照元');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('参照元ID');
            $table->text('notes')->nullable()->comment('備考');
            $table->unsignedBigInteger('user_id')->nullable()->comment('処理者');
            $table->dateTime('movement_date')->comment('入出庫日時');
            $table->timestamps();

            // インデックス
            $table->index('goods_id');
            $table->index('warehouse_id');
            $table->index('movement_type');
            $table->index('movement_date');
            $table->index(['reference_type', 'reference_id']);
            $table->index('user_id');

            // 外部キー
            $table->foreign('goods_id')
                  ->references('id')
                  ->on('t_goods')
                  ->onDelete('cascade');
            $table->foreign('warehouse_id')
                  ->references('id')
                  ->on('m_warehouses')
                  ->onDelete('restrict');
            $table->foreign('location_id')
                  ->references('id')
                  ->on('m_locations')
                  ->onDelete('set null');
            $table->foreign('user_id')
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
        Schema::dropIfExists('t_stock_movements');
    }
}
