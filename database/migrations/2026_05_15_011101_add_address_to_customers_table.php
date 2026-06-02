<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAddressToCustomersTable extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->text('address')->nullable()->after('phone');
        });

        /*
        OPTIONAL:
        Jika sebelumnya alamat sudah pernah tersimpan di orders.address,
        bagian ini akan mengambil alamat terakhir customer dari order terbaru.
        Aman jika kolom orders.address masih ada.
        */
        if (Schema::hasColumn('orders', 'address')) {
            $orders = DB::table('orders')
                ->whereNotNull('address')
                ->where('address', '!=', '')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($orders as $order) {
                DB::table('customers')
                    ->where('id', $order->customer_id)
                    ->update([
                        'address' => $order->address
                    ]);
            }
        }
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
}