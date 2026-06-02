@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --purple: #34215f;
            --border: #cfcfcf;
            --shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        }



        .dashboard-title {
            font-size: 28px;
            font-weight: 700;
            color: #000;
            margin-bottom: 24px;
        }

        .dashboard-card {
            background: #fff;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .card-title-purple {
            color: var(--purple);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 14px;
        }

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

        .pipeline-card {
            padding: 16px;
            min-height: 455px;
        }

        .pipeline-chart-area {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .chart-wrap {
            position: relative;
            width: 210px;
            height: 210px;
        }

        .donut-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #111;
            font-size: 24px;
            font-weight: 700;
            pointer-events: none;
        }

        .pipeline-legend-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 6px 10px;
            margin: 8px 0 12px;
            padding: 0 4px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
            font-size: 11px;
            color: #555;
            line-height: 1.2;
        }

        .legend-item span:last-child {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .legend-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex: 0 0 9px;
        }

        .order-total-box {
            background: #ececf3;
            color: var(--purple);
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0 12px;
            gap: 10px;
        }

        .order-total-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            font-size: 15px;
            font-weight: 700;
        }

        .order-total-left i {
            font-size: 22px;
            flex: 0 0 auto;
        }

        .order-total-left span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .order-total-number {
            font-size: 23px;
            font-weight: 700;
            white-space: nowrap;
        }

        .pipeline-stats-compact {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .pipeline-stat {
            min-height: 62px;
            padding: 8px;
            display: grid;
            grid-template-columns: 22px minmax(0, 1fr);
            align-items: center;
            column-gap: 7px;
            overflow: hidden;
        }

        .pipeline-stat i {
            font-size: 20px;
            text-align: center;
        }

        .pipeline-stat>div {
            min-width: 0;
        }

        .pipeline-stat-title {
            font-size: 10.5px;
            line-height: 1.15;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pipeline-stat-number {
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
            margin-top: 5px;
        }

        .stat-pending {
            background: #eee9ff;
            color: #5f45c8;
        }

        .stat-dp {
            background: #ffe8e5;
            color: #b94b43;
        }

        .stat-processed {
            background: #e8f8fb;
            color: #15899a;
        }

        .stat-assigned {
            background: #fff0d8;
            color: #9a6500;
        }

        .stat-paid {
            background: #eaf0ff;
            color: #2458b8;
        }

        .stat-rent {
            background: #e4f8f0;
            color: #0d7656;
        }

        .stat-return {
            background: #f3e8ff;
            color: #7d3dc4;
        }

        .stat-completed {
            background: #e8f8e8;
            color: #147542;
        }

        .stat-cancelled {
            background: #eeeeee;
            color: #555;
        }

        @media (max-width: 1200px) {

            .pipeline-legend-grid,
            .pipeline-stats-compact {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {

            .pipeline-legend-grid,
            .pipeline-stats-compact {
                grid-template-columns: 1fr;
            }

            .chart-wrap {
                width: 190px;
                height: 190px;
            }
        }

        /* Trend */
        .trend-card {
            padding: 18px;
            min-height: 455px;
        }

        .trend-chart-wrap {
            height: 360px;
            padding: 16px;
        }

        @media (max-width: 1200px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .pipeline-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .marketing-page {
                padding: 16px;
            }

            .summary-grid,
            .pipeline-stats,
            .availability-bottom-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="marketing-page">


        <div class="row g-2 mb-2">
            <div class="row g-2 mb-2 align-items-stretch">
                <div class="col-lg-4 d-flex">
                    <div class="dashboard-card availability-card w-100 h-100">
                        <h4 class="card-title-purple">Availability Check</h4>

                        <form action="{{ route('availability.marketing.check') }}" method="GET" class="availability-form">
                            <div class="availability-date-row">
                                <input type="text" id="marketingDateRange" name="date_range"
                                    class="availability-date-input" placeholder="Pilih Tanggal" readonly
                                    value="{{ isset($pickupDate, $returnDate) ? $pickupDate . ' to ' . $returnDate : '' }}">

                                <button type="button" class="availability-calendar-btn" id="openMarketingCalendar">
                                    <i class="bi bi-calendar3"></i>
                                </button>
                            </div>

                            <input type="hidden" name="pickup_date" id="marketingPickupDate"
                                value="{{ $pickupDate ?? '' }}">
                            <input type="hidden" name="return_date" id="marketingReturnDate"
                                value="{{ $returnDate ?? '' }}">

                            <div class="availability-bottom-row">
                                <select name="product_type" class="availability-select" required>
                                    <option value="">Unit</option>
                                    @foreach ($productTypes as $type)
                                        <option value="{{ $type }}"
                                            {{ ($productType ?? '') == $type ? 'selected' : '' }}>
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

            {{-- ORDER PIPELINE & TREND --}}
            <div class="row g-2">
                <div class="col-lg-5">
                    <div class="dashboard-card pipeline-card">
                        <h4 class="card-title-purple">Order Pipeline</h4>

                        <div class="pipeline-chart-area">
                            <div class="chart-wrap">
                                <canvas id="pipelineChart"></canvas>
                                <div class="donut-center">{{ $totalOrders }}</div>
                            </div>
                        </div>

                        <div class="pipeline-legend-grid">
                            <div class="legend-item">
                                <span class="legend-dot" style="background:#8b73ff;"></span>
                                <span>Pending</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-dot" style="background:#ff8a83;"></span>
                                <span>DP Paid</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-dot" style="background:#39bfd3;"></span>
                                <span>Processed</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-dot" style="background:#ffb04a;"></span>
                                <span>Assigned</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-dot" style="background:#5b8def;"></span>
                                <span>Fully Paid</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-dot" style="background:#20b486;"></span>
                                <span>On Rent</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-dot" style="background:#b56cff;"></span>
                                <span>Return Check</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-dot" style="background:#46c46f;"></span>
                                <span>Completed</span>
                            </div>

                            <div class="legend-item">
                                <span class="legend-dot" style="background:#9a9a9a;"></span>
                                <span>Cancelled</span>
                            </div>
                        </div>

                        <div class="order-total-box">
                            <div class="order-total-left">
                                <i class="bi bi-card-checklist"></i>
                                <span>Total Orders</span>
                            </div>

                            <div class="order-total-number">
                                {{ $totalOrders }}
                            </div>
                        </div>

                        <div class="pipeline-stats pipeline-stats-compact">
                            <div class="pipeline-stat stat-pending">
                                <i class="bi bi-hourglass-split"></i>
                                <div>
                                    <div class="pipeline-stat-title">Pending</div>
                                    <div class="pipeline-stat-number">{{ $pendingApproval }}</div>
                                </div>
                            </div>

                            <div class="pipeline-stat stat-dp">
                                <i class="bi bi-cash-coin"></i>
                                <div>
                                    <div class="pipeline-stat-title">DP Paid</div>
                                    <div class="pipeline-stat-number">{{ $dpPaid }}</div>
                                </div>
                            </div>

                            <div class="pipeline-stat stat-processed">
                                <i class="bi bi-gear"></i>
                                <div>
                                    <div class="pipeline-stat-title">Processed</div>
                                    <div class="pipeline-stat-number">{{ $processedOrders }}</div>
                                </div>
                            </div>

                            <div class="pipeline-stat stat-assigned">
                                <i class="bi bi-person-check"></i>
                                <div>
                                    <div class="pipeline-stat-title">Assigned</div>
                                    <div class="pipeline-stat-number">{{ $assignedOrders }}</div>
                                </div>
                            </div>

                            <div class="pipeline-stat stat-paid">
                                <i class="bi bi-credit-card"></i>
                                <div>
                                    <div class="pipeline-stat-title">Fully Paid</div>
                                    <div class="pipeline-stat-number">{{ $fullyPaid }}</div>
                                </div>
                            </div>

                            <div class="pipeline-stat stat-rent">
                                <i class="bi bi-box-arrow-right"></i>
                                <div>
                                    <div class="pipeline-stat-title">On Rent</div>
                                    <div class="pipeline-stat-number">{{ $onRentOrders }}</div>
                                </div>
                            </div>

                            <div class="pipeline-stat stat-return">
                                <i class="bi bi-clipboard-check"></i>
                                <div>
                                    <div class="pipeline-stat-title">Return</div>
                                    <div class="pipeline-stat-number">{{ $returnCheckingOrders }}</div>
                                </div>
                            </div>

                            <div class="pipeline-stat stat-completed">
                                <i class="bi bi-check-circle"></i>
                                <div>
                                    <div class="pipeline-stat-title">Completed</div>
                                    <div class="pipeline-stat-number">{{ $completed }}</div>
                                </div>
                            </div>

                            <div class="pipeline-stat stat-cancelled">
                                <i class="bi bi-x-circle"></i>
                                <div>
                                    <div class="pipeline-stat-title">Cancelled</div>
                                    <div class="pipeline-stat-number">{{ $cancelledOrders }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="dashboard-card trend-card">
                        <h4 class="card-title-purple">Order Trend</h4>

                        <div class="trend-chart-wrap">
                            <canvas id="orderTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dateRangeInput = document.getElementById('marketingDateRange');
                const openCalendarButton = document.getElementById('openMarketingCalendar');
                const pickupDateInput = document.getElementById('marketingPickupDate');
                const returnDateInput = document.getElementById('marketingReturnDate');

                if (dateRangeInput) {
                    const dateRangePicker = flatpickr(dateRangeInput, {
                        mode: "range",
                        dateFormat: "Y-m-d",
                        altInput: false,
                        allowInput: false,
                        disableMobile: true,
                        onChange: function(selectedDates, dateStr, instance) {
                            if (selectedDates.length === 2) {
                                pickupDateInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                                returnDateInput.value = instance.formatDate(selectedDates[1], "Y-m-d");
                            }
                        }
                    });

                    openCalendarButton.addEventListener('click', function() {
                        dateRangePicker.open();
                    });

                    dateRangeInput.addEventListener('click', function() {
                        dateRangePicker.open();
                    });
                }

                const pipelineData = @json(array_values($pipelineData));
                const orderTrendData = @json($orderTrend);


                new Chart(document.getElementById('pipelineChart'), {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Pending',
                            'DP Paid',
                            'Processed',
                            'Assigned',
                            'Fully Paid',
                            'On Rent',
                            'Return Check',
                            'Completed',
                            'Cancelled'
                        ],
                        datasets: [{
                            data: pipelineData,
                            backgroundColor: [
                                '#8b73ff',
                                '#ff8a83',
                                '#39bfd3',
                                '#ffb04a',
                                '#5b8def',
                                '#20b486',
                                '#b56cff',
                                '#46c46f',
                                '#9a9a9a'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '58%',
                        layout: {
                            padding: 4
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                new Chart(document.getElementById('orderTrendChart'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agst', 'Sept', 'Okt', 'Nov',
                            'Des'
                        ],
                        datasets: [{
                            label: 'Jumlah Order',
                            data: orderTrendData,
                            borderColor: '#8b73ff',
                            backgroundColor: 'rgba(139, 115, 255, 0.15)',
                            pointBackgroundColor: '#8b73ff',
                            pointRadius: 4,
                            tension: 0.25,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endsection
