<?php

namespace App\Services\Whmcs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductCatalogService
{
    protected WhmcsClient $client;

    protected int $cacheTtl = 3600; // 1 hour

    protected string $cacheKey = 'whmcs_product_catalog';

    public function __construct(WhmcsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get normalized product catalog with cache and fallback
     */
    public function getCatalog(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            return $this->fetchAndCacheCatalog();
        }

        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return $this->fetchAndCacheCatalog();
        });
    }

    /**
     * Fetch products from WHMCS and normalize
     */
    protected function fetchAndCacheCatalog(): array
    {
        try {
            $response = $this->client->getProducts();

            if (! isset($response['products']['product'])) {
                throw new \Exception('Invalid WHMCS response structure');
            }

            $products = $response['products']['product'];
            $normalized = $this->normalizeProducts($products);

            // Store in persistent cache as fallback
            Cache::forever($this->cacheKey.'_fallback', $normalized);

            return $normalized;
        } catch (\Exception $e) {
            Log::error('Failed to fetch WHMCS catalog', [
                'error' => $e->getMessage(),
            ]);

            // Return cached fallback
            return $this->getFallbackCatalog();
        }
    }

    /**
     * Get fallback catalog when WHMCS is unavailable
     */
    protected function getFallbackCatalog(): array
    {
        $fallback = Cache::get($this->cacheKey.'_fallback');

        if ($fallback) {
            Log::warning('Using fallback WHMCS catalog');

            return $fallback;
        }

        // Default fallback if no cache exists
        return $this->getDefaultCatalog();
    }

    /**
     * Normalize WHMCS products to frontend format
     */
    protected function normalizeProducts(array $products): array
    {
        $normalized = [];

        foreach ($products as $product) {
            // Extract credits from product name or custom field
            $credits = $this->extractCreditsFromProduct($product);

            if ($credits === null) {
                continue; // Skip products without credits
            }

            $normalized[] = [
                'id' => (int) $product['pid'],
                'name' => $this->getProductLabel($credits),
                'credits' => $credits,
                'price' => $this->formatPrice($product['pricing']['BRL']['monthly'] ?? $product['pricing']['USD']['monthly'] ?? '0'),
                'checkoutUrl' => $this->buildCheckoutUrl($product['pid']),
                'description' => strip_tags($product['description'] ?? ''),
            ];
        }

        // Sort by credits
        usort($normalized, fn ($a, $b) => $a['credits'] <=> $b['credits']);

        return $normalized;
    }

    /**
     * Extract credits amount from product
     */
    protected function extractCreditsFromProduct(array $product): ?int
    {
        // Check custom fields first
        if (isset($product['customfields']['customfield'])) {
            foreach ($product['customfields']['customfield'] as $field) {
                if (strtolower($field['name']) === 'credits' || strtolower($field['name']) === 'créditos') {
                    return (int) $field['value'];
                }
            }
        }

        // Try to extract from name (e.g., "10 Créditos", "Pacote 25")
        if (preg_match('/(\d+)\s*(cr[eé]ditos?|credits?)/i', $product['name'], $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Format price for display
     */
    protected function formatPrice(string $price): string
    {
        $amount = (float) $price;

        return number_format($amount, 2, ',', '.');
    }

    /**
     * Get product label based on credits
     */
    protected function getProductLabel(int $credits): string
    {
        return match (true) {
            $credits >= 100 => 'Premium',
            $credits >= 50 => 'Avançado',
            $credits >= 25 => 'Popular',
            default => 'Básico',
        };
    }

    /**
     * Build checkout URL for product
     */
    protected function buildCheckoutUrl(int $productId): string
    {
        $baseUrl = rtrim(config('services.whmcs.api_url'), '/includes/api.php');

        return "{$baseUrl}/cart.php?a=add&pid={$productId}";
    }

    /**
     * Default catalog when everything fails
     */
    protected function getDefaultCatalog(): array
    {
        return [
            [
                'id' => 0,
                'name' => 'Básico',
                'credits' => 10,
                'price' => '29,90',
                'checkoutUrl' => '#',
                'description' => 'Plano básico',
            ],
            [
                'id' => 0,
                'name' => 'Popular',
                'credits' => 25,
                'price' => '59,90',
                'checkoutUrl' => '#',
                'description' => 'Plano mais popular',
            ],
            [
                'id' => 0,
                'name' => 'Avançado',
                'credits' => 50,
                'price' => '99,90',
                'checkoutUrl' => '#',
                'description' => 'Plano avançado',
            ],
            [
                'id' => 0,
                'name' => 'Premium',
                'credits' => 100,
                'price' => '179,90',
                'checkoutUrl' => '#',
                'description' => 'Plano premium',
            ],
        ];
    }
}
