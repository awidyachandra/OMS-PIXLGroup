<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quality_controls', function (Blueprint $table) {
            $table->string('qc_type')
                  ->default('order')
                  ->after('kode_unit');
        });
    }

    public function down(): void
    {
        Schema::table('quality_controls', function (Blueprint $table) {
            $table->dropColumn('qc_type');
        });
    }
};