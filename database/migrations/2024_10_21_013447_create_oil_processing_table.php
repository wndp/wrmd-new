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
        Schema::create('oil_processings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('patient_id')->index();
            $table->date('date_collected_at');
            $table->time('time_collected_at')->nullable();
            $table->boolean('is_individual_oiled_animal')->default(false);
            $table->unsignedBigInteger('collection_condition_id');
            $table->date('date_processed_at');
            $table->time('time_processed_at')->nullable();
            $table->string('processor')->nullable();
            $table->unsignedBigInteger('oiling_status_id')->nullable();
            $table->unsignedBigInteger('oiling_percentage_id')->nullable();
            $table->unsignedBigInteger('oiling_depth_id')->nullable();
            $table->unsignedBigInteger('oiling_location_id')->nullable();
            $table->unsignedBigInteger('type_of_oil_id')->nullable();
            $table->text('evidence_collected')->nullable();
            $table->string('evidence_collected_by')->nullable();
            $table->unsignedBigInteger('carcass_condition_id')->nullable();
            $table->unsignedBigInteger('extent_of_scavenging_id')->nullable();
            $table->text('comments')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oil_processings');
    }
};
