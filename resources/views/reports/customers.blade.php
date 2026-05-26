@extends('layouts.app')
@section('title', 'Customer Report')
@section('content')
<div class="card">
    <div class="card-header">Customer Purchase Report</div>
    <div class="card-body">
        <table class="table table-bordered" id="customerReportTable">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Total Spent</th><th>Balance</th></tr>
            </thead>
            <tbody>
                @foreach($customers as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ $c->sales_count }}</td>
                    <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($c->sales_sum_total, 2) }}</td>
                    <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($c->balance, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>$(document).ready(function(){$('#customerReportTable').DataTable();});</script>
@endpush
