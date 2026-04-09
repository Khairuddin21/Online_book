<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalanin seeder database
     */
    public function run(): void
    {
        // Bikin User Admin
        User::create([
            'nama' => 'Admin Toko Buku',
            'email' => 'admin@tokobuku.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'alamat' => 'Jakarta, Indonesia',
            'no_hp' => '081234567890',
        ]);

        // Bikin User Biasa
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

