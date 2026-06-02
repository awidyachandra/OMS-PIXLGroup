<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Document</title>

    <style>
        .modal {
            z-index: 99999 !important;
        }

        .modal-backdrop {
            z-index: 9999 !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .pagination {
            margin-left: 20px;
        }

        .pagination .page-link {
            color: #3b2a6f;
            border: 1px solid #3b2a6f;
        }

        .pagination .page-link:hover {
            background-color: #3b2a6f;
            color: #fff;
        }

        .pagination .page-item.active .page-link {
            background-color: #3b2a6f;
            border-color: #3b2a6f;
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            color: #aaa;
            border-color: #ddd;
        }

        .edit-info-box {
            background: #f8f7fb;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            font-size: 13px;
        }
    </style>
</head>

<body>
    {{-- resources/views/marketing/orders.blade.php --}}

    @extends('layouts.app')

    @section('content')

        <div class="container-fluid">
            <h4 class="fw-bold mb-4">Order Management</h4>

            <div class="mb-4 d-flex gap-2">
                {{-- ALL --}}
                <a href="{{ url('/marketing/orders?status=all') }}" class="btn {{ $status == 'all' ? 'text-white' : '' }}"
                    style="
                        background: {{ $status == 'all' ? '#3b2a6f' : 'white' }};
                        border: 1px solid #3b2a6f;
                        color: {{ $status == 'all' ? 'white' : '#3b2a6f' }};
                    ">
                    All
                </a>

                {{-- NEW --}}
                <a href="{{ url('/marketing/orders?status=pending approval') }}"
                    class="btn {{ $status == 'pending approval' ? 'text-white' : '' }}"
                    style="
                        background: {{ $status == 'pending approval' ? '#3b2a6f' : 'white' }};
                        border: 1px solid #3b2a6f;
                        color: {{ $status == 'pending approval' ? 'white' : '#3b2a6f' }};
                    ">
                    New
                </a>

                {{-- PROCESSED --}}
                <a href="{{ url('/marketing/orders?status=processed') }}"
                    class="btn {{ $status == 'processed' ? 'text-white' : '' }}"
                    style="
                        background: {{ $status == 'processed' ? '#3b2a6f' : 'white' }};
                        border: 1px solid #3b2a6f;
                        color: {{ $status == 'processed' ? 'white' : '#3b2a6f' }};
                    ">
                    Processed
                </a>

                {{-- ON RENT --}}
                <a href="{{ url('/marketing/orders?status=on rent') }}"
                    class="btn {{ $status == 'on rent' ? 'text-white' : '' }}"
                    style="
                        background: {{ $status == 'on rent' ? '#3b2a6f' : 'white' }};
                        border: 1px solid #3b2a6f;
                        color: {{ $status == 'on rent' ? 'white' : '#3b2a6f' }};
                    ">
                    On Rent
                </a>

                {{-- DONE --}}
                <a href="{{ url('/marketing/orders?status=completed') }}"
                    class="btn {{ $status == 'completed' ? 'text-white' : '' }}"
                    style="
                        background: {{ $status == 'completed' ? '#3b2a6f' : 'white' }};
                        border: 1px solid #3b2a6f;
                        color: {{ $status == 'completed' ? 'white' : '#3b2a6f' }};
                    ">
                    Done
                </a>

                {{-- OVERDUE --}}
                <a href="{{ url('/marketing/orders?status=overdue') }}"
                    class="btn {{ $status == 'overdue' ? 'text-white' : '' }}"
                    style="
                        background: {{ $status == 'overdue' ? '#3b2a6f' : 'white' }};
                        border: 1px solid #3b2a6f;
                        color: {{ $status == 'overdue' ? 'white' : '#3b2a6f' }};
                    ">
                    Overdue
                </a>

                {{-- CANCELLED --}}
                <a href="{{ url('/marketing/orders?status=cancelled') }}"
                    class="btn {{ $status == 'cancelled' ? 'text-white' : '' }}"
                    style="
                        background: {{ $status == 'cancelled' ? '#3b2a6f' : 'white' }};
                        border: 1px solid #3b2a6f;
                        color: {{ $status == 'cancelled' ? 'white' : '#3b2a6f' }};
                    ">
                    Cancelled
                </a>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Event</th>
                            <th>Tanggal Sewa</th>

                            @if ($status == 'pending approval')
                                <th>No Telp</th>
                                <th>Package</th>
                                <th>Proposal</th>
                            @endif

                            @if (in_array($status, ['processed', 'on rent']))
                                <th>PIC</th>
                                <th>Invoice</th>
                                <th>Status</th>
                            @elseif($status == 'overdue')
                                <th>PIC</th>
                                <th>No Telp</th>
                                <th>Instansi</th>
                            @elseif($status == 'all')
                                <th>Package</th>
                                <th>PIC</th>
                                <th>Status</th>
                            @endif

                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->customer->name }}</td>
                                <td>{{ $order->event }}</td>

                                <td>
                                    @if ($order->start_date && $order->end_date)
                                        {{ \Carbon\Carbon::parse($order->start_date)->format('d-m-Y') }}

                                        @if (\Carbon\Carbon::parse($order->start_date)->format('Y-m-d') != \Carbon\Carbon::parse($order->end_date)->format('Y-m-d'))
                                            -
                                            {{ \Carbon\Carbon::parse($order->end_date)->format('d-m-Y') }}
                                        @endif
                                    @else
                                        {{ \Carbon\Carbon::parse($order->date)->format('d-m-Y') }}
                                    @endif
                                </td>

                                @if ($status == 'pending approval')
                                    <td>{{ $order->customer->phone ?? '-' }}</td>

                                    <td>{{ $order->package ?? '-' }}</td>

                                    <td>
                                        @if ($order->proposal)
                                            <a href="{{ asset('storage/' . $order->proposal) }}" target="_blank"
                                                class="text-decoration-underline">
                                                Lihat
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif

                                {{-- PROCESSED + ON RENT --}}
                                @if (in_array($status, ['processed', 'on rent']))
                                    <td>{{ $order->processed_by ?? '-' }}</td>

                                    <td>
                                        @if ($order->invoice_file)
                                            <a href="{{ route('marketing.download.invoice', $order->id) }}">
                                                Download
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        @if ($order->status == 'assigned')
                                            <span class="badge bg-warning text-dark">Assigned</span>
                                        @elseif($order->status == 'processed')
                                            <span class="badge bg-primary">Processed</span>
                                        @elseif($order->status == 'on rent')
                                            <span class="badge bg-success">On Rent</span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="d-flex gap-2">

                                        @if (in_array($order->status, ['processed', 'assigned']))
                                            <a href="{{ url('/marketing/orders/detail/' . $order->id) }}"
                                                class="btn btn-sm"
                                                style="border:1px solid #3b2a6f; color:#3b2a6f;">
                                                View Detail
                                            </a>

                                            <button type="button" class="btn btn-sm"
                                                style="border:1px solid #3b2a6f; color:#3b2a6f;"
                                                onclick="editOrder({{ $order->id }})">
                                                Edit
                                            </button>

                                            @if ($order->status == 'processed')
                                                <button type="button" class="btn btn-sm text-white"
                                                    style="background:#dc3545;"
                                                    onclick="openCancelModal({{ $order->id }})">
                                                    Cancel
                                                </button>

                                                <form id="cancelForm{{ $order->id }}" method="POST"
                                                    action="/marketing/orders/cancel/{{ $order->id }}">
                                                    @csrf
                                                </form>
                                            @endif

                                        @elseif($order->status == 'on rent')
                                            <a href="{{ url('/marketing/orders/detail/' . $order->id) }}"
                                                class="btn btn-sm"
                                                style="border:1px solid #3b2a6f; color:#3b2a6f;">
                                                View Detail
                                            </a>
                                        @endif

                                    </td>

                                @elseif($status == 'overdue')
                                    <td>{{ $order->processed_by ?? '-' }}</td>

                                    <td>
                                        {{ $order->customer->phone ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $order->customer->agency ?? '-' }}
                                    </td>

                                    <td>
                                        <a href="{{ url('/marketing/orders/detail/' . $order->id) }}"
                                            class="btn btn-sm"
                                            style="border:1px solid #3b2a6f; color:#3b2a6f;">
                                            View Detail
                                        </a>
                                    </td>

                                @elseif($status == 'all')
                                    <td>{{ $order->package }}</td>

                                    <td>{{ $order->processed_by ?? '-' }}</td>

                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending approval' => 'secondary',
                                                'processed' => 'primary',
                                                'assigned' => 'warning text-dark',
                                                'dp paid' => 'info',
                                                'fully paid' => 'success',
                                                'on rent' => 'success',
                                                'return checking' => 'info',
                                                'completed' => 'dark',
                                                'cancelled' => 'secondary',
                                            ];

                                            $isOverdue =
                                                $order->status == 'on rent' &&
                                                $order->return_date &&
                                                \Carbon\Carbon::parse($order->return_date)->lt(\Carbon\Carbon::today());
                                        @endphp

                                        @if ($isOverdue)
                                            <span class="badge bg-danger">
                                                Overdue
                                            </span>
                                        @else
                                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                                {{ ucwords($order->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="d-flex gap-2">
                                        <a href="{{ url('/marketing/orders/detail/' . $order->id) }}"
                                            class="btn btn-sm"
                                            style="border:1px solid #3b2a6f; color:#3b2a6f;">
                                            View Detail
                                        </a>

                                        @if (!in_array($order->status, ['on rent', 'return checking', 'completed', 'cancelled']))
                                            <button type="button" class="btn btn-sm"
                                                style="border:1px solid #3b2a6f; color:#3b2a6f;"
                                                onclick="editOrder({{ $order->id }})">
                                                Edit
                                            </button>
                                        @endif
                                    </td>

                                {{-- CANCELLED --}}
                                @elseif($status == 'cancelled')
                                    <td class="d-flex gap-2">
                                        <a href="{{ url('/marketing/orders/detail/' . $order->id) }}"
                                            class="btn btn-sm"
                                            style="border:1px solid #3b2a6f; color:#3b2a6f;">
                                            View Detail
                                        </a>
                                    </td>

                                {{-- DONE --}}
                                @elseif($status == 'completed')
                                    <td class="d-flex gap-2">
                                        <a href="{{ url('/marketing/orders/detail/' . $order->id) }}"
                                            class="btn btn-sm"
                                            style="border:1px solid #3b2a6f; color:#3b2a6f;">
                                            View Detail
                                        </a>
                                    </td>

                                {{-- NEW / DEFAULT --}}
                                @else
                                    <td class="d-flex gap-2">
                                        <button class="btn btn-sm text-white" style="background:#3b2a6f;"
                                            onclick="processOrder({{ $order->id }})">
                                            Process
                                        </button>

                                        @if (!in_array($order->status, ['on rent', 'return checking', 'completed', 'cancelled']))
                                            <button type="button" class="btn btn-sm"
                                                style="border:1px solid #3b2a6f; color:#3b2a6f;"
                                                onclick="editOrder({{ $order->id }})">
                                                Edit
                                            </button>
                                        @endif

                                        <a href="{{ url('/marketing/orders/detail/' . $order->id) }}"
                                            class="btn btn-sm"
                                            style="border:1px solid #3b2a6f; color:#3b2a6f;">
                                            View Detail
                                        </a>

                                        <button type="button" class="btn btn-sm text-white" style="background:#dc3545;"
                                            onclick="openCancelModal({{ $order->id }})">
                                            Cancel
                                        </button>

                                        <form id="cancelForm{{ $order->id }}" method="POST"
                                            action="/marketing/orders/cancel/{{ $order->id }}">
                                            @csrf
                                        </form>
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-3">
                    {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

    @endsection

    {{-- CANCEL MODAL --}}
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 text-center" style="border-radius:12px;">

                <div class="mb-2">
                    <i class="bi bi-exclamation-circle text-danger" style="font-size:45px;"></i>
                </div>

                <h5 class="fw-bold">Konfirmasi Cancel</h5>

                <p class="mb-3">
                    Yakin ingin membatalkan order ini?
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Tidak
                    </button>

                    <button class="btn text-white" style="background:#dc3545;" onclick="submitCancel()">
                        Ya, Batalkan
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- DETAIL MODAL TETAP DIBIARKAN AGAR FUNGSI LAMA TIDAK TERGANGGU --}}
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4">

                <h4>Detail Order</h4>

                <div id="detailContent">
                    Loading...
                </div>

            </div>
        </div>
    </div>

    {{-- PROCESS MODAL --}}
    @foreach ($orders as $order)
        <div class="modal fade" id="processModal{{ $order->id }}" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-4">

                    <h3 class="fw-bold mb-4">
                        Processing Order
                    </h3>

                    <form method="POST" action="/marketing/orders/process/{{ $order->id }}">
                        @csrf

                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" class="form-control" value="{{ $order->customer->name }}"
                                readonly>
                        </div>

                        <div class="mb-3">
                            <label>Event</label>
                            <input type="text" class="form-control" value="{{ $order->event }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label>Paket</label>
                            <input type="text" class="form-control" value="{{ $order->package }}" readonly>
                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Tanggal Sewa</label>
                                <input type="text" class="form-control"
                                    value="@if ($order->start_date && $order->end_date)
{{ \Carbon\Carbon::parse($order->start_date)->format('d-m-Y') }}@if (\Carbon\Carbon::parse($order->start_date)->format('Y-m-d') != \Carbon\Carbon::parse($order->end_date)->format('Y-m-d')) - {{ \Carbon\Carbon::parse($order->end_date)->format('d-m-Y') }}@endif
@else
{{ \Carbon\Carbon::parse($order->date)->format('d-m-Y') }}
@endif"
                                    readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Jumlah Unit</label>
                                <input type="text" class="form-control" value="{{ $order->details->sum('qty') }}"
                                    readonly>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-3 mb-3">
                                <label>Subtotal Harga</label>
                                <input type="text" id="totalPrice{{ $order->id }}" class="form-control"
                                    value="{{ $order->total_price }}" readonly>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Tipe Discount</label>
                                <select name="discount_type" id="discountType{{ $order->id }}"
                                    class="form-control"
                                    onchange="calculateFinalPrice({{ $order->id }})">
                                    <option value="percent">Persen (%)</option>
                                    <option value="amount">Harga / Rupiah</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label id="discountLabel{{ $order->id }}">Discount (%)</label>
                                <input type="number" name="discount" id="discount{{ $order->id }}"
                                    class="form-control" value="0" min="0"
                                    oninput="calculateFinalPrice({{ $order->id }})">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Total Harga</label>
                                <input type="text" id="finalPriceDisplay{{ $order->id }}" class="form-control"
                                    value="Rp {{ number_format($order->total_price, 0, ',', '.') }}" readonly>
                            </div>

                            <input type="hidden" name="final_price" id="finalPrice{{ $order->id }}"
                                value="{{ $order->total_price }}">

                        </div>

                        <button type="submit" formaction="/marketing/orders/invoice/{{ $order->id }}"
                            formmethod="POST" class="btn w-100 text-white mt-3" style="background:#3b2a6f;">
                            Generate Invoice
                        </button>

                    </form>

                </div>
            </div>
        </div>
    @endforeach

    {{-- EDIT ORDER MODAL --}}
    @foreach ($orders as $order)
        @php
            $oldStartDate = $order->start_date
                ? \Carbon\Carbon::parse($order->start_date)->format('Y-m-d')
                : \Carbon\Carbon::parse($order->date)->format('Y-m-d');

            $oldEndDate = $order->end_date
                ? \Carbon\Carbon::parse($order->end_date)->format('Y-m-d')
                : \Carbon\Carbon::parse($order->date)->format('Y-m-d');
        @endphp

        <div class="modal fade" id="editOrderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-4">

                    <h3 class="fw-bold mb-4">
                        Edit Order #{{ $order->id }}
                    </h3>

                    <form method="POST"
                        action="/marketing/orders/update/{{ $order->id }}"
                        id="editOrderForm{{ $order->id }}"
                        onsubmit="return confirmSubmitEditOrder({{ $order->id }})">
                        @csrf

                        <div class="edit-info-box mb-3">
                            <strong>Catatan:</strong><br>
                            Jika tanggal diubah, sistem akan mengecek ketersediaan unit terlebih dahulu.
                            Jika order sudah assigned, maka assignment unit lama akan dikosongkan dan harus dilakukan assignment ulang.
                        </div>

                        <div class="mb-3">
                            <label>Customer</label>
                            <input type="text" class="form-control"
                                value="{{ $order->customer->name ?? '-' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label>Event</label>
                            <input type="text" name="event" class="form-control"
                                value="{{ $order->event }}" required>
                        </div>

                        <div class="mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="2" required>{{ $order->address }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label>Package</label>
                            <input type="text" name="package" class="form-control"
                                value="{{ $order->package }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Mulai Sewa</label>
                                <input type="date"
                                    name="start_date"
                                    id="editStartDate{{ $order->id }}"
                                    class="form-control"
                                    value="{{ $oldStartDate }}"
                                    data-old="{{ $oldStartDate }}"
                                    onchange="checkEditAvailability({{ $order->id }})"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Tanggal Selesai Sewa</label>
                                <input type="date"
                                    name="end_date"
                                    id="editEndDate{{ $order->id }}"
                                    class="form-control"
                                    value="{{ $oldEndDate }}"
                                    data-old="{{ $oldEndDate }}"
                                    onchange="checkEditAvailability({{ $order->id }})"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Jumlah Unit</label>
                            <input type="text"
                                class="form-control"
                                value="{{ $order->details->sum('qty') }} Unit"
                                readonly>
                        </div>

                        <div class="alert d-none"
                            id="editAvailabilityAlert{{ $order->id }}">
                        </div>

                        <input type="hidden"
                            id="editAvailabilityStatus{{ $order->id }}"
                            value="1">

                        <input type="hidden"
                            id="editOrderStatus{{ $order->id }}"
                            value="{{ $order->status }}">

                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit"
                                class="btn text-white"
                                style="background:#3b2a6f;">
                                Simpan Perubahan
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let selectedCancelId = null;

        function openCancelModal(orderId) {
            selectedCancelId = orderId;

            let modal = new bootstrap.Modal(
                document.getElementById('cancelModal')
            );

            modal.show();
        }

        function submitCancel() {
            if (selectedCancelId) {
                document.getElementById('cancelForm' + selectedCancelId).submit();
            }
        }

        function calculateFinalPrice(orderId) {
            let total = parseFloat(
                document.getElementById('totalPrice' + orderId).value
            ) || 0;

            let discountType = document.getElementById('discountType' + orderId).value;

            let discountInput = document.getElementById('discount' + orderId);

            let discountValue = parseFloat(discountInput.value) || 0;

            let discountAmount = 0;

            if (discountValue < 0) {
                discountValue = 0;
                discountInput.value = 0;
            }

            if (discountType === 'percent') {
                document.getElementById('discountLabel' + orderId).innerText = 'Discount (%)';

                if (discountValue > 100) {
                    discountValue = 100;
                    discountInput.value = 100;
                }

                discountAmount = (discountValue / 100) * total;
            } else {
                document.getElementById('discountLabel' + orderId).innerText = 'Discount Harga';

                if (discountValue > total) {
                    discountValue = total;
                    discountInput.value = total;
                }

                discountAmount = discountValue;
            }

            let finalPrice = total - discountAmount;

            if (finalPrice < 0) {
                finalPrice = 0;
            }

            document.getElementById('finalPrice' + orderId).value = finalPrice;

            document.getElementById('finalPriceDisplay' + orderId).value =
                'Rp ' + finalPrice.toLocaleString('id-ID');
        }

        function showDetail(orderId) {
            fetch(`/marketing/orders/detail/${orderId}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('detailContent').innerHTML = html;

                    new bootstrap.Modal(
                        document.getElementById('detailModal')
                    ).show();
                });
        }

        function processOrder(orderId) {
            let modal = new bootstrap.Modal(
                document.getElementById('processModal' + orderId)
            );

            modal.show();

            calculateFinalPrice(orderId);
        }

        function editOrder(orderId) {
            let modal = new bootstrap.Modal(
                document.getElementById('editOrderModal' + orderId)
            );

            modal.show();

            checkEditAvailability(orderId);
        }

        function checkEditAvailability(orderId) {
            let startDate = document.getElementById('editStartDate' + orderId).value;
            let endDate = document.getElementById('editEndDate' + orderId).value;
            let alertBox = document.getElementById('editAvailabilityAlert' + orderId);
            let statusInput = document.getElementById('editAvailabilityStatus' + orderId);

            if (!startDate || !endDate) {
                return;
            }

            if (endDate < startDate) {
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.';
                statusInput.value = '0';
                return;
            }

            alertBox.className = 'alert alert-info';
            alertBox.innerHTML = 'Mengecek ketersediaan unit...';
            statusInput.value = '0';

            fetch('/marketing/orders/check-edit-availability/' + orderId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    start_date: startDate,
                    end_date: endDate
                })
            })
            .then(response => response.json())
            .then(data => {
                let detailHtml = '';

                if (data.items && data.items.length > 0) {
                    detailHtml += '<ul class="mb-0 mt-2">';

                    data.items.forEach(function(item) {
                        detailHtml += '<li>' +
                            item.product_type +
                            ' | Dibutuhkan: ' + item.needed +
                            ' | Tersedia: ' + item.available +
                            '</li>';
                    });

                    detailHtml += '</ul>';
                }

                if (data.available) {
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML =
                        '<strong>Unit tersedia pada tanggal tersebut.</strong>' + detailHtml;

                    statusInput.value = '1';
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML =
                        '<strong>Unit tidak cukup pada tanggal tersebut.</strong>' + detailHtml;

                    statusInput.value = '0';
                }
            })
            .catch(error => {
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = 'Gagal mengecek availability.';
                statusInput.value = '0';
            });
        }

        function confirmSubmitEditOrder(orderId) {
            let statusInput = document.getElementById('editAvailabilityStatus' + orderId);
            let orderStatus = document.getElementById('editOrderStatus' + orderId).value;

            let startDate = document.getElementById('editStartDate' + orderId).value;
            let endDate = document.getElementById('editEndDate' + orderId).value;

            let oldStartDate = document.getElementById('editStartDate' + orderId).getAttribute('data-old');
            let oldEndDate = document.getElementById('editEndDate' + orderId).getAttribute('data-old');

            let dateChanged = startDate !== oldStartDate || endDate !== oldEndDate;

            if (endDate < startDate) {
                alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.');
                return false;
            }

            if (dateChanged && statusInput.value !== '1') {
                alert('Tanggal tidak dapat disimpan karena unit tidak tersedia.');
                return false;
            }

            if (dateChanged) {
                let message = 'Tanggal order akan diubah.\n\nUnit tersedia pada tanggal tersebut.';

                if (orderStatus === 'assigned') {
                    message += '\n\nOrder ini sudah assigned. Jika tanggal diubah, assignment unit lama akan dikosongkan dan harus melakukan assignment ulang.';
                }

                message += '\n\nLanjutkan simpan perubahan?';

                return confirm(message);
            }

            return confirm('Simpan perubahan order?');
        }
    </script>
</body>

</html>