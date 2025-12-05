<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_inventory_counts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('count_number', 30)->unique()->comment('棚卸番号');
            $table->unsignedBigInteger('warehouse_id')->comment('倉庫ID');
            $table->date('count_date')->comment('棚卸日');
            $table->enum('status', ['planning', 'in_progress', 'completed', 'cancelled'])
                  ->default('planning')->comment('ステータス');
            $table->unsignedBigInteger('user_id')->nullable()->comment('実施者');
            $table->integer('total_items')->nullable()->comment('対象商品数');
            $table->integer('checked_items')->default(0)->comment('チェック済み数');
            $table->text('notes')->nullable()->comment('備考');
            $table->dateTime('started_at')->nullable()->comment('開始日時');
            $table->dateTime('completed_at')->nullable()->comment('完了日時');
            $table->timestamps();

            // インデックス
            $table->index('count_number');
            $table->index('warehouse_id');
            $table->index('count_date');
            $table->index('status');
            $table->index('user_id');

            // 外部キー
            $table->foreign('warehouse_id')
                  ->references('id')
                  ->on('m_warehouses')
                  ->onDelete('restrict');
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
        Schema::dropIfExists('t_inventory_counts');
    }
}
