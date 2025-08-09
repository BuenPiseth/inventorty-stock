<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class PerformanceOptimization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $next($request);

        // Apply performance optimizations to the response
        $this->optimizeResponse($response, $request);

        // Monitor performance if enabled
        if (Config::get('performance.monitoring.enabled')) {
            $this->monitorPerformance($request, $startTime, $startMemory);
        }

        return $response;
    }

    /**
     * Optimize the HTTP response.
     *
     * @param  \Illuminate\Http\Response  $response
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function optimizeResponse($response, $request)
    {
        // Add caching headers
        if (Config::get('performance.optimization.response.browser_caching')) {
            $this->addCachingHeaders($response, $request);
        }

        // Add compression headers
        if (Config::get('performance.optimization.response.gzip_compression')) {
            $this->addCompressionHeaders($response);
        }

        // Add ETag if enabled
        if (Config::get('performance.optimization.response.etag_enabled')) {
            $this->addETag($response);
        }

        // Add security headers
        $this->addSecurityHeaders($response);

        // Add performance hints
        $this->addPerformanceHints($response);
    }

    /**
     * Add caching headers to the response.
     *
     * @param  \Illuminate\Http\Response  $response
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function addCachingHeaders($response, $request)
    {
        // Don't cache authenticated pages
        if (auth()->check()) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            return;
        }

        // Cache static assets
        if ($this->isStaticAsset($request)) {
            $ttl = Config::get('performance.cache.assets.ttl', 31536000);
            $response->headers->set('Cache-Control', "public, max-age={$ttl}");
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT');
        }
    }

    /**
     * Add compression headers.
     *
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    protected function addCompressionHeaders($response)
    {
        $response->headers->set('Vary', 'Accept-Encoding');
    }

    /**
     * Add ETag header.
     *
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    protected function addETag($response)
    {
        $content = $response->getContent();
        if ($content) {
            $etag = md5($content);
            $response->headers->set('ETag', '"' . $etag . '"');
        }
    }

    /**
     * Add security headers.
     *
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    protected function addSecurityHeaders($response)
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Add performance hints.
     *
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    protected function addPerformanceHints($response)
    {
        // DNS prefetch hints
        $dnsPrefetch = Config::get('performance.preloading.dns_prefetch', []);
        foreach ($dnsPrefetch as $domain) {
            $response->headers->set('Link', "<//fonts.googleapis.com>; rel=dns-prefetch", false);
        }

        // Preload critical resources
        $criticalCss = Config::get('performance.preloading.critical_css', []);
        foreach ($criticalCss as $css) {
            $response->headers->set('Link', "<" . asset("css/{$css}") . ">; rel=preload; as=style", false);
        }

        $criticalJs = Config::get('performance.preloading.critical_js', []);
        foreach ($criticalJs as $js) {
            $response->headers->set('Link', "<" . asset("js/{$js}") . ">; rel=preload; as=script", false);
        }
    }

    /**
     * Monitor performance metrics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  float  $startTime
     * @param  int  $startMemory
     * @return void
     */
    protected function monitorPerformance($request, $startTime, $startMemory)
    {
        $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = (memory_get_usage() - $startMemory) / 1024 / 1024; // Convert to MB
        $peakMemory = memory_get_peak_usage() / 1024 / 1024; // Convert to MB

        $thresholds = Config::get('performance.monitoring');

        // Log slow requests
        if ($executionTime > $thresholds['response_time_threshold']) {
            \Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime,
                'memory_usage' => $memoryUsage,
                'peak_memory' => $peakMemory,
            ]);
        }

        // Log high memory usage
        if ($peakMemory > $thresholds['memory_threshold']) {
            \Log::warning('High memory usage detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime,
                'memory_usage' => $memoryUsage,
                'peak_memory' => $peakMemory,
            ]);
        }

        // Store metrics for analysis
        $this->storePerformanceMetrics($request, $executionTime, $memoryUsage, $peakMemory);
    }

    /**
     * Store performance metrics for analysis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  float  $executionTime
     * @param  float  $memoryUsage
     * @param  float  $peakMemory
     * @return void
     */
    protected function storePerformanceMetrics($request, $executionTime, $memoryUsage, $peakMemory)
    {
        $key = 'performance_metrics_' . date('Y-m-d-H');
        
        $metrics = Cache::get($key, []);
        $metrics[] = [
            'timestamp' => now()->toISOString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'execution_time' => $executionTime,
            'memory_usage' => $memoryUsage,
            'peak_memory' => $peakMemory,
            'user_agent' => $request->userAgent(),
        ];

        // Keep only last 1000 entries per hour
        if (count($metrics) > 1000) {
            $metrics = array_slice($metrics, -1000);
        }

        Cache::put($key, $metrics, 3600); // Store for 1 hour
    }

    /**
     * Check if the request is for a static asset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isStaticAsset($request)
    {
        $path = $request->path();
        $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];
        
        foreach ($staticExtensions as $extension) {
            if (str_ends_with($path, '.' . $extension)) {
                return true;
            }
        }

        return false;
    }
}
