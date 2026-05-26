@extends('layouts.app')
@section('title', 'Adjust Stock - ' . $product->name)
@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4>Adjust Stock for: {{ $product->name }}</h4>
                <p>Current Stock: <strong>{{ $product->stock_quantity }}</strong></p>
            </div>
            <div class="card-body">
                <form action="{{ route('inventory.adjust.submit', $product) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Adjustment Type</label>
                        <select name="type" class="form-control" required>
                            <option value="add">Add Stock</option>
                            <option value="subtract">Remove Stock</option>
                            <option value="set">Set Exact Quantity</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" required>
                        <small class="text-muted">For "Add/Remove" – enter the number to add or remove. For "Set" – enter the new total stock quantity.</small>
                    </div>
                    <div class="mb-3">
                        <label>Reason (Optional)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="e.g., Damaged, Restock, Inventory count correction"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Apply Adjustment</button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
