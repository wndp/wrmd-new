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
        Schema::create('lab_cbc_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('packed_cell_volume')->nullable();
            $table->double('total_solids')->nullable();
            $table->double('buffy_coat')->nullable();
            $table->double('plasma_color')->nullable();
            $table->double('white_blod_cell_count')->nullable();
            $table->unsignedBigInteger('white_blod_cell_count_unit_id')->nullable()->index();
            $table->double('segmented_neutrophils')->nullable();
            $table->unsignedBigInteger('segmented_neutrophils_unit_id')->nullable()->index();
            $table->double('band_neutrophils')->nullable();
            $table->unsignedBigInteger('band_neutrophils_unit_id')->nullable()->index();
            $table->double('eosinophils')->nullable();
            $table->unsignedBigInteger('eosinophils_unit_id')->nullable()->index();
            $table->double('basophils')->nullable();
            $table->unsignedBigInteger('basophils_unit_id')->nullable()->index();
            $table->double('lymphocytes')->nullable();
            $table->unsignedBigInteger('lymphocytes_unit_id')->nullable()->index();
            $table->double('monocytes')->nullable();
            $table->unsignedBigInteger('monocytes_unit_id')->nullable()->index();
            $table->double('hemoglobin')->nullable();
            $table->double('mean_corpuscular_volume')->nullable();
            $table->double('mean_corpuscular_hemoglobin')->nullable();
            $table->double('mean_corpuscular_hemoglobin_concentration')->nullable();
            $table->string('erythrocytes')->nullable();
            $table->string('reticulocytes')->nullable();
            $table->string('thrombocytes')->nullable();
            $table->string('polychromasia')->nullable();
            $table->string('anisocytosis')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_cbc_results');
    }
};
