@extends('user.layout')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <h1 class="section-title">Keranjang Belanja</h1>
    
    @if($cartItems->count() > 0)
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px; margin-top: 30px; align-items: start;">
        <!-- Cart Items with Pagination -->
        <div>
            <div class="cart-items">
                @foreach($cartItems as $item)
                <div class="cart-item" data-cart-id="{{ $item->id_keranjang }}">
                    <div class="cart-item-image">
                        <img src="{{ $item->buku->cover ? asset('storage/' . $item->buku->cover) : 'https://via.placeholder.com/100x140?text=No+Cover' }}" 
                             alt="{{ $item->buku->judul }}">
                    </div>
                    <div class="cart-item-details">
                        <h3 class="cart-item-title">{{ $item->buku->judul }}</h3>
                        <p class="cart-item-author">{{ $item->buku->penulis }}</p>
                        <p class="cart-item-price">Rp {{ number_format($item->buku->harga, 0, ',', '.') }}</p>
                        <p class="cart-item-stock">Stok: <span class="stock-value">{{ $item->buku->stok }}</span></p>
                    </div>
                    <div class="cart-item-actions">
                        <div class="quantity-control">
                            <button class="qty-btn minus" data-cart-id="{{ $item->id_keranjang }}" data-max-stock="{{ $item->buku->stok }}">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   class="qty-input" 
                                   value="{{ $item->qty }}" 
                                   min="1" 
                                   max="{{ $item->buku->stok }}"
                                   data-cart-id="{{ $item->id_keranjang }}"
                                   data-max-stock="{{ $item->buku->stok }}">
                            <button class="qty-btn plus" data-cart-id="{{ $item->id_keranjang }}" data-max-stock="{{ $item->buku->stok }}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <p class="cart-item-subtotal">
                            Subtotal: <span class="subtotal-value">Rp {{ number_format($item->buku->harga * $item->qty, 0, ',', '.') }}</span>
                        </p>
                        <button class="btn-delete" data-cart-id="{{ $item->id_keranjang }}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($cartItems->hasPages())
            <div class="cart-pagination" style="margin-top: 30px;">
                <nav>
                    <ul class="pagination">
                        @foreach(range(1, $cartItems->lastPage()) as $page)
                            <li class="page-item {{ $cartItems->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $cartItems->url($page) }}">{{ $page }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
            @endif
        </div>
        
        <!-- Order Summary - Sticky/Fixed -->
        <div class="order-summary">
            <h3 style="color: var(--user-primary); margin-bottom: 20px;">Ringkasan Pesanan</h3>
            <div class="summary-row">
                <span>Total Item:</span>
                <span id="total-items">{{ $totalItems }} item</span>
            </div>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span id="subtotal-price">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span id="total-price">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <button class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px; margin-top: 20px;">
                <i class="fas fa-shopping-bag"></i> Checkout
            </button>
            <a href="{{ route('user.books') }}" class="btn btn-accent" style="width: 100%; padding: 15px; font-size: 16px; margin-top: 10px; text-align: center; display: block; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Lanjut Belanja
            </a>
        </div>
    </div>
    @else
    <div style="text-align: center; padding: 80px 20px;">
        <i class="fas fa-shopping-cart" style="font-size: 80px; color: var(--user-accent); margin-bottom: 20px;"></i>
        <h2 style="color: var(--user-primary); margin-bottom: 15px;">Keranjang Anda Kosong</h2>
        <p style="color: #666; margin-bottom: 30px;">Belum ada buku yang ditambahkan ke keranjang</p>
        <a href="{{ route('user.books') }}" class="btn btn-primary" style="padding: 12px 30px;">
            <i class="fas fa-book"></i> Belanja Sekarang
        </a>
    </div>
    @endif
</div>

@push('styles')
<style>
.cart-items {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cart-item {
    background: white;
    border-radius: 15px;
    padding: 20px;
    display: grid;
    grid-template-columns: 100px 1fr auto;
    gap: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    align-items: center;
}

.cart-item-image img {
    width: 100px;
    height: 140px;
    object-fit: cover;
    border-radius: 8px;
}

.cart-item-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.cart-item-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--user-dark);
    margin: 0;
}

.cart-item-author {
    font-size: 14px;
    color: #777;
    margin: 0;
}

.cart-item-price {
    font-size: 16px;
    font-weight: 600;
    color: var(--user-primary);
    margin: 0;
}

.cart-item-stock {
    font-size: 13px;
    color: #27ae60;
    margin: 0;
    font-weight: 500;
}

.stock-value {
    font-weight: 700;
}

.cart-item-actions {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: flex-end;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 10px;
}

.qty-btn {
    width: 35px;
    height: 35px;
    border: 1px solid var(--user-primary);
    background: white;
    color: var(--user-primary);
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.qty-btn:hover {
    background: var(--user-primary);
    color: white;
}

.qty-input {
    width: 60px;
    height: 35px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 600;
    cursor: text;
}

.qty-input:focus {
    outline: none;
    border-color: var(--user-primary);
    box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.1);
}

.cart-item-subtotal {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.subtotal-value {
    font-weight: 600;
    color: var(--user-primary);
}

.btn-delete {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-delete:hover {
    background: #c0392b;
}

.order-summary {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    height: fit-content;
    position: sticky;
    top: 100px;
    align-self: start;
}

.cart-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
}

.cart-pagination nav {
    display: flex;
    gap: 10px;
}

.cart-pagination .pagination {
    display: flex;
    gap: 5px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.cart-pagination .page-item {
    display: inline-block;
}

.cart-pagination .page-link {
    padding: 8px 15px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    color: var(--user-primary);
    text-decoration: none;
    transition: all 0.3s;
    font-weight: 500;
}

.cart-pagination .page-link:hover {
    background: var(--user-primary);
    color: white;
    border-color: var(--user-primary);
}

.cart-pagination .page-item.active .page-link {
    background: var(--user-primary);
    color: white;
    border-color: var(--user-primary);
}

.cart-pagination .page-item.disabled .page-link {
    background: #f5f5f5;
    color: #999;
    cursor: not-allowed;
    border-color: #ddd;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
    font-size: 15px;
    color: #666;
}

.summary-row.total {
    border-top: 2px solid var(--user-primary);
    border-bottom: none;
    padding-top: 15px;
    margin-top: 10px;
    font-size: 18px;
    font-weight: 700;
    color: var(--user-primary);
}

@media (max-width: 992px) {
    .user-container > div {
        grid-template-columns: 1fr !important;
    }
    
    .order-summary {
        position: static;
        margin-top: 30px;
    }
}

@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 80px 1fr;
        gap: 15px;
    }
    
    .cart-item-image img {
        width: 80px;
        height: 112px;
    }
    
    .cart-item-actions {
        grid-column: 1 / -1;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
    
    .order-summary {
        position: static;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update quantity buttons
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            const maxStock = parseInt(this.dataset.maxStock);
            const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
            const qtyInput = cartItem.querySelector('.qty-input');
            let currentQty = parseInt(qtyInput.value);
            
            if (this.classList.contains('plus')) {
                if (currentQty >= maxStock) {
                    alert(`Stok tidak mencukupi. Maksimal ${maxStock} item`);
                    return;
                }
                currentQty++;
            } else if (this.classList.contains('minus') && currentQty > 1) {
                currentQty--;
            } else {
                return;
            }
            
            updateCartQuantity(cartId, currentQty, cartItem, maxStock);
        });
    });
    
    // Manual input quantity
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.dataset.cartId;
            const maxStock = parseInt(this.dataset.maxStock);
            const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
            let newQty = parseInt(this.value);
            
            // Validasi input
            if (isNaN(newQty) || newQty < 1) {
                newQty = 1;
                this.value = 1;
            } else if (newQty > maxStock) {
                alert(`Stok tidak mencukupi. Maksimal ${maxStock} item`);
                newQty = maxStock;
                this.value = maxStock;
            }
            
            updateCartQuantity(cartId, newQty, cartItem, maxStock);
        });
        
        // Prevent typing non-numeric
        input.addEventListener('keypress', function(e) {
            if (e.key < '0' || e.key > '9') {
                e.preventDefault();
            }
        });
    });
    
    // Delete buttons
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
            
            if (confirm('Hapus buku ini dari keranjang?')) {
                deleteCartItem(cartId, cartItem);
            }
        });
    });
});

function updateCartQuantity(cartId, quantity, cartItem, maxStock) {
    fetch('/api/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            cart_id: cartId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update quantity input
            cartItem.querySelector('.qty-input').value = quantity;
            
            // Update subtotal
            cartItem.querySelector('.subtotal-value').textContent = 
                'Rp ' + new Intl.NumberFormat('id-ID').format(data.subtotal);
            
            // Update total
            document.getElementById('total-price').textContent = 
                'Rp ' + new Intl.NumberFormat('id-ID').format(data.total);
            document.getElementById('subtotal-price').textContent = 
                'Rp ' + new Intl.NumberFormat('id-ID').format(data.total);
            
            // Update cart badge
            updateCartBadge();
        } else {
            // Revert to previous value on error
            const currentValue = cartItem.querySelector('.qty-input').value;
            cartItem.querySelector('.qty-input').value = currentValue;
            alert(data.message || 'Gagal mengupdate jumlah');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function deleteCartItem(cartId, cartItem) {
    fetch(`/api/cart/delete/${cartId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove item with animation
            cartItem.style.opacity = '0';
            cartItem.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                // Reload page to update pagination and totals
                location.reload();
            }, 300);
        } else {
            alert(data.message || 'Gagal menghapus item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function updateCartBadge() {
    const badge = document.querySelector('.cart-badge');
    if (!badge) return;
    
    fetch('/api/cart/count')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'flex';
            } else {
                badge.textContent = '0';
            }
        });
}
</script>
@endpush
@endsection
