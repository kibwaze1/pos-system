@extends('layouts.app')
@section('title', 'Edit Product')
@section('content')
<div class="card">
    <div class="card-header">Edit Product: {{ $product->name }}</div>
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Barcode</label>
                    <div class="input-group">
                        <input type="text" name="barcode" id="barcode" class="form-control" value="{{ $product->barcode }}">
                        <button type="button" class="btn btn-secondary" id="generateBarcodeBtn">Generate Random</button>
                    </div>
                    <small class="text-muted">Leave empty to use SKU as barcode.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Category</label>
                    <div class="input-group">
                        <select name="category_id" class="form-control" id="categorySelect">
                            <option value="">Select existing category</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ $product->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                            <option value="new">+ Add new category</option>
                        </select>
                        <input type="text" name="new_category" id="newCategory" class="form-control" placeholder="New category name" style="display:none;">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Brand</label>
                    <div class="input-group">
                        <select name="brand_id" class="form-control" id="brandSelect">
                            <option value="">Select existing brand</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                            <option value="new">+ Add new brand</option>
                        </select>
                        <input type="text" name="new_brand" id="newBrand" class="form-control" placeholder="New brand name" style="display:none;">
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Purchase Price</label>
                    <input type="number" step="0.01" name="purchase_price" class="form-control" value="{{ $product->purchase_price }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Selling Price</label>
                    <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ $product->selling_price }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold" class="form-control" value="{{ $product->low_stock_threshold }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Current Image</label>
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" width="100">
                    @else
                        <p>No image</p>
                    @endif
                    <input type="file" name="image" class="form-control mt-2">
                </div>
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" {{ $product->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Category and brand logic
    document.getElementById('categorySelect').addEventListener('change', function() {
        let newCatInput = document.getElementById('newCategory');
        if (this.value === 'new') {
            newCatInput.style.display = 'block';
            newCatInput.required = true;
        } else {
            newCatInput.style.display = 'none';
            newCatInput.required = false;
        }
    });
    document.getElementById('brandSelect').addEventListener('change', function() {
        let newBrandInput = document.getElementById('newBrand');
        if (this.value === 'new') {
            newBrandInput.style.display = 'block';
            newBrandInput.required = true;
        } else {
            newBrandInput.style.display = 'none';
            newBrandInput.required = false;
        }
    });

    // Generate random barcode
    document.getElementById('generateBarcodeBtn').addEventListener('click', function() {
        let randomBarcode = Math.random().toString(36).substring(2, 10).toUpperCase();
        document.getElementById('barcode').value = randomBarcode;
    });
</script>
@endpush
