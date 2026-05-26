@extends('layouts.app')
@section('title', 'Inventory')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Current Stock</h3>
        <a href="{{ route('inventory.add-stock') }}" class="btn btn-success btn-sm float-end">+ Add Stock</a>
    </div>
    <div class="card-body">
        @if($lowStockProducts->count() > 0)
        <div class="alert alert-warning">
            <strong>Low Stock Alert!</strong> The following products are low:
            <ul>
                @foreach($lowStockProducts as $p)
                <li>{{ $p->name }} - Stock: {{ $p->stock_quantity }} (Threshold: {{ $p->low_stock_threshold }})</li>
                @endforeach
            </ul>
        </div>
        @endif
        <table class="table table-bordered" id="inventoryTable">
            <thead>
                <tr><th>Product</th><th>SKU</th><th>Category</th><th>Current Stock</th><th>Threshold</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                @php $statusClass = $p->stock_quantity <= $p->low_stock_threshold ? ($p->stock_quantity == 0 ? 'bg-danger text-white' : 'bg-warning') : ''; @endphp
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->category->name ?? 'N/A' }}</td>
                    <td><span class="{{ $statusClass }} px-2 py-1 rounded">{{ $p->stock_quantity }}</span></td>
                    <td>{{ $p->low_stock_threshold }}</td>
                    <td>
                        @if($p->stock_quantity == 0) <span class="badge bg-danger">Out of Stock</span>
                        @elseif($p->stock_quantity <= $p->low_stock_threshold) <span class="badge bg-warning">Low Stock</span>
                        @else <span class="badge bg-success">In Stock</span>
                        @endif
                    </td>
                    <td><a href="{{ route('inventory.adjust', $p) }}" class="btn btn-sm btn-primary">Adjust</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>$(document).ready(function(){$('#inventoryTable').DataTable();});</script>
@endpush
