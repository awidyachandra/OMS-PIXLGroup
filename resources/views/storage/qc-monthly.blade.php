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

<h3 class="fw-bold mb-4">QC Bulanan</h3>

<form method="POST" action="/storage/quality-control/monthly/store">
@csrf

<div class="card p-4">

<table class="table align-middle">

    <thead>
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
    @foreach($units as $unit)
    <tr>
        <td>{{ $unit->kode_unit }}</td>
        <td>{{ $unit->nama_unit }}</td>

        <td>
            <input type="checkbox"
                class="qc-check qc-status"
                name="units[{{ $unit->kode_unit }}][status]"
                value="on"
                data-group="{{ $unit->kode_unit }}"
                checked>
        </td>

        <td>
            <input type="checkbox"
                class="qc-check qc-status"
                name="units[{{ $unit->kode_unit }}][status]"
                value="off"
                data-group="{{ $unit->kode_unit }}">
        </td>

        <td>
            <input type="checkbox"
                class="qc-check qc-status"
                name="units[{{ $unit->kode_unit }}][status]"
                value="lost"
                data-group="{{ $unit->kode_unit }}">
        </td>

        <td>
            <textarea
                name="notes[{{ $unit->kode_unit }}]"
                class="form-control"
                rows="1"
                placeholder="Notes (optional)"></textarea>
        </td>
    </tr>
    @endforeach
    </tbody>

</table>

<button type="submit" class="btn text-white"
        style="background:#3b2a6f;">
    Simpan QC
</button>

</div>

</form>

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