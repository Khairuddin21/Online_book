<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoritBuku extends Model
{
    protected $table = 'favorit_buku';
    protected $primaryKey = 'id_favorit';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_buku',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
}
