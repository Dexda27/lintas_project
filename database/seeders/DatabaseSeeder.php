<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@fiber.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);



    }
}