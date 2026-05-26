<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Show current inventory of all products
    public function index()
    {
        $products = Product::with('category')->get();
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->get();
        return view('inventory.index', compact('products', 'lowStockProducts'));
    }

    // Show stock adjustment form for a specific product
    public function adjustForm(Product $product)
    {
        return view('inventory.adjust', compact('product'));
    }

    // Process stock adjustment (add, subtract, set)
    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:add,subtract,set',
            'reason' => 'nullable|string',
        ]);

        $oldQuantity = $product->stock_quantity;
        $change = 0;

        switch ($request->type) {
            case 'add':
                $change = $request->quantity;
                $product->stock_quantity += $request->quantity;
                break;
            case 'subtract':
                $change = -$request->quantity;
                $product->stock_quantity -= $request->quantity;
                break;
            case 'set':
                $change = $request->quantity - $oldQuantity;
                $product->stock_quantity = $request->quantity;
                break;
        }

        $product->save();

        InventoryLog::create([
            'product_id' => $product->id,
            'type' => 'adjustment',
            'quantity' => $change,
            'reason' => $request->reason ?? 'Manual adjustment',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('inventory.index')->with('success', 'Stock updated successfully.');
    }

    // View inventory logs history
    public function history()
    {
        $logs = InventoryLog::with(['product', 'user'])->orderBy('created_at', 'desc')->paginate(50);
        return view('inventory.history', compact('logs'));
    }

    // ------------------------------------------------------------
    // ADD STOCK (SCAN OR MANUAL)
    // ------------------------------------------------------------

    // Show the Add Stock form (scan or manual)
    public function addStockForm()
    {
        $products = Product::orderBy('name')->get();
        return view('inventory.add_stock', compact('products'));
    }

    // Process stock addition (simple increment)
    public function addStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldQuantity = $product->stock_quantity;
        $product->increment('stock_quantity', $request->quantity);

        InventoryLog::create([
            'product_id' => $product->id,
            'type' => 'stock_in',
            'quantity' => $request->quantity,
            'reason' => $request->reason ?? 'Manual stock addition',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('inventory.add-stock')
            ->with('success', "Added {$request->quantity} units to {$product->name}. New stock: {$product->stock_quantity}");
    }

    // AJAX endpoint to search product by barcode/SKU/name for the add stock form
    public function searchProductForStock(Request $request)
    {
        $search = $request->get('search');
        $product = Product::where('barcode', $search)
            ->orWhere('sku', $search)
            ->orWhere('name', 'like', "%{$search}%")
            ->first();

        if ($product) {
            return response()->json([
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'current_stock' => $product->stock_quantity,
            ]);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }
}
