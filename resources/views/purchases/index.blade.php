@extends('layouts.app')
@section('title', 'Purchases')
@section('content')
<div class="card">
    <div class="card-header">
        Purchase Orders
        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm float-end">New Purchase</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="purchasesTable">
            <thead>
                <tr><th>PO #</th><th>Supplier</th><th>Total Amount</th><th>Paid</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($purchases as $p)
                <tr>
                    <td>{{ $p->purchase_no }}</td>
                    <td>{{ $p->supplier->name }}</td>
                    <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($p->total_amount, 2) }}</td>
                    <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($p->paid_amount, 2) }}</td>
                    <td>{{ ucfirst($p->status) }}</td>
                    <td>{{ $p->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-info">View</a>
                    </td>
                </table>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>$(document).ready(function(){$('#purchasesTable').DataTable();});</script>
@endpush
