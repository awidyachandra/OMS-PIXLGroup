@extends('layouts.app')

@section('content')

<style>
    .btn-purple {
        background-color: #3b2a6f !important;
        color: #ffffff !important;
        border: 1px solid #3b2a6f !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        display: inline-block !important;
    }

    .btn-purple:hover {
        background-color: #2c1f54 !important;
        border-color: #2c1f54 !important;
        color: #ffffff !important;
    }

    .btn-outline-purple {
        background-color: #ffffff !important;
        color: #3b2a6f !important;
        border: 1px solid #3b2a6f !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        display: inline-block !important;
    }

    .btn-outline-purple:hover {
        background-color: #3b2a6f !important;
        color: #ffffff !important;
        border-color: #3b2a6f !important;
    }

    .btn-sm.btn-purple,
    .btn-sm.btn-outline-purple {
        padding: 3px 8px !important;
        font-size: 11px !important;
        border-radius: 6px !important;
        line-height: 1.4 !important;
    }

    .table-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        background: #fff;
        padding: 18px;
    }

    /*
    =========================
    FONT TABEL DIPERKECIL
    =========================
    */

    .finance-table {
        font-size: 13px !important;
        margin-bottom: 0;
    }

    .finance-table th {
        font-size: 13px !important;
        font-weight: 700 !important;
        white-space: nowrap;
        padding: 8px 7px !important;
        vertical-align: middle !important;
    }

    .finance-table td {
        font-size: 13px !important;
        padding: 7px 7px !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .finance-table .badge {
        font-size: 12px !important;
        padding: 5px 7px !important;
        border-radius: 6px !important;
    }

    .finance-table .invoice-buttons {
        min-width: 105px;
    }

    .finance-table .invoice-buttons .btn {
        width: 100%;
        text-align: center;
    }

    .pagination .page-link {
        color: #3b2a6f !important;
        border: 1px solid #3b2a6f !important;
        font-size: 12px !important;
        padding: 5px 10px !important;
    }

    .pagination .page-link:hover {
        background-color: #3b2a6f !important;
        color: #fff !important;
    }

    .pagination .page-item.active .page-link {
        background-color: #3b2a6f !important;
        border-color: #3b2a6f !important;
        color: #fff !important;
    }

    .modal {
        z-index: 99999 !important;
    }

    .modal-backdrop {
        z-index: 9999 !important;
    }

    .modal label {
        font-size: 13px;
        font-weight: 600;
    }

    .modal .form-control {
        font-size: 13px;
    }
</style>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Finance > Payments</h4>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Gagal!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTER --}}
    <div class="mb-4 d-flex gap-2 flex-wrap">

        <a href="{{ url('/finance/orders?payment_status=all') }}"
           class="btn {{ $paymentStatus == 'all' ? 'btn-purple' : 'btn-outline-purple' }}">
            Semua
        </a>

        <a href="{{ url('/finance/orders?payment_status=unpaid') }}"
           class="btn {{ $paymentStatus == 'unpaid' ? 'btn-purple' : 'btn-outline-purple' }}">
            Unpaid
        </a>

        <a href="{{ url('/finance/orders?payment_status=dp paid') }}"
           class="btn {{ $paymentStatus == 'dp paid' ? 'btn-purple' : 'btn-outline-purple' }}">
            DP Paid
        </a>

        <a href="{{ url('/finance/orders?payment_status=fully paid') }}"
           class="btn {{ $paymentStatus == 'fully paid' ? 'btn-purple' : 'btn-outline-purple' }}">
            Fully Paid
        </a>

    </div>

    <div class="table-card">

        <div class="table-responsive">
            <table class="table align-middle finance-table">

                <thead style="background:#f1f1f5;">
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Event</th>
                        <th>Total Invoice</th>
                        <th>DP</th>
                        <th>Pelunasan</th>
                        <th>Sisa</th>
                        <th>Status Bayar</th>
                        <th>Status Order</th>
                        <th>Invoice</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($orders as $order)

                    @php
                        $finalPrice = (float) $order->final_price;
                        $paidAmount = (float) $order->paid_amount;
                        $remaining = max($finalPrice - $paidAmount, 0);
                    @endphp

                    <tr>
                        <td>#{{ $order->id }}</td>

                        <td>{{ $order->customer->name ?? '-' }}</td>

                        <td>{{ $order->event ?? '-' }}</td>

                        <td>
                            Rp {{ number_format($order->final_price, 0, ',', '.') }}
                        </td>

                        <td>
                            Rp {{ number_format($order->dp_amount, 0, ',', '.') }}
                        </td>

                        <td>
                            Rp {{ number_format($order->settlement_amount, 0, ',', '.') }}
                        </td>

                        <td>
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </td>

                        <td>
                            @if($order->payment_status == 'fully paid')
                                <span class="badge bg-success">Fully Paid</span>
                            @elseif($order->payment_status == 'dp paid')
                                <span class="badge bg-warning text-dark">DP Paid</span>
                            @else
                                <span class="badge bg-secondary">Unpaid</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-dark">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex flex-column gap-1 invoice-buttons">
                                @if($order->invoice_file)
                                    <a href="{{ url('/marketing/orders/download/' . $order->id) }}"
                                       class="btn btn-sm btn-outline-purple">
                                        Invoice Utama
                                    </a>
                                @endif

                                @if($order->dp_invoice_file)
                                    <a href="{{ url('/finance/orders/download-dp-invoice/' . $order->id) }}"
                                       class="btn btn-sm btn-outline-purple">
                                        Invoice DP
                                    </a>
                                @endif

                                @if($order->fully_paid_invoice_file)
                                    <a href="{{ url('/finance/orders/download-fully-paid-invoice/' . $order->id) }}"
                                       class="btn btn-sm btn-outline-purple">
                                        Invoice Lunas
                                    </a>
                                @endif
                            </div>
                        </td>

                        <td class="text-center">

                            @if($order->payment_status == 'unpaid')
                                <button type="button"
                                        class="btn btn-sm btn-purple"
                                        data-bs-toggle="modal"
                                        data-bs-target="#dpModal{{ $order->id }}">
                                    Input DP
                                </button>
                            @elseif($order->payment_status == 'dp paid')
                                <button type="button"
                                        class="btn btn-sm btn-purple"
                                        data-bs-toggle="modal"
                                        data-bs-target="#fullModal{{ $order->id }}">
                                    Pelunasan
                                </button>
                            @else
                                <span class="text-success fw-semibold">
                                    Lunas
                                </span>
                            @endif

                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">
                            Tidak ada data order finance
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>

    </div>

</div>
@endsection
{{-- MODAL DP --}}
@foreach($orders as $order)
<div class="modal fade" id="dpModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4" style="border-radius:14px;">

            <h4 class="fw-bold mb-3">Input Pembayaran DP</h4>

            <form method="POST" action="{{ url('/finance/orders/dp-paid/' . $order->id) }}">
                @csrf

                <div class="mb-3">
                    <label>Order</label>
                    <input type="text"
                           class="form-control"
                           value="Order #{{ $order->id }} - {{ $order->customer->name ?? '-' }}"
                           readonly>
                </div>

                <div class="mb-3">
                    <label>Total Tagihan</label>
                    <input type="text"
                           class="form-control"
                           value="Rp {{ number_format($order->final_price, 0, ',', '.') }}"
                           readonly>
                </div>

                <div class="mb-3">
                    <label>Nominal DP Dibayarkan</label>
                    <input type="number"
                           name="dp_amount"
                           class="form-control"
                           min="1"
                           max="{{ $order->final_price }}"
                           required>
                </div>

                <div class="mb-4">
                    <label>Catatan Pembayaran</label>
                    <textarea name="payment_notes"
                              class="form-control"
                              rows="3"
                              placeholder="Opsional"></textarea>
                </div>

                <button type="submit"
                        class="btn btn-purple w-100">
                    Simpan DP & Generate Invoice
                </button>

            </form>

        </div>
    </div>
</div>
@endforeach

{{-- MODAL FULLY PAID --}}
@foreach($orders as $order)
@php
    $remaining = max((float) $order->final_price - (float) $order->paid_amount, 0);
@endphp

<div class="modal fade" id="fullModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4" style="border-radius:14px;">

            <h4 class="fw-bold mb-3">Input Pelunasan</h4>

            <form method="POST" action="{{ url('/finance/orders/fully-paid/' . $order->id) }}">
                @csrf

                <div class="mb-3">
                    <label>Order</label>
                    <input type="text"
                           class="form-control"
                           value="Order #{{ $order->id }} - {{ $order->customer->name ?? '-' }}"
                           readonly>
                </div>

                <div class="mb-3">
                    <label>Total Tagihan</label>
                    <input type="text"
                           class="form-control"
                           value="Rp {{ number_format($order->final_price, 0, ',', '.') }}"
                           readonly>
                </div>

                <div class="mb-3">
                    <label>Sudah Dibayar</label>
                    <input type="text"
                           class="form-control"
                           value="Rp {{ number_format($order->paid_amount, 0, ',', '.') }}"
                           readonly>
                </div>

                <div class="mb-3">
                    <label>Sisa Tagihan</label>
                    <input type="text"
                           class="form-control"
                           value="Rp {{ number_format($remaining, 0, ',', '.') }}"
                           readonly>
                </div>

                <div class="mb-3">
                    <label>Nominal Pelunasan</label>
                    <input type="number"
                           name="settlement_amount"
                           class="form-control"
                           value="{{ $remaining }}"
                           min="{{ $remaining }}"
                           max="{{ $remaining }}"
                           required>
                </div>

                <div class="mb-4">
                    <label>Catatan Pembayaran</label>
                    <textarea name="payment_notes"
                              class="form-control"
                              rows="3"
                              placeholder="Opsional">{{ $order->payment_notes }}</textarea>
                </div>

                <button type="submit"
                        class="btn btn-purple w-100">
                    Simpan Pelunasan & Generate Invoice
                </button>

            </form>

        </div>
    </div>
</div>
@endforeach

{{-- SUCCESS MODAL --}}
@if(session('success'))
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border-radius:12px;">

            <div class="mb-2">
                <i class="bi bi-check-circle-fill text-success"
                   style="font-size:50px;"></i>
            </div>

            <h5 class="fw-bold">Berhasil!</h5>

            <p>{{ session('success') }}</p>

            <button class="btn btn-purple"
                    data-bs-dismiss="modal">
                OK
            </button>

        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    let modalElement = document.getElementById('successModal');

    if (modalElement) {
        let successModal = new bootstrap.Modal(modalElement);
        successModal.show();
    }
});
</script>
@endif

