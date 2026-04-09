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
        Schema::create('favorit_buku', function (Blueprint $table) {
            $table->id('id_favorit');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_buku');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_buku')->references('id_buku')->on('buku')->onDelete('cascade');
            $table->unique(['id_user', 'id_buku']);
        });
    }

    /**
     * Balikin migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('favorit_buku');
    }
};
