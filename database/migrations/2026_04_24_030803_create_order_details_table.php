<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            // relasi ke orders
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            // produk yang diminta customer
            $table->string('product_type');

            // jumlah unit yang dibutuhkan
            $table->integer('qty');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};