<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('care_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('patient_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->date('date_care_at');
            $table->time('time_care_at')->nullable();
            $table->double('weight')->nullable();
            $table->unsignedBigInteger('weight_unit_id')->nullable()->index();
            $table->double('temperature')->nullable();
            $table->unsignedBigInteger('temperature_unit_id')->nullable()->index();
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
        Schema::dropIfExists('care_logs');
    }
};
