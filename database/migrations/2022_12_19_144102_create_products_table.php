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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedDecimal('price', 18, 2);
            $table->unsignedInteger('quantity');
            $table->foreignId('product_sub_category_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('cover_image_id')
                  ->nullable()
                  ->constrained('media_files')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('brand_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('store_id')
                  ->constrained()
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->timestamps();
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
        Schema::dropIfExists('products');
    }
};
