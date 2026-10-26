<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Aggregate statistics directly from database
        $totalProducts = Product::count();
        $totalUsers = User::count();

        $completedSalesQuery = Sale::where('status', 'completed');
        $totalSalesCount = (clone $completedSalesQuery)->count();
        $salesToday = (clone $completedSalesQuery)->whereDate('sold_at', $today)->sum('total');
        $salesThisMonth = (clone $completedSalesQuery)
            ->whereMonth('sold_at', $currentMonth)
            ->whereYear('sold_at', $currentYear)
            ->sum('total');

        $completedPurchasesQuery = Purchase::where('status', 'completed');
        $totalPurchasesCount = (clone $completedPurchasesQuery)->count();
        $purchasesThisMonth = (clone $completedPurchasesQuery)
            ->whereMonth('purchased_at', $currentMonth)
            ->whereYear('purchased_at', $currentYear)
            ->sum('total');

        // Low stock products alert
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->get();
        $lowStockCount = $lowStockProducts->count();

        // Recent transactions with eager loading to prevent N+1 queries
        $recentSales = Sale::with('user')
            ->latest('sold_at')
            ->take(6)
            ->get();

        $recentPurchases = Purchase::with(['supplier', 'user'])
            ->latest('purchased_at')
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalUsers',
            'totalSalesCount',
            'totalPurchasesCount',
            'salesToday',
            'salesThisMonth',
            'purchasesThisMonth',
            'lowStockProducts',
            'lowStockCount',
            'recentSales',
            'recentPurchases'
        ));
    }
}
