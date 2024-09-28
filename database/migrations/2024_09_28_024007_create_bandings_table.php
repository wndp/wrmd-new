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
        Schema::create('bandings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('patient_id')->index();
            $table->string('band_number', 30)->index()->nullable();
            $table->date('banded_at')->nullable();
            $table->unsignedBigInteger('band_size_id')->nullable()->index();
            $table->unsignedBigInteger('band_disposition_id')->nullable()->index();
            $table->unsignedBigInteger('age_code_id')->nullable()->index();
            $table->unsignedBigInteger('how_aged_id')->nullable()->index();
            $table->unsignedBigInteger('sex_code_id')->nullable()->index();
            $table->unsignedBigInteger('how_sexed_id')->nullable()->index();
            $table->unsignedBigInteger('status_code_id')->nullable()->index();
            $table->unsignedBigInteger('additional_status_code_id')->nullable()->index();
            $table->string('master_bander_number', 10)->nullable();
            $table->string('banded_by', 50)->nullable();
            $table->string('location_number', 10)->nullable();
            $table->string('auxiliary_marker', 10)->nullable();
            $table->unsignedBigInteger('auxiliary_marker_type_id')->nullable()->index();
            $table->unsignedBigInteger('auxiliary_marker_color_id')->nullable()->index();
            $table->unsignedBigInteger('auxiliary_marker_code_color_id')->nullable()->index();
            $table->unsignedBigInteger('auxiliary_side_of_bird_id')->nullable()->index();
            $table->unsignedBigInteger('auxiliary_placement_on_leg_id')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->date('recaptured_at')->nullable();
            $table->unsignedBigInteger('recapture_disposition_id')->nullable()->index();
            $table->unsignedBigInteger('present_condition_id')->nullable()->index();
            $table->unsignedBigInteger('how_present_condition_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bandings');
    }
};
