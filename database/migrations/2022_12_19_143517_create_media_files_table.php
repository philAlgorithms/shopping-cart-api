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
        Schema::create('media_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path');
            $table->foreignId('mime_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->foreignId('disk_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('media_fileables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('media_file_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->morphs('media_fileable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_fileables');
        Schema::dropIfExists('media_files');
    }
};
