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
        Schema::create('store_payouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedDecimal('amount', 18, 2);
            $table->foreignId('store_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('proof_id')
                ->nullable()
                ->constrained('media_files')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('approver_id')
                ->nullable()
                ->constrained('admins')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('decliner_id')
                ->nullable()
                ->constrained('admins')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('store_payouts');
    }
};
