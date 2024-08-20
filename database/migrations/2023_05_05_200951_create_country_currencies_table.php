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
        Schema::create('country_currencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('currency_id')
                  ->constrained('currencies')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['currency_id', 'country_id']);
        });

        Artisan::call('db:seed', [
            '--class' => 'CurrencySeeder'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country_currencies');
    }
};
