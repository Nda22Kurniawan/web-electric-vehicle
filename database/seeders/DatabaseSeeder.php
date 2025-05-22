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

        // Anda bisa menambahkan lebih banyak teknisi di sini
        User::factory()->count(5)->create([
            'role' => 'mechanic'
        ]);
    }
}