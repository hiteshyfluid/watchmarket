<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@watchmarket.co.uk'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '01234567890',
                'address' => 'Admin Office',
                'city' => 'London',
                'postal_code' => 'SW1A 1AA',
                'country' => 'United Kingdom',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
