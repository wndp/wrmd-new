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
        Schema::create('lab_chemistry_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('ast')->nullable();
            $table->double('ck')->nullable();
            $table->double('ggt')->nullable();
            $table->double('amy')->nullable();
            $table->double('alb')->nullable();
            $table->double('alp')->nullable();
            $table->double('alt')->nullable();
            $table->double('tp')->nullable();
            $table->double('glob')->nullable();
            $table->double('bun')->nullable();
            $table->double('chol')->nullable();
            $table->double('crea')->nullable();
            $table->double('ba')->nullable();
            $table->double('glu')->nullable();
            $table->double('ca')->nullable();
            $table->unsignedBigInteger('ca_unit_id')->nullable()->index();
            $table->double('p')->nullable();
            $table->unsignedBigInteger('p_unit_id')->nullable()->index();
            $table->double('cl')->nullable();
            $table->unsignedBigInteger('cl_unit_id')->nullable()->index();
            $table->double('k')->nullable();
            $table->unsignedBigInteger('k_unit_id')->nullable()->index();
            $table->double('na')->nullable();
            $table->unsignedBigInteger('na_unit_id')->nullable()->index();
            $table->double('total_bilirubin')->nullable();
            $table->double('ag_ratio')->nullable();
            $table->double('tri')->nullable();
            $table->double('nak_ratio')->nullable();
            $table->double('cap_ratio')->nullable();
            $table->double('ua')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_chemistry_results');
    }
};
