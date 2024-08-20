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
        Schema::create('buyer_referrals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('buyer_referral_program_id')
                ->constrained('buyer_referral_programs')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('referee_id')
                ->unique()
                ->constrained('buyers')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('order_id')
                ->nullable()
                ->unique()
                ->constrained('orders')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedDecimal('reward', 18, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buyer_referrals');
    }
};
