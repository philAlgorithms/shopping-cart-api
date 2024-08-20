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
        Schema::create('states', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->foreignId('country_id')
                  ->nullable()
                  ->constrained('countries')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->timestamps();
            $table->unique(['name', 'country_id']);
        });

        Artisan::call('db:seed', [
            '--class' => 'StateSeeder'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
};
