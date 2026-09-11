<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Default Employee User
        User::updateOrCreate(
            ['email' => 'employee@employee.com'],
            [
                'name' => 'Employee User',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
