@extends('layouts.app')
@section('title', 'Reports Dashboard')
@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5>Total Sales</h5>
                <p class="display-6">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($totalSales, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5>Total Expenses</h5>
                <p class="display-6">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($totalExpenses, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5>Net Profit</h5>
                <p class="display-6">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($netProfit, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5>Products</h5>
                <p class="display-6">{{ $totalProducts }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Monthly Sales</div>
            <div class="card-body">
                <canvas id="monthlyChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Quick Reports</div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item"><a href="{{ route('reports.sales') }}">Sales Report</a></li>
                    <li class="list-group-item"><a href="{{ route('reports.profit') }}">Profit & Loss</a></li>
                    <li class="list-group-item"><a href="{{ route('reports.inventory') }}">Inventory Report</a></li>
                    <li class="list-group-item"><a href="{{ route('reports.customers') }}">Customer Report</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const months = @json($monthlySales->pluck('month'));
    const totals = @json($monthlySales->pluck('total'));
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Sales ({{ \App\Models\Setting::get("currency_symbol", "$") }})',
                data: totals,
                backgroundColor: '#0d6efd'
            }]
        }
    });
</script>
@endpush
