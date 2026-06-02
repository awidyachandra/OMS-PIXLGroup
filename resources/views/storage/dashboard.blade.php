@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --purple: #34215f;
            --border: #c9c3d8;
            --shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        }

        .dashboard-page {
        }

        .dashboard-title {
            color: #34215f;
            font-weight: bold;
            margin-bottom: 24px;
            
        }

        .dashboard-card {
            background: #fff;
            border: 1px solid #cfcfcf;
            box-shadow: var(--shadow);
        }

        /* Availability */
        .availability-card,
        .summary-wrapper {
            min-height: 170px;
            height: 100%;
        }

        /* Availability */
        .availability-card {
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
        }

        .availability-date-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .availability-form {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .availability-date-input {
            flex: 1;
            height: 36px;
            border: 1px solid #bfc3cd;
            border-radius: 5px;
            padding: 0 12px;
            font-size: 14px;
            color: #666;
            background: #fff;
        }

        .availability-calendar-btn {
            width: 34px;
            height: 34px;
            border: none;
            background: transparent;
            color: #777;
            font-size: 23px;
            cursor: pointer;
        }

        .availability-bottom-row {
            display: grid;
            grid-template-columns: 90px 1fr 92px;
            gap: 12px;
            align-items: center;
        }

        .availability-select {
            height: 31px;
            border: 1px solid #bfc3cd;
            border-radius: 4px;
            font-size: 12px;
            padding: 0 8px;
            background: #fff;
        }

        .availability-stock {
            height: 31px;
            border: 1px solid #bfc3cd;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            background: #fff;
            white-space: nowrap;
        }

        .availability-check-btn {
            height: 31px;
            border: none;
            border-radius: 2px;
            background: var(--purple);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }

        /* Summary */
        .summary-wrapper {
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .summary-box {
            min-height: 105px;
            padding: 12px;
            color: var(--purple);
            display: grid;
            grid-template-columns: 38px 1fr;
            align-items: center;
            column-gap: 10px;
            overflow: hidden;
        }

        .summary-box i {
            font-size: 34px;
            line-height: 1;
        }

        .summary-content {
            min-width: 0;
            overflow: hidden;
        }

        .summary-title {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .summary-number {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.1;
            margin-top: 6px;
        }

        .summary-label {
            font-size: 12px;
            line-height: 1.1;
            margin-top: 6px;
            white-space: normal;
        }

        .summary-total {
            background: #ececf3;
        }

        .summary-available {
            background: #eaf8e8;
        }

        .summary-rented {
            background: #f0f3fa;
        }

        .summary-maintenance {
            background: #fffbd7;
        }

        .category-row {
            margin-top: 10px;
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .category-chip {
            flex: 0 0 auto;
            border: 1px solid #d5cfe3;
            border-radius: 14px;
            padding: 4px 10px;
            font-size: 12px;
            color: var(--purple);
            background: #fff;
            white-space: nowrap;
        }

        /* List cards */
        .section-title {
            color: var(--purple);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .list-card {
            padding: 16px 22px;
            min-height: 210px;
        }

        .card-scroll {
            max-height: 215px;
            overflow-y: auto;
        }

        .list-item {
            display: grid;
            grid-template-columns: 115px 1fr;
            align-items: center;
            border: 1px solid var(--border);
            border-bottom: none;
            padding: 12px 14px;
            color: var(--purple);
            text-decoration: none;
        }

        .list-item:last-child {
            border-bottom: 1px solid var(--border);
        }

        .list-item:hover {
            background: #f8f7fb;
            color: var(--purple);
        }

        .list-left {
            font-size: 20px;
            font-weight: 500;
            border-right: 1px solid var(--border);
            padding-right: 12px;
        }

        .list-right {
            padding-left: 14px;
            font-size: 14px;
            line-height: 1.2;
        }

        .assign-btn {
            background: var(--purple);
            color: #fff;
            border-radius: 5px;
            font-weight: 700;
            padding: 8px 22px;
            text-decoration: none;
            font-size: 14px;
        }

        .assign-btn:hover {
            color: #fff;
            background: #29184d;
        }

        .overdue-box {
            background: #ffe1e1;
            border: 1px solid #e5b7c3;
            padding: 14px;
            color: var(--purple);
            display: grid;
            grid-template-columns: 115px 1fr;
            align-items: center;
            text-decoration: none;
        }

        @media (max-width: 1200px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .summary-wrapper {
                min-height: auto;
            }
        }

        @media (max-width: 768px) {
            .dashboard-page {
                padding: 16px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .availability-bottom-row {
                grid-template-columns: 1fr;
            }

            .list-item,
            .overdue-box {
                grid-template-columns: 1fr;
            }

            .list-left {
                border-right: none;
                border-bottom: 1px solid var(--border);
                padding-bottom: 6px;
                margin-bottom: 6px;
            }

            .list-right {
                padding-left: 0;
            }
        }
    </style>

    <div class="dashboard-page">


        <div class="row g-4 mb-4 align-items-stretch">
                <div class="col-lg-4 d-flex">
                    <div class="dashboard-card availability-card w-100 h-100">
                        <h4 class="card-title-purple">Availability Check</h4>

                        <form action="{{ route('availability.check') }}" method="GET" class="availability-form">
    <div class="availability-date-row">
        <input 
            type="text" 
            id="storageDateRange" 
            name="date_range"
            class="availability-date-input" 
            placeholder="Pilih Tanggal" 
            readonly
            value="{{ isset($pickupDate, $returnDate) ? $pickupDate . ' to ' . $returnDate : '' }}"
        >

        <button type="button" class="availability-calendar-btn" id="openStorageCalendar">
            <i class="bi bi-calendar3"></i>
        </button>
    </div>

    <input type="hidden" name="pickup_date" id="storagePickupDate" value="{{ $pickupDate ?? '' }}">
    <input type="hidden" name="return_date" id="storageReturnDate" value="{{ $returnDate ?? '' }}">

    <div class="availability-bottom-row">
        <select name="product_type" class="availability-select" required>
            <option value="">Unit</option>
            @foreach ($productTypes as $type)
                <option value="{{ $type }}" {{ ($productType ?? '') == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>

        <div class="availability-stock">
            {{ $availableStock !== null ? $availableStock . ' Units' : '- Units' }}
        </div>

        <button type="submit" class="availability-check-btn">
            Check
        </button>
    </div>
</form>
                    </div>
                </div>

                <div class="col-lg-8 d-flex">
                    <div class="dashboard-card summary-wrapper w-100 h-100">
                        <div class="summary-grid">
                            <div class="summary-box summary-total">
                                <i class="bi bi-back"></i>
                                <div class="summary-content">
                                    <div class="summary-title">Total Units</div>
                                    <div class="summary-number">{{ $totalUnits }}</div>
                                    <div class="summary-label">Total Units</div>
                                </div>
                            </div>

                            <div class="summary-box summary-available">
                                <i class="bi bi-calendar-check"></i>
                                <div class="summary-content">
                                    <div class="summary-title">Available</div>
                                    <div class="summary-number">{{ $available }}</div>
                                    <div class="summary-label">Unit Tersedia</div>
                                </div>
                            </div>

                            <div class="summary-box summary-rented">
                                <i class="bi bi-box-arrow-left"></i>
                                <div class="summary-content">
                                    <div class="summary-title">Rented</div>
                                    <div class="summary-number">{{ $rented }}</div>
                                    <div class="summary-label">Disewa</div>
                                </div>
                            </div>

                            <div class="summary-box summary-maintenance">
                                <i class="bi bi-tools"></i>
                                <div class="summary-content">
                                    <div class="summary-title">Maintenance</div>
                                    <div class="summary-number">{{ $maintenance }}</div>
                                    <div class="summary-label">Dalam Perbaikan</div>
                                </div>
                            </div>
                        </div>

                        <div class="category-row">
                            @foreach ($categoryStocks as $cat)
                                <div class="category-chip">
                                    {{ $cat->kategori }}: <b>{{ $cat->total }}</b>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        <div class="row g-4">

            <div class="col-lg-4">

                <div class="dashboard-card list-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="section-title mb-0">Quick Assign</h6>

                        <a href="/storage/reservation-list" class="assign-btn">
                            Assign Now <i class="bi bi-chevron-right ms-2"></i>
                        </a>
                    </div>

                    <div class="card-scroll">
                        @forelse ($quickAssign as $q)
                            <a href="/marketing/orders/detail/{{ $q->id }}" class="list-item">
                                <div class="list-left">
                                    Order #{{ $q->id }}
                                </div>
                                <div class="list-right">
                                    <b>{{ $q->event }}</b><br>
                                    Need {{ $q->details->sum('qty') }} Units
                                </div>
                            </a>
                        @empty
                            <small>Tidak ada order untuk assign.</small>
                        @endforelse
                    </div>
                </div>

                <div class="dashboard-card list-card">
                    <h6 class="section-title">Overdue Returns</h6>

                    <div class="card-scroll">
                        @forelse($overdue as $o)
                            <a href="/marketing/orders/detail/{{ $o->id }}" class="overdue-box mb-2">
                                <div class="list-left">
                                    Order #{{ $o->id }}
                                </div>
                                <div class="list-right">
                                    <b>{{ $o->details->sum('qty') }} Units</b><br>
                                    @php
                                        $returnDate = \Carbon\Carbon::parse($o->return_date)->startOfDay();
                                        $today = now()->startOfDay();
                                        $daysLate = (int) $returnDate->diffInDays($today);
                                    @endphp

                                    {{ $daysLate }} Days Late<br> <small>
                                        Tanggal Pengembalian:
                                        {{ \Carbon\Carbon::parse($o->return_date)->format('d F Y') }}
                                    </small>
                                </div>
                            </a>
                        @empty
                            <small>Tidak ada overdue.</small>
                        @endforelse
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="dashboard-card list-card">
                    <h6 class="section-title">Upcoming Event</h6>

                    <div class="card-scroll">
                        @forelse ($upcoming as $u)
                            <a href="/marketing/orders/detail/{{ $u->id }}" class="list-item">
                                <div class="list-left">
                                    {{ \Carbon\Carbon::parse($u->date)->format('d M') }}
                                </div>
                                <div class="list-right">
                                    <b>{{ $u->event }}</b><br>
                                    {{ $u->details->sum('qty') }} Units
                                </div>
                            </a>
                        @empty
                            <small>Tidak ada upcoming event.</small>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-card list-card">
                    <h6 class="section-title">Currently Rented</h6>

                    <div class="card-scroll">
                        @forelse ($rentedOrders as $r)
                            <a href="/storage/reservation-detail/{{ $r->id }}" class="list-item">
                                <div class="list-left">
                                    Order #{{ $r->id }}
                                </div>
                                <div class="list-right">
                                    <b>{{ $r->details->sum('qty') }} Units</b><br>
                                    @php
                                        $returnDate = \Carbon\Carbon::parse($r->return_date)->startOfDay();
                                        $today = now()->startOfDay();
                                        $daysLeft = (int) $today->diffInDays($returnDate, false);
                                    @endphp

                                    <small>
                                        @if ($daysLeft > 0)
                                            Return in {{ $daysLeft }} Days
                                        @elseif ($daysLeft === 0)
                                            Return Today
                                        @else
                                            Overdue {{ abs($daysLeft) }} Days
                                        @endif
                                    </small><br>
                                    <small>
                                        {{ \Carbon\Carbon::parse($r->return_date)->format('d M Y') }}
                                    </small>
                                </div>
                            </a>
                        @empty
                            <small>Tidak ada unit yang sedang disewa.</small>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateRangeInput = document.getElementById('storageDateRange');
        const openCalendarButton = document.getElementById('openStorageCalendar');
        const pickupDateInput = document.getElementById('storagePickupDate');
        const returnDateInput = document.getElementById('storageReturnDate');

        if (!dateRangeInput || !pickupDateInput || !returnDateInput) {
            return;
        }

        const dateRangePicker = flatpickr(dateRangeInput, {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: false,
            allowInput: false,
            disableMobile: true,
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    pickupDateInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                    returnDateInput.value = instance.formatDate(selectedDates[1], "Y-m-d");
                }
            }
        });

        if (openCalendarButton) {
            openCalendarButton.addEventListener('click', function () {
                dateRangePicker.open();
            });
        }

        dateRangeInput.addEventListener('click', function () {
            dateRangePicker.open();
        });
    });
</script>
@endsection
