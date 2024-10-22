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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('team_field_id')->index();
            $table->string('group', 50);
            $table->string('panel', 50)->nullable();
            $table->string('location', 20);
            $table->string('type', 20);
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
