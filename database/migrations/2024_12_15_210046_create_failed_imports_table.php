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
        Schema::create('failed_imports', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->foreignUuid('import_id')->index();
            $table->json('disclosures');
            $table->json('row');
            $table->longText('exception');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_imports');
    }
};
