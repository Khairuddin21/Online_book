<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keranjang extends Model
{
    protected $table = 'keranjang';
    protected $primaryKey = 'id_keranjang';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_buku',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
        'tanggal' => 'datetime',
    ];

    /**
     * Ambil user yang punya keranjang ini
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Ambil buku yang ada di keranjang ini
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
}
