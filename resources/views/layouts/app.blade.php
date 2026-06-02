<!DOCTYPE html>
<html>
<head>
    <title>PIXL System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
        }

        .sidebar {
            padding-top: 75px;
            width: 200px;
            height: 100vh;
            background-color: #3b2a6f;
            color: white;
            position: fixed;
                z-index: 100;

        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #2c1f55;
        }

        .topbar {
            height: 60px;
            box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.2);
            top: 0;
            left: 0;
            background: white;
            margin-left: 200px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: relative;
            z-index: 100;
        }
        .dropdown-menu {
            position: fixed !important;
            z-index: 9999 !important;
        }
        .content {
            margin-left: 200px;
            padding: 20px;
            font-size: 13px;
            position: relative;
            z-index: 1;
        }
        .modal {
    z-index: 9999 !important;
}

.modal-backdrop {
    z-index: 9998 !important;
}

.modal-dialog {
    position: relative;
    z-index: 10000 !important;
}

    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">

    {{-- MENU BERDASARKAN ROLE --}}
    @if(auth()->user()->role == 'owner')
        <a href="/owner/dashboard">Dashboard</a>
        <a href="/calendar">Booking Calendar</a>
        <a href="/owner/report">Reports</a>
        <a href="/owner/system-log">System Log</a>
        <a href="/storage/quality-control">QC Reports</a>
        <a href="/owner/users">Users</a>

    @elseif(auth()->user()->role == 'marketing')
        <a href="/marketing/dashboard">Dashboard</a>
        <a href="/marketing/customers">Customers</a>
        <a href="/marketing/orders">Orders</a>
        <a href="/calendar">Booking Calendar</a>
        <a href="/weekly-report">Weekly Report</a>

    @elseif(auth()->user()->role == 'storage')
        <a href="/storage/dashboard">Dashboard</a>
        <a href="/storage/reservation-list">Reservation List</a>
        <a href="/storage/equipment">Equipment</a>
        <a href="/calendar">Booking Calendar</a>
        <a href="/storage/quality-control">Quality Control</a>
        <a href="/weekly-report">Weekly Report</a>

    @elseif(auth()->user()->role == 'finance')
        <a href="/finance/dashboard">Dashboard</a>
        <a href="/finance/orders">Orders</a>
        <a href="/calendar">Booking Calendar</a>
        <a href="/weekly-report">Weekly Report</a>
    @else
        <a href="/calendar">Booking Calendar</a>
        <a href="/weekly-report">Weekly Report</a>
    @endif
</div>

<!-- TOPBAR -->
<div class="topbar">
    <div>
        <img src="{{ asset('images/image.png') }}" width="50">
    </div>

    <div class="dropdown">
    <button class="btn d-flex align-items-center border-0 bg-transparent" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
        <i class="bi bi-person-circle fs-1" style="color: #D9D9D9;"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end">
        <li class="px-3 py-2">
            <strong>{{ auth()->user()->name }}</strong><br>
            <small class="text-muted">{{ auth()->user()->role }}</small>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                Change Password
            </a>
        </li>

        <li>
            <form method="POST" action="/logout">
                @csrf
                <button class="dropdown-item text-danger">Logout</button>
            </form>
        </li>
    </ul>
</div>
</div>
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content p-4">

            <h5 class="mb-3">Ganti Password</h5>

            <form method="POST" action="/change-password">
                @csrf

                <div class="mb-3">
                    <label>Password Lama</label>
                    <input type="password" name="old_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>

                <button class="btn w-100 text-white"
                        style="background-color:#3b2a6f;">
                    Simpan
                </button>

            </form>

        </div>
    </div>
</div>
<!-- CONTENT -->
<div class="content">
    @yield('content')
</div>
</body>
</html>