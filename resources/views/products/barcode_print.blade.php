@extends('layouts.app')
@section('title', 'Print Barcode Labels')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Print Barcode Labels</h3>
        <p class="text-muted">Select products and quantity of labels to print. Layout will be optimized automatically.</p>
    </div>
    <div class="card-body">
        <form action="{{ route('products.barcode.sheet') }}" method="POST" target="_blank">
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered" id="productsTable">
                    <thead>
                        <tr>
                            <th style="width: 30px;"><input type="checkbox" id="selectAll"></th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th style="width: 150px;">Quantity of Labels</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td><input type="checkbox" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}" class="product-checkbox"></td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($product->selling_price, 2) }}</td>
                            <td>
                                <input type="number" name="products[{{ $loop->index }}][quantity]" class="form-control quantity-input" value="1" min="1" max="50" style="width: 100px;" disabled>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary" id="printBtn" disabled>Generate & Print Labels</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const printBtn = document.getElementById('printBtn');

    function updatePrintButton() {
        let anyChecked = false;
        checkboxes.forEach((cb, index) => {
            if (cb.checked) {
                anyChecked = true;
                quantityInputs[index].disabled = false;
            } else {
                quantityInputs[index].disabled = true;
                quantityInputs[index].value = 1;
            }
        });
        printBtn.disabled = !anyChecked;
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updatePrintButton();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updatePrintButton));
    updatePrintButton();
</script>
@endpush
