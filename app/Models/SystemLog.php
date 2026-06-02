<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'level',
        'user',
        'activity',
        'context',
        'created_at'
    ];
}
