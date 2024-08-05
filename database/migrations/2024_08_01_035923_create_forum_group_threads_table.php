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
        Schema::create('forum_group_threads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_group_id')->index();
            $table->unsignedBigInteger('thread_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_group_threads');
    }
};
