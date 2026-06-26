<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the public landing page with dynamic plans.
     */
    public function __invoke(): Response
    {
        $plans = Product::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get()
            ->map(fn (Product $product) => $this->mapProductToPlan($product))
            ->values();

        $brands = Brand::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Brand $brand) => [
                'id' => $brand->id,
                'name' => $brand->name,
                'logo_url' => $brand->logo_url,
                'website_url' => $brand->website_url,
            ])
            ->values();

        return Inertia::render('Welcome', [
            'plans' => $plans,
            'brands' => $brands,
        ]);
    }

    /**
     * Map a native Product into the landing page plan shape.
     *
     * @return array<string, mixed>
     */
    protected function mapProductToPlan(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'credits' => $this->resolveCredits($product),
            'price' => number_format((float) $product->price, 2, ',', '.'),
            'description' => $product->description
                ? strip_tags($product->description)
                : null,
            'checkoutUrl' => route('checkout.buy', $product),
            'popular' => (bool) $product->is_featured,
        ];
    }

    /**
     * Resolve the amount of credits represented by a product.
     *
     * Looks at the JSON features first (e.g. {"credits": 25}), then falls
     * back to parsing the product name (e.g. "Plano 25 Créditos").
     */
    protected function resolveCredits(Product $product): int
    {
        $features = $product->features ?? [];

        if (is_array($features)) {
            foreach (['credits', 'creditos', 'créditos'] as $key) {
                if (isset($features[$key]) && is_numeric($features[$key])) {
                    return (int) $features[$key];
                }
            }
        }

        if (preg_match('/(\d+)\s*(cr[eé]ditos?|credits?)/i', (string) $product->name, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
