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
        Schema::create('daily_tasks', function (Blueprint $table) {
            $table->uuid()->index();
            $table->morphs('task');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('summary');
            $table->string('occurrence');
            $table->date('occurrence_at');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->shardKey(['task_type', 'task_id', 'occurrence', 'occurrence_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_tasks');
    }
};
