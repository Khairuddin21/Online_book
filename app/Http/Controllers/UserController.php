<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Pembayaran;
use App\Models\AlamatPengiriman;
use App\Models\PesanKontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display the user home page
     */
    public function home()
    {
        // Get latest books (limit to 8 for homepage) - order by id_buku since no timestamps
        $books = Buku::with('kategori')
            ->orderBy('id_buku', 'desc')
            ->take(8)
            ->get();
        
        // Get all categories with book count
        $categories = KategoriBuku::withCount('buku')
            ->orderBy('nama_kategori', 'asc')
            ->get();
        
        return view('user.home', compact('books', 'categories'));
    }
    
    /**
     * Display all books
     */
    public function books(Request $request)
    {
        $query = Buku::with('kategori');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->has('kategori') && $request->kategori) {
            $query->where('id_kategori', $request->kategori);
        }
        
        $books = $query->orderBy('id_buku', 'desc')->paginate(12);
        $categories = KategoriBuku::withCount('buku')->get();
        
        return view('user.books', compact('books', 'categories'));
    }
    
    /**
     * Display all categories
     */
    public function categories()
    {
        $categories = KategoriBuku::withCount('buku')
            ->orderBy('nama_kategori', 'asc')
            ->get();
        
        return view('user.categories', compact('categories'));
    }
    
    /**
     * Display user's cart
     */
    public function cart()
    {
        $cartItems = Keranjang::where('id_user', Auth::id())
            ->with('buku')
            ->orderBy('id_keranjang', 'desc')
            ->paginate(4);
        
        // Calculate total from all items (not just current page)
        $allItems = Keranjang::where('id_user', Auth::id())
            ->with('buku')
            ->get();
            
        $total = $allItems->sum(function($item) {
            return $item->buku->harga * $item->qty;
        });
        
        $totalItems = $allItems->sum('qty');
        
        return view('user.cart', compact('cartItems', 'total', 'totalItems'));
    }
    
    /**
     * Add item to cart (API)
     */
    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'book_id' => 'required|exists:buku,id_buku',
                'quantity' => 'required|integer|min:1'
            ]);
            
            $userId = Auth::id();
            $bookId = $request->book_id;
            $quantity = $request->quantity;
            
            // Use database transaction to ensure data consistency
            DB::beginTransaction();
            
            try {
                // Get book to check stock
                $book = Buku::findOrFail($bookId);
                
                // Check if item already exists in cart
                $cartItem = Keranjang::where('id_user', $userId)
                    ->where('id_buku', $bookId)
                    ->lockForUpdate()
                    ->first();
                
                $newQty = $cartItem ? ($cartItem->qty + $quantity) : $quantity;
                
                // Validate stock
                if ($newQty > $book->stok) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi. Stok tersedia: {$book->stok}"
                    ], 400);
                }
                
                if ($cartItem) {
                    // Update quantity if already exists
                    $cartItem->qty = $newQty;
                    $cartItem->save();
                } else {
                    // Create new cart item
                    $cartItem = Keranjang::create([
                        'id_user' => $userId,
                        'id_buku' => $bookId,
                        'qty' => $quantity
                    ]);
                }
                
                DB::commit();
                
                // Refresh to ensure data is saved
                $cartItem->refresh();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Buku berhasil ditambahkan ke keranjang',
                    'cart_item' => $cartItem
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get cart items count (API)
     */
    public function getCartCount()
    {
        $count = Keranjang::where('id_user', Auth::id())->sum('qty');
        
        return response()->json([
            'count' => $count
        ]);
    }
    
    /**
     * Update cart item quantity (API)
     */
    public function updateCart(Request $request)
    {
        try {
            $request->validate([
                'cart_id' => 'required|exists:keranjang,id_keranjang',
                'quantity' => 'required|integer|min:1'
            ]);
            
            $cartItem = Keranjang::where('id_keranjang', $request->cart_id)
                ->where('id_user', Auth::id())
                ->with('buku')
                ->first();
            
            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                ], 404);
            }
            
            // Validate stock
            if ($request->quantity > $cartItem->buku->stok) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi. Stok tersedia: {$cartItem->buku->stok}"
                ], 400);
            }
            
            $cartItem->qty = $request->quantity;
            $cartItem->save();
            
            // Calculate new subtotal
            $subtotal = $cartItem->buku->harga * $cartItem->qty;
            
            // Calculate new total
            $total = Keranjang::where('id_user', Auth::id())
                ->with('buku')
                ->get()
                ->sum(function($item) {
                    return $item->buku->harga * $item->qty;
                });
            
            return response()->json([
                'success' => true,
                'message' => 'Jumlah berhasil diupdate',
                'subtotal' => $subtotal,
                'total' => $total
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove item from cart (API)
     */
    public function removeFromCart($id)
    {
        try {
            $cartItem = Keranjang::where('id_keranjang', $id)
                ->where('id_user', Auth::id())
                ->first();
            
            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                ], 404);
            }
            
            $cartItem->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display user's orders
     */
    public function orders()
    {
        // TODO: Implement orders functionality
        return view('user.orders');
    }
    
    /**
     * Display user profile
     */
    public function profile()
    {
        return view('user.profile');
    }
    
    /**
     * Display inbox page with messages
     */
    public function inbox()
    {
        $messages = PesanKontak::where('id_user', Auth::id())
                               ->whereNotNull('balasan_admin')
                               ->orderBy('tanggal_balas', 'desc')
                               ->paginate(10);
        
        return view('user.inbox', compact('messages'));
    }
    
    /**
     * Display contact page
     */
    public function contact()
    {
        return view('user.contact');
    }
    
    /**
     * Submit contact form
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'subjek' => 'required|string|max:150',
            'pesan' => 'required|string|min:10',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'subjek.required' => 'Subjek wajib diisi',
            'pesan.required' => 'Pesan wajib diisi',
            'pesan.min' => 'Pesan minimal 10 karakter',
        ]);
        
        try {
            // Create contact message
            PesanKontak::create([
                'id_user' => Auth::id(),
                'subjek' => $request->subjek,
                'isi_pesan' => "Nama: {$request->nama}\nEmail: {$request->email}\n\n{$request->pesan}",
                'tanggal' => now(),
            ]);
            
            return redirect()->back()
                ->with('success', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim pesan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show checkout page with address form
     */
    public function showCheckout()
    {
        $cartItems = Keranjang::where('id_user', Auth::id())
            ->with('buku')
            ->get();
        
        if ($cartItems->count() === 0) {
            return redirect()->route('user.cart')
                ->with('error', 'Keranjang Anda kosong');
        }
        
        $total = $cartItems->sum(function($item) {
            return $item->buku->harga * $item->qty;
        });
        
        $user = Auth::user();
        $addresses = AlamatPengiriman::where('id_user', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user.checkout', compact('cartItems', 'total', 'user', 'addresses'));
    }
    
    /**
     * Process checkout and create order
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'id_alamat' => 'required|exists:alamat_pengiriman,id_alamat',
        ], [
            'id_alamat.required' => 'Pilih alamat pengiriman',
            'id_alamat.exists' => 'Alamat tidak valid',
        ]);
        
        DB::beginTransaction();
        
        try {
            $userId = Auth::id();
            
            // Verify address belongs to user
            $alamat = AlamatPengiriman::where('id_alamat', $request->id_alamat)
                ->where('id_user', $userId)
                ->firstOrFail();
            
            // Get cart items
            $cartItems = Keranjang::where('id_user', $userId)
                ->with('buku')
                ->lockForUpdate()
                ->get();
            
            if ($cartItems->count() === 0) {
                DB::rollBack();
                return redirect()->route('user.cart')
                    ->with('error', 'Keranjang Anda kosong');
            }
            
            // Calculate total
            $total = $cartItems->sum(function($item) {
                return $item->buku->harga * $item->qty;
            });
            
            // Create order
            $pesanan = Pesanan::create([
                'id_user' => $userId,
                'tanggal_pesanan' => now(),
                'total_harga' => $total,
                'status' => 'menunggu',
            ]);
            
            // Create order details and reduce stock
            foreach ($cartItems as $item) {
                // Check stock availability
                if ($item->buku->stok < $item->qty) {
                    DB::rollBack();
                    return redirect()->route('user.cart')
                        ->with('error', "Stok {$item->buku->judul} tidak mencukupi");
                }
                
                // Create order detail
                PesananDetail::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_buku' => $item->id_buku,
                    'qty' => $item->qty,
                    'harga' => $item->buku->harga,
                ]);
                
                // Reduce stock
                $item->buku->decrement('stok', $item->qty);
            }
            
            // Clear cart
            Keranjang::where('id_user', $userId)->delete();
            
            DB::commit();
            
            return redirect()->route('user.payment', $pesanan->id_pesanan)
                ->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show payment page
     */
    public function showPayment($orderId)
    {
        $pesanan = Pesanan::with(['details.buku', 'user'])
            ->where('id_pesanan', $orderId)
            ->where('id_user', Auth::id())
            ->firstOrFail();
        
        return view('user.payment', compact('pesanan'));
    }
    
    /**
     * Process payment
     */
    public function processPayment(Request $request, $orderId)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:transfer,e-wallet,kartu_kredit',
        ]);
        
        DB::beginTransaction();
        
        try {
            $pesanan = Pesanan::where('id_pesanan', $orderId)
                ->where('id_user', Auth::id())
                ->firstOrFail();
            
            // Create payment record (dummy payment - auto valid)
            Pembayaran::create([
                'id_pesanan' => $orderId,
                'metode' => $request->metode_pembayaran,
                'jumlah' => $pesanan->total_harga,
                'status_verifikasi' => 'valid', // Auto valid for dummy payment
            ]);
            
            // Update order status to 'selesai' since payment is auto valid
            $pesanan->update(['status' => 'selesai']);
            
            DB::commit();
            
            return redirect()->route('user.orders')
                ->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Store new shipping address
     */
    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'nama_penerima' => 'required|string|max:150',
            'no_hp' => 'required|string|max:20',
            'alamat_lengkap' => 'required|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // If this is set as default, unset other defaults
            if ($request->has('is_default') && $request->is_default) {
                AlamatPengiriman::where('id_user', Auth::id())
                    ->update(['is_default' => false]);
            }
            
            AlamatPengiriman::create([
                'id_user' => Auth::id(),
                'label' => $request->label,
                'nama_penerima' => $request->nama_penerima,
                'no_hp' => $request->no_hp,
                'alamat_lengkap' => $request->alamat_lengkap,
                'is_default' => $request->has('is_default') ? true : false,
            ]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Alamat berhasil ditambahkan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan alamat: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update shipping address
     */
    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'nama_penerima' => 'required|string|max:150',
            'no_hp' => 'required|string|max:20',
            'alamat_lengkap' => 'required|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $address = AlamatPengiriman::where('id_alamat', $id)
                ->where('id_user', Auth::id())
                ->firstOrFail();
            
            // If this is set as default, unset other defaults
            if ($request->has('is_default') && $request->is_default) {
                AlamatPengiriman::where('id_user', Auth::id())
                    ->where('id_alamat', '!=', $id)
                    ->update(['is_default' => false]);
            }
            
            $address->update([
                'label' => $request->label,
                'nama_penerima' => $request->nama_penerima,
                'no_hp' => $request->no_hp,
                'alamat_lengkap' => $request->alamat_lengkap,
                'is_default' => $request->has('is_default') ? true : false,
            ]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Alamat berhasil diperbarui');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui alamat: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete shipping address
     */
    public function deleteAddress($id)
    {
        try {
            $address = AlamatPengiriman::where('id_alamat', $id)
                ->where('id_user', Auth::id())
                ->firstOrFail();
            
            $address->delete();
            
            // Return JSON for AJAX requests
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alamat berhasil dihapus'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Alamat berhasil dihapus');
                
        } catch (\Exception $e) {
            // Return JSON for AJAX requests
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus alamat: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus alamat: ' . $e->getMessage());
        }
    }
}
