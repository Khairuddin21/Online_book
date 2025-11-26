<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananDetail extends Model
{
    protected $table = 'pesanan_detail';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'id_buku',
        'qty',
        'harga_satuan',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_satuan' => 'decimal:2',
    ];

    /**
     * Get the pesanan that owns this detail
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    /**
     * Get the buku that belongs to this detail
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
}
