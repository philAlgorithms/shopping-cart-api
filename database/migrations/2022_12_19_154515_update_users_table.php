<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->foreignId('country_id')
                  ->nullable()
                  ->constrained('countries')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            // $table->foreignId('town_id')
            //       ->nullable()
            //       ->constrained('towns')
            //       ->nullOnDelete()
            //       ->cascadeOnUpdate();
            $table->string('address')->nullable();
            $table->string('bvn')->nullable();
            $table->timestamp('bvn_verified_at')->nullable();
            $table->timestamp('bvn_declined_at')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->foreignId('bank_id')
                  ->nullable()
                  ->constrained('banks')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            $table->string('last_uploaded_bvn')->nullable();
            $table->string('last_uploaded_bank_account_number')->nullable();
            $table->foreignId('last_uploaded_bank_id')
                  ->nullable()
                  ->constrained('banks')
                  ->nullOnDelete();
            $table->string('paystack_customer_code')->nullable();
            $table->foreignId('avatar_id')
                ->nullable()
                ->constrained('media_files')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        Artisan::call('db:seed --class=AdminSeeder');
        Artisan::call('db:seed --class=BuyerSeeder');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
