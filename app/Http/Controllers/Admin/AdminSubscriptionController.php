<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $subscriptions = Subscription::query()
            ->with(['user', 'product'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
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

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(Subscription $subscription): Response
    {
        $subscription->load(['user', 'product', 'order']);

        return Inertia::render('Admin/Subscriptions/Show', [
            'subscription' => $subscription,
        ]);
    }

    public function edit(Subscription $subscription): Response
    {
        $subscription->load(['user', 'product']);

        return Inertia::render('Admin/Subscriptions/Edit', [
            'subscription' => $subscription,
            'users' => User::select('id', 'name', 'email')->get(),
            'products' => Product::active()->select('id', 'name', 'price')->get(),
        ]);
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'next_billing_date' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $subscription->update($validated);

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Assinatura atualizada com sucesso.');
    }

    public function pause(Subscription $subscription): RedirectResponse
    {
        $subscription->pause();

        return back()->with('success', 'Assinatura pausada com sucesso.');
    }

    public function resume(Subscription $subscription): RedirectResponse
    {
        $subscription->resume();

        return back()->with('success', 'Assinatura retomada com sucesso.');
    }

    public function cancel(Request $request, Subscription $subscription): RedirectResponse
    {
        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string'],
        ]);

        $subscription->cancel($validated['cancellation_reason'] ?? null);

        return back()->with('success', 'Assinatura cancelada com sucesso.');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Assinatura deletada com sucesso.');
    }
}
