<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();

            // otomatis dari login
            $table->string('department');
            $table->string('created_by');

            // periode
            $table->integer('week'); // minggu ke 1-5
            $table->integer('month');
            $table->integer('year');

            // isi laporan
            $table->text('report');

            // bukti pdf/foto
            $table->string('proof_file')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_reports');
    }
};