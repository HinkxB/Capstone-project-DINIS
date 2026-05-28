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
        Schema::table('citizen_nrc_record', function (Blueprint $table) {
            // Add our color-coded ID types
            $table->enum('nrc_type', ['Green', 'Pink', 'Blue'])->default('Green')->after('sex');
            
            // Add foreign tracking data
            $table->string('country_of_origin')->default('Zambia')->after('nrc_type');
            $table->string('passport_number')->nullable()->after('country_of_origin');
        });
    }

    public function down(): void
    {
        Schema::table('citizen_nrc_record', function (Blueprint $table) {
            $table->dropColumn(['nrc_type', 'country_of_origin', 'passport_number']);
        });
    }
};
