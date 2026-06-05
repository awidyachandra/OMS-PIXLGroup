<?php
// app/Http/Controllers/MarketingOrderController.php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MarketingOrderController extends Controller
{
    public function dashboard() {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        return view('marketing.dashboard');
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $status = $request->status ?? 'all';

        $query = Order::with(['customer','details'])->latest();

        if ($status == 'all') {
        } elseif ($status == 'processed') {
            $query->whereIn('status', [
                'processed',
                'assigned',
                'dp paid',
                'fully paid'
            ]);
        } elseif ($status == 'on rent') {
            $query->where('status', 'on rent');
        } elseif ($status == 'overdue') {
            $query->where('status', 'on rent')
                  ->whereDate('return_date', '<', Carbon::today());
        } else {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('marketing.orders', compact('orders', 'status'));
    }

    /*
    =========================================
    HITUNG DISCOUNT
    =========================================
    */
    private function calculateDiscount(Request $request, $totalPrice)
    {
        $totalPrice = (float) $totalPrice;

        $discountType = $request->discount_type ?? 'percent';
        $discountValue = (float) ($request->discount ?? 0);

        if ($discountValue < 0) {
            $discountValue = 0;
        }

        if ($discountType === 'percent') {
            if ($discountValue > 100) {
                $discountValue = 100;
            }

            $discountAmount = ($discountValue / 100) * $totalPrice;
        } else {
            if ($discountValue > $totalPrice) {
                $discountValue = $totalPrice;
            }

            $discountAmount = $discountValue;
        }

        $finalPrice = $totalPrice - $discountAmount;

        if ($finalPrice < 0) {
            $finalPrice = 0;
        }

        return [
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
        ];
    }

    /*
    =========================================
    CEK AVAILABILITY SAAT EDIT ORDER
    =========================================
    */
    public function checkEditAvailability(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $order = Order::with('details')->findOrFail($id);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->startOfDay();

        $requestedPickupDate = $startDate->copy()->subDay();
        $requestedReturnDate = $endDate->copy()->addDay();

        $results = [];
        $isAvailable = true;

        foreach ($order->details->groupBy('product_type') as $productType => $details) {

            $neededQty = $details->sum('qty');

            /*
            =========================================
            TOTAL STOK UTAMA SAJA
            Backup unit tidak dihitung stok utama
            =========================================
            */
            $totalStock = Unit::whereRaw(
                    'LOWER(TRIM(kategori)) = ?',
                    [strtolower(trim($productType))]
                )
                ->where('is_backup', 0)
                ->count();

            /*
            =========================================
            USED QTY ORDER LAIN
            Current order dikecualikan
            =========================================
            */
            $usedQty = DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.id', '!=', $order->id)
                ->whereRaw(
                    'LOWER(TRIM(order_details.product_type)) = ?',
                    [strtolower(trim($productType))]
                )
                ->whereIn('orders.status', [
                    'processed',
                    'dp paid',
                    'assigned',
                    'on rent',
                    'return checking'
                ])
                ->whereDate('orders.pickup_date', '<=', $requestedReturnDate)
                ->whereDate('orders.return_date', '>=', $requestedPickupDate)
                ->sum('order_details.qty');

            $availableQty = max($totalStock - $usedQty, 0);

            if ($availableQty < $neededQty) {
                $isAvailable = false;
            }

            $results[] = [
                'product_type' => $productType,
                'needed' => $neededQty,
                'total_stock' => $totalStock,
                'used' => $usedQty,
                'available' => $availableQty,
            ];
        }

        return response()->json([
            'available' => $isAvailable,
            'items' => $results,
            'message' => $isAvailable
                ? 'Unit tersedia pada tanggal tersebut.'
                : 'Unit tidak cukup pada tanggal tersebut.'
        ]);
    }

    /*
    =========================================
    UPDATE ORDER
    =========================================
    */
    public function updateOrder(Request $request, $id)
{
    $request->validate([
        'event' => 'required',
        'address' => 'required',
        'package' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $order = Order::with(['details', 'customer'])->findOrFail($id);

    if (in_array($order->status, ['on rent', 'return checking', 'completed', 'cancelled'])) {
        return back()->with('error', 'Order dengan status ini tidak dapat diedit.');
    }

    $oldStartDate = $order->start_date
        ? Carbon::parse($order->start_date)->format('Y-m-d')
        : Carbon::parse($order->date)->format('Y-m-d');

    $oldEndDate = $order->end_date
        ? Carbon::parse($order->end_date)->format('Y-m-d')
        : Carbon::parse($order->date)->format('Y-m-d');

    $newStartDate = Carbon::parse($request->start_date)->startOfDay();
    $newEndDate = Carbon::parse($request->end_date)->startOfDay();

    $newStartDateString = $newStartDate->format('Y-m-d');
    $newEndDateString = $newEndDate->format('Y-m-d');

    $dateChanged = $oldStartDate != $newStartDateString || $oldEndDate != $newEndDateString;

    if ($dateChanged) {
        $requestedPickupDate = $newStartDate->copy()->subDay();
        $requestedReturnDate = $newEndDate->copy()->addDay();

        foreach ($order->details->groupBy('product_type') as $productType => $details) {
            $neededQty = $details->sum('qty');

            $totalStock = Unit::whereRaw(
                    'LOWER(TRIM(kategori)) = ?',
                    [strtolower(trim($productType))]
                )
                ->where('is_backup', 0)
                ->count();

            $usedQty = DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.id', '!=', $order->id)
                ->whereRaw(
                    'LOWER(TRIM(order_details.product_type)) = ?',
                    [strtolower(trim($productType))]
                )
                ->whereIn('orders.status', [
                    'processed',
                    'dp paid',
                    'assigned',
                    'on rent',
                    'return checking'
                ])
                ->whereDate('orders.pickup_date', '<=', $requestedReturnDate)
                ->whereDate('orders.return_date', '>=', $requestedPickupDate)
                ->sum('order_details.qty');

            $availableQty = max($totalStock - $usedQty, 0);

            if ($availableQty < $neededQty) {
                return back()->with(
                    'error',
                    'Tanggal tidak dapat diubah karena stok ' . $productType .
                    ' tidak cukup. Dibutuhkan ' . $neededQty .
                    ', tersedia ' . $availableQty . '.'
                );
            }
        }
    }

    /*
    =========================================
    HITUNG TANGGAL OPERASIONAL
    =========================================
    */
    $rentalDuration = $newStartDate->diffInDays($newEndDate) + 1;
    $pickupDate = $newStartDate->copy()->subDay();
    $returnDate = $newEndDate->copy()->addDay();

    /*
    =========================================
    JIKA SUDAH ASSIGNED DAN TANGGAL BERUBAH
    MAKA ASSIGNMENT DIKOSONGKAN ULANG
    =========================================
    */
    $needReassign = $dateChanged && $order->status == 'assigned';

    if ($needReassign) {
        $assignedUnitCodes = $order->details
            ->pluck('kode_unit')
            ->filter()
            ->unique()
            ->values();

        foreach ($assignedUnitCodes as $kodeUnit) {
            Unit::where('kode_unit', $kodeUnit)
                ->update([
                    'status' => 'available'
                ]);
        }

        DB::table('order_details')
            ->where('order_id', $order->id)
            ->update([
                'kode_unit' => null
            ]);
    }

    /*
    =========================================
    UPDATE CUSTOMER ADDRESS
    Alamat penyewa disimpan ke customers.address
    =========================================
    */
    if ($order->customer) {
        $order->customer->update([
            'address' => $request->address
        ]);
    }

    /*
    =========================================
    UPDATE ORDER
    address tidak lagi diupdate ke orders
    =========================================
    */
    $updateData = [
        'event' => $request->event,
        'package' => $request->package,
        'date' => $newStartDate,
        'start_date' => $newStartDate,
        'end_date' => $newEndDate,
        'rental_duration' => $rentalDuration,
        'pickup_date' => $pickupDate,
        'return_date' => $returnDate,
    ];

    if ($needReassign) {
        $updateData['status'] = 'processed';
        $updateData['assigned_at'] = null;
    }

    $order->update($updateData);

    $logMessage = 'Order berhasil diupdate.';

    if ($needReassign) {
        $logMessage .= ' Tanggal berubah setelah assignment, sehingga assignment unit dikosongkan dan perlu assignment ulang.';
    }

    LogHelper::add(
        'warning',
        'Update Order (#' . $order->id . ')',
        'Customer: ' . ($order->customer->name ?? '-') .
        ', Event: ' . $request->event .
        ', Alamat customer: ' . $request->address .
        ', Tanggal: ' . $newStartDateString . ' s/d ' . $newEndDateString .
        ($needReassign ? ', Assignment ulang diperlukan' : '')
    );

    return back()->with('success', $logMessage);
}

    /*
    =========================================
    PROCESS ORDER
    =========================================
    */
    public function process(Request $request, $id)
    {
        $request->validate([
            'discount_type' => 'required|in:percent,amount',
            'discount' => 'nullable|numeric|min:0'
        ]);

        $order = Order::findOrFail($id);

        $user = Auth::user();

        $discountData = $this->calculateDiscount($request, $order->total_price);

        $order->update([
            'discount' => $discountData['discount_amount'],
            'final_price' => $discountData['final_price'],
            'processed_by' => $user->name
        ]);

        return redirect('/marketing/orders')
            ->with('success', 'Order berhasil diproses');
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'status' => 'cancelled'
        ]);

        LogHelper::add(
            'warning',
            'Cancel Order (#' . $order->id . ')',
            'Customer: ' . $order->customer->name .
            ', Event: ' . $order->event
        );

        return redirect('/marketing/orders')
            ->with('success', 'Order berhasil dibatalkan');
    }

    public function generateInvoice(Request $request, $id)
    {
        $request->validate([
            'discount_type' => 'required|in:percent,amount',
            'discount' => 'nullable|numeric|min:0'
        ]);

        $order = Order::with([
            'customer',
            'details'
        ])->findOrFail($id);

        $user = Auth::user();

        $discountData = $this->calculateDiscount($request, $order->total_price);

        $order->update([
            'discount' => $discountData['discount_amount'],
            'final_price' => $discountData['final_price'],
            'processed_by' => $user->name
        ]);

        $fileName = 'invoice-order-' . $order->id . '.pdf';

        $pdf = Pdf::loadView(
            'invoice-pdf',
            compact('order')
        );

        Storage::disk('public')->put(
            'invoices/' . $fileName,
            $pdf->output()
        );

        $order->update([
    'invoice_file' => 'invoices/' . $fileName,
    'status' => 'processed',
    'payment_status' => $order->payment_status ?? 'unpaid',
    'paid_amount' => $order->paid_amount ?? 0,
    'remaining_amount' => $order->final_price
]);

        LogHelper::add(
            'info',
            'Generate Invoice (#' . $order->id . ')',
            'Diproses oleh ' . $user->name
        );

        return $pdf->download($fileName);
    }

    public function detail($id)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $order = Order::with([
            'customer',
            'details'
        ])->findOrFail($id);

        return view(
            'marketing.detail-order',
            compact('order')
        );
    }

    public function downloadInvoice($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->invoice_file) {
            return back()->with('error', 'Invoice belum tersedia');
        }

        $path = storage_path('app/public/' . $order->invoice_file);

        if (!file_exists($path)) {
            return back()->with('error', 'File invoice tidak ditemukan');
        }

        return response()->download($path);
    }
}