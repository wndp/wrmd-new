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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('entity_id')->nullable()->index();
            $table->string('organization')->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('phone_clean', 50)->nullable();
            $table->string('alternate_phone', 50)->nullable();
            $table->string('alternate_phone_clean', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('subdivision', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('address', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->float('lat', 10, 6)->nullable();
            $table->float('lng', 10, 6)->nullable();
            $table->string('county', 50)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('no_solicitations')->index()->default(1);
            $table->boolean('is_volunteer')->index()->default(0);
            $table->boolean('is_member')->index()->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
