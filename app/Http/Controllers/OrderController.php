<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * List the authenticated customer's orders.
     */
    public function index(Request $request): Response
    {
        $orders = $request->user()->orders()
            ->with('product')
            ->latest()
            ->get()
            ->map(fn (Order $order) => $this->present($order));

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show the tracking timeline for a single order.
     */
    public function show(Request $request, Order $order): Response
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['product', 'service', 'invoices']);

        return Inertia::render('Orders/Show', [
            'order' => $this->present($order, full: true),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function present(Order $order, bool $full = false): array
    {
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'product_name' => $order->product?->name,
            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'status_color' => $order->status->color(),
            'amount' => $order->amount,
            'created_at' => $order->created_at?->toIso8601String(),
            'activated_at' => $order->activated_at?->toIso8601String(),
            'cancelled_at' => $order->cancelled_at?->toIso8601String(),
            'next_due_date' => $order->next_due_date?->toDateString(),
        ];

        if ($full) {
            $data['cancellation_reason'] = $order->cancellation_reason;
            $data['timeline'] = $this->buildTimeline($order);
            $data['service'] = $order->service ? [
                'id' => $order->service->id,
                'service_number' => $order->service->service_number,
                'status' => $order->service->status->value,
                'status_label' => $order->service->status->label(),
                'status_color' => $order->service->status->color(),
                'activation_code' => $order->service->activation_code,
                'delivered_at' => $order->service->delivered_at?->toIso8601String(),
            ] : null;
        }

        return $data;
    }

    /**
     * Build a tracking timeline from the order's lifecycle timestamps.
     *
     * @return array<int, array{key: string, label: string, done: bool, at: string|null}>
     */
    protected function buildTimeline(Order $order): array
    {
        $service = $order->service;

        return [
            [
                'key' => 'placed',
                'label' => 'Pedido realizado',
                'done' => true,
                'at' => $order->created_at?->toIso8601String(),
            ],
            [
                'key' => 'paid',
                'label' => 'Pagamento confirmado',
                'done' => $order->activated_at !== null
                    || $order->invoices->contains(fn ($invoice) => $invoice->status->value === 'paid'),
                'at' => $order->invoices
                    ->firstWhere(fn ($invoice) => $invoice->status->value === 'paid')
                    ?->paid_at?->toIso8601String(),
            ],
            [
                'key' => 'activated',
                'label' => 'Pedido ativado',
                'done' => $order->activated_at !== null,
                'at' => $order->activated_at?->toIso8601String(),
            ],
            [
                'key' => 'delivered',
                'label' => 'Acesso entregue',
                'done' => $service?->delivered_at !== null,
                'at' => $service?->delivered_at?->toIso8601String(),
            ],
        ];
    }
}
