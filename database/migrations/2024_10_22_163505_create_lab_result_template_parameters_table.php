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
        Schema::create('lab_result_template_parameters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lab_result_template_id');
            $table->unsignedBigInteger('parameter_id');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('data_type', 50);
            $table->unsignedBigInteger('sort_order');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_result_template_parameters');
    }
};
