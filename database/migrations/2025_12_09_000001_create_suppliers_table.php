<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code', 20)->unique()->comment('仕入先コード');
            $table->string('supplier_name', 100)->comment('仕入先名');
            $table->string('contact_person', 50)->nullable()->comment('担当者名');
            $table->string('contact_email', 100)->nullable()->comment('メールアドレス');
            $table->string('contact_phone', 20)->nullable()->comment('電話番号');
            $table->string('fax', 20)->nullable()->comment('FAX');
            $table->string('postal_code', 10)->nullable()->comment('郵便番号');
            $table->string('address', 255)->nullable()->comment('住所');
            $table->string('payment_terms', 100)->nullable()->comment('支払条件');
            $table->integer('lead_time_days')->nullable()->comment('リードタイム（日数）');
            $table->decimal('minimum_order_amount', 12, 2)->nullable()->comment('最低発注金額');
            $table->text('notes')->nullable()->comment('備考');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_suppliers');
    }
}
