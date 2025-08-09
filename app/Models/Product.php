<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Cacheable;
use App\Traits\OptimizedQueries;

/**
 * Product Model
 *
 * This model represents a product in the inventory system.
 * It includes relationships to categories and various scopes for filtering.
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property int $quantity
 * @property string $unit
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Category $category
 */
class Product extends Model
{
    use HasFactory, Cacheable, OptimizedQueries;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment methods like create() or fill().
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'description',
        'price',
        'sku',
        'category_id',
        'warehouse_id',
        'quantity',
        'min_stock',
        'unit',
        'status',
        'expiry_date',
        'purchase_date',
        'last_stock_check',
        'purchase_price',
        'selling_price',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'min_stock' => 'integer',
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'expiry_date' => 'date',
        'purchase_date' => 'date',
        'last_stock_check' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the category that owns the product.
     *
     * This defines a many-to-one relationship where each product belongs to one category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the warehouse that owns the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the stock movements for the product.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get recent stock movements.
     */
    public function recentStockMovements($limit = 10)
    {
        return $this->stockMovements()
                    ->with('user')
                    ->orderBy('movement_date', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope a query to only include active products.
     *
     * Usage: Product::active()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive products.
     *
     * Usage: Product::inactive()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include discontinued products.
     *
     * Usage: Product::discontinued()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDiscontinued($query)
    {
        return $query->where('status', 'discontinued');
    }

    /**
     * Scope a query to only include products with low stock.
     *
     * Products with quantity <= 10 are considered low stock.
     * Usage: Product::lowStock()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $threshold
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('quantity', '<=', $threshold);
    }

    /**
     * Scope a query to search products by name.
     *
     * Usage: Product::searchByName('laptop')->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where('name', 'like', '%' . $name . '%');
    }

    /**
     * Get the status badge color for display purposes.
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'discontinued' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Check if the product has low stock.
     *
     * @param int $threshold
     * @return bool
     */
    public function hasLowStock($threshold = null): bool
    {
        $threshold = $threshold ?? $this->min_stock;
        return $this->quantity <= $threshold;
    }

    /**
     * Get the product image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        return asset('images/no-image.svg');
    }

    /**
     * Scope to filter products that have stock movements in a date range.
     */
    public function scopeWithMovementsInDateRange($query, $startDate, $endDate, $reasonFilter = null, $movementType = null)
    {
        return $query->whereHas('stockMovements', function ($movementQuery) use ($startDate, $endDate, $reasonFilter, $movementType) {
            // Order conditions for optimal index usage
            $movementQuery->whereBetween('movement_date', [$startDate, $endDate]);

            if ($movementType && $movementType !== 'all') {
                $movementQuery->where('type', $movementType);
            }

            if ($reasonFilter) {
                $movementQuery->where('reason', $reasonFilter);
            }
        });
    }

    /**
     * Check if product has movements in a specific date range.
     */
    public function hasMovementsInDateRange($startDate, $endDate, $reasonFilter = null, $movementType = null)
    {
        $query = $this->stockMovements()
            ->whereBetween('movement_date', [$startDate, $endDate]);

        if ($reasonFilter) {
            $query->where('reason', $reasonFilter);
        }

        if ($movementType && $movementType !== 'all') {
            $query->where('type', $movementType);
        }

        return $query->exists();
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->price ? '$' . number_format($this->price, 2) : 'N/A';
    }

    /**
     * Get stock status.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->hasLowStock()) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Get stock status color.
     */
    public function getStockStatusColorAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            'in_stock' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get formatted quantity with unit.
     *
     * @return string
     */
    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->quantity) . ' ' . $this->unit;
    }

    /**
     * Get common relations for eager loading.
     *
     * @return array
     */
    protected function getCommonRelations()
    {
        return ['category', 'warehouse'];
    }

    /**
     * Get optimized columns for selection.
     *
     * @return array
     */
    protected function getOptimizedColumns()
    {
        return [
            'id', 'name', 'sku', 'category_id', 'warehouse_id',
            'quantity', 'min_stock', 'unit', 'status', 'price',
            'selling_price', 'image', 'created_at', 'updated_at'
        ];
    }

    /**
     * Get searchable columns.
     *
     * @return array
     */
    protected function getSearchableColumns()
    {
        return ['name', 'sku', 'description'];
    }

    /**
     * Clear related caches when product is updated.
     *
     * @return void
     */
    protected function clearRelatedCaches()
    {
        // Clear category cache if category exists and has the clearModelCache method
        if ($this->category && method_exists($this->category, 'clearModelCache')) {
            $this->category->clearModelCache();
        }

        // Clear dashboard stats
        cache()->forget('dashboard_stats_product');
        cache()->forget('low_stock_products');
        cache()->forget('recent_products');

        // Clear category-specific product caches
        if ($this->category_id) {
            cache()->forget("products_category_{$this->category_id}");
        }
    }

    /**
     * Get cached low stock products.
     *
     * @param int $threshold
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCachedLowStock($threshold = 10)
    {
        return static::cacheQuery("low_stock_{$threshold}", function () use ($threshold) {
            return static::lowStock($threshold)
                ->withCommonRelations()
                ->selectOptimized()
                ->get();
        }, 300); // 5 minutes cache
    }

    /**
     * Get cached recent products.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCachedRecent($limit = 10)
    {
        return static::cacheQuery("recent_{$limit}", function () use ($limit) {
            return static::latestOptimized($limit)
                ->get();
        }, 300); // 5 minutes cache
    }

    /**
     * Get cached products by category.
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCachedByCategory($categoryId)
    {
        return static::cacheQuery("category_{$categoryId}", function () use ($categoryId) {
            return static::where('category_id', $categoryId)
                ->active()
                ->withCommonRelations()
                ->selectOptimized()
                ->get();
        });
    }

    /**
     * Get optimized dashboard statistics.
     *
     * @return array
     */
    public static function getDashboardStats()
    {
        return cache()->remember('dashboard_stats_product', 300, function () {
            return [
                'total' => static::countOptimized(),
                'active' => static::active()->countOptimized(),
                'low_stock' => static::lowStock()->countOptimized(),
                'out_of_stock' => static::where('quantity', 0)->countOptimized(),
                'recent' => static::where('created_at', '>=', now()->subDays(7))->countOptimized(),
            ];
        });
    }
}
