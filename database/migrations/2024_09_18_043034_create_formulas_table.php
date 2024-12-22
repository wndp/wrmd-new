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
        Schema::create('formulas', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('type')->index();
            $table->string('name');
            $table->json('defaults');
            $table->timestamps();

            $table->shardKey(['team_id'])->unique(['team_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulas');
    }
};
