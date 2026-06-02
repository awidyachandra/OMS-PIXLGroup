<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'product_type',
        'kode_unit',
        'qty',
        'unit_price',
        'subtotal'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'kode_unit', 'kode_unit');
    }
}