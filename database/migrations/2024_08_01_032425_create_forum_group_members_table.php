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
        Schema::create('forum_group_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('forum_group_id')->index();
            $table->foreignId('team_id')->index();
            $table->string('role');
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_group_members');
    }
};
