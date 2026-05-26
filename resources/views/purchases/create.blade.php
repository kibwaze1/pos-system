@extends('layouts.app')
@section('title', 'Create Purchase Order')
@section('content')
<div class="card">
    <div class="card-header">New Purchase Order</div>
    <div class="card-body">
        <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
            @csrf
            <div class="mb-3">
                <label>Supplier</label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Products</label>
                <div id="items">
                    <div class="row mb-2 item-row">
                        <div class="col-5"><select name="items[0][product_id]" class="form-control product-select" required><option value="">Select Product</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>@endforeach</select></div>
                        <div class="col-2"><input type="number" name="items[0][quantity]" class="form-control" placeholder="Qty" required></div>
                        <div class="col-3"><input type="number" step="0.01" name="items[0][price]" class="form-control" placeholder="Price" required></div>
                        <div class="col-2"><button type="button" class="btn btn-danger remove-item">Remove</button></div>
                    </div>
                </div>
                <button type="button" id="addItem" class="btn btn-sm btn-secondary">Add Another Product</button>
            </div>
            <div class="mb-3">
                <label>Amount Paid (Optional)</label>
                <input type="number" step="0.01" name="paid_amount" class="form-control" value="0">
            </div>
            <button type="submit" class="btn btn-primary">Create Purchase Order</button>
        </form>
    </div>
</div>
<script>
    let itemIndex = 1;
    document.getElementById('addItem').addEventListener('click', function() {
        let container = document.getElementById('items');
        let newRow = document.createElement('div');
        newRow.className = 'row mb-2 item-row';
        newRow.innerHTML = `
            <div class="col-5"><select name="items[${itemIndex}][product_id]" class="form-control product-select" required><option value="">Select Product</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>@endforeach</select></div>
            <div class="col-2"><input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Qty" required></div>
            <div class="col-3"><input type="number" step="0.01" name="items[${itemIndex}][price]" class="form-control" placeholder="Price" required></div>
            <div class="col-2"><button type="button" class="btn btn-danger remove-item">Remove</button></div>
        `;
        container.appendChild(newRow);
        itemIndex++;
        attachRemoveEvents();
    });
    function attachRemoveEvents() {
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.removeEventListener('click', removeHandler);
            btn.addEventListener('click', removeHandler);
        });
    }
    function removeHandler(e) {
        e.target.closest('.item-row').remove();
    }
    attachRemoveEvents();
</script>
@endsection
