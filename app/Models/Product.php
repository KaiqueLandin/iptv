<?php

namespace App\Models;

use App\Enums\BillingCycle;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'description',
        'price',
        'setup_fee',
        'billing_cycle',
        'type',
        'is_active',
        'is_featured',
        'sort_order',
        'features',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'billing_cycle' => BillingCycle::class,
        'type' => ProductType::class,
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'features' => 'array',
        'stock' => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Products recommended as cross-sells for this product.
     *
     * @return BelongsToMany<Product>
     */
    public function crossSells(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_cross_sells',
            'product_id',
            'cross_sell_product_id',
        )->withPivot('sort_order')->withTimestamps()->orderBy('product_cross_sells.sort_order');
    }

    /**
     * Wishlist entries that reference this product.
     */
    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Generate a slug from the given name that is unique across products.
     *
     * @param  int|null  $ignoreId  Product id to ignore (used when updating).
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'produto';
        $slug = $base;
        $suffix = 1;

        while (
            static::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.++$suffix;
        }

        return $slug;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function hasStock(): bool
    {
        if ($this->stock === null) {
            return true; // unlimited
        }

        return $this->stock > 0;
    }

    public function decrementStock(int $quantity = 1): void
    {
        if ($this->stock !== null) {
            $this->decrement('stock', $quantity);
        }
    }
}
