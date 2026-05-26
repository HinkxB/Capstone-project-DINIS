<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('village', function (Blueprint $table) {
            $table->id('village_id');
            // Must use unsignedBigInteger to match the chiefdom_id above
            $table->unsignedBigInteger('chiefdom_id'); 
            $table->string('village_name', 150);
            $table->timestamps();
            
            $table->foreign('chiefdom_id')->references('chiefdom_id')->on('chiefdom');
        });
    }

    public function down(): void {
        Schema::dropIfExists('village');
    }
};