<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique()->comment('発注番号');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('仕入先ID');
            $table->date('order_date')->comment('発注日');
            $table->date('expected_delivery_date')->nullable()->comment('納期予定日');
            $table->date('ordered_date')->nullable()->comment('発注確定日');
            $table->date('received_date')->nullable()->comment('入荷日');
            $table->enum('status', ['draft', 'pending', 'ordered', 'received', 'cancelled'])->default('draft')->comment('ステータス');
            $table->decimal('total_amount', 12, 2)->default(0)->comment('合計金額');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('m_suppliers')->onDelete('set null');
        });

        Schema::create('t_purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id')->comment('発注書ID');
            $table->unsignedBigInteger('goods_id')->comment('商品ID');
            $table->integer('quantity')->comment('発注数量');
            $table->decimal('unit_price', 10, 2)->comment('単価');
            $table->decimal('subtotal', 12, 2)->comment('小計');
            $table->integer('received_quantity')->nullable()->default(0)->comment('入荷済数量');
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('t_purchase_orders')->onDelete('cascade');
            $table->foreign('goods_id')->references('id')->on('t_goods')->onDelete('cascade');
        });

        Schema::create('t_purchase_order_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id')->comment('発注書ID');
            $table->string('action', 50)->comment('アクション');
            $table->string('old_status', 20)->nullable()->comment('変更前ステータス');
            $table->string('new_status', 20)->nullable()->comment('変更後ステータス');
            $table->text('notes')->nullable()->comment('備考');
            $table->unsignedBigInteger('created_by')->nullable()->comment('作成者ID');
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('t_purchase_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_purchase_order_history');
        Schema::dropIfExists('t_purchase_order_details');
        Schema::dropIfExists('t_purchase_orders');
    }
}
