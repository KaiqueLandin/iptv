<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDomainController extends Controller
{
    public function index(Request $request): Response
    {
        $domains = Domain::query()
            ->with(['user', 'order'])
            ->when($request->search, function ($query, $search) {
                $query->where('domain_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->registrar, function ($query, $registrar) {
                $query->where('registrar', $registrar);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Domains/Index', [
            'domains' => $domains,
            'filters' => $request->only(['search', 'status', 'registrar']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Domains/Create', [
            'users' => User::select('id', 'name', 'email')->get(),
            'registrars' => ['RegistroBR', 'GoDaddy', 'Namecheap', 'CloudFlare', 'Other'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'domain_name' => ['required', 'string', 'unique:domains,domain_name'],
            'registrar' => ['nullable', 'string'],
            'status' => ['required', 'string'],
            'registration_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'auto_renew' => ['boolean'],
            'privacy_protection' => ['boolean'],
            'nameservers' => ['nullable', 'array'],
        ]);

        Domain::create($validated);

        return redirect()->route('admin.domains.index')
            ->with('success', 'Domínio criado com sucesso.');
    }

    public function show(Domain $domain): Response
    {
        $domain->load(['user', 'order']);

        return Inertia::render('Admin/Domains/Show', [
            'domain' => $domain,
        ]);
    }

    public function edit(Domain $domain): Response
    {
        return Inertia::render('Admin/Domains/Edit', [
            'domain' => $domain,
            'registrars' => ['RegistroBR', 'GoDaddy', 'Namecheap', 'CloudFlare', 'Other'],
        ]);
    }

    public function update(Request $request, Domain $domain): RedirectResponse
    {
        $validated = $request->validate([
            'registrar' => ['nullable', 'string'],
            'status' => ['required', 'string'],
            'registration_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'auto_renew' => ['boolean'],
            'privacy_protection' => ['boolean'],
            'nameservers' => ['nullable', 'array'],
            'dns_records' => ['nullable', 'array'],
        ]);

        $domain->update($validated);

        return redirect()->route('admin.domains.index')
            ->with('success', 'Domínio atualizado com sucesso.');
    }

    public function renew(Request $request, Domain $domain): RedirectResponse
    {
        $validated = $request->validate([
            'years' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $domain->renew($validated['years']);

        return back()->with('success', "Domínio renovado por {$validated['years']} ano(s).");
    }

    public function destroy(Domain $domain): RedirectResponse
    {
        $domain->delete();

        return redirect()->route('admin.domains.index')
            ->with('success', 'Domínio deletado com sucesso.');
    }
}
