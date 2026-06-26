<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\SalesMetrics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON analytics endpoints for the admin dashboard charts. Reuses
 * SalesMetrics so the API and the Inertia report pages share a single
 * definition of revenue and cannot drift.
 */
class AnalyticsController extends Controller
{
    /**
     * Allowed reporting periods.
     *
     * @var array<int, string>
     */
    protected array $periods = ['day', 'week', 'month', 'year'];

    /**
     * Paid revenue grouped by day for the requested period, plus headline totals.
     */
    public function revenue(Request $request): JsonResponse
    {
        $period = $this->resolvePeriod($request);

        $series = SalesMetrics::revenueByDay(SalesMetrics::periodStart($period))
            ->map(fn ($row) => [
                'date' => $row->date,
                'total' => (float) $row->total,
            ]);

        $totals = SalesMetrics::totals();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'series' => $series,
                'totals' => [
                    'total_revenue' => $totals['total_revenue'],
                    'monthly_revenue' => $totals['monthly_revenue'],
                    'yearly_revenue' => $totals['yearly_revenue'],
                    'today_revenue' => $totals['today_revenue'],
                    'avg_invoice' => $totals['avg_invoice'],
                ],
            ],
        ]);
    }

    /**
     * New customer signups grouped by day for the requested period, plus totals.
     */
    public function customers(Request $request): JsonResponse
    {
        $period = $this->resolvePeriod($request);

        $series = SalesMetrics::newCustomersByDay(SalesMetrics::periodStart($period))
            ->map(fn ($row) => [
                'date' => $row->date,
                'count' => (int) $row->count,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'series' => $series,
                'totals' => SalesMetrics::customerTotals(),
            ],
        ]);
    }

    /**
     * Top products ranked by number of orders.
     */
    public function products(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);
        $limit = max(1, min($limit, 50));

        $products = SalesMetrics::topProducts($limit)
            ->map(fn ($row) => [
                'id' => (int) $row->id,
                'name' => $row->name,
                'orders_count' => (int) $row->orders_count,
                'revenue' => (float) $row->revenue,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
            ],
        ]);
    }

    /**
     * Resolve and validate the requested period, defaulting to "month".
     */
    protected function resolvePeriod(Request $request): string
    {
        $period = (string) $request->get('period', 'month');

        return in_array($period, $this->periods, true) ? $period : 'month';
    }
}
