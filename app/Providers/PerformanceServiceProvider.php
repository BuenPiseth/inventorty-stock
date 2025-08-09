<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register performance configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../config/performance.php', 'performance'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register view composers for performance optimization
        $this->registerViewComposers();

        // Register Blade directives for lazy loading
        $this->registerBladeDirectives();

        // Monitor database queries if enabled
        if (Config::get('performance.monitoring.enabled')) {
            $this->monitorDatabaseQueries();
        }

        // Optimize asset loading
        $this->optimizeAssetLoading();
    }

    /**
     * Register view composers for performance optimization.
     *
     * @return void
     */
    protected function registerViewComposers()
    {
        // Share performance configuration with views
        View::composer('*', function ($view) {
            $view->with('performanceConfig', Config::get('performance'));
        });

        // Optimize product count queries
        View::composer(['layouts.modern', 'layouts.modern-optimized'], function ($view) {
            if (Config::get('performance.cache.queries.enabled')) {
                $productCount = cache()->remember('product_count', 3600, function () {
                    return \App\Models\Product::count();
                });

                $categoryCount = cache()->remember('category_count', 3600, function () {
                    return \App\Models\Category::count();
                });

                $view->with(compact('productCount', 'categoryCount'));
            }
        });
    }

    /**
     * Register Blade directives for lazy loading and performance.
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        // Lazy load images directive
        Blade::directive('lazyImage', function ($expression) {
            return "<?php echo app('App\\Services\\LazyLoadService')->image({$expression}); ?>";
        });

        // Conditional asset loading
        Blade::directive('conditionalAsset', function ($expression) {
            return "<?php echo app('App\\Services\\AssetService')->conditionalLoad({$expression}); ?>";
        });

        // Performance monitoring directive
        Blade::directive('performanceStart', function ($name) {
            return "<?php \$performanceTimer_{$name} = microtime(true); ?>";
        });

        Blade::directive('performanceEnd', function ($name) {
            return "<?php 
                if (config('performance.monitoring.enabled')) {
                    \$executionTime = (microtime(true) - \$performanceTimer_{$name}) * 1000;
                    Log::info('Performance: ' . {$name} . ' took ' . \$executionTime . 'ms');
                }
            ?>";
        });

        // Critical CSS directive
        Blade::directive('criticalCss', function ($expression) {
            return "<?php echo app('App\\Services\\CriticalCssService')->inline({$expression}); ?>";
        });

        // Async script loading
        Blade::directive('asyncScript', function ($expression) {
            return "<?php echo app('App\\Services\\AssetService')->asyncScript({$expression}); ?>";
        });
    }

    /**
     * Monitor database queries for performance issues.
     *
     * @return void
     */
    protected function monitorDatabaseQueries()
    {
        DB::listen(function (QueryExecuted $query) {
            $threshold = Config::get('performance.monitoring.slow_query_threshold', 1000);
            
            if ($query->time > $threshold) {
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                    'connection' => $query->connectionName,
                ]);
            }

            // Detect N+1 queries
            $this->detectNPlusOneQueries($query);
        });
    }

    /**
     * Detect potential N+1 query issues.
     *
     * @param  \Illuminate\Database\Events\QueryExecuted  $query
     * @return void
     */
    protected function detectNPlusOneQueries($query)
    {
        static $queryPatterns = [];
        
        $pattern = preg_replace('/\d+/', '?', $query->sql);
        
        if (!isset($queryPatterns[$pattern])) {
            $queryPatterns[$pattern] = 0;
        }
        
        $queryPatterns[$pattern]++;
        
        // If the same pattern is executed more than 10 times, it might be N+1
        if ($queryPatterns[$pattern] > 10) {
            Log::warning('Potential N+1 query detected', [
                'pattern' => $pattern,
                'count' => $queryPatterns[$pattern],
                'sql' => $query->sql,
            ]);
        }
    }

    /**
     * Optimize asset loading strategies.
     *
     * @return void
     */
    protected function optimizeAssetLoading()
    {
        // Register asset optimization macros
        if (Config::get('performance.optimization.assets.minify_css')) {
            $this->registerCssOptimization();
        }

        if (Config::get('performance.optimization.assets.minify_js')) {
            $this->registerJsOptimization();
        }

        if (Config::get('performance.optimization.assets.lazy_load_images')) {
            $this->registerImageOptimization();
        }
    }

    /**
     * Register CSS optimization helpers.
     *
     * @return void
     */
    protected function registerCssOptimization()
    {
        // This would integrate with a CSS minification service
        // For now, we'll just add the configuration
    }

    /**
     * Register JavaScript optimization helpers.
     *
     * @return void
     */
    protected function registerJsOptimization()
    {
        // This would integrate with a JS minification service
        // For now, we'll just add the configuration
    }

    /**
     * Register image optimization helpers.
     *
     * @return void
     */
    protected function registerImageOptimization()
    {
        // Register image lazy loading service
        $this->app->singleton('App\\Services\\LazyLoadService', function ($app) {
            return new \App\Services\LazyLoadService();
        });
    }
}
