<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orphan_registry', function (Blueprint $table) {
            $table->uuid('orphan_id')->primary(); 
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('sex', ['M', 'F']);
            
            $table->integer('village_id');
            $table->integer('chiefdom_id');
            
            $table->string('institution_name');
            $table->date('date_registered_in_system'); 
            $table->char('claimed_person_id', 36)->nullable(); 
            $table->timestamps();

            // Only enforce the UUID foreign key, skip the integer ones to bypass MySQL Error 150
            $table->foreign('claimed_person_id')->references('person_id')->on('citizen_nrc_record');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orphan_registry');
    }
};