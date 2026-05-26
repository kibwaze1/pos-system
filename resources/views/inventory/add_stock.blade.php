@extends('layouts.app')
@section('title', 'Add Stock')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Add Stock to Product</h3>
        <p class="text-muted">Scan a barcode or search manually, then enter quantity.</p>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Scan Barcode / Search</div>
                    <div class="card-body">
                        <input type="text" id="scanInput" class="form-control" placeholder="Scan barcode, type SKU, or product name...">
                        <div id="productInfo" class="mt-3" style="display:none;">
                            <hr>
                            <p><strong>Product:</strong> <span id="productName"></span></p>
                            <p><strong>SKU:</strong> <span id="productSku"></span></p>
                            <p><strong>Current Stock:</strong> <span id="currentStock"></span></p>
                            <form action="{{ route('inventory.add-stock.submit') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" id="productId">
                                <div class="mb-3"><label>Quantity to add</label><input type="number" name="quantity" class="form-control" min="1" required></div>
                                <div class="mb-3"><label>Reason</label><textarea name="reason" class="form-control" rows="2"></textarea></div>
                                <button type="submit" class="btn btn-success">Add Stock</button>
                            </form>
                        </div>
                        <div id="notFoundMsg" class="alert alert-warning mt-3" style="display:none;">Product not found.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Manual Product Selection</div>
                    <div class="card-body">
                        <select id="productSelect" class="form-select">
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-sku="{{ $product->sku }}" data-stock="{{ $product->stock_quantity }}">{{ $product->name }} ({{ $product->sku }}) - Stock: {{ $product->stock_quantity }}</option>
                            @endforeach
                        </select>
                        <div id="manualInfo" class="mt-3" style="display:none;">
                            <hr>
                            <p><strong>Product:</strong> <span id="manualName"></span></p>
                            <p><strong>SKU:</strong> <span id="manualSku"></span></p>
                            <p><strong>Current Stock:</strong> <span id="manualStock"></span></p>
                            <form action="{{ route('inventory.add-stock.submit') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" id="manualProductId">
                                <div class="mb-3"><label>Quantity to add</label><input type="number" name="quantity" class="form-control" min="1" required></div>
                                <div class="mb-3"><label>Reason</label><textarea name="reason" class="form-control" rows="2"></textarea></div>
                                <button type="submit" class="btn btn-success">Add Stock</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global barcode scanner for this page
    let barcodeBuffer = '';
    let barcodeTimer = null;

    function fetchProductAndShow(id) {
        $.get('/api/products/check-stock/' + id, function(data) {
            if (data.success) {
                $('#productId').val(id);
                $('#productName').text(data.product_name);
                $('#productSku').text(data.sku);
                $('#currentStock').text(data.current_stock);
                $('#productInfo').show();
                $('#notFoundMsg').hide();
            } else {
                $('#productInfo').hide();
                $('#notFoundMsg').show();
            }
        });
    }

    function searchProduct(query) {
        if (!query.trim()) return;
        $.get('{{ route("api.products.search") }}', {search: query}, function(data) {
            if (data.length === 1) {
                fetchProductAndShow(data[0].id);
                $('#scanInput').val('');
            } else if (data.length > 1) {
                // Multiple matches – do nothing, user must type further or select from dropdown
                $('#productInfo').hide();
                $('#notFoundMsg').text('Multiple products found. Please refine search.').show();
            } else {
                $('#productInfo').hide();
                $('#notFoundMsg').show();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (document.activeElement === document.getElementById('scanInput')) return;
        if (e.key === 'Enter') {
            e.preventDefault();
            if (barcodeBuffer.length > 0) {
                searchProduct(barcodeBuffer);
                barcodeBuffer = '';
                clearTimeout(barcodeTimer);
            }
            return;
        }
        if (e.key.length === 1 && /[a-zA-Z0-9\-]/.test(e.key)) {
            barcodeBuffer += e.key;
            clearTimeout(barcodeTimer);
            barcodeTimer = setTimeout(() => { barcodeBuffer = ''; }, 100);
        }
    });

    // Manual typing in scan input (debounced)
    let searchTimeout;
    $('#scanInput').on('input', function() {
        let query = $(this).val().trim();
        if (searchTimeout) clearTimeout(searchTimeout);
        if (query === '') { $('#productInfo').hide(); $('#notFoundMsg').hide(); return; }
        searchTimeout = setTimeout(() => searchProduct(query), 500);
    });

    // Manual select dropdown
    $('#productSelect').on('change', function() {
        let opt = $(this).find(':selected');
        if (this.value) {
            $('#manualProductId').val(this.value);
            $('#manualName').text(opt.data('name'));
            $('#manualSku').text(opt.data('sku'));
            $('#manualStock').text(opt.data('stock'));
            $('#manualInfo').show();
        } else {
            $('#manualInfo').hide();
        }
    });
</script>
@endsection
