<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Keuangan user
        User::create([
            'email' => 'keuangan@gmail.com',
            'name' => 'Staff Keuangan',
            'password' => bcrypt('keuangan123'),
            'role' => User::ROLE_KEUANGAN
        ]);

        // Create Kepsek user
        User::create([
            'email' => 'kepsek@gmail.com',
            'name' => 'Kepala Sekolah',
            'password' => bcrypt('kepsek123'),
            'role' => User::ROLE_KEPSEK
        ]);

        // Update existing admin user to keuangan role
        User::create([
            'email' => 'admin@gmail.com',
            'name' => 'Admin',
            'password' => bcrypt('admin123'),
            'role' => User::ROLE_KEUANGAN
        ]);
    }
}
