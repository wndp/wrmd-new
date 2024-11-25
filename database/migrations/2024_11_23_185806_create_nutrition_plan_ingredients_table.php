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
        Schema::create('nutrition_plan_ingredients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('nutrition_plan_id')->index();
            $table->double('quantity');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('ingredient');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_ingredients');
    }
};
