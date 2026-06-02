<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'event',
        'package',
        'proposal',

        // tanggal utama
        'date',

        // tanggal sewa
        'start_date',
        'end_date',
        'rental_duration',

        // bukti pickup / return
        'pickup_photo',
        'return_photo',
        'picked_up_at',
        'returned_at',

        // tanggal operasional
        'pickup_date',
        'return_date',

        'assigned_at',
        'total_price',
        'discount',
        'final_price',
        'invoice_file',

        // status operasional
        'status',

        // status pembayaran finance
        'payment_status',
        'dp_amount',
        'settlement_amount',
        'paid_amount',
        'remaining_amount',
        'dp_invoice_file',
        'fully_paid_invoice_file',
        'dp_paid_at',
        'fully_paid_at',
        'payment_notes',

        'processed_by'
    ];

    protected $casts = [
        'date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'pickup_date' => 'date',
        'return_date' => 'date',
        'rental_duration' => 'integer',
        'dp_paid_at' => 'datetime',
        'fully_paid_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /*
    =========================================
    RELATION CUSTOMER
    =========================================
    */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /*
    =========================================
    RELATION ORDER DETAILS
    =========================================
    */

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}