<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paystack_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('paystack_payment_id')
                ->constrained('paystack_payments')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedDecimal('amount', 18, 2);
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
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
        Schema::dropIfExists('paystack_purchases');
    }
};
