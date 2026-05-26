<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ \App\Models\Setting::get('store_name', 'POS System') }}</h2>
        <p>Sales Report – Products Sold</p>
        <p>Period: {{ $start }} to {{ $end }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th>Sale Date</th>
                <th>Invoice No</th>
            </tr>
        </thead>
        <tbody>
            @foreach($saleItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->price, 2) }}</td>
                <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->total, 2) }}</td>
                <td>{{ $item->sale->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $item->sale->invoice_no }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="3" align="right"><strong>Total Sales</strong></td><td colspan="2"><strong>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($total, 2) }}</strong></td><td></td></tr>
        </tfoot>
    </table>

    <div class="footer">
        Generated on {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
