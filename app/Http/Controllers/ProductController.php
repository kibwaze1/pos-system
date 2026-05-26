<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;

class ProductController extends Controller
{
    // Display list of products
    public function index()
    {
        $products = Product::with(['category', 'brand'])->get();
        return view('products.index', compact('products'));
    }

    // Show form to create a new product
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('products.create', compact('categories', 'brands'));
    }

    // Store a new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
        ]);

        // Handle Category (new or existing)
        if ($request->category_id == 'new' && $request->filled('new_category')) {
            $category = Category::create(['name' => $request->new_category]);
            $categoryId = $category->id;
        } else {
            $categoryId = $request->category_id;
        }

        // Handle Brand (new or existing)
        if ($request->brand_id == 'new' && $request->filled('new_brand')) {
            $brand = Brand::create(['name' => $request->new_brand]);
            $brandId = $brand->id;
        } else {
            $brandId = $request->brand_id;
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Barcode: use provided or generate from SKU, then ensure uniqueness
        $barcode = $request->barcode ?: $request->sku;
        $originalBarcode = $barcode;
        $counter = 1;
        while (Product::where('barcode', $barcode)->exists()) {
            $barcode = $originalBarcode . '-' . $counter;
            $counter++;
        }

        Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'barcode' => $barcode,
            'category_id' => $categoryId,
            'brand_id' => $brandId ?: null,
            'description' => $request->description,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'low_stock_threshold' => $request->low_stock_threshold ?? 5,
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    // Show form to edit a product
    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('products.edit', compact('product', 'categories', 'brands'));
    }

    // Update a product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'selling_price' => 'required|numeric',
        ]);

        // Handle Category
        if ($request->category_id == 'new' && $request->filled('new_category')) {
            $category = Category::create(['name' => $request->new_category]);
            $categoryId = $category->id;
        } else {
            $categoryId = $request->category_id;
        }

        // Handle Brand
        if ($request->brand_id == 'new' && $request->filled('new_brand')) {
            $brand = Brand::create(['name' => $request->new_brand]);
            $brandId = $brand->id;
        } else {
            $brandId = $request->brand_id;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        // Barcode: if user provided new barcode, ensure uniqueness (except current product)
        $barcode = $request->barcode ?: $product->barcode;
        if ($barcode !== $product->barcode) {
            $originalBarcode = $barcode;
            $counter = 1;
            while (Product::where('barcode', $barcode)->where('id', '!=', $product->id)->exists()) {
                $barcode = $originalBarcode . '-' . $counter;
                $counter++;
            }
        }

        $product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'barcode' => $barcode,
            'category_id' => $categoryId,
            'brand_id' => $brandId ?: null,
            'description' => $request->description,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'low_stock_threshold' => $request->low_stock_threshold ?? 5,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    // Delete a product
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    // AJAX search for POS (used by barcode scanner and manual search)
    public function search(Request $request)
    {
        $search = $request->get('search');
        $products = Product::where('name', 'like', "%{$search}%")
            ->orWhere('barcode', $search)
            ->orWhere('sku', $search)
            ->where('stock_quantity', '>', 0)
            ->limit(10)
            ->get(['id', 'name', 'selling_price', 'stock_quantity']);
        return response()->json($products);
    }

    // AJAX stock check for a single product (used by Add Stock and POS)
    public function checkStock($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json([
                'success' => true,
                'current_stock' => $product->stock_quantity,
                'product_name' => $product->name,
                'sku' => $product->sku,
            ]);
        }
        return response()->json(['success' => false], 404);
    }

    // Generate barcode image (PNG) for single product
    public function generateBarcode(Product $product)
    {
        $code = $product->barcode ?: $product->sku;
        $barcodeGenerator = new DNS1D();
        $image = $barcodeGenerator->getBarcodePNG($code, 'C128');
        return response($image)->header('Content-type', 'image/png');
    }

    // Show barcode printing interface
    public function printBarcodes()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('products.barcode_print', compact('products'));
    }

    // Generate printable barcode sheet – dynamic layout
    public function generateBarcodeSheet(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1|max:50',
        ]);

        $barcodeGenerator = new DNS1D();

        $allLabels = [];
        foreach ($request->products as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                $barcodeCode = $product->barcode ?: $product->sku;
                $barcodeImage = $barcodeGenerator->getBarcodePNG($barcodeCode, 'C128', 2, 50);
                for ($i = 0; $i < $item['quantity']; $i++) {
                    $allLabels[] = [
                        'product' => $product,
                        'barcode_image' => $barcodeImage,
                    ];
                }
            }
        }

        $totalLabels = count($allLabels);
        if ($totalLabels == 0) {
            return view('products.barcode_sheet', ['pages' => [], 'gridCols' => 4, 'gridRows' => 0]);
        }

        // Dynamic columns (2-6) and rows
        $bestCols = 4;
        $bestRows = ceil($totalLabels / $bestCols);
        $bestEmpty = $bestCols * $bestRows - $totalLabels;

        for ($cols = 6; $cols >= 2; $cols--) {
            $rows = ceil($totalLabels / $cols);
            $empty = $cols * $rows - $totalLabels;
            if ($empty < $bestEmpty || ($empty == $bestEmpty && $cols > $bestCols)) {
                $bestEmpty = $empty;
                $bestCols = $cols;
                $bestRows = $rows;
            }
            if ($bestEmpty == 0) break;
        }

        $gridCols = $bestCols;
        $gridRows = $bestRows;
        $labelsPerPage = $gridCols * $gridRows;
        $pages = array_chunk($allLabels, $labelsPerPage);

        return view('products.barcode_sheet', compact('pages', 'gridCols', 'gridRows', 'labelsPerPage'));
    }
}
