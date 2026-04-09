<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update data lama dari 'cod' ke 'offline'
        DB::table('pesanan')->where('metode_pembayaran', 'cod')->update(['metode_pembayaran' => 'offline']);

        // Ganti enum dari ['midtrans', 'cod'] jadi ['midtrans', 'offline']
        DB::statement("ALTER TABLE pesanan MODIFY COLUMN metode_pembayaran ENUM('midtrans', 'offline') DEFAULT 'midtrans'");

        // Rename kolom bukti_cod jadi bukti_offline
        Schema::table('pesanan', function (Blueprint $table) {
            $table->renameColumn('bukti_cod', 'bukti_offline');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->renameColumn('bukti_offline', 'bukti_cod');
        });

        DB::table('pesanan')->where('metode_pembayaran', 'offline')->update(['metode_pembayaran' => 'cod']);
        DB::statement("ALTER TABLE pesanan MODIFY COLUMN metode_pembayaran ENUM('midtrans', 'cod') DEFAULT 'midtrans'");
    }
};
