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

    <h2 class="fw-bold mb-4">
        Report Placement
    </h2>

    {{-- FILTER --}}
    <form method="GET" class="row g-3 mb-4">

        {{-- BULAN --}}
        <div class="col-md-3">
            <input
                type="month"
                name="period"
                class="form-control"
                value="{{ $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) }}">
        </div>

        {{-- DEPARTMENT --}}
        <div class="col-md-3">
            <select
                name="department"
                class="form-select">

                <option value="">
                    Semua Departemen
                </option>

                <option value="Marketing"
                    {{ $department == 'Marketing' ? 'selected' : '' }}>
                    Marketing
                </option>

                <option value="Storage"
                    {{ $department == 'Storage' ? 'selected' : '' }}>
                    Storage
                </option>

                <option value="Finance"
                    {{ $department == 'Finance' ? 'selected' : '' }}>
                    Finance
                </option>

                <option value="Creative"
                    {{ $department == 'Creative' ? 'selected' : '' }}>
                    Creative
                </option>

                <option value="Supervisor"
                    {{ $department == 'Supervisor' ? 'selected' : '' }}>
                    Supervisor
                </option>

            </select>
        </div>

        <div class="col-md-2">
            <button
                class="btn text-white"
                style="background:#3b2a6f;">
                Filter
            </button>
        </div>

    </form>

    {{-- CARD REPORT --}}
    <div class="row">

        @for($i = 1; $i <= 5; $i++)

            <div class="col-md-6 mb-4">

                <div class="card shadow-sm p-4"
                     style="min-height:300px;">

                    <h4 class="fw-bold"
                        style="color:#3b2a6f;">
                        Minggu Ke-{{ $i }}
                    </h4>

                    <hr>

                    @if(isset($reports[$i]))

                        @foreach($reports[$i] as $report)

                            <div class="mb-4">

                                <p class="mb-2">
                                    <strong>Departemen:</strong>
                                    {{ $report->department }}
                                </p>

                                <p class="mb-2">
                                    <strong>Dibuat oleh:</strong>
                                    {{ $report->created_by }}
                                </p>

                                {{-- TANGGAL LAPORAN DIBUAT KHUSUS OWNER --}}
                                @if(auth()->check() && auth()->user()->role == 'owner')
                                    <p class="mb-2">
                                        <strong>Tanggal dibuat:</strong>
                                        {{ $report->created_at ? \Carbon\Carbon::parse($report->created_at)->format('d-m-Y H:i') : '-' }}
                                    </p>
                                @endif

                                <p>
                                    {!! nl2br(e($report->report)) !!}
                                </p>

                                @if($report->proof_file)
                                    <a
                                        href="{{ asset('storage/' . $report->proof_file) }}"
                                        target="_blank">
                                        Lihat Bukti
                                    </a>
                                @endif

                            </div>

                            <hr>

                        @endforeach

                    @else

                        <p class="text-muted">
                            Belum ada laporan
                        </p>

                    @endif

                </div>

            </div>

        @endfor

    </div>

</div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>