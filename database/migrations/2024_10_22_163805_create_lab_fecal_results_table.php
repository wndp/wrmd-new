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
        Schema::create('lab_fecal_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('float_id')->nullable()->index();
            $table->unsignedBigInteger('direct_id')->nullable()->index();
            $table->unsignedBigInteger('centrifugation_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_fecal_results');
    }
};
