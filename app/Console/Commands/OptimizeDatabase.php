<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptimizeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize {--analyze : Analyze tables for optimization}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize database tables and indexes for better performance';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting database optimization...');

        if ($this->option('analyze')) {
            $this->analyzeDatabase();
        }

        $this->optimizeTables();
        $this->checkIndexes();
        $this->updateStatistics();

        $this->info('Database optimization completed!');
        return 0;
    }

    /**
     * Analyze database for optimization opportunities.
     *
     * @return void
     */
    protected function analyzeDatabase()
    {
        $this->info('Analyzing database...');

        // Get table sizes
        $tables = $this->getTableSizes();
        
        $this->table(['Table', 'Rows', 'Size (MB)', 'Index Size (MB)'], $tables);

        // Check for missing indexes
        $this->checkMissingIndexes();

        // Check for unused indexes
        $this->checkUnusedIndexes();
    }

    /**
     * Get table sizes and row counts.
     *
     * @return array
     */
    protected function getTableSizes()
    {
        $query = "
            SELECT 
                table_name,
                table_rows,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                ROUND((index_length / 1024 / 1024), 2) AS index_size_mb
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
            ORDER BY (data_length + index_length) DESC
        ";

        return DB::select($query);
    }

    /**
     * Check for missing indexes on foreign keys.
     *
     * @return void
     */
    protected function checkMissingIndexes()
    {
        $this->info('Checking for missing indexes...');

        $foreignKeys = [
            'products' => ['category_id', 'warehouse_id'],
            'stock_movements' => ['product_id', 'user_id'],
        ];

        foreach ($foreignKeys as $table => $columns) {
            if (Schema::hasTable($table)) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $hasIndex = $this->hasIndex($table, $column);
                        if (!$hasIndex) {
                            $this->warn("Missing index on {$table}.{$column}");
                            $this->createIndex($table, $column);
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if a column has an index.
     *
     * @param  string  $table
     * @param  string  $column
     * @return bool
     */
    protected function hasIndex($table, $column)
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Column_name = ?", [$column]);
        return !empty($indexes);
    }

    /**
     * Create an index on a column.
     *
     * @param  string  $table
     * @param  string  $column
     * @return void
     */
    protected function createIndex($table, $column)
    {
        try {
            $indexName = "idx_{$table}_{$column}";
            DB::statement("CREATE INDEX {$indexName} ON {$table} ({$column})");
            $this->info("Created index {$indexName}");
        } catch (\Exception $e) {
            $this->error("Failed to create index on {$table}.{$column}: " . $e->getMessage());
        }
    }

    /**
     * Check for unused indexes.
     *
     * @return void
     */
    protected function checkUnusedIndexes()
    {
        $this->info('Checking for unused indexes...');

        // This would require performance schema to be enabled
        // For now, we'll just show a message
        $this->comment('To check for unused indexes, enable performance_schema and run:');
        $this->comment('SELECT * FROM performance_schema.table_io_waits_summary_by_index_usage WHERE index_name IS NOT NULL AND count_star = 0;');
    }

    /**
     * Optimize database tables.
     *
     * @return void
     */
    protected function optimizeTables()
    {
        $this->info('Optimizing tables...');

        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableColumn = "Tables_in_{$databaseName}";

        foreach ($tables as $table) {
            $tableName = $table->$tableColumn;
            
            try {
                DB::statement("OPTIMIZE TABLE {$tableName}");
                $this->info("Optimized table: {$tableName}");
            } catch (\Exception $e) {
                $this->error("Failed to optimize {$tableName}: " . $e->getMessage());
            }
        }
    }

    /**
     * Update table statistics.
     *
     * @return void
     */
    protected function updateStatistics()
    {
        $this->info('Updating table statistics...');

        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableColumn = "Tables_in_{$databaseName}";

        foreach ($tables as $table) {
            $tableName = $table->$tableColumn;
            
            try {
                DB::statement("ANALYZE TABLE {$tableName}");
                $this->info("Analyzed table: {$tableName}");
            } catch (\Exception $e) {
                $this->error("Failed to analyze {$tableName}: " . $e->getMessage());
            }
        }
    }

    /**
     * Check and create recommended indexes.
     *
     * @return void
     */
    protected function checkIndexes()
    {
        $this->info('Checking recommended indexes...');

        $recommendedIndexes = [
            'products' => [
                ['status'],
                ['quantity'],
                ['created_at'],
                ['category_id', 'status'],
                ['warehouse_id', 'status'],
            ],
            'stock_movements' => [
                ['movement_date'],
                ['movement_type'],
                ['product_id', 'movement_date'],
                ['user_id', 'movement_date'],
            ],
            'categories' => [
                ['status'],
                ['created_at'],
            ],
        ];

        foreach ($recommendedIndexes as $table => $indexes) {
            if (Schema::hasTable($table)) {
                foreach ($indexes as $columns) {
                    $this->createCompositeIndex($table, $columns);
                }
            }
        }
    }

    /**
     * Create a composite index.
     *
     * @param  string  $table
     * @param  array   $columns
     * @return void
     */
    protected function createCompositeIndex($table, $columns)
    {
        $indexName = 'idx_' . $table . '_' . implode('_', $columns);
        $columnList = implode(', ', $columns);

        // Check if index already exists
        $exists = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        
        if (empty($exists)) {
            try {
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$columnList})");
                $this->info("Created composite index: {$indexName}");
            } catch (\Exception $e) {
                $this->error("Failed to create index {$indexName}: " . $e->getMessage());
            }
        }
    }
}
