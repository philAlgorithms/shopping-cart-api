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
        Schema::create('home_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('logistics_personnel_id')
                ->nullable()
                ->constrained('logistics_personnels')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedDecimal('cost', 18, 2);
            $table->string('origin_address');
            // Destination address should be associated with order shipping address.
            $table->timestamp('left_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('received_at')->nullable();
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
        Schema::dropIfExists('home_deliveries');
    }
};
