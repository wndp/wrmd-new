<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('veterinarians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('license');
            $table->string('business_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('subdivision')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('phone_normalized', 50)->nullable();
            $table->string('phone_e164', 50)->nullable();
            $table->string('phone_national', 50)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veterinarians');
    }
};
