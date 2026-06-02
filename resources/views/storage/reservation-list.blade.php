<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        .pagination .page-link {
            color: #3b2a6f;
            border: 1px solid #3b2a6f;
        }

        .pagination {
            margin-left: 20px;
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

        .proof-img {
            width: 70px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h4 class="fw-bold mb-4">
        Reservation List
    </h4>

    {{-- TAB --}}
    <div class="mb-4 d-flex gap-2">

        <a href="{{ url('/storage/reservation-list?status=processed') }}"
           class="btn {{ $status == 'processed' ? 'text-white' : '' }}"
           style="
                background: {{ $status == 'processed' ? '#3b2a6f' : 'white' }};
                border: 1px solid #3b2a6f;
                color: {{ $status == 'processed' ? 'white' : '#3b2a6f' }};
           ">
            New
        </a>

        <a href="{{ url('/storage/reservation-list?status=assigned') }}"
           class="btn {{ $status == 'assigned' ? 'text-white' : '' }}"
           style="
                background: {{ $status == 'assigned' ? '#3b2a6f' : 'white' }};
                border: 1px solid #3b2a6f;
                color: {{ $status == 'assigned' ? 'white' : '#3b2a6f' }};
           ">
            Assigned
        </a>

        <a href="{{ url('/storage/reservation-list?status=on rent') }}"
           class="btn {{ $status == 'on rent' ? 'text-white' : '' }}"
           style="
                background: {{ $status == 'on rent' ? '#3b2a6f' : 'white' }};
                border: 1px solid #3b2a6f;
                color: {{ $status == 'on rent' ? 'white' : '#3b2a6f' }};
           ">
            On Rent
        </a>

        <a href="{{ url('/storage/reservation-list?status=completed') }}"
           class="btn {{ $status == 'completed' ? 'text-white' : '' }}"
           style="
                background: {{ $status == 'completed' ? '#3b2a6f' : 'white' }};
                border: 1px solid #3b2a6f;
                color: {{ $status == 'completed' ? 'white' : '#3b2a6f' }};
           ">
            Completed
        </a>

        <a href="{{ url('/storage/reservation-list?status=overdue') }}"
           class="btn {{ $status == 'overdue' ? 'text-white' : '' }}"
           style="
                background: {{ $status == 'overdue' ? '#3b2a6f' : 'white' }};
                border: 1px solid #3b2a6f;
                color: {{ $status == 'overdue' ? 'white' : '#3b2a6f' }};
           ">
            Overdue
        </a>

    </div>

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

    <table class="table">

        <thead>
            <tr>
                <th>Customer</th>
                <th>Event</th>
                <th>Tanggal</th>
                <th>Unit</th>
                <th>Status</th>

                @if(in_array($status, ['on rent', 'completed']))
                    <th>Bukti Pickup</th>
                @endif

                @if($status == 'completed')
                    <th>Bukti Return</th>
                @endif

                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        @forelse($orders as $order)

            <tr>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->event }}</td>
                <td>{{ $order->date }}</td>

                <td>
                    @if(in_array($order->status, ['assigned', 'on rent', 'return checking', 'completed']))
                        @foreach($order->details as $detail)
                            <span class="badge bg-secondary">
                                {{ $detail->kode_unit }}
                            </span>
                        @endforeach
                    @else
                        -
                    @endif
                </td>

                <td>{{ ucfirst($order->status) }}</td>

                {{-- TAMPILKAN FOTO PICKUP DI STATUS ON RENT DAN COMPLETED --}}
                @if(in_array($status, ['on rent', 'completed']))
                    <td>
                        @if($order->pickup_photo)
                            <a href="{{ asset('storage/' . $order->pickup_photo) }}" target="_blank">
                                <img src="{{ asset('storage/' . $order->pickup_photo) }}"
                                     class="proof-img"
                                     alt="Bukti Pickup">
                            </a>
                            <br>
                            <small class="text-muted">
                                {{ $order->picked_up_at ? \Carbon\Carbon::parse($order->picked_up_at)->format('d-m-Y H:i') : '' }}
                            </small>
                        @else
                            <span class="text-muted">Belum ada</span>
                        @endif
                    </td>
                @endif

                {{-- TAMPILKAN FOTO RETURN DI STATUS COMPLETED --}}
                @if($status == 'completed')
                    <td>
                        @if($order->return_photo)
                            <a href="{{ asset('storage/' . $order->return_photo) }}" target="_blank">
                                <img src="{{ asset('storage/' . $order->return_photo) }}"
                                     class="proof-img"
                                     alt="Bukti Return">
                            </a>
                            <br>
                            <small class="text-muted">
                                {{ $order->returned_at ? \Carbon\Carbon::parse($order->returned_at)->format('d-m-Y H:i') : '' }}
                            </small>
                        @else
                            <span class="text-muted">Belum ada</span>
                        @endif
                    </td>
                @endif

                <td>

                    @if($status == 'processed')

                        <a href="{{ url('/storage/assignment/' . $order->id) }}"
                           class="btn btn-sm text-white"
                           style="background:#3b2a6f;">
                            Assign
                        </a>

                    @elseif($order->status == 'assigned')

                        <button type="button"
                                class="btn btn-sm"
                                style="color: #fff; background-color:#3b2a6f"
                                onclick="openPickupModal({{ $order->id }})">
                            Pickup
                        </button>

                    @elseif($order->status == 'on rent')

                        <button type="button"
                                class="btn btn-sm"
                                style="color: #fff; background-color:#3b2a6f"
                                onclick="openReturnModal({{ $order->id }})">
                            Return
                        </button>

                    @else

                        <a href="{{ url('/storage/reservation-detail/' . $order->id) }}"
                           class="btn btn-sm"
                           style="
                                border:1px solid #3b2a6f;
                                color:#3b2a6f;
                           ">
                            View Detail
                        </a>

                    @endif

                </td>
            </tr>

        @empty

            <tr>
                <td colspan="8" class="text-center">
                    Tidak ada data reservation
                </td>
            </tr>

        @endforelse

        </tbody>
    </table>

    <div class="d-flex justify-content-end mt-3">
        {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>

</div>



@endsection
{{-- MODAL PICKUP --}}
<div class="modal fade" id="pickupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4" style="border-radius:10px;">

            <div class="text-center mb-2">
                <i class="bi bi-question-circle text-warning" style="font-size:40px;"></i>
            </div>

            <h5 class="fw-bold text-center">Konfirmasi Pickup</h5>

            <p class="mb-3 text-center">
                Upload bukti foto bahwa customer sudah mengambil unit.
            </p>

            <form id="pickupForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3 text-start">
                    <label class="form-label fw-semibold">
                        Bukti Foto Pickup
                    </label>
                    <input type="file"
                           name="pickup_photo"
                           id="pickupPhotoInput"
                           class="form-control"
                           accept="image/*"
                           required>
                    <small class="text-muted">
                        Format: jpg, jpeg, png, webp. Maksimal 2MB.
                    </small>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Tidak
                    </button>

                    <button type="submit"
                            class="btn text-white"
                            style="background:#3b2a6f;">
                        Ya, Pickup
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- MODAL RETURN --}}
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4" style="border-radius:10px;">

            <div class="text-center mb-2">
                <i class="bi bi-arrow-return-left text-warning" style="font-size:40px;"></i>
            </div>

            <h5 class="fw-bold text-center">Konfirmasi Pengembalian</h5>

            <p class="mb-3 text-center">
                Upload bukti foto bahwa semua unit sudah dikembalikan.
            </p>

            <form id="returnForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3 text-start">
                    <label class="form-label fw-semibold">
                        Bukti Foto Return
                    </label>
                    <input type="file"
                           name="return_photo"
                           id="returnPhotoInput"
                           class="form-control"
                           accept="image/*"
                           required>
                    <small class="text-muted">
                        Format: jpg, jpeg, png, webp. Maksimal 2MB.
                    </small>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Tidak
                    </button>

                    <button type="submit"
                            class="btn text-white"
                            style="background:#3b2a6f;">
                        Ya, Return
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

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

            <button class="btn text-white"
                    style="background:#3b2a6f;"
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

<script>
function openPickupModal(orderId) {
    const form = document.getElementById('pickupForm');
    const input = document.getElementById('pickupPhotoInput');

    form.action = '/storage/pickup/' + orderId;
    input.value = '';

    let modal = new bootstrap.Modal(
        document.getElementById('pickupModal')
    );

    modal.show();
}

function openReturnModal(orderId) {
    const form = document.getElementById('returnForm');
    const input = document.getElementById('returnPhotoInput');

    form.action = '/storage/return/' + orderId;
    input.value = '';

    let modal = new bootstrap.Modal(
        document.getElementById('returnModal')
    );

    modal.show();
}
</script>

</body>
</html>