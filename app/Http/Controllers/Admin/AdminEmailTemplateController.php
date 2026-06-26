<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminEmailTemplateController extends Controller
{
    public function index(Request $request): Response
    {
        $templates = EmailTemplate::query()
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/EmailTemplates/Index', [
            'templates' => $templates,
            'filters' => $request->only(['type', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/EmailTemplates/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string'],
            'body_html' => ['required', 'string'],
            'body_text' => ['nullable', 'string'],
            'available_variables' => ['nullable', 'array'],
            'type' => ['required', 'in:invoice,order,ticket,general,notification'],
            'is_active' => ['boolean'],
        ]);

        $validated['slug'] = EmailTemplate::generateUniqueSlug($validated['name']);

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template de email criado com sucesso.');
    }

    public function edit(EmailTemplate $emailTemplate): Response
    {
        return Inertia::render('Admin/EmailTemplates/Edit', [
            'template' => $emailTemplate,
        ]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string'],
            'body_html' => ['required', 'string'],
            'body_text' => ['nullable', 'string'],
            'available_variables' => ['nullable', 'array'],
            'type' => ['required', 'in:invoice,order,ticket,general,notification'],
            'is_active' => ['boolean'],
        ]);

        if ($validated['name'] !== $emailTemplate->name) {
            $validated['slug'] = EmailTemplate::generateUniqueSlug($validated['name'], $emailTemplate->id);
        }

        $emailTemplate->update($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template atualizado com sucesso.');
    }

    public function destroy(EmailTemplate $emailTemplate): RedirectResponse
    {
        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template deletado com sucesso.');
    }
}
