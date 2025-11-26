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
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id('id_keranjang');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedBigInteger('id_buku')->nullable();
            $table->integer('qty')->default(1);
            $table->timestamp('tanggal')->useCurrent();
            
            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('users')
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
        Schema::dropIfExists('keranjang');
    }
};
