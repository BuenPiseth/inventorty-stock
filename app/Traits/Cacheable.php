<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

trait Cacheable
{
    /**
     * Cache key prefix for this model.
     *
     * @var string
     */
    protected $cachePrefix;

    /**
     * Default cache TTL in seconds.
     *
     * @var int
     */
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Boot the cacheable trait.
     *
     * @return void
     */
    public static function bootCacheable()
    {
        // Clear cache when model is created, updated, or deleted
        static::created(function ($model) {
            $model->clearModelCache();
        });

        static::updated(function ($model) {
            $model->clearModelCache();
        });

        static::deleted(function ($model) {
            $model->clearModelCache();
        });
    }

    /**
     * Get cache key for this model.
     *
     * @param  string  $suffix
     * @return string
     */
    public function getCacheKey($suffix = '')
    {
        $prefix = $this->cachePrefix ?: strtolower(class_basename($this));
        $key = $prefix . '_' . $this->getKey();
        
        return $suffix ? $key . '_' . $suffix : $key;
    }

    /**
     * Get cache key for model collection.
     *
     * @param  string  $suffix
     * @return string
     */
    public static function getCollectionCacheKey($suffix = '')
    {
        $prefix = strtolower(class_basename(static::class));
        $key = $prefix . '_collection';
        
        return $suffix ? $key . '_' . $suffix : $key;
    }

    /**
     * Cache a value for this model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int     $ttl
     * @return mixed
     */
    public function cacheValue($key, $value, $ttl = null)
    {
        if (!Config::get('performance.cache.queries.enabled', true)) {
            return $value;
        }

        $ttl = $ttl ?: $this->cacheTtl;
        $cacheKey = $this->getCacheKey($key);
        
        return Cache::remember($cacheKey, $ttl, function () use ($value) {
            return is_callable($value) ? $value() : $value;
        });
    }

    /**
     * Get cached value for this model.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function getCachedValue($key, $default = null)
    {
        if (!Config::get('performance.cache.queries.enabled', true)) {
            return $default;
        }

        $cacheKey = $this->getCacheKey($key);
        return Cache::get($cacheKey, $default);
    }

    /**
     * Clear cache for this model.
     *
     * @param  string  $suffix
     * @return void
     */
    public function clearCache($suffix = '')
    {
        $cacheKey = $this->getCacheKey($suffix);
        Cache::forget($cacheKey);
    }

    /**
     * Clear all cache related to this model.
     *
     * @return void
     */
    public function clearModelCache()
    {
        $prefix = $this->cachePrefix ?: strtolower(class_basename($this));
        
        // Clear individual model cache
        $this->clearCache();
        
        // Clear collection cache
        Cache::forget(static::getCollectionCacheKey());
        
        // Clear related caches
        $this->clearRelatedCaches();
        
        // Clear tagged cache if using tags
        if (method_exists(Cache::getStore(), 'tags')) {
            $tag = Config::get("performance.cache.queries.tags.{$prefix}");
            if ($tag) {
                Cache::tags($tag)->flush();
            }
        }
    }

    /**
     * Clear related model caches.
     * Override this method in models to clear related caches.
     *
     * @return void
     */
    protected function clearRelatedCaches()
    {
        // Override in models to clear related caches
    }

    /**
     * Cache a query result.
     *
     * @param  string    $key
     * @param  callable  $callback
     * @param  int       $ttl
     * @return mixed
     */
    public static function cacheQuery($key, callable $callback, $ttl = null)
    {
        if (!Config::get('performance.cache.queries.enabled', true)) {
            return $callback();
        }

        $ttl = $ttl ?: Config::get('performance.cache.queries.ttl', 3600);
        $cacheKey = static::getCollectionCacheKey($key);
        
        return Cache::remember($cacheKey, $ttl, $callback);
    }

    /**
     * Get cached count for this model.
     *
     * @return int
     */
    public static function getCachedCount()
    {
        return static::cacheQuery('count', function () {
            return static::count();
        });
    }

    /**
     * Get cached all records for this model.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCachedAll()
    {
        return static::cacheQuery('all', function () {
            return static::all();
        });
    }

    /**
     * Get cached active records for this model.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCachedActive()
    {
        return static::cacheQuery('active', function () {
            return static::where('status', 'active')->get();
        });
    }

    /**
     * Warm up cache for this model.
     *
     * @return void
     */
    public static function warmUpCache()
    {
        // Warm up common queries
        static::getCachedCount();
        static::getCachedAll();
        
        if (method_exists(static::class, 'getCachedActive')) {
            static::getCachedActive();
        }
    }
}
