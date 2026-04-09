<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\PesanKontak;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    /**
     * Nampilin halaman dashboard admin
     */
    public function dashboard()
    {
        // Ambil data statistik
        $totalBuku = Buku::count();
        $totalPesanan = Pesanan::count();
        $pesananDibatalkan = Pesanan::where('status', 'dibatalkan')->count();
        $totalPendapatan = Pembayaran::where('status_verifikasi', 'valid')->sum('jumlah');
        
        // Pendapatan bulanan 6 bulan terakhir (dari pembayaran yang udah valid)
        $monthlyRevenue = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->where('pesanan.tanggal_pesanan', '>=', now()->subMonths(5)->startOfMonth())
            ->select(
                DB::raw('MONTH(pesanan.tanggal_pesanan) as bulan'),
                DB::raw('YEAR(pesanan.tanggal_pesanan) as tahun'),
                DB::raw('SUM(pembayaran.jumlah) as total')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        // Jumlah pesanan per bulan 6 bulan terakhir
        $monthlyOrders = Pesanan::where('tanggal_pesanan', '>=', now()->subMonths(5)->startOfMonth())
            ->select(
                DB::raw('MONTH(tanggal_pesanan) as bulan'),
                DB::raw('YEAR(tanggal_pesanan) as tahun'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        // Bikin label dan data buat grafik 6 bulan terakhir
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];
        $bulanNama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $m = (int) $date->format('m');
            $y = (int) $date->format('Y');
            $chartLabels[] = $bulanNama[$m] . ' ' . $y;

            $rev = $monthlyRevenue->first(fn($r) => $r->bulan == $m && $r->tahun == $y);
            $chartRevenue[] = $rev ? (float) $rev->total : 0;

            $ord = $monthlyOrders->first(fn($o) => $o->bulan == $m && $o->tahun == $y);
            $chartOrders[] = $ord ? (int) $ord->total : 0;
        }

        // Distribusi status pesanan
        $statusCounts = Pesanan::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Ambil pesanan terbaru beserta relasi user
        $pesananTerbaru = Pesanan::with(['user', 'pembayaran'])
            ->orderBy('tanggal_pesanan', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalBuku',
            'totalPesanan', 
            'pesananDibatalkan',
            'totalPendapatan',
            'pesananTerbaru',
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'statusCounts'
        ));
    }
    
    /**
     * Nampilin daftar pesanan
     */
    public function indexPesanan(Request $request)
    {
        $query = Pesanan::with(['user', 'pembayaran']);

        // Cari berdasarkan nama user atau id pesanan
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id_pesanan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $pesanan = $query->orderBy('tanggal_pesanan', 'desc')->paginate(15);

        return view('admin.pesanan.index', compact('pesanan'));
    }

    /**
     * Nampilin detail pesanan
     */
    public function showPesanan($id)
    {
        $pesanan = Pesanan::with([
            'user',
            'pesananDetails.buku',
            'pembayaran'
        ])->findOrFail($id);
        
        return view('admin.pesanan-detail', compact('pesanan'));
    }

    /**
     * Update status pesanan
     */
    public function updateStatusPesanan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,dikirim,selesai,dibatalkan',
        ]);

        try {
            $pesanan = Pesanan::findOrFail($id);
            $pesanan->update(['status' => $request->status]);

            // Kalo statusnya "selesai" dan ada pembayaran, tandai jadi valid
            if ($request->status === 'selesai' && $pesanan->pembayaran) {
                $pesanan->pembayaran->update(['status_verifikasi' => 'valid']);
            }

            $statusLabel = ucfirst($request->status);
            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', "Status pesanan berhasil diubah menjadi {$statusLabel}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi bukti COD — terima atau tolak
     */
    public function verifyCod(Request $request, $id)
    {
        $request->validate([
            'aksi' => 'required|in:terima,tolak',
        ]);

        try {
            $pesanan = Pesanan::with('pembayaran')->findOrFail($id);

            if ($pesanan->metode_pembayaran !== 'cod') {
                return redirect()->back()->with('error', 'Pesanan ini bukan COD.');
            }

            if ($request->aksi === 'terima') {
                $pesanan->update(['status' => 'selesai']);
                if ($pesanan->pembayaran) {
                    $pesanan->pembayaran->update(['status_verifikasi' => 'valid']);
                }

                // Bikin notifikasi inbox buat user
                PesanKontak::create([
                    'id_user' => $pesanan->id_user,
                    'subjek' => 'COD Pesanan #' . $pesanan->id_pesanan . ' Terverifikasi',
                    'isi_pesan' => 'Bukti pembayaran COD untuk pesanan #' . $pesanan->id_pesanan . ' telah diverifikasi. Pesanan selesai.',
                    'tanggal' => now(),
                    'balasan_admin' => 'Terima kasih! Pembayaran COD Anda telah dikonfirmasi. Pesanan #' . $pesanan->id_pesanan . ' telah selesai.',
                    'tanggal_balas' => now(),
                ]);

                return redirect()->route('admin.pesanan.show', $id)
                    ->with('success', 'Bukti COD diverifikasi. Pesanan ditandai selesai.');
            } else {
                if ($pesanan->pembayaran) {
                    $pesanan->pembayaran->update(['status_verifikasi' => 'invalid']);
                }
                // Hapus bukti biar user bisa upload ulang
                $pesanan->update(['bukti_cod' => null]);

                PesanKontak::create([
                    'id_user' => $pesanan->id_user,
                    'subjek' => 'Bukti COD Pesanan #' . $pesanan->id_pesanan . ' Ditolak',
                    'isi_pesan' => 'Bukti pembayaran COD yang Anda kirim untuk pesanan #' . $pesanan->id_pesanan . ' ditolak.',
                    'tanggal' => now(),
                    'balasan_admin' => 'Bukti COD Anda ditolak. Silakan unggah ulang foto bukti penerimaan dan pembayaran yang lebih jelas.',
                    'tanggal_balas' => now(),
                ]);

                return redirect()->route('admin.pesanan.show', $id)
                    ->with('success', 'Bukti COD ditolak. User akan mendapat notifikasi untuk upload ulang.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memverifikasi COD: ' . $e->getMessage());
        }
    }
    
    /**
     * Hapus pesanan (riwayat) dan urutkan ulang ID-nya
     */
    public function deletePesanan($id)
    {
        try {
            DB::beginTransaction();

            $pesanan = Pesanan::findOrFail($id);
            $deletedId = $pesanan->id_pesanan;

            // Hapus data yang terkait dulu
            PesananDetail::where('id_pesanan', $deletedId)->delete();
            Pembayaran::where('id_pesanan', $deletedId)->delete();

            // Hapus pesanan
            $pesanan->delete();

            DB::commit();

            // Urutkan ulang: geser semua ID yang lebih besar dari yang dihapus
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::update('UPDATE pesanan_detail SET id_pesanan = id_pesanan - 1 WHERE id_pesanan > ?', [$deletedId]);
            DB::update('UPDATE pembayaran SET id_pesanan = id_pesanan - 1 WHERE id_pesanan > ?', [$deletedId]);
            DB::update('UPDATE pesanan SET id_pesanan = id_pesanan - 1 WHERE id_pesanan > ?', [$deletedId]);
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Reset AUTO_INCREMENT ke id max + 1 (DDL diluar transaksi)
            $maxId = Pesanan::max('id_pesanan') ?? 0;
            DB::statement('ALTER TABLE pesanan AUTO_INCREMENT = ' . ($maxId + 1));

            return redirect()->route('admin.pesanan.index')
                ->with('success', 'Pesanan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
        }
    }
    
    // ============================================
    // CRUD KATEGORI BUKU
    // ============================================
    
    /**
     * Nampilin daftar kategori
     */
    public function indexKategori(Request $request)
    {
        $query = KategoriBuku::withCount('buku');
        
        // Fitur pencarian
        if ($request->has('search') && $request->search) {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%');
        }
        
        $kategori = $query->orderBy('nama_kategori', 'asc')->paginate(10);
        
        return view('admin.kategori.index', compact('kategori'));
    }
    
    /**
     * Nampilin form buat bikin kategori baru
     */
    public function createKategori()
    {
        return view('admin.kategori.create');
    }
    
    /**
     * Simpan kategori baru
     */
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_buku,nama_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter',
            'nama_kategori.unique' => 'Kategori ini sudah ada',
        ]);
        
        try {
            KategoriBuku::create([
                'nama_kategori' => $request->nama_kategori,
            ]);
            
            return redirect()->route('admin.kategori.index')
                ->with('success', 'Kategori berhasil ditambahkan');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Nampilin form edit kategori
     */
    public function editKategori($id)
    {
        $kategori = KategoriBuku::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }
    
    /**
     * Update data kategori
     */
    public function updateKategori(Request $request, $id)
    {
        $kategori = KategoriBuku::findOrFail($id);
        
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_buku,nama_kategori,' . $id . ',id_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter',
            'nama_kategori.unique' => 'Kategori ini sudah ada',
        ]);
        
        try {
            $kategori->update([
                'nama_kategori' => $request->nama_kategori,
            ]);
            
            return redirect()->route('admin.kategori.index')
                ->with('success', 'Kategori berhasil diperbarui');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Hapus kategori
     */
    public function destroyKategori($id)
    {
        try {
            $kategori = KategoriBuku::withCount('buku')->findOrFail($id);
            
            // Cek apakah kategori masih punya buku
            if ($kategori->buku_count > 0) {
                return redirect()->back()
                    ->with('error', "Kategori tidak dapat dihapus karena masih memiliki {$kategori->buku_count} buku");
            }
            
            $kategori->delete();
            
            return redirect()->route('admin.kategori.index')
                ->with('success', 'Kategori berhasil dihapus');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
    
    // ============================================
    // CRUD BUKU
    // ============================================
    
    /**
     * Nampilin daftar buku
     */
    public function indexBuku(Request $request)
    {
        $query = Buku::with('kategori');
        
        // Fitur pencarian
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan kategori
        if ($request->has('kategori') && $request->kategori) {
            $query->where('id_kategori', $request->kategori);
        }
        
        $buku = $query->orderBy('id_buku', 'desc')->paginate(10);
        $kategori = KategoriBuku::orderBy('nama_kategori', 'asc')->get();
        
        return view('admin.buku.index', compact('buku', 'kategori'));
    }
    
    /**
     * Nampilin form buat bikin buku baru
     */
    public function createBuku()
    {
        $kategori = KategoriBuku::orderBy('nama_kategori', 'asc')->get();
        return view('admin.buku.create', compact('kategori'));
    }
    
    /**
     * Simpan buku baru
     */
    public function storeBuku(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori_buku,id_kategori',
            'judul' => 'required|string|max:200',
            'isbn' => 'required|string|max:20|unique:buku,isbn',
            'penulis' => 'required|string|max:150',
            'penerbit' => 'required|string|max:150',
            'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'cover' => 'nullable|url|max:500',
        ], [
            'id_kategori.required' => 'Kategori wajib dipilih',
            'judul.required' => 'Judul buku wajib diisi',
            'isbn.required' => 'ISBN wajib diisi',
            'isbn.unique' => 'ISBN sudah terdaftar',
            'penulis.required' => 'Penulis wajib diisi',
            'penerbit.required' => 'Penerbit wajib diisi',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi',
            'stok.required' => 'Stok wajib diisi',
            'harga.required' => 'Harga wajib diisi',
            'cover.url' => 'Cover harus berupa URL yang valid',
        ]);
        
        try {
            Buku::create([
                'id_kategori' => $request->id_kategori,
                'judul' => $request->judul,
                'isbn' => $request->isbn,
                'penulis' => $request->penulis,
                'penerbit' => $request->penerbit,
                'tahun_terbit' => $request->tahun_terbit,
                'stok' => $request->stok,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'cover' => $request->cover,
            ]);
            
            return redirect()->route('admin.buku.index')
                ->with('success', 'Buku berhasil ditambahkan');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan buku: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Nampilin form edit buku
     */
    public function editBuku($id)
    {
        $buku = Buku::findOrFail($id);
        $kategori = KategoriBuku::orderBy('nama_kategori', 'asc')->get();
        return view('admin.buku.edit', compact('buku', 'kategori'));
    }
    
    /**
     * Update data buku
     */
    public function updateBuku(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);
        
        $request->validate([
            'id_kategori' => 'required|exists:kategori_buku,id_kategori',
            'judul' => 'required|string|max:200',
            'isbn' => 'required|string|max:20|unique:buku,isbn,' . $id . ',id_buku',
            'penulis' => 'required|string|max:150',
            'penerbit' => 'required|string|max:150',
            'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'cover' => 'nullable|url|max:500',
        ], [
            'id_kategori.required' => 'Kategori wajib dipilih',
            'judul.required' => 'Judul buku wajib diisi',
            'isbn.required' => 'ISBN wajib diisi',
            'isbn.unique' => 'ISBN sudah terdaftar',
            'penulis.required' => 'Penulis wajib diisi',
            'penerbit.required' => 'Penerbit wajib diisi',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi',
            'stok.required' => 'Stok wajib diisi',
            'harga.required' => 'Harga wajib diisi',
            'cover.url' => 'Cover harus berupa URL yang valid',
        ]);
        
        try {
            $buku->update([
                'id_kategori' => $request->id_kategori,
                'judul' => $request->judul,
                'isbn' => $request->isbn,
                'penulis' => $request->penulis,
                'penerbit' => $request->penerbit,
                'tahun_terbit' => $request->tahun_terbit,
                'stok' => max(0, $buku->stok + (int) $request->stok_adjustment),
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'cover' => $request->cover,
            ]);
            
            return redirect()->route('admin.buku.index')
                ->with('success', 'Buku berhasil diperbarui');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui buku: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Hapus buku
     */
    public function destroyBuku($id)
    {
        try {
            $buku = Buku::findOrFail($id);
            
            // Cek apakah buku masih ada di keranjang atau pesanan
            $hasKeranjang = $buku->keranjang()->exists();
            $hasPesanan = $buku->pesananDetail()->exists();
            
            if ($hasKeranjang || $hasPesanan) {
                return redirect()->back()
                    ->with('error', 'Buku tidak dapat dihapus karena sudah ada di keranjang atau pesanan');
            }
            
            $buku->delete();
            
            return redirect()->route('admin.buku.index')
                ->with('success', 'Buku berhasil dihapus');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus buku: ' . $e->getMessage());
        }
    }
    
    // ============================================
    // MANAJEMEN USER
    // ============================================
    
    /**
     * Nampilin daftar user beserta pesanannya
     */
    public function indexUsers(Request $request)
    {
        $query = User::withCount('pesanan');
        
        // Fitur pencarian
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        $users = $query->orderBy('id_user', 'desc')->paginate(15);
        
        return view('admin.users', compact('users'));
    }
    
    /**
     * Update role user
     */
    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);
        
        try {
            $user = User::findOrFail($id);
            
            // Gabisa ganti role sendiri
            if ($user->id_user == Auth::id()) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat mengubah role Anda sendiri');
            }
            
            $user->update(['role' => $request->role]);
            
            return redirect()->back()
                ->with('success', "Role user {$user->nama} berhasil diubah menjadi {$request->role}");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengubah role: ' . $e->getMessage());
        }
    }
    
    /**
     * Nampilin semua pesan kontak (inbox)
     */
    public function indexPesan(Request $request)
    {
        // Bersihin percakapan yang udah expired (24 jam dari pesan pertama per user)
        $userIds = ChatMessage::select('id_user')->distinct()->pluck('id_user');
        foreach ($userIds as $uid) {
            $first = ChatMessage::where('id_user', $uid)->orderBy('waktu', 'asc')->first();
            if ($first && $first->waktu->diffInHours(now()) >= 24) {
                ChatMessage::where('id_user', $uid)->delete();
            }
        }

        // Kalo ada user yang dipilih, tandai pesannya jadi udah dibaca DULU (sebelum ngitung yang belum dibaca)
        $selectedUser = null;
        $messages = collect();
        $chatExpiresAt = null;
        if ($request->has('user_id')) {
            $selectedUser = User::find($request->user_id);
            if ($selectedUser) {
                ChatMessage::where('id_user', $selectedUser->id_user)
                    ->where('pengirim', 'user')
                    ->where('dibaca', false)
                    ->update(['dibaca' => true]);

                $messages = ChatMessage::where('id_user', $selectedUser->id_user)
                    ->orderBy('waktu', 'asc')
                    ->get();

                // Hitung waktu expired-nya
                $firstMsg = $messages->first();
                if ($firstMsg) {
                    $chatExpiresAt = $firstMsg->waktu->copy()->addHours(24);
                }
            }
        }

        // Get all users who have chat messages, with latest message & unread count
        $query = User::whereHas('chatMessages')
            ->withCount(['chatMessages as unread_count' => function ($q) {
                $q->where('pengirim', 'user')->where('dibaca', false);
            }])
            ->with(['chatMessages' => function ($q) {
                $q->orderBy('waktu', 'desc')->limit(1);
            }]);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get()->sortByDesc(function ($user) {
            return $user->chatMessages->first()->waktu ?? now()->subYears(10);
        });

        return view('admin.pesan.index', compact('users', 'selectedUser', 'messages', 'chatExpiresAt'));
    }

    /**
     * Admin ngirim pesan chat ke user
     */
    public function sendPesan(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'pesan' => 'required|string|max:5000',
        ]);

        ChatMessage::create([
            'id_user' => $request->id_user,
            'pengirim' => 'admin',
            'pesan' => $request->pesan,
            'waktu' => now(),
        ]);

        return redirect()->route('admin.pesan.index', ['user_id' => $request->id_user])
            ->with('success', 'Pesan terkirim');
    }

    /**
     * Admin hapus seluruh percakapan sama user
     */
    public function deletePesan($id)
    {
        try {
            ChatMessage::where('id_user', $id)->delete();

            return redirect()->route('admin.pesan.index')
                ->with('success', 'Percakapan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus percakapan: ' . $e->getMessage());
        }
    }

    /**
     * Ambil pesan baru buat polling (AJAX)
     */
    public function getNewMessages(Request $request)
    {
        $userId = $request->user_id;
        $lastId = $request->last_id ?? 0;

        // Cek expired 24 jam
        $firstMessage = ChatMessage::where('id_user', $userId)->orderBy('waktu', 'asc')->first();
        if ($firstMessage && $firstMessage->waktu->diffInHours(now()) >= 24) {
            ChatMessage::where('id_user', $userId)->delete();
            return response()->json(['messages' => [], 'expired' => true]);
        }

        $expiresAt = $firstMessage ? $firstMessage->waktu->copy()->addHours(24)->toIso8601String() : null;

        // Tandai pesan user jadi udah dibaca
        ChatMessage::where('id_user', $userId)
            ->where('pengirim', 'user')
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        $messages = ChatMessage::where('id_user', $userId)
            ->where('id_chat', '>', $lastId)
            ->orderBy('waktu', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($m) {
                return [
                    'id_chat' => $m->id_chat,
                    'pengirim' => $m->pengirim,
                    'pesan' => e($m->pesan),
                    'waktu' => $m->waktu->format('H:i'),
                    'tanggal' => $m->waktu->format('d M Y'),
                ];
            }),
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Nampilin halaman laporan bulanan
     */
    public function laporan(Request $request)
    {
        $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Default: bulan dan tahun sekarang
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $startDate = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // === RINGKASAN BULAN INI ===
        $totalPesanan = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])->count();
        $pesananSelesai = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])->where('status', 'selesai')->count();
        $pesananDiproses = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])->whereIn('status', ['menunggu', 'diproses', 'dikirim'])->count();
        $pesananDibatalkan = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])->where('status', 'dibatalkan')->count();

        $pendapatanBulanIni = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->sum('pembayaran.jumlah');

        // Bulan sebelumnya buat perbandingan
        $prevStart = $startDate->copy()->subMonth()->startOfMonth();
        $prevEnd = $startDate->copy()->subMonth()->endOfMonth();
        $pendapatanBulanLalu = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->whereBetween('pesanan.tanggal_pesanan', [$prevStart, $prevEnd])
            ->sum('pembayaran.jumlah');
        $totalPesananBulanLalu = Pesanan::whereBetween('tanggal_pesanan', [$prevStart, $prevEnd])->count();

        // === PENDAPATAN HARIAN (buat grafik) ===
        $dailyRevenue = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->select(
                DB::raw('DAY(pesanan.tanggal_pesanan) as hari'),
                DB::raw('SUM(pembayaran.jumlah) as total')
            )
            ->groupBy('hari')
            ->orderBy('hari')
            ->get();

        $daysInMonth = $startDate->daysInMonth;
        $chartDailyLabels = [];
        $chartDailyRevenue = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $chartDailyLabels[] = $d;
            $rev = $dailyRevenue->firstWhere('hari', $d);
            $chartDailyRevenue[] = $rev ? (float) $rev->total : 0;
        }

        // === PESANAN HARIAN (buat grafik) ===
        $dailyOrders = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])
            ->select(
                DB::raw('DAY(tanggal_pesanan) as hari'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('hari')
            ->orderBy('hari')
            ->get();

        $chartDailyOrders = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $ord = $dailyOrders->firstWhere('hari', $d);
            $chartDailyOrders[] = $ord ? (int) $ord->total : 0;
        }

        // === DISTRIBUSI STATUS ===
        $statusCounts = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // === METODE PEMBAYARAN ===
        $metodePembayaran = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->select('pembayaran.metode', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(pembayaran.jumlah) as total'))
            ->groupBy('pembayaran.metode')
            ->orderByDesc('total')
            ->get();

        // === BUKU PALING LARIS ===
        $bukuTerlaris = DB::table('pesanan_detail')
            ->join('pesanan', 'pesanan_detail.id_pesanan', '=', 'pesanan.id_pesanan')
            ->join('buku', 'pesanan_detail.id_buku', '=', 'buku.id_buku')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->whereIn('pesanan.status', ['diproses', 'dikirim', 'selesai'])
            ->select(
                'buku.id_buku',
                'buku.judul',
                'buku.penulis',
                'buku.cover',
                'buku.harga',
                DB::raw('SUM(pesanan_detail.qty) as total_terjual'),
                DB::raw('SUM(pesanan_detail.qty * pesanan_detail.harga_satuan) as total_pendapatan')
            )
            ->groupBy('buku.id_buku', 'buku.judul', 'buku.penulis', 'buku.cover', 'buku.harga')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        // === PELANGGAN PALING TOP ===
        $topCustomers = DB::table('pesanan')
            ->join('users', 'pesanan.id_user', '=', 'users.id_user')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->whereIn('pesanan.status', ['diproses', 'dikirim', 'selesai'])
            ->select(
                'users.id_user',
                'users.nama',
                'users.email',
                DB::raw('COUNT(pesanan.id_pesanan) as total_pesanan'),
                DB::raw('SUM(pesanan.total_harga) as total_belanja')
            )
            ->groupBy('users.id_user', 'users.nama', 'users.email')
            ->orderByDesc('total_belanja')
            ->limit(5)
            ->get();

        // === USER BARU BULAN INI ===
        $userBaru = User::where('role', 'user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Available years for filter
        $availableYears = Pesanan::selectRaw('YEAR(tanggal_pesanan) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();
        if (!in_array(now()->year, $availableYears)) {
            array_unshift($availableYears, now()->year);
        }

        return view('admin.laporan', compact(
            'bulan', 'tahun', 'bulanNama', 'availableYears',
            'totalPesanan', 'pesananSelesai', 'pesananDiproses', 'pesananDibatalkan',
            'pendapatanBulanIni', 'pendapatanBulanLalu', 'totalPesananBulanLalu',
            'chartDailyLabels', 'chartDailyRevenue', 'chartDailyOrders',
            'statusCounts', 'metodePembayaran',
            'bukuTerlaris', 'topCustomers', 'userBaru'
        ));
    }

    /**
     * Download laporan bulanan jadi PDF
     */
    public function downloadLaporan(Request $request)
    {
        $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $startDate = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $totalPesanan = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])->count();
        $pesananSelesai = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])->where('status', 'selesai')->count();
        $pesananDiproses = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])->whereIn('status', ['menunggu', 'diproses', 'dikirim'])->count();
        $pesananDibatalkan = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])->where('status', 'dibatalkan')->count();

        $pendapatanBulanIni = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->sum('pembayaran.jumlah');

        $prevStart = $startDate->copy()->subMonth()->startOfMonth();
        $prevEnd = $startDate->copy()->subMonth()->endOfMonth();
        $pendapatanBulanLalu = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->whereBetween('pesanan.tanggal_pesanan', [$prevStart, $prevEnd])
            ->sum('pembayaran.jumlah');
        $totalPesananBulanLalu = Pesanan::whereBetween('tanggal_pesanan', [$prevStart, $prevEnd])->count();

        $dailyRevenue = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->select(DB::raw('DAY(pesanan.tanggal_pesanan) as hari'), DB::raw('SUM(pembayaran.jumlah) as total'))
            ->groupBy('hari')->orderBy('hari')->get();

        $daysInMonth = $startDate->daysInMonth;
        $chartDailyLabels = [];
        $chartDailyRevenue = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $chartDailyLabels[] = $d;
            $rev = $dailyRevenue->firstWhere('hari', $d);
            $chartDailyRevenue[] = $rev ? (float) $rev->total : 0;
        }

        $dailyOrders = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])
            ->select(DB::raw('DAY(tanggal_pesanan) as hari'), DB::raw('COUNT(*) as total'))
            ->groupBy('hari')->orderBy('hari')->get();

        $chartDailyOrders = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $ord = $dailyOrders->firstWhere('hari', $d);
            $chartDailyOrders[] = $ord ? (int) $ord->total : 0;
        }

        $statusCounts = Pesanan::whereBetween('tanggal_pesanan', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $metodePembayaran = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->select('pembayaran.metode', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(pembayaran.jumlah) as total'))
            ->groupBy('pembayaran.metode')->orderByDesc('total')->get();

        $bukuTerlaris = DB::table('pesanan_detail')
            ->join('pesanan', 'pesanan_detail.id_pesanan', '=', 'pesanan.id_pesanan')
            ->join('buku', 'pesanan_detail.id_buku', '=', 'buku.id_buku')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->whereIn('pesanan.status', ['diproses', 'dikirim', 'selesai'])
            ->select('buku.id_buku', 'buku.judul', 'buku.penulis', 'buku.harga',
                DB::raw('SUM(pesanan_detail.qty) as total_terjual'),
                DB::raw('SUM(pesanan_detail.qty * pesanan_detail.harga_satuan) as total_pendapatan'))
            ->groupBy('buku.id_buku', 'buku.judul', 'buku.penulis', 'buku.harga')
            ->orderByDesc('total_terjual')->limit(10)->get();

        $topCustomers = DB::table('pesanan')
            ->join('users', 'pesanan.id_user', '=', 'users.id_user')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate, $endDate])
            ->whereIn('pesanan.status', ['diproses', 'dikirim', 'selesai'])
            ->select('users.id_user', 'users.nama', 'users.email',
                DB::raw('COUNT(pesanan.id_pesanan) as total_pesanan'),
                DB::raw('SUM(pesanan.total_harga) as total_belanja'))
            ->groupBy('users.id_user', 'users.nama', 'users.email')
            ->orderByDesc('total_belanja')->limit(5)->get();

        $userBaru = User::where('role', 'user')
            ->whereBetween('created_at', [$startDate, $endDate])->count();

        $pdf = Pdf::loadView('admin.laporan-pdf', compact(
            'bulan', 'tahun', 'bulanNama',
            'totalPesanan', 'pesananSelesai', 'pesananDiproses', 'pesananDibatalkan',
            'pendapatanBulanIni', 'pendapatanBulanLalu', 'totalPesananBulanLalu',
            'chartDailyLabels', 'chartDailyRevenue', 'chartDailyOrders',
            'statusCounts', 'metodePembayaran',
            'bukuTerlaris', 'topCustomers', 'userBaru'
        ))->setPaper('a4', 'portrait');

        $filename = 'laporan-' . $bulanNama[$bulan] . '-' . $tahun . '.pdf';

        return $pdf->download($filename);
    }
}
