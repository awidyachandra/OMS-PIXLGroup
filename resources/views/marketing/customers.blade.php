<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        .star-rating {
            font-size: 42px;
            color: #f4c542;
            cursor: pointer;
            user-select: none;
        }

        .star {
            margin-right: 6px;
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

        .modal {
            z-index: 99999 !important;
        }

        .modal-backdrop {
            z-index: 9999 !important;
        }
    </style>
</head>

<body>
@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h4 class="fw-bold mb-4">
        Customers
    </h4>

    {{-- SEARCH --}}
    <form method="GET" class="mb-4" style="max-width:400px;">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Search"
               value="{{ request('search') }}">
    </form>

    <div class="table-responsive">
        <table class="table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Telp</th>
                    <th>Alamat</th>
                    <th>Instagram</th>
                    <th>Organisasi</th>
                    <th>Instansi/Agensi</th>
                    <th>Rating</th>
                    <th>Notes</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>

            @forelse($customers as $customer)

                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>

                    <td style="max-width:220px;">
                        {{ $customer->address ?? '-' }}
                    </td>

                    <td>{{ $customer->instagram ?? '-' }}</td>
                    <td>{{ $customer->organization }}</td>
                    <td>{{ $customer->agency ?? '-' }}</td>

                    <td>
                        {{ $customer->rating ? $customer->rating . '/5' : '-' }}
                    </td>

                    <td style="max-width:200px;">
                        {{ $customer->review ?? '-' }}
                    </td>

                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">

                            <button
                                type="button"
                                class="btn btn-sm"
                                style="border:1px solid #3b2a6f; color:#3b2a6f;"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $customer->id }}">
                                Edit
                            </button>

                            <button
                                type="button"
                                class="btn btn-sm text-white"
                                style="background:#3b2a6f;"
                                data-bs-toggle="modal"
                                data-bs-target="#rateModal{{ $customer->id }}">
                                Rate
                            </button>

                        </div>
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="11" class="text-center">
                        Tidak ada data customer
                    </td>
                </tr>

            @endforelse

            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $customers->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection

{{-- ========================= --}}
{{-- MODAL EDIT CUSTOMER --}}
{{-- ========================= --}}

@foreach($customers as $customer)

<div class="modal fade"
     id="editModal{{ $customer->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">

            <h2 class="fw-bold mb-4">
                Edit Customer
            </h2>

            <form method="POST"
                  action="{{ url('/marketing/customers/update/' . $customer->id) }}">
                @csrf

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ $customer->name }}"
                           required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ $customer->email }}"
                           required>
                </div>

                <div class="mb-3">
                    <label>No. Telp</label>
                    <input type="text"
                           name="phone"
                           class="form-control"
                           value="{{ $customer->phone }}"
                           required>
                </div>

                <div class="mb-3">
                    <label>Alamat</label>
                    <textarea name="address"
                              class="form-control"
                              rows="3"
                              required>{{ $customer->address }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Instagram</label>
                    <input type="text"
                           name="instagram"
                           class="form-control"
                           value="{{ $customer->instagram }}">
                </div>

                <div class="mb-3">
                    <label>Organisasi</label>
                    <select name="organization" class="form-control" required>
                        <option value="">Pilih Organisasi</option>
                        <option value="Umum" {{ $customer->organization == 'Umum' ? 'selected' : '' }}>
                            Umum
                        </option>
                        <option value="Event Organizer" {{ $customer->organization == 'Event Organizer' ? 'selected' : '' }}>
                            Event Organizer
                        </option>
                        <option value="Wedding Organizer" {{ $customer->organization == 'Wedding Organizer' ? 'selected' : '' }}>
                            Wedding Organizer
                        </option>
                        <option value="BEM Fakultas" {{ $customer->organization == 'BEM Fakultas' ? 'selected' : '' }}>
                            BEM Fakultas
                        </option>
                        <option value="BEM Universitas" {{ $customer->organization == 'BEM Universitas' ? 'selected' : '' }}>
                            BEM Universitas
                        </option>
                        <option value="HIMA Jurusan" {{ $customer->organization == 'HIMA Jurusan' ? 'selected' : '' }}>
                            HIMA Jurusan
                        </option>
                        <option value="OSIS" {{ $customer->organization == 'OSIS' ? 'selected' : '' }}>
                            OSIS
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label>Instansi/Agensi</label>
                    <input type="text"
                           name="agency"
                           class="form-control"
                           value="{{ $customer->agency }}">
                </div>

                <button
                    type="submit"
                    class="btn w-100 text-white"
                    style="background:#3b2a6f;">
                    Update
                </button>

            </form>

        </div>
    </div>
</div>

@endforeach

{{-- ========================= --}}
{{-- MODAL RATE CUSTOMER --}}
{{-- ========================= --}}

@foreach($customers as $customer)

<div class="modal fade"
     id="rateModal{{ $customer->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">

            <h2 class="fw-bold mb-4">
                Rate Customers
            </h2>

            <form method="POST"
                  action="{{ url('/marketing/customers/rate/' . $customer->id) }}">
                @csrf

                {{-- STAR RATING --}}
                <div class="mb-4">
                    <label class="form-label d-block mb-3">
                        Rating
                    </label>

                    <input
                        type="hidden"
                        name="rating"
                        id="ratingValue{{ $customer->id }}"
                        value="{{ $customer->rating }}"
                        required>

                    <div class="star-rating">

                        <span class="star"
                              data-customer="{{ $customer->id }}"
                              data-value="1">
                            {!! $customer->rating >= 1 ? '&#9733;' : '&#9734;' !!}
                        </span>

                        <span class="star"
                              data-customer="{{ $customer->id }}"
                              data-value="2">
                            {!! $customer->rating >= 2 ? '&#9733;' : '&#9734;' !!}
                        </span>

                        <span class="star"
                              data-customer="{{ $customer->id }}"
                              data-value="3">
                            {!! $customer->rating >= 3 ? '&#9733;' : '&#9734;' !!}
                        </span>

                        <span class="star"
                              data-customer="{{ $customer->id }}"
                              data-value="4">
                            {!! $customer->rating >= 4 ? '&#9733;' : '&#9734;' !!}
                        </span>

                        <span class="star"
                              data-customer="{{ $customer->id }}"
                              data-value="5">
                            {!! $customer->rating >= 5 ? '&#9733;' : '&#9734;' !!}
                        </span>

                    </div>
                </div>

                {{-- REVIEW --}}
                <div class="mb-4">
                    <label class="form-label">
                        Review
                    </label>

                    <textarea
                        name="review"
                        class="form-control"
                        rows="4"
                        placeholder="Berikan Review">{{ $customer->review }}</textarea>
                </div>

                <button
                    type="submit"
                    class="btn w-100 text-white"
                    style="background:#3b2a6f;">
                    Submit
                </button>

            </form>

        </div>
    </div>
</div>

@endforeach

{{-- SUCCESS MODAL --}}
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const stars = document.querySelectorAll(".star");

    stars.forEach(star => {

        star.addEventListener("click", function () {

            let customerId = this.getAttribute("data-customer");
            let value = this.getAttribute("data-value");

            document.getElementById(
                "ratingValue" + customerId
            ).value = value;

            const customerStars = document.querySelectorAll(
                '.star[data-customer="' + customerId + '"]'
            );

            customerStars.forEach(s => {

                if (
                    parseInt(s.getAttribute("data-value")) <= parseInt(value)
                ) {
                    s.innerHTML = "&#9733;";
                } else {
                    s.innerHTML = "&#9734;";
                }

            });

        });

    });

});
</script>

@if(session('success'))
<script>
window.onload = function () {
    let modalElement = document.getElementById('successModal');

    if (modalElement) {
        let successModal = new bootstrap.Modal(modalElement);
        successModal.show();
    }
}
</script>
@endif

</body>
</html>