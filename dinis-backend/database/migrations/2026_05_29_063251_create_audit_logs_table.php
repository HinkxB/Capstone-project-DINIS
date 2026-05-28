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
    Schema::create('audit_logs', function (Blueprint $table) {
        $table->id();
        // Who did it?
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); 
        // What did they do? (e.g., "Registered Alien", "Printed ID")
        $table->string('action'); 
        // To whom did they do it? (e.g., The NRC number)
        $table->string('target_identifier')->nullable(); 
        // Where did they do it from?
        $table->ipAddress('ip_address')->nullable(); 
        
        $table->timestamps(); // Automatically records the exact date and time
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
