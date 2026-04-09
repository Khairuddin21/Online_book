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
use App\Models\ChatMessage;
use App\Models\FavoritBuku;
use App\Models\UlasanBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    /**
     * Nampilin halaman utama user
     */
    public function home()
    {
        // Ambil buku terbaru (max 10 buat homepage) - urut berdasarkan id_buku soalnya ga pake timestamps
        $books = Buku::with('kategori')
            ->orderBy('id_buku', 'desc')
            ->take(10)
            ->get();
        
        // Ambil semua kategori beserta jumlah bukunya
        $categories = KategoriBuku::withCount('buku')
            ->orderBy('nama_kategori', 'asc')
            ->get();
        
        return view('user.home', compact('books', 'categories'));
    }
    
    /**
     * Nampilin semua buku
     */
    public function books(Request $request)
    {
        $query = Buku::with('kategori');
        
        // Fitur pencarian
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan kategori
        if ($request->has('kategori') && $request->kategori) {
            $query->where('id_kategori', $request->kategori);
        }
        
        $books = $query->orderBy('id_buku', 'desc')->paginate(12);
        $categories = KategoriBuku::withCount('buku')->get();
        
        return view('user.books', compact('books', 'categories'));
    }
    
    /**
     * Nampilin halaman detail buku
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
        
        // Buku terkait dari kategori yang sama
        $relatedBooks = Buku::where('id_kategori', $book->id_kategori)
            ->where('id_buku', '!=', $book->id_buku)
            ->take(6)
            ->get();
        
        return view('user.book-detail', compact(
            'book', 'isFavorited', 'avgRating', 'reviewCount', 'userReview', 'relatedBooks'
        ));
    }
    
    /**
     * Toggle favorit buku
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
     * Kirim ulasan buku
     */
    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);
        
        $userId = Auth::id();
        
        // Cek apakah user udah pernah review buku ini
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
     * Nampilin semua kategori
     */
    public function categories()
    {
        $categories = KategoriBuku::withCount('buku')
            ->orderBy('nama_kategori', 'asc')
            ->get();
        
        return view('user.categories', compact('categories'));
    }
    
    /**
     * Nampilin keranjang belanja user
     */
    public function cart()
    {
        $cartItems = Keranjang::where('id_user', Auth::id())
            ->with('buku')
            ->orderBy('id_keranjang', 'desc')
            ->paginate(4);
        
        // Hitung total dari semua item (bukan cuma halaman sekarang)
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
     * Tambah item ke keranjang (API)
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
            
            // Pake transaksi database biar datanya konsisten
            DB::beginTransaction();
            
            try {
                // Ambil buku buat cek stok
                $book = Buku::findOrFail($bookId);
                
                // Cek apakah item udah ada di keranjang
                $cartItem = Keranjang::where('id_user', $userId)
                    ->where('id_buku', $bookId)
                    ->lockForUpdate()
                    ->first();
                
                $newQty = $cartItem ? ($cartItem->qty + $quantity) : $quantity;
                
                // Validasi stok
                if ($newQty > $book->stok) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi. Stok tersedia: {$book->stok}"
                    ], 400);
                }
                
                if ($cartItem) {
                    // Update jumlah kalo udah ada
                    $cartItem->qty = $newQty;
                    $cartItem->save();
                } else {
                    // Bikin item keranjang baru
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
     * Ambil jumlah item keranjang (API)
     */
    public function getCartCount()
    {
        $count = Keranjang::where('id_user', Auth::id())->sum('qty');
        
        return response()->json([
            'count' => $count
        ]);
    }
    
    /**
     * Update jumlah item keranjang (API)
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
     * Hapus item dari keranjang (API)
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
     * Nampilin pesanan user
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
     * Batalin pesanan yang belum dibayar dan balikin stok
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
     * Upload bukti pembayaran offline (foto bukti bayar di kasir)
     */
    public function uploadBuktiOffline(Request $request, $orderId)
    {
        $request->validate([
            'bukti_offline' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'bukti_offline.required' => 'Foto bukti wajib diunggah.',
            'bukti_offline.image' => 'File harus berupa gambar.',
            'bukti_offline.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
            'bukti_offline.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        try {
            $pesanan = Pesanan::where('id_pesanan', $orderId)
                ->where('id_user', Auth::id())
                ->where('metode_pembayaran', 'offline')
                ->whereIn('status', ['dikirim', 'diproses'])
                ->firstOrFail();

            // Simpan fotonya
            $path = $request->file('bukti_offline')->store('bukti_offline', 'public');

            $pesanan->update(['bukti_offline' => $path]);

            // Update bukti di tabel pembayaran
            if ($pesanan->pembayaran) {
                $pesanan->pembayaran->update([
                    'bukti_pembayaran' => $path,
                ]);
            }

            return redirect()->route('user.orders')
                ->with('success', 'Bukti pembayaran offline berhasil diunggah! Menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            return redirect()->route('user.orders')
                ->with('error', 'Gagal mengunggah bukti: ' . $e->getMessage());
        }
    }

    /**
     * Nampilin profil user
     */
    public function profile()
    {
        return view('user.profile');
    }

    /**
     * Update profil user
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
     * Ganti password user
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
     * Nampilin halaman inbox pesan
     */
    public function inbox()
    {
        $userId = Auth::id();

        // Hapus pesan yang udah expired (lebih dari 24 jam dari pesan pertama)
        $firstMessage = ChatMessage::where('id_user', $userId)->orderBy('waktu', 'asc')->first();
        $chatExpiresAt = null;
        if ($firstMessage && $firstMessage->waktu->diffInHours(now()) >= 24) {
            ChatMessage::where('id_user', $userId)->delete();
            $firstMessage = null;
        }

        // Hitung sisa waktu
        if ($firstMessage) {
            $chatExpiresAt = $firstMessage->waktu->copy()->addHours(24);
        }

        // Tandai semua pesan admin jadi udah dibaca
        ChatMessage::where('id_user', $userId)
            ->where('pengirim', 'admin')
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        $messages = ChatMessage::where('id_user', $userId)
            ->orderBy('waktu', 'asc')
            ->get();

        return view('user.inbox', compact('messages', 'chatExpiresAt'));
    }

    /**
     * User ngirim pesan chat
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string|max:5000',
        ]);

        ChatMessage::create([
            'id_user' => Auth::id(),
            'pengirim' => 'user',
            'pesan' => $request->pesan,
            'waktu' => now(),
        ]);

        return redirect()->route('user.inbox')->with('success', 'Pesan terkirim');
    }

    /**
     * Ambil pesan baru buat polling (AJAX)
     */
    public function getNewMessages(Request $request)
    {
        $userId = Auth::id();
        $lastId = $request->last_id ?? 0;

        // Cek expired 24 jam
        $firstMessage = ChatMessage::where('id_user', $userId)->orderBy('waktu', 'asc')->first();
        if ($firstMessage && $firstMessage->waktu->diffInHours(now()) >= 24) {
            ChatMessage::where('id_user', $userId)->delete();
            return response()->json(['messages' => [], 'expired' => true]);
        }

        $expiresAt = $firstMessage ? $firstMessage->waktu->copy()->addHours(24)->toIso8601String() : null;

        // Tandai pesan admin jadi udah dibaca
        ChatMessage::where('id_user', $userId)
            ->where('pengirim', 'admin')
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
     * Hapus pesan inbox
     */
    public function deleteInboxMessage($id)
    {
        $pesan = ChatMessage::where('id_user', Auth::id())->findOrFail($id);
        $pesan->delete();

        return redirect()->route('user.inbox')
            ->with('success', 'Pesan berhasil dihapus');
    }

    /**
     * Cek pesan yang belum dibaca (buat notifikasi global polling)
     */
    public function checkUnreadMessages()
    {
        $userId = Auth::id();

        $unreadCount = ChatMessage::where('id_user', $userId)
            ->where('pengirim', 'admin')
            ->where('dibaca', false)
            ->count();

        $latestMessage = null;
        if ($unreadCount > 0) {
            $latest = ChatMessage::where('id_user', $userId)
                ->where('pengirim', 'admin')
                ->where('dibaca', false)
                ->orderBy('waktu', 'desc')
                ->first();

            if ($latest) {
                $latestMessage = [
                    'id_chat' => $latest->id_chat,
                    'pesan' => mb_strlen($latest->pesan) > 80 ? mb_substr($latest->pesan, 0, 80) . '...' : $latest->pesan,
                    'waktu' => $latest->waktu->format('H:i'),
                ];
            }
        }

        return response()->json([
            'unread_count' => $unreadCount,
            'latest' => $latestMessage,
        ]);
    }
    
    /**
     * Nampilin halaman checkout beserta form alamat
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
     * Proses checkout dan bikin pesanan
     */
    public function processCheckout(Request $request)
    {
        // Kalo offline, alamat gak wajib karena beli langsung di store
        $rules = [
            'metode_pembayaran' => 'required|in:midtrans,offline',
        ];
        $messages = [
            'metode_pembayaran.required' => 'Pilih metode pembayaran',
        ];

        if ($request->metode_pembayaran !== 'offline') {
            $rules['id_alamat'] = 'required|exists:alamat_pengiriman,id_alamat';
            $messages['id_alamat.required'] = 'Pilih alamat pengiriman';
            $messages['id_alamat.exists'] = 'Alamat tidak valid';
        }

        $request->validate($rules, $messages);
        
        DB::beginTransaction();
        
        try {
            $userId = Auth::id();
            
            // Pastiin alamat punya user yang bersangkutan (skip kalo offline)
            if ($request->metode_pembayaran !== 'offline' && $request->id_alamat) {
                $alamat = AlamatPengiriman::where('id_alamat', $request->id_alamat)
                    ->where('id_user', $userId)
                    ->firstOrFail();
            }
            
            // Ambil item keranjang
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
            
            // Bikin detail pesanan dan kurangin stok
            foreach ($cartItems as $item) {
                // Cek ketersediaan stok
                if ($item->buku->stok < $item->qty) {
                    DB::rollBack();
                    return redirect()->route('user.cart')
                        ->with('error', "Stok {$item->buku->judul} tidak mencukupi");
                }
                
                // Bikin detail pesanan
                PesananDetail::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_buku' => $item->id_buku,
                    'qty' => $item->qty,
                    'harga_satuan' => $item->buku->harga,
                ]);
                
                // Kurangin stok
                $item->buku->decrement('stok', $item->qty);
            }
            
            // Kosongin keranjang
            Keranjang::where('id_user', $userId)->delete();
            
            DB::commit();

            // Offline: arahkan ke halaman pesanan, Midtrans: arahkan ke halaman bayar
            if ($request->metode_pembayaran === 'offline') {
                // Bikin record pembayaran dengan status 'menunggu' buat offline
                Pembayaran::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'metode' => 'offline',
                    'jumlah' => $total,
                    'status_verifikasi' => 'menunggu',
                ]);

                return redirect()->route('user.orders')
                    ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran di kasir offline.');
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
     * Nampilin halaman pembayaran pake token Midtrans Snap
     */
    public function showPayment($orderId)
    {
        $pesanan = Pesanan::with(['details.buku', 'user'])
            ->where('id_pesanan', $orderId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // Cuma boleh bayar kalo pesanan masih pending
        if ($pesanan->status !== 'menunggu') {
            return redirect()->route('user.orders')
                ->with('error', 'Pesanan ini sudah dibayar atau dibatalkan.');
        }

        // Pake ulang snap token yang udah ada kalo masih ada
        if (!$pesanan->snap_token) {
            // Konfigurasi Midtrans
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
     * Proses pembayaran setelah Midtrans Snap sukses (dipanggil lewat AJAX dari frontend)
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

            // Bikin notifikasi inbox buat user
            PesanKontak::create([
                'id_user' => Auth::id(),
                'subjek' => 'Pesanan #' . $pesanan->id_pesanan . ' Berhasil Dibayar',
                'isi_pesan' => 'Pembayaran untuk pesanan #' . $pesanan->id_pesanan . ' sebesar Rp ' . number_format($pesanan->total_harga, 0, ',', '.') . ' telah berhasil dilakukan.',
                'tanggal' => now(),
                'balasan_admin' => 'Terima kasih! Pesanan Anda sedang dikemas dan akan segera dikirim. Anda dapat memantau status pesanan di halaman Pesanan Saya.',
                'tanggal_balas' => now(),
            ]);

            DB::commit();

            // Ambil data invoice
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
     * Download invoice PDF buat pesanan
     */
    public function downloadInvoice($orderId)
    {
        $pesanan = Pesanan::where('id_pesanan', $orderId)
            ->where('id_user', Auth::id())
            ->with(['details.buku', 'user', 'pembayaran'])
            ->firstOrFail();

        if (!in_array($pesanan->status, ['diproses', 'dikirim', 'selesai'])) {
            return redirect()->route('user.orders')->with('error', 'Invoice belum tersedia untuk pesanan ini.');
        }

        $pdf = Pdf::loadView('user.invoice-pdf', compact('pesanan'));

        return $pdf->download('invoice-' . $pesanan->id_pesanan . '.pdf');
    }

    /**
     * Handle notifikasi server-to-server dari Midtrans (webhook)
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

            // Ambil ID pesanan dari format order_id: ORDER-{id}-{timestamp}
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
                // Pembayaran masih pending — status tetap 'menunggu'
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                // Pembayaran gagal — batalin pesanan dan balikin stok
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
     * Simpan alamat pengiriman baru
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
            // Kalo diset default, matiin default yang lain
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
     * Update alamat pengiriman
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
            
            // Kalo diset default, matiin default yang lain
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
     * Hapus alamat pengiriman
     */
    public function deleteAddress($id)
    {
        try {
            $address = AlamatPengiriman::where('id_alamat', $id)
                ->where('id_user', Auth::id())
                ->firstOrFail();
            
            $address->delete();
            
            // Balikin JSON kalo request-nya AJAX
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alamat berhasil dihapus'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Alamat berhasil dihapus');
                
        } catch (\Exception $e) {
            // Balikin JSON kalo request-nya AJAX
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
