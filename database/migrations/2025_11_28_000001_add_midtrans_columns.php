<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('status');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('midtrans_transaction_id')->nullable()->after('id_pesanan');
            $table->string('midtrans_order_id')->nullable()->after('midtrans_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('snap_token');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['midtrans_transaction_id', 'midtrans_order_id']);
        });
    }
};
