<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'total_harga',
        'status',
        'snap_token',
        'metode_pembayaran',
        'bukti_offline',
    ];

    protected $casts = [
        'tanggal_pesanan' => 'datetime',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Ambil user yang punya pesanan ini
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Ambil semua detail buat pesanan ini
     */
    public function details(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'id_pesanan', 'id_pesanan');
    }
    
    /**
     * Alias buat relasi details
     */
    public function pesananDetails(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'id_pesanan', 'id_pesanan');
    }

    /**
     * Ambil pembayaran buat pesanan ini
     */
    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }
    
    /**
     * Ambil alamat pengiriman (kalo ada di session atau data terkait)
     * Catatan: alamat disimpen di session waktu checkout
     */
    public function alamatPengiriman()
    {
        // Balikin null dulu soalnya alamat disimpen di session
        // Ntar bisa ditambahin kolom id_alamat di tabel pesanan
        return null;
    }
}
