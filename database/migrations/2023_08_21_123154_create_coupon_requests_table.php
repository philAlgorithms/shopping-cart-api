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
        // Schema::create('coupon_requests', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->foreignId('buyer_id')
        //         ->constrained('buyers')
        //         ->cascadeOnUpdate()
        //         ->cascadeOnDelete();
        //     $table->foreignId('coupon_id')
        //         ->nullable()
        //         ->unique()
        //         ->constrained('coupons')
        //         ->cascadeOnUpdate()
        //         ->nullOnDelete();
        //     $table->timestamp('approved_at')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_requests');
    }
};
