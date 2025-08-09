<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'phone',
        'email',
        'manager_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the products for the warehouse.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the stock movements for the warehouse.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get active warehouses only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total products count for this warehouse.
     */
    public function getTotalProductsAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Get total stock value for this warehouse.
     */
    public function getTotalStockValueAttribute()
    {
        return $this->products()->sum(DB::raw('quantity * price'));
    }

    /**
     * Get low stock products count for this warehouse.
     */
    public function getLowStockCountAttribute()
    {
        return $this->products()->where('quantity', '<=', 5)->count();
    }
}
