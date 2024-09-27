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
        Schema::create('necropsies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('patient_id')->index();
            $table->date('date_necropsied_at')->nullable();
            $table->time('time_necropsied_at')->nullable();
            $table->string('prosector', 100)->nullable();
            $table->boolean('is_photos_collected')->default(0);
            $table->boolean('is_carcass_radiographed')->default(0);
            $table->boolean('is_previously_frozen')->default(0);
            $table->boolean('is_scavenged')->default(0);
            $table->boolean('is_discarded')->default(0);
            $table->unsignedBigInteger('carcass_condition_id')->index()->nullable();
            $table->double('weight')->nullable();
            $table->unsignedBigInteger('weight_unit_id')->index()->nullable();
            $table->double('age')->nullable();
            $table->unsignedBigInteger('age_unit_id')->index()->nullable();
            $table->unsignedBigInteger('sex_id')->index()->nullable();
            $table->unsignedBigInteger('body_condition_id')->index()->nullable();
            $table->double('wing')->nullable();
            $table->double('tarsus')->nullable();
            $table->double('culmen')->nullable();
            $table->double('exposed_culmen')->nullable();
            $table->double('bill_depth')->nullable();

            $table->unsignedBigInteger('integument_finding_id')->index()->nullable();
            $table->text('integument')->nullable();
            $table->unsignedBigInteger('cavities_finding_id')->index()->nullable();
            $table->text('cavities')->nullable();
            $table->unsignedBigInteger('cardiovascular_finding_id')->index()->nullable();
            $table->text('cardiovascular')->nullable();
            $table->unsignedBigInteger('respiratory_finding_id')->index()->nullable();
            $table->text('respiratory')->nullable();
            $table->unsignedBigInteger('gastrointestinal_finding_id')->index()->nullable();
            $table->text('gastrointestinal')->nullable();
            $table->unsignedBigInteger('endocrine_reproductive_finding_id')->index()->nullable();
            $table->text('endocrine_reproductive')->nullable();
            $table->unsignedBigInteger('liver_gallbladder_finding_id')->index()->nullable();
            $table->text('liver_gallbladder')->nullable();
            $table->unsignedBigInteger('hematopoietic_finding_id')->index()->nullable();
            $table->text('hematopoietic')->nullable();
            $table->unsignedBigInteger('renal_finding_id')->index()->nullable();
            $table->text('renal')->nullable();
            $table->unsignedBigInteger('nervous_finding_id')->index()->nullable();
            $table->text('nervous')->nullable();
            $table->unsignedBigInteger('musculoskeletal_finding_id')->index()->nullable();
            $table->text('musculoskeletal')->nullable();
            $table->unsignedBigInteger('head_finding_id')->index()->nullable();
            $table->text('head')->nullable();
            $table->json('samples_collected')->nullable();
            $table->text('other_sample')->nullable();
            $table->text('morphologic_diagnosis')->nullable();
            $table->text('gross_summary_diagnosis')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('necropsies');
    }
};
