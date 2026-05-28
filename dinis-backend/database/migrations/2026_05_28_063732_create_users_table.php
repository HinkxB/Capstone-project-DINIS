<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('full_name');
        $table->string('username')->unique();
        $table->string('password');
        $table->string('department')->nullable();
        $table->enum('role', [
            'admin', 
            'auditor', 
            'dnr_officer', 
            'security_officer', 
            'health_provider', 
            'welfare_partner'
        ])->default('dnr_officer');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
