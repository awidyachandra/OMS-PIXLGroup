<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice PIXL Rent</title>

    <style>
        @page {
            margin: 32px 38px 28px 38px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 100%;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            vertical-align: top;
        }

        .red-line {
            height: 9px;
            background: #ff0b0b;
            width: 100%;
            margin-bottom: 22px;
        }

        .header-area {
            position: relative;
            height: 92px;
            width: 100%;
        }

        .logo-box {
            position: absolute;
            top: 0;
            width: 90px;
            text-align: center;
        }

        .logo-img {
            width: 62px;
            height: auto;
        }

        .invoice-title {
            position: absolute;
            left: 0;
            right: 0;
            top: 47px;
            text-align: center;
            font-size: 23px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .company-box {
            position: absolute;
            right: 0;
            top: 0;
            width: 250px;
            text-align: right;
            font-size: 9px;
            line-height: 1.6;
        }

        .company-name {
            font-weight: bold;
            font-size: 9px;
        }

        .main-separator {
            border-bottom: 2px solid #1262ff;
        }

        .info-table {
            margin-top: 43px;
        }

        .info-table td {
            border: none;
            padding: 0;
        }

        .bill-box {
            width: 56%;
            padding-left: 0;
            padding-right: 60px;
        }

        .right-info-box {
            width: 44%;
            padding-left: 0;
            padding-right: 0;
        }

        .section-line {
            border-top: 2px solid #1262ff;
            padding-top: 8px;
        }

        .section-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 7px;
        }

        .bill-text {
            font-size: 9px;
            line-height: 1.65;
        }

        .invoice-meta {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-meta td {
            border: none;
            padding: 4px 0 19px 0;
            font-size: 10px;
        }

        .meta-label {
            font-weight: bold;
            width: 50%;
        }

        .meta-value {
            text-align: right;
            color: #0057d8;
            font-weight: bold;
        }

        .item-table {
            margin-top: 52px;
        }

        .item-table th {
            border: none;
            border-bottom: 2px solid #1262ff;
            padding: 0 4px 7px 4px;
            font-size: 10px;
            font-weight: bold;
            text-align: left;
        }

        .item-table td {
            border: none;
            padding: 8px 4px;
            font-size: 9px;
        }

        .item-no {
            width: 4%;
            text-align: left;
            padding-left: 9px !important;
        }

        .item-desc {
            width: 56%;
        }

        .item-qty {
            width: 10%;
            text-align: center !important;
        }

        .item-price {
            width: 15%;
            text-align: right !important;
        }

        .item-total {
            width: 15%;
            text-align: right !important;
        }

        .bottom-table {
            margin-top: 315px;
        }

        .bottom-table td {
            border: none;
            padding: 0;
        }

        .notes-box {
            width: 56%;
            padding-right: 60px;
        }

        .summary-box {
            width: 44%;
        }

        .notes-line {
            border-top: 2px solid #1262ff;
            padding-top: 8px;
        }

        .notes-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .notes-text {
            font-size: 9px;
            line-height: 1.25;
        }

        .notes-text strong {
            font-weight: bold;
        }

        .summary-line {
            border-top: 2px solid #1262ff;
            padding-top: 7px;
        }

        .summary-table td {
            border: none;
            padding: 4px 0;
            font-size: 9px;
        }

        .summary-label {
            font-weight: bold;
            text-align: left;
        }

        .summary-value {
            text-align: right;
        }

        .summary-bottom-line {
            border-bottom: 2px solid #1262ff;
            padding-bottom: 13px !important;
        }

        .total-table {
            margin-top: 34px;
        }

        .total-table td {
            border: none;
            padding: 0;
            font-size: 12px;
            font-weight: bold;
        }

        .total-label {
            text-align: left;
        }

        .total-value {
            text-align: right;
            color: #0057d8;
        }
    </style>
</head>

<body>

    @php
        $invoiceNumber = str_pad($order->id, 4, '0', STR_PAD_LEFT);

        $invoiceDate = $order->created_at
            ? \Carbon\Carbon::parse($order->created_at)->format('j/n/Y')
            : now()->format('j/n/Y');

        $eventDate = $order->date
            ? \Carbon\Carbon::parse($order->date)->translatedFormat('j F Y')
            : '-';

        $pickupDate = $order->pickup_date
            ? \Carbon\Carbon::parse($order->pickup_date)->translatedFormat('j F Y')
            : '-';

        $returnDate = $order->return_date
            ? \Carbon\Carbon::parse($order->return_date)->translatedFormat('j F Y')
            : '-';

        $subtotal = $order->total_price ?? 0;
        $discountAmount = $order->discount ?? 0;
        $finalPrice = $order->final_price ?? ($subtotal - $discountAmount);

        $discountPercent = 0;

        if ($subtotal > 0 && $discountAmount > 0) {
            $discountPercent = ($discountAmount / $subtotal) * 100;
        }

        $discountPercentText = rtrim(rtrim(number_format($discountPercent, 2, '.', ''), '0'), '.');

        /*
        Pastikan logo ada di:
        public/images/pixl_logo.png
        */
        $logoPath = public_path('images/pixl_logo.png');
    @endphp

    <div class="page">

        <div class="red-line"></div>

        {{-- HEADER --}}
        <div class="header-area">

            <div class="logo-box">
                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" class="logo-img">
                @else
                    <div style="width:60px; height:60px; border:1px solid #999; margin:auto; font-size:8px; line-height:60px;">
                        LOGO
                    </div>
                @endif
            </div>

            <div class="invoice-title">
                INVOICE
            </div>

            <div class="company-box">
                <div class="company-name">PIXL RENT</div>
                <div>Jl. Raya Pandugo No.70</div>
                <div>Penjaringan Sari, Kec. Rungkut, Surabaya</div>
                <div>60296</div>
                <div>pixlgroupinc@gmail.com</div>
            </div>

        </div>

        <div class="main-separator"></div>

        {{-- BILL TO + INVOICE INFO --}}
        <table class="info-table">
            <tr>
                <td class="bill-box">
                    <div class="section-line">
                        <div class="section-title">BILL TO</div>

                        <div class="bill-text">
                            {{ $order->customer->name }}

                            @if ($order->event)
                                ({{ $order->event }})
                            @endif

                            <br>

                            {{ $order->address ?? '-' }}<br>

                            @if ($order->customer->agency)
                                {{ $order->customer->agency }}<br>
                            @endif

                            {{ $order->customer->phone ?? '-' }}
                        </div>
                    </div>
                </td>

                <td class="right-info-box">
                    <div class="section-line">
                        <table class="invoice-meta">
                            <tr>
                                <td class="meta-label">INVOICE #</td>
                                <td class="meta-value">{{ $invoiceNumber }}</td>
                            </tr>

                            <tr>
                                <td class="meta-label">DATE</td>
                                <td class="meta-value">{{ $invoiceDate }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ITEM TABLE --}}
        <table class="item-table">
            <thead>
                <tr>
                    <th class="item-no"></th>
                    <th class="item-desc">DESCRIPTION</th>
                    <th class="item-qty">QTY</th>
                    <th class="item-price">PRICE</th>
                    <th class="item-total">TOTAL</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($order->details as $detail)
                    <tr>
                        <td class="item-no">{{ $loop->iteration }}</td>

                        <td class="item-desc">
                            {{ $detail->product_type }}

                            @if ($order->date)
                                ({{ $eventDate }})
                            @endif
                        </td>

                        <td class="item-qty">
                            {{ $detail->qty }}
                        </td>

                        <td class="item-price">
                            Rp{{ number_format($detail->unit_price, 0, ',', '.') }}
                        </td>

                        <td class="item-total">
                            Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- BOTTOM --}}
        <table class="bottom-table">
            <tr>
                <td class="notes-box">
                    <div class="notes-line">
                        <div class="notes-title">NOTES</div>

                        <div class="notes-text">
                            <strong>TANGGAL PENGAMBILAN :</strong> {{ $pickupDate }}<br>
                            <strong>TANGGAL PENGEMBALIAN :</strong> {{ $returnDate }}<br>
                            <strong>PAYMENT DP (MAX) :</strong> -<br>
                            <strong>PAYMENT PELUNASAN (MAX) :</strong> {{ $pickupDate }}<br>
                            <strong>PEMBAYARAN:</strong><br>
                            <strong>SEABANK&nbsp;&nbsp;901435613276 (Amanda Soraya)</strong><br>
                            <strong>BCA DIGITAL : 090171737528 (Amanda Soraya)</strong>
                        </div>
                    </div>
                </td>

                <td class="summary-box">
                    <div class="summary-line">
                        <table class="summary-table">
                            <tr>
                                <td class="summary-label">SUB TOTAL</td>
                                <td class="summary-value">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>

                            <tr>
                                <td colspan="2" style="height: 12px;"></td>
                            </tr>

                            <tr>
                                <td class="summary-label">TAX (%)</td>
                                <td class="summary-value">0%</td>
                            </tr>

                            <tr>
                                <td class="summary-label summary-bottom-line">DISCOUNT (%)</td>
                                <td class="summary-value summary-bottom-line">{{ $discountPercentText }}%</td>
                            </tr>
                        </table>

                        <table class="total-table">
                            <tr>
                                <td class="total-label">TOTAL</td>
                                <td class="total-value">Rp{{ number_format($finalPrice, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

    </div>

</body>

</html>