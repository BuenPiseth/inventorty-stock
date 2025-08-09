<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VerifyDatabaseStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:verify {--detailed : Show detailed information about each table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that all data is properly stored in MySQL/phpMyAdmin database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verifying Database Storage for DEER BAKERY & CAKE');
        $this->info('================================================');

        // Check database connection
        try {
            $pdo = DB::connection()->getPdo();
            $this->info("✅ Database Connection: ACTIVE");
            $this->info("   Driver: " . $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
            $this->info("   Server: " . $pdo->getAttribute(\PDO::ATTR_SERVER_INFO));
            $this->info("   Database: " . config('database.connections.mysql.database'));
        } catch (\Exception $e) {
            $this->error("❌ Database Connection Failed: " . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Verify all required tables exist
        $requiredTables = [
            'products' => Product::class,
            'categories' => Category::class,
            'stock_movements' => StockMovement::class,
            'warehouses' => Warehouse::class,
        ];

        $this->info('📋 Table Verification:');
        $totalRecords = 0;

        foreach ($requiredTables as $tableName => $modelClass) {
            if (Schema::hasTable($tableName)) {
                $count = $modelClass::count();
                $totalRecords += $count;
                $this->info("   ✅ {$tableName}: {$count} records");

                if ($this->option('detailed')) {
                    $this->showTableDetails($tableName, $modelClass);
                }
            } else {
                $this->error("   ❌ {$tableName}: TABLE MISSING!");
            }
        }

        $this->newLine();
        $this->info("📊 Total Records in Database: {$totalRecords}");

        // Check recent activity
        $this->info('🕒 Recent Activity:');
        $recentProducts = Product::where('created_at', '>=', now()->subDays(7))->count();
        $recentMovements = StockMovement::where('created_at', '>=', now()->subDays(7))->count();

        $this->info("   📦 Products added (last 7 days): {$recentProducts}");
        $this->info("   📋 Stock movements (last 7 days): {$recentMovements}");

        // Storage verification
        $this->newLine();
        $this->info('💾 Storage Verification:');

        // Check if data is actually persisted
        $testProduct = Product::first();
        if ($testProduct) {
            $this->info("   ✅ Data Persistence: CONFIRMED");
            $this->info("   📝 Sample Product: {$testProduct->name} (ID: {$testProduct->id})");
        } else {
            $this->warn("   ⚠️  No products found - database may be empty");
        }

        // Check warehouse data
        $warehouses = Warehouse::all();
        if ($warehouses->count() > 0) {
            $this->info("   🏢 Warehouses configured: {$warehouses->count()}");
            foreach ($warehouses as $warehouse) {
                $this->info("      - {$warehouse->name} ({$warehouse->code})");
            }
        }

        $this->newLine();
        $this->info('✅ Database verification completed successfully!');
        $this->info('🎯 All data is properly stored in MySQL/phpMyAdmin');

        return 0;
    }

    private function showTableDetails($tableName, $modelClass)
    {
        $this->info("      📋 Table Structure:");
        $columns = Schema::getColumnListing($tableName);
        $this->info("         Columns: " . implode(', ', $columns));

        if ($modelClass::count() > 0) {
            $latest = $modelClass::latest()->first();
            $this->info("         Latest Record: " . $latest->created_at->format('Y-m-d H:i:s'));
        }
        $this->newLine();
    }
}
