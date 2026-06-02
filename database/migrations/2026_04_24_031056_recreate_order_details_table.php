<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // hapus tabel lama jika sudah ada
        Schema::dropIfExists('order_details');

        // buat ulang tabel order_details
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            // relasi ke tabel orders
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            /*
            customer memilih jenis produk,
            bukan unit fisik
            contoh:
            - HT
            - Photobooth
            - Smoke Stage LED
            */
            $table->string('product_type');

            // jumlah unit yang dibutuhkan
            $table->integer('qty');

            /*
            harga saat order dibuat
            penting untuk invoice & histori harga
            */
            $table->decimal('unit_price', 12, 2)->default(0);

            /*
            qty × unit_price
            */
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};