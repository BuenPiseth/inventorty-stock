/**
 * DEER BAKERY & CAKE - Performance Monitor
 * 
 * Monitors application performance, tracks metrics,
 * and provides optimization insights for the inventory system.
 * 
 * @version 1.0.0
 */

export class PerformanceMonitor {
    static metrics = new Map();
    static observers = new Set();
    static isMonitoring = false;
    static config = {
        enableMetrics: true,
        enableResourceTiming: true,
        enableUserTiming: true,
        sampleRate: 1.0 // Monitor 100% of sessions
    };

    /**
     * Start performance monitoring
     */
    static start() {
        if (this.isMonitoring) return;
        
        this.isMonitoring = true;
        this.setupPerformanceObservers();
        this.trackPageLoad();
        this.trackUserInteractions();
        this.trackMemoryUsage();
        
        console.log('📊 Performance Monitor started');
    }

    /**
     * Stop performance monitoring
     */
    static stop() {
        this.isMonitoring = false;
        this.observers.forEach(observer => observer.disconnect());
        this.observers.clear();
        console.log('📊 Performance Monitor stopped');
    }

    /**
     * Setup performance observers
     */
    static setupPerformanceObservers() {
        // Navigation timing
        if ('PerformanceObserver' in window) {
            try {
                const navObserver = new PerformanceObserver((list) => {
                    list.getEntries().forEach(entry => {
                        this.recordMetric('navigation', entry);
                    });
                });
                navObserver.observe({ entryTypes: ['navigation'] });
                this.observers.add(navObserver);
            } catch (error) {
                console.warn('Navigation timing not supported:', error);
            }

            // Resource timing
            if (this.config.enableResourceTiming) {
                try {
                    const resourceObserver = new PerformanceObserver((list) => {
                        list.getEntries().forEach(entry => {
                            this.recordResourceMetric(entry);
                        });
                    });
                    resourceObserver.observe({ entryTypes: ['resource'] });
                    this.observers.add(resourceObserver);
                } catch (error) {
                    console.warn('Resource timing not supported:', error);
                }
            }

            // User timing
            if (this.config.enableUserTiming) {
                try {
                    const userObserver = new PerformanceObserver((list) => {
                        list.getEntries().forEach(entry => {
                            this.recordMetric('user-timing', entry);
                        });
                    });
                    userObserver.observe({ entryTypes: ['measure', 'mark'] });
                    this.observers.add(userObserver);
                } catch (error) {
                    console.warn('User timing not supported:', error);
                }
            }

            // Long tasks
            try {
                const longTaskObserver = new PerformanceObserver((list) => {
                    list.getEntries().forEach(entry => {
                        this.recordLongTask(entry);
                    });
                });
                longTaskObserver.observe({ entryTypes: ['longtask'] });
                this.observers.add(longTaskObserver);
            } catch (error) {
                console.warn('Long task timing not supported:', error);
            }
        }
    }

    /**
     * Track page load performance
     */
    static trackPageLoad() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                const navigation = performance.getEntriesByType('navigation')[0];
                if (navigation) {
                    this.recordPageLoadMetrics(navigation);
                }
            }, 0);
        });
    }

    /**
     * Record page load metrics
     */
    static recordPageLoadMetrics(navigation) {
        const metrics = {
            dns_lookup: navigation.domainLookupEnd - navigation.domainLookupStart,
            tcp_connection: navigation.connectEnd - navigation.connectStart,
            ssl_negotiation: navigation.connectEnd - navigation.secureConnectionStart,
            request_time: navigation.responseStart - navigation.requestStart,
            response_time: navigation.responseEnd - navigation.responseStart,
            dom_processing: navigation.domContentLoadedEventStart - navigation.responseEnd,
            dom_complete: navigation.domComplete - navigation.domContentLoadedEventStart,
            load_complete: navigation.loadEventEnd - navigation.loadEventStart,
            total_load_time: navigation.loadEventEnd - navigation.navigationStart,
            first_paint: this.getFirstPaint(),
            first_contentful_paint: this.getFirstContentfulPaint()
        };

        this.recordMetric('page-load', metrics);
        this.analyzePageLoadPerformance(metrics);
    }

    /**
     * Get First Paint timing
     */
    static getFirstPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const firstPaint = paintEntries.find(entry => entry.name === 'first-paint');
        return firstPaint ? firstPaint.startTime : null;
    }

    /**
     * Get First Contentful Paint timing
     */
    static getFirstContentfulPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const fcp = paintEntries.find(entry => entry.name === 'first-contentful-paint');
        return fcp ? fcp.startTime : null;
    }

    /**
     * Track user interactions
     */
    static trackUserInteractions() {
        // Track form submissions
        document.addEventListener('submit', (event) => {
            const startTime = performance.now();
            const form = event.target;
            
            form.addEventListener('ajax:complete', () => {
                const duration = performance.now() - startTime;
                this.recordMetric('form-submission', {
                    form_id: form.id || 'unknown',
                    duration,
                    action: form.action
                });
            }, { once: true });
        });

        // Track button clicks
        document.addEventListener('click', (event) => {
            if (event.target.matches('button, .btn')) {
                this.recordMetric('button-click', {
                    button_text: event.target.textContent.trim(),
                    button_class: event.target.className,
                    timestamp: Date.now()
                });
            }
        });

        // Track navigation
        document.addEventListener('click', (event) => {
            if (event.target.matches('a[href]')) {
                this.recordMetric('navigation-click', {
                    href: event.target.href,
                    text: event.target.textContent.trim(),
                    timestamp: Date.now()
                });
            }
        });
    }

    /**
     * Track memory usage
     */
    static trackMemoryUsage() {
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                this.recordMetric('memory-usage', {
                    used: memory.usedJSHeapSize,
                    total: memory.totalJSHeapSize,
                    limit: memory.jsHeapSizeLimit,
                    timestamp: Date.now()
                });
            }, 30000); // Every 30 seconds
        }
    }

    /**
     * Record resource metrics
     */
    static recordResourceMetric(entry) {
        if (entry.duration > 100) { // Only track slow resources
            this.recordMetric('slow-resource', {
                name: entry.name,
                duration: entry.duration,
                size: entry.transferSize,
                type: this.getResourceType(entry.name)
            });
        }
    }

    /**
     * Record long tasks
     */
    static recordLongTask(entry) {
        this.recordMetric('long-task', {
            duration: entry.duration,
            start_time: entry.startTime,
            attribution: entry.attribution
        });

        // Show warning for very long tasks
        if (entry.duration > 100) {
            console.warn(`Long task detected: ${entry.duration}ms`);
        }
    }

    /**
     * Get resource type from URL
     */
    static getResourceType(url) {
        if (url.includes('.css')) return 'css';
        if (url.includes('.js')) return 'javascript';
        if (url.match(/\.(jpg|jpeg|png|gif|webp|svg)$/i)) return 'image';
        if (url.includes('/api/')) return 'api';
        return 'other';
    }

    /**
     * Record metric
     */
    static recordMetric(type, data) {
        if (!this.config.enableMetrics) return;
        
        const metric = {
            type,
            data,
            timestamp: Date.now(),
            url: window.location.href,
            user_agent: navigator.userAgent
        };

        // Store in memory
        if (!this.metrics.has(type)) {
            this.metrics.set(type, []);
        }
        this.metrics.get(type).push(metric);

        // Limit memory usage
        const typeMetrics = this.metrics.get(type);
        if (typeMetrics.length > 100) {
            typeMetrics.shift(); // Remove oldest
        }

        // Send to analytics if configured
        this.sendToAnalytics(metric);
    }

    /**
     * Analyze page load performance
     */
    static analyzePageLoadPerformance(metrics) {
        const issues = [];

        if (metrics.total_load_time > 3000) {
            issues.push('Slow page load time (>3s)');
        }

        if (metrics.first_contentful_paint > 1500) {
            issues.push('Slow First Contentful Paint (>1.5s)');
        }

        if (metrics.dns_lookup > 200) {
            issues.push('Slow DNS lookup (>200ms)');
        }

        if (metrics.response_time > 500) {
            issues.push('Slow server response (>500ms)');
        }

        if (issues.length > 0) {
            console.warn('Performance issues detected:', issues);
            this.recordMetric('performance-issues', { issues, metrics });
        }
    }

    /**
     * Get performance summary
     */
    static getPerformanceSummary() {
        const summary = {
            total_metrics: 0,
            types: {},
            memory_usage: null,
            recent_issues: []
        };

        this.metrics.forEach((metrics, type) => {
            summary.total_metrics += metrics.length;
            summary.types[type] = metrics.length;
        });

        // Get latest memory usage
        const memoryMetrics = this.metrics.get('memory-usage');
        if (memoryMetrics && memoryMetrics.length > 0) {
            summary.memory_usage = memoryMetrics[memoryMetrics.length - 1].data;
        }

        // Get recent performance issues
        const issueMetrics = this.metrics.get('performance-issues');
        if (issueMetrics) {
            summary.recent_issues = issueMetrics.slice(-5);
        }

        return summary;
    }

    /**
     * Send metrics to analytics service
     */
    static sendToAnalytics(metric) {
        // Sample based on configuration
        if (Math.random() > this.config.sampleRate) return;

        // In production, send to analytics service
        if (window.gtag) {
            window.gtag('event', 'performance_metric', {
                metric_type: metric.type,
                metric_value: JSON.stringify(metric.data)
            });
        }

        // Could also send to custom analytics endpoint
        if (window.analyticsEndpoint) {
            fetch(window.analyticsEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(metric)
            }).catch(error => {
                console.warn('Failed to send analytics:', error);
            });
        }
    }

    /**
     * Mark performance timing
     */
    static mark(name) {
        if ('performance' in window && 'mark' in performance) {
            performance.mark(name);
        }
    }

    /**
     * Measure performance between marks
     */
    static measure(name, startMark, endMark) {
        if ('performance' in window && 'measure' in performance) {
            try {
                performance.measure(name, startMark, endMark);
            } catch (error) {
                console.warn('Failed to measure performance:', error);
            }
        }
    }

    /**
     * Time a function execution
     */
    static async timeFunction(name, fn) {
        const startTime = performance.now();
        this.mark(`${name}-start`);
        
        try {
            const result = await fn();
            const endTime = performance.now();
            const duration = endTime - startTime;
            
            this.mark(`${name}-end`);
            this.measure(name, `${name}-start`, `${name}-end`);
            
            this.recordMetric('function-timing', {
                name,
                duration,
                success: true
            });
            
            return result;
        } catch (error) {
            const endTime = performance.now();
            const duration = endTime - startTime;
            
            this.recordMetric('function-timing', {
                name,
                duration,
                success: false,
                error: error.message
            });
            
            throw error;
        }
    }

    /**
     * Clear all metrics
     */
    static clearMetrics() {
        this.metrics.clear();
        if ('performance' in window && 'clearMarks' in performance) {
            performance.clearMarks();
            performance.clearMeasures();
        }
    }
}
