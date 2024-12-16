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
        Schema::create('oil_waterproofing_assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('patient_id')->index();
            $table->date('date_evaluated_at');
            $table->time('time_evaluated_at')->nullable();
            $table->unsignedBigInteger('buoyancy_id')->nullable()->index();
            $table->unsignedBigInteger('hauled_out_id')->nullable()->index();
            $table->unsignedBigInteger('preening_id')->nullable()->index();
            $table->json('areas_wet_to_skin')->nullable();
            $table->json('areas_surface_wet')->nullable();
            $table->text('comments')->nullable();
            $table->string('examiner');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oil_waterproofing_assessments');
    }
};
