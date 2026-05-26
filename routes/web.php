<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

// Root redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS & Sales
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [SaleController::class, 'store'])->name('pos.checkout');
    Route::get('/pos/invoice/{sale}', [SaleController::class, 'invoice'])->name('pos.invoice');

    // API
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('api.products.search');
    Route::get('/api/products/check-stock/{id}', [ProductController::class, 'checkStock'])->name('api.products.check-stock');

    // Printing
    Route::post('/print/receipt/{sale}', [PrintController::class, 'directPrint'])->name('print.direct');
    Route::get('/product/barcode/{product}', [ProductController::class, 'generateBarcode'])->name('product.barcode');

    // ------------------------------------------------------------------
    // Module Permissions (cashiers can access if granted)
    // ------------------------------------------------------------------
    Route::resource('products', ProductController::class)->middleware('module:products');
    Route::get('/products/barcode/print', [ProductController::class, 'printBarcodes'])->name('products.barcode.print')->middleware('module:print_barcodes');
    Route::post('/products/barcode/sheet', [ProductController::class, 'generateBarcodeSheet'])->name('products.barcode.sheet')->middleware('module:print_barcodes');

    Route::resource('customers', CustomerController::class)->middleware('module:customers');
    Route::resource('suppliers', SupplierController::class)->middleware('module:suppliers');
    Route::resource('purchases', PurchaseController::class)->middleware('module:purchases');
    Route::resource('expenses', ExpenseController::class)->middleware('module:expenses');

    // Inventory routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index')->middleware('module:inventory_view');
        Route::get('/adjust/{product}', [InventoryController::class, 'adjustForm'])->name('adjust');
        Route::post('/adjust/{product}', [InventoryController::class, 'adjust'])->name('adjust.submit');
        Route::get('/add-stock', [InventoryController::class, 'addStockForm'])->name('add-stock')->middleware('module:add_stock');
        Route::post('/add-stock', [InventoryController::class, 'addStock'])->name('add-stock.submit')->middleware('module:add_stock');
        Route::get('/search-product', [InventoryController::class, 'searchProductForStock'])->name('search-product');
    });
    Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history')->middleware('module:inventory_logs');

    // ------------------------------------------------------------------
    // Admin Only (role:admin)
    // ------------------------------------------------------------------
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/profit', [ReportController::class, 'profit'])->name('profit');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
            Route::get('/export-sales-pdf', [ReportController::class, 'exportSalesPDF'])->name('export-sales-pdf');
        });
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/update', [SettingController::class, 'update'])->name('update');
            Route::post('/backup', [SettingController::class, 'backup'])->name('backup');
            Route::post('/restore', [SettingController::class, 'restore'])->name('restore');
        });
    });
});
