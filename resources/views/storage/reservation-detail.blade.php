<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    {{-- resources/views/storage/reservation-detail.blade.php --}}

    @extends('layouts.app')

    @section('content')

        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="mb-4">
                <h4 class="fw-bold">
                    <span style="color:#999;">
                        Reservation List >
                    </span>
                    Detail Reservation
                </h4>
            </div>

            {{-- CARD DETAIL --}}
            <div class="card"
                style="
            border-radius: 12px;
            padding: 35px;
            border: 1px solid #ddd;
            background: white;
         ">

                <h2 class="fw-bold mb-4">
                    Reservation Detail #{{ $order->id }}
                </h2>

                {{-- CUSTOMER INFO --}}
                <div class="row mb-4">

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Customer Name</label>
                        <div>{{ $order->customer->name ?? '-' }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Email</label>
                        <div>{{ $order->customer->email ?? '-' }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Phone</label>
                        <div>{{ $order->customer->phone ?? '-' }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Agency</label>
                        <div>{{ $order->customer->agency ?? '-' }}</div>
                    </div>

                </div>

                <hr>

                {{-- ORDER INFO --}}
                <div class="row mt-4 mb-4">

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Event</label>
                        <div>{{ $order->event }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Package</label>
                        <div>{{ $order->package }}</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Tanggal Sewa</label>
                        <div>{{ $order->date }}</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Pickup Date (H-1)</label>
                        <div>{{ $order->pickup_date ?? '-' }}</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Return Date (H+1)</label>
                        <div>{{ $order->return_date ?? '-' }}</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Status</label>
                        <div>
                            <span class="badge bg-secondary">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Total Price</label>
                        <div>
                            Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Final Price</label>
                        <div>
                            Rp {{ number_format($order->final_price ?? 0, 0, ',', '.') }}
                        </div>
                    </div>

                </div>

                <hr>

                {{-- DETAIL PRODUK --}}
                <div class="mt-4">

                    <h4 class="fw-bold mb-3">
                        Ordered Items
                    </h4>

                    <table class="table">

                        <thead>
                            <tr>
                                <th>Product Type</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($order->details as $detail)
                                <tr>
                                    <td>{{ $detail->product_type ?? '-' }}</td>
                                    <td>
                                        @if ($detail->kode_unit)
                                            <span class="badge bg-dark">
                                                {{ $detail->kode_unit }}
                                            </span>

                                            {{-- optional tampil nama unit --}}
                                            @if ($detail->unit && $detail->unit->nama_unit)
                                                <br>
                                                <small class="text-muted">
                                                    {{ $detail->unit->nama_unit }}
                                                </small>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $detail->qty }}</td>

                                    <td>
                                        Rp {{ number_format($detail->unit_price ?? 0, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Tidak ada detail order
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- BUTTON --}}
                <div class="mt-4">

                    <a href="{{ url('/storage/reservation-list?status=' . $order->status) }}" class="btn"
                        style="
                    border:1px solid #3b2a6f;
                    color:#3b2a6f;
               ">
                        Back
                    </a>

                </div>

            </div>

        </div>

    @endsection
</body>

</html>
