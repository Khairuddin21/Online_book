@extends('user.layout')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Keranjang Belanja</h1>
        <p class="page-subtitle">Kelola item belanjaan Anda sebelum checkout</p>
    </div>

    @if($cartItems->count() > 0)
    <div class="cart-layout">
        <!-- Daftar Item Keranjang -->
        <div>
            <div class="cart-items">
                @foreach($cartItems as $item)
                <div class="cart-item" data-cart-id="{{ $item->id_keranjang }}">
                    <div class="cart-item-image">
                        <img src="{{ Str::startsWith($item->buku->cover, 'http') ? $item->buku->cover : ($item->buku->cover ? asset('storage/' . $item->buku->cover) : 'https://via.placeholder.com/100x140?text=No+Cover') }}" 
                             alt="{{ $item->buku->judul }}"
                             onerror="this.src='https://via.placeholder.com/100x140?text=No+Cover'">
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

            @if($cartItems->hasPages())
            <div class="pagination-wrapper">
                <div class="custom-pagination">
                    @if($cartItems->onFirstPage())
                        <span class="pagination-btn disabled">
                            <i class="fas fa-chevron-left"></i> Previous
                        </span>
                    @else
                        <a href="{{ $cartItems->previousPageUrl() }}" class="pagination-btn">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    @endif
                    <span class="pagination-info">
                        Halaman {{ $cartItems->currentPage() }} dari {{ $cartItems->lastPage() }}
                    </span>
                    @if($cartItems->hasMorePages())
                        <a href="{{ $cartItems->nextPageUrl() }}" class="pagination-btn">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="pagination-btn disabled">
                            Next <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Ringkasan Pesanan -->
        <div class="order-summary">
            <h3 class="order-summary-title">
                <i class="fas fa-receipt"></i> Ringkasan Pesanan
            </h3>
            <div class="summary-row">
                <span>Total Item</span>
                <span id="total-items">{{ $totalItems }} item</span>
            </div>
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal-price">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row total">
                <span>Total Bayar</span>
                <span id="total-price">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('user.checkout') }}" class="btn btn-green btn-block" style="padding: 14px; font-size: 16px; margin-top: 20px;">
                <i class="fas fa-shopping-bag"></i> Checkout Sekarang
            </a>
            <a href="{{ route('user.books') }}" class="btn btn-outline-green btn-block" style="padding: 14px; font-size: 16px; margin-top: 10px;">
                <i class="fas fa-arrow-left"></i> Lanjut Belanja
            </a>
        </div>
    </div>
    @else
    <div class="cart-empty">
        <i class="fas fa-shopping-bag"></i>
        <h2>Keranjang Anda Kosong</h2>
        <p>Belum ada buku yang ditambahkan ke keranjang</p>
        <a href="{{ route('user.books') }}" class="btn btn-green" style="padding: 12px 28px; font-size: 15px;">
            Belanja Sekarang
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.dataset.cartId;
            const maxStock = parseInt(this.dataset.maxStock);
            const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
            let newQty = parseInt(this.value);
            
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
        
        input.addEventListener('keypress', function(e) {
            if (e.key < '0' || e.key > '9') {
                e.preventDefault();
            }
        });
    });
    
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
    fetch('{{ route("api.cart.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ cart_id: cartId, quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cartItem.querySelector('.qty-input').value = quantity;
            cartItem.querySelector('.subtotal-value').textContent = 
                'Rp ' + new Intl.NumberFormat('id-ID').format(data.subtotal);
            document.getElementById('total-price').textContent = 
                'Rp ' + new Intl.NumberFormat('id-ID').format(data.total);
            document.getElementById('subtotal-price').textContent = 
                'Rp ' + new Intl.NumberFormat('id-ID').format(data.total);
            updateCartBadge();
        } else {
            alert(data.message || 'Gagal mengupdate jumlah');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function deleteCartItem(cartId, cartItem) {
    fetch('{{ route("api.cart.delete", "") }}/' + cartId, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cartItem.style.opacity = '0';
            cartItem.style.transform = 'translateX(-20px)';
            setTimeout(() => location.reload(), 300);
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
    fetch('{{ route("api.cart.count") }}')
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
