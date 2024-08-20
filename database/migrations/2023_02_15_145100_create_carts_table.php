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
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id')->nullable();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('order_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->unsignedDecimal('price', 18, 2)->nullable();
            $table->integer('quantity');
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
        Schema::dropIfExists('carts');
    }
};
