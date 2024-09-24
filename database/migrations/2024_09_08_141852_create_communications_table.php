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
        Schema::create('communications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('incident_id')->index();
            $table->datetime('communication_at');
            $table->text('communication_by')->nullable();
            $table->text('communication');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
