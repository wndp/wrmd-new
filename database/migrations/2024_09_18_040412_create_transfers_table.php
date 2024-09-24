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
        Schema::create('transfers', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->unsignedBigInteger('patient_id')->index();
            $table->unsignedBigInteger('cloned_patient_id')->index()->nullable();
            $table->unsignedBigInteger('from_team_id')->index();
            $table->unsignedBigInteger('to_team_id')->index();
            //$table->unsignedBigInteger('thread_id')->index();
            $table->boolean('is_collaborative')->default(0);
            $table->boolean('is_accepted')->nullable();
            $table->datetime('responded_at')->nullable();
            $table->timestamps();

            $table->shardKey(['patient_id', 'from_team_id', 'to_team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
