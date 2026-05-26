<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizen_nrc_record', function (Blueprint $table) {
            // Primary Key (UUID)
            $table->uuid('person_id')->primary();
            
            // Core Identity
            $table->string('nrc_number', 11)->unique()->comment('Format: 000000/00/1');
            $table->string('maiden_full_name', 255)->comment('Immutable legal name');
            $table->date('date_of_birth');
            $table->enum('sex', ['M', 'F']);
            
            // Location Links (matching your INT types from Schema.txt)
            $table->integer('village_id');
            $table->integer('chiefdom_id');
            
            // Extra Details
            $table->string('father_birth_place', 150)->nullable();
            $table->string('mother_birth_place', 150)->nullable();
            $table->text('special_marks')->nullable();
            
            // System/Blockchain Data
            $table->date('registration_date');
            $table->string('record_hash', 64)->unique();
            
            // Laravel timestamps (created_at, updated_at)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizen_nrc_record');
    }
};