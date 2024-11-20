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
        Schema::create('incidents', function (Blueprint $table) {
            $table->rowstore();
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignUuid('responder_id')->index();
            $table->foreignUuid('patient_id')->nullable()->index();
            $table->string('incident_number');
            $table->datetime('reported_at');
            $table->datetime('occurred_at');
            $table->string('recorded_by', 150);
            $table->decimal('duration_of_call')->nullable();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->boolean('is_priority')->default(0);
            $table->string('suspected_species')->nullable();
            $table->smallInteger('number_of_animals')->nullable();
            $table->unsignedBigInteger('incident_status_id')->index();
            $table->string('incident_address', 150)->nullable();
            $table->string('incident_city', 50)->nullable();
            $table->string('incident_subdivision', 50)->nullable();
            $table->string('incident_postal_code', 50)->nullable();
            $table->point('incident_coordinates')->nullable();
            $table->text('description')->nullable();
            $table->datetime('resolved_at')->nullable();
            $table->text('resolution')->nullable();
            $table->boolean('given_information')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->spatialIndex('incident_coordinates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
