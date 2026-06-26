<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\User;
use App\Support\SalesMetrics;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        $revenueTotals = SalesMetrics::totals();

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_products' => Product::count(),
            'active_products' => Product::active()->count(),
            'total_orders' => Order::count(),
            'active_orders' => Order::active()->count(),
            'pending_orders' => Order::pending()->count(),
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::active()->count(),
            'total_invoices' => Invoice::count(),
            'unpaid_invoices' => Invoice::unpaid()->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
            'total_revenue' => $revenueTotals['total_revenue'],
            'monthly_revenue' => $revenueTotals['monthly_revenue'],
            'today_revenue' => $revenueTotals['today_revenue'],
            'avg_ticket' => $revenueTotals['avg_invoice'],
            'total_services' => Service::count(),
            'active_services' => Service::active()->count(),
            'failed_services' => Service::where('status', 'failed')->count(),
            'pending_services' => Service::where('status', 'pending')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'salesAnalytics' => $this->salesAnalytics(),
            'topProducts' => $this->topProducts(),
            'leastProducts' => $this->leastProducts(),
            'revenueByDay' => SalesMetrics::revenueByDay(now()->subDays(13)),
            'recentServices' => Service::with(['user', 'product'])
                ->latest()
                ->take(8)
                ->get()
                ->map(fn (Service $service) => [
                    'id' => $service->id,
                    'service_number' => $service->service_number,
                    'user_name' => $service->user?->name,
                    'product_name' => $service->product?->name,
                    'status' => $service->status->value,
                    'status_label' => $service->status->label(),
                    'status_color' => $service->status->color(),
                    'delivered_at' => $service->delivered_at?->toIso8601String(),
                ]),
            'recent_orders' => Order::with(['user', 'product'])
                ->latest()
                ->take(8)
                ->get(),
            'recent_invoices' => Invoice::with('user')
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }

    /**
     * Aggregated sales metrics derived from active/paid orders.
     *
     * @return array<string, mixed>
     */
    protected function salesAnalytics(): array
    {
        $soldStatuses = ['active', 'completed'];

        return [
            'total_units' => (int) Order::whereIn('status', $soldStatuses)->count(),
            'total_sales_value' => (float) Order::whereIn('status', $soldStatuses)->sum('amount'),
            'units_this_month' => (int) Order::whereIn('status', $soldStatuses)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'distinct_products_sold' => (int) Order::whereIn('status', $soldStatuses)
                ->distinct('product_id')
                ->count('product_id'),
        ];
    }

    /**
     * Best selling products by units sold.
     *
     * @return Collection<int, object>
     */
    protected function topProducts()
    {
        return $this->productSalesQuery()
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();
    }

    /**
     * Least selling active products (includes products with zero sales).
     *
     * @return Collection<int, object>
     */
    protected function leastProducts()
    {
        return DB::table('products')
            ->leftJoin('orders', function ($join) {
                $join->on('orders.product_id', '=', 'products.id')
                    ->whereIn('orders.status', ['active', 'completed'])
                    ->whereNull('orders.deleted_at');
            })
            ->whereNull('products.deleted_at')
            ->where('products.is_active', true)
            ->groupBy('products.id', 'products.name')
            ->select(
                'products.id',
                'products.name',
                DB::raw('COUNT(orders.id) as units_sold'),
                DB::raw('COALESCE(SUM(orders.amount), 0) as revenue'),
            )
            ->orderBy('units_sold')
            ->orderBy('products.name')
            ->limit(5)
            ->get();
    }

    /**
     * Base query joining products with their sold orders.
     */
    protected function productSalesQuery()
    {
        return DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->whereIn('orders.status', ['active', 'completed'])
            ->whereNull('orders.deleted_at')
            ->groupBy('products.id', 'products.name')
            ->select(
                'products.id',
                'products.name',
                DB::raw('COUNT(orders.id) as units_sold'),
                DB::raw('COALESCE(SUM(orders.amount), 0) as revenue'),
            );
    }
}
