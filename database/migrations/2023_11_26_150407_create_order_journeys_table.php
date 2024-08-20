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
        Schema::create('order_journeys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedDecimal('cost', 18, 2);
            $table->foreignId('journey_id')
                ->nullable()
                ->constrained('journeys')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            // $table->foreignId('town_id')
            //     ->constrained('towns')
            //     ->restrictOnDelete()
            //     ->cascadeOnUpdate();
            // $table->timestamp('arrived_at');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('received_at')->nullable();
            // $table->morphs('receiverable');
            $table->timestamps();
            $table->unique(['order_id', 'journey_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_journeys');
    }
};
