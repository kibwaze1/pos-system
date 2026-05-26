@extends('layouts.app')
@section('title', 'Products')
@section('content')
<div class="card">
    <div class="card-header">
        Products
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm float-end">Add Product</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="productsTable">
            <thead>
                <tr><th>SKU</th><th>Name</th><th>Category</th><th>Barcode</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                <tr>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? 'N/A' }}</td>
                    <td>
                        @if($p->barcode)
                            <img src="{{ route('product.barcode', $p) }}" width="100" height="40" alt="barcode">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($p->selling_price, 2) }}</td>
                    <td>{{ $p->stock_quantity }}</td>
                    <td>{{ $p->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-product" data-id="{{ $p->id }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#productsTable').DataTable();
    $('.delete-product').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/products/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('Deleted!', 'Product has been deleted.', 'success');
                        location.reload();
                    }
                });
            }
        });
    });
});
</script>
@endpush
