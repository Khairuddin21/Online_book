<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_bayar';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'metode',
        'jumlah',
        'bukti_pembayaran',
        'status_verifikasi',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'datetime',
    ];

    /**
     * Get the pesanan that owns this pembayaran
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
