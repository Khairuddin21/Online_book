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
        Schema::table('pesan_kontak', function (Blueprint $table) {
            $table->text('balasan_admin')->nullable()->after('isi_pesan');
            $table->timestamp('tanggal_balas')->nullable()->after('balasan_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesan_kontak', function (Blueprint $table) {
            $table->dropColumn(['balasan_admin', 'tanggal_balas']);
        });
    }
};
