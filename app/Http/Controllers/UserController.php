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
use App\Models\FavoritBuku;
use App\Models\UlasanBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display the user home page
     */
    public function home()
    {
        // Get latest books (limit to 10 for homepage) - order by id_buku since no timestamps
        $books = Buku::with('kategori')
            ->orderBy('id_buku', 'desc')
            ->take(10)
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
     * Display book detail page
     */
    public function bookDetail($id)
    {
        $book = Buku::with(['kategori', 'ulasan.user'])->findOrFail($id);
        
        $isFavorited = FavoritBuku::where('id_user', Auth::id())
            ->where('id_buku', $id)
            ->exists();
        
        $avgRating = $book->ulasan->avg('rating') ?? 0;
        $reviewCount = $book->ulasan->count();
        
        $userReview = UlasanBuku::where('id_user', Auth::id())
            ->where('id_buku', $id)
            ->first();
        
        // Related books from same category
        $relatedBooks = Buku::where('id_kategori', $book->id_kategori)
            ->where('id_buku', '!=', $book->id_buku)
            ->take(6)
            ->get();
        
        return view('user.book-detail', compact(
            'book', 'isFavorited', 'avgRating', 'reviewCount', 'userReview', 'relatedBooks'
        ));
    }
    
    /**
     * Toggle book favorite
     */
    public function toggleFavorite($id)
    {
        $userId = Auth::id();
        $existing = FavoritBuku::where('id_user', $userId)->where('id_buku', $id)->first();
        
        if ($existing) {
            $existing->delete();
            return response()->json(['success' => true, 'favorited' => false, 'message' => 'Buku dihapus dari favorit']);
        }
        
        FavoritBuku::create(['id_user' => $userId, 'id_buku' => $id]);
        return response()->json(['success' => true, 'favorited' => true, 'message' => 'Buku ditambahkan ke favorit']);
    }
    
    /**
     * Submit book review
     */
    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);
        
        $userId = Auth::id();
        
        // Check if user already reviewed this book
        $existing = UlasanBuku::where('id_user', $userId)->where('id_buku', $id)->first();
        if ($existing) {
            $existing->update([
                'rating' => $request->rating,
                'komentar' => $request->komentar,
            ]);
            return back()->with('success', 'Ulasan berhasil diperbarui!');
        }
        
        UlasanBuku::create([
            'id_user' => $userId,
            'id_buku' => $id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);
        
        return back()->with('success', 'Ulasan berhasil ditambahkan!');
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
        $orders = Pesanan::with(['details.buku', 'pembayaran'])
            ->where('id_user', Auth::id())
            ->orderBy('tanggal_pesanan', 'desc')
            ->get();

        return view('user.orders', compact('orders'));
    }
    
    /**
     * Cancel unpaid order and restore stock
     */
    public function cancelOrder($orderId)
    {
        DB::beginTransaction();

        try {
            $pesanan = Pesanan::with('details.buku')
                ->where('id_pesanan', $orderId)
                ->where('id_user', Auth::id())
                ->firstOrFail();

            if ($pesanan->status !== 'menunggu') {
                return redirect()->route('user.orders')
                    ->with('error', 'Pesanan ini tidak dapat dibatalkan.');
            }

            $pesanan->update(['status' => 'dibatalkan']);

            // Restore stock
            foreach ($pesanan->details as $detail) {
                if ($detail->buku) {
                    $detail->buku->increment('stok', $detail->qty);
                }
            }

            DB::commit();

            return redirect()->route('user.orders')
                ->with('success', 'Pesanan #' . $pesanan->id_pesanan . ' berhasil dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.orders')
                ->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Upload bukti COD (foto bukti penerimaan + pembayaran)
     */
    public function uploadBuktiCod(Request $request, $orderId)
    {
        $request->validate([
            'bukti_cod' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'bukti_cod.required' => 'Foto bukti wajib diunggah.',
            'bukti_cod.image' => 'File harus berupa gambar.',
            'bukti_cod.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
            'bukti_cod.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        try {
            $pesanan = Pesanan::where('id_pesanan', $orderId)
                ->where('id_user', Auth::id())
                ->where('metode_pembayaran', 'cod')
                ->whereIn('status', ['dikirim', 'diproses'])
                ->firstOrFail();

            // Store the photo
            $path = $request->file('bukti_cod')->store('bukti_cod', 'public');

            $pesanan->update(['bukti_cod' => $path]);

            // Update pembayaran bukti
            if ($pesanan->pembayaran) {
                $pesanan->pembayaran->update([
                    'bukti_pembayaran' => $path,
                ]);
            }

            return redirect()->route('user.orders')
                ->with('success', 'Bukti penerimaan & pembayaran COD berhasil diunggah! Menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            return redirect()->route('user.orders')
                ->with('error', 'Gagal mengunggah bukti: ' . $e->getMessage());
        }
    }

    /**
     * Display user profile
     */
    public function profile()
    {
        return view('user.profile');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id_user . ',id_user',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:1000',
        ]);

        $user->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('user.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'password' => $request->password,
        ]);

        return redirect()->route('user.profile')->with('success', 'Password berhasil diubah.');
    }
    
    /**
     * Display inbox page with messages
     */
    public function inbox()
    {
        // Mark all unread messages as read when user opens inbox
        PesanKontak::where('id_user', Auth::id())
                   ->whereNotNull('balasan_admin')
                   ->where(function ($q) {
                       $q->whereNull('dibaca_user')->orWhere('dibaca_user', false);
                   })
                   ->update(['dibaca_user' => true]);

        $messages = PesanKontak::where('id_user', Auth::id())
                               ->whereNotNull('balasan_admin')
                               ->orderBy('tanggal_balas', 'desc')
                               ->paginate(10);
        
        return view('user.inbox', compact('messages'));
    }

    /**
     * Delete inbox message
     */
    public function deleteInboxMessage($id)
    {
        $pesan = PesanKontak::where('id_user', Auth::id())->findOrFail($id);
        $pesan->delete();

        return redirect()->route('user.inbox')
            ->with('success', 'Pesan berhasil dihapus');
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
            'metode_pembayaran' => 'required|in:midtrans,cod',
        ], [
            'id_alamat.required' => 'Pilih alamat pengiriman',
            'id_alamat.exists' => 'Alamat tidak valid',
            'metode_pembayaran.required' => 'Pilih metode pembayaran',
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
                'metode_pembayaran' => $request->metode_pembayaran,
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
                    'harga_satuan' => $item->buku->harga,
                ]);
                
                // Reduce stock
                $item->buku->decrement('stok', $item->qty);
            }
            
            // Clear cart
            Keranjang::where('id_user', $userId)->delete();
            
            DB::commit();

            // COD: redirect to orders page, Midtrans: redirect to payment page
            if ($request->metode_pembayaran === 'cod') {
                // Create pembayaran record with 'menunggu' status for COD
                Pembayaran::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'metode' => 'cod',
                    'jumlah' => $total,
                    'status_verifikasi' => 'menunggu',
                ]);

                return redirect()->route('user.orders')
                    ->with('success', 'Pesanan COD berhasil dibuat! Siapkan pembayaran saat barang diterima.');
            }
            
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
     * Show payment page with Midtrans Snap token
     */
    public function showPayment($orderId)
    {
        $pesanan = Pesanan::with(['details.buku', 'user'])
            ->where('id_pesanan', $orderId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // Only allow payment for pending orders
        if ($pesanan->status !== 'menunggu') {
            return redirect()->route('user.orders')
                ->with('error', 'Pesanan ini sudah dibayar atau dibatalkan.');
        }

        // Reuse existing snap token if available
        if (!$pesanan->snap_token) {
            // Configure Midtrans
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized', true);
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds', true);

            $orderId_midtrans = 'ORDER-' . $pesanan->id_pesanan . '-' . time();

            $itemDetails = $pesanan->details->map(function ($detail) {
                return [
                    'id' => (string) $detail->id_buku,
                    'price' => (int) $detail->harga_satuan,
                    'quantity' => (int) $detail->qty,
                    'name' => substr($detail->buku->judul, 0, 50),
                ];
            })->toArray();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId_midtrans,
                    'gross_amount' => (int) $pesanan->total_harga,
                ],
                'customer_details' => [
                    'first_name' => $pesanan->user->nama,
                    'email' => $pesanan->user->email,
                ],
                'item_details' => $itemDetails,
            ];

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $pesanan->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Gagal memuat pembayaran. Silakan coba lagi.');
            }
        }

        $clientKey = config('midtrans.client_key');

        return view('user.payment', compact('pesanan', 'clientKey'));
    }
    
    /**
     * Process payment after Midtrans Snap success (called via AJAX from frontend)
     */
    public function processPayment(Request $request, $orderId)
    {
        DB::beginTransaction();

        try {
            $pesanan = Pesanan::where('id_pesanan', $orderId)
                ->where('id_user', Auth::id())
                ->firstOrFail();

            if ($pesanan->status !== 'menunggu') {
                return response()->json(['success' => false, 'message' => 'Pesanan sudah diproses.']);
            }

            // Create payment record
            Pembayaran::create([
                'id_pesanan' => $orderId,
                'midtrans_transaction_id' => $request->transaction_id,
                'midtrans_order_id' => $request->order_id,
                'metode' => $request->payment_type ?? 'midtrans',
                'jumlah' => $pesanan->total_harga,
                'status_verifikasi' => 'valid',
            ]);

            // Update order status to 'diproses' (sedang dikemas)
            $pesanan->update(['status' => 'diproses']);

            // Create inbox notification for user
            PesanKontak::create([
                'id_user' => Auth::id(),
                'subjek' => 'Pesanan #' . $pesanan->id_pesanan . ' Berhasil Dibayar',
                'isi_pesan' => 'Pembayaran untuk pesanan #' . $pesanan->id_pesanan . ' sebesar Rp ' . number_format($pesanan->total_harga, 0, ',', '.') . ' telah berhasil dilakukan.',
                'tanggal' => now(),
                'balasan_admin' => 'Terima kasih! Pesanan Anda sedang dikemas dan akan segera dikirim. Anda dapat memantau status pesanan di halaman Pesanan Saya.',
                'tanggal_balas' => now(),
            ]);

            DB::commit();

            // Load invoice data
            $pesanan->load('details.buku');
            $user = Auth::user();

            $invoiceItems = $pesanan->details->map(function ($detail) {
                return [
                    'judul' => $detail->buku->judul,
                    'penulis' => $detail->buku->penulis,
                    'qty' => $detail->qty,
                    'harga_satuan' => $detail->harga_satuan,
                    'subtotal' => $detail->harga_satuan * $detail->qty,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil!',
                'redirect' => route('user.orders'),
                'invoice' => [
                    'id_pesanan' => $pesanan->id_pesanan,
                    'tanggal' => now()->format('d M Y, H:i'),
                    'nama_pembeli' => $user->nama ?? $user->name ?? 'Pelanggan',
                    'metode' => $request->payment_type ?? 'midtrans',
                    'transaction_id' => $request->transaction_id,
                    'items' => $invoiceItems,
                    'total_harga' => $pesanan->total_harga,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Process Payment Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan.'], 500);
        }
    }

    /**
     * Handle Midtrans server-to-server notification (webhook)
     */
    public function midtransNotification(Request $request)
    {
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');

            $notification = new \Midtrans\Notification();

            $transactionStatus = $notification->transaction_status;
            $paymentType = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status ?? null;

            // Extract pesanan ID from order_id format: ORDER-{id}-{timestamp}
            $parts = explode('-', $orderId);
            if (count($parts) < 2) {
                Log::warning('Midtrans notification: invalid order_id format: ' . $orderId);
                return response()->json(['status' => 'error'], 400);
            }
            $pesananId = $parts[1];

            $pesanan = Pesanan::find($pesananId);
            if (!$pesanan) {
                Log::warning('Midtrans notification: pesanan not found: ' . $pesananId);
                return response()->json(['status' => 'error'], 404);
            }

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($fraudStatus == 'accept' || $fraudStatus === null) {
                    // Payment successful
                    if ($pesanan->status === 'menunggu') {
                        $pesanan->update(['status' => 'diproses']);

                        // Upsert payment record
                        Pembayaran::updateOrCreate(
                            ['id_pesanan' => $pesananId],
                            [
                                'midtrans_transaction_id' => $notification->transaction_id,
                                'midtrans_order_id' => $orderId,
                                'metode' => $paymentType,
                                'jumlah' => $pesanan->total_harga,
                                'status_verifikasi' => 'valid',
                            ]
                        );

                        // Create inbox notification if not already created
                        $existingNotif = PesanKontak::where('id_user', $pesanan->id_user)
                            ->where('subjek', 'Pesanan #' . $pesanan->id_pesanan . ' Berhasil Dibayar')
                            ->first();

                        if (!$existingNotif) {
                            PesanKontak::create([
                                'id_user' => $pesanan->id_user,
                                'subjek' => 'Pesanan #' . $pesanan->id_pesanan . ' Berhasil Dibayar',
                                'isi_pesan' => 'Pembayaran untuk pesanan #' . $pesanan->id_pesanan . ' sebesar Rp ' . number_format($pesanan->total_harga, 0, ',', '.') . ' telah berhasil.',
                                'tanggal' => now(),
                                'balasan_admin' => 'Terima kasih! Pesanan Anda sedang dikemas dan akan segera dikirim.',
                                'tanggal_balas' => now(),
                            ]);
                        }
                    }
                }
            } elseif ($transactionStatus == 'pending') {
                // Payment is pending — keep status as 'menunggu'
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                // Payment failed — cancel order and restore stock
                if ($pesanan->status === 'menunggu') {
                    $pesanan->update(['status' => 'dibatalkan']);

                    // Restore stock
                    foreach ($pesanan->details as $detail) {
                        if ($detail->buku) {
                            $detail->buku->increment('stok', $detail->qty);
                        }
                    }

                    Pembayaran::updateOrCreate(
                        ['id_pesanan' => $pesananId],
                        [
                            'midtrans_transaction_id' => $notification->transaction_id,
                            'midtrans_order_id' => $orderId,
                            'metode' => $paymentType,
                            'jumlah' => $pesanan->total_harga,
                            'status_verifikasi' => 'invalid',
                        ]
                    );
                }
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
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
