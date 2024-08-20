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
        Schema::create('logistics_personnels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('logistics_company_id')
                ->nullable()
                ->constrained('logistics_companies')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('base_town_id')
                ->constrained('towns')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('destination_town_id')
                ->nullable()
                ->constrained('towns')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('base_address');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Artisan::call('db:seed', [
            '--class' => 'LogisticsPersonnelSeeder'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logistics_personnels');
    }
};
