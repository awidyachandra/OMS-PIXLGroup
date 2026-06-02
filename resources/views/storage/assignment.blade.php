<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .card {
            border:none;
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>
<body>
    {{-- resources/views/storage/assignment.blade.php --}}

@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Breadcrumb --}}
    <div class="mb-4">
        <h4 class="fw-bold">
            <span style="color: #999;">
                Reservation List >
            </span>
            Assignment
        </h4>
    </div>

    {{-- ORDER INFO --}}
    <div class="card mb-4"
         style="border-radius: 0px; padding: 30px; border: 1px solid #ddd; font-size:14px;" >

        <h5 class="fw-bold mb-4">
            Assignment Order #{{ $order->id }}
        </h5>

        <div class="row">

            <div class="col-md-6">
                <div class="text">
                    Event : {{ $order->event }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="text">
                    Date : {{ \Carbon\Carbon::parse($order->date)->format('d M Y') }}
                </div>
            </div>

        </div>

        <div class="mt-3">
            <div class="text">
                Qty Need : {{ $detail->qty }} Units
            </div>
        </div>

    </div>

    {{-- AVAILABLE UNITS --}}
    <div class="card"
         style="border-radius: 0px; padding: 30px; border: 1px solid #ddd;">

        <h5 class="fw-bold mb-1">
            Available Units
        </h5>

        <small class="text-muted d-block mb-4">
            Unit backup tidak ditampilkan pada assignment biasa.
        </small>

        {{-- SEARCH --}}
        <div class="mb-4" style="max-width: 350px;">
            <input
                type="text"
                id="searchInput"
                class="form-control"
                placeholder="Search kode unit..."
            >
        </div>

        <form method="POST"
              action="{{ url('/storage/assignment/' . $order->id) }}">

            @csrf

            <table class="table">

                <thead style="background: #f1f2f6;">
                    <tr>
                        <th>Action</th>
                        <th>Kode Unit</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>

                <tbody id="unitTable">

                    @forelse($availableUnits as $unit)

                        <tr class="unit-row">

                            {{-- CHECKBOX --}}
                            <td>
                                <input
                                    type="checkbox"
                                    name="selected_units[]"
                                    value="{{ $unit->kode_unit }}"
                                    class="unit-checkbox"

                                    {{-- AUTO CHECK SESUAI QTY --}}
                                    {{ in_array($unit->kode_unit, $autoSelected) ? 'checked' : '' }}
                                >
                            </td>

                            {{-- KODE UNIT --}}
                            <td>
                                {{ $unit->kode_unit }}
                            </td>

                            {{-- PRODUCT --}}
                            <td>
                                {{ $unit->nama_unit }}
                            </td>

                            {{-- STATUS --}}
                            <td>
                                {{ ucfirst($unit->status) }}
                            </td>

                            {{-- UPDATED --}}
                            <td>
                                {{ $unit->updated_at ? $unit->updated_at->format('d-m-Y H:i') : '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Tidak ada unit tersedia
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

            {{-- FOOTER --}}
            <div class="d-flex justify-content-end align-items-center mt-4 gap-4">

                <div>
                    <strong>
                        Selected Unit:
                        <span id="selectedCount">
                            {{ count($autoSelected) }}
                        </span>
                        / {{ $detail->qty }}
                    </strong>
                </div>

                <button
                    type="submit"
                    class="btn text-white px-5"
                    style="
                        background: #3b2a6f;
                        border-radius: 8px;
                        min-width: 220px;
                    "
                >
                    Assign Units
                </button>

            </div>

        </form>

    </div>

</div>

{{-- SCRIPT --}}
<script>

document.addEventListener("DOMContentLoaded", function () {

    const checkboxes = document.querySelectorAll(".unit-checkbox");
    const selectedCount = document.getElementById("selectedCount");
    const maxQty = {{ $detail->qty }};
    const searchInput = document.getElementById("searchInput");
    const rows = document.querySelectorAll(".unit-row");

    /*
    =====================================
    LIMIT CHECKBOX SESUAI QTY
    =====================================
    */

    function updateCount() {
        let checked = document.querySelectorAll(".unit-checkbox:checked").length;
        selectedCount.innerText = checked;
    }

    checkboxes.forEach(cb => {
        cb.addEventListener("change", function () {

            let checked = document.querySelectorAll(".unit-checkbox:checked").length;

            if (checked > maxQty) {
                this.checked = false;
                alert("Jumlah unit tidak boleh melebihi qty order");
            }

            updateCount();
        });
    });

    updateCount();

    /*
    =====================================
    SEARCH UNIT
    =====================================
    */

    searchInput.addEventListener("keyup", function () {

        let keyword = this.value.toLowerCase();

        rows.forEach(row => {

            let kode = row.children[1].innerText.toLowerCase();

            if (kode.includes(keyword)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }

        });

    });

});

</script>

@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>