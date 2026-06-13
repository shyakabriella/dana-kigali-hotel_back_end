<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Dana Hotel Admin',
            'email' => 'admin@danahotel.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'phone' => '+1234567890',
        ]);
    }
}