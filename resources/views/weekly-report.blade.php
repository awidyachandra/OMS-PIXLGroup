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

    {{-- FILTER BULAN --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <form method="GET" style="max-width:300px;">
            <input
                type="month"
                name="period"
                class="form-control"
                value="{{ $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) }}"
                onchange="this.form.submit()">
        </form>

        <button
            class="btn text-white"
            style="background:#3b2a6f;"
            data-bs-toggle="modal"
            data-bs-target="#weeklyReportModal">
            + Buat Laporan
        </button>

    </div>


    {{-- WEEKLY REPORT CARD --}}
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

                            <p>{!! nl2br(e($report->report)) !!}</p>

                            @if($report->proof_file)
                                <a
                                    href="{{ asset('storage/' . $report->proof_file) }}"
                                    target="_blank">
                                    Lihat Bukti
                                </a>
                                <div class="mt-3">
                                    <button
                                        class="btn btn-sm text-white"
                                        style="background:#3b2a6f;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editReportModal{{ $report->id }}">
                                        Edit
                                    </button>
                                    <button
                                        class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteReportModal{{ $report->id }}">
                                        Delete
                                    </button>
                                </div>
                            @endif

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
{{-- MODAL TAMBAH LAPORAN --}}
<div class="modal fade"
     id="weeklyReportModal"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">

            <h2 class="fw-bold mb-4">
                Weekly Report
            </h2>

            <form
                method="POST"
                action="{{ url('/weekly-report/store') }}"
                enctype="multipart/form-data">

                @csrf

                {{-- PERIODE MINGGU --}}
                <div class="mb-3">
                    <label>Periode Minggu</label>

                    <select
                        name="week"
                        class="form-select"
                        required>

                        <option value="">
                            Pilih Periode
                        </option>

                        <option value="1">
                            Minggu Ke-1
                        </option>

                        <option value="2">
                            Minggu Ke-2
                        </option>

                        <option value="3">
                            Minggu Ke-3
                        </option>

                        <option value="4">
                            Minggu Ke-4
                        </option>

                        <option value="5">
                            Minggu Ke-5
                        </option>

                    </select>
                </div>

                {{-- BULAN --}}
                <div class="mb-3">
                    <label>Bulan & Tahun</label>

                    <input
                        type="month"
                        name="period"
                        class="form-control"
                        required>
                </div>

                {{-- REPORT --}}
                <div class="mb-3">
                    <label>Report</label>

                    <textarea
                        name="report"
                        rows="6"
                        class="form-control"
                        required></textarea>
                </div>

                {{-- FILE --}}
                <div class="mb-4">
                    <label>Bukti (PDF / Foto)</label>

                    <input
                        type="file"
                        name="proof_file"
                        class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png">
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

@foreach($reports->flatten() as $report)
<div class="modal fade"
     id="editReportModal{{ $report->id }}"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">

            <h2 class="fw-bold mb-4">
                Edit Weekly Report
            </h2>

            <form method="POST"
                  action="{{ url('/weekly-report/update/' . $report->id) }}"
                  enctype="multipart/form-data">
                @csrf

                {{-- WEEK --}}
                <div class="mb-3">
                    <label>Minggu</label>
                    <input
                        type="text"
                        class="form-control"
                        value="Minggu Ke-{{ $report->week }}"
                        readonly>
                </div>

                {{-- PERIODE --}}
                <div class="mb-3">
                    <label>Periode</label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ date('F Y', mktime(0,0,0,$report->month,1,$report->year)) }}"
                        readonly>
                </div>

                {{-- REPORT --}}
                <div class="mb-4">
                    <label>Report</label>

                    <textarea
                        name="report"
                        class="form-control"
                        rows="8"
                        required>{{ $report->report }}</textarea>
                </div>

                {{-- FILE LAMA --}}
                @if($report->proof_file)
                    <div class="mb-3">
                        <label>Bukti Saat Ini</label><br>

                        <a href="{{ asset('storage/' . $report->proof_file) }}"
                           target="_blank">
                            Lihat Bukti Lama
                        </a>
                    </div>
                @endif

                {{-- GANTI FILE --}}
                <div class="mb-4">
                    <label>Ganti Bukti (Opsional)</label>

                    <input
                        type="file"
                        name="proof_file"
                        class="form-control">
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
@foreach($reports->flatten() as $report)

<div class="modal fade"
     id="deleteReportModal{{ $report->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">

            <h4 class="fw-bold mb-3">
                Hapus Laporan
            </h4>

            <p>
                Apakah Anda yakin ingin menghapus laporan ini?
            </p>

            <form
                method="POST"
                action="{{ route('weekly.report.delete', $report->id) }}">
                @csrf

                <div class="d-flex gap-2 justify-content-end">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger">
                        Ya, Hapus
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endforeach
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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