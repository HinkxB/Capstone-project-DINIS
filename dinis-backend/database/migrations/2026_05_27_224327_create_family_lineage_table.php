<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ------------------------------------------------------------------------
        // FAMILY LINEAGE TABLE
        // This table maps the recursive parent-child relationships required for 
        // the visual Family Tree module.
        // ------------------------------------------------------------------------
        Schema::create('family_lineage', function (Blueprint $table) {
            $table->id();
            $table->char('parent_person_id', 36); // Links to citizen_nrc_record
            $table->char('child_person_id', 36);  // Links to citizen_nrc_record
            $table->enum('relationship_type', ['MOTHER', 'FATHER']);
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('parent_person_id')->references('person_id')->on('citizen_nrc_record')->onDelete('cascade');
            $table->foreign('child_person_id')->references('person_id')->on('citizen_nrc_record')->onDelete('cascade');
            
            // Prevent duplicate linkages
            $table->unique(['parent_person_id', 'child_person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_lineage');
    }
};