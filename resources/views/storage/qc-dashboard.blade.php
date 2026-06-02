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

    <h2 class="fw-bold mb-4">Quality Control</h2>

    {{-- BUTTON INPUT --}}
@if(Auth::check() && Auth::user()->role == 'storage')
    <a href="/storage/quality-control/pending"
       class="btn text-white mb-4 px-4 py-2"
       style="background:#3b2a6f; border-radius:10px;">
        Input Quality Control
    </a>
@endif

    <form method="GET" class="row mb-4">

        <div class="col-md-4">
            <label>Periode</label>
            <input type="month"
                name="period"
                class="form-control"
                value="{{ $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) }}"
                onchange="this.form.submit()">
        </div>

        <div class="col-md-3">
            <label>Jenis QC</label>
            <select name="type"
                    class="form-control"
                    onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>
                    Per Order
                </option>
                <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>
                    Bulanan
                </option>
            </select>
        </div>

    </form>

    @forelse($qc as $key => $items)

    @php
        $totalUnit = $items->count();
        $goodUnit = $items->where('result', 'Good')->count();
        $badUnit = $items->where('result', 'Need Maintenance')->count();
        $lostUnit = $items->where('result', 'Lost')->count();
    @endphp

    <div class="card p-4 shadow-sm mb-4" style="border-radius:12px;">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-start mb-3">

            <div>
                <h5 class="fw-bold mb-1">

                    @if($key == 'monthly')
                        QC Bulanan
                    @else
                        Order #{{ $key }}
                    @endif

                </h5>

                <small class="text-muted">

                    @if($key != 'monthly')
                        Customer: {{ $items->first()->order->customer->name ?? '-' }}
                        <br>
                        Event: {{ $items->first()->order->event ?? '-' }}
                        <br>
                    @endif

                    QC Date:
                    {{ \Carbon\Carbon::parse($items->first()->qc_date)->format('d M Y') }}

                </small>
            </div>

            <span class="badge bg-dark">
                {{ $totalUnit }} Unit
            </span>

        </div>

        {{-- SUMMARY QC --}}
        <div class="row mb-4">

            <div class="col-md-3 mb-2">
                <div class="card border-0 shadow-sm p-3" style="border-radius:10px;">
                    <small class="text-muted">Total Unit QC</small>
                    <h4 class="fw-bold mb-0">{{ $totalUnit }}</h4>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <div class="card border-0 shadow-sm p-3" style="border-radius:10px;">
                    <small class="text-muted">Good Condition</small>
                    <h4 class="fw-bold text-success mb-0">{{ $goodUnit }}</h4>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <div class="card border-0 shadow-sm p-3" style="border-radius:10px;">
                    <small class="text-muted">Rusak</small>
                    <h4 class="fw-bold text-warning mb-0">{{ $badUnit }}</h4>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <div class="card border-0 shadow-sm p-3" style="border-radius:10px;">
                    <small class="text-muted">Hilang</small>
                    <h4 class="fw-bold text-danger mb-0">{{ $lostUnit }}</h4>
                </div>
            </div>

        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table align-middle">

                <thead style="background:#f1f1f5;">
                    <tr>
                        <th>Kode Unit</th>
                        <th>Product</th>
                        <th>Status Unit</th>
                        <th>ON</th>
                        <th>OFF</th>
                        <th>Hilang</th>
                        <th>Result</th>
                        <th>Notes</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($items as $q)
                    <tr>
                        <td>{{ $q->kode_unit }}</td>

                        <td>
                            {{ $q->unit->nama_unit ?? $q->product_name ?? '-' }}
                        </td>

                        <td>
                            @if($q->result == 'Lost')
                                <span class="badge bg-danger">Unit Hilang / Terhapus</span>
                            @elseif($q->unit && $q->unit->status == 'available')
                                <span class="badge bg-success">Available</span>
                            @elseif($q->unit && $q->unit->status == 'maintenance')
                                <span class="badge bg-warning text-dark">Maintenance</span>
                            @elseif($q->unit)
                                <span class="badge bg-secondary">
                                    {{ ucfirst($q->unit->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Data Unit Tidak Ada</span>
                            @endif
                        </td>

                        <td>
                            <input type="checkbox" disabled {{ $q->on ? 'checked' : '' }}>
                        </td>

                        <td>
                            <input type="checkbox" disabled {{ $q->off ? 'checked' : '' }}>
                        </td>

                        <td>
                            <input type="checkbox" disabled {{ $q->lost ? 'checked' : '' }}>
                        </td>

                        <td>
                            @if($q->result == 'Good')
                                <span class="text-success fw-semibold">Good</span>
                            @elseif($q->result == 'Need Maintenance')
                                <span class="text-warning fw-semibold">Need Maintenance</span>
                            @elseif($q->result == 'Lost')
                                <span class="text-danger fw-semibold">Lost</span>
                            @else
                                <span class="text-secondary fw-semibold">{{ $q->result }}</span>
                            @endif
                        </td>

                        <td style="max-width:200px;">
                            <small>{{ $q->notes ?? '-' }}</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>

    @empty
    <div class="text-center">
        Tidak ada data QC
    </div>
    @endforelse

</div>

@endsection
{{-- MODAL AUTO REPLACEMENT --}}
@if(session('replacementLogs'))
<div class="modal fade" id="replacementModal" tabindex="-1" aria-labelledby="replacementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">

            <div class="modal-header" style="background:#3b2a6f; color:white;">
                <h5 class="modal-title fw-bold" id="replacementModalLabel">
                    Informasi Penggantian Unit Backup
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="alert alert-warning mb-3">
                    Terdapat unit yang rusak / hilang saat QC, sehingga sistem mencoba mengganti unit pada order berikutnya dengan unit backup.
                </div>

                <ul class="mb-0">
                    @foreach(session('replacementLogs') as $log)
                        <li class="mb-2">
                            {{ $log }}
                        </li>
                    @endforeach
                </ul>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn text-white px-4"
                        style="background:#3b2a6f;"
                        data-bs-dismiss="modal">
                    Mengerti
                </button>
            </div>

        </div>
    </div>
</div>
@endif
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@if(session('replacementLogs'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const replacementModalElement = document.getElementById('replacementModal');

        if (replacementModalElement) {
            const replacementModal = new bootstrap.Modal(replacementModalElement);
            replacementModal.show();
        }
    });
</script>
@endif
</body>
</html>