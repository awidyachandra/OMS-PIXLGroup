<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard</title>

    <style>
        .summary-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 22px;
            background: #fff;
            height: 100%;
        }

        .summary-title {
            font-size: 14px;
            color: #777;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: 700;
            color: #3b2a6f;
            margin-bottom: 0;
        }

        .summary-sub {
            font-size: 13px;
            color: #999;
        }

        .btn-purple {
            background: #3b2a6f;
            color: white;
            border-radius: 9px;
        }

        .btn-purple:hover {
            background: #2c1f54;
            color: white;
        }

        .badge-purple {
            background: #3b2a6f;
        }

        .table-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            background: #fff;
            padding: 22px;
        }
    </style>
</head>
<body>
@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Finance Dashboard</h4>

        
    </div>

    <div class="row mb-4">

        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <div class="summary-title">Total Invoice</div>
                <p class="summary-value">
                    Rp {{ number_format($totalInvoice, 0, ',', '.') }}
                </p>
                <div class="summary-sub">Total tagihan invoice</div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <div class="summary-title">Total Pendapatan Masuk</div>
                <p class="summary-value">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </p>
                <div class="summary-sub">DP + pelunasan</div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <div class="summary-title">Total DP</div>
                <p class="summary-value">
                    Rp {{ number_format($totalDp, 0, ',', '.') }}
                </p>
                <div class="summary-sub">Pembayaran DP diterima</div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <div class="summary-title">Outstanding</div>
                <p class="summary-value">
                    Rp {{ number_format($outstanding, 0, ',', '.') }}
                </p>
                <div class="summary-sub">Sisa belum dibayar</div>
            </div>
        </div>

    </div>

    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="summary-card">
                <div class="summary-title">Unpaid</div>
                <p class="summary-value">{{ $unpaidCount }}</p>
                <div class="summary-sub">Order belum ada pembayaran</div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="summary-card">
                <div class="summary-title">DP Paid</div>
                <p class="summary-value">{{ $dpPaidCount }}</p>
                <div class="summary-sub">Order sudah membayar DP</div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="summary-card">
                <div class="summary-title">Fully Paid</div>
                <p class="summary-value">{{ $fullyPaidCount }}</p>
                <div class="summary-sub">Order sudah lunas</div>
            </div>
        </div>

    </div>

    <div class="table-card">

        <h5 class="fw-bold mb-3">Order Terbaru</h5>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead style="background:#f1f1f5;">
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Event</th>
                        <th>Total</th>
                        <th>Dibayar</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->customer->name ?? '-' }}</td>
                        <td>{{ $order->event ?? '-' }}</td>
                        <td>Rp {{ number_format($order->final_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</td>
                        <td>
                            @if($order->payment_status == 'fully paid')
                                <span class="badge bg-success">Fully Paid</span>
                            @elseif($order->payment_status == 'dp paid')
                                <span class="badge bg-warning text-dark">DP Paid</span>
                            @else
                                <span class="badge bg-secondary">Unpaid</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Belum ada data pembayaran
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>