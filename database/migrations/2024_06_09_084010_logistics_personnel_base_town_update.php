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
        Schema::table('logistics_personnels', function (Blueprint $table) {
            if (Schema::hasColumn('logistics_personnels', 'base_town_id')) {
                $table->dropForeign('logistics_personnels_base_town_id_foreign');
                $table->dropColumn('base_town_id');
            }
            if (Schema::hasColumn('logistics_personnels', 'base_address')) {
                $table->dropColumn('base_address');
            }
        });

        Schema::table('logistics_personnels', function (Blueprint $table) {
            $table->string('base_address')->nullable();
            $table->foreignId('base_town_id')
                ->nullable()
                ->constrained('towns')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logistics_personnels', function (Blueprint $table) {
            //
        });
    }
};
