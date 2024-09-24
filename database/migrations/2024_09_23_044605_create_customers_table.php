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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->foreignId('billable_id');
            $table->string('billable_type');
            $table->string('paddle_id')->unique();
            $table->string('name');
            $table->string('email');
            $table->timestamp('trial_ends_at')->nullable();
            $table->string('pending_checkout_id')->nullable();
            $table->timestamps();

            $table->index(['billable_id', 'billable_type']);
            $table->shardKey('paddle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
