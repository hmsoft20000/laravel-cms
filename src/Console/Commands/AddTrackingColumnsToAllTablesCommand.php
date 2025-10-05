<?php

namespace HMsoft\Cms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
// No need to import Doctrine\DBAL\Schema\AbstractSchemaManager explicitly here,
// as DB::connection()->getSchemaManager() handles it.

class AddTrackingColumnsToAllTablesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:add-tracking-columns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds created_at, created_by, updated_at, and updated_by columns to all tables that do not have them, excluding default Laravel tables.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to add tracking columns to tables...');

        // List of tables to exclude from modification
        $excludedTables = [
            'migrations',
            'users', // Users table typically has these or different tracking needs
            'password_reset_tokens',
            'failed_jobs',
            'personal_access_tokens',
            // Add any other default Laravel tables here if they exist in your application
            // e.g., 'cache', 'sessions', 'jobs'
        ];

        // Get all table names in the database using the correct method
        $tables = DB::select('SHOW TABLES');
        $tables = array_map(fn($table) => array_values((array)$table)[0], $tables);
        
        foreach ($tables as $table) {
            // Check if the table is in the excluded list
            if (in_array($table, $excludedTables)) {
                $this->warn("Skipping table: {$table}");
                continue;
            }

            // Ensure the table exists and is modifiable
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $hasChanges = false;

                    // Add created_at if not exists
                    if (!Schema::hasColumn($table, 'created_at')) {
                        $blueprint->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
                        $this->line(" - Added 'created_at' to {$table}");
                        $hasChanges = true;
                    }

                    // Add created_by if not exists
                    if (!Schema::hasColumn($table, 'created_by')) {
                        // Ensure 'users' table exists before adding foreign key
                        if (Schema::hasTable('users')) {
                            $blueprint->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                            $this->line(" - Added 'created_by' (FK to users) to {$table}");
                            $hasChanges = true;
                        } else {
                            $this->warn("   - 'users' table not found, skipping 'created_by' foreign key for {$table}.");
                            $blueprint->bigInteger('created_by')->nullable()->unsigned(); // Add without FK if users table not found
                            $this->line("   - Added 'created_by' (BIGINT) to {$table} without foreign key.");
                            $hasChanges = true;
                        }
                    }

                    // Add updated_at if not exists
                    if (!Schema::hasColumn($table, 'updated_at')) {
                        // Using nullable and default(null) to allow `useCurrentOnUpdate` to work correctly
                        $blueprint->timestamp('updated_at')->nullable()->default(null)->useCurrentOnUpdate();
                        $this->line(" - Added 'updated_at' to {$table}");
                        $hasChanges = true;
                    }

                    // Add updated_by if not exists
                    if (!Schema::hasColumn($table, 'updated_by')) {
                        // Ensure 'users' table exists before adding foreign key
                        if (Schema::hasTable('users')) {
                            $blueprint->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                            $this->line(" - Added 'updated_by' (FK to users) to {$table}");
                            $hasChanges = true;
                        } else {
                            $this->warn("   - 'users' table not found, skipping 'updated_by' foreign key for {$table}.");
                            $blueprint->bigInteger('updated_by')->nullable()->unsigned(); // Add without FK if users table not found
                            $this->line("   - Added 'updated_by' (BIGINT) to {$table} without foreign key.");
                            $hasChanges = true;
                        }
                    }

                    if ($hasChanges) {
                        $this->info("Successfully updated table: {$table}");
                    } else {
                        $this->comment("No changes needed for table: {$table}");
                    }
                });
            } else {
                $this->error("Table '{$table}' not found. Skipping.");
            }
        }

        $this->info('Finished adding tracking columns.');
    }
}
