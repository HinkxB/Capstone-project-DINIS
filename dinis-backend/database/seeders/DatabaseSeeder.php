<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Seed System User
        DB::table('system_user')->updateOrInsert(
            ['id' => 1],
            [
                'username' => 'admin',
                'full_name' => 'System Administrator', // The missing field!
                'password' => Hash::make('password123'),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Seed Chiefdom
        DB::table('chiefdom')->updateOrInsert(
            ['chiefdom_id' => 1],
            ['chiefdom_name' => 'Default Chiefdom']
        );

        // 3. Seed Village
        DB::table('village')->updateOrInsert(
            ['village_id' => 1],
            [
                'village_name' => 'Default Village',
                'chiefdom_id' => 1
            ]
        );

        Schema::enableForeignKeyConstraints();
        $this->command->info('Database seeded perfectly using the exact schema!');
    }
}