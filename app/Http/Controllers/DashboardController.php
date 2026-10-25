<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Pelanggan tidak memakai dashboard internal; arahkan ke katalog menu.
        if (! Gate::allows('view-inventory')) {
            return redirect()->route('storefront.index');
        }

        $user = $request->user();

        // Admin melihat seluruh penjualan; staff hanya miliknya sendiri.
        $sales = fn () => Sale::completed()
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));

        $stats = [
            'products' => Product::count(),
            'customers' => Customer::count(),
            'low_stock' => Product::lowStock()->count(),
            'sales_today' => $sales()->whereDate('sold_at', today())->sum('total'),
            'sales_month' => $sales()
                ->whereYear('sold_at', now()->year)
                ->whereMonth('sold_at', now()->month)
                ->sum('total'),
            'sales_count' => $sales()->count(),
            'purchases_count' => $user->isAdmin() ? Purchase::completed()->count() : null,
        ];

        // Penjualan 7 hari terakhir (query agregat).
        $daily = $sales()
            ->where('sold_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(sold_at) as day, SUM(total) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $chart = collect(range(6, 0))->map(function ($i) use ($daily) {
            $date = now()->subDays($i);

            return [
                'label' => $date->format('d/m'),
                'total' => (float) ($daily[$date->toDateString()] ?? 0),
                'today' => $i === 0,
            ];
        });

        return view('dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'chartMax' => max(1, $chart->max('total')),
            'lowStockProducts' => Product::with('category')->lowStock()
                ->orderByRaw('(minimum_stock - stock) desc')->limit(5)->get(),
            'recentSales' => Sale::with('user')
                ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
                ->latest('sold_at')->latest('id')->limit(5)->get(),
        ]);
    }
}