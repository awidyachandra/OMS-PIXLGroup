<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $primaryKey = 'kode_unit';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_unit',
        'nama_unit',
        'kategori',
        'status',
        'is_backup',
        'harga_sewa'
    ];

    protected $casts = [
        'is_backup' => 'boolean',
    ];
}