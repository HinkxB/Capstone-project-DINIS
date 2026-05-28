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
        Schema::create('marriage_registry', function (Blueprint $table) {
            $table->id('marriage_id');
            // We use the UUIDs from the citizen_nrc_record table
            $table->char('husband_person_id', 36);
            $table->char('wife_person_id', 36);
            
            $table->string('certificate_number')->unique();
            $table->date('date_of_marriage');
            
            // Status tracking
            $table->enum('status', ['Married', 'Divorced'])->default('Married');
            $table->date('date_of_divorce')->nullable();
            
            $table->timestamps();

            // Note: We are skipping the strict $table->foreign() constraints here to prevent the MySQL Error 150 we saw earlier, as our Controller will enforce the relationships!
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marriage_registry');
    }
};
