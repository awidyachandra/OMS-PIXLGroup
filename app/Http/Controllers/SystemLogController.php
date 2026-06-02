<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemLog;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::query();

        // FILTER TANGGAL
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // SEARCH
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('activity', 'like', '%' . $request->search . '%')
                  ->orWhere('user', 'like', '%' . $request->search . '%')
                  ->orWhere('context', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->latest()->paginate(10)->withQueryString();
        return view('owner.system-log', compact('logs'));
    }
}
