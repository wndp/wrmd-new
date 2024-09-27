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
        Schema::create('team_extensions', function (Blueprint $table) {
            $table->uuid('id');
            $table->unsignedBigInteger('team_id');
            $table->string('extension');
            $table->timestamps();

            $table->unique(['team_id', 'extension']);
            $table->shardKey(['team_id', 'extension']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_extensions');
    }
};
