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
            $table->boolean('dibaca_user')->default(false)->after('tanggal_balas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesan_kontak', function (Blueprint $table) {
            $table->dropColumn('dibaca_user');
        });
    }
};
