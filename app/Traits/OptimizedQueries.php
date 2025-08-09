<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

trait OptimizedQueries
{
    /**
     * Scope to eager load common relationships.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCommonRelations(Builder $query)
    {
        if (!Config::get('performance.optimization.database.eager_loading', true)) {
            return $query;
        }

        $relations = $this->getCommonRelations();
        
        if (!empty($relations)) {
            return $query->with($relations);
        }

        return $query;
    }

    /**
     * Get common relations that should be eager loaded.
     * Override this method in models to define common relations.
     *
     * @return array
     */
    protected function getCommonRelations()
    {
        return [];
    }

    /**
     * Scope to select only necessary columns.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSelectOptimized(Builder $query, array $columns = [])
    {
        if (empty($columns)) {
            $columns = $this->getOptimizedColumns();
        }

        return $query->select($columns);
    }

    /**
     * Get optimized columns for selection.
     * Override this method in models to define optimized columns.
     *
     * @return array
     */
    protected function getOptimizedColumns()
    {
        return ['*'];
    }

    /**
     * Scope for paginated results with optimization.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $perPage
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaginateOptimized(Builder $query, $perPage = 15)
    {
        return $query
            ->selectOptimized()
            ->withCommonRelations()
            ->paginate($perPage);
    }

    /**
     * Scope to add index hints for better query performance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $index
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUseIndex(Builder $query, $index, $type = 'USE')
    {
        if (!Config::get('performance.optimization.database.index_hints', true)) {
            return $query;
        }

        $table = $this->getTable();
        return $query->from("{$table} {$type} INDEX ({$index})");
    }

    /**
     * Scope for efficient counting with optimization.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return int
     */
    public function scopeCountOptimized(Builder $query)
    {
        // Use primary key for counting when possible
        $primaryKey = $this->getKeyName();
        return $query->count($primaryKey);
    }

    /**
     * Scope for efficient exists check.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return bool
     */
    public function scopeExistsOptimized(Builder $query)
    {
        // Limit to 1 for efficiency
        return $query->limit(1)->exists();
    }

    /**
     * Scope to chunk results for memory efficiency.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $count
     * @param  callable  $callback
     * @return bool
     */
    public function scopeChunkOptimized(Builder $query, $count, callable $callback)
    {
        return $query
            ->selectOptimized()
            ->chunk($count, $callback);
    }

    /**
     * Scope for latest records with optimization.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestOptimized(Builder $query, $limit = 10)
    {
        return $query
            ->selectOptimized()
            ->withCommonRelations()
            ->latest()
            ->limit($limit);
    }

    /**
     * Scope for search functionality with optimization.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $term
     * @param  array   $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchOptimized(Builder $query, $term, array $columns = [])
    {
        if (empty($term)) {
            return $query;
        }

        if (empty($columns)) {
            $columns = $this->getSearchableColumns();
        }

        return $query->where(function ($q) use ($term, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'LIKE', "%{$term}%");
            }
        });
    }

    /**
     * Get searchable columns.
     * Override this method in models to define searchable columns.
     *
     * @return array
     */
    protected function getSearchableColumns()
    {
        return ['name'];
    }

    /**
     * Scope for filtering with optimization.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterOptimized(Builder $query, array $filters)
    {
        foreach ($filters as $column => $value) {
            if ($value !== null && $value !== '') {
                if (is_array($value)) {
                    $query->whereIn($column, $value);
                } else {
                    $query->where($column, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Get optimized query for dashboard statistics.
     *
     * @return array
     */
    public static function getDashboardStats()
    {
        $cacheKey = 'dashboard_stats_' . strtolower(class_basename(static::class));
        
        return cache()->remember($cacheKey, 300, function () { // 5 minutes cache
            return [
                'total' => static::countOptimized(),
                'recent' => static::where('created_at', '>=', now()->subDays(7))->countOptimized(),
                'active' => method_exists(static::class, 'scopeActive') 
                    ? static::active()->countOptimized() 
                    : null,
            ];
        });
    }

    /**
     * Optimize query for bulk operations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBulkOptimized(Builder $query)
    {
        return $query
            ->selectOptimized()
            ->withoutGlobalScopes(); // Remove global scopes for bulk operations
    }

    /**
     * Get memory-efficient iterator for large datasets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $chunkSize
     * @return \Generator
     */
    public function scopeLazyOptimized(Builder $query, $chunkSize = 1000)
    {
        return $query
            ->selectOptimized()
            ->lazy($chunkSize);
    }
}
