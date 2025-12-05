<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_lots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lot_number', 50)->unique()->comment('ロット番号');
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->date('manufacturing_date')->nullable()->comment('製造日');
            $table->date('expiry_date')->nullable()->comment('有効期限');
            $table->date('received_date')->comment('入荷日');
            $table->integer('quantity_received')->comment('入荷数量');
            $table->integer('quantity_remaining')->comment('残数量');
            $table->string('supplier_name', 100)->nullable()->comment('仕入先名');
            $table->text('notes')->nullable()->comment('備考');
            $table->boolean('is_active')->default(1)->comment('有効フラグ');
            $table->timestamps();

            // インデックス
            $table->index('lot_number');
            $table->index('goods_id');
            $table->index('expiry_date');
            $table->index('is_active');

            // 外部キー
            $table->foreign('goods_id')
                  ->references('id')
                  ->on('t_goods')
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
        Schema::dropIfExists('m_lots');
    }
}
