<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class AssetService
{
    /**
     * Generate conditional asset loading HTML.
     *
     * @param  string  $asset
     * @param  string  $condition
     * @return string
     */
    public function conditionalLoad($asset, $condition = null)
    {
        $version = Config::get('performance.cache.assets.version', '1.0');
        $assetUrl = asset($asset) . '?v=' . $version;

        if ($condition) {
            return "
            <script>
            if ({$condition}) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '{$assetUrl}';
                document.head.appendChild(link);
            }
            </script>";
        }

        return '<link rel="stylesheet" href="' . $assetUrl . '">';
    }

    /**
     * Generate async script loading HTML.
     *
     * @param  string  $src
     * @param  array   $attributes
     * @return string
     */
    public function asyncScript($src, $attributes = [])
    {
        $version = Config::get('performance.cache.assets.version', '1.0');
        $scriptUrl = asset($src) . '?v=' . $version;

        $defaultAttributes = [
            'src' => $scriptUrl,
            'async' => true,
            'defer' => true,
        ];

        $attributes = array_merge($defaultAttributes, $attributes);

        return '<script ' . $this->buildAttributes($attributes) . '></script>';
    }

    /**
     * Generate preload link for critical resources.
     *
     * @param  string  $href
     * @param  string  $as
     * @param  array   $attributes
     * @return string
     */
    public function preload($href, $as, $attributes = [])
    {
        $version = Config::get('performance.cache.assets.version', '1.0');
        $resourceUrl = asset($href) . '?v=' . $version;

        $defaultAttributes = [
            'rel' => 'preload',
            'href' => $resourceUrl,
            'as' => $as,
        ];

        $attributes = array_merge($defaultAttributes, $attributes);

        return '<link ' . $this->buildAttributes($attributes) . '>';
    }

    /**
     * Generate DNS prefetch links.
     *
     * @param  array  $domains
     * @return string
     */
    public function dnsPrefetch($domains)
    {
        $html = '';
        foreach ($domains as $domain) {
            $html .= '<link rel="dns-prefetch" href="//' . $domain . '">' . "\n";
        }
        return $html;
    }

    /**
     * Generate resource hints for performance.
     *
     * @return string
     */
    public function resourceHints()
    {
        $hints = '';

        // DNS prefetch
        $dnsPrefetch = Config::get('performance.preloading.dns_prefetch', []);
        $hints .= $this->dnsPrefetch($dnsPrefetch);

        // Preload critical CSS
        $criticalCss = Config::get('performance.preloading.critical_css', []);
        foreach ($criticalCss as $css) {
            $hints .= $this->preload("css/{$css}", 'style') . "\n";
        }

        // Preload critical JS
        $criticalJs = Config::get('performance.preloading.critical_js', []);
        foreach ($criticalJs as $js) {
            $hints .= $this->preload("js/{$js}", 'script') . "\n";
        }

        return $hints;
    }

    /**
     * Generate versioned asset URL.
     *
     * @param  string  $path
     * @return string
     */
    public function version($path)
    {
        $version = Config::get('performance.cache.assets.version', '1.0');
        return asset($path) . '?v=' . $version;
    }

    /**
     * Check if CDN is enabled and return CDN URL.
     *
     * @param  string  $path
     * @param  string  $type
     * @return string
     */
    public function cdn($path, $type = 'css')
    {
        if (!Config::get('performance.cdn.enabled', false)) {
            return $this->version($path);
        }

        if (!Config::get("performance.cdn.assets.{$type}", false)) {
            return $this->version($path);
        }

        $cdnUrl = Config::get('performance.cdn.url');
        $version = Config::get('performance.cache.assets.version', '1.0');

        return rtrim($cdnUrl, '/') . '/' . ltrim($path, '/') . '?v=' . $version;
    }

    /**
     * Build HTML attributes string.
     *
     * @param  array  $attributes
     * @return string
     */
    protected function buildAttributes($attributes)
    {
        $html = [];
        
        foreach ($attributes as $key => $value) {
            if ($value !== null && $value !== false) {
                if ($value === true) {
                    $html[] = $key;
                } else {
                    $html[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
                }
            }
        }

        return implode(' ', $html);
    }

    /**
     * Generate critical CSS loading strategy.
     *
     * @param  string  $cssFile
     * @return string
     */
    public function criticalCss($cssFile)
    {
        $version = Config::get('performance.cache.assets.version', '1.0');
        $cssUrl = asset("css/{$cssFile}") . '?v=' . $version;

        return "
        <link rel=\"preload\" href=\"{$cssUrl}\" as=\"style\" onload=\"this.onload=null;this.rel='stylesheet'\">
        <noscript><link rel=\"stylesheet\" href=\"{$cssUrl}\"></noscript>";
    }

    /**
     * Generate performance monitoring script.
     *
     * @return string
     */
    public function performanceMonitoring()
    {
        if (!Config::get('performance.monitoring.enabled', false)) {
            return '';
        }

        return '
        <script>
        window.addEventListener("load", function() {
            if ("performance" in window) {
                const perfData = performance.getEntriesByType("navigation")[0];
                const loadTime = perfData.loadEventEnd - perfData.loadEventStart;
                const domContentLoaded = perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart;
                
                // Send performance data to server
                fetch("/api/performance-metrics", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                    },
                    body: JSON.stringify({
                        load_time: loadTime,
                        dom_content_loaded: domContentLoaded,
                        url: window.location.href,
                        user_agent: navigator.userAgent
                    })
                }).catch(function(error) {
                    console.warn("Failed to send performance metrics:", error);
                });
            }
        });
        </script>';
    }
}
