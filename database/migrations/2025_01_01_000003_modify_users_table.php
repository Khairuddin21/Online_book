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
        Schema::table('users', function (Blueprint $table) {
            // Ganti nama kolom id jadi id_user
            $table->renameColumn('id', 'id_user');
            
            // Ganti nama kolom name jadi nama
            $table->renameColumn('name', 'nama');
            
            // Tambahin kolom baru
            $table->enum('role', ['admin', 'user'])->default('user')->after('password');
            $table->text('alamat')->nullable()->after('role');
            $table->string('no_hp', 20)->nullable()->after('alamat');
        });
    }

    /**
     * Balikin migrasi
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id_user', 'id');
            $table->renameColumn('nama', 'name');
            $table->dropColumn(['role', 'alamat', 'no_hp']);
        });
    }
};
