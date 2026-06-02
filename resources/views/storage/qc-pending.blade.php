<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
   @extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h3 class="fw-bold mb-4">QC Pending</h3>

    {{-- BUTTON QC BULANAN --}}
    <div class="mb-4">
        <a href="/storage/quality-control/monthly"
           class="btn text-white"
           style="background:#3b2a6f;">
            QC Bulanan (Semua Unit)
        </a>
    </div>

    <div class="card p-4 shadow-sm">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Event</th>
                    <th>Tanggal</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            @forelse($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->event }}</td>

                <td>
                    {{ \Carbon\Carbon::parse($order->date)->format('d M Y') }}
                    <br>
                    <small class="text-muted">
                        {{ $order->pickup_date }} → {{ $order->return_date }}
                    </small>
                </td>

                <td>
                    {{ $order->details->count() }} Unit
                </td>

                <td>
                    <span class="badge bg-warning text-dark">
                        Return Checking
                    </span>
                </td>

                <td>
                    <a href="/storage/quality-control/input/{{ $order->id }}"
                       class="btn btn-primary btn-sm">
                        QC
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">
                    Tidak ada order yang perlu QC
                </td>
            </tr>
            @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
</body>
</html>