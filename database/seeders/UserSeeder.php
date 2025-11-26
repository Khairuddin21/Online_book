<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'nama' => 'Admin Toko Buku',
            'email' => 'admin@tokobuku.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'alamat' => 'Jakarta, Indonesia',
            'no_hp' => '081234567890',
        ]);

        // Create Regular User
        User::create([
            'nama' => 'User Test',
            'email' => 'user@tokobuku.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'alamat' => 'Bandung, Indonesia',
            'no_hp' => '081987654321',
        ]);

        echo "✓ 2 users created successfully!\n";
        echo "  - Admin: admin@tokobuku.com / admin123\n";
        echo "  - User:  user@tokobuku.com / user123\n";
    }
}

