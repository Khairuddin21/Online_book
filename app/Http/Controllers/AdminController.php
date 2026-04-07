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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        // Get statistics
        $totalBuku = Buku::count();
        $totalPesanan = Pesanan::count();
        $pesananDibatalkan = Pesanan::where('status', 'dibatalkan')->count();
        $totalPendapatan = Pembayaran::where('status_verifikasi', 'valid')->sum('jumlah');
        
        // Monthly revenue for last 6 months (from pembayaran with valid status)
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

        // Monthly orders count for last 6 months
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

        // Build chart labels and data for last 6 months
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

        // Order status distribution
        $statusCounts = Pesanan::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Get latest orders with user relationship
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
     * Display list of pesanan
     */
    public function indexPesanan(Request $request)
    {
        $query = Pesanan::with(['user', 'pembayaran']);

        // Search by user name or order id
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

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $pesanan = $query->orderBy('tanggal_pesanan', 'desc')->paginate(15);

        return view('admin.pesanan.index', compact('pesanan'));
    }

    /**
     * Show pesanan details
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
     * Update pesanan status
     */
    public function updateStatusPesanan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,dikirim,selesai,dibatalkan',
        ]);

        try {
            $pesanan = Pesanan::findOrFail($id);
            $pesanan->update(['status' => $request->status]);

            // If status is "selesai" and pembayaran exists, mark as valid
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
     * Verify COD proof — accept or reject
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

                // Create inbox notification
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
                // Clear bukti so user can re-upload
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
     * Delete pesanan (history)
     */
    public function deletePesanan($id)
    {
        try {
            DB::beginTransaction();
            
            $pesanan = Pesanan::findOrFail($id);
            
            // Delete related records
            PesananDetail::where('id_pesanan', $id)->delete();
            Pembayaran::where('id_pesanan', $id)->delete();
            
            // Delete pesanan
            $pesanan->delete();
            
            DB::commit();

            // Reset AUTO_INCREMENT outside transaction (DDL causes implicit commit)
            $remainingPesanan = Pesanan::count();
            if ($remainingPesanan == 0) {
                DB::statement('ALTER TABLE pesanan AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE pesanan_detail AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE pembayaran AUTO_INCREMENT = 1');
            } else {
                $maxId = Pesanan::max('id_pesanan');
                if ($maxId) {
                    DB::statement('ALTER TABLE pesanan AUTO_INCREMENT = ' . ($maxId + 1));
                }
            }
            
            return redirect()->route('admin.pesanan.index')
                ->with('success', 'Pesanan berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
        }
    }
    
    // ============================================
    // KATEGORI BUKU CRUD
    // ============================================
    
    /**
     * Display list of kategori
     */
    public function indexKategori(Request $request)
    {
        $query = KategoriBuku::withCount('buku');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%');
        }
        
        $kategori = $query->orderBy('nama_kategori', 'asc')->paginate(10);
        
        return view('admin.kategori.index', compact('kategori'));
    }
    
    /**
     * Show form to create new kategori
     */
    public function createKategori()
    {
        return view('admin.kategori.create');
    }
    
    /**
     * Store new kategori
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
     * Show form to edit kategori
     */
    public function editKategori($id)
    {
        $kategori = KategoriBuku::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }
    
    /**
     * Update kategori
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
     * Delete kategori
     */
    public function destroyKategori($id)
    {
        try {
            $kategori = KategoriBuku::withCount('buku')->findOrFail($id);
            
            // Check if kategori has books
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
    // BUKU CRUD
    // ============================================
    
    /**
     * Display list of buku
     */
    public function indexBuku(Request $request)
    {
        $query = Buku::with('kategori');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        
        // Filter by kategori
        if ($request->has('kategori') && $request->kategori) {
            $query->where('id_kategori', $request->kategori);
        }
        
        $buku = $query->orderBy('id_buku', 'desc')->paginate(10);
        $kategori = KategoriBuku::orderBy('nama_kategori', 'asc')->get();
        
        return view('admin.buku.index', compact('buku', 'kategori'));
    }
    
    /**
     * Show form to create new buku
     */
    public function createBuku()
    {
        $kategori = KategoriBuku::orderBy('nama_kategori', 'asc')->get();
        return view('admin.buku.create', compact('kategori'));
    }
    
    /**
     * Store new buku
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
     * Show form to edit buku
     */
    public function editBuku($id)
    {
        $buku = Buku::findOrFail($id);
        $kategori = KategoriBuku::orderBy('nama_kategori', 'asc')->get();
        return view('admin.buku.edit', compact('buku', 'kategori'));
    }
    
    /**
     * Update buku
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
     * Delete buku
     */
    public function destroyBuku($id)
    {
        try {
            $buku = Buku::findOrFail($id);
            
            // Check if buku has related keranjang or pesanan
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
    // USERS MANAGEMENT
    // ============================================
    
    /**
     * Display list of users with their orders
     */
    public function indexUsers(Request $request)
    {
        $query = User::withCount('pesanan');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        $users = $query->orderBy('id_user', 'desc')->paginate(15);
        
        return view('admin.users', compact('users'));
    }
    
    /**
     * Update user role
     */
    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);
        
        try {
            $user = User::findOrFail($id);
            
            // Prevent changing own role
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
     * Display all pesan kontak (inbox)
     */
    public function indexPesan(Request $request)
    {
        $query = PesanKontak::with('user');
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subjek', 'like', "%{$search}%")
                  ->orWhere('isi_pesan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Status filter (dibaca/belum dibaca)
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'belum_dibaca') {
                $query->whereNull('balasan_admin');
            } elseif ($request->status == 'sudah_dibaca') {
                $query->whereNotNull('balasan_admin');
            }
        }
        
        $pesan = $query->orderBy('tanggal', 'desc')->paginate(15);
        
        return view('admin.pesan.index', compact('pesan'));
    }
    
    /**
     * Show pesan detail
     */
    public function showPesan($id)
    {
        $pesan = PesanKontak::with('user')->findOrFail($id);
        
        return view('admin.pesan.show', compact('pesan'));
    }
    
    /**
     * Reply to pesan kontak
     */
    public function replyPesan(Request $request, $id)
    {
        $request->validate([
            'balasan' => 'required|string|min:10',
        ], [
            'balasan.required' => 'Balasan wajib diisi',
            'balasan.min' => 'Balasan minimal 10 karakter',
        ]);
        
        try {
            $pesan = PesanKontak::findOrFail($id);
            
            $pesan->update([
                'balasan_admin' => $request->balasan,
                'tanggal_balas' => now(),
            ]);
            
            return redirect()->back()
                ->with('success', 'Balasan berhasil dikirim ke user');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim balasan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete pesan kontak
     */
    public function deletePesan($id)
    {
        try {
            $pesan = PesanKontak::findOrFail($id);
            $pesan->delete();
            
            return redirect()->route('admin.pesan.index')
                ->with('success', 'Pesan berhasil dihapus');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus pesan: ' . $e->getMessage());
        }
    }

    /**
     * Display laporan bulanan page
     */
    public function laporan(Request $request)
    {
        $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Default: current month/year
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

        // Previous month for comparison
        $prevStart = $startDate->copy()->subMonth()->startOfMonth();
        $prevEnd = $startDate->copy()->subMonth()->endOfMonth();
        $pendapatanBulanLalu = DB::table('pembayaran')
            ->join('pesanan', 'pembayaran.id_pesanan', '=', 'pesanan.id_pesanan')
            ->where('pembayaran.status_verifikasi', 'valid')
            ->whereBetween('pesanan.tanggal_pesanan', [$prevStart, $prevEnd])
            ->sum('pembayaran.jumlah');
        $totalPesananBulanLalu = Pesanan::whereBetween('tanggal_pesanan', [$prevStart, $prevEnd])->count();

        // === PENDAPATAN HARIAN (chart) ===
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

        // === PESANAN HARIAN (chart) ===
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

        // === STATUS DISTRIBUTION ===
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

        // === BUKU TERLARIS ===
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

        // === PELANGGAN TERATAS ===
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
}
