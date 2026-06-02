<?php

namespace App\Helpers;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    public static function add($level, $activity, $context = null)
    {
        $user = Auth::check() ? Auth::user()->username : 'Guest';

        SystemLog::create([
            'level' => $level,
            'user' => $user,
            'activity' => $activity,
            'context' => $context,
            'created_at' => now()
        ]);
    }
}