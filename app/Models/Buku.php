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
     * Ambil kategori yang punya buku ini
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBuku::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Ambil semua item keranjang buat buku ini
     */
    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_buku', 'id_buku');
    }

    /**
     * Ambil semua detail pesanan buat buku ini
     */
    public function pesananDetail(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'id_buku', 'id_buku');
    }

    public function favorit(): HasMany
    {
        return $this->hasMany(FavoritBuku::class, 'id_buku', 'id_buku');
    }

    public function ulasan(): HasMany
    {
        return $this->hasMany(UlasanBuku::class, 'id_buku', 'id_buku');
    }
}
