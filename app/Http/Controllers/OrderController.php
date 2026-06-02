<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Unit;
use Carbon\Carbon;

class OrderController extends Controller
{
    /*
    =====================================
    FORM ORDER
    =====================================
    */

    public function index()
    {
        return view('form-order');
    }

    /*
    =====================================
    HITUNG AVAILABILITY BERDASARKAN TANGGAL
    =====================================
    */

    private function getAvailableQtyByDate($product, $startDate, $endDate, $excludeOrderId = null)
    {
        $product = trim($product);

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->startOfDay();

        /*
        =====================================
        RANGE OPERASIONAL
        start sewa - 1 hari = pickup
        end sewa + 1 hari = return
        =====================================
        */

        $requestedPickupDate = $startDate->copy()->subDay();
        $requestedReturnDate = $endDate->copy()->addDay();

        /*
        =====================================
        TOTAL UNIT UTAMA SAJA
        Backup tidak dihitung.
        Unit maintenance tidak dihitung.
        Status rented tidak langsung dikeluarkan,
        karena bisa saja rented hari ini tapi available pada tanggal order.
        =====================================
        */

        $totalUnits = Unit::whereRaw(
                'LOWER(TRIM(kategori)) = ?',
                [strtolower($product)]
            )
            ->where(function ($query) {
                $query->where('is_backup', 0)
                      ->orWhereNull('is_backup');
            })
            ->whereNotIn('status', ['maintenance'])
            ->count();

        /*
        =====================================
        USED QTY
        Cek order lain yang tanggalnya bentrok.
        =====================================
        */

        $usedQuery = OrderDetail::whereRaw(
                'LOWER(TRIM(product_type)) = ?',
                [strtolower($product)]
            )
            ->whereHas('order', function ($q) use ($requestedPickupDate, $requestedReturnDate, $excludeOrderId) {
                $q->whereIn('status', [
                    'processed',
                    'dp paid',
                    'assigned',
                    'on rent',
                    'return checking'
                ]);

                if ($excludeOrderId) {
                    $q->where('id', '!=', $excludeOrderId);
                }

                $q->where(function ($query) use ($requestedPickupDate, $requestedReturnDate) {
                    $query->whereDate('pickup_date', '<=', $requestedReturnDate)
                          ->whereDate('return_date', '>=', $requestedPickupDate);
                });
            });

        $usedQty = $usedQuery->sum('qty');

        return max($totalUnits - $usedQty, 0);
    }

    /*
    =====================================
    AMBIL HARGA UNIT
    =====================================
    */

    private function getUnitPrice($product)
    {
        $unit = Unit::whereRaw(
                'LOWER(TRIM(kategori)) = ?',
                [strtolower(trim($product))]
            )
            ->where(function ($query) {
                $query->where('is_backup', 0)
                      ->orWhereNull('is_backup');
            })
            ->whereNotIn('status', ['maintenance'])
            ->first();

        return $unit ? $unit->harga_sewa : null;
    }

    /*
    =====================================
    SIMPAN ORDER AWAL
    =====================================
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|regex:/^0[0-9]{10,}$/',
            'organization' => 'required',
            'package' => 'required',
            'product' => 'required',
            'qty' => 'required|numeric|min:1',

            // alamat penyewa
            'address' => 'required',

            // tanggal dari flatpickr
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'date' => 'required|date',

            'proposal' => 'nullable|mimes:pdf|max:2048'
        ]);

        /*
        =====================
        UPLOAD PROPOSAL
        =====================
        */

        $proposalPath = null;

        if ($request->hasFile('proposal')) {
            $proposalPath = $request->file('proposal')
                ->store('proposal', 'public');
        }

        /*
        =====================
        SIMPAN CUSTOMER
        Alamat disimpan ke customers.address,
        bukan orders.address.
        =====================
        */

        $customer = Customer::where('email', $request->email)->first();

        if ($customer) {

            if ($customer->agency != $request->agency) {
                $customer->agency = $request->agency;
            }

            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->address = $request->address;
            $customer->instagram = $request->instagram;
            $customer->organization = $request->organization;

            $customer->save();

        } else {

            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'instagram' => $request->instagram,
                'organization' => $request->organization,
                'agency' => $request->agency
            ]);
        }

        /*
        =====================================
        HITUNG TANGGAL OTOMATIS
        =====================================
        */

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->startOfDay();

        $rentalDuration = $startDate->diffInDays($endDate) + 1;

        $pickupDate = $startDate->copy()->subDay();
        $returnDate = $endDate->copy()->addDay();

        /*
        =====================================
        CEK STOK BERDASARKAN TANGGAL
        Bukan berdasarkan status available hari ini.
        =====================================
        */

        $availableQty = $this->getAvailableQtyByDate(
            $request->product,
            $startDate,
            $endDate
        );

        if ($availableQty < $request->qty) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unit tidak cukup untuk ' . $request->product .
                    '. Dibutuhkan ' . $request->qty .
                    ', tersedia ' . $availableQty . '.'
                );
        }

        /*
        =====================
        AMBIL HARGA UNIT
        =====================
        */

        $unitPrice = $this->getUnitPrice($request->product);

        if (!$unitPrice) {
            return back()
                ->withInput()
                ->with('error', 'Unit tidak tersedia');
        }

        $subtotal = $unitPrice * $request->qty;

        /*
        =====================
        SIMPAN ORDER
        address tidak disimpan ke orders.
        =====================
        */

        $order = Order::create([
            'customer_id' => $customer->id,
            'event' => $request->event,
            'package' => $request->package,

            // agar fungsi lama yang memakai kolom date tetap aman
            'date' => $startDate,

            // tanggal sewa asli dari input flatpickr
            'start_date' => $startDate,
            'end_date' => $endDate,

            // otomatis dari sistem
            'rental_duration' => $rentalDuration,
            'pickup_date' => $pickupDate,
            'return_date' => $returnDate,

            'proposal' => $proposalPath,
            'status' => 'pending approval',
            'total_price' => $subtotal,
            'discount' => 0,
            'final_price' => $subtotal
        ]);

        /*
        =====================
        SIMPAN ORDER DETAIL
        =====================
        */

        OrderDetail::create([
            'order_id' => $order->id,
            'product_type' => $request->product,
            'qty' => $request->qty,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal
        ]);

        /*
        =====================
        TAMBAH PRODUK?
        =====================
        */

        if ((int) $request->input('add_product') === 1) {
            return redirect()->to('/order/add-order/' . $order->id);
        }

        return redirect('/order')
            ->with('success', 'Order berhasil dibuat');
    }

    /*
    =====================================
    HALAMAN TAMBAH PRODUK
    =====================================
    */

    public function addProductForm($id)
    {
        $order = Order::findOrFail($id);

        return view('add-order', compact('id', 'order'));
    }

    /*
    =====================================
    SIMPAN PRODUK TAMBAHAN
    =====================================
    */

    public function storeProduct(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'product' => 'required',
            'qty' => 'required|numeric|min:1'
        ]);

        $order = Order::with('details')->findOrFail($request->order_id);

        /*
        =====================================
        AMBIL TANGGAL ORDER UTAMA
        =====================================
        */

        $startDate = $order->start_date
            ? Carbon::parse($order->start_date)->startOfDay()
            : Carbon::parse($order->date)->startOfDay();

        $endDate = $order->end_date
            ? Carbon::parse($order->end_date)->startOfDay()
            : Carbon::parse($order->date)->startOfDay();

        /*
        =====================================
        CEK STOK BERDASARKAN TANGGAL
        Current order dikecualikan.
        =====================================
        */

        $availableQty = $this->getAvailableQtyByDate(
            $request->product,
            $startDate,
            $endDate,
            $order->id
        );

        if ($availableQty < $request->qty) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unit tidak cukup untuk ' . $request->product .
                    '. Dibutuhkan ' . $request->qty .
                    ', tersedia ' . $availableQty . '.'
                );
        }

        /*
        =====================
        AMBIL HARGA UNIT
        =====================
        */

        $unitPrice = $this->getUnitPrice($request->product);

        if (!$unitPrice) {
            return back()
                ->withInput()
                ->with('error', 'Unit tidak tersedia');
        }

        $subtotal = $unitPrice * $request->qty;

        /*
        =====================
        SIMPAN ORDER DETAIL
        =====================
        */

        OrderDetail::create([
            'order_id' => $request->order_id,
            'product_type' => $request->product,
            'qty' => $request->qty,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal
        ]);

        /*
        =====================
        UPDATE TOTAL ORDER
        =====================
        */

        $order->total_price += $subtotal;
        $order->final_price += $subtotal;
        $order->save();

        return redirect('/order')
            ->with('success', 'Produk tambahan berhasil ditambahkan');
    }

    /*
    =====================================
    CEK KETERSEDIAAN UNIT
    =====================================
    */

    public function checkAvailability(Request $request)
    {
        $product = trim($request->product);

        $startInput = $request->start_date;
        $endInput = $request->end_date;

        if (!$startInput || !$endInput) {
            $startInput = $request->date;
            $endInput = $request->date;
        }

        if (!$product || !$startInput || !$endInput) {
            return response()->json([
                'available' => 0
            ]);
        }

        $availableCount = $this->getAvailableQtyByDate(
            $product,
            $startInput,
            $endInput
        );

        return response()->json([
            'available' => $availableCount
        ]);
    }
}