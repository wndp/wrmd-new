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
        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->index();
            $table->string('name')->nullable();
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->double('frequency')->nullable();
            $table->unsignedBigInteger('frequency_unit_id')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_plans');
    }
};
