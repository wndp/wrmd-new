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
        Schema::table('teams', function (Blueprint $table) {
            $table->after('personal_team', function ($table) {
                $table->string('status', 20)->index();
                $table->boolean('is_master_account')->default(0);
                $table->unsignedBigInteger('master_account_id')->nullable()->index();
                $table->string('federal_permit_number')->nullable();
                $table->string('subdivision_permit_number')->nullable();
                $table->string('contact_name');
                $table->string('country');
                $table->string('address');
                $table->string('city');
                $table->string('subdivision')->nullable();
                $table->string('postal_code')->nullable();
                $table->point('coordinates')->nullable();
                $table->string('phone_number');
                $table->string('contact_email');
                $table->string('website')->nullable();
                $table->string('profile_photo_path')->nullable();
                $table->string('notes')->nullable();
                $table->string('timezone')->nullable();

                $table->spatialIndex('coordinates');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropSpatialIndex('coordinates');

            $table->dropColumn('status');
            $table->dropColumn('is_master_account');
            $table->dropColumn('master_account_id');
            $table->dropColumn('federal_permit_number');
            $table->dropColumn('subdivision_permit_number');
            $table->dropColumn('contact_name');
            $table->dropColumn('country');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('subdivision');
            $table->dropColumn('postal_code');
            $table->dropColumn('coordinates');
            $table->dropColumn('phone_number');
            $table->dropColumn('contact_email');
            $table->dropColumn('website');
            $table->dropColumn('profile_photo_path');
            $table->dropColumn('notes');
        });
    }
};
