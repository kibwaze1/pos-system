<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice #{{ $sale->invoice_no }}</title>
    <style>
        /* Default screen style – not used for printing */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f0f0f0;
        }
        .invoice-box {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        /* THERMAL PRINT STYLES – 80mm width */
        @media print {
            @page {
                size: 80mm auto;      /* paper width 80mm, height auto */
                margin: 5mm;          /* minimal margins */
            }
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            .invoice-box {
                max-width: 100%;
                margin: 0;
                padding: 2mm;
                border: none;
                border-radius: 0;
                box-shadow: none;
                font-size: 10pt;      /* 10pt fits ~40 characters per line */
            }
            .header h2 {
                font-size: 14pt;
                margin: 2mm 0;
            }
            .header p, .header h3 {
                margin: 1mm 0;
                font-size: 9pt;
            }
            .table {
                width: 100%;
                border-collapse: collapse;
                margin: 2mm 0;
            }
            .table th, .table td {
                border: 1px solid #000;
                padding: 2px;
                font-size: 9pt;
                text-align: left;
            }
            .table th {
                background: #eee;
            }
            .no-print {
                display: none !important;
            }
            /* Avoid page breaks inside the invoice */
            .invoice-box, .header, .table, .total, .footer {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }

        /* Print button style */
        .btn {
            padding: 8px 15px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-size: 12px;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
    </style>
</head>
<body>
<div class="invoice-box">
    <div class="header">
        <h2>{{ \App\Models\Setting::get('store_name', 'POS System') }}</h2>
        <p>{{ \App\Models\Setting::get('store_address', '') }}</p>
        <p>Tel: {{ \App\Models\Setting::get('store_phone', '') }} | Email: {{ \App\Models\Setting::get('store_email', '') }}</p>
        <h3>INVOICE</h3>
        <p>Invoice No: {{ $sale->invoice_no }}</p>
        <p>Date: {{ $sale->created_at->format('Y-m-d H:i:s') }}</p>
        <p>Cashier: {{ $sale->user->name }}</p>
        <p>{{ \App\Models\Setting::get('receipt_header', 'Thank you for shopping!') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->price, 2) }}</td>
                <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="3" align="right"><strong>Subtotal</strong></td><td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($sale->subtotal, 2) }}</td></tr>
            @if($sale->discount > 0)
            <tr><td colspan="3" align="right"><strong>Discount</strong></td><td>-{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($sale->discount, 2) }}</td></tr>
            @endif
            @if($sale->tax > 0)
            <tr><td colspan="3" align="right"><strong>Tax</strong></td><td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($sale->tax, 2) }}</td></tr>
            @endif
            <tr class="total"><td colspan="3" align="right"><strong>TOTAL</strong></td><td><strong>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($sale->total, 2) }}</strong></td></tr>
            <tr><td colspan="3" align="right">Paid</td><td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($sale->paid, 2) }}</td></tr>
            <tr><td colspan="3" align="right">Change</td><td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($sale->change, 2) }}</td></tr>
        </tfoot>
    </table>

    <div class="header">
        <p>{{ \App\Models\Setting::get('receipt_footer', 'Thank you for your purchase!') }}</p>
        <button class="btn btn-primary no-print" onclick="window.print()">🖨️ Print Receipt</button>
        <button class="btn btn-info no-print" onclick="directPrint()">⚡ Direct Print (ESC/POS)</button>
        <button class="btn btn-secondary no-print" onclick="window.close()">❌ Close</button>
    </div>
</div>

<script>
function directPrint() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = '{{ route("print.direct", $sale) }}';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({})
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.error || 'Server error');
            }).catch(() => {
                throw new Error('HTTP error ' + response.status);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) alert('✅ Receipt sent to printer');
        else alert('❌ ' + (data.error || 'Print error'));
    })
    .catch(err => {
        console.error(err);
        alert('❌ Print request failed: ' + err.message);
    });
}
</script>
</body>
</html>
