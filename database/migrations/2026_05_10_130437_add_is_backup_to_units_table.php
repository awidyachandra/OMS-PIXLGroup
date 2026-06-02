<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('units', 'is_backup')) {
            Schema::table('units', function (Blueprint $table) {
                $table->boolean('is_backup')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('units', 'is_backup')) {
            Schema::table('units', function (Blueprint $table) {
                $table->dropColumn('is_backup');
            });
        }
    }
};