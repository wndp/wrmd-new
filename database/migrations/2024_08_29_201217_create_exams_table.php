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
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('patient_id')->index();
            $table->date('date_examined_at')->nullable();
            $table->time('time_examined_at')->nullable();
            $table->unsignedBigInteger('exam_type_id')->nullable()->index();
            $table->unsignedBigInteger('sex_id')->nullable()->index();
            $table->double('weight')->nullable();
            $table->unsignedBigInteger('weight_unit_id')->nullable()->index();
            $table->unsignedBigInteger('body_condition_id')->nullable()->index();
            $table->double('age')->nullable();
            $table->unsignedBigInteger('age_unit_id')->nullable()->index();
            $table->unsignedBigInteger('attitude_id')->nullable()->index();
            $table->unsignedBigInteger('dehydration_id')->nullable()->index();
            $table->double('temperature')->nullable();
            $table->unsignedBigInteger('temperature_unit_id')->nullable()->index();
            $table->unsignedBigInteger('mucous_membrane_color_id')->nullable()->index();
            $table->unsignedBigInteger('mucous_membrane_texture_id')->nullable()->index();
            $table->text('head')->nullable();
            $table->text('cns')->nullable();
            $table->text('cardiopulmonary')->nullable();
            $table->text('gastrointestinal')->nullable();
            $table->text('musculoskeletal')->nullable();
            $table->text('integument')->nullable();
            $table->text('body')->nullable();
            $table->text('forelimb')->nullable();
            $table->text('hindlimb')->nullable();
            $table->unsignedBigInteger('head_finding_id')->nullable()->index();
            $table->unsignedBigInteger('cns_finding_id')->nullable()->index();
            $table->unsignedBigInteger('cardiopulmonary_finding_id')->nullable()->index();
            $table->unsignedBigInteger('gastrointestinal_finding_id')->nullable()->index();
            $table->unsignedBigInteger('musculoskeletal_finding_id')->nullable()->index();
            $table->unsignedBigInteger('integument_finding_id')->nullable()->index();
            $table->unsignedBigInteger('body_finding_id')->nullable()->index();
            $table->unsignedBigInteger('forelimb_finding_id')->nullable()->index();
            $table->unsignedBigInteger('hindlimb_finding_id')->nullable()->index();
            $table->text('treatment')->nullable();
            $table->text('nutrition')->nullable();
            $table->text('comments')->nullable();
            $table->string('examiner', 50)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
