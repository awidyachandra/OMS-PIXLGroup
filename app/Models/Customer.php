<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'instagram',
        'organization',
        'agency',
        'rating',
        'review'
    ];

    // =========================
    // RELATION
    // =========================

    // 1 customer punya banyak order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // =========================
    // ACCESSOR
    // =========================

    public function getNameAttribute($value)
    {
        return strtoupper($value);
    }

    // =========================
    // MUTATOR
    // =========================

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
}