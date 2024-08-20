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
        Schema::create('buyer_referral_programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->foreignId('buyer_id')
                ->unique()
                ->constrained('buyers')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('activator_id')
                ->nullable()
                ->constrained('admins')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('deactivator_id')
                ->nullable()
                ->constrained('admins')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamp('activated_at')->nullable();
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
        Schema::dropIfExists('buyer_referral_programs');
    }
};
