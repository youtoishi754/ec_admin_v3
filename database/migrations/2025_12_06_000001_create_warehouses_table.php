<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_warehouses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('warehouse_code', 20)->unique()->comment('倉庫コード');
            $table->string('warehouse_name', 100)->comment('倉庫名');
            $table->string('postal_code', 8)->nullable()->comment('郵便番号');
            $table->unsignedBigInteger('prefecture_id')->nullable()->comment('都道府県ID');
            $table->string('city', 100)->nullable()->comment('市区町村');
            $table->string('address_line', 255)->nullable()->comment('番地・建物');
            $table->string('manager_name', 100)->nullable()->comment('管理者名');
            $table->string('phone', 20)->nullable()->comment('電話番号');
            $table->boolean('is_active')->default(1)->comment('有効フラグ');
            $table->timestamps();

            // インデックス
            $table->index('warehouse_code');
            $table->index('is_active');

            // 外部キー
            $table->foreign('prefecture_id')
                  ->references('id')
                  ->on('m_prefectures')
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
        Schema::dropIfExists('m_warehouses');
    }
}
