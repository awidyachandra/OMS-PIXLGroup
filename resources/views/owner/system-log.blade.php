<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Document</title>
    <style>
/* DEFAULT */
.pagination .page-link {
    color: #3b2a6f;
    border: 1px solid #3b2a6f;
}

/* HOVER */
.pagination .page-link:hover {
    background-color: #3b2a6f;
    color: #fff;
}

/* ACTIVE */
.pagination .page-item.active .page-link {
    background-color: #3b2a6f;
    border-color: #3b2a6f;
    color: #fff;
}

/* DISABLED */
.pagination .page-item.disabled .page-link {
    color: #aaa;
    border-color: #ddd;
}
</style>
</head>
<body>
    @extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">System Log</h2>

    <form method="GET" class="d-flex justify-content-between mb-4">

        {{-- FILTER TANGGAL --}}
        <div style="max-width:250px;">
            <input type="date"
                   name="date"
                   class="form-control"
                   value="{{ request('date') }}"
                   onchange="this.form.submit()">
        </div>

        {{-- SEARCH --}}
        <div style="max-width:300px;">
            <input type="text"
                   name="search"
                   class="form-control"
                   placeholder="Search"
                   value="{{ request('search') }}">
        </div>

    </form>

    <div class="card p-4 shadow-sm" style="border-radius:12px;">

        <table class="table align-middle">
            <thead style="background:#f1f1f5;">
                <tr>
                    <th>Level</th>
                    <th>User</th>
                    <th>Activity</th>
                    <th>Context</th>
                    <th>Date Time</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $log)
                <tr>

                    {{-- LEVEL --}}
                    <td>
                        @if($log->level == 'info')
                            <span class="badge bg-primary">Info</span>
                        @elseif($log->level == 'warning')
                            <span class="badge bg-warning text-dark">Warning</span>
                        @elseif($log->level == 'error')
                            <span class="badge bg-danger">Error</span>
                        @endif
                    </td>

                    {{-- USER --}}
                    <td>{{ $log->user }}</td>

                    {{-- ACTIVITY --}}
                    <td>{{ $log->activity }}</td>

                    {{-- CONTEXT --}}
                    <td>{{ $log->context }}</td>

                    {{-- DATE --}}
                    <td>
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">
                        Tidak ada log
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>

    </div>
<div class="d-flex justify-content-end mt-3">
    {{ $logs->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
</div>

@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>