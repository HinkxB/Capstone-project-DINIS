<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemUser;
use Illuminate\Support\Facades\Hash;

class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        SystemUser::create([
            'username' => 'superadmin',
            'password' => Hash::make('password123'), // Change this in production
            'full_name' => 'System Administrator',
            'status' => 'active',
        ]);
    }
}
