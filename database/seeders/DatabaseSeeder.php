<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Make sure this matches your User model path
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // This ensures we don't create duplicate admins
        User::updateOrCreate(
            ['email' => 'chiongysha@gmail.com'],
            [
                'name' => 'Admin Ysha',
                'password' => Hash::make('ysha123'),
                // 'is_admin' => true, // Uncomment if you have an admin flag column
            ]
        );
    }
}