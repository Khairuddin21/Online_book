@extends('user.layout')

@section('title', 'Checkout')

@section('content')
<div class="user-container" style="min-height: 60vh;">
    <div class="page-header">
        <h1 class="page-title">Checkout</h1>
        <p class="page-subtitle">Pilih metode pembayaran dan lengkapi informasi untuk melanjutkan</p>
    </div>

    <!-- Langkah-Langkah Checkout -->
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

    <div class="checkout-layout">
        <!-- Konten Utama -->
        <div>
            <!-- Preview Item Pesanan -->
            <div class="checkout-section">
                <div class="checkout-section-header">
                    <h3><i class="fas fa-box"></i> Pesanan ({{ $cartItems->count() }} Item)</h3>
                    @if($cartItems->count() > 4)
                    <button id="toggleOrderItems" class="btn btn-outline-green btn-sm">
                        <i class="fas fa-chevron-down"></i> Lihat Semua
                    </button>
                    @endif
                </div>

                <div class="order-items" id="orderItemsContainer">
                    @php $displayItems = $cartItems->take(4); @endphp
                    @foreach($displayItems as $item)
                    <div class="order-item order-item-visible">
                        <div class="order-item-info">
                            <img src="{{ Str::startsWith($item->buku->cover, 'http') ? $item->buku->cover : ($item->buku->cover ? asset('storage/' . $item->buku->cover) : 'https://via.placeholder.com/60x80?text=No+Cover') }}" 
                                 alt="{{ $item->buku->judul }}"
                                 class="order-item-img"
                                 onerror="this.src='https://via.placeholder.com/60x80?text=No+Cover'">
                            <div class="order-item-detail">
                                <h4>{{ $item->buku->judul }}</h4>
                                <p>{{ $item->qty }} barang</p>
                            </div>
                        </div>
                        <div class="order-item-price">
                            Rp {{ number_format($item->buku->harga * $item->qty, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach

                    @if($cartItems->count() > 4)
                        @foreach($cartItems->slice(4) as $item)
                        <div class="order-item order-item-hidden" style="display: none;">
                            <div class="order-item-info">
                                <img src="{{ Str::startsWith($item->buku->cover, 'http') ? $item->buku->cover : ($item->buku->cover ? asset('storage/' . $item->buku->cover) : 'https://via.placeholder.com/60x80?text=No+Cover') }}" 
                                     alt="{{ $item->buku->judul }}"
                                     class="order-item-img"
                                     onerror="this.src='https://via.placeholder.com/60x80?text=No+Cover'">
                                <div class="order-item-detail">
                                    <h4>{{ $item->buku->judul }}</h4>
                                    <p>{{ $item->qty }} barang</p>
                                </div>
                            </div>
                            <div class="order-item-price">
                                Rp {{ number_format($item->buku->harga * $item->qty, 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Bagian Alamat Pengiriman -->
            <div class="checkout-section" id="shippingSection" style="margin-top: 24px;">
                <div class="checkout-section-header">
                    <h3><i class="fas fa-shipping-fast"></i> Alamat Pengiriman</h3>
                    <button id="toggleAddressForm" class="btn btn-outline-green btn-sm">
                        <i class="fas fa-plus"></i> Tambah Alamat
                    </button>
                </div>

                <form action="{{ route('user.checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf

                @if($addresses->count() > 0)
                    <div class="saved-addresses" id="addressList">
                        @foreach($addresses as $address)
                        <label class="address-card">
                            <input type="radio" name="id_alamat" value="{{ $address->id_alamat }}" 
                                   {{ $address->is_default ? 'checked' : '' }}>
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
                @else
                <div class="address-empty" id="addressEmpty">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Belum ada alamat tersimpan. Tambahkan alamat pengiriman Anda.</p>
                </div>
                @endif
                </form>

                <!-- Form Tambah/Edit Alamat -->
                <div id="addressFormContainer" class="address-form-container" style="display: none;">
                    <h4 class="address-form-title">
                        <span id="formTitle">Tambah Alamat Baru</span>
                    </h4>

                    <form id="addressForm" method="POST" action="{{ route('user.address.store') }}">
                        @csrf
                        <input type="hidden" name="_method" value="POST" id="formMethod">
                        <input type="hidden" name="address_id" id="addressId">

                        <div class="form-group">
                            <label class="form-label">Label Alamat <span class="required">*</span></label>
                            <select id="label" name="label" class="form-input" required>
                                <option value="">Pilih Label</option>
                                <option value="Rumah">Rumah</option>
                                <option value="Kantor">Kantor</option>
                                <option value="Kos">Kos</option>
                                <option value="Apartemen">Apartemen</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="nama_penerima">Nama Penerima <span class="required">*</span></label>
                            <input type="text" id="nama_penerima" name="nama_penerima" class="form-input" 
                                   value="{{ old('nama_penerima', $user->nama) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="no_hp_form">Nomor HP <span class="required">*</span></label>
                            <input type="text" id="no_hp_form" name="no_hp" class="form-input" 
                                   value="{{ old('no_hp', $user->no_hp) }}" placeholder="08xxxxxxxxxx" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="alamat_lengkap">Alamat Lengkap <span class="required">*</span></label>
                            <textarea id="alamat_lengkap" name="alamat_lengkap" class="form-input" rows="4" 
                                      placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"
                                      required>{{ old('alamat_lengkap', $user->alamat) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_default" id="is_default" value="1">
                                <span>Jadikan alamat utama</span>
                            </label>
                        </div>

                        <button type="button" class="btn btn-outline btn-block" style="margin-bottom: 12px;" onclick="alert('Fitur pilih lokasi dari peta akan segera hadir!')">
                            <i class="fas fa-map-marker-alt"></i> Pilih lewat Peta
                        </button>

                        <div class="address-form-actions">
                            <button type="button" id="cancelAddressForm" class="btn btn-outline">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-green">
                                <i class="fas fa-save"></i> Simpan Alamat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pesanan -->
        <div class="checkout-summary">
            <h3 class="order-summary-title">
                <i class="fas fa-receipt"></i> Ringkasan Belanja
            </h3>

            <div class="summary-row">
                <span>Total Harga ({{ $cartItems->sum('qty') }} Barang)</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Biaya Pengiriman</span>
                <span>Rp0</span>
            </div>
            <div class="summary-row total">
                <span>Total Belanja</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <!-- Pilih Metode Pembayaran -->
            <div class="payment-method-section">
                <h4 class="pm-section-title"><i class="fas fa-wallet"></i> Metode Pembayaran</h4>
                <div class="pm-options">
                    <label class="pm-option">
                        <input type="radio" name="metode_pembayaran" value="midtrans" form="checkoutForm" checked>
                        <div class="pm-option-content">
                            <div class="pm-option-icon"><i class="fas fa-credit-card"></i></div>
                            <div class="pm-option-text">
                                <span class="pm-option-name">Online Payment</span>
                                <span class="pm-option-desc">Transfer Bank, E-Wallet, QRIS, Kartu Kredit</span>
                            </div>
                        </div>
                    </label>
                    <label class="pm-option">
                        <input type="radio" name="metode_pembayaran" value="offline" form="checkoutForm">
                        <div class="pm-option-content">
                            <div class="pm-option-icon"><i class="fas fa-store"></i></div>
                            <div class="pm-option-text">
                                <span class="pm-option-name">Payment Offline</span>
                                <span class="pm-option-desc">Bayar langsung di kasir offline</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" form="checkoutForm" class="btn btn-green btn-block" id="checkoutSubmitBtn"
                    style="padding: 14px; font-size: 16px; margin-top: 20px;" 
                    {{ $addresses->count() == 0 ? 'disabled' : '' }}>
                <i class="fas fa-shopping-bag"></i> Lanjut Pembayaran
            </button>

            <p class="checkout-warning" id="addressWarning" style="{{ $addresses->count() == 0 ? '' : 'display:none;' }}">
                <i class="fas fa-exclamation-circle"></i> Tambahkan alamat pengiriman terlebih dahulu
            </p>

            <!-- Info muncul kalo pilih offline -->
            <div id="offlineInfo" style="display:none; margin-top: 12px; padding: 12px 16px; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 10px; font-size: 13px; color: #92400e;">
                <i class="fas fa-store" style="margin-right: 6px;"></i>
                Pembayaran offline — Anda bisa langsung datang ke store kami untuk mengambil dan membayar buku.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === Toggle payment method: sembunyiin alamat kalo offline ===
    const paymentRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
    const shippingSection = document.getElementById('shippingSection');
    const submitBtn = document.getElementById('checkoutSubmitBtn');
    const addressWarning = document.getElementById('addressWarning');
    const offlineInfo = document.getElementById('offlineInfo');
    const hasAddresses = {{ $addresses->count() > 0 ? 'true' : 'false' }};

    function toggleShipping() {
        const selectedMethod = document.querySelector('input[name="metode_pembayaran"]:checked');
        const isOffline = selectedMethod && selectedMethod.value === 'offline';

        if (isOffline) {
            // Sembunyiin section alamat, enable tombol, tampilin info offline
            shippingSection.style.display = 'none';
            submitBtn.disabled = false;
            if (addressWarning) addressWarning.style.display = 'none';
            offlineInfo.style.display = 'block';
            submitBtn.innerHTML = '<i class="fas fa-store"></i> Buat Pesanan Offline';
        } else {
            // Tampilin lagi section alamat
            shippingSection.style.display = '';
            offlineInfo.style.display = 'none';
            submitBtn.innerHTML = '<i class="fas fa-shopping-bag"></i> Lanjut Pembayaran';
            if (!hasAddresses) {
                submitBtn.disabled = true;
                if (addressWarning) addressWarning.style.display = '';
            } else {
                submitBtn.disabled = false;
                if (addressWarning) addressWarning.style.display = 'none';
            }
        }
    }

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', toggleShipping);
    });

    // Jalanin pas load pertama kali
    toggleShipping();

    // === Toggle order items ===
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

    const toggleAddressBtn = document.getElementById('toggleAddressForm');
    const addressFormContainer = document.getElementById('addressFormContainer');
    const cancelAddressBtn = document.getElementById('cancelAddressForm');
    const addressForm = document.getElementById('addressForm');
    const formTitle = document.getElementById('formTitle');

    if (toggleAddressBtn) {
        toggleAddressBtn.addEventListener('click', function() {
            addressFormContainer.style.display = 'block';
            this.style.display = 'none';
            addressForm.action = '{{ route("user.address.store") }}';
            document.getElementById('formMethod').value = 'POST';
            formTitle.textContent = 'Tambah Alamat Baru';
            addressForm.reset();
        });
    }

    if (cancelAddressBtn) {
        cancelAddressBtn.addEventListener('click', function() {
            addressFormContainer.style.display = 'none';
            toggleAddressBtn.style.display = 'inline-flex';
            addressForm.reset();
        });
    }

    document.querySelectorAll('.btn-edit-address').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const addressId = this.dataset.id;
            const card = this.closest('.address-card');

            const label = card.querySelector('.address-label').textContent;
            const nama = card.querySelector('.address-name').textContent;
            const phone = card.querySelector('.address-phone').textContent;
            const detail = card.querySelector('.address-detail').textContent;
            const isDefault = card.querySelector('.badge-default') !== null;

            document.getElementById('label').value = label;
            document.getElementById('nama_penerima').value = nama;
            document.getElementById('no_hp_form').value = phone;
            document.getElementById('alamat_lengkap').value = detail;
            document.getElementById('is_default').checked = isDefault;

            addressForm.action = `/address/update/${addressId}`;
            document.getElementById('formMethod').value = 'POST';
            formTitle.textContent = 'Ubah Alamat';

            addressFormContainer.style.display = 'block';
            toggleAddressBtn.style.display = 'none';
            addressFormContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});

function deleteAddress(addressId) {
    if (!confirm('Yakin ingin menghapus alamat ini?')) return;

    fetch(`/address/delete/${addressId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || (data.message && data.message.includes('berhasil'))) {
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
