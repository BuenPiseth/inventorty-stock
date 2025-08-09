<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Stock Movement Model
 *
 * This model tracks all stock movements (in/out) for products.
 * It maintains a complete audit trail of inventory changes.
 */
class StockMovement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'reference',
        'notes',
        'reason',
        'user_id',
        'movement_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'new_stock' => 'integer',
        'unit_cost' => 'decimal:2',
        'movement_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the stock movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse that owns the stock movement.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the user who created the stock movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include stock in movements.
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope a query to only include stock out movements.
     */
    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by reason.
     */
    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    /**
     * Scope a query to filter by movement type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get all available reasons.
     */
    public static function getAvailableReasons()
    {
        return ['St360', 'Bakery', 'Cake', 'Koh Pich', 'Cashier'];
    }

    /**
     * Get all available movement types.
     */
    public static function getMovementTypes()
    {
        return [
            'all' => 'All Movements',
            'in' => 'Stock In Only',
            'out' => 'Stock Out Only'
        ];
    }

    /**
     * Get formatted movement type.
     */
    public function getFormattedTypeAttribute(): string
    {
        return $this->type === 'in' ? 'Stock In' : 'Stock Out';
    }

    /**
     * Get the total value of the movement.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->quantity * ($this->unit_cost ?? 0);
    }
}
