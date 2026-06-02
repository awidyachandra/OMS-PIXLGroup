<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice Lunas</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #3b2a6f;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 13px;
            color: #555;
        }

        .box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #3b2a6f;
            color: white;
            padding: 9px;
            text-align: left;
        }

        td {
            border-bottom: 1px solid #ddd;
            padding: 9px;
        }

        .right {
            text-align: right;
        }

        .summary td {
            border: none;
            padding: 6px;
        }

        .paid {
            color: #198754;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="title">INVOICE LUNAS</div>
    <div class="subtitle">PIXL Rent & PIXL Moment</div>
</div>

<div class="box">
    <table>
        <tr>
            <td><strong>No. Order</strong></td>
            <td>#{{ $order->id }}</td>
            <td><strong>Tanggal Pelunasan</strong></td>
            <td>{{ \Carbon\Carbon::parse($paymentDate)->format('d M Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Customer</strong></td>
            <td>{{ $order->customer->name ?? '-' }}</td>
            <td><strong>Status Pembayaran</strong></td>
            <td>{{ strtoupper($order->payment_status) }}</td>
        </tr>
        <tr>
            <td><strong>Event</strong></td>
            <td>{{ $order->event ?? '-' }}</td>
            <td><strong>Tanggal Sewa</strong></td>
            <td>
                {{ $order->start_date ? \Carbon\Carbon::parse($order->start_date)->format('d M Y') : '-' }}
                -
                {{ $order->end_date ? \Carbon\Carbon::parse($order->end_date)->format('d M Y') : '-' }}
            </td>
        </tr>
    </table>
</div>

<table>
    <thead>
        <tr>
            <th>Produk</th>
            <th class="right">Qty</th>
            <th class="right">Harga</th>
            <th class="right">Subtotal</th>
        </tr>
    </thead>

    <tbody>
    @foreach($order->details as $detail)
        <tr>
            <td>{{ $detail->product_type }}</td>
            <td class="right">{{ $detail->qty }}</td>
            <td class="right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<br>

<table class="summary">
    <tr>
        <td class="right"><strong>Total Tagihan</strong></td>
        <td class="right">Rp {{ number_format($order->final_price, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="right"><strong>DP Sebelumnya</strong></td>
        <td class="right">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="right"><strong>Pelunasan Dibayarkan</strong></td>
        <td class="right">Rp {{ number_format($settlementAmount, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="right"><strong>Total Dibayar</strong></td>
        <td class="right paid">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="right"><strong>Sisa Tagihan</strong></td>
        <td class="right paid">Rp 0</td>
    </tr>
</table>

<br><br>

<p>
    Invoice ini merupakan bukti bahwa Order #{{ $order->id }} telah lunas.
</p>

</body>
</html>