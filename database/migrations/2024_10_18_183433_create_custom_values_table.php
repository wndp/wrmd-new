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
        Schema::create('custom_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('team_id')->index();
            $table->uuidMorphs('customable');
            $table->string('custom_field_1')->nullable();
            $table->string('custom_field_2')->nullable();
            $table->string('custom_field_3')->nullable();
            $table->string('custom_field_4')->nullable();
            $table->string('custom_field_5')->nullable();
            $table->string('custom_field_6')->nullable();
            $table->string('custom_field_7')->nullable();
            $table->string('custom_field_8')->nullable();
            $table->string('custom_field_9')->nullable();
            $table->string('custom_field_10')->nullable();
            $table->string('custom_field_11')->nullable();
            $table->string('custom_field_12')->nullable();
            $table->string('custom_field_13')->nullable();
            $table->string('custom_field_14')->nullable();
            $table->string('custom_field_15')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_values');
    }
};
