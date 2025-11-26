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
        Schema::table('users', function (Blueprint $table) {
            // Rename id to id_user
            $table->renameColumn('id', 'id_user');
            
            // Rename name to nama
            $table->renameColumn('name', 'nama');
            
            // Add new columns
            $table->enum('role', ['admin', 'user'])->default('user')->after('password');
            $table->text('alamat')->nullable()->after('role');
            $table->string('no_hp', 20)->nullable()->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
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
