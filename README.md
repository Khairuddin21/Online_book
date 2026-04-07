# 📚 Book.com — Toko Buku Online

Aplikasi toko buku online berbasis **Laravel** dengan integrasi **Midtrans Payment Gateway** dan **Google OAuth**. Dibangun dengan tema hijau pastel yang modern dan responsif.

---

## 🛠 Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 11, PHP 8.2+ |
| Database | MySQL (XAMPP) |
| Frontend | Blade Templates, CSS Custom, Font Awesome, Chart.js |
| Payment | Midtrans Snap |
| OAuth | Laravel Socialite (Google) |
| Font | Inter (Bunny Fonts) |

---

## 📋 Daftar Fitur Lengkap

### 👤 Sisi Pengguna (User)
- Registrasi & Login (Email/Password + Google OAuth)
- Beranda dengan rekomendasi buku terbaru
- Katalog buku (pencarian, filter kategori, paginasi)
- Detail buku (deskripsi, review, rating bintang, favorit)
- Keranjang belanja (tambah, ubah qty, hapus — semua via AJAX)
- Checkout dengan manajemen alamat pengiriman
- Pembayaran via Midtrans Snap (GoPay, Bank Transfer, dll)
- Riwayat pesanan & pembatalan pesanan
- Inbox notifikasi (balasan admin, badge belum dibaca)
- Halaman kontak (kirim pesan ke admin)
- Profil pengguna

### 🔧 Sisi Admin
- Dashboard (statistik, grafik pendapatan 6 bulan, distribusi status, pesanan terbaru)
- Kelola Kategori Buku (CRUD)
- Kelola Buku (CRUD, filter kategori)
- Kelola Pesanan (lihat, update status, hapus)
- Kelola Pengguna (lihat, ubah role)
- Kelola Pesan Kontak (baca, balas, hapus)

---

## 🗺 Flowchart Lengkap — Perjalanan Sebagai User

Berikut adalah alur penggunaan website **dari sudut pandang seorang pengguna baru** bernama **Rani**, mulai dari pertama kali membuka website hingga pesanan selesai.

---

### 1. 🏠 Landing Page (Halaman Awal)

```
┌─────────────────────────────────────────────┐
│              LANDING PAGE (/)                │
│                                              │
│  ┌────────────────────────────────────────┐  │
│  │  Hero Section                          │  │
│  │  "Temukan Buku Favoritmu"              │  │
│  │  [Jelajahi Buku] [Masuk]               │  │
│  │  + Animasi 3D Rak Buku                 │  │
│  └────────────────────────────────────────┘  │
│                                              │
│  ┌────────────────────────────────────────┐  │
│  │  Jenis Buku (Fiksi, Non-Fiksi, dll)   │  │
│  └────────────────────────────────────────┘  │
│                                              │
│  ┌────────────────────────────────────────┐  │
│  │  8 Buku Rekomendasi Terbaru            │  │
│  └────────────────────────────────────────┘  │
│                                              │
│  ┌────────────────────────────────────────┐  │
│  │  Tentang Kami                           │  │
│  └────────────────────────────────────────┘  │
└─────────────────────────────────────────────┘
```

> **Rani** membuka `localhost/Online_book/public` dan melihat halaman landing yang menarik. Dia tertarik untuk melihat koleksi buku dan memutuskan untuk mendaftar.

---

### 2. 🔐 Registrasi & Login

```
                    ┌──────────────┐
                    │ Landing Page │
                    └──────┬───────┘
                           │
               ┌───────────┴───────────┐
               ▼                       ▼
        ┌─────────────┐        ┌─────────────┐
        │   Register  │        │    Login     │
        │  /register  │        │   /login     │
        └──────┬──────┘        └──────┬───────┘
               │                      │
    ┌──────────┴──────────┐    ┌──────┴──────────────┐
    │ Isi Form:           │    │ Pilih metode:       │
    │ • Nama              │    │                      │
    │ • Email             │    │  ┌──────────────┐   │
    │ • Password          │    │  │Email+Password│   │
    │ • Konfirmasi Pass   │    │  └──────┬───────┘   │
    └──────────┬──────────┘    │         │            │
               │               │  ┌──────┴───────┐   │
               │               │  │Google OAuth  │   │
               │               │  │/auth/google  │   │
               │               │  └──────┬───────┘   │
               │               └─────────┼───────────┘
               │                         │
               └────────┬────────────────┘
                        ▼
              ┌───────────────────┐
              │  Cek Role User    │
              └────────┬──────────┘
                       │
            ┌──────────┴──────────┐
            ▼                     ▼
   ┌──────────────┐     ┌──────────────┐
   │  role: user  │     │ role: admin  │
   │ → /home      │     │ → /admin     │
   │              │     │   /dashboard │
   └──────────────┘     └──────────────┘
```

> **Rani** klik "Daftar", mengisi nama, email, dan password. Akun langsung aktif dan dia diarahkan ke halaman beranda user. Alternatifnya, dia bisa klik "Masuk dengan Google" untuk login instan.

---

### 3. 🏡 Beranda User & Jelajah Buku

```
┌─────────────────────────────────────────────────────┐
│                  BERANDA (/home)                     │
│                                                      │
│  ┌────────────────────────────────────────────────┐  │
│  │  Navbar: Home | Buku | Kontak | 🛒(badge) | 👤 │  │
│  │          Pesanan | Pesan(notif) | Logout        │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  ┌──────────────────┐  ┌──────────────────────────┐  │
│  │ 10 Buku Terbaru  │  │ Daftar Kategori          │  │
│  │ (grid cards)     │  │ • Fiksi                  │  │
│  │                  │  │ • Non-Fiksi              │  │
│  │ [Lihat Detail]   │  │ • Teknologi              │  │
│  │                  │  │ • dll...                  │  │
│  └──────────────────┘  └──────────────────────────┘  │
└─────────────────────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────┐
│              KATALOG BUKU (/books)                   │
│                                                      │
│  🔍 [Cari judul/penulis/penerbit...]                │
│  📂 Filter: [Semua Kategori ▼]                      │
│                                                      │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐                   │
│  │Buku1│ │Buku2│ │Buku3│ │Buku4│  ... (12/halaman) │
│  │     │ │     │ │     │ │     │                    │
│  │ Rp  │ │ Rp  │ │ Rp  │ │ Rp  │                    │
│  └──┬──┘ └──┬──┘ └──┬──┘ └──┬──┘                   │
│     │       │       │       │                        │
│  [◄ 1  2  3  4  ►]  ← Paginasi                     │
└─────────────────────────────────────────────────────┘
```

> **Rani** melihat 10 buku terbaru di beranda. Dia klik "Buku" di navbar untuk membuka katalog lengkap. Dia bisa mencari berdasarkan judul/penulis atau filter berdasarkan kategori.

---

### 4. 📖 Detail Buku — Favorit & Review

```
┌──────────────────────────────────────────────────────────┐
│               DETAIL BUKU (/book/{id})                    │
│                                                           │
│  ┌──────────┐  ┌──────────────────────────────────────┐  │
│  │          │  │ Judul Buku                           │  │
│  │  COVER   │  │ Penulis: xxxxxxx                     │  │
│  │  BUKU    │  │ Penerbit: xxxxxxx                    │  │
│  │          │  │ ISBN: xxxxxxx                         │  │
│  │          │  │ Tahun: 2024                           │  │
│  │          │  │ Stok: 15                              │  │
│  │          │  │ Harga: Rp 85.000                      │  │
│  └──────────┘  │                                       │  │
│                │ [❤️ Favorit]  [🛒 Tambah ke Keranjang] │  │
│                │                                       │  │
│                │ ⭐⭐⭐⭐☆ (4.2 dari 10 ulasan)       │  │
│                └──────────────────────────────────────┘  │
│                                                           │
│  ┌────────────────────────────────────────────────────┐  │
│  │ DESKRIPSI                                          │  │
│  │ Lorem ipsum dolor sit amet...                      │  │
│  └────────────────────────────────────────────────────┘  │
│                                                           │
│  ┌────────────────────────────────────────────────────┐  │
│  │ ULASAN & RATING                                    │  │
│  │                                                     │  │
│  │ Tulis Ulasan:                                       │  │
│  │ ⭐ [1] [2] [3] [4] [5]                             │  │
│  │ [Komentar anda...                    ] [Kirim]      │  │
│  │                                                     │  │
│  │ ─────────────────────────────────────               │  │
│  │ 👤 Budi  ⭐⭐⭐⭐⭐  "Buku bagus sekali!"          │  │
│  │ 👤 Sari  ⭐⭐⭐⭐☆  "Recommended!"                 │  │
│  └────────────────────────────────────────────────────┘  │
│                                                           │
│  ┌────────────────────────────────────────────────────┐  │
│  │ BUKU TERKAIT (kategori sama)                       │  │
│  │ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐                  │  │
│  │ │     │ │     │ │     │ │     │                   │  │
│  │ └─────┘ └─────┘ └─────┘ └─────┘                  │  │
│  └────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────┘
```

**Alur Favorit & Review:**
```
User di halaman detail buku
         │
    ┌────┴────────────────────┐
    ▼                         ▼
 Klik ❤️ Favorit          Tulis Review
 (AJAX POST)              (1-5 bintang + komentar)
    │                         │
    ▼                         ▼
 Toggle On/Off            Tersimpan di DB
 (tanpa reload)           (1 review per user per buku)
                          (bisa edit ulang)
```

> **Rani** klik salah satu buku dan melihat detail lengkap. Dia klik tombol ❤️ untuk menandai sebagai favorit (tanpa reload halaman). Dia juga memberi rating 5 bintang dan komentar "Sangat bagus!". Lalu klik "Tambah ke Keranjang".

---

### 5. 🛒 Keranjang Belanja

```
┌────────────────────────────────────────────────────┐
│              KERANJANG (/cart)                       │
│                                                     │
│  ┌───────────────────────────────────────────────┐  │
│  │ Cover │ Judul Buku A    │  [-] 2 [+]  │ Rp170│  │
│  │       │ Rp 85.000/pcs   │    ✕ Hapus  │  .000│  │
│  ├───────┼─────────────────┼─────────────┼──────┤  │
│  │ Cover │ Judul Buku B    │  [-] 1 [+]  │ Rp95 │  │
│  │       │ Rp 95.000/pcs   │    ✕ Hapus  │  .000│  │
│  └───────────────────────────────────────────────┘  │
│                                                     │
│              Total: Rp 265.000                      │
│                                                     │
│         [ 🛒 Lanjut ke Checkout ]                   │
└────────────────────────────────────────────────────┘
```

**Alur Keranjang (semua AJAX):**
```
Halaman Buku ──── [Tambah ke Keranjang] ────► POST /api/cart/add
                                                     │
                                                     ▼
                                              Badge 🛒 di navbar
                                              otomatis update
                                              (GET /api/cart/count)
                                                     │
                                                     ▼
                                              ┌──────────────┐
                                              │ Halaman Cart │
                                              │  /cart        │
                                              └──────┬───────┘
                                                     │
                              ┌───────────────┬──────┴──────┐
                              ▼               ▼              ▼
                        Ubah Qty         Hapus Item     Checkout
                     POST /api/       DELETE /api/     GET /checkout
                     cart/update      cart/delete/{id}
                     (cek stok)
```

> **Rani** menambahkan 2 buku ke keranjang. Icon 🛒 di navbar langsung menunjukkan angka "2". Dia buka keranjang, mengubah qty Buku A jadi 3 (sistem cek stok otomatis), lalu klik "Lanjut ke Checkout".

---

### 6. 📦 Checkout & Alamat Pengiriman

```
┌────────────────────────────────────────────────────────────┐
│                   CHECKOUT (/checkout)                       │
│                                                              │
│            ① Pengiriman ─────── ② Pembayaran                │
│               (aktif)             (belum)                    │
│                                                              │
│  ┌─────────────────────────────┐  ┌────────────────────────┐│
│  │ PESANAN (3 Item)            │  │ RINGKASAN BELANJA      ││
│  │                             │  │                         ││
│  │ 📖 Buku A × 3  Rp 255.000  │  │ Subtotal:  Rp 350.000  ││
│  │ 📖 Buku B × 1  Rp  95.000  │  │ Ongkir:    Rp       0  ││
│  │                             │  │ ────────────────────── ││
│  ├─────────────────────────────┤  │ TOTAL:     Rp 350.000  ││
│  │ ALAMAT PENGIRIMAN           │  │                         ││
│  │                             │  │ [Buat Pesanan]          ││
│  │ ○ Rumah (default)           │  └────────────────────────┘│
│  │   Rani - 0812xxxxx         │                              │
│  │   Jl. Merdeka No. 10...    │                              │
│  │                             │                              │
│  │ ○ Kantor                    │                              │
│  │   Rani - 0812xxxxx         │                              │
│  │   Jl. Sudirman No. 5...    │                              │
│  │                             │                              │
│  │ [+ Tambah Alamat Baru]     │                              │
│  └─────────────────────────────┘                              │
└────────────────────────────────────────────────────────────┘
```

**Alur Checkout:**
```
GET /checkout
      │
      ├── Belum punya alamat? ──► [+ Tambah Alamat Baru]
      │                                    │
      │                          POST /address/store
      │                          (label, nama, hp, alamat)
      │                                    │
      │◄───────────────────────────────────┘
      │
      ├── Pilih alamat pengiriman
      │
      ▼
POST /checkout/process
      │
      ├── Buat record Pesanan (status: 'menunggu')
      ├── Buat PesananDetail untuk setiap item
      ├── Kurangi stok buku
      ├── Hapus item dari keranjang
      │
      ▼
Redirect → /payment/{id_pesanan}
```

> **Rani** memilih alamat "Rumah" yang sudah dia simpan sebelumnya. Dia review pesanan 3 item senilai Rp 350.000 lalu klik "Buat Pesanan". Sistem membuat pesanan, mengurangi stok, dan mengarahkan ke halaman pembayaran.

---

### 7. 💳 Pembayaran (Midtrans Snap)

```
┌──────────────────────────────────────────────────────┐
│              PEMBAYARAN (/payment/{id})                │
│                                                       │
│            ① Pengiriman ─────── ② Pembayaran          │
│              (selesai ✓)          (aktif)              │
│                                                       │
│  ┌─────────────────────────────────────────────────┐  │
│  │                                                  │  │
│  │  Order #5 — Rp 350.000                          │  │
│  │                                                  │  │
│  │         [ 💳 Bayar Sekarang ]                    │  │
│  │                                                  │  │
│  └─────────────────────────────────────────────────┘  │
│                                                       │
│         ┌──────────────────────┐                      │
│         │ MIDTRANS SNAP POPUP  │                      │
│         │                      │                      │
│         │ Pilih metode:        │                      │
│         │ • GoPay              │                      │
│         │ • Bank Transfer      │                      │
│         │   - BCA              │                      │
│         │   - BNI              │                      │
│         │   - Mandiri          │                      │
│         │ • Kartu Kredit       │                      │
│         │ • dll                │                      │
│         │                      │                      │
│         │   [ Bayar Rp350.000] │                      │
│         └──────────────────────┘                      │
└──────────────────────────────────────────────────────┘
```

**Alur Pembayaran:**
```
GET /payment/{id}
      │
      ├── Generate Snap Token (server-side Midtrans API)
      │   (atau gunakan token yang sudah ada)
      │
      ▼
Tampilkan halaman + tombol "Bayar Sekarang"
      │
      ▼
Klik "Bayar" → Midtrans Snap Popup muncul
      │
      ├── User pilih metode pembayaran
      ├── Proses pembayaran di Midtrans
      │
      ▼
  ┌───────────────────────────────────────┐
  │              HASIL                     │
  │                                        │
  │  ✅ Berhasil:                          │
  │     POST /payment/{id}/process         │
  │     → Simpan record Pembayaran         │
  │     → Status pesanan → 'diproses'      │
  │     → Kirim notifikasi ke inbox user   │
  │     → Redirect ke /orders              │
  │                                        │
  │  ⏳ Pending:                            │
  │     Menunggu konfirmasi bank            │
  │     Webhook Midtrans akan update nanti │
  │                                        │
  │  ❌ Gagal/Expired:                      │
  │     Status → 'dibatalkan'              │
  │     Stok buku dikembalikan             │
  └───────────────────────────────────────┘
```

**Webhook Midtrans (Server-to-Server):**
```
Midtrans Server ──── POST /midtrans/notification ────► Laravel
                                                          │
                     ┌────────────────────────────────────┤
                     ▼                    ▼                ▼
               capture/            pending          deny/cancel/
               settlement                           expire
                     │                    │                │
                     ▼                    ▼                ▼
              Status: diproses    Status: menunggu   Status: dibatalkan
              Bayar: valid        (tidak berubah)    Bayar: invalid
                                                     Stok: dikembalikan
```

> **Rani** klik "Bayar Sekarang", popup Midtrans muncul. Dia pilih GoPay, scan QR code, dan pembayaran berhasil. Sistem otomatis mencatat pembayaran, mengubah status pesanan ke "Diproses", dan mengirim notifikasi ke inbox Rani.

---

### 8. 📋 Riwayat Pesanan

```
┌────────────────────────────────────────────────────────────┐
│                PESANAN SAYA (/orders)                        │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ #5     07 Apr 2026    Rp 350.000                     │   │
│  │                                                       │   │
│  │ 📖 Buku A × 3                         Rp 255.000     │   │
│  │ 📖 Buku B × 1                         Rp  95.000     │   │
│  │                                                       │   │
│  │ Status: [🔄 Diproses]     Bayar: [✓ Valid]           │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ #4     06 Apr 2026    Rp 95.000                      │   │
│  │                                                       │   │
│  │ 📖 Buku C × 1                         Rp  95.000     │   │
│  │                                                       │   │
│  │ Status: [⏳ Menunggu]     Bayar: [Belum Bayar]       │   │
│  │                                                       │   │
│  │ [💳 Bayar Sekarang]  [✕ Batalkan Pesanan]            │   │
│  └──────────────────────────────────────────────────────┘   │
└────────────────────────────────────────────────────────────┘
```

**Alur Status Pesanan (sisi User):**
```
┌────────────┐     ┌────────────┐     ┌────────────┐     ┌────────────┐
│  Menunggu   │────►│  Diproses  │────►│  Dikirim   │────►│  Selesai   │
│  (unpaid)   │     │  (paid)    │     │  (shipped)  │     │  (done)    │
└──────┬─────┘     └────────────┘     └────────────┘     └────────────┘
       │
       ▼
┌────────────┐
│ Dibatalkan │  ◄── User bisa batalkan selama belum bayar
│ (cancelled)│      Stok otomatis dikembalikan
└────────────┘
```

> Pesanan **Rani** #5 sudah dibayar, statusnya "Diproses" — dia tinggal menunggu admin memproses dan mengirim. Pesanan #4 belum dibayar, dia bisa klik "Bayar Sekarang" untuk lanjut bayar atau "Batalkan" jika berubah pikiran.

---

### 9. 📬 Inbox & Kontak

```
KIRIM PESAN                              INBOX

┌──────────────────────┐      ┌───────────────────────────────┐
│ KONTAK (/contact)    │      │ INBOX (/inbox)                │
│                      │      │                                │
│ Subjek: [........]   │      │ ┌─────────────────────────┐   │
│ Pesan:               │      │ │ Subjek: Stok Buku A     │   │
│ [                 ]  │      │ │ 07 Apr 2026   [🗑 Hapus]│   │
│ [                 ]  │ ───► │ │                          │   │
│ [    Kirim Pesan  ]  │      │ │ Pesan Anda:              │   │
└──────────────────────┘      │ │ "Kapan stok Buku A...?"  │   │
                              │ │                          │   │
Admin menerima &              │ │ 💬 Balasan Admin:        │   │
membalas pesan                │ │ "Stok akan tersedia..."  │   │
        │                     │ └─────────────────────────┘   │
        │                     │                                │
        └─────────────────────│── 🔴 Badge notifikasi         │
         (notif di navbar)    │    muncul jika ada balasan     │
                              │    yang belum dibaca           │
                              └───────────────────────────────┘
```

**Alur Notifikasi Inbox:**
```
User kirim pesan (/contact/submit)
        │
        ▼
Admin baca & balas (/admin/pesan/{id}/reply)
        │
        ├── Simpan balasan_admin + tanggal_balas
        │
        ▼
dibaca_user = false (belum dibaca)
        │
        ▼
User layout navbar → 🔴 badge muncul (jumlah pesan belum dibaca)
        │
        ▼
User buka /inbox → Semua pesan ditandai dibaca_user = true
        │
        ▼
Badge hilang
```

> **Rani** punya pertanyaan tentang stok buku. Dia buka halaman Kontak, tulis pesan, dan kirim. Beberapa saat kemudian, muncul badge 🔴 di menu "Pesan" navbar. Dia klik dan melihat balasan admin di inbox-nya. Setelah dibaca, badge otomatis hilang.

---

### 10. 👤 Profil Pengguna

```
┌──────────────────────────────────────┐
│          PROFIL (/profile)            │
│                                       │
│  ┌─────────────────────────────────┐ │
│  │ Avatar: [R]                     │ │
│  │ Nama:    Rani                   │ │
│  │ Email:   rani@email.com         │ │
│  │ No. HP:  0812xxxxxxxx          │ │
│  │ Alamat:  Jl. Merdeka No. 10    │ │
│  │ Role:    User                   │ │
│  └─────────────────────────────────┘ │
└──────────────────────────────────────┘
```

---

## 🗺 Flowchart Lengkap — Perjalanan Sebagai Admin

Berikut alur dari sudut pandang **Admin** bernama **Pak Budi**.

---

### 1. 📊 Dashboard Admin

```
┌────────────────────────────────────────────────────────────┐
│              DASHBOARD (/admin/dashboard)                    │
│                                                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌───────────────┐  │
│  │ 11       │ │ 4        │ │ 1        │ │ Rp 350.000    │  │
│  │Total Buku│ │Total     │ │Dibatalkan│ │Total          │  │
│  │          │ │Pesanan   │ │          │ │Pendapatan     │  │
│  └──────────┘ └──────────┘ └──────────┘ └───────────────┘  │
│                                                              │
│  ┌─────────────────────────────┐  ┌────────────────────┐    │
│  │ 📈 Pendapatan 6 Bulan       │  │ 🍩 Distribusi      │    │
│  │    (Line Chart)              │  │    Status Pesanan  │    │
│  │                              │  │    (Doughnut)      │    │
│  │    ╱╲     ╱╲                │  │                     │    │
│  │   ╱  ╲   ╱  ╲              │  │  ● Selesai          │    │
│  │  ╱    ╲_╱    ╲             │  │  ● Diproses         │    │
│  │ ╱              ╲            │  │  ● Menunggu         │    │
│  └─────────────────────────────┘  │  ● Dibatalkan       │    │
│                                    └────────────────────┘    │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 📊 Jumlah Pesanan 6 Bulan (Bar Chart)               │    │
│  │  █                                                   │    │
│  │  █         █                                        │    │
│  │  █    █    █    █         █                          │    │
│  │  Nov  Des  Jan  Feb  Mar  Apr                        │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                              │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 🕐 Pesanan Terbaru (10 terakhir)                    │    │
│  │ ID | Pelanggan | Tanggal | Total | Status | Aksi    │    │
│  │ #5 | Rani      | 07 Apr  | 350K  | Proses | 👁🗑  │    │
│  │ #4 | Hafiz     | 07 Apr  | 95K   | Batal  | 👁🗑  │    │
│  └─────────────────────────────────────────────────────┘    │
└────────────────────────────────────────────────────────────┘
```

> **Pak Budi** login sebagai admin dan melihat dashboard. Semua angka dan grafik diambil dari database secara real-time: total buku, pesanan, pendapatan dari pembayaran valid, tren 6 bulan terakhir.

---

### 2. 📚 Kelola Buku & Kategori

```
┌─────────────────────────────────┐
│ SIDEBAR ADMIN                   │
│                                  │
│  📊 Dashboard                   │
│                                  │
│  ── MANAJEMEN BUKU ──          │
│  📂 Kategori                    │
│  📚 Kelola Buku                 │
│                                  │
│  ── TRANSAKSI ──                │
│  📦 Pesanan                     │
│                                  │
│  ── PENGGUNA ──                 │
│  👥 Pengguna                    │
│                                  │
│  ── KOMUNIKASI ──               │
│  💬 Pesan Kontak                │
└─────────────────────────────────┘
```

**CRUD Kategori:**
```
/admin/kategori
      │
      ├── [+ Tambah Kategori] → /admin/kategori/create
      │                              │
      │                    POST /admin/kategori (store)
      │
      ├── [✏️ Edit] → /admin/kategori/{id}/edit
      │                       │
      │             PUT /admin/kategori/{id} (update)
      │
      └── [🗑 Hapus] → DELETE /admin/kategori/{id}
                              │
                    ❌ Ditolak jika kategori masih punya buku
```

**CRUD Buku:**
```
/admin/buku
      │
      ├── 🔍 Cari + 📂 Filter Kategori
      │
      ├── [+ Tambah Buku] → /admin/buku/create
      │       │
      │       └── Form: judul, ISBN, penulis, penerbit,
      │            tahun, stok, harga, deskripsi, cover URL,
      │            kategori (dropdown)
      │            │
      │       POST /admin/buku (store)
      │
      ├── [✏️ Edit] → /admin/buku/{id}/edit
      │       │
      │   PUT /admin/buku/{id} (update)
      │
      └── [🗑 Hapus] → DELETE /admin/buku/{id}
                              │
                    ❌ Ditolak jika buku ada di keranjang/pesanan
```

---

### 3. 📦 Kelola Pesanan & Update Status

```
/admin/pesanan ──── Daftar semua pesanan
      │              (cari + filter status)
      │
      ▼
/admin/pesanan/{id} ──── Detail Pesanan
      │
      │  ┌───────────────────────────────────────┐
      │  │ Info Pesanan  │  Info Pelanggan        │
      │  │ ID: #5        │  Nama: Rani            │
      │  │ Tgl: 07 Apr   │  Email: rani@mail.com  │
      │  │ Status: ⏳     │  HP: 0812xxx           │
      │  ├───────────────┤                         │
      │  │ Item Pesanan  │  ┌──────────────────┐  │
      │  │ Buku A × 3    │  │ UPDATE STATUS    │  │
      │  │ Buku B × 1    │  │                  │  │
      │  │               │  │ [Proses Pesanan] │  │
      │  │ Total: 350K   │  │ [Batalkan]       │  │
      │  └───────────────┘  └──────────────────┘  │
      │                                            │
      └────────────────────────────────────────────┘
```

**Alur Update Status Pesanan (Admin):**
```
                    ┌────────────┐
                    │  Menunggu   │
                    │  (unpaid)   │
                    └──────┬─────┘
                           │
              ┌────────────┤
              ▼            ▼
     ┌──────────────┐  ┌────────────┐
     │ [Proses       │  │ [Batalkan] │
     │  Pesanan]     │  └────────────┘
     └───────┬──────┘
             ▼
     ┌────────────┐
     │  Diproses   │
     └──────┬─────┘
            │
   ┌────────┤
   ▼        ▼
┌────────┐ ┌────────────┐
│[Tandai │ │ [Batalkan] │
│Dikirim]│ └────────────┘
└───┬────┘
    ▼
┌────────────┐
│  Dikirim    │
└──────┬─────┘
       ▼
┌────────────────┐
│ [Tandai Selesai]│
└───────┬────────┘
        ▼
┌────────────┐
│  Selesai ✓  │ ──► Pembayaran otomatis divalidasi
│  (final)    │     Pendapatan tercatat di dashboard
└────────────┘
```

> **Pak Budi** melihat pesanan #5 dari Rani sudah dibayar. Dia klik "Proses Pesanan" → lalu setelah packing, klik "Tandai Dikirim" → setelah kurir konfirmasi, klik "Tandai Selesai". Pendapatan Rp 350.000 langsung masuk ke grafik dashboard.

---

### 4. 👥 Kelola Pengguna

```
/admin/users
      │
      ├── 🔍 Cari nama/email
      ├── 📂 Filter: Semua / Admin / User
      │
      │  ┌──────────────────────────────────────────┐
      │  │ ID │ Nama  │ Email       │ Role   │ Aksi │
      │  │ 1  │ Budi  │ admin@...   │ Admin  │  -   │
      │  │ 2  │ Rani  │ rani@...    │ User   │ [🔄] │
      │  │ 3  │ Sari  │ sari@...    │ User   │ [🔄] │
      │  └──────────────────────────────────────────┘
      │
      └── [🔄 Ubah Role] → POST /admin/users/{id}/update-role
                                    │
                          Toggle: user ↔ admin
                          (tidak bisa ubah role sendiri)
```

---

### 5. 💬 Kelola Pesan Kontak

```
/admin/pesan ──── Daftar pesan masuk
      │            (cari + filter dibaca/belum)
      │
      ▼
/admin/pesan/{id} ──── Detail Pesan
      │
      │  ┌──────────────────────────────────────┐
      │  │ Dari: Rani (rani@email.com)          │
      │  │ Subjek: Stok Buku A                  │
      │  │ Tanggal: 07 Apr 2026                 │
      │  │                                       │
      │  │ Pesan:                                │
      │  │ "Kapan stok Buku A tersedia lagi?"    │
      │  │                                       │
      │  │ ─────────────────────────────         │
      │  │ Balas:                                │
      │  │ [                              ]      │
      │  │ [         Kirim Balasan        ]      │
      │  └──────────────────────────────────────┘
      │
      └── POST /admin/pesan/{id}/reply
               │
               ▼
          Balasan tersimpan → Muncul di inbox user
          dengan badge notifikasi 🔴
```

---

## 🔄 Alur Lengkap End-to-End (Ringkasan)

```
╔═══════════════════════════════════════════════════════════════════════════╗
║                        ALUR LENGKAP BOOK.COM                             ║
╠═══════════════════════════════════════════════════════════════════════════╣
║                                                                          ║
║  GUEST                                                                   ║
║  ═════                                                                   ║
║  Landing Page → Register/Login (Email atau Google)                       ║
║       │                                                                  ║
║       ▼                                                                  ║
║  USER                                                                    ║
║  ════                                                                    ║
║  Beranda → Katalog Buku → Detail Buku → ❤️ Favorit / ⭐ Review          ║
║       │                        │                                         ║
║       │                   🛒 Tambah ke Keranjang                         ║
║       │                        │                                         ║
║       │                   Keranjang (ubah qty / hapus)                   ║
║       │                        │                                         ║
║       │                   Checkout (pilih alamat)                        ║
║       │                        │                                         ║
║       │                   Pembayaran (Midtrans Snap)                    ║
║       │                        │                                         ║
║       │              ┌─────────┴──────────┐                              ║
║       │              ▼                    ▼                               ║
║       │         ✅ Berhasil          ❌ Gagal/Batal                       ║
║       │         Status: Diproses     Status: Dibatalkan                  ║
║       │              │               Stok dikembalikan                   ║
║       │              ▼                                                   ║
║       │         Riwayat Pesanan                                          ║
║       │                                                                  ║
║       ├── Kontak → Kirim Pesan → Admin Balas → 🔴 Inbox                 ║
║       └── Profil                                                         ║
║                                                                          ║
║  ADMIN                                                                   ║
║  ═════                                                                   ║
║  Dashboard (statistik + grafik real-time)                                ║
║       │                                                                  ║
║       ├── Kelola Kategori (CRUD)                                        ║
║       ├── Kelola Buku (CRUD + cover URL)                                ║
║       ├── Kelola Pesanan (lihat + update status + hapus)                ║
║       │       │                                                          ║
║       │       └── Menunggu → Diproses → Dikirim → Selesai ✓             ║
║       │           (selesai = pendapatan tercatat)                        ║
║       │                                                                  ║
║       ├── Kelola Pengguna (lihat + ubah role)                           ║
║       └── Kelola Pesan (baca + balas + hapus)                           ║
║                                                                          ║
╚═══════════════════════════════════════════════════════════════════════════╝
```

---

## 🗃 Struktur Database (ERD Ringkas)

```
┌──────────────┐       ┌──────────────┐
│ kategori_buku│       │    users     │
│──────────────│       │──────────────│
│ id_kategori  │◄──┐   │ id_user      │
│ nama_kategori│   │   │ nama         │
│ deskripsi    │   │   │ email        │
└──────────────┘   │   │ password     │
                   │   │ role         │
┌──────────────┐   │   │ alamat       │
│     buku     │   │   │ no_hp        │
│──────────────│   │   └──────┬───────┘
│ id_buku      │   │          │
│ id_kategori  │───┘    ┌─────┴──────────────────────────┐
│ judul        │        │     │          │        │       │
│ isbn         │        ▼     ▼          ▼        ▼       ▼
│ penulis      │   ┌────────┐┌────────┐┌───────┐┌──────┐┌──────────┐
│ penerbit     │   │keranjng││pesanan ││favorit││ulasan││pesan     │
│ harga        │   │────────││────────││───────││──────││kontak    │
│ stok         │   │id_krnj ││id_pesn ││id_fav ││id_uls││──────────│
│ deskripsi    │   │id_user ││id_user ││id_user││id_usr││id_pesan  │
│ cover        │   │id_buku ││total   ││id_buku││id_bku││id_user   │
│ tahun_terbit │   │qty     ││status  │└───────┘│rating││subjek    │
└──────┬───────┘   │tanggal ││snap_tkn│         │komtr ││isi_pesan │
       │           └────────┘│tanggal │         └──────┘│balasan   │
       │                     └───┬────┘                  │dibaca_usr│
       │              ┌──────────┴──────────┐            └──────────┘
       │              ▼                     ▼
       │     ┌──────────────┐      ┌─────────────┐      ┌───────────┐
       │     │pesanan_detail│      │ pembayaran  │      │  alamat   │
       │     │──────────────│      │─────────────│      │pengiriman │
       └────►│ id_detail    │      │ id_bayar    │      │───────────│
             │ id_pesanan   │      │ id_pesanan  │      │ id_alamat │
             │ id_buku      │      │ midtrans_id │      │ id_user   │
             │ qty          │      │ metode      │      │ label     │
             │ harga_satuan │      │ jumlah      │      │ nama      │
             └──────────────┘      │ status      │      │ no_hp     │
                                   │ tanggal     │      │ alamat    │
                                   └─────────────┘      │ is_default│
                                                        └───────────┘
```

---

## 🚀 Cara Menjalankan

1. **Clone & Install:**
   ```bash
   git clone <repo-url> Online_book
   cd Online_book
   composer install
   npm install
   ```

2. **Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Konfigurasi `.env`:
   ```env
   DB_DATABASE=online_book
   DB_USERNAME=root
   DB_PASSWORD=

   MIDTRANS_MERCHANT_ID=your_merchant_id
   MIDTRANS_CLIENT_KEY=your_client_key
   MIDTRANS_SERVER_KEY=your_server_key

   GOOGLE_CLIENT_ID=your_google_client_id
   GOOGLE_CLIENT_SECRET=your_google_client_secret
   GOOGLE_REDIRECT_URI=http://localhost/Online_book/public/callback/google
   ```

3. **Database:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Jalankan:**
   ```
   Buka: http://localhost/Online_book/public
   ```

---

## 👥 Akun Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@tokobuku.com | password |
| User | user@tokobuku.com | password |

---

*Built with ❤️ using Laravel 11 — Book.com © 2026*
