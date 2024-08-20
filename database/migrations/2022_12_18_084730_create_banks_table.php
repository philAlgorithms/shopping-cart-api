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
        Schema::create('banks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('paystack_id');
            $table->string('name');
            $table->string('slug');
            $table->string('code');
            $table->string('long_code')->nullable();
            $table->string('gateway')->nullable();
            $table->boolean('pay_with_bank')->default(1);
            $table->boolean('active')->default(1);
            $table->string('country');
            $table->string('currency');
            $table->string('type');
            $table->timestamps();
        });

        Artisan::call('db:seed --class=BankSeeder');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks');
    }
};
