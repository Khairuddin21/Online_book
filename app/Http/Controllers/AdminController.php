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
            'pesananTerbaru'
        ));
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
            
            // Reset AUTO_INCREMENT if no more pesanan exist
            $remainingPesanan = Pesanan::count();
            if ($remainingPesanan == 0) {
                DB::statement('ALTER TABLE pesanan AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE pesanan_detail AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE pembayaran AUTO_INCREMENT = 1');
            } else {
                // Reset to next available ID after max existing ID
                $maxId = Pesanan::max('id_pesanan');
                if ($maxId) {
                    DB::statement('ALTER TABLE pesanan AUTO_INCREMENT = ' . ($maxId + 1));
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.dashboard')
                ->with('success', 'Pesanan berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.dashboard')
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
                'stok' => $request->stok,
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
}
