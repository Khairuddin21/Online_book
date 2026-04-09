<?php

namespace Database\Seeders;

use App\Models\User;
// pake Illuminate\Database\Console\Seeds\WithoutModelEvents kalo mau matiin event
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Isi database aplikasi pake data awal
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BukuSeeder::class,
        ]);
    }
}
