<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlamatPengiriman extends Model
{
    protected $table = 'alamat_pengiriman';
    protected $primaryKey = 'id_alamat';
    
    protected $fillable = [
        'id_user',
        'label',
        'nama_penerima',
        'no_hp',
        'alamat_lengkap',
        'is_default',
    ];
    
    protected $casts = [
        'is_default' => 'boolean',
    ];
    
    /**
     * Get the user that owns the address
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
