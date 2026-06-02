<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    protected $table = 'weekly_reports';

    protected $fillable = [
        'department',
        'created_by',
        'week',
        'month',
        'year',
        'report',
        'proof_file'
    ];
}