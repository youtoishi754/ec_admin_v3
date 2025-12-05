<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->unsignedBigInteger('warehouse_id')->comment('倉庫ID');
            $table->unsignedBigInteger('location_id')->nullable()->comment('ロケーションID');
            $table->string('lot_number', 50)->nullable()->comment('ロット番号');
            $table->string('serial_number', 100)->nullable()->comment('シリアル番号');
            $table->integer('quantity')->default(0)->comment('在庫数');
            $table->integer('reserved_quantity')->default(0)->comment('引当済み数量');
            // available_quantity は GENERATED COLUMN として後で追加
            $table->date('expiry_date')->nullable()->comment('有効期限');
            $table->date('manufacturing_date')->nullable()->comment('製造日');
            $table->date('received_date')->nullable()->comment('入荷日');
            $table->integer('alert_threshold')->nullable()->comment('アラート閾値');
            $table->enum('status', ['normal', 'low_stock', 'out_of_stock', 'excess', 'expired'])
                  ->default('normal')->comment('ステータス');
            $table->timestamps();

            // インデックス
            $table->index('goods_id');
            $table->index('warehouse_id');
            $table->index('location_id');
            $table->index('lot_number');
            $table->index('serial_number');
            $table->index('expiry_date');
            $table->index('status');
            $table->unique(['goods_id', 'warehouse_id', 'location_id', 'lot_number', 'serial_number'], 'unique_inventory');

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
        });

        // GENERATED COLUMN を追加（生SQLで実行）
        DB::statement('ALTER TABLE t_inventories ADD COLUMN available_quantity INT GENERATED ALWAYS AS (quantity - reserved_quantity) STORED COMMENT \'利用可能在庫数\'');
        DB::statement('ALTER TABLE t_inventories ADD INDEX idx_available (available_quantity)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_inventories');
    }
}
