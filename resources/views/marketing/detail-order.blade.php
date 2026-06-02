<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
 @extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <h4 class="fw-bold mb-4">Detail Order</h4>

    <div class="card p-4 shadow-sm" style="border-radius:12px;">

        <h5 class="fw-bold mb-3">
            Order #{{ $order->id }}
        </h5>

        {{-- CUSTOMER --}}
        <div class="row mb-3">

            <div class="col-md-6 mb-2">
                <b>Nama</b><br>
                {{ $order->customer->name ?? '-' }}
            </div>

            <div class="col-md-6 mb-2">
                <b>Email</b><br>
                {{ $order->customer->email ?? '-' }}
            </div>

            <div class="col-md-6 mb-2">
                <b>No Telp</b><br>
                {{ $order->customer->phone ?? '-' }}
            </div>

            <div class="col-md-6 mb-2">
                <b>Instansi</b><br>
                {{ $order->customer->agency ?? '-' }}
            </div>

        </div>

        <hr>

        {{-- ORDER --}}
        <div class="row mb-3">

            <div class="col-md-6 mb-2">
                <b>Event</b><br>
                {{ $order->event }}
            </div>

            <div class="col-md-6 mb-2">
                <b>Paket</b><br>
                {{ $order->package }}
            </div>
            <div class="col-md-12 mb-2">
    <b>Alamat</b><br>
    {{ $order->address ?? '-' }}
</div>

            <div class="col-md-4 mb-2">
    <b>Tanggal Sewa</b><br>

    @if($order->start_date && $order->end_date)
        {{ \Carbon\Carbon::parse($order->start_date)->format('d-m-Y') }}

        @if(\Carbon\Carbon::parse($order->start_date)->format('Y-m-d') != \Carbon\Carbon::parse($order->end_date)->format('Y-m-d'))
            -
            {{ \Carbon\Carbon::parse($order->end_date)->format('d-m-Y') }}
        @endif
    @else
        {{ $order->date ? \Carbon\Carbon::parse($order->date)->format('d-m-Y') : '-' }}
    @endif
</div>

            <div class="col-md-4 mb-2">
    <b>Pickup</b><br>
    {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d-m-Y') : '-' }}
</div>

<div class="col-md-4 mb-2">
    <b>Return</b><br>
    {{ $order->return_date ? \Carbon\Carbon::parse($order->return_date)->format('d-m-Y') : '-' }}
</div>

            <div class="col-md-6 mb-2">
                <b>PIC</b><br>
                {{ $order->processed_by ?? '-' }}
            </div>

            <div class="col-md-6 mb-2">
                <b>Status</b><br>

                @php
                    $isOverdue = $order->status == 'on rent'
                        && $order->return_date
                        && \Carbon\Carbon::parse($order->return_date)->lt(now());
                @endphp

                @if($isOverdue)
                    <span class="badge bg-danger">Overdue</span>
                @else
                    <span class="badge bg-secondary">
                        {{ ucwords($order->status) }}
                    </span>
                @endif
            </div>

            <div class="col-md-6 mb-2">
                <b>Total</b><br>
                Rp {{ number_format($order->total_price ?? 0,0,',','.') }}
            </div>

            <div class="col-md-6 mb-2">
                <b>Final Price</b><br>
                Rp {{ number_format($order->final_price ?? 0,0,',','.') }}
            </div>

        </div>

        <hr>

        {{-- DETAIL PRODUK + UNIT --}}
        <h5 class="fw-bold mb-3">Detail Produk</h5>

        <div class="table-responsive">
            <table class="table">

                <thead style="background:#f5f5f5;">
                    <tr>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($order->details as $detail)

                        <tr>
                            <td>{{ $detail->product_type ?? '-' }}</td>

                            {{-- UNIT --}}
                            <td>
                                @if($detail->kode_unit)
                                    <span class="badge bg-dark">
                                        {{ $detail->kode_unit }}
                                    </span>

                                    @if($detail->unit && $detail->unit->nama_unit)
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
                                Rp {{ number_format($detail->unit_price ?? 0,0,',','.') }}
                            </td>

                            <td>
                                Rp {{ number_format($detail->subtotal ?? 0,0,',','.') }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Tidak ada detail
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
            
        </div>
<div class="mt-4">
    <button onclick="window.history.back()"
            class="btn"
            style="border:1px solid #3b2a6f; color:#3b2a6f;">
        ← Back
    </button>
</div>
    </div>

</div>

@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>