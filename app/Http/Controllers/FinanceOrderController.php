<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Helpers\LogHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FinanceOrderController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $totalInvoice = Order::whereNotNull('invoice_file')
            ->whereNotIn('status', ['cancelled'])
            ->sum('final_price');

        $totalPaid = Order::whereNotNull('invoice_file')
            ->whereNotIn('status', ['cancelled'])
            ->sum('paid_amount');

        $totalDp = Order::whereNotNull('invoice_file')
            ->whereNotIn('status', ['cancelled'])
            ->sum('dp_amount');

        $totalSettlement = Order::whereNotNull('invoice_file')
            ->whereNotIn('status', ['cancelled'])
            ->sum('settlement_amount');

        $outstanding = max($totalInvoice - $totalPaid, 0);

        $unpaidCount = Order::whereNotNull('invoice_file')
            ->whereNotIn('status', ['cancelled'])
            ->where('payment_status', 'unpaid')
            ->count();

        $dpPaidCount = Order::whereNotNull('invoice_file')
            ->whereNotIn('status', ['cancelled'])
            ->where('payment_status', 'dp paid')
            ->count();

        $fullyPaidCount = Order::whereNotNull('invoice_file')
            ->whereNotIn('status', ['cancelled'])
            ->where('payment_status', 'fully paid')
            ->count();

        $recentOrders = Order::with('customer')
            ->whereNotNull('invoice_file')
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->take(5)
            ->get();

        return view('finance.dashboard', compact(
            'totalInvoice',
            'totalPaid',
            'totalDp',
            'totalSettlement',
            'outstanding',
            'unpaidCount',
            'dpPaidCount',
            'fullyPaidCount',
            'recentOrders'
        ));
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $paymentStatus = $request->payment_status ?? 'all';

        $query = Order::with(['customer', 'details'])
            ->whereNotNull('invoice_file')
            ->whereNotIn('status', ['cancelled'])
            ->latest();

        if ($paymentStatus != 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('finance.orders', compact('orders', 'paymentStatus'));
    }

    public function markDpPaid(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $request->validate([
            'dp_amount' => 'required|numeric|min:1',
            'payment_notes' => 'nullable|string'
        ]);

        $order = Order::with(['customer', 'details'])->findOrFail($id);

        if (!$order->invoice_file) {
            return back()->with('error', 'Invoice utama belum dibuat oleh marketing.');
        }

        if ($order->payment_status == 'fully paid') {
            return back()->with('error', 'Order ini sudah lunas.');
        }

        $finalPrice = (float) $order->final_price;
        $dpAmount = (float) $request->dp_amount;

        if ($dpAmount > $finalPrice) {
            return back()->with('error', 'Nominal DP tidak boleh lebih besar dari total tagihan.');
        }

        $remainingAmount = max($finalPrice - $dpAmount, 0);

        $order->update([
            'payment_status' => $remainingAmount <= 0 ? 'fully paid' : 'dp paid',
            'dp_amount' => $dpAmount,
            'paid_amount' => $dpAmount,
            'remaining_amount' => $remainingAmount,
            'dp_paid_at' => now(),
            'fully_paid_at' => $remainingAmount <= 0 ? now() : null,
            'payment_notes' => $request->payment_notes
        ]);

        $order = $order->fresh(['customer', 'details']);

        /*
        =====================================
        GENERATE INVOICE DP
        =====================================
        */

        $fileName = 'invoice-dp-order-' . $order->id . '-' . time() . '.pdf';

        $pdf = Pdf::loadView('finance.invoice-dp-pdf', [
            'order' => $order,
            'dpAmount' => $dpAmount,
            'remainingAmount' => $remainingAmount,
            'paymentDate' => now()
        ]);

        Storage::disk('public')->put(
            'finance-invoices/dp/' . $fileName,
            $pdf->output()
        );

        $updateData = [
            'dp_invoice_file' => 'finance-invoices/dp/' . $fileName
        ];

        /*
        Jika DP langsung sama dengan total tagihan,
        buat juga invoice lunas.
        */
        if ($remainingAmount <= 0) {
            $fullFileName = 'invoice-lunas-order-' . $order->id . '-' . time() . '.pdf';

            $fullPdf = Pdf::loadView('finance.invoice-fully-paid-pdf', [
                'order' => $order,
                'settlementAmount' => 0,
                'paymentDate' => now()
            ]);

            Storage::disk('public')->put(
                'finance-invoices/fully-paid/' . $fullFileName,
                $fullPdf->output()
            );

            $updateData['fully_paid_invoice_file'] = 'finance-invoices/fully-paid/' . $fullFileName;
        }

        $order->update($updateData);

        LogHelper::add(
            'info',
            'Finance DP Paid (Order #' . $order->id . ')',
            'Nominal DP: Rp ' . number_format($dpAmount, 0, ',', '.') .
            ', Sisa: Rp ' . number_format($remainingAmount, 0, ',', '.')
        );

        return back()->with('success', 'Pembayaran DP berhasil disimpan dan invoice DP berhasil dibuat.');
    }

    /*
    =====================================
    MARK FULLY PAID + GENERATE INVOICE LUNAS
    =====================================
    */

    public function markFullyPaid(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $request->validate([
            'settlement_amount' => 'required|numeric|min:1',
            'payment_notes' => 'nullable|string'
        ]);

        $order = Order::with(['customer', 'details'])->findOrFail($id);

        if (!$order->invoice_file) {
            return back()->with('error', 'Invoice utama belum dibuat oleh marketing.');
        }

        if ($order->payment_status == 'fully paid') {
            return back()->with('error', 'Order ini sudah lunas.');
        }

        $finalPrice = (float) $order->final_price;
        $paidAmount = (float) $order->paid_amount;
        $remainingAmount = max($finalPrice - $paidAmount, 0);
        $settlementAmount = (float) $request->settlement_amount;

        if ($settlementAmount < $remainingAmount) {
            return back()->with(
                'error',
                'Nominal pelunasan kurang. Sisa tagihan adalah Rp ' .
                number_format($remainingAmount, 0, ',', '.')
            );
        }

        if ($settlementAmount > $remainingAmount) {
            return back()->with(
                'error',
                'Nominal pelunasan tidak boleh lebih besar dari sisa tagihan. Sisa tagihan adalah Rp ' .
                number_format($remainingAmount, 0, ',', '.')
            );
        }

        /*
        =====================================
        UPDATE DATA PEMBAYARAN LUNAS
        =====================================
        */

        $order->update([
            'payment_status' => 'fully paid',
            'settlement_amount' => $settlementAmount,
            'paid_amount' => $finalPrice,
            'remaining_amount' => 0,
            'fully_paid_at' => now(),
            'payment_notes' => $request->payment_notes ?? $order->payment_notes
        ]);

        $order = $order->fresh(['customer', 'details']);

        /*
        =====================================
        GENERATE INVOICE LUNAS
        =====================================
        */

        $fileName = 'invoice-lunas-order-' . $order->id . '-' . time() . '.pdf';

        $pdf = Pdf::loadView('finance.invoice-fully-paid-pdf', [
            'order' => $order,
            'settlementAmount' => $settlementAmount,
            'paymentDate' => now()
        ]);

        Storage::disk('public')->put(
            'finance-invoices/fully-paid/' . $fileName,
            $pdf->output()
        );

        $order->update([
            'fully_paid_invoice_file' => 'finance-invoices/fully-paid/' . $fileName
        ]);

        LogHelper::add(
            'info',
            'Finance Fully Paid (Order #' . $order->id . ')',
            'Nominal pelunasan: Rp ' . number_format($settlementAmount, 0, ',', '.')
        );

        return back()->with('success', 'Pembayaran pelunasan berhasil disimpan dan invoice lunas berhasil dibuat.');
    }

    /*
    =====================================
    DOWNLOAD INVOICE DP
    =====================================
    */

    public function downloadDpInvoice($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->dp_invoice_file) {
            return back()->with('error', 'Invoice DP belum tersedia.');
        }

        $path = storage_path('app/public/' . $order->dp_invoice_file);

        if (!file_exists($path)) {
            return back()->with('error', 'File invoice DP tidak ditemukan.');
        }

        return response()->download($path);
    }

    /*
    =====================================
    DOWNLOAD INVOICE LUNAS
    =====================================
    */

    public function downloadFullyPaidInvoice($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->fully_paid_invoice_file) {
            return back()->with('error', 'Invoice lunas belum tersedia.');
        }

        $path = storage_path('app/public/' . $order->fully_paid_invoice_file);

        if (!file_exists($path)) {
            return back()->with('error', 'File invoice lunas tidak ditemukan.');
        }

        return response()->download($path);
    }
}