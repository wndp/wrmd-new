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
        Schema::create('expense_transactions', function (Blueprint $table) {
            $table->uuid('id');
            $table->unsignedBigInteger('legacy_id')->index()->nullable();
            $table->foreignUuid('patient_id')->index()->nullable();
            $table->foreignUuid('expense_category_id')->index()->nullable();
            $table->date('transacted_at');
            $table->text('memo')->nullable();
            $table->integer('credit')->unsigned()->default(0);
            $table->integer('debit')->unsigned()->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_transactions');
    }
};
