<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Create a new tenant with a copy of the template database
     */
    public function createTenant($name, $domain, $explicitDatabaseName = null)
    {
        try {
            // Generate database name based on tenant name (slug) unless an explicit database name was provided.
            // We intentionally ignore the domain (which may contain .smartprep.local) to avoid suffixes like
            // "-smartprep-local" ending up in the physical database name. Desired pattern: smartprep_<slug>
            $slug = Str::slug($name);
            $databaseName = $explicitDatabaseName ?: ('smartprep_' . $slug);
            
            // Create the tenant record first
            $tenant = Tenant::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'domain' => $domain,
                'database_name' => $databaseName,
            ]);
            
            // Create the database
            $this->createTenantDatabase($databaseName);
            
            // Copy structure and data from template
            $this->copyTemplateDatabase($databaseName);
            
            return $tenant;
            
        } catch (\Exception $e) {
            // If tenant was created but database setup failed, clean up
            if (isset($tenant)) {
                $tenant->delete();
            }
            throw $e;
        }
    }
    
    /**
     * Create a new database for the tenant
     */
    private function createTenantDatabase($databaseName)
    {
        $charset = config('database.connections.mysql.charset', 'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');
        
        DB::statement("CREATE DATABASE IF NOT EXISTS `$databaseName` CHARACTER SET $charset COLLATE $collation");
    }
    
    /**
     * Copy the template database (smartprep_artc) to the new tenant database
     */
    private function copyTemplateDatabase($targetDatabase)
    {
        $sourceDatabase = 'smartprep_artc';
        
        // Get all tables from source database
        $tables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?", [$sourceDatabase]);
        
        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            
            // Copy table structure
            DB::statement("CREATE TABLE `$targetDatabase`.`$tableName` LIKE `$sourceDatabase`.`$tableName`");
            
            // Copy table data (excluding sensitive user data for new tenants)
            if ($this->shouldCopyTableData($tableName)) {
                DB::statement("INSERT INTO `$targetDatabase`.`$tableName` SELECT * FROM `$sourceDatabase`.`$tableName`");
            }
        }
    }
    
    /**
     * Determine if we should copy data for a specific table
     */
    private function shouldCopyTableData($tableName)
    {
        // Don't copy user-specific data for new tenants
        $excludeDataTables = [
            'users',
            'password_resets',
            'personal_access_tokens',
            'sessions',
            'quiz_attempts',
            'enrollments',
            'user_progress',
            'notifications'
        ];
        
        return !in_array($tableName, $excludeDataTables);
    }
    
    /**
     * Switch to tenant database context
     */
    public function switchToTenant(Tenant $tenant)
    {
        // Configure tenant connection with the correct database
        config(['database.connections.tenant.database' => $tenant->database_name]);
        DB::purge('tenant');
        
        // Set tenant connection as the default
        config(['database.default' => 'tenant']);
        
        // Clear any cached connections
        DB::purge('mysql');
        
        // Establish the tenant connection
        DB::connection('tenant');
    }
    
    /**
     * Switch back to main database
     */
    public function switchToMain()
    {
        // Set mysql connection as the default
        config(['database.default' => 'mysql']);
        
        // Clear any cached connections
        DB::purge('tenant');
        DB::purge('mysql');
        
        // Establish the main connection
        DB::connection('mysql');
    }
    
    /**
     * Get tenant by domain
     */
    public function getTenantByDomain($domain)
    {
        return Tenant::where('domain', $domain)->first();
    }
    
    /**
     * Delete tenant and its database
     */
    public function deleteTenant(Tenant $tenant)
    {
        // Drop the database
        DB::statement("DROP DATABASE IF EXISTS `{$tenant->database_name}`");
        
        // Delete the tenant record
        $tenant->delete();
    }
    
    /**
     * List all tenants
     */
    public function getAllTenants()
    {
        return Tenant::all();
    }
}
