<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLostAndProductNameToQualityControlsTable extends Migration
{
    public function up()
    {
        Schema::table('quality_controls', function (Blueprint $table) {
            $table->boolean('lost')->default(false)->after('off');
            $table->string('product_name')->nullable()->after('kode_unit');
        });
    }

    public function down()
    {
        Schema::table('quality_controls', function (Blueprint $table) {
            $table->dropColumn(['lost', 'product_name']);
        });
    }
}