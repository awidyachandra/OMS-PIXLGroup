<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Form Order</title>
</head>
<body>
@extends('layouts.form')

@section('content')

<div class="container mt-4" style="max-width:700px;">

    <h4 class="text-center mb-5 fw-bold" style="color:#3b2a6f;">
        Form Order PIXL Rent & PIXL Moment
    </h4>

    <div class="card" style="border-radius:15px; background-color:#ececec; padding:40px; border-style:none;">

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

        <form id="orderForm" method="POST" action="/order" enctype="multipart/form-data">
            @csrf

            {{-- PENANDA TAMBAH PRODUK --}}
            <input type="hidden" name="add_product" id="add_product" value="0">

            <!-- NAMA -->
            <div class="mb-3">
                <label>Nama</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name') }}"
                       oninput="this.value = this.value.toUpperCase()"
                       placeholder="Masukkan nama Anda"
                       required>
            </div>

            <!-- EMAIL -->
            <div class="mb-3">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       placeholder="Masukkan email Anda"
                       required>
            </div>

            <!-- WHATSAPP -->
            <div class="mb-3">
                <label>No. Whatsapp</label>
                <input type="text"
                       name="phone"
                       class="form-control"
                       value="{{ old('phone') }}"
                       placeholder="Masukkan no. Whatsapp Anda"
                       required>
            </div>

            <!-- INSTAGRAM -->
            <div class="mb-3">
                <label>Instagram Event/Instansi</label>
                <input type="text"
                       name="instagram"
                       class="form-control"
                       value="{{ old('instagram') }}"
                       placeholder="Masukkan username instagram Event atau Instansi Anda">
            </div>

            <!-- JENIS ORGANISASI -->
            <div class="mb-3">
                <label>Jenis Organisasi</label>
                <select name="organization" class="form-select" required>
                    <option value="">Pilih Organisasi</option>
                    <option value="Umum" {{ old('organization') == 'Umum' ? 'selected' : '' }}>Umum</option>
                    <option value="Himpunan Mahasiswa Jurusan" {{ old('organization') == 'Himpunan Mahasiswa Jurusan' ? 'selected' : '' }}>Himpunan Mahasiswa Jurusan</option>
                    <option value="BEM Universitas" {{ old('organization') == 'BEM Universitas' ? 'selected' : '' }}>BEM Universitas</option>
                    <option value="BEM Fakultas" {{ old('organization') == 'BEM Fakultas' ? 'selected' : '' }}>BEM Fakultas</option>
                    <option value="Wedding Organizer" {{ old('organization') == 'Wedding Organizer' ? 'selected' : '' }}>Wedding Organizer</option>
                    <option value="Event Organizer" {{ old('organization') == 'Event Organizer' ? 'selected' : '' }}>Event Organizer</option>
                </select>
            </div>

            <!-- INSTANSI -->
            <div class="mb-3">
                <label>Instansi/Agensi (Tuliskan secara lengkap tanpa disingkat)</label>
                <input type="text"
                       name="agency"
                       class="form-control"
                       value="{{ old('agency') }}"
                       placeholder="Contoh: Universitas Airlangga">
            </div>

            <!-- EVENT -->
            <div class="mb-3">
                <label>Event</label>
                <input type="text"
                       name="event"
                       class="form-control"
                       value="{{ old('event') }}"
                       placeholder="Masukkan nama acara">
            </div>

            <!-- ALAMAT -->
            <div class="mb-3">
                <label>Alamat Penyewa</label>
                <textarea name="address"
                          class="form-control"
                          rows="3"
                          placeholder="Masukkan alamat lengkap penyewa"
                          required>{{ old('address') }}</textarea>
            </div>

            <!-- PAKET -->
            <div class="mb-3">
                <label>Paket</label>
                <select name="package" class="form-select" required>
                    <option value="">Pilih Paket</option>
                    <option value="Regular" {{ old('package') == 'Regular' ? 'selected' : '' }}>Regular</option>
                    <option value="Sponsorship" {{ old('package') == 'Sponsorship' ? 'selected' : '' }}>Sponsorship</option>
                </select>
            </div>

            <!-- PROPOSAL -->
            <div class="mb-3">
                <label>Proposal (PDF)</label>
                <input type="file"
                       name="proposal"
                       class="form-control"
                       accept="application/pdf">
            </div>

            <!-- TANGGAL -->
            <div class="mb-3">
                <label>Tanggal Sewa</label>
                <input type="text"
                       id="dateRange"
                       class="form-control"
                       placeholder="Pilih tanggal sewa"
                       value="{{ old('start_date') && old('end_date') ? old('start_date') . ' to ' . old('end_date') : '' }}"
                       required>
            </div>

            {{-- hidden --}}
            <input type="hidden" name="start_date" id="start_date" value="{{ old('start_date') }}">
            <input type="hidden" name="end_date" id="end_date" value="{{ old('end_date') }}">
            <input type="hidden" name="date" id="date" value="{{ old('date') }}">

            <!-- PRODUK -->
            <div class="mb-3">
                <label>Produk</label>
                <select name="product"
                        id="product"
                        class="form-select"
                        required>
                    <option value="">Pilih Produk</option>
                    <option value="HT" {{ old('product') == 'HT' ? 'selected' : '' }}>HT</option>
                    <option value="Photobooth" {{ old('product') == 'Photobooth' ? 'selected' : '' }}>Photobooth</option>
                    <option value="SMOKE STAGE LED" {{ old('product') == 'SMOKE STAGE LED' ? 'selected' : '' }}>SMOKE STAGE LED</option>
                    <option value="HDTV SPLITTER" {{ old('product') == 'HDTV SPLITTER' ? 'selected' : '' }}>HDTV SPLITTER</option>
                    <option value="KABEL HDMI" {{ old('product') == 'KABEL HDMI' ? 'selected' : '' }}>KABEL HDMI</option>
                </select>
            </div>

            <!-- JUMLAH -->
            <div class="mb-3">
                <label id="stockLabel">
                    Jumlah Unit (Unit tersedia: 0)
                </label>

                <input type="number"
                       name="qty"
                       id="qty"
                       class="form-control"
                       value="{{ old('qty') }}"
                       placeholder="Masukkan jumlah unit"
                       min="1"
                       required>

                <small id="qtyWarning" class="text-danger d-none">
                    Jumlah unit tidak boleh melebihi unit yang tersedia.
                </small>
            </div>

            <button type="button"
                    class="btn w-100 text-white mt-3"
                    style="background:#3b2a6f;"
                    onclick="openConfirmModal()">
                Kirim
            </button>

        </form>
    </div>
</div>

@endsection

<div class="modal fade" id="confirmOrderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 text-center" style="border-radius:10px;">

            <div class="mb-2">
                <i class="bi bi-question-circle text-warning" style="font-size:40px;"></i>
            </div>

            <h5 class="fw-bold">Konfirmasi</h5>

            <p class="mb-3">Apakah ingin menambahkan produk lain?</p>

            <div class="d-flex justify-content-center gap-2">
                <button type="button"
                        class="btn btn-secondary"
                        onclick="submitForm(false)">
                    Tidak
                </button>

                <button type="button"
                        class="btn text-white"
                        style="background:#3b2a6f;"
                        onclick="submitForm(true)">
                    Ya
                </button>
            </div>

        </div>
    </div>
</div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
let availableStock = 0;

document.addEventListener("DOMContentLoaded", function () {

    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        minDate: "today",

        onChange: function (selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                let start = instance.formatDate(selectedDates[0], "Y-m-d");
                let end = instance.formatDate(selectedDates[1], "Y-m-d");

                document.getElementById('start_date').value = start;
                document.getElementById('end_date').value = end;

                // date tetap diisi tanggal awal agar fungsi lama tetap aman
                document.getElementById('date').value = start;

                resetQtyInput();
                checkAvailability();
            }
        }
    });

    document.getElementById('product').addEventListener('change', function () {
        resetQtyInput();
        checkAvailability();
    });

    document.getElementById('qty').addEventListener('input', validateQty);

    if (
        document.getElementById('start_date').value &&
        document.getElementById('end_date').value &&
        document.getElementById('product').value
    ) {
        checkAvailability();
    }
});

function checkAvailability() {
    let start = document.getElementById('start_date').value;
    let end = document.getElementById('end_date').value;
    let product = document.getElementById('product').value;
    let qtyInput = document.getElementById('qty');

    if (start && end && product) {
        fetch(`/check-availability?start_date=${start}&end_date=${end}&product=${encodeURIComponent(product)}`)
            .then(response => response.json())
            .then(data => {
                availableStock = parseInt(data.available) || 0;

                document.getElementById('stockLabel').innerText =
                    `Jumlah Unit (Unit tersedia: ${availableStock})`;

                qtyInput.max = availableStock;

                if (availableStock <= 0) {
                    qtyInput.value = '';
                    qtyInput.disabled = true;
                    document.getElementById('qtyWarning').classList.remove('d-none');
                    document.getElementById('qtyWarning').innerText =
                        'Unit tidak tersedia untuk tanggal dan produk yang dipilih.';
                } else {
                    qtyInput.disabled = false;
                    document.getElementById('qtyWarning').classList.add('d-none');
                    document.getElementById('qtyWarning').innerText =
                        'Jumlah unit tidak boleh melebihi unit yang tersedia.';
                }

                validateQty();
            })
            .catch(error => {
                console.log(error);
            });
    }
}

function validateQty() {
    let qtyInput = document.getElementById('qty');
    let warning = document.getElementById('qtyWarning');
    let qty = parseInt(qtyInput.value) || 0;

    if (availableStock <= 0) {
        warning.classList.remove('d-none');
        warning.innerText = 'Unit tidak tersedia untuk tanggal dan produk yang dipilih.';
        return false;
    }

    if (qty > availableStock) {
        qtyInput.value = availableStock;
        warning.classList.remove('d-none');
        warning.innerText = `Jumlah unit maksimal hanya ${availableStock} unit.`;
        return false;
    }

    if (qty < 1 && qtyInput.value !== '') {
        qtyInput.value = 1;
        warning.classList.remove('d-none');
        warning.innerText = 'Jumlah unit minimal 1.';
        return false;
    }

    warning.classList.add('d-none');
    warning.innerText = 'Jumlah unit tidak boleh melebihi unit yang tersedia.';
    return true;
}

function resetQtyInput() {
    let qtyInput = document.getElementById('qty');
    let warning = document.getElementById('qtyWarning');

    availableStock = 0;
    qtyInput.value = '';
    qtyInput.removeAttribute('max');
    qtyInput.disabled = false;

    document.getElementById('stockLabel').innerText =
        'Jumlah Unit (Unit tersedia: 0)';

    warning.classList.add('d-none');
    warning.innerText = 'Jumlah unit tidak boleh melebihi unit yang tersedia.';
}

function openConfirmModal() {
    let start = document.getElementById('start_date').value;
    let end = document.getElementById('end_date').value;
    let product = document.getElementById('product').value;
    let qty = document.getElementById('qty').value;

    if (!start || !end) {
        alert('Silakan pilih tanggal sewa terlebih dahulu.');
        return;
    }

    if (!product) {
        alert('Silakan pilih produk terlebih dahulu.');
        return;
    }

    if (availableStock <= 0) {
        alert('Unit tidak tersedia untuk tanggal dan produk yang dipilih.');
        return;
    }

    if (!qty || parseInt(qty) < 1) {
        alert('Silakan masukkan jumlah unit.');
        return;
    }

    if (parseInt(qty) > availableStock) {
        alert(`Jumlah unit tidak boleh melebihi ${availableStock} unit.`);
        return;
    }

    let modal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
    modal.show();
}

function submitForm(isAddProduct) {
    document.getElementById('add_product').value = isAddProduct ? "1" : "0";
    document.getElementById('orderForm').submit();
}
</script>

@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    let modalElement = document.getElementById('successModal');

    if (modalElement) {
        let successModal = new bootstrap.Modal(modalElement);
        successModal.show();
    }
});
</script>
@endif

</body>
</html>