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
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('facility_id')->index();
            $table->string('area', 50);
            $table->string('enclosure', 50)->nullable();
            $table->string('hash', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
