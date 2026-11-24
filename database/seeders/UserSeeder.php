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
            ['email' => 'admin@dispora-palu.go.id'],
            [
                'name' => 'Admin Dispora',
                'password' => Hash::make('Admin@12345'),
            ]
        );
    }
}
