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
        Schema::create('alamat_pengiriman', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->unsignedBigInteger('id_user');
            $table->string('label', 50); // contoh: 'Rumah', 'Kantor', 'Kos'
            $table->string('nama_penerima', 150);
            $table->string('no_hp', 20);
            $table->text('alamat_lengkap');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Balikin migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat_pengiriman');
    }
};
