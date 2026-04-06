<?php

namespace Database\Seeders;

use App\Models\UlasanBuku;
use App\Models\User;
use App\Models\Buku;
use Illuminate\Database\Seeder;

class UlasanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->pluck('id_user')->toArray();
        $books = Buku::pluck('id_buku')->toArray();

        if (empty($users) || empty($books)) {
            return;
        }

        $komentars = [
            'Buku yang sangat bagus! Ceritanya menarik dan penuh emosi.',
            'Saya sangat menikmati buku ini. Penulisannya sangat rapi dan mudah dipahami.',
            'Alur ceritanya menarik, tapi ending-nya agak mengecewakan.',
            'Recommended banget! Wajib baca untuk pecinta novel.',
            'Bukunya oke, tapi menurut saya agak terlalu panjang.',
            'Sangat inspiratif. Saya membacanya dalam 2 hari!',
            'Cerita yang indah dan menyentuh hati. Penulis berbakat!',
            'Lumayan bagus, cocok untuk mengisi waktu luang.',
            'Salah satu buku terbaik yang pernah saya baca tahun ini.',
            'Plotnya biasa saja, tapi karakter-karakternya sangat kuat.',
            'Pengiriman cepat dan buku dalam kondisi baik. Ceritanya juga bagus!',
            'Kualitas cetakan bagus. Ceritanya seru dari awal sampai akhir.',
        ];

        $inserted = [];
        foreach ($books as $bookId) {
            $reviewCount = rand(1, min(4, count($users)));
            shuffle($users);
            $selectedUsers = array_slice($users, 0, $reviewCount);

            foreach ($selectedUsers as $userId) {
                $key = $userId . '-' . $bookId;
                if (isset($inserted[$key])) continue;

                UlasanBuku::create([
                    'id_user' => $userId,
                    'id_buku' => $bookId,
                    'rating' => rand(3, 5),
                    'komentar' => $komentars[array_rand($komentars)],
                ]);
                $inserted[$key] = true;
            }
        }
    }
}
