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
    Schema::table('citizen_nrc_record', function (Blueprint $table) {
        // Pending = Registered but no card yet
        // Captured = Biometrics taken
        // Issued = Physical card printed
        $table->enum('print_status', ['Pending', 'Captured', 'Issued'])->default('Pending');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            //
        });
    }
};
