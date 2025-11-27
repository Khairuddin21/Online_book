@extends('user.layout')

@section('title', 'Checkout')

@section('content')
<div class="user-container" style="min-height: 60vh; padding: 40px 20px;">
    <h1 class="section-title" style="text-align: center; margin-bottom: 40px;">Checkout</h1>
    
    <!-- Progress Steps -->
    <div class="checkout-steps">
        <div class="step active">
            <div class="step-number">1</div>
            <div class="step-label">Pengiriman</div>
        </div>
        <div class="step-line active"></div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-label">Pembayaran</div>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success" style="margin: 20px 0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error" style="margin: 20px 0;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    
    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 30px; margin-top: 40px; align-items: start;">
        <!-- Main Content -->
        <div>
            <!-- Order Items Preview with Navbar -->
            <div class="checkout-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="color: var(--user-primary); font-size: 18px; margin: 0;">
                        <i class="fas fa-box"></i> Pesanan ({{ $cartItems->count() }} Item)
                    </h3>
                    @if($cartItems->count() > 4)
                    <button id="toggleOrderItems" class="btn btn-accent" style="padding: 8px 20px; font-size: 14px;">
                        <i class="fas fa-chevron-down"></i> Lihat Semua
                    </button>
                    @endif
                </div>
                
                <div class="order-items" id="orderItemsContainer">
                    @php
                        $displayItems = $cartItems->take(4);
                    @endphp
                    @foreach($displayItems as $item)
                    <div class="order-item order-item-visible">
                        <div class="order-item-info">
                            <img src="{{ $item->buku->cover ? asset('storage/' . $item->buku->cover) : 'https://via.placeholder.com/60x80?text=No+Cover' }}" 
                                 alt="{{ $item->buku->judul }}"
                                 style="width: 60px; height: 80px; object-fit: cover; border-radius: 5px;">
                            <div style="flex: 1; margin-left: 15px;">
                                <h4 style="font-size: 14px; margin: 0 0 5px 0; color: var(--user-dark);">{{ $item->buku->judul }}</h4>
                                <p style="font-size: 13px; color: #666; margin: 0;">{{ $item->qty }} barang</p>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 15px; font-weight: 600; color: var(--user-primary); margin: 0;">
                                Rp {{ number_format($item->buku->harga * $item->qty, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($cartItems->count() > 4)
                        @foreach($cartItems->slice(4) as $item)
                        <div class="order-item order-item-hidden" style="display: none;">
                            <div class="order-item-info">
                                <img src="{{ $item->buku->cover ? asset('storage/' . $item->buku->cover) : 'https://via.placeholder.com/60x80?text=No+Cover' }}" 
                                     alt="{{ $item->buku->judul }}"
                                     style="width: 60px; height: 80px; object-fit: cover; border-radius: 5px;">
                                <div style="flex: 1; margin-left: 15px;">
                                    <h4 style="font-size: 14px; margin: 0 0 5px 0; color: var(--user-dark);">{{ $item->buku->judul }}</h4>
                                    <p style="font-size: 13px; color: #666; margin: 0;">{{ $item->qty }} barang</p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 15px; font-weight: 600; color: var(--user-primary); margin: 0;">
                                    Rp {{ number_format($item->buku->harga * $item->qty, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
            
            <!-- Shipping Address Section -->
            <div class="checkout-section" style="margin-top: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="color: var(--user-primary); font-size: 20px; margin: 0;">
                        <i class="fas fa-shipping-fast"></i> Alamat Pengiriman
                    </h3>
                    <button id="toggleAddressForm" class="btn btn-accent" style="padding: 8px 20px; font-size: 14px;">
                        <i class="fas fa-plus"></i> Tambah Alamat
                    </button>
                </div>
                
                <!-- Saved Addresses -->
                @if($addresses->count() > 0)
                <form action="{{ route('user.checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf
                    <div class="saved-addresses">
                        @foreach($addresses as $address)
                        <label class="address-card">
                            <input type="radio" name="id_alamat" value="{{ $address->id_alamat }}" 
                                   {{ $address->is_default ? 'checked' : '' }} required>
                            <div class="address-content">
                                <div class="address-header">
                                    <span class="address-label">{{ $address->label }}</span>
                                    @if($address->is_default)
                                    <span class="badge-default">Default</span>
                                    @endif
                                </div>
                                <p class="address-name">{{ $address->nama_penerima }}</p>
                                <p class="address-phone">{{ $address->no_hp }}</p>
                                <p class="address-detail">{{ $address->alamat_lengkap }}</p>
                                <div class="address-actions">
                                    <button type="button" class="btn-edit-address" data-id="{{ $address->id_alamat }}">
                                        <i class="fas fa-edit"></i> Ubah
                                    </button>
                                    <button type="button" class="btn-delete-address" 
                                            data-address-id="{{ $address->id_alamat }}"
                                            onclick="deleteAddress({{ $address->id_alamat }})">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('id_alamat')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </form>
                @else
                <p style="color: #666; margin-bottom: 20px; text-align: center; padding: 20px;">
                    <i class="fas fa-map-marker-alt" style="font-size: 40px; display: block; margin-bottom: 10px; color: #d1d5db;"></i>
                    Belum ada alamat tersimpan. Tambahkan alamat pengiriman Anda.
                </p>
                @endif
                
                <!-- Add/Edit Address Form -->
                <div id="addressFormContainer" style="display: none; margin-top: 25px; padding-top: 25px; border-top: 2px solid #e5e7eb;">
                    <h4 style="color: var(--user-primary); margin-bottom: 20px; font-size: 18px;">
                        <span id="formTitle">Tambah Alamat Baru</span>
                    </h4>
                    
                    <form id="addressForm" method="POST" action="{{ route('user.address.store') }}">
                        @csrf
                        <input type="hidden" name="_method" value="POST" id="formMethod">
                        <input type="hidden" name="address_id" id="addressId">
                        
                        <div class="form-group">
                            <label for="label">Label Alamat <span style="color: red;">*</span></label>
                            <select id="label" name="label" class="form-input" required>
                                <option value="">Pilih Label</option>
                                <option value="Rumah">Rumah</option>
                                <option value="Kantor">Kantor</option>
                                <option value="Kos">Kos</option>
                                <option value="Apartemen">Apartemen</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_penerima">Nama Penerima <span style="color: red;">*</span></label>
                            <input type="text" 
                                   id="nama_penerima" 
                                   name="nama_penerima" 
                                   class="form-input" 
                                   value="{{ old('nama_penerima', $user->nama) }}" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="no_hp_form">Nomor HP <span style="color: red;">*</span></label>
                            <input type="text" 
                                   id="no_hp_form" 
                                   name="no_hp" 
                                   class="form-input" 
                                   value="{{ old('no_hp', $user->no_hp) }}" 
                                   placeholder="08xxxxxxxxxx"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat_lengkap">Alamat Lengkap <span style="color: red;">*</span></label>
                            <textarea id="alamat_lengkap" 
                                      name="alamat_lengkap" 
                                      class="form-input" 
                                      rows="4" 
                                      placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"
                                      required>{{ old('alamat_lengkap', $user->alamat) }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_default" id="is_default" value="1">
                                <span>Jadikan alamat utama</span>
                            </label>
                        </div>
                        
                        <button type="button" class="btn btn-outline" style="width: 100%; padding: 12px; font-size: 15px; margin-bottom: 10px;" onclick="alert('Fitur pilih lokasi dari peta akan segera hadir!')">
                            <i class="fas fa-map-marker-alt"></i> Pilih lewat Peta
                        </button>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="button" id="cancelAddressForm" class="btn btn-outline" style="flex: 1; padding: 12px;">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px;">
                                <i class="fas fa-save"></i> Simpan Alamat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Order Summary (Sticky) -->
        <div class="checkout-summary">
            <h3 style="color: var(--user-primary); margin-bottom: 20px; font-size: 20px;">Ringkasan Belanja</h3>
            
            <div class="summary-row">
                <span>Total Harga ({{ $cartItems->sum('qty') }} Barang)</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            
            <div class="summary-row">
                <span>Total Biaya Pengiriman</span>
                <span>Rp0</span>
            </div>
            
            <div class="summary-row" style="border-top: 2px solid var(--user-primary); margin-top: 15px; padding-top: 15px;">
                <span style="font-weight: 700; font-size: 18px;">Total Belanja</span>
                <span style="font-weight: 700; font-size: 18px; color: var(--user-primary);">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            
            <button type="submit" form="checkoutForm" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px; margin-top: 25px;" {{ $addresses->count() == 0 ? 'disabled' : '' }}>
                <i class="fas fa-shopping-bag"></i> Lanjut Pembayaran
            </button>
            
            @if($addresses->count() == 0)
            <p style="color: #e74c3c; font-size: 13px; margin-top: 10px; text-align: center;">
                Tambahkan alamat pengiriman terlebih dahulu
            </p>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.checkout-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 500px;
    margin: 0 auto 50px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.step-number {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #9ca3af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
    border: 3px solid #e5e7eb;
}

.step.active .step-number {
    background: var(--user-primary);
    color: white;
    border-color: var(--user-primary);
}

.step-label {
    font-size: 14px;
    font-weight: 600;
    color: #9ca3af;
}

.step.active .step-label {
    color: var(--user-primary);
}

.step-line {
    width: 120px;
    height: 3px;
    background: #e5e7eb;
    margin: 0 20px;
    margin-bottom: 35px;
}

.step-line.active {
    background: var(--user-primary);
}

.checkout-section {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #374151;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s;
}

.form-input:focus {
    outline: none;
    border-color: var(--user-primary);
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}

.form-error {
    color: #e74c3c;
    font-size: 13px;
    margin-top: 5px;
    display: block;
}

.order-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.saved-addresses {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.address-card {
    display: block;
    cursor: pointer;
    position: relative;
}

.address-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.address-content {
    padding: 20px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    transition: all 0.3s;
    background: white;
}

.address-card input[type="radio"]:checked ~ .address-content {
    border-color: var(--user-primary);
    background: #eff6ff;
}

.address-card:hover .address-content {
    border-color: var(--user-primary);
}

.address-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.address-label {
    font-weight: 700;
    color: var(--user-primary);
    font-size: 16px;
}

.badge-default {
    background: #27ae60;
    color: white;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.address-name {
    font-weight: 600;
    color: #374151;
    margin: 0 0 5px 0;
    font-size: 15px;
}

.address-phone {
    color: #666;
    margin: 0 0 8px 0;
    font-size: 14px;
}

.address-detail {
    color: #666;
    margin: 0 0 12px 0;
    font-size: 14px;
    line-height: 1.5;
}

.address-actions {
    display: flex;
    gap: 10px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
}

.btn-edit-address,
.btn-delete-address {
    background: none;
    border: 1px solid var(--user-primary);
    color: var(--user-primary);
    padding: 6px 15px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-edit-address:hover {
    background: var(--user-primary);
    color: white;
}

.btn-delete-address {
    border-color: #e74c3c;
    color: #e74c3c;
}

.btn-delete-address:hover {
    background: #e74c3c;
    color: white;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-weight: 500;
    color: #374151;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkout-summary {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    position: sticky;
    top: 100px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    font-size: 15px;
    color: #374151;
}

@media (max-width: 992px) {
    .user-container > div {
        grid-template-columns: 1fr !important;
    }
    
    .checkout-summary {
        position: static;
        margin-top: 30px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Toggle order items visibility
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleOrderItems');
    const hiddenItems = document.querySelectorAll('.order-item-hidden');
    let isExpanded = false;
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            isExpanded = !isExpanded;
            
            hiddenItems.forEach(item => {
                item.style.display = isExpanded ? 'flex' : 'none';
            });
            
            const text = isExpanded ? 'Sembunyikan' : 'Lihat Semua';
            const iconClass = isExpanded ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
            
            this.innerHTML = `<i class="${iconClass}"></i> ${text}`;
        });
    }
    
    // Toggle address form
    const toggleAddressBtn = document.getElementById('toggleAddressForm');
    const addressFormContainer = document.getElementById('addressFormContainer');
    const cancelAddressBtn = document.getElementById('cancelAddressForm');
    const addressForm = document.getElementById('addressForm');
    const formTitle = document.getElementById('formTitle');
    
    if (toggleAddressBtn) {
        toggleAddressBtn.addEventListener('click', function() {
            addressFormContainer.style.display = 'block';
            this.style.display = 'none';
            
            // Reset form to add mode
            addressForm.action = '{{ route("user.address.store") }}';
            document.getElementById('formMethod').value = 'POST';
            formTitle.textContent = 'Tambah Alamat Baru';
            addressForm.reset();
        });
    }
    
    if (cancelAddressBtn) {
        cancelAddressBtn.addEventListener('click', function() {
            addressFormContainer.style.display = 'none';
            toggleAddressBtn.style.display = 'inline-block';
            addressForm.reset();
        });
    }
    
    // Edit address buttons
    document.querySelectorAll('.btn-edit-address').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const addressId = this.dataset.id;
            const card = this.closest('.address-card');
            
            // Get address data from card
            const label = card.querySelector('.address-label').textContent;
            const nama = card.querySelector('.address-name').textContent;
            const phone = card.querySelector('.address-phone').textContent;
            const detail = card.querySelector('.address-detail').textContent;
            const isDefault = card.querySelector('.badge-default') !== null;
            
            // Fill form
            document.getElementById('label').value = label;
            document.getElementById('nama_penerima').value = nama;
            document.getElementById('no_hp_form').value = phone;
            document.getElementById('alamat_lengkap').value = detail;
            document.getElementById('is_default').checked = isDefault;
            
            // Set form to update mode
            addressForm.action = `/address/update/${addressId}`;
            document.getElementById('formMethod').value = 'POST';
            formTitle.textContent = 'Ubah Alamat';
            
            // Show form
            addressFormContainer.style.display = 'block';
            toggleAddressBtn.style.display = 'none';
            
            // Scroll to form
            addressFormContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});

// Delete address function
function deleteAddress(addressId) {
    if (!confirm('Yakin ingin menghapus alamat ini?')) {
        return;
    }
    
    fetch(`/address/delete/${addressId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.message.includes('berhasil')) {
            // Reload page to refresh address list
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menghapus alamat');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus alamat');
    });
}
</script>
@endpush
@endsection
