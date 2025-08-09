<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:monitor {--report : Generate performance report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor application performance and generate reports';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting performance monitoring...');

        if ($this->option('report')) {
            $this->generateReport();
        } else {
            $this->runMonitoring();
        }

        return 0;
    }

    /**
     * Run performance monitoring.
     *
     * @return void
     */
    protected function runMonitoring()
    {
        $this->checkDatabasePerformance();
        $this->checkCachePerformance();
        $this->checkMemoryUsage();
        $this->checkSlowQueries();
    }

    /**
     * Generate performance report.
     *
     * @return void
     */
    protected function generateReport()
    {
        $this->info('Generating performance report...');

        $report = [
            'timestamp' => now()->toISOString(),
            'database' => $this->getDatabaseMetrics(),
            'cache' => $this->getCacheMetrics(),
            'memory' => $this->getMemoryMetrics(),
            'queries' => $this->getSlowQueryMetrics(),
        ];

        $this->displayReport($report);
        $this->saveReport($report);
    }

    /**
     * Check database performance.
     *
     * @return void
     */
    protected function checkDatabasePerformance()
    {
        $this->info('Checking database performance...');

        $startTime = microtime(true);
        
        // Test query performance
        DB::table('products')->count();
        
        $queryTime = (microtime(true) - $startTime) * 1000;
        
        if ($queryTime > 100) { // 100ms threshold
            $this->warn("Slow database query detected: {$queryTime}ms");
        } else {
            $this->info("Database query time: {$queryTime}ms");
        }
    }

    /**
     * Check cache performance.
     *
     * @return void
     */
    protected function checkCachePerformance()
    {
        $this->info('Checking cache performance...');

        $startTime = microtime(true);
        
        // Test cache performance
        Cache::put('performance_test', 'test_value', 60);
        $value = Cache::get('performance_test');
        Cache::forget('performance_test');
        
        $cacheTime = (microtime(true) - $startTime) * 1000;
        
        if ($cacheTime > 10) { // 10ms threshold
            $this->warn("Slow cache operation detected: {$cacheTime}ms");
        } else {
            $this->info("Cache operation time: {$cacheTime}ms");
        }
    }

    /**
     * Check memory usage.
     *
     * @return void
     */
    protected function checkMemoryUsage()
    {
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
        $peakMemory = memory_get_peak_usage(true) / 1024 / 1024; // MB
        
        $this->info("Current memory usage: {$memoryUsage}MB");
        $this->info("Peak memory usage: {$peakMemory}MB");
        
        if ($peakMemory > 128) { // 128MB threshold
            $this->warn("High memory usage detected: {$peakMemory}MB");
        }
    }

    /**
     * Check for slow queries.
     *
     * @return void
     */
    protected function checkSlowQueries()
    {
        $this->info('Checking for slow queries...');

        // Get slow query log if available
        try {
            $slowQueries = DB::select("SHOW VARIABLES LIKE 'slow_query_log'");
            if (!empty($slowQueries) && $slowQueries[0]->Value === 'ON') {
                $this->info('Slow query log is enabled');
            } else {
                $this->warn('Slow query log is disabled');
            }
        } catch (\Exception $e) {
            $this->error('Could not check slow query log: ' . $e->getMessage());
        }
    }

    /**
     * Get database metrics.
     *
     * @return array
     */
    protected function getDatabaseMetrics()
    {
        $metrics = [];

        try {
            // Connection count
            $connections = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            $metrics['connections'] = $connections[0]->Value ?? 0;

            // Query cache hit rate
            $qcHits = DB::select("SHOW STATUS LIKE 'Qcache_hits'");
            $qcInserts = DB::select("SHOW STATUS LIKE 'Qcache_inserts'");
            
            $hits = $qcHits[0]->Value ?? 0;
            $inserts = $qcInserts[0]->Value ?? 0;
            $total = $hits + $inserts;
            
            $metrics['query_cache_hit_rate'] = $total > 0 ? ($hits / $total) * 100 : 0;

            // Table sizes
            $tableSizes = DB::select("
                SELECT table_name, table_rows, 
                       ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
                LIMIT 5
            ");
            
            $metrics['largest_tables'] = $tableSizes;

        } catch (\Exception $e) {
            $metrics['error'] = $e->getMessage();
        }

        return $metrics;
    }

    /**
     * Get cache metrics.
     *
     * @return array
     */
    protected function getCacheMetrics()
    {
        $metrics = [];

        try {
            // Test cache operations
            $startTime = microtime(true);
            Cache::put('perf_test_write', 'test', 60);
            $writeTime = (microtime(true) - $startTime) * 1000;

            $startTime = microtime(true);
            Cache::get('perf_test_write');
            $readTime = (microtime(true) - $startTime) * 1000;

            Cache::forget('perf_test_write');

            $metrics['write_time_ms'] = $writeTime;
            $metrics['read_time_ms'] = $readTime;
            $metrics['driver'] = config('cache.default');

        } catch (\Exception $e) {
            $metrics['error'] = $e->getMessage();
        }

        return $metrics;
    }

    /**
     * Get memory metrics.
     *
     * @return array
     */
    protected function getMemoryMetrics()
    {
        return [
            'current_usage_mb' => memory_get_usage(true) / 1024 / 1024,
            'peak_usage_mb' => memory_get_peak_usage(true) / 1024 / 1024,
            'limit_mb' => ini_get('memory_limit'),
        ];
    }

    /**
     * Get slow query metrics.
     *
     * @return array
     */
    protected function getSlowQueryMetrics()
    {
        $metrics = [];

        try {
            $slowQueries = DB::select("SHOW STATUS LIKE 'Slow_queries'");
            $metrics['slow_query_count'] = $slowQueries[0]->Value ?? 0;

            $longQueryTime = DB::select("SHOW VARIABLES LIKE 'long_query_time'");
            $metrics['long_query_time'] = $longQueryTime[0]->Value ?? 0;

        } catch (\Exception $e) {
            $metrics['error'] = $e->getMessage();
        }

        return $metrics;
    }

    /**
     * Display performance report.
     *
     * @param  array  $report
     * @return void
     */
    protected function displayReport($report)
    {
        $this->info('=== PERFORMANCE REPORT ===');
        $this->info('Timestamp: ' . $report['timestamp']);
        $this->newLine();

        // Database metrics
        $this->info('DATABASE METRICS:');
        $db = $report['database'];
        $this->info("- Connections: " . ($db['connections'] ?? 'N/A'));
        $this->info("- Query Cache Hit Rate: " . number_format($db['query_cache_hit_rate'] ?? 0, 2) . '%');
        $this->newLine();

        // Cache metrics
        $this->info('CACHE METRICS:');
        $cache = $report['cache'];
        $this->info("- Driver: " . ($cache['driver'] ?? 'N/A'));
        $this->info("- Write Time: " . number_format($cache['write_time_ms'] ?? 0, 2) . 'ms');
        $this->info("- Read Time: " . number_format($cache['read_time_ms'] ?? 0, 2) . 'ms');
        $this->newLine();

        // Memory metrics
        $this->info('MEMORY METRICS:');
        $memory = $report['memory'];
        $this->info("- Current Usage: " . number_format($memory['current_usage_mb'], 2) . 'MB');
        $this->info("- Peak Usage: " . number_format($memory['peak_usage_mb'], 2) . 'MB');
        $this->info("- Memory Limit: " . $memory['limit_mb']);
        $this->newLine();

        // Query metrics
        $this->info('QUERY METRICS:');
        $queries = $report['queries'];
        $this->info("- Slow Queries: " . ($queries['slow_query_count'] ?? 'N/A'));
        $this->info("- Long Query Time: " . ($queries['long_query_time'] ?? 'N/A') . 's');
    }

    /**
     * Save performance report.
     *
     * @param  array  $report
     * @return void
     */
    protected function saveReport($report)
    {
        $filename = 'performance_report_' . date('Y-m-d_H-i-s') . '.json';
        $path = storage_path('logs/' . $filename);
        
        file_put_contents($path, json_encode($report, JSON_PRETTY_PRINT));
        
        $this->info("Report saved to: {$path}");
    }
}
