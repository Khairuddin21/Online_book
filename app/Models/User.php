<?php

namespace App\Models;

// pake Illuminate\Contracts\Auth\MustVerifyEmail kalo mau verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\FavoritBuku;
use App\Models\UlasanBuku;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> - pake factory buat bikin data user */
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    /**
     * Atribut yang bisa diisi massal
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
     * Atribut yang disembunyiin pas serialisasi
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Ambil atribut yang harus di-cast
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
     * Ambil semua pesan kontak buat user ini
     */
    public function pesanKontak(): HasMany
    {
        return $this->hasMany(PesanKontak::class, 'id_user', 'id_user');
    }

    /**
     * Ambil semua item keranjang buat user ini
     */
    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_user', 'id_user');
    }

    /**
     * Ambil semua pesanan buat user ini
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_user', 'id_user');
    }

    public function favorit(): HasMany
    {
        return $this->hasMany(FavoritBuku::class, 'id_user', 'id_user');
    }

    public function ulasan(): HasMany
    {
        return $this->hasMany(UlasanBuku::class, 'id_user', 'id_user');
    }

    /**
     * Ambil semua alamat pengiriman buat user ini
     */
    public function alamatPengiriman(): HasMany
    {
        return $this->hasMany(AlamatPengiriman::class, 'id_user', 'id_user');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'id_user', 'id_user');
    }
}
