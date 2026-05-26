@extends('layouts.app')
@section('title', 'Inventory Report')
@section('content')
<div class="card">
    <div class="card-header">Inventory Report</div>
    <div class="card-body">
        @if($lowStock->count())
        <div class="alert alert-warning">Low stock products: {{ $lowStock->count() }}</div>
        @endif
        <table class="table table-bordered" id="inventoryReportTable">
            <thead>
                <tr><th>Product</th><th>SKU</th><th>Category</th><th>Stock</th><th>Threshold</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                @php $status = $p->stock_quantity <= $p->low_stock_threshold ? ($p->stock_quantity==0 ? 'Out of Stock' : 'Low Stock') : 'In Stock'; @endphp
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->category->name ?? 'N/A' }}</td>
                    <td>{{ $p->stock_quantity }}</td>
                    <td>{{ $p->low_stock_threshold }}</td>
                    <td>{{ $status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>$(document).ready(function(){$('#inventoryReportTable').DataTable();});</script>
@endpush
