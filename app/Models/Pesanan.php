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
    ];

    protected $casts = [
        'tanggal_pesanan' => 'datetime',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Get the user that owns this pesanan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get all details for this pesanan
     */
    public function details(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'id_pesanan', 'id_pesanan');
    }

    /**
     * Get the pembayaran for this pesanan
     */
    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }
}
