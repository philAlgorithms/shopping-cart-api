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
        Schema::create('journeys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('logistics_personnel_id')
                ->nullable()
                ->constrained('logistics_personnels')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('origin_town_id')
                ->nullable()
                ->constrained('towns')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('destination_town_id')
                ->constrained('towns')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->timestamp('left_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamps();
            $table->unique(['origin_town_id', 'destination_town_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journeys');
    }
};
