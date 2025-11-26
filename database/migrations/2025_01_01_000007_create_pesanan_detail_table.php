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
        Schema::create('pesanan_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_pesanan')->nullable();
            $table->unsignedBigInteger('id_buku')->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('harga_satuan', 12, 2)->nullable();
            
            $table->foreign('id_pesanan')
                  ->references('id_pesanan')
                  ->on('pesanan')
                  ->onDelete('cascade');
            
            $table->foreign('id_buku')
                  ->references('id_buku')
                  ->on('buku')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_detail');
    }
};
