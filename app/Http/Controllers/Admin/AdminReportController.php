<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SalesMetrics;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Reports/Index');
    }

    public function revenue(Request $request): Response
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        $data = SalesMetrics::revenueByDay(SalesMetrics::periodStart($period));

        $totals = SalesMetrics::totals();

        $stats = [
            'total_revenue' => $totals['total_revenue'],
            'monthly_revenue' => $totals['monthly_revenue'],
            'yearly_revenue' => $totals['yearly_revenue'],
            'avg_invoice' => $totals['avg_invoice'],
        ];

        return Inertia::render('Admin/Reports/Revenue', [
            'data' => $data,
            'stats' => $stats,
            'period' => $period,
        ]);
    }

    public function customers(Request $request): Response
    {
        $period = $request->get('period', 'month');

        $data = SalesMetrics::newCustomersByDay(SalesMetrics::periodStart($period));

        $stats = SalesMetrics::customerTotals();

        return Inertia::render('Admin/Reports/Customers', [
            'data' => $data,
            'stats' => $stats,
            'period' => $period,
        ]);
    }

    public function products(): Response
    {
        $topProducts = SalesMetrics::topProducts(10);

        return Inertia::render('Admin/Reports/Products', [
            'topProducts' => $topProducts,
        ]);
    }
}
