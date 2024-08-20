<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
        Schema::create('towns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->foreignId('state_id')
                  ->constrained('states')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->timestamps();
            $table->unique(['name', 'state_id']);
        });

        Artisan::call('db:seed', [
            '--class' => 'TownSeeder'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('towns');
    }
};
