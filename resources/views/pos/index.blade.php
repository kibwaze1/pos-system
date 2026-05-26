@extends('layouts.app')
@section('title', 'Point of Sale')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">Search Products</div>
            <div class="card-body">
                <input type="text" id="productSearch" class="form-control" placeholder="Scan barcode or type name...">
                <div id="productResults" class="row mt-3"></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Shopping Cart</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="cartTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price ({{ \App\Models\Setting::get('currency_symbol', '$') }})</th>
                                <th>Quantity</th>
                                <th>Total ({{ \App\Models\Setting::get('currency_symbol', '$') }})</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cartBody"></tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-borderless">
                            <tr><td class="text-end"><strong>Subtotal:</strong></td><td id="subtotal">0.00</td></tr>
                            <tr><td class="text-end"><label>Discount ({{ \App\Models\Setting::get('currency_symbol', '$') }}):</label></td>
                                <td><input type="number" id="discount" class="form-control" value="0" step="0.01" style="width:120px"></td>
                            <tr>
                            <tr><td class="text-end"><label>Tax (%):</label></td>
                                <td><input type="number" id="tax" class="form-control" value="0" step="0.01" style="width:120px"></td>
                            </tr>
                            <tr class="table-active"><td class="text-end"><strong>Grand Total:</strong></td>
                                <td><strong id="grandTotal">0.00</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label>Payment Method</label>
                        <select id="paymentMethod" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="mpesaPhoneGroup" style="display: none;">
                        <label>M-Pesa Phone Number</label>
                        <input type="tel" id="mpesaPhone" class="form-control" placeholder="0712345678">
                    </div>
                    <div class="col-md-4" id="cashPaymentGroup">
                        <label>Amount Paid ({{ \App\Models\Setting::get('currency_symbol', '$') }})</label>
                        <input type="number" id="paidAmount" class="form-control" step="0.01">
                    </div>
                </div>
                <div class="row mt-2" id="changeGroup">
                    <div class="col-md-4 offset-md-8">
                        <label>Change ({{ \App\Models\Setting::get('currency_symbol', '$') }})</label>
                        <input type="text" id="changeAmount" class="form-control" readonly>
                    </div>
                </div>
                <div class="mt-3">
                    <button id="checkoutBtn" class="btn btn-success w-100">Complete Sale</button>
                    <button id="clearCartBtn" class="btn btn-danger mt-2">Clear Cart</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];
    const currencySymbol = "{{ \App\Models\Setting::get('currency_symbol', '$') }}";
    let barcodeBuffer = '';
    let barcodeTimer = null;
    let searchTimeout = null;

    // ----- Add to cart with stock check -----
    function addToCart(id, name, price, availableStock) {
        let existing = cart.find(i => i.id === id);
        let newQty = existing ? existing.quantity + 1 : 1;
        if (newQty > availableStock) {
            Swal.fire('Stock Limit', `Only ${availableStock} units available for ${name}`, 'warning');
            return;
        }
        if (existing) existing.quantity++;
        else cart.push({id, name, price, quantity: 1, stock: availableStock});
        renderCart();
    }

    // ----- Search & auto-add / show buttons -----
    function performSearch(query) {
        if (!query || query.length < 1) return;
        $.get('{{ route("api.products.search") }}', {search: query}, function(data) {
            if (data.length === 1) {
                let p = data[0];
                addToCart(p.id, p.name, p.selling_price, p.stock_quantity);
                $('#productSearch').val('');
                $('#productResults').html('');
            } else if (data.length > 1) {
                let html = '<div class="row">';
                data.forEach(p => {
                    html += `<div class="col-3 col-md-2 mb-2">
                                <button class="btn btn-outline-primary w-100 product-add-btn" data-id="${p.id}" data-name="${p.name}" data-price="${p.selling_price}" data-stock="${p.stock_quantity}">
                                    ${p.name}<br>${currencySymbol}${p.selling_price}
                                </button>
                            </div>`;
                });
                html += '</div>';
                $('#productResults').html(html);
                $('.product-add-btn').off('click').on('click', function() {
                    let id = $(this).data('id'), name = $(this).data('name'), price = $(this).data('price'), stock = $(this).data('stock');
                    addToCart(id, name, price, stock);
                });
            } else {
                $('#productResults').html('<p class="text-muted">No products found</p>');
            }
        });
    }

    // ----- Global barcode scanner -----
    document.addEventListener('keydown', function(e) {
        if (document.activeElement === document.getElementById('productSearch')) return;
        if (e.key === 'Enter') {
            e.preventDefault();
            if (barcodeBuffer.length > 0) {
                performSearch(barcodeBuffer);
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

    // ----- Manual search (debounced) -----
    $('#productSearch').on('input', function() {
        let query = $(this).val().trim();
        if (searchTimeout) clearTimeout(searchTimeout);
        if (query === '') { $('#productResults').html(''); return; }
        searchTimeout = setTimeout(() => performSearch(query), 500);
    });

    // ----- Payment method toggling -----
    function togglePaymentFields() {
        let method = $('#paymentMethod').val();
        if (method === 'mpesa') {
            $('#mpesaPhoneGroup').show();
            $('#cashPaymentGroup').hide();
            $('#changeGroup').hide();
            $('#paidAmount').prop('required', false);
        } else {
            $('#mpesaPhoneGroup').hide();
            $('#cashPaymentGroup').show();
            $('#changeGroup').show();
            $('#paidAmount').prop('required', true);
        }
    }
    $('#paymentMethod').on('change', togglePaymentFields);
    togglePaymentFields(); // initial call

    // ----- Cart rendering -----
    function renderCart() {
        let tbody = '', subtotal = 0;
        cart.forEach((item, idx) => {
            let total = item.price * item.quantity;
            subtotal += total;
            tbody += `<tr>
                <td>${item.name}</td>
                <td>${currencySymbol}${item.price.toFixed(2)}</td>
                <td><input type="number" class="form-control qty-input" data-index="${idx}" value="${item.quantity}" min="1" style="width:80px"></td>
                <td>${currencySymbol}${total.toFixed(2)}</td>
                <td><button class="btn btn-sm btn-danger" onclick="removeItem(${idx})">Remove</button></td>
            </tr>`;
        });
        $('#cartBody').html(tbody);
        $('#subtotal').text(subtotal.toFixed(2));
        calculateTotal();
        $('.qty-input').off('change').on('change', function() {
            let idx = $(this).data('index');
            let newQty = parseInt($(this).val());
            if (!isNaN(newQty) && newQty > 0) {
                let item = cart[idx];
                if (newQty > item.stock) {
                    Swal.fire('Stock Limit', `Only ${item.stock} units available`, 'warning');
                    $(this).val(item.quantity);
                    return;
                }
                item.quantity = newQty;
                renderCart();
            } else {
                renderCart();
            }
        });
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function calculateTotal() {
        let subtotal = parseFloat($('#subtotal').text());
        let discount = parseFloat($('#discount').val());
        let tax = parseFloat($('#tax').val());
        let total = subtotal - discount + (subtotal * tax / 100);
        $('#grandTotal').text(total.toFixed(2));
        let paid = parseFloat($('#paidAmount').val());
        if (!isNaN(paid)) {
            let change = paid - total;
            $('#changeAmount').val(change >= 0 ? change.toFixed(2) : '0.00');
        }
    }

    $('#discount, #tax, #paidAmount').on('input', calculateTotal);
    $('#clearCartBtn').click(() => { cart = []; renderCart(); });

    // ----- Checkout -----
    $('#checkoutBtn').click(function() {
        if (cart.length === 0) {
            Swal.fire('Error', 'Cart is empty', 'error');
            return;
        }

        let total = parseFloat($('#grandTotal').text());
        let paymentMethod = $('#paymentMethod').val();
        let saleData = {
            items: cart,
            subtotal: $('#subtotal').text(),
            discount: $('#discount').val(),
            tax: $('#tax').val(),
            total: total,
            payment_method: paymentMethod,
            _token: '{{ csrf_token() }}'
        };

        if (paymentMethod === 'mpesa') {
            let phone = $('#mpesaPhone').val();
            if (!phone) {
                Swal.fire('Error', 'Please enter M-Pesa phone number', 'error');
                return;
            }
            saleData.phone = phone;
            saleData.paid = total;   // exact amount
        } else {
            let paid = parseFloat($('#paidAmount').val());
            if (isNaN(paid) || paid < total) {
                Swal.fire('Error', 'Insufficient payment', 'error');
                return;
            }
            saleData.paid = paid;
        }

        $.post('{{ route("pos.checkout") }}', saleData, function(response) {
            if (response.success) {
                if (paymentMethod === 'mpesa') {
                    Swal.fire('M-Pesa Initiated', response.message || 'Check your phone and enter PIN', 'info');
                } else {
                    Swal.fire('Success', 'Sale completed! Invoice: ' + response.invoice_no, 'success');
                    window.open('/pos/invoice/' + response.sale_id, '_blank');
                }
                cart = [];
                renderCart();
                $('#discount').val(0);
                $('#tax').val(0);
                $('#paidAmount').val('');
                $('#changeAmount').val('');
                $('#mpesaPhone').val('');
                togglePaymentFields(); // reset UI
            } else {
                Swal.fire('Error', response.error || 'Sale failed', 'error');
            }
        }).fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.error || 'Sale failed', 'error');
        });
    });
</script>
@endpush
