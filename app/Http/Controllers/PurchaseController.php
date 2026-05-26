<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier', 'user')->orderBy('created_at', 'desc')->get();
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        $purchaseNo = 'PO-' . date('Ymd') . '-' . Str::upper(Str::random(6));
        $totalAmount = 0;
        foreach ($request->items as $item) {
            $totalAmount += $item['quantity'] * $item['price'];
        }

        $purchase = Purchase::create([
            'purchase_no' => $purchaseNo,
            'supplier_id' => $request->supplier_id,
            'user_id' => auth()->id(),
            'total_amount' => $totalAmount,
            'paid_amount' => $request->paid_amount ?? 0,
            'status' => 'received',
        ]);

        foreach ($request->items as $item) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ]);

            // Update product stock (increase)
            $product = Product::find($item['product_id']);
            $product->increment('stock_quantity', $item['quantity']);

            // Log inventory (optional)
            \App\Models\InventoryLog::create([
                'product_id' => $item['product_id'],
                'type' => 'stock_in',
                'quantity' => $item['quantity'],
                'reason' => 'Purchase Order: ' . $purchaseNo,
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase order created and stock updated.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'supplier', 'user');
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        // Editing purchases not recommended, but you can implement if needed
        abort(403, 'Editing purchases is disabled.');
    }

    public function update(Request $request, Purchase $purchase)
    {
        abort(403, 'Updating purchases is disabled.');
    }

    public function destroy(Purchase $purchase)
    {
        // Only allow deletion if not affecting stock? For simplicity, deny.
        abort(403, 'Deleting purchases is disabled.');
    }
}
