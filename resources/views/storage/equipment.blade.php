<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Equipment</title>

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
    </style>
</head>

<body>
@extends('layouts.app')

@section('content')

<h4 class="fw-bold mb-3">
    @if(request('backup') == '1')
        Backup Unit
    @else
        Equipment
    @endif
</h4>

<div class="mb-3">

    <!-- BARIS 1: SEARCH + BUTTON -->
    <div class="d-flex justify-content-between align-items-center mb-2">

        <form method="GET" action="" class="d-flex gap-2">
            <input type="text" name="search" class="form-control"
                   placeholder="Search"
                   style="width:300px;"
                   value="{{ request('search') }}">

            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif

            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            @if(request('backup') == '1')
                <input type="hidden" name="backup" value="1">
            @endif
        </form>

        <div class="d-flex gap-2">
            @if(request('backup') == '1')
                <a href="{{ url('/storage/equipment') }}"
                   class="btn"
                   style="border:1px solid #3b2a6f; color:#3b2a6f; border-radius:20px;">
                    Lihat Unit Reguler
                </a>
            @else
                <a href="{{ url('/storage/equipment?backup=1') }}"
                   class="btn"
                   style="border:1px solid #3b2a6f; color:#3b2a6f; border-radius:20px;">
                    Lihat Backup Unit
                </a>
            @endif

            <button class="btn text-white"
                style="background:#3b2a6f; border-radius:20px;"
                data-bs-toggle="modal" data-bs-target="#modalTambah">
                + Tambah Unit
            </button>
        </div>

    </div>

    <!-- BARIS 2: FILTER -->
    <form method="GET" action="">
        <div class="d-flex gap-2">

            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            @if(request('backup') == '1')
                <input type="hidden" name="backup" value="1">
            @endif

            <!-- KATEGORI -->
            <select name="kategori" class="form-select" style="width:200px;">
                <option value="">Semua Kategori</option>
                <option value="HT" {{ request('kategori') == 'HT' ? 'selected' : '' }}>HT</option>
                <option value="Photobooth" {{ request('kategori') == 'Photobooth' ? 'selected' : '' }}>Photobooth</option>
                <option value="SMOKE STAGE LED" {{ request('kategori') == 'SMOKE STAGE LED' ? 'selected' : '' }}>SMOKE STAGE LED</option>
                <option value="HDTV SPLITTER" {{ request('kategori') == 'HDTV SPLITTER' ? 'selected' : '' }}>HDTV SPLITTER</option>
                <option value="KABEL HDMI" {{ request('kategori') == 'KABEL HDMI' ? 'selected' : '' }}>KABEL HDMI</option>
            </select>

            <!-- STATUS -->
            <select name="status" class="form-select" style="width:200px;">
                <option value="">Semua Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>

            <!-- BUTTON -->
            <button class="btn text-white" style="background:#3b2a6f;">
                Filter
            </button>

        </div>
    </form>

</div>

<table class="table">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Kategori</th>
            <th>Nama Produk</th>
            <th>Status</th>
            <th>Harga</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($units as $u)
        <tr>
            <td>{{ $u->kode_unit }}</td>

            <td>{{ $u->kategori }}</td>

            <td>{{ $u->nama_unit }}</td>

            <td>
                <span class="badge
                    @if($u->status == 'available') bg-success
                    @elseif($u->status == 'assigned') bg-primary
                    @elseif($u->status == 'rented') bg-warning text-dark
                    @elseif($u->status == 'maintenance') bg-secondary
                    @else bg-dark
                    @endif">
                    {{ $u->status }}
                </span>
            </td>

            <td>
                Rp {{ number_format($u->harga_sewa) }}
            </td>

            <td>
                <a href="javascript:void(0)" onclick='editUnit(@json($u))'>
                    Edit
                </a>

                <a href="javascript:void(0)" onclick="confirmDelete('{{ $u->kode_unit }}')">
                    Delete
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-end mt-3">
    {{ $units->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif

@endsection

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">

            <h4 class="mb-4">Tambah Unit</h4>

            <form method="POST" action="/storage/equipment/store">
                @csrf

                <!-- KATEGORI -->
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="kategori" class="form-select" onchange="previewKode()" required>
                        <option value="">Pilih</option>
                        <option value="HT">HT</option>
                        <option value="SMOKE STAGE LED">SMOKE STAGE LED</option>
                        <option value="HDTV SPLITTER">HDTV SPLITTER</option>
                        <option value="KABEL HDMI">KABEL HDMI</option>
                                                <option value="EARPHONE">EARPHONE</option>

                    </select>
                </div>

                <!-- KODE -->
                <div class="mb-3">
                    <label>Kode Unit</label>
                    <input type="text" id="kode_preview" class="form-control" readonly>
                </div>

                <!-- NAMA -->
                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_unit" class="form-control" required>
                </div>

                <!-- HARGA -->
                <div class="mb-3">
                    <label>Harga Sewa</label>
                    <input type="number" name="harga_sewa" class="form-control" required>
                </div>

                <!-- STATUS -->
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="available" selected>Available</option>
                        <option value="rented">Rented</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

                <!-- BACKUP -->
                <div class="mb-3">
                    <label>Backup Unit</label>
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="is_backup"
                               value="1"
                               id="is_backup">

                        <label class="form-check-label" for="is_backup">
                            Jadikan sebagai backup unit
                        </label>
                    </div>
                </div>

                <button class="btn w-100 text-white" style="background:#3b2a6f;">
                    Simpan
                </button>

            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">

            <h4>Edit Unit</h4>

            <form method="POST" action="/storage/equipment/update">
                @csrf

                <input type="hidden" name="id" id="edit_id">

                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="kategori" id="edit_kategori" class="form-select">
                        <option value="HT">HT</option>
                        <option value="SMOKE STAGE LED">SMOKE STAGE LED</option>
                        <option value="HDTV SPLITTER">HDTV SPLITTER</option>
                        <option value="KABEL HDMI">KABEL HDMI</option>
                        <option value="EARPHONE">EARPHONE</option>

                    </select>
                </div>

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama_unit" id="edit_nama" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Harga</label>
                    <input type="number" name="harga_sewa" id="edit_harga" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" id="edit_status" class="form-select">
                        <option value="available">Available</option>
                        <option value="rented">Rented</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

                <!-- BACKUP -->
                <div class="mb-3">
                    <label>Backup Unit</label>
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="is_backup"
                               value="1"
                               id="edit_is_backup">

                        <label class="form-check-label" for="edit_is_backup">
                            Tandai sebagai backup unit
                        </label>
                    </div>
                </div>

                <button class="btn w-100 text-white" style="background:#3b2a6f;">
                    Update
                </button>

            </form>
        </div>
    </div>
</div>

{{-- MODAL DELETE --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border-radius:10px;">

            <div class="mb-2">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size:50px;"></i>
            </div>

            <h5 class="fw-bold">Konfirmasi</h5>

            <p>Yakin ingin menghapus data ini?</p>

            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <a id="btnDelete" class="btn text-white" style="background:#dc3545;">
                    Hapus
                </a>
            </div>

        </div>
    </div>
</div>

{{-- MODAL SUCCESS --}}
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border-radius:10px;">

            <div class="mb-2">
                <i class="bi bi-check-circle-fill text-success" style="font-size:50px;"></i>
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

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const prefixMap = {
    "HT": "ht",
    "Photobooth": "pb",
    "SMOKE STAGE LED": "sm",
    "HDTV SPLITTER": "hs",
    "KABEL HDMI": "kh",
    "EARPHONE": "ep"
    
};

// preview kode
function previewKode() {
    let kategori = document.querySelector('#modalTambah [name="kategori"]').value;

    if (!kategori) return;

    fetch('/storage/get-kode-unit/' + encodeURIComponent(kategori))
        .then(response => response.json())
        .then(data => {
            document.getElementById('kode_preview').value = data.kode;
        })
        .catch(error => {
            console.log('Error:', error);
        });
}

// EDIT
function editUnit(data) {
    document.getElementById('edit_id').value = data.kode_unit;
    document.getElementById('edit_kategori').value = data.kategori;
    document.getElementById('edit_nama').value = data.nama_unit;
    document.getElementById('edit_harga').value = data.harga_sewa;
    document.getElementById('edit_status').value = data.status;

    document.getElementById('edit_is_backup').checked =
        data.is_backup == 1 || data.is_backup === true;

    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function confirmDelete(id) {
    document.getElementById('btnDelete').href = '/storage/equipment/delete/' + id;

    let modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

</html>