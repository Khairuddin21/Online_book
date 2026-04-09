<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalanin migrasi
     */
    public function up(): void
    {
        Schema::create('kategori_buku', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori', 100);
            $table->text('deskripsi')->nullable();
        });
    }

    /**
     * Balikin migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_buku');
    }
};
