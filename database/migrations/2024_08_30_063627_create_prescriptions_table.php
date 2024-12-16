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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('patient_id')->index();
            $table->foreignUuid('veterinarian_id')->nullable()->index();
            $table->string('drug');
            $table->double('concentration')->nullable();
            $table->unsignedBigInteger('concentration_unit_id')->nullable();
            $table->double('dosage')->nullable();
            $table->unsignedBigInteger('dosage_unit_id')->nullable();
            $table->double('loading_dose')->nullable();
            $table->unsignedBigInteger('loading_dose_unit_id')->nullable();
            $table->double('dose')->nullable();
            $table->unsignedBigInteger('dose_unit_id')->nullable();
            $table->unsignedBigInteger('frequency_id')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->date('rx_started_at');
            $table->date('rx_ended_at')->nullable();
            $table->boolean('is_controlled_substance')->default(0);
            $table->text('instructions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
