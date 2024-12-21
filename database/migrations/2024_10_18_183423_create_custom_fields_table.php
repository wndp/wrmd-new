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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('team_field_id')->index();
            $table->unsignedBigInteger('group_id')->index();
            $table->unsignedBigInteger('panel_id')->index()->nullable();
            $table->unsignedBigInteger('location_id')->index();
            $table->unsignedBigInteger('type_id')->index();
            $table->string('label', 50);
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
