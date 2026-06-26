<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Notifications\InvoiceSentNotification;
use App\Services\Provisioning\ProvisioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminInvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $invoices = Invoice::query()
            ->with('user')
            ->when($request->search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%")
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

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Invoices/Create', [
            'users' => User::select('id', 'name', 'email')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'due_date' => ['required', 'date'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $invoice = Invoice::create([
            'user_id' => $validated['user_id'],
            'due_date' => $validated['due_date'],
            'tax' => $validated['tax'] ?? 0,
            'discount' => $validated['discount'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Fatura criada com sucesso.');
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load(['user', 'items']);

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit(Invoice $invoice): Response
    {
        $invoice->load(['user', 'items']);

        return Inertia::render('Admin/Invoices/Edit', [
            'invoice' => $invoice,
            'users' => User::select('id', 'name', 'email')->get(),
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'due_date' => ['required', 'date'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice->update($validated);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Fatura atualizada com sucesso.');
    }

    public function markPaid(Request $request, Invoice $invoice, ProvisioningService $provisioning): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['nullable', 'string'],
        ]);

        $invoice->markAsPaid($validated['payment_method'] ?? null);

        // Provision + deliver the service (generates the IPTV code and emails it).
        $provisioning->fulfillInvoice($invoice);

        return back()->with('success', 'Fatura marcada como paga e serviço provisionado.');
    }

    public function send(Invoice $invoice): RedirectResponse
    {
        $invoice->load('user');

        if (! $invoice->user) {
            return back()->with('error', 'Usuário não encontrado para esta fatura.');
        }

        $invoice->user->notify(new InvoiceSentNotification($invoice));

        return back()->with('success', 'Fatura enviada por email com sucesso.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Fatura deletada com sucesso.');
    }
}
