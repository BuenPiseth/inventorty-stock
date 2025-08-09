<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class LazyLoadService
{
    /**
     * Generate lazy loading image HTML.
     *
     * @param  string  $src
     * @param  string  $alt
     * @param  array   $attributes
     * @return string
     */
    public function image($src, $alt = '', $attributes = [])
    {
        if (!Config::get('performance.lazy_loading.images.enabled', true)) {
            return $this->regularImage($src, $alt, $attributes);
        }

        $placeholder = Config::get('performance.lazy_loading.images.placeholder');
        $threshold = Config::get('performance.lazy_loading.images.threshold', 300);

        $defaultAttributes = [
            'class' => 'lazy-load',
            'data-src' => $src,
            'data-threshold' => $threshold,
            'loading' => 'lazy',
            'alt' => $alt,
        ];

        $attributes = array_merge($defaultAttributes, $attributes);
        
        // Add placeholder as src
        $attributes['src'] = $placeholder;

        return '<img ' . $this->buildAttributes($attributes) . '>';
    }

    /**
     * Generate regular image HTML (fallback).
     *
     * @param  string  $src
     * @param  string  $alt
     * @param  array   $attributes
     * @return string
     */
    protected function regularImage($src, $alt = '', $attributes = [])
    {
        $defaultAttributes = [
            'src' => $src,
            'alt' => $alt,
            'loading' => 'lazy',
        ];

        $attributes = array_merge($defaultAttributes, $attributes);

        return '<img ' . $this->buildAttributes($attributes) . '>';
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
     * Generate lazy loading script.
     *
     * @return string
     */
    public function script()
    {
        if (!Config::get('performance.lazy_loading.images.enabled', true)) {
            return '';
        }

        return '
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            if ("IntersectionObserver" in window) {
                const lazyImages = document.querySelectorAll(".lazy-load");
                const imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const image = entry.target;
                            image.src = image.dataset.src;
                            image.classList.remove("lazy-load");
                            image.classList.add("lazy-loaded");
                            imageObserver.unobserve(image);
                        }
                    });
                }, {
                    rootMargin: "' . Config::get('performance.lazy_loading.images.threshold', 300) . 'px"
                });

                lazyImages.forEach(function(image) {
                    imageObserver.observe(image);
                });
            } else {
                // Fallback for browsers without IntersectionObserver
                const lazyImages = document.querySelectorAll(".lazy-load");
                lazyImages.forEach(function(image) {
                    image.src = image.dataset.src;
                    image.classList.remove("lazy-load");
                    image.classList.add("lazy-loaded");
                });
            }
        });
        </script>';
    }
}
