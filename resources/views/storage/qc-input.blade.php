<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        .qc-check {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    @extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h4 class="fw-bold mb-4">
        Quality Control > Input
    </h4>

    {{-- SEARCH --}}
    <div class="mb-3" style="max-width:300px;">
        <input type="text" class="form-control" placeholder="Search">
    </div>

    <form method="POST" action="/storage/quality-control/store/{{ $order->id }}">
        @csrf

        <div class="card p-4 shadow-sm" style="border-radius:10px;">

            <table class="table align-middle">

                <thead style="background:#f1f1f5;">
                    <tr>
                        <th>Kode Unit</th>
                        <th>Product</th>
                        <th>ON</th>
                        <th>OFF</th>
                        <th>Hilang</th>
                        <th>Notes</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($order->details as $detail)
                    <tr>
                        <td>{{ $detail->kode_unit }}</td>
                        <td>{{ $detail->product_type }}</td>

                        <td>
                            <input type="checkbox"
                                class="qc-check qc-status"
                                name="units[{{ $detail->kode_unit }}][status]"
                                value="on"
                                data-group="{{ $detail->kode_unit }}"
                                checked>
                        </td>

                        <td>
                            <input type="checkbox"
                                class="qc-check qc-status"
                                name="units[{{ $detail->kode_unit }}][status]"
                                value="off"
                                data-group="{{ $detail->kode_unit }}">
                        </td>

                        <td>
                            <input type="checkbox"
                                class="qc-check qc-status"
                                name="units[{{ $detail->kode_unit }}][status]"
                                value="lost"
                                data-group="{{ $detail->kode_unit }}">
                        </td>

                        <td>
                            <textarea
                                name="notes[{{ $detail->kode_unit }}]"
                                class="form-control"
                                rows="2"
                                placeholder="Catatan (optional)"></textarea>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

            {{-- PAGINATION UI --}}
            <div class="d-flex justify-content-between mt-4">

                <div>
                    <button type="button" class="btn"
                            style="background:#3b2a6f; color:white;">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <button type="button" class="btn btn-light">1</button>
                    <button type="button" class="btn btn-light">2</button>

                    <button type="button" class="btn"
                            style="background:#3b2a6f; color:white;">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <button type="submit" class="btn text-white px-4"
                        style="background:#3b2a6f; border-radius:10px;">
                    Simpan Data QC
                </button>

            </div>

        </div>

    </form>

</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.qc-status');

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const group = this.getAttribute('data-group');

                const groupCheckboxes = document.querySelectorAll(
                    '.qc-status[data-group="' + group + '"]'
                );

                if (this.checked) {
                    groupCheckboxes.forEach(function (item) {
                        if (item !== checkbox) {
                            item.checked = false;
                        }
                    });
                } else {
                    const checkedInGroup = document.querySelectorAll(
                        '.qc-status[data-group="' + group + '"]:checked'
                    );

                    if (checkedInGroup.length === 0) {
                        this.checked = true;
                    }
                }
            });
        });
    });
</script>

</body>
</html>