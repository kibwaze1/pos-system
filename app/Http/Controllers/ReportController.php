<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Reports Dashboard
    public function index()
    {
        $totalSales = Sale::sum('total');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalSales - $totalExpenses;
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        $monthlySales = Sale::select(
                DB::raw('strftime("%Y-%m", created_at) as month'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('reports.index', compact('totalSales', 'totalExpenses', 'netProfit', 'totalProducts', 'totalCustomers', 'monthlySales'));
    }

    // Sales Report (HTML view)
    public function sales(Request $request)
    {
        $start = $request->get('start', now()->startOfMonth()->toDateString());
        $end = $request->get('end', now()->endOfMonth()->toDateString());

        $saleItems = SaleItem::with(['product', 'sale'])
            ->whereHas('sale', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->orderBy('sale_id', 'desc')
            ->get();

        $total = $saleItems->sum('total');

        return view('reports.sales', compact('saleItems', 'total', 'start', 'end'));
    }

    // Export Sales Report to PDF
    public function exportSalesPDF(Request $request)
    {
        $start = $request->get('start', now()->startOfMonth()->toDateString());
        $end = $request->get('end', now()->endOfMonth()->toDateString());

        $saleItems = SaleItem::with(['product', 'sale'])
            ->whereHas('sale', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->orderBy('sale_id', 'desc')
            ->get();

        $total = $saleItems->sum('total');

        $pdf = Pdf::loadView('reports.sales_pdf', compact('saleItems', 'total', 'start', 'end'));
        return $pdf->download('sales_report_' . date('Y-m-d') . '.pdf');
    }

    // Profit & Loss Report
    public function profit(Request $request)
    {
        $start = $request->get('start', now()->startOfMonth()->toDateString());
        $end = $request->get('end', now()->endOfMonth()->toDateString());

        $saleItems = SaleItem::with('product')
            ->whereHas('sale', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->get();

        $totalRevenue = 0;
        $totalCost = 0;
        $profitByProduct = [];

        foreach ($saleItems as $item) {
            $revenue = $item->total;
            $cost = ($item->product->purchase_price ?? 0) * $item->quantity;
            $profit = $revenue - $cost;

            $totalRevenue += $revenue;
            $totalCost += $cost;

            $productName = $item->product->name;
            if (!isset($profitByProduct[$productName])) {
                $profitByProduct[$productName] = [
                    'quantity' => 0,
                    'revenue' => 0,
                    'cost' => 0,
                    'profit' => 0
                ];
            }
            $profitByProduct[$productName]['quantity'] += $item->quantity;
            $profitByProduct[$productName]['revenue'] += $revenue;
            $profitByProduct[$productName]['cost'] += $cost;
            $profitByProduct[$productName]['profit'] += $profit;
        }

        $grossProfit = $totalRevenue - $totalCost;
        $totalExpenses = Expense::whereBetween('expense_date', [$start, $end])->sum('amount');
        $netProfit = $grossProfit - $totalExpenses;

        $dailyProfit = SaleItem::select(
                DB::raw('DATE(sales.created_at) as date'),
                DB::raw('SUM(sale_items.total) as revenue'),
                DB::raw('SUM(products.purchase_price * sale_items.quantity) as cost')
            )
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->where('sales.status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                $item->profit = $item->revenue - $item->cost;
                return $item;
            });

        return view('reports.profit', compact(
            'start', 'end', 'totalRevenue', 'totalCost', 'grossProfit',
            'totalExpenses', 'netProfit', 'profitByProduct', 'dailyProfit'
        ));
    }

    // Inventory Report
    public function inventory(Request $request)
    {
        $products = Product::with('category')->get();
        $lowStock = $products->filter(function($p) {
            return $p->stock_quantity <= $p->low_stock_threshold;
        });
        return view('reports.inventory', compact('products', 'lowStock'));
    }

    // Customer Report
    public function customers(Request $request)
    {
        $customers = Customer::withCount('sales')
            ->withSum('sales', 'total')
            ->orderBy('sales_sum_total', 'desc')
            ->get();
        return view('reports.customers', compact('customers'));
    }

    // Export placeholder (optional)
    public function export($type, $format)
    {
        return redirect()->back()->with('info', 'Export feature coming soon.');
    }
}
