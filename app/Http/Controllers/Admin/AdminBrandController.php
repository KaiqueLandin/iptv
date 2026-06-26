<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminBrandController extends Controller
{
    public function index(Request $request): Response
    {
        $brands = Brand::query()
            ->withCount('products')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Brands/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'website_url' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = Brand::generateUniqueSlug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marca criada com sucesso.');
    }

    public function edit(Brand $brand): Response
    {
        return Inertia::render('Admin/Brands/Edit', [
            'brand' => $brand,
        ]);
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'website_url' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validated['name'] !== $brand->name) {
            $validated['slug'] = Brand::generateUniqueSlug($validated['name'], $brand->id);
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marca atualizada com sucesso.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->with('error', 'Não é possível deletar uma marca com produtos associados.');
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marca deletada com sucesso.');
    }
}
