<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
            'paid' => 'required|numeric',
            'payment_method' => 'required|in:cash,mpesa,card',
        ]);

        // Generate unique invoice number
        $invoiceNo = 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(6));

        // Calculate change
        $change = $request->paid - $request->total;

        // Create sale record
        $sale = Sale::create([
            'invoice_no' => $invoiceNo,
            'user_id' => auth()->id(),
            'customer_id' => null, // optional, can be selected later
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'tax' => $request->tax,
            'total' => $request->total,
            'payment_method' => $request->payment_method,
            'paid' => $request->paid,
            'change' => $change,
            'status' => 'completed',
        ]);

        // Insert sale items and update stock
        foreach ($request->items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
            ]);

            // Update product stock
            $product = Product::find($item['id']);
            $product->decrement('stock_quantity', $item['quantity']);

            // Log inventory movement
            InventoryLog::create([
                'product_id' => $item['id'],
                'type' => 'stock_out',
                'quantity' => $item['quantity'],
                'reason' => 'Sale - ' . $invoiceNo,
                'user_id' => auth()->id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'invoice_no' => $invoiceNo,
            'sale_id' => $sale->id
        ]);
    }

    public function invoice(Sale $sale)
    {
        $sale->load('items.product', 'user');
        return view('pos.invoice', compact('sale'));
    }
}
