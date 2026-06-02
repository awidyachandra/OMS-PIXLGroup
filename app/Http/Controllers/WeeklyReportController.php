<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeeklyReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WeeklyReportController extends Controller
{
    /*
    =========================================
    HALAMAN WEEKLY REPORT
    =========================================
    */

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }
        $period = $request->period;
        $department = $request->department;

        /*
        =========================================
        FILTER BULAN & TAHUN
        =========================================
        */

        if ($period) {
            $year = date('Y', strtotime($period));
            $month = date('m', strtotime($period));
        } else {
            $year = date('Y');
            $month = date('m');
        }

        $user = Auth::user();

        /*
        =========================================
        QUERY DASAR
        =========================================
        */

        $query = WeeklyReport::where('month', $month)
            ->where('year', $year);

        /*
        =========================================
        OWNER → BISA FILTER DEPARTEMEN
        USER LAIN → HANYA MILIK SENDIRI
        =========================================
        */

        if ($user->role == 'owner') {

            if ($department) {
                $query->where('department', $department);
            }

        } else {

            $query->where('created_by', $user->name);
        }

        /*
        =========================================
        GET DATA
        =========================================
        */

        $weekReports = $query
            ->orderBy('week', 'asc')
            ->get();

        $reports = $weekReports->groupBy('week');

        return view(
            'weekly-report',
            compact(
                'reports',
                'weekReports',
                'month',
                'year',
                'department',
                'user'
            )
        );
    }

    /*
    =========================================
    STORE REPORT
    =========================================
    */

    public function store(Request $request)
{
    $request->validate([
        'week' => 'required',
        'period' => 'required',
        'report' => 'required',
        'proof_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096'
    ]);

    $filePath = null;

    if ($request->hasFile('proof_file')) {
        $filePath = $request->file('proof_file')
            ->store('weekly_reports', 'public');
    }

    /*
    =========================================
    AUTO AMBIL USER LOGIN
    =========================================
    */

    $user = Auth::user();

    $createdBy = $user->name; // atau $user->name
    $department = ucfirst($user->role); // marketing → Marketing

    WeeklyReport::create([
        'department' => $department,
        'created_by' => $createdBy,
        'week' => $request->week,
        'month' => date('m', strtotime($request->period)),
        'year' => date('Y', strtotime($request->period)),
        'report' => $request->report,
        'proof_file' => $filePath
    ]);

    return back()->with(
        'success',
        'Laporan berhasil ditambahkan'
    );
}
public function update(Request $request, $id)
{
    $request->validate([
        'report' => 'required',
        'proof_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096'
    ]);

    $weeklyReport = WeeklyReport::findOrFail($id);

    $filePath = $weeklyReport->proof_file;

    if ($request->hasFile('proof_file')) {
        $filePath = $request->file('proof_file')
            ->store('weekly_reports', 'public');
    }

    $weeklyReport->update([
        'report' => $request->report,
        'proof_file' => $filePath
    ]);

    return back()->with(
        'success',
        'Laporan berhasil diupdate'
    );
}
public function delete($id)
{
    $report = WeeklyReport::findOrFail($id);

    /*
    =========================================
    HAPUS FILE BUKTI JIKA ADA
    =========================================
    */

    if ($report->proof_file) {
        Storage::disk('public')->delete($report->proof_file);
    }

    /*
    =========================================
    HAPUS DATA REPORT
    =========================================
    */

    $report->delete();

    return back()->with(
        'success',
        'Laporan berhasil dihapus'
    );
}
}