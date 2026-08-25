<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@example.test',
            ],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Password123!'),
                'role' => User::ROLE_ADMINISTRATOR,
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'officer@example.test',
            ],
            [
                'name' => 'Loan Officer',
                'password' => Hash::make('Password123!'),
                'role' => User::ROLE_LOAN_OFFICER,
            ]
        );
    }
}