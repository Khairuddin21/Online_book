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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_bayar');
            $table->unsignedBigInteger('id_pesanan')->nullable();
            $table->string('metode', 50)->nullable();
            $table->decimal('jumlah', 12, 2)->nullable();
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'valid', 'invalid'])->default('menunggu');
            $table->timestamp('tanggal')->useCurrent();
            
            $table->foreign('id_pesanan')
                  ->references('id_pesanan')
                  ->on('pesanan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
