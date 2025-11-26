<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id('id_buku');
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('judul', 255);
            $table->string('isbn', 50)->nullable();
            $table->string('penulis', 150)->nullable();
            $table->string('penerbit', 150)->nullable();
            $table->year('tahun_terbit')->nullable();
            $table->integer('stok')->default(0);
            $table->decimal('harga', 12, 2);
            $table->text('deskripsi')->nullable();
            $table->string('cover', 255)->nullable();
            
            $table->foreign('id_kategori')
                  ->references('id_kategori')
                  ->on('kategori_buku')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
