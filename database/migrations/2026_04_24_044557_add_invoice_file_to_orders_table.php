<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('invoice_file')->nullable()->after('final_price');
            $table->string('google_drive_link')->nullable()->after('invoice_file');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_file',
                'google_drive_link'
            ]);
        });
    }
};