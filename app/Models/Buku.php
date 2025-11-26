<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'id_buku';
    public $timestamps = false;

    protected $fillable = [
        'id_kategori',
        'judul',
        'isbn',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'stok',
        'harga',
        'deskripsi',
        'cover',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    /**
     * Get the kategori that owns this buku
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBuku::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Get all keranjang items for this buku
     */
    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_buku', 'id_buku');
    }

    /**
     * Get all pesanan details for this buku
     */
    public function pesananDetail(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'id_buku', 'id_buku');
    }
}
