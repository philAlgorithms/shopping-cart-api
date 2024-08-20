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
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description');
            $table->string('key')->unique();
            $table->foreignId('vendor_id')
                  ->constrained('vendors')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('logo_id')
                  ->nullable()
                  ->constrained('media_files')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('cac_file_id')
                  ->nullable()
                  ->constrained('media_files')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            $table->timestamps();
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
};
