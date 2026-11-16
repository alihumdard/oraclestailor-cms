<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 40px auto; color: #222; }
        h1 { font-size: 20px; margin-bottom: 0; }
        .muted { color: #666; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 8px 0; border-bottom: 1px solid #eee; font-size: 14px; }
        td:last-child { text-align: right; }
        .total-row td { font-weight: bold; border-bottom: none; padding-top: 14px; }
        .print-btn { margin-top: 24px; padding: 8px 16px; cursor: pointer; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <h1>{{ $businessSetting->business_name }}</h1>
    <p class="muted">{{ $businessSetting->full_address }}@if($businessSetting->phone) · {{ $businessSetting->phone }}@endif</p>

    <p><strong>Receipt:</strong> SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}<br>
    <strong>Date:</strong> {{ now()->format('d M Y') }}<br>
    <strong>Customer:</strong> {{ $order->customer->name }} ({{ $order->customer->phone }})</p>

    <table>
        <tr><td>{{ $order->garment }} — {{ $order->fabric }}</td><td>PKR {{ number_format($order->total) }}</td></tr>
        <tr><td>Advance paid</td><td>PKR {{ number_format($order->advance) }}</td></tr>
        <tr class="total-row"><td>Balance due</td><td>PKR {{ number_format($order->balance) }}</td></tr>
    </table>

    <button class="print-btn" onclick="window.print()">Print receipt</button>
</body>
</html>
