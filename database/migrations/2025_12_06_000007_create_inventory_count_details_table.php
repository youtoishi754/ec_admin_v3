<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateInventoryCountDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_inventory_count_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inventory_count_id')->comment('棚卸ID');
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->unsignedBigInteger('location_id')->nullable()->comment('ロケーションID');
            $table->string('lot_number', 50)->nullable()->comment('ロット番号');
            $table->integer('system_quantity')->comment('システム在庫数');
            $table->integer('counted_quantity')->nullable()->comment('実地棚卸数');
            // difference は GENERATED COLUMN として後で追加
            $table->text('adjustment_reason')->nullable()->comment('調整理由');
            $table->boolean('is_adjusted')->default(0)->comment('調整済みフラグ');
            $table->dateTime('counted_at')->nullable()->comment('棚卸実施日時');
            $table->timestamps();

            // インデックス
            $table->index('inventory_count_id');
            $table->index('goods_id');
            $table->index('is_adjusted');

            // 外部キー
            $table->foreign('inventory_count_id')
                  ->references('id')
                  ->on('t_inventory_counts')
                  ->onDelete('cascade');
            $table->foreign('goods_id')
                  ->references('id')
                  ->on('t_goods')
                  ->onDelete('cascade');
            $table->foreign('location_id')
                  ->references('id')
                  ->on('m_locations')
                  ->onDelete('set null');
        });

        // GENERATED COLUMN を追加（生SQLで実行）
        DB::statement('ALTER TABLE t_inventory_count_details ADD COLUMN difference INT GENERATED ALWAYS AS (counted_quantity - system_quantity) STORED COMMENT \'差異\'');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_inventory_count_details');
    }
}
