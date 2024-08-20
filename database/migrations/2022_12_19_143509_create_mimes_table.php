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
        Schema::create('mimes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('extension')->unique();
            $table->string('type');
            $table->string('document');
            $table->timestamps();
            $table->softDeletes();
        });

        Artisan::call('db:seed --class=MimeSeeder');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mimes');
    }
};
