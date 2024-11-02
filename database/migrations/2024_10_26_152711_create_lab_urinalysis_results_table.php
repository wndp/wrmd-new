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
        Schema::create('lab_urinalysis_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('collection_method_id')->nullable()->index();
            $table->double('sg')->nullable();
            $table->double('ph')->nullable();
            $table->double('pro')->nullable();
            $table->string('glu')->nullable();
            $table->string('ket')->nullable();
            $table->string('bili')->nullable();
            $table->string('ubg')->nullable();
            $table->string('nitrite')->nullable();
            $table->double('bun')->nullable();
            $table->string('leuc')->nullable();
            $table->string('blood')->nullable();
            $table->string('color')->nullable();
            $table->unsignedBigInteger('turbidity_id')->nullable()->index();
            $table->unsignedBigInteger('odor_id')->nullable()->index();
            $table->string('crystals')->nullable();
            $table->string('casts')->nullable();
            $table->string('cells')->nullable();
            $table->string('microorganisms')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_urinalysis_results');
    }
};
