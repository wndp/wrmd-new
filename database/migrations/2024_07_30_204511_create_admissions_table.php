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
        Schema::create('admissions', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->unsignedBigInteger('team_id');
            $table->unsignedInteger('case_year');
            $table->unsignedBigInteger('case_id');
            $table->foreignUuid('patient_id')->index();
            $table->string('hash', 20)->nullable()->index();
            $table->timestamps();

            $table->sortKey(['case_year', 'case_id']);
            $table->shardKey(['team_id', 'case_year', 'case_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
