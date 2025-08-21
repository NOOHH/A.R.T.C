<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;

class EnsureTenantSettingsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:ensure-settings-table {--tenant= : Specific tenant slug to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all tenant databases have the settings table';

    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ENSURING TENANT SETTINGS TABLE ===');

        $specificTenant = $this->option('tenant');
        
        if ($specificTenant) {
            $tenants = Tenant::where('slug', $specificTenant)->get();
            if ($tenants->isEmpty()) {
                $this->error("Tenant with slug '{$specificTenant}' not found.");
                return 1;
            }
        } else {
            $tenants = Tenant::all();
        }

        $this->info("Found " . $tenants->count() . " tenant(s) to check.");

        $successCount = 0;
        $errorCount = 0;

        foreach ($tenants as $tenant) {
            $this->line("\n--- Checking tenant: {$tenant->name} (DB: {$tenant->database_name}) ---");
            
            try {
                // Switch to tenant database
                $this->tenantService->switchToTenant($tenant);
                
                // Check if settings table exists
                $tables = DB::select("SHOW TABLES LIKE 'settings'");
                
                if (empty($tables)) {
                    $this->warn("Settings table does not exist. Creating...");
                    
                    // Create the settings table
                    DB::statement("CREATE TABLE IF NOT EXISTS settings (
                        id bigint unsigned NOT NULL AUTO_INCREMENT,
                        `group` varchar(100) NOT NULL,
                        `key` varchar(100) NOT NULL,
                        `value` text,
                        `type` varchar(50) DEFAULT 'text',
                        created_at timestamp NULL DEFAULT NULL,
                        updated_at timestamp NULL DEFAULT NULL,
                        PRIMARY KEY (id),
                        UNIQUE KEY settings_group_key_unique (`group`,`key`),
                        KEY settings_group_index (`group`),
                        KEY settings_key_index (`key`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                    
                    $this->info("✅ Settings table created successfully.");
                } else {
                    $this->info("✅ Settings table already exists.");
                }
                
                // Test the table by inserting a test record
                $testKey = 'test_' . time();
                DB::table('settings')->insert([
                    'group' => 'test',
                    'key' => $testKey,
                    'value' => 'test_value',
                    'type' => 'text',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Clean up test record
                DB::table('settings')->where('group', 'test')->where('key', $testKey)->delete();
                
                $this->info("✅ Settings table is working correctly.");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("❌ Error processing tenant {$tenant->name}: " . $e->getMessage());
                $errorCount++;
            } finally {
                // Always switch back to main database
                try {
                    $this->tenantService->switchToMain();
                } catch (\Exception $e) {
                    $this->error("Failed to switch back to main database: " . $e->getMessage());
                }
            }
        }

        $this->line("\n=== SUMMARY ===");
        $this->info("✅ Successfully processed: {$successCount} tenant(s)");
        if ($errorCount > 0) {
            $this->error("❌ Errors: {$errorCount} tenant(s)");
        }

        if ($errorCount === 0) {
            $this->info("🎉 All tenant databases now have the settings table!");
        }

        return $errorCount > 0 ? 1 : 0;
    }
} 