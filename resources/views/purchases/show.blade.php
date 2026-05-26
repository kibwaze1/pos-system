@extends('layouts.app')
@section('title', 'Purchase Order #' . $purchase->purchase_no)
@section('content')
<div class="card">
    <div class="card-header">Purchase Order Details</div>
    <div class="card-body">
        <p><strong>PO Number:</strong> {{ $purchase->purchase_no }}</p>
        <p><strong>Supplier:</strong> {{ $purchase->supplier->name }}</p>
        <p><strong>Date:</strong> {{ $purchase->created_at->format('Y-m-d H:i') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($purchase->status) }}</p>
        <p><strong>Total Amount:</strong> {{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($purchase->total_amount, 2) }}</p>
        <p><strong>Paid:</strong> {{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($purchase->paid_amount, 2) }}</p>
        <p><strong>Balance:</strong> {{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($purchase->total_amount - $purchase->paid_amount, 2) }}</p>
        <hr>
        <h5>Items</h5>
        <table class="table table-sm">
            <thead><tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($purchase->items as $item)
                <tr><td>{{ $item->product->name }}</td><td>{{ $item->quantity }}</td><td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->price, 2) }}</td><td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->total, 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection
