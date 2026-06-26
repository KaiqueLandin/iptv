<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminPaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $payments = Payment::query()
            ->with(['user', 'invoice', 'gateway'])
            ->when($request->search, function ($query, $search) {
                $query->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('gateway_transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->gateway_id, function ($query, $gatewayId) {
                $query->where('payment_gateway_id', $gatewayId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_completed' => Payment::completed()->sum('amount'),
            'total_pending' => Payment::where('status', 'pending')->sum('amount'),
            'total_fees' => Payment::completed()->sum('fee'),
        ];

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'gateway_id']),
        ]);
    }

    public function show(Payment $payment): Response
    {
        $payment->load(['user', 'invoice', 'gateway']);

        return Inertia::render('Admin/Payments/Show', [
            'payment' => $payment,
        ]);
    }
}
