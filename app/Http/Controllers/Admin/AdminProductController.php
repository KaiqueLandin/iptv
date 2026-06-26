<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BillingCycle;
use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::query()
            ->with('brand')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'types' => collect(ProductType::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
            'billing_cycles' => collect(BillingCycle::cases())->map(fn ($cycle) => [
                'value' => $cycle->value,
                'label' => $cycle->label(),
            ]),
            'brands' => $this->brandOptions(),
            'crossSellOptions' => $this->crossSellOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'setup_fee' => ['nullable', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'string'],
            'type' => ['required', 'string'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'cross_sell_ids' => ['nullable', 'array'],
            'cross_sell_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $crossSellIds = $validated['cross_sell_ids'] ?? [];
        unset($validated['cross_sell_ids']);

        $validated['slug'] = Product::generateUniqueSlug($validated['name']);
        $validated['setup_fee'] = $validated['setup_fee'] ?? 0;

        $product = Product::create($validated);

        $product->crossSells()->sync($this->crossSellSyncData($crossSellIds, $product->id));

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso.');
    }

    public function show(Product $product): Response
    {
        $product->load(['orders', 'subscriptions']);

        return Inertia::render('Admin/Products/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): Response
    {
        $product->load('crossSells:id');

        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                ...$product->only([
                    'id', 'name', 'description', 'brand_id', 'price', 'setup_fee',
                    'billing_cycle', 'type', 'is_active', 'is_featured', 'stock',
                ]),
                'cross_sell_ids' => $product->crossSells->pluck('id'),
            ],
            'types' => collect(ProductType::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
            'billing_cycles' => collect(BillingCycle::cases())->map(fn ($cycle) => [
                'value' => $cycle->value,
                'label' => $cycle->label(),
            ]),
            'brands' => $this->brandOptions(),
            'crossSellOptions' => $this->crossSellOptions($product->id),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'setup_fee' => ['nullable', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'string'],
            'type' => ['required', 'string'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'cross_sell_ids' => ['nullable', 'array'],
            'cross_sell_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $crossSellIds = array_values(array_filter(
            $validated['cross_sell_ids'] ?? [],
            fn ($id) => (int) $id !== $product->id,
        ));
        unset($validated['cross_sell_ids']);

        if ($validated['name'] !== $product->name) {
            $validated['slug'] = Product::generateUniqueSlug($validated['name'], $product->id);
        }

        $product->update($validated);

        $product->crossSells()->sync($this->crossSellSyncData($crossSellIds, $product->id));

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orders()->exists() || $product->subscriptions()->exists()) {
            return back()->with('error', 'Não é possível deletar um produto com pedidos ou assinaturas associadas.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto deletado com sucesso.');
    }

    /**
     * Brand options for product forms.
     *
     * @return \Illuminate\Support\Collection<int, array{value: int, label: string}>
     */
    protected function brandOptions(): \Illuminate\Support\Collection
    {
        return Brand::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Brand $brand) => [
                'value' => $brand->id,
                'label' => $brand->name,
            ]);
    }

    /**
     * Product options available as cross-sells (excludes the current product).
     *
     * @return \Illuminate\Support\Collection<int, array{value: int, label: string}>
     */
    protected function crossSellOptions(?int $ignoreId = null): \Illuminate\Support\Collection
    {
        return Product::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Product $product) => [
                'value' => $product->id,
                'label' => $product->name,
            ]);
    }

    /**
     * Build the sync payload for the cross-sell pivot, applying sort order.
     *
     * @param  array<int, int|string>  $ids
     * @return array<int, array{sort_order: int}>
     */
    protected function crossSellSyncData(array $ids, int $productId): array
    {
        $data = [];
        $order = 0;

        foreach ($ids as $id) {
            $id = (int) $id;

            if ($id === $productId || isset($data[$id])) {
                continue;
            }

            $data[$id] = ['sort_order' => $order++];
        }

        return $data;
    }
}
