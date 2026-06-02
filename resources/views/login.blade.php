<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login PIXL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #3b2a6f;
        }
        .login-card {
            width: 400px;
            border-radius: 20px;
        }
        .btn-custom {
            background-color: #3b2a6f;
            color: white;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

<div class="text-center">
    <img src="{{ asset('images/image.png') }}" width="80" class="mb-3">
    
    <div class="card login-card p-4">
        <h4 class="mb-3">Login</h4>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="mb-3 text-start">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan Nama Pengguna">
            </div>

            <div class="mb-3 text-start">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan Kata Sandi">
            </div>

            <button class="btn btn-custom w-100">Masuk</button>
        </form>
    </div>
</div>

</body>
</html>