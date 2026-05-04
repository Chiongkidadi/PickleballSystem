<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // This will safely create the admin without crashing
        User::updateOrCreate(
            ['email' => 'chiongysha@gmail.com'], 
            [
                'name' => 'Admin',            
                'password' => bcrypt('ysha123') 
            ]
        );
    }
}