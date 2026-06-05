<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Unit;
use App\Models\QualityControl;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QualityControlController extends Controller
{
    public function index(Request $request)
    {
        if ($request->period) {
            $year = date('Y', strtotime($request->period));
            $month = date('m', strtotime($request->period));
        } else {
            $year = date('Y');
            $month = date('m');
        }

        $qc = QualityControl::with(['unit', 'order.customer'])
            ->whereMonth('qc_date', $month)
            ->whereYear('qc_date', $year)
            ->when($request->type, function ($q) use ($request) {
                $q->where('qc_type', $request->type);
            })
            ->latest()
            ->get()
            ->groupBy(function ($item) {
                return $item->order_id ?? 'monthly';
            });

        return view('storage.qc-dashboard', compact('qc', 'month', 'year'));
    }

    public function pending()
    {
        $orders = Order::with(['customer', 'details'])
            ->where('status', 'return checking')
            ->oldest()
            ->get();

        return view('storage.qc-pending', compact('orders'));
    }

    public function input($id)
    {
        $order = Order::with('details.unit')->findOrFail($id);

        return view('storage.qc-input', compact('order'));
    }

    public function store(Request $request, $id)
    {
        $order = Order::with('details')->findOrFail($id);

        $goodCount = 0;
        $badCount = 0;
        $lostCount = 0;
        $units = [];
        $replacementLogs = [];

        foreach ($order->details as $detail) {

            $kodeUnit = $detail->kode_unit;

            $unit = Unit::where('kode_unit', $kodeUnit)->first();

            $data = $request->units[$kodeUnit] ?? [];

            /*
            =========================
            STATUS QC
            Default ON jika tidak ada input
            =========================
            */

            $qcStatus = $data['status'] ?? 'on';

            $on = $qcStatus == 'on';
            $off = $qcStatus == 'off';
            $lost = $qcStatus == 'lost';

            /*
            =========================
            LOGIKA QC
            ON     = available / Good
            OFF    = maintenance / Need Maintenance
            HILANG = unit dihapus dari data unit
            =========================
            */

            if ($on) {
                $result = 'Good';
                $status = 'available';
                $goodCount++;
            } elseif ($off) {
                $result = 'Need Maintenance';
                $status = 'maintenance';
                $badCount++;
            } else {
                $result = 'Lost';
                $status = 'lost';
                $lostCount++;
            }

            $units[] = $kodeUnit;

            $note = $request->notes[$kodeUnit] ?? null;

            /*
            =========================
            SIMPAN QC TERLEBIH DAHULU
            product_name disimpan agar tetap tampil di dashboard
            meskipun unit dihapus karena hilang
            =========================
            */

            QualityControl::create([
                'order_id' => $order->id,
                'kode_unit' => $kodeUnit,
                'product_name' => $unit->nama_unit ?? $detail->product_type,
                'qc_type' => 'order',
                'on' => $on,
                'off' => $off,
                'lost' => $lost,
                'clear' => false,
                'result' => $result,
                'notes' => $note,
                'qc_date' => now()
            ]);

            /*
            =========================
            UPDATE / HAPUS UNIT HASIL QC
            =========================
            */

            if ($lost) {

                /*
                Jika unit hilang dan masih dipakai order berikutnya,
                sistem tetap mencoba mengganti dengan backup terlebih dahulu.
                */
                $replacementResult = $this->replaceBrokenUnitForUpcomingOrders($kodeUnit, $order);

                if (!empty($replacementResult)) {
                    foreach ($replacementResult as $log) {
                        $replacementLogs[] = $log;
                    }
                }

                /*
                Hapus data unit dari tabel units.
                */
                Unit::where('kode_unit', $kodeUnit)->delete();

            } else {

                Unit::where('kode_unit', $kodeUnit)
                    ->update(['status' => $status]);

                /*
                AUTO REPLACEMENT BACKUP
                Jika unit OFF / maintenance, cek order berikutnya
                */
                if ($off) {
                    $replacementResult = $this->replaceBrokenUnitForUpcomingOrders($kodeUnit, $order);

                    if (!empty($replacementResult)) {
                        foreach ($replacementResult as $log) {
                            $replacementLogs[] = $log;
                        }
                    }
                }
            }
        }

        /*
        =========================
        UPDATE ORDER
        =========================
        */

        $order->update([
            'status' => 'completed'
        ]);

        /*
        =========================
        LOG QC
        =========================
        */

        $context = 'Total: ' . count($units) .
            ' unit | Good: ' . $goodCount .
            ', Maintenance: ' . $badCount .
            ', Hilang: ' . $lostCount .
            ' → ' . implode(', ', $units);

        if (!empty($replacementLogs)) {
            $context .= "\n\nAuto Replacement:\n" . implode("\n", $replacementLogs);
        }

        LogHelper::add(
            'info',
            'Quality Control Order (#' . $order->id . ')',
            $context
        );

        /*
        =========================
        REDIRECT + POPUP DATA
        =========================
        Jika ada replacement backup, kirim data ke session
        agar bisa ditampilkan sebagai popup di dashboard QC.
        =========================
        */

        $redirect = redirect('/storage/quality-control')
            ->with('success', 'QC selesai');

        if (!empty($replacementLogs)) {
            $redirect->with('replacementLogs', $replacementLogs);
        }

        return $redirect;
    }

        /*
        =========================================
        AUTO REPLACE UNIT RUSAK / HILANG KE BACKUP
        =========================================
        */

    private function replaceBrokenUnitForUpcomingOrders($brokenKodeUnit, $currentOrder)
{
    $logs = [];

    $brokenUnit = Unit::where('kode_unit', $brokenKodeUnit)->first();

    if (!$brokenUnit) {
        return $logs;
    }

    /*
    Ambil tanggal mulai pengecekan.
    Prioritas dari return_date order yang baru selesai.
    */
    $startDate = $currentOrder->return_date
        ? Carbon::parse($currentOrder->return_date)->format('Y-m-d')
        : Carbon::today()->format('Y-m-d');

    /*
    Cari order berikutnya yang masih memakai unit rusak / hilang.
    Status completed/cancelled tidak perlu dicek.
    */
    $upcomingDetails = DB::table('order_details')
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->where('order_details.kode_unit', $brokenKodeUnit)
        ->where('orders.id', '!=', $currentOrder->id)
        ->whereNotIn('orders.status', ['completed', 'cancelled'])
        ->whereDate('orders.pickup_date', '>=', $startDate)
        ->select(
            'order_details.id as detail_id',
            'order_details.order_id',
            'orders.pickup_date',
            'orders.return_date',
            'orders.status'
        )
        ->orderBy('orders.pickup_date', 'asc')
        ->get();

    foreach ($upcomingDetails as $upcoming) {

        $pickupDate = $upcoming->pickup_date
            ? Carbon::parse($upcoming->pickup_date)->format('Y-m-d')
            : $startDate;

        $returnDate = $upcoming->return_date
            ? Carbon::parse($upcoming->return_date)->format('Y-m-d')
            : $pickupDate;

        $backupUnit = $this->findAvailableBackupUnit(
            $brokenUnit,
            $pickupDate,
            $returnDate
        );

        if ($backupUnit) {

            /*
            Ganti unit rusak / hilang di order berikutnya dengan backup.
            */
            DB::table('order_details')
                ->where('id', $upcoming->detail_id)
                ->update([
                    'kode_unit' => $backupUnit->kode_unit
                ]);

            /*
            Backup tidak perlu diubah status menjadi assigned.
            Status unit tetap available sampai nanti pickup dilakukan.
            */

            $logs[] = $brokenKodeUnit .
                ' pada Order #' . $upcoming->order_id .
                ' diganti dengan backup ' . $backupUnit->kode_unit;

        } else {

            $logs[] = $brokenKodeUnit .
                ' pada Order #' . $upcoming->order_id .
                ' belum bisa diganti karena backup tidak tersedia';
        }
    }

    return $logs;
}

    /*
    =========================================
    CARI BACKUP AVAILABLE YANG TIDAK BENTROK JADWAL
    =========================================
    */

    private function findAvailableBackupUnit($brokenUnit, $pickupDate, $returnDate)
    {
        $busyUnitCodes = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['completed', 'cancelled'])
            ->whereDate('orders.pickup_date', '<=', $returnDate)
            ->whereDate('orders.return_date', '>=', $pickupDate)
            ->pluck('order_details.kode_unit')
            ->toArray();

        $query = Unit::where('is_backup', 1)
            ->where('status', 'available')
            ->where('kategori', $brokenUnit->kategori)
            ->whereNotIn('kode_unit', $busyUnitCodes)
            ->orderBy('kode_unit', 'asc');

        return $query->first();
    }

    public function monthly()
    {
        $units = Unit::where('status', 'available')
            ->orderBy('kode_unit')
            ->get();

        return view('storage.qc-monthly', compact('units'));
    }

    // STORE QC BULANAN
    public function storeMonthly(Request $request)
    {
        $goodCount = 0;
        $badCount = 0;
        $lostCount = 0;
        $units = [];

        foreach ($request->units as $kodeUnit => $value) {

            $unit = Unit::where('kode_unit', $kodeUnit)->first();

            /*
            =========================
            STATUS QC
            Default ON
            =========================
            */

            $qcStatus = $value['status'] ?? 'on';

            $on = $qcStatus == 'on';
            $off = $qcStatus == 'off';
            $lost = $qcStatus == 'lost';

            /*
            =========================
            LOGIKA QC
            =========================
            */

            if ($on) {
                $result = 'Good';
                $status = 'available';
                $goodCount++;
            } elseif ($off) {
                $result = 'Need Maintenance';
                $status = 'maintenance';
                $badCount++;
            } else {
                $result = 'Lost';
                $status = 'lost';
                $lostCount++;
            }

            $units[] = $kodeUnit;

            $note = $request->notes[$kodeUnit] ?? null;

            /*
            =========================
            SIMPAN QC TERLEBIH DAHULU
            =========================
            */

            QualityControl::create([
                'order_id' => null,
                'kode_unit' => $kodeUnit,
                'product_name' => $unit->nama_unit ?? '-',
                'qc_type' => 'monthly',
                'on' => $on,
                'off' => $off,
                'lost' => $lost,
                'clear' => false,
                'result' => $result,
                'notes' => $note,
                'qc_date' => now()
            ]);

            /*
            =========================
            UPDATE / HAPUS UNIT
            =========================
            */

            if ($lost) {
                Unit::where('kode_unit', $kodeUnit)->delete();
            } else {
                Unit::where('kode_unit', $kodeUnit)
                    ->update(['status' => $status]);
            }
        }

        LogHelper::add(
            'info',
            'Quality Control Bulanan',
            'Total: ' . count($units) .
            ' unit | Good: ' . $goodCount .
            ', Maintenance: ' . $badCount .
            ', Hilang: ' . $lostCount
        );

        return redirect('/storage/quality-control')
            ->with('success', 'QC bulanan berhasil');
    }
}