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
        Schema::create('range_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('key')->unique();
            $table->timestamps();
        });

        Artisan::call('db:seed --class=RangeTypeSeeder');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('range_types');
    }
};
