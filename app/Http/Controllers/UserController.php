<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\Keranjang;
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
     * Display contact page
     */
    public function contact()
    {
        return view('user.contact');
    }
}
