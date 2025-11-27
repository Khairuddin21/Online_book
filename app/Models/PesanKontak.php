<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesanKontak extends Model
{
    protected $table = 'pesan_kontak';
    protected $primaryKey = 'id_pesan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'subjek',
        'isi_pesan',
        'tanggal',
        'balasan_admin',
        'tanggal_balas',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'tanggal_balas' => 'datetime',
    ];

    /**
     * Get the user that owns this pesan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
