<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@loan.com',
            'password' => Hash::make('admin123'),
            'phone'    => '9000000001',
            'address'  => '123, Admin Office, Mumbai, Maharashtra',
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Rahul Sharma',
            'email'    => 'rahul@example.com',
            'password' => Hash::make('password'),
            'phone'    => '9876543210',
            'address'  => '45, Park Street, Pune, Maharashtra',
            'role'     => 'customer',
        ]);
    }
}
