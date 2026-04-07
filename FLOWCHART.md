# 🗺 Flowchart Keseluruhan Sistem — Book.com

```
╔══════════════════════════════════════════════════════════════════════════════════════════╗
║                                                                                          ║
║                              🌐 WEBSITE BOOK.COM                                         ║
║                         http://localhost/Online_book/public                               ║
║                                                                                          ║
╠══════════════════════════════════════════════════════════════════════════════════════════╣
║                                                                                          ║
║                              ┌─────────────────────┐                                     ║
║                              │   LANDING PAGE (/)   │                                     ║
║                              │                      │                                     ║
║                              │  • Hero + 3D Animasi │                                     ║
║                              │  • Jenis Buku        │                                     ║
║                              │  • 8 Rekomendasi     │                                     ║
║                              │  • Tentang Kami      │                                     ║
║                              └──────────┬───────────┘                                     ║
║                                         │                                                 ║
║                            ┌────────────┴────────────┐                                    ║
║                            ▼                         ▼                                    ║
║                    ┌──────────────┐          ┌──────────────┐                              ║
║                    │   REGISTER   │          │    LOGIN     │                              ║
║                    │  /register   │          │   /login     │                              ║
║                    │              │          │              │                              ║
║                    │ Nama, Email, │          │ Email + Pass │                              ║
║                    │ Password,    │          │     atau     │                              ║
║                    │ Konfirmasi   │          │ Google OAuth │                              ║
║                    └──────┬───────┘          └──────┬───────┘                              ║
║                           │                         │                                     ║
║                           └────────────┬────────────┘                                     ║
║                                        ▼                                                  ║
║                              ┌───────────────────┐                                        ║
║                              │   CEK ROLE USER   │                                        ║
║                              └────────┬──────────┘                                        ║
║                                       │                                                   ║
║                    ┌──────────────────┬┴──────────────────┐                                ║
║                    ▼                                      ▼                                ║
║  ╔═════════════════════════════════╗  ╔═════════════════════════════════╗                  ║
║  ║         ROLE: USER             ║  ║        ROLE: ADMIN              ║                  ║
║  ╚════════════════╤════════════════╝  ╚════════════════╤════════════════╝                  ║
║                   ▼                                    ▼                                  ║
║  ┌─────────────────────────────┐      ┌─────────────────────────────┐                     ║
║  │      BERANDA (/home)        │      │   DASHBOARD                 │                     ║
║  │                             │      │   (/admin/dashboard)        │                     ║
║  │  • 10 Buku Terbaru         │      │                             │                     ║
║  │  • Daftar Kategori         │      │  ┌────┐┌────┐┌────┐┌────┐  │                     ║
║  │                             │      │  │Buku││Psnn││Btlk││Rp$$│  │                     ║
║  └──────────┬──────────────────┘      │  └────┘└────┘└────┘└────┘  │                     ║
║             │                         │                             │                     ║
║             ▼                         │  📈 Chart Pendapatan 6 Bln  │                     ║
║  ┌─────────────────────────────┐      │  📊 Chart Pesanan 6 Bln     │                     ║
║  │   KATALOG BUKU (/books)    │      │  🍩 Distribusi Status       │                     ║
║  │                             │      │  📋 10 Pesanan Terbaru      │                     ║
║  │  🔍 Cari (judul/penulis)   │      └──────────┬──────────────────┘                     ║
║  │  📂 Filter Kategori        │                  │                                        ║
║  │  📄 Paginasi (12/hal)      │                  │                                        ║
║  └──────────┬──────────────────┘     ┌───────────┼───────────┬──────────┐                 ║
║             │                        ▼           ▼           ▼          ▼                 ║
║             ▼               ┌────────────┐┌────────────┐┌────────┐┌──────────┐            ║
║  ┌─────────────────────┐    │  KELOLA    ││  KELOLA    ││KELOLA  ││ KELOLA   │            ║
║  │ DETAIL BUKU         │    │  BUKU      ││  PESANAN   ││USER    ││ PESAN    │            ║
║  │ (/book/{id})        │    │            ││            ││        ││ KONTAK   │            ║
║  │                     │    │/admin/buku ││/admin/     ││/admin/ ││/admin/   │            ║
║  │ • Info Lengkap      │    │/admin/     ││ pesanan    ││ users  ││ pesan    │            ║
║  │ • Cover, Harga, Stok│    │ kategori   ││            ││        ││          │            ║
║  │ • Deskripsi         │    └─────┬──────┘└─────┬──────┘└───┬────┘└────┬─────┘            ║
║  │                     │          │             │           │          │                   ║
║  │ ┌────────────────┐  │          ▼             ▼           ▼          ▼                   ║
║  │ │❤️ Favorit(AJAX)│  │   ┌────────────┐ ┌──────────┐ ┌────────┐ ┌────────┐             ║
║  │ │⭐ Review+Rating│  │   │• Tambah    │ │• Lihat   │ │• Cari  │ │• Baca  │             ║
║  │ │🛒 + Keranjang  │  │   │• Edit      │ │  Detail  │ │• Filter│ │• Balas │             ║
║  │ └────────────────┘  │   │• Hapus     │ │• Update  │ │  Role  │ │• Hapus │             ║
║  └──────────┬──────────┘   │• Cari      │ │  Status  │ │• Ubah  │ └───┬────┘             ║
║             │              │• Filter    │ │• Hapus   │ │  Role  │     │                   ║
║             ▼              └────────────┘ └────┬─────┘ └────────┘     │                   ║
║  ┌─────────────────────┐                       │                      │                   ║
║  │ KERANJANG (/cart)   │                       ▼                      │                   ║
║  │                     │          ┌─────────────────────┐             │                   ║
║  │ • Ubah Qty (AJAX)   │          │  UPDATE STATUS      │             │                   ║
║  │ • Hapus Item (AJAX) │          │  PESANAN             │             │                   ║
║  │ • Badge 🛒 Navbar   │          │                      │             │                   ║
║  │ • Lihat Total       │          │ Menunggu             │             │                   ║
║  └──────────┬──────────┘          │   │                  │             │                   ║
║             │                     │   ├─► [Proses]       │             │                   ║
║             ▼                     │   └─► [Batalkan]     │             │                   ║
║  ┌─────────────────────┐          │                      │             │                   ║
║  │ CHECKOUT            │          │ Diproses             │             │                   ║
║  │ (/checkout)         │          │   │                  │             │                   ║
║  │                     │          │   ├─► [Kirim]        │             │                   ║
║  │ ① Pengiriman        │          │   └─► [Batalkan]     │             │                   ║
║  │                     │          │                      │             │                   ║
║  │ • Pilih Alamat      │          │ Dikirim              │             │                   ║
║  │ • Tambah Alamat     │          │   │                  │             │                   ║
║  │ • Edit/Hapus Alamat │          │   └─► [Selesaikan]   │             │                   ║
║  │ • Review Pesanan    │          │                      │             │                   ║
║  └──────────┬──────────┘          │ Selesai ✓            │             │                   ║
║             │                     │   └─► Pembayaran     │             │                   ║
║             │                     │       divalidasi     │             │                   ║
║             │                     │       otomatis       │             │                   ║
║             │                     │                      │             │                   ║
║             ▼                     │ Dibatalkan ✗         │             │                   ║
║  ┌─────────────────────┐          └─────────────────────┘             │                   ║
║  │ PEMBAYARAN          │                    │                         │                   ║
║  │ (/payment/{id})     │                    │                         │                   ║
║  │                     │                    │     ┌────────────────────┘                   ║
║  │ ② Pembayaran        │                    │     │                                       ║
║  │                     │                    │     │  Admin balas pesan                     ║
║  │ • Midtrans Snap     │                    │     │         │                              ║
║  │   Popup             │                    │     │         ▼                              ║
║  │ • GoPay, Transfer,  │                    │     │  User terima notif                    ║
║  │   CC, dll           │                    │     │  🔴 Badge di navbar                    ║
║  └──────────┬──────────┘                    │     │         │                              ║
║             │                               │     │         ▼                              ║
║    ┌────────┴─────────┐                     │     │  ┌────────────────┐                    ║
║    ▼                  ▼                     │     │  │ INBOX (/inbox) │                    ║
║ ┌────────┐      ┌──────────┐                │     │  │ • Baca balasan │                    ║
║ │✅ Bayar │      │❌ Gagal/  │                │     │  │ • Hapus pesan  │                    ║
║ │Berhasil│      │  Expired │                │     │  │ • Badge hilang │                    ║
║ └───┬────┘      └────┬─────┘                │     │  └────────────────┘                    ║
║     │                │                      │     │                                        ║
║     ▼                ▼                      │     │                                        ║
║ ┌──────────┐   ┌───────────┐                │     │                                        ║
║ │Pembayaran│   │ Pesanan   │                │     │                                        ║
║ │tersimpan │   │ dibatalkan│                │     │                                        ║
║ │Status:   │   │ Stok      │                │     │                                        ║
║ │diproses  │   │ kembali   │                │     │                                        ║
║ │Notif ke  │   └───────────┘                │     │                                        ║
║ │inbox user│                                │     │                                        ║
║ └────┬─────┘          ▲                     │     │                                        ║
║      │                │                     │     │                                        ║
║      │    ┌───────────┘                     │     │                                        ║
║      │    │  Webhook Midtrans               │     │                                        ║
║      │    │  (deny/cancel/expire)           │     │                                        ║
║      │    │                                 │     │                                        ║
║      ▼    │                                 │     │                                        ║
║ ┌─────────┴────────────────┐                │     │                                        ║
║ │  RIWAYAT PESANAN         │                │     │                                        ║
║ │  (/orders)               │                │     │                                        ║
║ │                          │                │     │                                        ║
║ │  • Lihat semua pesanan   │                │     │                                        ║
║ │  • Status + badge warna  │                │     │                                        ║
║ │  • Bayar (jika menunggu) │────────────────┘     │                                        ║
║ │  • Batalkan (jika unpaid)│                      │                                        ║
║ └──────────────────────────┘                      │                                        ║
║                                                   │                                        ║
║  ┌─────────────────────┐     ┌─────────────────┐  │                                        ║
║  │ KONTAK (/contact)   │────►│ Pesan tersimpan  │──┘                                        ║
║  │                     │     │ di database      │                                           ║
║  │ • Subjek + Isi pesan│     │                  │                                           ║
║  │ • Kirim ke admin    │     │ Admin bisa baca  │                                           ║
║  └─────────────────────┘     │ dan balas        │                                           ║
║                              └──────────────────┘                                           ║
║                                                                                             ║
║  ┌─────────────────────┐                                                                    ║
║  │ PROFIL (/profile)   │                                                                    ║
║  │                     │                                                                    ║
║  │ • Lihat data diri   │                                                                    ║
║  │ • Nama, Email, HP   │                                                                    ║
║  │ • Alamat, Role      │                                                                    ║
║  └─────────────────────┘                                                                    ║
║                                                                                             ║
╚═════════════════════════════════════════════════════════════════════════════════════════════╝
```

---

## 🔗 Alur Koneksi Antar Sistem (Simplified)

```
┌─────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│ LANDING │────►│ AUTH     │────►│ BERANDA  │────►│ KATALOG  │────►│ DETAIL   │────►│KERANJANG │
│         │     │Login/Reg │     │          │     │  BUKU    │     │  BUKU    │     │          │
└─────────┘     └──────────┘     └──────────┘     └──────────┘     └──────────┘     └────┬─────┘
                     │                                    ▲              │                │
                     │                                    │         ❤️Favorit             │
                     │                                    │         ⭐Review              │
                     │                                    │                               ▼
                     │           ┌──────────┐     ┌───────┴──┐     ┌──────────┐     ┌──────────┐
                     │           │  INBOX   │◄────│  KONTAK  │     │  PROFIL  │     │ CHECKOUT │
                     │           │          │     │          │     │          │     │          │
                     │           └──────────┘     └──────────┘     └──────────┘     └────┬─────┘
                     │                ▲                                                   │
                     │                │ notifikasi                                        ▼
                     │                │ balasan                                     ┌──────────┐
                     │           ┌────┴─────┐                                      │PEMBAYARAN│
                     │           │  ADMIN   │                                      │ Midtrans │
                     │           │  BALAS   │                                      └────┬─────┘
                     │           │  PESAN   │                                           │
                     ▼           └──────────┘                                           ▼
              ┌──────────────┐        ▲                                          ┌──────────┐
              │ ADMIN        │        │                                          │ RIWAYAT  │
              │ DASHBOARD    │────────┤                                          │ PESANAN  │
              │              │        │                                          └────┬─────┘
              │ Stats+Charts │   ┌────┴─────┐     ┌──────────┐     ┌──────────┐      │
              └──────┬───────┘   │  KELOLA  │     │  KELOLA  │     │  KELOLA  │      │
                     │           │  PESANAN │◄────│  BUKU &  │     │  USER    │      │
                     │           │  +Status │     │ KATEGORI │     │  +Role   │      │
                     └───────────┴──────────┘     └──────────┘     └──────────┘      │
                           │                                                          │
                           │            Admin update status                           │
                           │◄─────────────────────────────────────────────────────────┘
                           │  Menunggu → Diproses → Dikirim → Selesai
                           │  (selesai = pendapatan masuk dashboard)
                           ▼
                    ┌──────────────┐
                    │  PENDAPATAN  │
                    │  TERCATAT    │
                    │  DI DASHBOARD│
                    └──────────────┘
```

---

## 📊 Alur Data & Status

```
══════════════════════════════════════════════════════════════════════════
                        ALUR PESANAN (ORDER LIFECYCLE)
══════════════════════════════════════════════════════════════════════════

  USER                          SISTEM                         ADMIN
  ════                          ══════                         ═════

  Tambah ke Keranjang ──────►  Simpan di tabel keranjang
  (AJAX, cek stok)             Badge 🛒 update

  Klik Checkout ────────────►  Tampilkan halaman checkout
                               Muat alamat pengiriman

  Pilih Alamat +
  Buat Pesanan ─────────────►  ┌─ Buat Pesanan (menunggu)
                               ├─ Buat PesananDetail
                               ├─ Kurangi stok buku
                               └─ Hapus keranjang

  Bayar via Midtrans ───────►  ┌─ Generate Snap Token
                               ├─ Tampilkan popup Midtrans
                               │
                               ├─ Jika BERHASIL:
                               │  ├─ Simpan Pembayaran
                               │  ├─ Status → diproses
                               │  └─ Kirim notif inbox
                               │
                               ├─ Jika PENDING:
                               │  └─ Tunggu webhook
                               │
                               └─ Jika GAGAL:
                                  ├─ Status → dibatalkan
                                  └─ Stok dikembalikan

  Lihat di /orders                                     Lihat di /admin/pesanan
       │                                                        │
       │                                               Klik [Proses Pesanan]
       │                                               ─────────────────────►
       │                                               Status → diproses
       │
       │                                               Klik [Tandai Dikirim]
       │                                               ─────────────────────►
       │                                               Status → dikirim
       │
       │                                               Klik [Tandai Selesai]
       │                                               ─────────────────────►
       │                                               Status → selesai
       │                                               Pembayaran → valid
       │                                               Pendapatan tercatat
       │                                                        │
       ▼                                                        ▼
  Status update terlihat                               Dashboard update:
  real-time di /orders                                 📈 Chart pendapatan
                                                       📊 Chart pesanan
                                                       💰 Total pendapatan

══════════════════════════════════════════════════════════════════════════
                        ALUR KOMUNIKASI (MESSAGING)
══════════════════════════════════════════════════════════════════════════

  USER                          SISTEM                         ADMIN
  ════                          ══════                         ═════

  Kirim pesan (/contact) ───►  Simpan pesan_kontak     ───►  Muncul di
                               (subjek + isi_pesan)           /admin/pesan

                                                       ◄───  Baca pesan

                                                       ◄───  Tulis balasan
                               Simpan balasan_admin    ───►  POST reply
                               dibaca_user = false

  🔴 Badge muncul di  ◄──────  View Composer cek
  navbar "Pesan"               pesan belum dibaca

  Buka /inbox ──────────────►  Tandai semua dibaca
                               dibaca_user = true
  Baca balasan admin
  Badge hilang

  Hapus pesan (opsional) ───►  Hapus dari database

══════════════════════════════════════════════════════════════════════════
                        ALUR AUTENTIKASI
══════════════════════════════════════════════════════════════════════════

  VISITOR                       SISTEM                    GOOGLE
  ═══════                       ══════                    ══════

  ┌─ OPSI 1: Register ──────►  Hash password
  │  (nama, email, pass)       Buat user (role: user)
  │                            Auto-login
  │                            Redirect → /home
  │
  ├─ OPSI 2: Login ─────────►  Auth::attempt()
  │  (email + password)        Session regenerate
  │                            Cek role → redirect
  │
  └─ OPSI 3: Google OAuth ──►  Socialite redirect  ───►  Google consent
                                                    ◄───  User data
                               Cari/buat user
                               Auto-login
                               Cek role → redirect

  Logout ───────────────────►  Auth::logout()
                               Session invalidate
                               Redirect → landing

══════════════════════════════════════════════════════════════════════════
                        ALUR STOK BUKU
══════════════════════════════════════════════════════════════════════════

                            STOK BUKU
                               │
              ┌────────────────┼────────────────┐
              ▼                ▼                ▼
         BERKURANG        BERTAMBAH         DICEK
              │                │                │
   ┌──────────┤         ┌──────┤          ┌─────┤
   ▼          ▼         ▼      ▼          ▼     ▼
 Checkout   Admin     User   Webhook   Tambah  Update
 process    edit      cancel  Midtrans  cart    cart qty
 (buat      buku     pesanan  (gagal/   (cek   (cek
 pesanan)   (manual)          expire)   ≤stok)  ≤stok)

══════════════════════════════════════════════════════════════════════════
                        ALUR PEMBAYARAN MIDTRANS
══════════════════════════════════════════════════════════════════════════

  BROWSER USER              LARAVEL SERVER            MIDTRANS SERVER
  ════════════              ══════════════            ═══════════════

  GET /payment/{id} ──────► Generate Snap Token ────► API Request
                                                 ◄──── Snap Token

  Klik "Bayar"
  Snap Popup muncul ◄──────  Render snap.js

  Pilih metode bayar ─────────────────────────────►  Proses bayar

  Callback success ◄──────────────────────────────── Hasil bayar
       │
       ▼
  POST /payment/                                     POST /midtrans/
  {id}/process ──────────►  Simpan Pembayaran        notification
                            Status → diproses    ◄──  (async webhook)
                            Notif inbox               │
                                                      ├─ settlement → valid
                            ◄─────────────────────────┤
                                                      ├─ pending → tunggu
  Redirect /orders                                    │
                                                      └─ cancel → batalkan
                                                         + kembalikan stok
```

---

## 🧭 Navigasi Lengkap

```
╔══════════════════════════════════════════════════════════════════╗
║                         NAVBAR USER                              ║
╠══════════════════════════════════════════════════════════════════╣
║                                                                  ║
║  ┌──────┐ ┌──────┐ ┌────────┐ ┌──────┐ ┌────────────────────┐  ║
║  │ Home │ │ Buku │ │ Kontak │ │ 🛒 3 │ │ 👤 Rani         ▼ │  ║
║  └──┬───┘ └──┬───┘ └───┬────┘ └──┬───┘ │  ┌──────────────┐ │  ║
║     │        │         │         │      │  │ Pesanan      │ │  ║
║     ▼        ▼         ▼         ▼      │  │ Pesan 🔴2    │ │  ║
║   /home    /books   /contact    /cart   │  │ Profil       │ │  ║
║                                         │  │ Logout       │ │  ║
║                                         │  └──────────────┘ │  ║
║                                         └────────────────────┘  ║
╠══════════════════════════════════════════════════════════════════╣
║                       SIDEBAR ADMIN                              ║
╠══════════════════════════════════════════════════════════════════╣
║                                                                  ║
║  📊 Dashboard ─────────────── /admin/dashboard                   ║
║                                                                  ║
║  ── MANAJEMEN BUKU ──                                           ║
║  📂 Kategori ──────────────── /admin/kategori                    ║
║  📚 Kelola Buku ───────────── /admin/buku                        ║
║                                                                  ║
║  ── TRANSAKSI ──                                                ║
║  📦 Pesanan ───────────────── /admin/pesanan                     ║
║                                                                  ║
║  ── PENGGUNA ──                                                 ║
║  👥 Pengguna ──────────────── /admin/users                       ║
║                                                                  ║
║  ── KOMUNIKASI ──                                               ║
║  💬 Pesan Kontak ──────────── /admin/pesan                       ║
║                                                                  ║
╚══════════════════════════════════════════════════════════════════╝
```

---

## 🔁 Semua Route (Peta URL Lengkap)

```
/                                    GET    Landing page
/login                               GET    Form login
/login                               POST   Proses login
/register                            GET    Form register
/register                            POST   Proses register
/logout                              POST   Logout
/auth/google                         GET    Google OAuth redirect
/callback/google                     GET    Google OAuth callback
/midtrans/notification               POST   Webhook Midtrans
│
├── /home                            GET    Beranda user
├── /books                           GET    Katalog buku
├── /book/{id}                       GET    Detail buku
├── /book/{id}/favorite              POST   Toggle favorit (AJAX)
├── /book/{id}/review                POST   Kirim review
├── /cart                            GET    Keranjang
├── /api/cart/add                    POST   Tambah keranjang (AJAX)
├── /api/cart/count                  GET    Hitung badge (AJAX)
├── /api/cart/update                 POST   Update qty (AJAX)
├── /api/cart/delete/{id}            DELETE Hapus item (AJAX)
├── /checkout                        GET    Halaman checkout
├── /checkout/process                POST   Proses checkout
├── /payment/{id}                    GET    Halaman pembayaran
├── /payment/{id}/process            POST   Proses setelah bayar (AJAX)
├── /orders                          GET    Riwayat pesanan
├── /orders/{id}/cancel              POST   Batalkan pesanan
├── /profile                         GET    Profil user
├── /inbox                           GET    Inbox notifikasi
├── /inbox/{id}                      DELETE Hapus pesan inbox
├── /contact                         GET    Form kontak
├── /contact/submit                  POST   Kirim pesan kontak
├── /address/store                   POST   Tambah alamat
├── /address/update/{id}             POST   Update alamat
├── /address/delete/{id}             DELETE Hapus alamat
│
└── /admin/
    ├── dashboard                    GET    Dashboard admin
    ├── kategori                     GET    List kategori
    ├── kategori/create              GET    Form tambah kategori
    ├── kategori                     POST   Simpan kategori
    ├── kategori/{id}/edit           GET    Form edit kategori
    ├── kategori/{id}                PUT    Update kategori
    ├── kategori/{id}                DELETE Hapus kategori
    ├── buku                         GET    List buku
    ├── buku/create                  GET    Form tambah buku
    ├── buku                         POST   Simpan buku
    ├── buku/{id}/edit               GET    Form edit buku
    ├── buku/{id}                    PUT    Update buku
    ├── buku/{id}                    DELETE Hapus buku
    ├── pesanan                      GET    List pesanan
    ├── pesanan/{id}                 GET    Detail pesanan
    ├── pesanan/{id}/status          PUT    Update status pesanan
    ├── pesanan/{id}                 DELETE Hapus pesanan
    ├── users                        GET    List users
    ├── users/{id}/update-role       POST   Ubah role user
    ├── pesan                        GET    List pesan
    ├── pesan/{id}                   GET    Detail pesan
    ├── pesan/{id}/reply             POST   Balas pesan
    └── pesan/{id}                   DELETE Hapus pesan
```
