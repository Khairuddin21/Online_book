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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamp('tanggal_pesanan')->useCurrent();
            $table->decimal('total_harga', 12, 2)->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('menunggu');
            
            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
