<?php

/**
 * Direct Database Analytics Verification
 * Run this script to verify tenant database counts directly
 */

require_once "vendor/autoload.php";

use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Services\TenantService;

$tenants = ["test2", "artc"];

foreach ($tenants as $tenantSlug) {
    echo "=== Tenant: {$tenantSlug} ===\n";
    
    try {
        $tenant = Tenant::where("slug", $tenantSlug)->first();
        if (!$tenant) {
            echo "❌ Tenant not found\n\n";
            continue;
        }
        
        $tenantService = app(TenantService::class);
        $tenantService->switchToTenant($tenant);
        
        echo "Database: {$tenant->database_name}\n";
        
        // Check table existence and counts
        $tables = ["students", "programs", "modules", "enrollments", "student_enrollments"];
        
        foreach ($tables as $table) {
            try {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $count = DB::table($table)->count();
                    echo "✅ {$table}: {$count} records\n";
                } else {
                    echo "❌ {$table}: Table does not exist\n";
                }
            } catch (Exception $e) {
                echo "⚠️  {$table}: Error - {$e->getMessage()}\n";
            }
        }
        
        // Additional checks
        echo "\nDetailed Analysis:\n";
        
        // Students by status
        try {
            $activeStudents = DB::table("students")->where("is_archived", 0)->count();
            $archivedStudents = DB::table("students")->where("is_archived", 1)->count();
            echo "- Active students: {$activeStudents}\n";
            echo "- Archived students: {$archivedStudents}\n";
        } catch (Exception $e) {
            echo "- Students status check failed: {$e->getMessage()}\n";
        }
        
        // Programs by status
        try {
            $activePrograms = DB::table("programs")->where("is_archived", 0)->count();
            $archivedPrograms = DB::table("programs")->where("is_archived", 1)->count();
            echo "- Active programs: {$activePrograms}\n";
            echo "- Archived programs: {$archivedPrograms}\n";
        } catch (Exception $e) {
            echo "- Programs status check failed: {$e->getMessage()}\n";
        }
        
        $tenantService->switchToMain();
        
    } catch (Exception $e) {
        echo "❌ Error testing tenant {$tenantSlug}: {$e->getMessage()}\n";
    }
    
    echo "\n";
}
