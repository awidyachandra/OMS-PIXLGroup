<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quality_controls', function (Blueprint $table) {

            // hapus unit_id lama
            $table->dropColumn('unit_id');

            // tambah kode_unit
            $table->string('kode_unit')->after('order_id');

        });
    }

    public function down(): void
    {
        Schema::table('quality_controls', function (Blueprint $table) {

            $table->dropColumn('kode_unit');

            $table->unsignedBigInteger('unit_id');

        });
    }
};
