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
        Schema::create('wildlife_recovery_patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->integer('team_id')->unsigned()->index();
            $table->foreignUuid('patient_id')->nullable()->index();
            $table->string('surveyid', 50)->nullable();
            $table->string('surveyname', 50)->nullable();
            $table->string('method', 50)->nullable();
            $table->string('spillname', 50)->nullable();
            $table->string('teamname', 50)->nullable();
            $table->string('userorg', 50)->nullable();
            $table->string('surveyuserenter', 50)->nullable();
            $table->string('entrytime', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('count', 50)->nullable();
            $table->string('taxa', 50)->nullable();
            $table->string('subtype', 50)->nullable();
            $table->string('condition', 50)->nullable();
            $table->string('notes', 50)->nullable();
            $table->string('locdesc', 50)->nullable();
            $table->string('highpriority', 50)->nullable();
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->string('entryacc', 50)->nullable();
            $table->string('entryid', 75)->nullable();
            $table->string('qr_code', 50)->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wildlife_recovery_patients');
    }
};
