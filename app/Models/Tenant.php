<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Tenant extends Model
{
    // Always use main database connection for tenant lookups
    protected $connection = 'mysql';
    
    protected $fillable = [
        'name',
        'slug',
        'database_name',
        'domain',
        'status',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    /**
     * Switch to tenant database
     */
    public static function switchToTenant($tenantSlug)
    {
        $tenant = self::where('slug', $tenantSlug)->first();
        
        if (!$tenant) {
            throw new \Exception("Tenant not found: {$tenantSlug}");
        }

        // Switch to tenant database
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');

        return $tenant;
    }

    /**
     * Switch back to main database
     */
    public static function switchToMain()
    {
        Config::set('database.connections.mysql.database', env('DB_DATABASE', 'smartprep'));
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    /**
     * Get current tenant from request
     */
    public static function current()
    {
        $tenantSlug = request()->segment(2); // t/{tenant}
        
        if ($tenantSlug && $tenantSlug !== 'admin') {
            return self::where('slug', $tenantSlug)->first();
        }

        return null;
    }

    /**
     * Check if current request is for a tenant
     */
    public static function isTenantRequest()
    {
        $segments = request()->segments();
        return count($segments) > 1 && $segments[0] === 't';
    }
}
