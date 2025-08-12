<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@tour.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        // Create 5 agent users
        User::factory()->count(5)->create([
            'role' => 'agent',
            'password' => Hash::make('agent123')
        ]);
        
       
    }
}