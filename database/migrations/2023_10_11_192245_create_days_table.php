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
        Schema::create('days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key')->unique();
            $table->string('name')->unique();
            $table->unsignedSmallInteger('iso_8601')->unique();
            $table->unsignedSmallInteger('numeric')->unique();
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => 'DaySeeder'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('days');
    }
};
