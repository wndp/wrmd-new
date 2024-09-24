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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->foreignId('billable_id');
            $table->string('billable_type');
            $table->string('paddle_id')->unique();
            $table->string('paddle_subscription_id')->nullable()->index();
            $table->string('invoice_number')->nullable();
            $table->string('status');
            $table->string('total');
            $table->string('tax');
            $table->string('currency', 3);
            $table->timestamp('billed_at');
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
        Schema::dropIfExists('transactions');
    }
};
