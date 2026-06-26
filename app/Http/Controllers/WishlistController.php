<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    /**
     * List the authenticated user's wishlisted products.
     */
    public function index(Request $request): Response
    {
        $products = $request->user()->wishlistProducts()
            ->with('brand')
            ->orderByPivot('created_at', 'desc')
            ->get()
            ->map(fn (Product $product) => $this->present($product));

        return Inertia::render('Wishlist/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Add a product to the wishlist (idempotent).
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $request->user()->wishlistProducts()->syncWithoutDetaching([$product->id]);

        return back()->with('success', 'Produto adicionado aos favoritos.');
    }

    /**
     * Remove a product from the wishlist.
     */
    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $request->user()->wishlistProducts()->detach($product->id);

        return back()->with('success', 'Produto removido dos favoritos.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function present(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'description' => $product->description
                ? strip_tags($product->description)
                : null,
            'brand_name' => $product->brand?->name,
            'is_active' => $product->is_active,
            'checkoutUrl' => route('checkout.store', $product),
        ];
    }
}
