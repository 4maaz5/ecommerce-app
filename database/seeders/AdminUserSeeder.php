<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // check if email already exists
            [
                'name' => 'Admin User',
                'role' => 2,
                'password' => Hash::make('Admin@123'),
            ]
        );
    }
}
