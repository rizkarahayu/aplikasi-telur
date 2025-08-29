<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['kg', 'peti']); // jenis penjualan
            $table->integer('jumlah');            // jumlah kg atau jumlah peti
            $table->integer('harga_satuan');      // harga per kg atau per peti saat transaksi
            $table->integer('total');             // total uang
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
