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
        Schema::create('morphometrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->foreignUuid('patient_id')->index();
            $table->date('measured_at');
            $table->double('bill_length')->nullable();
            $table->double('bill_width')->nullable();
            $table->double('bill_depth')->nullable();
            $table->double('head_bill_length')->nullable();
            $table->double('culmen')->nullable();
            $table->double('exposed_culmen')->nullable();
            $table->double('wing_chord')->nullable();
            $table->double('flat_wing')->nullable();
            $table->double('tarsus_length')->nullable();
            $table->double('middle_toe_length')->nullable();
            $table->double('toe_pad_length')->nullable();
            $table->double('halux_length')->nullable();
            $table->double('tail_length')->nullable();
            $table->double('weight')->nullable();
            $table->text('samples_collected')->nullable();
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('morphometrics');
    }
};
