<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .text {
            font-size: 13px;
        }
        #successModal .modal-content {
            width: 400px;
            margin: auto;
        }

        #successModal h5 {
            font-size: 18px;
        }

        #successModal p {
            color: #666;
        }
        .container-fluid {
            overflow: visible !important;
        }
        .pagination {
    margin-left: 20px;
}
    </style>
</head>
<body>
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4>User Management</h4>

    <form method="GET" action="/owner/users" class="d-flex justify-content-between align-items-center mb-3 mt-4">
    
        <div class="d-flex gap-2">
            <input type="text" name="search" class="form-control"
                placeholder="Search"
                value="{{ request('search') }}"
                style="width:300px; font-size: 13px;"
                onkeyup="this.form.submit()">
        </div>

        <!-- tombol tambah user -->
        <a href="#" class="btn text-white"
        style="background-color:#3b2a6f; border-radius:20px; font-size: 13px"
        data-bs-toggle="modal" data-bs-target="#modalUser">
            + Tambah User
        </a>

    </form>


    <div class="card border-0 shadow-sm">
        <table class="table mb-0">
            <thead style="background:#e9e9ee;">
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th>No. Telp</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>
                        <span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ $user->status }}
                        </span>
                    </td>
                    <td>{{ $user->no_telp ?? '-' }}</td>
                    <td>{{ $user->email ?? '-' }}</td>
                    <td>
                        <a href="#"
                        data-bs-toggle="modal"
                        data-bs-target="#editUserModal"
                        onclick="setEditUser({{ $user }})">
                        Edit  
                        </a>
                        <a href="#" 
                        class="text-danger" style="padding-left: 10px;"
                        onclick="openResetModal({{ $user->id }})">
                        Reset Password
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>

        </table>
        
    </div>
    <div class="d-flex justify-content-end mt-3">
    {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
    @if(session('success'))
<script>
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
</script>
@endif
<!-- AUTO OPEN MODAL IF ERROR -->
        @if ($errors->any())
        <script>
            var myModal = new bootstrap.Modal(document.getElementById('modalUser'));
            myModal.show();
        </script>
        @endif
</div>

<style>
.bi-eye, .bi-eye-slash {
    color: #888;
}
.bi-eye:hover, .bi-eye-slash:hover {
    color: #3b2a6f;
}
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
@endsection
<!-- MODAL -->
    <div class="modal fade" id="modalUser" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width:550px;">
            <div class="modal-content p-4" style="border-radius:0px; font-size:13px;">

                <h4 class="mb-3">Tambah User</h4>

                <form method="POST" action="/owner/users/store">
                    @csrf

                    <!-- NAMA -->
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Masukkan Nama" style="font-size:13px; width:500px;" required>
                         @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- USERNAME -->
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control"
                               placeholder="Masukkan Nama Pengguna" style="font-size:13px; width:500px;" required>
                        @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- DEPARTEMEN -->
                    <div class="mb-3">
                        <label>Departemen</label>
                        <select name="role" class="form-control" style="font-size:13px; width:500px;" required>
                            <option value="">Pilih Departemen</option>
                            <option value="owner">Owner</option>
                            <option value="marketing">Marketing</option>
                            <option value="storage">Storage</option>
                            <option value="finance">Finance</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="creative and design">Creative & Design</option>
                        </select>
                    </div>

                    <!-- EMAIL + TELP -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="Masukkan email" style="font-size:13px; width:235px;" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>No. Telp</label>
                            <input type="text" name="no_telp" class="form-control"
                                   placeholder="Masukkan No. Telp" style="font-size:13px; width:235px;" required>
                            @error('no_telp') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-3 position-relative">
                        <label>Password</label>
                        <div class="position-relative">
                            <input type="password" id="password" name="password"
                                class="form-control pe-5"
                                placeholder="Masukkan Password"
                                style="font-size:13px; width:500px;" required>

                            <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3"
                            style="cursor:pointer;"
                            onclick="togglePassword('password', this)"></i>
                        </div>
                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- KONFIRMASI PASSWORD -->
                    <div class="mb-3 position-relative">
                        <label>Konfirmasi Password</label>
                        <div class="position-relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control pe-5"
                                placeholder="Masukkan Ulang Password"
                                style="font-size:13px; width:500px;" required>

                            <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3"
                            style="cursor:pointer;"
                            onclick="togglePassword('password_confirmation', this)"></i>
                        </div>
                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- BUTTON -->
                    <button class="btn text-white"
                            style="background-color:#3b2a6f; border-radius:8px; font-size:13px; width:500px;">
                        Save
                    </button>

                </form>

            </div>
        </div>

        
    </div>

    @if(session('success'))
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-4 text-center" style="border-radius:10px;">

                    <!-- ICON -->
                    <div class="mb-2">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:40px;"></i>
                    </div>

                    <!-- TITLE -->
                    <h5 class="fw-bold">Berhasil</h5>

                    <!-- MESSAGE -->
                    <p class="mb-3">{{ session('success') }}</p>

                    <!-- BUTTON -->
                    <button class="btn text-white px-4"
                            style="background-color:#3b2a6f; border-radius:8px;"
                            data-bs-dismiss="modal">
                        Done
                    </button>

                </div>
            </div>
        </div>
@endif


    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:550px;">
            <div class="modal-content p-4">

                <h4 class="mb-3">Edit Data User</h4>

                <form method="POST" action="/owner/users/update">
                    @csrf

                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" name="name" id="edit_name" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>No. Telp</label>
                            <input type="text" name="no_telp" id="edit_no_telp" class="form-control">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label>Departemen</label>
                        <select name="role" id="edit_role" class="form-control">
                            <option value="owner">Owner</option>
                            <option value="marketing">Marketing</option>
                            <option value="storage">Storage</option>
                            <option value="finance">Finance</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="creative and design">Creative & Design</option>
                        </select>
                    </div>

                    <!-- STATUS -->
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>

                    <button class="btn text-white w-100"
                            style="background-color:#3b2a6f;">
                        Save
                    </button>

                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmResetModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content p-4 text-center">

                <!-- ICON -->
                <div class="mb-2">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size:40px;"></i>
                </div>

                <!-- TITLE -->
                <h5 class="fw-bold">Konfirmasi</h5>

                <!-- MESSAGE -->
                <p class="mb-3">Reset password ke default (123456)?</p>

                <!-- BUTTON -->
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    <a id="confirmResetBtn" class="btn text-white"
                    style="background-color:#3b2a6f;">
                    Yes
                    </a>
                </div>

            </div>
        </div>
    </div>
<!-- SCRIPT -->
<script>
function togglePassword(id, icon) {
    let input = document.getElementById(id);

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}
</script>
<script>
function setEditUser(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_no_telp').value = user.no_telp;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_status').value = user.status;
}
</script>
<script>
function openResetModal(userId) {
    // set link reset ke tombol Yes
    document.getElementById('confirmResetBtn').href = '/owner/users/reset/' + userId;

    // tampilkan modal
    var modal = new bootstrap.Modal(document.getElementById('confirmResetModal'));
    modal.show();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>