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
        Schema::create('oil_washes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('patient_id')->index();
            $table->date('date_washed_at');
            $table->time('time_washed_at')->nullable();
            $table->unsignedBigInteger('pre_treatment_id')->nullable()->index();
            $table->double('pre_treatment_duration')->nullable();
            $table->unsignedBigInteger('wash_type_id')->nullable()->index();
            $table->double('wash_duration')->nullable();
            $table->unsignedBigInteger('detergent_id')->nullable()->index();
            $table->double('rinse_duration')->nullable();
            $table->string('washer')->nullable();
            $table->string('handler')->nullable();
            $table->string('rinser')->nullable();
            $table->string('dryer')->nullable();
            $table->unsignedBigInteger('drying_method_id')->nullable()->index();
            $table->double('drying_duration')->nullable();
            $table->string('observations')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oil_washes');
    }
};
