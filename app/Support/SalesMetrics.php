<?php

namespace App\Support;

use App\Models\Invoice;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Centralizes paid-invoice revenue metrics so the admin dashboard and the
 * reports screen share a single definition of "revenue" and cannot drift.
 */
class SalesMetrics
{
    /**
     * Base query for revenue: only paid invoices.
     */
    public static function paidInvoices()
    {
        return Invoice::query()->where('status', 'paid');
    }

    /**
     * Paid revenue grouped by day since the given start date.
     *
     * @return Collection<int, object{date: string, total: string}>
     */
    public static function revenueByDay(DateTimeInterface $since): Collection
    {
        return static::paidInvoices()
            ->whereDate('paid_at', '>=', $since)
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(total) as total'),
            ]);
    }

    /**
     * Resolve the start date for a named reporting period.
     */
    public static function periodStart(string $period): DateTimeInterface
    {
        return match ($period) {
            'day' => now()->subDays(30),
            'week' => now()->subWeeks(12),
            'year' => now()->subYears(2),
            default => now()->subMonths(12),
        };
    }

    /**
     * Headline revenue totals used across dashboards.
     *
     * @return array<string, float>
     */
    public static function totals(): array
    {
        return [
            'total_revenue' => (float) static::paidInvoices()->sum('total'),
            'monthly_revenue' => (float) static::paidInvoices()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('total'),
            'yearly_revenue' => (float) static::paidInvoices()
                ->whereYear('paid_at', now()->year)
                ->sum('total'),
            'today_revenue' => (float) static::paidInvoices()
                ->whereDate('paid_at', today())
                ->sum('total'),
            'avg_invoice' => (float) static::paidInvoices()->avg('total'),
        ];
    }

    /**
     * New customer signups grouped by day since the given start date.
     *
     * @return Collection<int, object{date: string, count: int}>
     */
    public static function newCustomersByDay(DateTimeInterface $since): Collection
    {
        return DB::table('users')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $since)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Headline customer counts used across dashboards.
     *
     * @return array<string, int>
     */
    public static function customerTotals(): array
    {
        return [
            'total_customers' => (int) DB::table('users')->count(),
            'active_customers' => (int) DB::table('users')->where('is_active', true)->count(),
            'new_this_month' => (int) DB::table('users')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'new_today' => (int) DB::table('users')->whereDate('created_at', today())->count(),
        ];
    }

    /**
     * Top products ranked by number of orders.
     *
     * @return Collection<int, object{id: int, name: string, orders_count: int, revenue: string}>
     */
    public static function topProducts(int $limit = 10): Collection
    {
        return DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(orders.amount) as revenue'),
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('orders_count')
            ->limit($limit)
            ->get();
    }
}
