<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityControl extends Model
{
    protected $fillable = [
        'order_id',
        'kode_unit',
        'product_name',
        'qc_type',
        'on',
        'off',
        'lost',
        'clear',
        'result',
        'notes',
        'qc_date'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'kode_unit', 'kode_unit');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}