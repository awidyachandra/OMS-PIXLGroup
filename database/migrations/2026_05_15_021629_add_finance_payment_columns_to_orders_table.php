<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFinancePaymentColumnsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');

            $table->decimal('dp_amount', 15, 2)->default(0)->after('payment_status');
            $table->decimal('settlement_amount', 15, 2)->default(0)->after('dp_amount');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('settlement_amount');
            $table->decimal('remaining_amount', 15, 2)->default(0)->after('paid_amount');

            $table->string('dp_invoice_file')->nullable()->after('remaining_amount');
            $table->string('fully_paid_invoice_file')->nullable()->after('dp_invoice_file');

            $table->timestamp('dp_paid_at')->nullable()->after('fully_paid_invoice_file');
            $table->timestamp('fully_paid_at')->nullable()->after('dp_paid_at');

            $table->text('payment_notes')->nullable()->after('fully_paid_at');
        });

        DB::table('orders')
            ->whereNull('payment_status')
            ->update([
                'payment_status' => 'unpaid'
            ]);
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'dp_amount',
                'settlement_amount',
                'paid_amount',
                'remaining_amount',
                'dp_invoice_file',
                'fully_paid_invoice_file',
                'dp_paid_at',
                'fully_paid_at',
                'payment_notes'
            ]);
        });
    }
}