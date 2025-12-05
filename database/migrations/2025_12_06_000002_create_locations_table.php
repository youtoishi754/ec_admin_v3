<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('warehouse_id')->comment('倉庫ID');
            $table->string('location_code', 30)->comment('ロケーションコード');
            $table->string('aisle', 10)->nullable()->comment('通路番号');
            $table->string('rack', 10)->nullable()->comment('棚番号');
            $table->string('shelf', 10)->nullable()->comment('段番号');
            $table->integer('capacity')->nullable()->comment('収容可能数');
            $table->boolean('is_active')->default(1)->comment('有効フラグ');
            $table->timestamps();

            // インデックス
            $table->unique(['warehouse_id', 'location_code'], 'unique_location');
            $table->index('warehouse_id');
            $table->index('location_code');
            $table->index('is_active');

            // 外部キー
            $table->foreign('warehouse_id')
                  ->references('id')
                  ->on('m_warehouses')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_locations');
    }
}
