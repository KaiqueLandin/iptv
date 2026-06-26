<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::query()
            ->with(['user', 'product'])
            ->when($request->search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['user', 'product']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function edit(Order $order): Response
    {
        $order->load(['user', 'product']);

        return Inertia::render('Admin/Orders/Edit', [
            'order' => $order,
            'users' => User::select('id', 'name', 'email')->get(),
            'products' => Product::active()->select('id', 'name', 'price')->get(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'next_due_date' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Pedido atualizado com sucesso.');
    }

    public function activate(Order $order): RedirectResponse
    {
        $order->activate();

        return back()->with('success', 'Pedido ativado com sucesso.');
    }

    public function suspend(Order $order): RedirectResponse
    {
        $order->suspend();

        return back()->with('success', 'Pedido suspenso com sucesso.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string'],
        ]);

        $order->cancel($validated['cancellation_reason'] ?? null);

        return back()->with('success', 'Pedido cancelado com sucesso.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pedido deletado com sucesso.');
    }
}
