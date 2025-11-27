<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'alamat',
        'no_hp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all pesan kontak for this user
     */
    public function pesanKontak(): HasMany
    {
        return $this->hasMany(PesanKontak::class, 'id_user', 'id_user');
    }

    /**
     * Get all keranjang items for this user
     */
    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_user', 'id_user');
    }

    /**
     * Get all pesanan for this user
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_user', 'id_user');
    }

    /**
     * Get all shipping addresses for this user
     */
    public function alamatPengiriman(): HasMany
    {
        return $this->hasMany(AlamatPengiriman::class, 'id_user', 'id_user');
    }
}
