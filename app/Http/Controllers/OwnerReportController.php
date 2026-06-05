<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeeklyReport;
use Illuminate\Support\Facades\Auth;


class OwnerReportController extends Controller
{
    /*
    =========================================
    HALAMAN REPORT OWNER
    =========================================
    */

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }
        $period = $request->period;
        $department = $request->department;

        if ($period) {
            $year = date('Y', strtotime($period));
            $month = date('m', strtotime($period));
        } else {
            $year = date('Y');
            $month = date('m');
        }

        $query = WeeklyReport::where('month', $month)
            ->where('year', $year);

        if ($department) {
            $query->where('department', $department);
        }

        $weekReports = $query
            ->orderBy('week', 'asc')
            ->latest()
            ->get();

        $reports = $weekReports->groupBy('week');

        return view(
            'owner.report',
            compact(
                'reports',
                'weekReports',
                'month',
                'year',
                'department'
            )
        );
    }
}