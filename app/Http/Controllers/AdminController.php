<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
}
