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
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->foreignUuid('parent_id')->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('default_credit')->unsigned()->default(0);
            $table->integer('default_debit')->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['team_id', 'name']);
            $table->shardKey(['team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
