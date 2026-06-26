<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminWebhookController extends Controller
{
    public function index(): Response
    {
        $webhooks = Webhook::withCount('logs')->latest()->get();

        return Inertia::render('Admin/Webhooks/Index', [
            'webhooks' => $webhooks,
        ]);
    }

    public function create(): Response
    {
        $events = [
            'invoice.created',
            'invoice.paid',
            'invoice.overdue',
            'order.created',
            'order.activated',
            'order.suspended',
            'order.cancelled',
            'payment.completed',
            'payment.failed',
            'ticket.created',
            'ticket.replied',
            'ticket.closed',
            'user.registered',
        ];

        return Inertia::render('Admin/Webhooks/Create', [
            'events' => $events,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event' => ['required', 'string'],
            'url' => ['required', 'url'],
            'secret' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'headers' => ['nullable', 'array'],
            'timeout' => ['nullable', 'integer', 'min:1', 'max:120'],
            'max_retries' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $validated['timeout'] = $validated['timeout'] ?? 30;
        $validated['max_retries'] = $validated['max_retries'] ?? 3;

        Webhook::create($validated);

        return redirect()->route('admin.webhooks.index')
            ->with('success', 'Webhook criado com sucesso.');
    }

    public function edit(Webhook $webhook): Response
    {
        return Inertia::render('Admin/Webhooks/Edit', [
            'webhook' => $webhook,
        ]);
    }

    public function update(Request $request, Webhook $webhook): RedirectResponse
    {
        $validated = $request->validate([
            'event' => ['required', 'string'],
            'url' => ['required', 'url'],
            'secret' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'headers' => ['nullable', 'array'],
            'timeout' => ['nullable', 'integer', 'min:1', 'max:120'],
            'max_retries' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $webhook->update($validated);

        return redirect()->route('admin.webhooks.index')
            ->with('success', 'Webhook atualizado com sucesso.');
    }

    public function logs(Webhook $webhook, Request $request): Response
    {
        $logs = $webhook->logs()
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Webhooks/Logs', [
            'webhook' => $webhook,
            'logs' => $logs,
            'filters' => $request->only(['status']),
        ]);
    }

    public function destroy(Webhook $webhook): RedirectResponse
    {
        $webhook->delete();

        return redirect()->route('admin.webhooks.index')
            ->with('success', 'Webhook deletado com sucesso.');
    }
}
