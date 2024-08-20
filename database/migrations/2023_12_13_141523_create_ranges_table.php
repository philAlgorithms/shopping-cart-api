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
        Schema::create('ranges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('minimum');
            $table->integer('maximum');
            $table->decimal('value', 18, 2);
            $table->foreignId('range_type_id')
                ->constrained('range_types')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
            $table->unique(['minimum', 'maximum', 'range_type_id']);
        });

        Artisan::call('db:seed --class=RangeSeeder');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ranges');
    }
};
