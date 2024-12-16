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
        Schema::create('lab_toxicology_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('toxin_id')->nullable()->index();
            $table->double('level')->nullable();
            $table->unsignedBigInteger('level_unit_id')->nullable()->index();
            $table->string('source')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_toxicology_results');
    }
};
