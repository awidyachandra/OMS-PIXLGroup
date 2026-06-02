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

        .topbar {
            height: 60px;
            box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.2);
            top: 0;
            left: 0;
            background: #3b2a6f ;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: relative;
            z-index: 2000;
        }
        
        .content {
            padding: 20px;
            font-size: 13px;
            position: relative;
            z-index: 1;
        }
        
    </style>
</head>
<body>


<!-- TOPBAR -->
<div class="topbar">
    <div>
        <img src="{{ asset('images/image.png') }}" width="50">
    </div>
</div>
<!-- CONTENT -->
<div class="content">
    @yield('content')
</div>
</body>
</html>