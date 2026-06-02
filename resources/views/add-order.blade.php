<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Order</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@extends('layouts.form')

@section('content')

<div class="container mt-4" style="max-width:700px;">

    <h4 class="text-center mb-5 fw-bold" style="color:#3b2a6f;">
        Form Order PIXL Rent & PIXL Moment
    </h4>

    <div class="card"
         style="border-radius:15px; background-color:#ececec; padding:40px; border:none;">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Gagal!</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/order/product/store">
            @csrf

            {{-- HIDDEN --}}
            <input type="hidden" name="order_id" value="{{ $id }}">

            {{-- tetap disediakan agar fungsi lama aman --}}
            <input type="hidden"
                   name="date"
                   id="date"
                   value="{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}">

            {{-- tanggal sewa order utama --}}
            <input type="hidden"
                   name="start_date"
                   id="start_date"
                   value="{{ $order->start_date ? \Carbon\Carbon::parse($order->start_date)->format('Y-m-d') : \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}">

            <input type="hidden"
                   name="end_date"
                   id="end_date"
                   value="{{ $order->end_date ? \Carbon\Carbon::parse($order->end_date)->format('Y-m-d') : \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}">

            {{-- INFO TANGGAL --}}
            <div class="mb-3">
                <label>Tanggal Sewa</label>
                <input type="text"
                       class="form-control"
                       value="{{
                            ($order->start_date ? \Carbon\Carbon::parse($order->start_date)->format('d M Y') : \Carbon\Carbon::parse($order->date)->format('d M Y'))
                            .
                            ' s/d '
                            .
                            ($order->end_date ? \Carbon\Carbon::parse($order->end_date)->format('d M Y') : \Carbon\Carbon::parse($order->date)->format('d M Y'))
                       }}"
                       readonly>
            </div>

            {{-- PRODUK --}}
            <div class="mb-3">
                <label>Produk</label>

                <select
                    name="product"
                    id="product"
                    class="form-select"
                    required
                >
                    <option value="">Pilih Produk</option>
                    <option value="HT">HT</option>
                    <option value="Photobooth">Photobooth</option>
                    <option value="SMOKE STAGE LED">SMOKE STAGE LED</option>
                    <option value="HDTV SPLITTER">HDTV SPLITTER</option>
                    <option value="KABEL HDMI">KABEL HDMI</option>
                </select>
            </div>

            {{-- JUMLAH --}}
            <div class="mb-3">
                <label id="stockLabel">
                    Jumlah Unit (Unit tersedia: 0)
                </label>

                <input
                    type="number"
                    name="qty"
                    id="qty"
                    class="form-control"
                    placeholder="Masukkan jumlah unit"
                    min="1"
                    required
                >

                <small id="qtyWarning" class="text-danger d-none">
                    Jumlah unit tidak boleh melebihi unit yang tersedia.
                </small>
            </div>

            <button
                type="submit"
                id="submitBtn"
                class="btn w-100 text-white mt-3"
                style="background:#3b2a6f;"
            >
                Kirim
            </button>

        </form>

    </div>
</div>

<script>
let availableStock = null;

function checkAvailability() {
    let startDate = document.getElementById('start_date').value;
    let endDate = document.getElementById('end_date').value;
    let product = document.getElementById('product').value;
    let qtyInput = document.getElementById('qty');
    let submitBtn = document.getElementById('submitBtn');
    let warning = document.getElementById('qtyWarning');

    if (startDate && endDate && product) {
        submitBtn.disabled = true;

        fetch(`/check-availability?start_date=${startDate}&end_date=${endDate}&product=${encodeURIComponent(product)}`)
            .then(response => response.json())
            .then(data => {
                availableStock = parseInt(data.available) || 0;

                document.getElementById('stockLabel').innerText =
                    `Jumlah Unit (Unit tersedia: ${availableStock})`;

                qtyInput.max = availableStock;

                if (availableStock <= 0) {
                    qtyInput.value = '';
                    qtyInput.disabled = true;
                    submitBtn.disabled = true;

                    warning.classList.remove('d-none');
                    warning.innerText = 'Unit tidak tersedia untuk tanggal dan produk yang dipilih.';
                } else {
                    qtyInput.disabled = false;
                    submitBtn.disabled = false;

                    warning.classList.add('d-none');
                    warning.innerText = 'Jumlah unit tidak boleh melebihi unit yang tersedia.';
                }

                validateQty();
            })
            .catch(error => {
                console.log(error);
                submitBtn.disabled = true;
            });
    }
}

function validateQty() {
    let qtyInput = document.getElementById('qty');
    let warning = document.getElementById('qtyWarning');
    let submitBtn = document.getElementById('submitBtn');
    let qty = parseInt(qtyInput.value) || 0;

    if (availableStock === null) {
        submitBtn.disabled = true;
        return false;
    }

    if (availableStock <= 0) {
        submitBtn.disabled = true;
        warning.classList.remove('d-none');
        warning.innerText = 'Unit tidak tersedia untuk tanggal dan produk yang dipilih.';
        return false;
    }

    if (qty > availableStock) {
        qtyInput.value = availableStock;
        warning.classList.remove('d-none');
        warning.innerText = `Jumlah unit maksimal hanya ${availableStock} unit.`;
        submitBtn.disabled = false;
        return false;
    }

    if (qty < 1 && qtyInput.value !== '') {
        qtyInput.value = 1;
        warning.classList.remove('d-none');
        warning.innerText = 'Jumlah unit minimal 1.';
        submitBtn.disabled = false;
        return false;
    }

    warning.classList.add('d-none');
    warning.innerText = 'Jumlah unit tidak boleh melebihi unit yang tersedia.';
    submitBtn.disabled = false;

    return true;
}

window.onload = function () {

    document.getElementById('submitBtn').disabled = true;

    document.getElementById('product')
        .addEventListener('change', function () {
            availableStock = null;
            document.getElementById('qty').value = '';
            document.getElementById('submitBtn').disabled = true;
            checkAvailability();
        });

    document.getElementById('qty')
        .addEventListener('input', validateQty);

    document.querySelector('form').addEventListener('submit', function (e) {
        let product = document.getElementById('product').value;
        let qty = parseInt(document.getElementById('qty').value) || 0;

        if (!product) {
            e.preventDefault();
            alert('Silakan pilih produk terlebih dahulu.');
            return;
        }

        if (availableStock === null) {
            e.preventDefault();
            alert('Sistem masih mengecek ketersediaan unit.');
            return;
        }

        if (availableStock <= 0) {
            e.preventDefault();
            alert('Unit tidak tersedia untuk produk ini pada tanggal order.');
            return;
        }

        if (qty < 1) {
            e.preventDefault();
            alert('Silakan masukkan jumlah unit.');
            return;
        }

        if (qty > availableStock) {
            e.preventDefault();
            alert('Jumlah unit melebihi stok tersedia.');
            return;
        }
    });

};
</script>

@endsection
</body>
</html>