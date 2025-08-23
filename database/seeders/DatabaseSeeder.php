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

        // Create Regional Admins
        $regions = ['Jakarta', 'Surabaya', 'Bandung', 'Medan'];

        foreach ($regions as $region) {
            User::create([
                'name' => "Admin {$region}",
                'email' => strtolower($region) . '@fiber.com',
                'password' => Hash::make('password'),
                'role' => 'admin_region',
                'region' => $region,
            ]);

            // Create sites for each region
            for ($i = 1; $i <= 3; $i++) {
                Site::create([
                    'name' => "{$region} Site {$i}",
                    'region' => $region,
                    'latitude' => fake()->latitude(),
                    'longitude' => fake()->longitude(),
                    'description' => "Site {$i} di wilayah {$region}",
                ]);
            }
        }
    }
}