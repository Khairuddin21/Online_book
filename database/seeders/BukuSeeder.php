<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihin data yang udah ada
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('buku')->truncate();
        DB::table('kategori_buku')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Kategori
        $categories = [
            ['nama_kategori' => 'Novel Terjemahan', 'deskripsi' => 'Novel terjemahan dari penulis luar negeri'],
            ['nama_kategori' => 'Novel Indonesia',  'deskripsi' => 'Karya fiksi dari penulis Indonesia'],
            ['nama_kategori' => 'Puisi & Sastra',   'deskripsi' => 'Kumpulan puisi dan karya sastra'],
            ['nama_kategori' => 'Novel Romance',    'deskripsi' => 'Novel percintaan dan roman'],
        ];

        DB::table('kategori_buku')->insert($categories);

        // Map nama -> id_kategori (auto-increment mulai dari 1)
        $kat = [
            'terjemahan' => 1,
            'indo'       => 2,
            'puisi'      => 3,
            'romance'    => 4,
        ];

        $books = [
            [
                'id_kategori'  => $kat['terjemahan'],
                'judul'        => 'I Will Forget This Feeling Someday',
                'isbn'         => '978-602-063-948-5',
                'penulis'      => 'Yoru Sumino',
                'penerbit'     => 'Haru Media',
                'tahun_terbit' => 2020,
                'stok'         => 45,
                'harga'        => 89000.00,
                'deskripsi'    => 'Sebuah novel karya Yoru Sumino yang mengisahkan tentang perasaan yang perlahan memudar seiring waktu. Cerita menyentuh tentang kenangan, cinta, dan keikhlasan melepaskan perasaan yang pernah begitu kuat di hati.',
                'cover'        => 'https://i.pinimg.com/736x/e1/41/1c/e1411c28009d9c970f56ac24057d60bf.jpg',
            ],
            [
                'id_kategori'  => $kat['romance'],
                'judul'        => 'What If We Stay',
                'isbn'         => '978-623-257-412-3',
                'penulis'      => 'Aireen Natasha',
                'penerbit'     => 'Gramedia Pustaka Utama',
                'tahun_terbit' => 2023,
                'stok'         => 50,
                'harga'        => 95000.00,
                'deskripsi'    => 'Bagaimana jika kita memilih untuk tetap tinggal? Novel romance yang mengeksplorasi dilema antara pergi dan bertahan dalam sebuah hubungan. Kisah dua hati yang harus memutuskan apakah cinta mereka cukup kuat untuk menghadapi segalanya.',
                'cover'        => 'https://i.pinimg.com/736x/39/a8/11/39a811d5a27ecddbee09cb8c1aa5cf75.jpg',
            ],
            [
                'id_kategori'  => $kat['terjemahan'],
                'judul'        => 'Things Left Behind',
                'isbn'         => '978-623-257-518-2',
                'penulis'      => 'Kim Sae Byoul',
                'penerbit'     => 'Haru Media',
                'tahun_terbit' => 2022,
                'stok'         => 40,
                'harga'        => 92000.00,
                'deskripsi'    => 'Novel Korea yang menyentuh tentang hal-hal yang kita tinggalkan dalam hidup — kenangan, orang-orang, dan perasaan. Sebuah refleksi mendalam tentang kehilangan, penyesalan, dan bagaimana kita belajar merelakan apa yang tak lagi bisa kita genggam.',
                'cover'        => 'https://i.pinimg.com/736x/0b/72/2f/0b722f26eb331faf208f574f920990c9.jpg',
            ],
            [
                'id_kategori'  => $kat['puisi'],
                'judul'        => 'Manusia dan Badainya',
                'isbn'         => '978-623-199-205-7',
                'penulis'      => 'Syahid Muhammad',
                'penerbit'     => 'Buku Mojok',
                'tahun_terbit' => 2021,
                'stok'         => 35,
                'harga'        => 78000.00,
                'deskripsi'    => 'Kumpulan tulisan penuh makna dari Syahid Muhammad yang membahas pergulatan batin manusia menghadapi badai kehidupan. Sebuah karya sastra yang menggambarkan keresahan, harapan, dan keteguhan hati di tengah cobaan yang tak henti datang.',
                'cover'        => 'https://i.pinimg.com/736x/e7/f1/df/e7f1df60791d2be391ce064d4065c812.jpg',
            ],
            [
                'id_kategori'  => $kat['indo'],
                'judul'        => 'Segala yang Dihisap Langit',
                'isbn'         => '978-602-291-876-4',
                'penulis'      => 'Pinto Anugrah',
                'penerbit'     => 'Gramedia Pustaka Utama',
                'tahun_terbit' => 2019,
                'stok'         => 30,
                'harga'        => 85000.00,
                'deskripsi'    => 'Novel yang mengisahkan tentang kerinduan, kehilangan, dan pencarian makna hidup. Pinto Anugrah merangkai kata-kata indah tentang segala hal yang menguap ke langit — mimpi, harapan, dan orang-orang yang pergi tanpa pamit.',
                'cover'        => 'https://i.pinimg.com/736x/d2/d0/8a/d2d08ae8354438386ebea0da12fa6513.jpg',
            ],
            [
                'id_kategori'  => $kat['indo'],
                'judul'        => 'Ajaklah Tuhan ke Tanah Jawa',
                'isbn'         => '978-623-306-112-8',
                'penulis'      => 'Sekar Ayu Asmara',
                'penerbit'     => 'Mojok',
                'tahun_terbit' => 2022,
                'stok'         => 25,
                'harga'        => 82000.00,
                'deskripsi'    => 'Sebuah perjalanan spiritual dan budaya yang memadukan keimanan dengan kearifan lokal Jawa. Sekar Ayu Asmara mengajak pembaca menelusuri makna ketuhanan yang hidup dalam tradisi, ritual, dan kebijaksanaan masyarakat Tanah Jawa.',
                'cover'        => 'https://i.pinimg.com/736x/51/68/fb/5168fb18919c8525216ce8168dafa164.jpg',
            ],
            [
                'id_kategori'  => $kat['indo'],
                'judul'        => 'Sisi Tergelap Surga',
                'isbn'         => '978-623-257-625-7',
                'penulis'      => 'Brian Krisna',
                'penerbit'     => 'Gramedia Pustaka Utama',
                'tahun_terbit' => 2023,
                'stok'         => 55,
                'harga'        => 98000.00,
                'deskripsi'    => 'Novel yang menguak sisi gelap di balik hal-hal yang tampak sempurna. Brian Krisna membawa pembaca menyelami kegelapan yang bersembunyi di balik keindahan, tentang luka yang tersimpan rapi di balik senyuman dan surga yang ternyata menyimpan rahasia.',
                'cover'        => 'https://i.pinimg.com/736x/59/99/69/599969d075895875c16576361a30f16b.jpg',
            ],
            [
                'id_kategori'  => $kat['indo'],
                'judul'        => 'Seporsi Mie Ayam Sebelum Mati',
                'isbn'         => '978-623-257-501-4',
                'penulis'      => 'Brian Krisna',
                'penerbit'     => 'Gramedia Pustaka Utama',
                'tahun_terbit' => 2022,
                'stok'         => 60,
                'harga'        => 88000.00,
                'deskripsi'    => 'Apa yang akan kamu lakukan jika tahu hidupmu tinggal sebentar lagi? Novel populer karya Brian Krisna ini mengisahkan perjalanan emosional seseorang yang mencari makna hidup melalui hal-hal sederhana — termasuk semangkuk mie ayam terakhir.',
                'cover'        => 'https://i.pinimg.com/736x/c3/7a/05/c37a051b723b3c5467f6ec2edf2a1f52.jpg',
            ],
            [
                'id_kategori'  => $kat['romance'],
                'judul'        => 'Bandung After Rain',
                'isbn'         => '978-623-362-045-1',
                'penulis'      => 'Wulan Nur Amalia',
                'penerbit'     => 'Bhuana Ilmu Populer',
                'tahun_terbit' => 2023,
                'stok'         => 48,
                'harga'        => 90000.00,
                'deskripsi'    => 'Cerita cinta yang tumbuh di kota Bandung setelah hujan reda. Novel romance yang hangat tentang pertemuan tak terduga, secangkir kopi di kafe kecil, dan perasaan yang perlahan tumbuh di antara dua orang yang sama-sama sedang menyembuhkan luka lama.',
                'cover'        => 'https://i.pinimg.com/736x/8b/7d/b9/8b7db9227047ebaab26cc0cfbbda043d.jpg',
            ],
            [
                'id_kategori'  => $kat['indo'],
                'judul'        => 'Hello',
                'isbn'         => '978-623-246-178-5',
                'penulis'      => 'Tere Liye',
                'penerbit'     => 'Sabak Grip Nusantara',
                'tahun_terbit' => 2023,
                'stok'         => 70,
                'harga'        => 99000.00,
                'deskripsi'    => 'Novel terbaru dari seri dunia paralel karya Tere Liye. Mengisahkan petualangan yang mempertemukan karakter-karakter dari berbagai dunia dalam satu misi besar. Penuh aksi, persahabatan, dan pesan mendalam tentang keberanian menghadapi takdir.',
                'cover'        => 'https://i.pinimg.com/736x/7e/96/99/7e9699c659d25e2139093a393af96afb.jpg',
            ],
            [
                'id_kategori'  => $kat['indo'],
                'judul'        => 'Pulang',
                'isbn'         => '978-602-024-349-1',
                'penulis'      => 'Tere Liye',
                'penerbit'     => 'Republika Penerbit',
                'tahun_terbit' => 2015,
                'stok'         => 65,
                'harga'        => 95000.00,
                'deskripsi'    => 'Novel epik karya Tere Liye tentang Bujang, seorang anak dari pedalaman Sumatera yang terjebak dalam dunia gelap perbankan ilegal internasional. Kisah tentang pencarian jati diri, pengkhianatan, dan arti sesungguhnya dari kata pulang ke tanah kelahiran.',
                'cover'        => 'https://i.pinimg.com/736x/ac/73/19/ac7319cac6b2cb5bde04decd2e314aaf.jpg',
            ],
        ];

        DB::table('buku')->insert($books);
    }
}
