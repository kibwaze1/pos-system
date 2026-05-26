<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Sale::whereDate('created_at', today())->sum('total');
        $monthSales = Sale::whereMonth('created_at', now()->month)->sum('total');
        $totalSales = Sale::sum('total');
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalExpenses = Expense::sum('amount');

        // Low stock products
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->get();
        $outOfStockProducts = Product::where('stock_quantity', 0)->count();

        $recentSales = Sale::with('user')->latest()->limit(10)->get();

        // Chart data (last 7 days)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('D, M j');
            $chartData[] = Sale::whereDate('created_at', $date)->sum('total');
        }

        // Top selling products
        $topProducts = DB::table('sale_items')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'todaySales', 'monthSales', 'totalSales', 'totalProducts',
            'totalCustomers', 'totalExpenses', 'lowStockProducts',
            'outOfStockProducts', 'recentSales', 'chartLabels',
            'chartData', 'topProducts'
        ));
    }
}
