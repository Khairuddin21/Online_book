<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriBuku extends Model
{
    protected $table = 'kategori_buku';
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    /**
     * Get all buku for this kategori
     */
    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class, 'id_kategori', 'id_kategori');
    }
}
