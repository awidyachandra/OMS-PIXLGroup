<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickupReturnPhotoToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pickup_photo')->nullable()->after('assigned_at');
            $table->string('return_photo')->nullable()->after('pickup_photo');
            $table->timestamp('picked_up_at')->nullable()->after('return_photo');
            $table->timestamp('returned_at')->nullable()->after('picked_up_at');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_photo',
                'return_photo',
                'picked_up_at',
                'returned_at'
            ]);
        });
    }
}