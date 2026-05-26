<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chiefdom', function (Blueprint $table) {
            // Using id() creates a BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->id('chiefdom_id'); 
            $table->string('chiefdom_name', 150);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('chiefdom');
    }
};