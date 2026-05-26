@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Today's Sales</h5>
                <p class="card-text display-6">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($todaySales, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Monthly Sales</h5>
                <p class="card-text display-6">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($monthSales, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Low Stock Alerts</h5>
                <p class="card-text display-6">{{ $lowStockProducts->count() }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title">Out of Stock</h5>
                <p class="card-text display-6">{{ $outOfStockProducts }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">Last 7 Days Sales</div>
            <div class="card-body">
                <canvas id="salesChart" height="300"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Top Selling Products</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr><th>Product</th><th>Units Sold</th></tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $p)
                        <td><td>{{ $p->name }}</td><td>{{ $p->total_sold }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">Low Stock Products</div>
            <div class="card-body">
                @if($lowStockProducts->count())
                    <ul class="list-group">
                        @foreach($lowStockProducts as $p)
                        <li class="list-group-item d-flex justify-content-between">
                            {{ $p->name }}
                            <span class="badge bg-warning">Stock: {{ $p->stock_quantity }}</span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p>All products have sufficient stock.</p>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header">Recent Transactions</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr><th>Invoice</th><th>Amount</th><th>Time</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($sale->total, 2) }}</td>
                            <td>{{ $sale->created_at->format('H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Sales ({{ \App\Models\Setting::get("currency_symbol", "$") }})',
                data: @json($chartData),
                borderColor: 'rgb(13, 110, 253)',
                tension: 0.1
            }]
        }
    });
</script>
@endpush
