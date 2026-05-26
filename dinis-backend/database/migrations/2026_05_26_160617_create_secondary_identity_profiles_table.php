<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secondary_identity_profiles', function (Blueprint $table) {
            $table->id();
            // This is the link back to the citizen_nrc_record table
            $table->uuid('person_id'); 
            
            $table->string('identity_type'); // e.g., 'PASSPORT', 'VOTER_ID', 'DRIVERS_LICENSE'
            $table->string('document_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->timestamps();

            // This tells the database that person_id MUST exist in the citizen_nrc_record table
            $table->foreign('person_id')->references('person_id')->on('citizen_nrc_record')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secondary_identity_profiles');
    }
};