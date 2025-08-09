<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Cacheable;

class Category extends Model
{
    use HasFactory, Cacheable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Clear related caches when category is updated.
     *
     * @return void
     */
    protected function clearRelatedCaches()
    {
        // Clear product caches that might be affected by category changes
        cache()->forget('dashboard_stats_product');
        cache()->forget('low_stock_products');
        cache()->forget('recent_products');

        // Clear category-specific product caches
        cache()->forget("products_category_{$this->id}");
    }
}
