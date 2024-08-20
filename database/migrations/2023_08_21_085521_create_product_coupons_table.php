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
        Schema::create('product_coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->foreignId('coupon_id')
                  ->constrained('coupons')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
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
        Schema::dropIfExists('product_coupons');
    }
};
