<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--path= : Custom backup path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the MySQL database for phpMyAdmin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('💾 Creating Database Backup for DEER BAKERY & CAKE');
        $this->info('=================================================');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        // Create backup filename with timestamp
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "deer_inventory_backup_{$timestamp}.sql";

        // Determine backup path
        $backupPath = $this->option('path') ?? storage_path('app/backups');

        // Create backup directory if it doesn't exist
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
            $this->info("📁 Created backup directory: {$backupPath}");
        }

        $fullPath = $backupPath . DIRECTORY_SEPARATOR . $filename;

        // Build mysqldump command
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($fullPath)
        );

        $this->info("🔄 Creating backup...");

        // Execute the backup command
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($fullPath)) {
            $fileSize = $this->formatBytes(filesize($fullPath));
            $this->info("✅ Backup created successfully!");
            $this->info("📄 File: {$filename}");
            $this->info("📍 Location: {$fullPath}");
            $this->info("📊 Size: {$fileSize}");

            // Show backup contents summary
            $this->showBackupSummary($fullPath);

            $this->newLine();
            $this->info("💡 To restore this backup in phpMyAdmin:");
            $this->info("   1. Open phpMyAdmin");
            $this->info("   2. Select 'deer_inventory' database");
            $this->info("   3. Go to 'Import' tab");
            $this->info("   4. Choose file: {$filename}");
            $this->info("   5. Click 'Go' to restore");

        } else {
            $this->error("❌ Backup failed!");
            $this->error("Return code: {$returnCode}");
            if (!empty($output)) {
                $this->error("Output: " . implode("\n", $output));
            }
            return 1;
        }

        return 0;
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    private function showBackupSummary($filePath)
    {
        $this->info("\n📋 Backup Contents:");

        // Count tables and data in backup file
        $content = file_get_contents($filePath);

        // Count CREATE TABLE statements
        $tableCount = substr_count($content, 'CREATE TABLE');

        // Count INSERT statements
        $insertCount = substr_count($content, 'INSERT INTO');

        $this->info("   📊 Tables backed up: {$tableCount}");
        $this->info("   📝 Data records: {$insertCount}");

        // Show which tables are included
        preg_match_all('/CREATE TABLE `([^`]+)`/', $content, $matches);
        if (!empty($matches[1])) {
            $this->info("   📋 Tables included: " . implode(', ', $matches[1]));
        }
    }
}
