@extends('layouts.app')
@section('title', 'Profit & Loss Report')
@section('content')
<div class="card">
    <div class="card-header">Profit & Loss Report</div>
    <div class="card-body">
        <!-- Date Filter Form -->
        <form method="GET" class="row mb-4">
            <div class="col-md-4">
                <label>Start Date</label>
                <input type="date" name="start" class="form-control" value="{{ $start }}">
            </div>
            <div class="col-md-4">
                <label>End Date</label>
                <input type="date" name="end" class="form-control" value="{{ $end }}">
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">Filter</button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5>Total Revenue</h5>
                        <p class="display-6">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($totalRevenue, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5>Total Cost (Purchase Price)</h5>
                        <p class="display-6">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($totalCost, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5>Gross Profit</h5>
                        <p class="display-6">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($grossProfit, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5>Expenses</h5>
                        <p class="display-6">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($totalExpenses, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h3>Net Profit (Revenue - Cost - Expenses)</h3>
                        <p class="display-4">{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($netProfit, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Profit Chart -->
        <div class="card mb-4">
            <div class="card-header">Daily Profit Trend</div>
            <div class="card-body">
                <canvas id="profitChart" height="300"></canvas>
            </div>
        </div>

        <!-- Profit by Product Table -->
        <div class="card">
            <div class="card-header">Profit by Product</div>
            <div class="card-body">
                <table class="table table-bordered" id="profitTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity Sold</th>
                            <th>Revenue</th>
                            <th>Cost</th>
                            <th>Profit</th>
                            <th>Margin %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profitByProduct as $name => $data)
                        <tr>
                            <td>{{ $name }}</td>
                            <td>{{ $data['quantity'] }}</td>
                            <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($data['revenue'], 2) }}</td>
                            <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($data['cost'], 2) }}</td>
                            <td class="{{ $data['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($data['profit'], 2) }}
                            </td>
                            <td>{{ number_format(($data['revenue'] > 0 ? ($data['profit'] / $data['revenue']) * 100 : 0), 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </td>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('#profitTable').DataTable();

        // Daily profit chart
        const ctx = document.getElementById('profitChart').getContext('2d');
        const dailyData = @json($dailyProfit);
        const labels = dailyData.map(item => item.date);
        const profits = dailyData.map(item => item.profit);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Profit ({{ \App\Models\Setting::get("currency_symbol", "$") }})',
                    data: profits,
                    borderColor: 'rgb(40, 167, 69)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Profit: ' + '{{ \App\Models\Setting::get("currency_symbol", "$") }}' + context.raw.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
