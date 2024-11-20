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
        Schema::create('patients', function (Blueprint $table) {
            $table->rowstore();
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->unsignedBigInteger('team_possession_id')->index();
            $table->foreignUuid('rescuer_id')->index();
            $table->unsignedBigInteger('taxon_id')->nullable()->index();
            $table->boolean('is_resident')->default(0);
            $table->datetime('voided_at')->nullable();
            $table->datetime('locked_at')->nullable();
            $table->string('common_name', 50);
            $table->unsignedBigInteger('morph_id')->nullable()->index();
            $table->date('date_admitted_at')->nullable();
            $table->time('time_admitted_at')->nullable();
            $table->string('admitted_by', 50)->nullable();
            $table->string('transported_by', 50)->nullable();
            $table->date('found_at')->nullable();
            $table->string('address_found', 150)->nullable();
            $table->string('city_found', 50)->nullable();
            $table->string('subdivision_found', 10)->nullable();
            $table->string('postal_code_found', 10)->nullable();
            $table->string('county_found', 50)->nullable();
            $table->point('coordinates_found')->nullable();
            $table->text('reason_for_admission')->nullable();
            $table->text('care_by_rescuer')->nullable();
            $table->text('notes_about_rescue')->nullable();
            $table->text('diagnosis')->nullable();
            $table->string('band', 50)->nullable();
            $table->string('microchip_number')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('name', 50)->nullable();
            //'keywords',
            $table->unsignedBigInteger('disposition_id')->nullable()->index();
            $table->unsignedBigInteger('transfer_type_id')->nullable()->index();
            $table->unsignedBigInteger('release_type_id')->nullable()->index();
            $table->date('dispositioned_at')->nullable();
            $table->string('disposition_address', 150)->nullable();
            $table->string('disposition_city', 50)->nullable();
            $table->string('disposition_subdivision', 50)->nullable();
            $table->string('disposition_postal_code', 10)->nullable();
            $table->string('disposition_county', 50)->nullable();
            $table->point('disposition_coordinates')->nullable();
            $table->text('reason_for_disposition')->nullable();
            $table->string('dispositioned_by')->nullable();
            $table->boolean('is_carcass_saved')->default(0);
            $table->boolean('is_criminal_activity')->default(0);
            $table->timestamps();

            $table->spatialIndex('coordinates_found');
            $table->spatialIndex('disposition_coordinates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
