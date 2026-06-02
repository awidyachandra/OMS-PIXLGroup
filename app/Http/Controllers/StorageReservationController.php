<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Storage;

class StorageReservationController extends Controller
{
    /*
    =====================================
    RESERVATION LIST
    =====================================
    */

    public function index(Request $request)
{
    if (!Auth::check()) {
        return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
    }

    $status = $request->status ?? 'processed';

    $orders = Order::with([
        'customer',
        'details'
    ])
    ->when($status == 'processed', function ($query) {
        /*
        =====================================
        TAB NEW STORAGE
        Hanya order processed yang sudah DP / lunas
        =====================================
        */
        $query->where('status', 'processed')
              ->whereIn('payment_status', ['dp paid', 'fully paid']);
    })
    ->when($status != 'processed', function ($query) use ($status) {
        $query->where('status', $status);
    })
    ->oldest()
    ->paginate(10)
    ->withQueryString();

    return view(
        'storage.reservation-list',
        compact('orders', 'status')
    );
}

    /*
    =====================================
    AMBIL UNIT AVAILABLE BERDASARKAN TANGGAL
    =====================================
    */

    private function getAvailableUnitsByDate($kategori, $pickupDate, $returnDate)
    {
        /*
        Ambil kode unit yang sudah dipakai oleh order lain
        pada rentang tanggal pickup - return.
        Jika jadwalnya overlap, unit dianggap tidak tersedia.
        */
        $busyUnitCodes = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereNotNull('order_details.kode_unit')
            ->whereNotIn('orders.status', [
                'completed',
                'cancelled'
            ])
            ->whereDate('orders.pickup_date', '<=', $returnDate)
            ->whereDate('orders.return_date', '>=', $pickupDate)
            ->pluck('order_details.kode_unit')
            ->toArray();

        /*
        Unit yang boleh diassign:
        - kategori sesuai
        - bukan backup
        - tidak maintenance
        - tidak sedang dipakai pada tanggal tersebut
        */
        return Unit::where('kategori', $kategori)
            ->where(function ($query) {
                $query->where('is_backup', 0)
                      ->orWhereNull('is_backup');
            })
            ->whereNotIn('status', [
                'maintenance'
            ])
            ->whereNotIn('kode_unit', $busyUnitCodes)
            ->orderBy('kode_unit')
            ->get();
    }

    /*
    =====================================
    AUTO ASSIGN UNIT
    =====================================
    */

    public function assign($id)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $order = Order::with('details')->findOrFail($id);

        $eventDate = Carbon::parse($order->date);
        $pickupDate = $eventDate->copy()->subDay()->format('Y-m-d');
        $returnDate = $eventDate->copy()->addDay()->format('Y-m-d');

        foreach ($order->details as $detail) {

            /*
            Ambil unit berdasarkan ketersediaan tanggal,
            bukan berdasarkan status available hari ini.
            */
            $availableUnits = $this->getAvailableUnitsByDate(
                $detail->product_type,
                $pickupDate,
                $returnDate
            )->take($detail->qty);

            if ($availableUnits->count() < $detail->qty) {
                return back()->with(
                    'error',
                    'Unit tidak cukup untuk ' . $detail->product_type . ' pada tanggal tersebut'
                );
            }

            /*
            =====================================
            HAPUS ROW LAMA
            =====================================
            */

            $detail->delete();

            /*
            =====================================
            INSERT PER UNIT
            =====================================
            */

            foreach ($availableUnits as $unit) {

                \App\Models\OrderDetail::create([
                    'order_id' => $order->id,
                    'product_type' => $detail->product_type,
                    'kode_unit' => $unit->kode_unit,
                    'qty' => 1,
                    'unit_price' => $unit->harga_sewa,
                    'subtotal' => $unit->harga_sewa
                ]);

            }
        }

        $order->update([
            'pickup_date' => $pickupDate,
            'return_date' => $returnDate,
            'assigned_at' => now(),
            'status' => 'assigned'
        ]);

        return back()->with('success', 'Auto assign berhasil');
    }

    /*
    =====================================
    UPDATE STATUS
    =====================================
    */

    public function updateStatus($id, $status)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'status' => $status
        ]);

        return back()->with(
            'success',
            'Status berhasil diupdate'
        );
    }

    /*
    =====================================
    SHOW ASSIGN PAGE
    =====================================
    */

    public function showAssignPage($id)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu!');
        }

        $order = Order::with([
            'customer',
            'details'
        ])->findOrFail($id);

        $detail = $order->details->first();

        if (!$detail) {
            return back()->with('error', 'Detail order tidak ditemukan');
        }

        $eventDate = Carbon::parse($order->date);
        $pickupDate = $eventDate->copy()->subDay()->format('Y-m-d');
        $returnDate = $eventDate->copy()->addDay()->format('Y-m-d');

        /*
        =====================================
        AMBIL UNIT TERSEDIA BERDASARKAN TANGGAL
        =====================================
        */

        $availableUnits = $this->getAvailableUnitsByDate(
            $detail->product_type,
            $pickupDate,
            $returnDate
        );

        /*
        =====================================
        AUTO SELECT SESUAI QTY
        =====================================
        */

        $autoSelected = $availableUnits
            ->take($detail->qty)
            ->pluck('kode_unit')
            ->toArray();

        return view(
            'storage.assignment',
            compact(
                'order',
                'detail',
                'availableUnits',
                'autoSelected'
            )
        );
    }

    /*
    =====================================
    STORE ASSIGN
    =====================================
    */

    public function storeAssign(Request $request, $id)
    {
        $request->validate([
            'selected_units' => 'required|array'
        ]);

        $order = Order::with('details')->findOrFail($id);

        $detail = $order->details->first();

        if (!$detail) {
            return back()->with('error', 'Detail order tidak ditemukan');
        }

        if (count($request->selected_units) != $detail->qty) {
            return back()->with(
                'error',
                'Jumlah unit yang dipilih harus sesuai dengan qty order'
            );
        }

        $eventDate = Carbon::parse($order->date);
        $pickupDate = $eventDate->copy()->subDay()->format('Y-m-d');
        $returnDate = $eventDate->copy()->addDay()->format('Y-m-d');

        /*
        =====================================
        VALIDASI UNIT BERDASARKAN TANGGAL
        =====================================
        */

        $availableUnits = $this->getAvailableUnitsByDate(
            $detail->product_type,
            $pickupDate,
            $returnDate
        );

        $validUnits = $availableUnits
            ->whereIn('kode_unit', $request->selected_units)
            ->values();

        if ($validUnits->count() != count($request->selected_units)) {
            return back()->with(
                'error',
                'Assignment gagal. Ada unit yang tidak tersedia pada tanggal tersebut, unit backup, atau unit sedang maintenance.'
            );
        }

        /*
        =====================================
        HAPUS DETAIL LAMA
        =====================================
        */

        \App\Models\OrderDetail::where('order_id', $order->id)->delete();

        /*
        =====================================
        INSERT PER UNIT
        =====================================
        */

        foreach ($validUnits as $unit) {

            \App\Models\OrderDetail::create([
                'order_id' => $order->id,
                'product_type' => $unit->kategori,
                'kode_unit' => $unit->kode_unit,
                'qty' => 1,
                'unit_price' => $unit->harga_sewa,
                'subtotal' => $unit->harga_sewa
            ]);

        }

        $order->update([
            'pickup_date' => $pickupDate,
            'return_date' => $returnDate,
            'assigned_at' => now(),
            'status' => 'assigned'
        ]);

        /*
        =====================================
        LOG
        =====================================
        */

        $unitsAssigned = $validUnits->pluck('kode_unit')->toArray();

        if (!empty($unitsAssigned)) {
            LogHelper::add(
                'info',
                'Assign Unit (Order #' . $order->id . ')',
                'Total ' . count($unitsAssigned) . ' unit → ' . implode(', ', $unitsAssigned)
            );
        }

        return redirect('/storage/reservation-list')
            ->with('success', 'Assign berhasil');
    }

    /*
    =====================================
    DETAIL RESERVATION
    =====================================
    */

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
            'storage.reservation-detail',
            compact('order')
        );
    }

    /*
    =====================================
    RETURN
    =====================================
    */

    public function return(Request $request, $id)
{
    $request->validate([
        'return_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
    ], [
        'return_photo.required' => 'Bukti foto return wajib diupload.',
        'return_photo.image' => 'File bukti return harus berupa gambar.',
        'return_photo.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
        'return_photo.max' => 'Ukuran foto maksimal 2MB.'
    ]);

    $order = Order::findOrFail($id);

    /*
    =====================================
    SIMPAN FOTO RETURN
    =====================================
    */

    $returnPhotoPath = $request->file('return_photo')
        ->store('proofs/return', 'public');

    $order->update([
        'status' => 'return checking',
        'return_photo' => $returnPhotoPath,
        'returned_at' => now()
    ]);

    LogHelper::add(
        'info',
        'Return Order (#' . $order->id . ')',
        'Barang dikembalikan dan masuk tahap QC. Bukti return: ' . $returnPhotoPath
    );

    return back()->with('success', 'Barang telah diterima dengan bukti foto return');
}

    /*
    =====================================
    PICKUP
    =====================================
    */

    public function pickup(Request $request, $id)
{
    $request->validate([
        'pickup_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
    ], [
        'pickup_photo.required' => 'Bukti foto pickup wajib diupload.',
        'pickup_photo.image' => 'File bukti pickup harus berupa gambar.',
        'pickup_photo.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
        'pickup_photo.max' => 'Ukuran foto maksimal 2MB.'
    ]);

    $order = Order::with('details')->findOrFail($id);

    /*
    =====================================
    SIMPAN FOTO PICKUP
    =====================================
    */

    $pickupPhotoPath = $request->file('pickup_photo')
        ->store('proofs/pickup', 'public');

    foreach ($order->details as $detail) {
        Unit::where('kode_unit', $detail->kode_unit)
            ->update(['status' => 'rented']);
    }

    $order->update([
        'status' => 'on rent',
        'pickup_photo' => $pickupPhotoPath,
        'picked_up_at' => now()
    ]);

    LogHelper::add(
        'info',
        'Pickup Order (#' . $order->id . ')',
        'Unit diambil oleh customer. Bukti pickup: ' . $pickupPhotoPath
    );

    return back()->with('success', 'Unit telah diambil customer dengan bukti foto pickup');
}
}