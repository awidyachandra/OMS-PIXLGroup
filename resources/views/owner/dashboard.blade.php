@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --purple: #34215f;
        --border: #cfcfcf;
        --shadow: 0 2px 7px rgba(0,0,0,0.18);
    }



    .owner-section {
        background: #fff;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 14px;
        margin-bottom: 16px;
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--purple);
        margin-bottom: 14px;
        line-height: 1.1;
    }

    .dashboard-card {
        background: #fff;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        height: 100%;
        overflow: hidden;
    }

    .card-inner {
        padding: 12px 14px;
        height: 100%;
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--purple);
        margin-bottom: 10px;
        line-height: 1.2;
    }

    canvas {
        max-width: 100%;
    }

    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */

    .order-left-card {
        min-height: 520px;
    }

    .trend-chart-area {
        height: 330px;
        position: relative;
    }

    .pic-mini-section {
        margin-top: 14px;
    }

    .pic-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .pic-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--purple);
    }

    .pic-total-badge {
        background: #ececf3;
        color: var(--purple);
        font-size: 12px;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 14px;
        white-space: nowrap;
    }

    .pic-table-wrap {
        border: 1px solid #d8d8d8;
    }

    .pic-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pic-table th,
    .pic-table td {
        padding: 7px 10px;
        border-bottom: 1px solid #e2e2e2;
        font-size: 12px;
    }

    .pic-table th {
        background: var(--purple);
        color: #fff;
        font-weight: 600;
    }

    .pic-table td {
        background: #fff;
        color: #333;
    }

    .pic-table td:last-child,
    .pic-table th:last-child {
        text-align: center;
        width: 90px;
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER PIPELINE
    |--------------------------------------------------------------------------
    */

    .pipeline-card {
        min-height: 520px;
    }

    .pipeline-chart-area {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .pipeline-chart-wrap {
        position: relative;
        width: 165px;
        height: 165px;
    }

    .donut-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #111;
        font-size: 20px;
        font-weight: 700;
        pointer-events: none;
    }

    .pipeline-legend-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 5px 8px;
        margin: 10px 0;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
        font-size: 10px;
        color: #555;
        line-height: 1.15;
    }

    .legend-item span:last-child {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex: 0 0 8px;
    }

    .order-total-box {
        background: #ececf3;
        color: var(--purple);
        padding: 9px 11px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 8px 0 10px;
        gap: 8px;
    }

    .order-total-left {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        font-size: 13px;
        font-weight: 700;
    }

    .order-total-left i {
        font-size: 18px;
        flex: 0 0 auto;
    }

    .order-total-left span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .order-total-number {
        font-size: 19px;
        font-weight: 700;
        white-space: nowrap;
    }

    .pipeline-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 6px;
    }

    .pipeline-stat {
        min-height: 52px;
        padding: 7px;
        display: grid;
        grid-template-columns: 18px minmax(0, 1fr);
        align-items: center;
        column-gap: 6px;
        overflow: hidden;
    }

    .pipeline-stat i {
        font-size: 17px;
        text-align: center;
    }

    .pipeline-stat > div {
        min-width: 0;
    }

    .pipeline-stat-title {
        font-size: 9.5px;
        line-height: 1.1;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pipeline-stat-number {
        font-size: 17px;
        font-weight: 700;
        line-height: 1;
        margin-top: 4px;
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

    /*
    |--------------------------------------------------------------------------
    | UNITS
    |--------------------------------------------------------------------------
    */

    .units-card {
        min-height: 410px;
    }

    .units-stack {
        display: grid;
        grid-template-columns: 1fr;
        border: 1px solid #d7d7d7;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .unit-box {
        padding: 10px 12px;
        display: grid;
        grid-template-columns: 34px minmax(0, 1fr);
        gap: 10px;
        align-items: center;
        min-height: 68px;
        color: var(--purple);
    }

    .unit-box i {
        font-size: 24px;
        color: var(--purple);
        text-align: center;
    }

    .unit-total {
        background: #ececf3;
    }

    .unit-available {
        background: #eaf8e8;
    }

    .unit-rented {
        background: #f0f3fa;
    }

    .unit-maintenance {
        background: #fffbd7;
    }

    .unit-title {
        font-size: 12px;
        font-weight: 700;
        line-height: 1.1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .unit-number {
        font-size: 21px;
        font-weight: 700;
        margin-top: 4px;
        line-height: 1;
    }

    .category-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--purple);
        margin: 10px 0 7px;
    }

    .category-chip-wrap {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px;
    }

    .category-chip {
        border: 1px solid #d5cfe3;
        background: #fff;
        color: var(--purple);
        padding: 6px 8px;
        font-size: 11px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .category-chip span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .category-chip b {
        flex: 0 0 auto;
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMERS
    |--------------------------------------------------------------------------
    */

    .customer-section-card {
        min-height: 410px;
    }

    .customer-summary-box {
        background: #fff;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 12px 14px;
        color: var(--purple);
        min-height: 74px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .customer-summary-label {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
    }

    .customer-summary-value {
        font-size: 24px;
        font-weight: 700;
        white-space: nowrap;
    }

    .segments-card {
        background: #fff;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 12px 14px;
    }

    .segments-layout {
        display: grid;
        grid-template-columns: 210px minmax(0, 1fr);
        gap: 12px;
        align-items: center;
    }

    .segments-chart-box {
        height: 180px;
        position: relative;
    }

    .segments-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--purple);
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .segments-legend {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px 8px;
        margin-bottom: 10px;
    }

    .segments-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .segments-table th,
    .segments-table td {
        border: 1px solid #cfc7dd;
        padding: 7px 5px;
        font-size: 10px;
        text-align: center;
        word-break: break-word;
        line-height: 1.15;
    }

    .segments-table th {
        background: var(--purple);
        color: #fff;
        font-weight: 500;
    }

    .segments-table td {
        background: #fff;
        color: #333;
        font-weight: 600;
    }

    @media (max-width: 1200px) {
        .pipeline-legend-grid,
        .pipeline-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .segments-layout {
            grid-template-columns: 1fr;
        }

        .segments-chart-box {
            height: 170px;
        }
    }

    @media (max-width: 992px) {
        .owner-page {
            padding: 14px;
        }

        .order-left-card,
        .pipeline-card,
        .units-card,
        .customer-section-card {
            min-height: auto;
        }

        .category-chip-wrap {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .pipeline-legend-grid,
        .pipeline-stats,
        .category-chip-wrap,
        .segments-legend {
            grid-template-columns: 1fr;
        }

        .pipeline-chart-wrap {
            width: 150px;
            height: 150px;
        }

        .trend-chart-area {
            height: 280px;
        }
    }
</style>

<div class="owner-page">

    {{-- ========================= ORDERS SECTION ========================= --}}
    <div class="owner-section">
        <div class="section-title">Orders</div>

        <div class="row g-3 align-items-stretch">

            {{-- ORDER TREND + PIC --}}
            <div class="col-lg-7 d-flex">
                <div class="dashboard-card order-left-card w-100">
                    <div class="card-inner">

                        <div class="card-title">Order Trend</div>

                        <div class="trend-chart-area">
                            <canvas id="ownerOrderTrendChart"></canvas>
                        </div>

                        <div class="pic-mini-section">
                            <div class="pic-header-row">
                                <div class="pic-title">PIC Orders</div>
                                <div class="pic-total-badge">
                                    Total PIC: {{ $totalPic }}
                                </div>
                            </div>

                            <div class="pic-table-wrap">
                                <table class="pic-table">
                                    <thead>
                                        <tr>
                                            <th>Nama PIC</th>
                                            <th>Jumlah Order</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($picOrders as $pic)
                                            <tr>
                                                <td>{{ $pic->pic }}</td>
                                                <td>{{ $pic->total_orders }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2">Belum ada data PIC</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ORDER PIPELINE --}}
            <div class="col-lg-5 d-flex">
                <div class="dashboard-card pipeline-card w-100">
                    <div class="card-inner">
                        <div class="card-title">Order Pipeline</div>

                        <div class="pipeline-chart-area">
                            <div class="pipeline-chart-wrap">
                                <canvas id="ownerPipelineChart"></canvas>
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
                                <span>Return</span>
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

                        <div class="pipeline-stats">
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
            </div>

        </div>
    </div>

    {{-- ========================= UNITS + CUSTOMERS SECTION ========================= --}}
    <div class="row g-3 align-items-stretch">

        {{-- UNITS --}}
        <div class="col-lg-4 d-flex">
            <div class="owner-section w-100 units-card">
                <div class="section-title mb-3">Units</div>

                <div class="units-stack">
                    <div class="unit-box unit-total">
                        <i class="bi bi-back"></i>
                        <div>
                            <div class="unit-title">Total Units</div>
                            <div class="unit-number">{{ $totalUnits }}</div>
                        </div>
                    </div>

                    <div class="unit-box unit-available">
                        <i class="bi bi-calendar-check"></i>
                        <div>
                            <div class="unit-title">Available</div>
                            <div class="unit-number">{{ $availableUnits }}</div>
                        </div>
                    </div>

                    <div class="unit-box unit-rented">
                        <i class="bi bi-box-arrow-left"></i>
                        <div>
                            <div class="unit-title">Rented</div>
                            <div class="unit-number">{{ $rentedUnits }}</div>
                        </div>
                    </div>

                    <div class="unit-box unit-maintenance">
                        <i class="bi bi-tools"></i>
                        <div>
                            <div class="unit-title">Maintenance</div>
                            <div class="unit-number">{{ $maintenanceUnits }}</div>
                        </div>
                    </div>
                </div>

                <div class="category-title">Total Unit per Kategori</div>

                <div class="category-chip-wrap">
                    @forelse ($categoryStocks as $cat)
                        <div class="category-chip">
                            <span>{{ $cat->kategori }}</span>
                            <b>{{ $cat->total }}</b>
                        </div>
                    @empty
                        <div class="category-chip">
                            <span>Belum ada kategori</span>
                            <b>0</b>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- CUSTOMERS --}}
        <div class="col-lg-8 d-flex">
            <div class="owner-section w-100 customer-section-card">
                <div class="section-title mb-3">Customers</div>

                <div class="customer-summary-box">
                    <div class="customer-summary-label">
                        Total Customer
                    </div>

                    <div class="customer-summary-value">
                        {{ $totalCustomers }}
                    </div>
                </div>

                <div class="segments-card">
                    <div class="segments-layout">
                        <div class="segments-chart-box">
                            <canvas id="ownerCustomerSegmentChart"></canvas>
                        </div>

                        <div>
                            <div class="segments-title">Customer Segments</div>

                            <div class="segments-legend">
                                <div class="legend-item">
                                    <span class="legend-dot" style="background:#7b68ee;"></span>
                                    <span>Umum</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-dot" style="background:#f28b82;"></span>
                                    <span>Event Organizer</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-dot" style="background:#3fbad9;"></span>
                                    <span>Wedding Organizer</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-dot" style="background:#f9ad4b;"></span>
                                    <span>BEM Fakultas</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-dot" style="background:#4f7ff0;"></span>
                                    <span>BEM Universitas</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-dot" style="background:#6cc98b;"></span>
                                    <span>HIMA Jurusan</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-dot" style="background:#8fd19e;"></span>
                                    <span>OSIS</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="segments-table">
                        <thead>
                            <tr>
                                <th>Umum</th>
                                <th>Event Organizer</th>
                                <th>Wedding Organizer</th>
                                <th>BEM Fakultas</th>
                                <th>BEM Universitas</th>
                                <th>HIMA Jurusan</th>
                                <th>OSIS</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>{{ $customerSegments['Umum'] }}</td>
                                <td>{{ $customerSegments['Event Organizer'] }}</td>
                                <td>{{ $customerSegments['Wedding Organizer'] }}</td>
                                <td>{{ $customerSegments['BEM Fakultas'] }}</td>
                                <td>{{ $customerSegments['BEM Universitas'] }}</td>
                                <td>{{ $customerSegments['HIMA Jurusan'] }}</td>
                                <td>{{ $customerSegments['OSIS'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const orderTrendData = @json($orderTrend);
        const pipelineData = @json(array_values($pipelineData));
        const customerSegmentData = @json(array_values($customerSegments));

        new Chart(document.getElementById('ownerOrderTrendChart'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agst', 'Sept', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Order',
                    data: orderTrendData,
                    borderColor: '#8b73ff',
                    backgroundColor: 'rgba(139, 115, 255, 0.12)',
                    pointBackgroundColor: '#8b73ff',
                    pointRadius: 3,
                    borderWidth: 2,
                    tension: 0.25,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 6
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 9,
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('ownerPipelineChart'), {
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
                    padding: 3
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        new Chart(document.getElementById('ownerCustomerSegmentChart'), {
            type: 'pie',
            data: {
                labels: [
                    'Umum',
                    'Event Organizer',
                    'Wedding Organizer',
                    'BEM Fakultas',
                    'BEM Universitas',
                    'HIMA Jurusan',
                    'OSIS'
                ],
                datasets: [{
                    data: customerSegmentData,
                    backgroundColor: [
                        '#7b68ee',
                        '#f28b82',
                        '#3fbad9',
                        '#f9ad4b',
                        '#4f7ff0',
                        '#6cc98b',
                        '#8fd19e'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
    });
</script>

@endsection