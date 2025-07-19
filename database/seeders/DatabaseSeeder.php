<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed admin user
        User::create([
            'name' => 'Admin ElectroMech',
            'email' => 'admin@electromech.com',
            'phone' => '081234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Seed mechanic user
        User::create([
            'name' => 'Teknisi Budi',
            'email' => 'budi@electromech.com',
            'phone' => '081298765432',
            'password' => Hash::make('teknisi123'),
            'role' => 'mechanic'
        ]);

        User::create([
            'name' => 'Nanda',
            'email' => 'nanda@gmail.com',
            'phone' => '0895704340678',
            'password' => Hash::make('nanda123'),
            'role' => 'customer'
        ]);

        // Create additional mechanics
        User::factory()->mechanic()->count(5)->create();

        // Create some customers
        User::factory()->customer()->count(10)->create();
    }
}