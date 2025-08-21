<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\TenantProvisioner;

class HomeController extends Controller
{
    public function index($tenant)
    {
        // Resolve tenant; if missing, auto-create from matching client
        $tenantModel = $this->resolveOrProvisionTenant($tenant);
        $this->switchToTenantDB($tenantModel);
        
        // Use ARTC live preview UI (welcome.homepage) for tenant sites
        $tenantDb = DB::connection('tenant');
        $programs = $tenantDb->table('programs')
            ->where('is_archived', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Load homepage settings from tenant database (not main SmartPrep DB)
        // Since we've already switched to tenant DB, use Setting model which will use the tenant connection
        try {
            $homepageSettings = \App\Models\Setting::getGroup('homepage')->toArray();
        } catch (\Exception $e) {
            // Fallback to default settings if tenant doesn't have settings table
            $homepageSettings = [];
        }
        
        $homepageContent = array_merge([
            'hero_title' => 'Review Smarter. Learn Better. Succeed Faster.',
            'hero_subtitle' => 'Your premier destination for comprehensive review programs and professional training.',
            'hero_button_text' => 'ENROLL NOW',
            'programs_title' => 'Our Programs',
            'programs_subtitle' => 'Choose from our comprehensive range of review and training programs',
            'modalities_title' => 'Learning Modalities',
            'modalities_subtitle' => 'Choose the learning style that works best for you',
            'about_title' => 'About Us',
            'about_subtitle' => 'We are committed to providing high-quality education and training',
        ], $homepageSettings);

        $homepageTitle = $homepageContent['hero_button_text'] ?? 'ENROLL NOW';

        return view('welcome.homepage', compact('programs', 'homepageTitle', 'homepageContent'));
    }
    
    public function programs($tenant)
    {
        $tenantModel = $this->resolveOrProvisionTenant($tenant);
        $this->switchToTenantDB($tenantModel);
        
        $programs = DB::table('programs')
            ->where('is_archived', false)
            ->orderBy('program_name')
            ->get();
            
        return view('tenant.artc.programs', [
            'tenant' => $tenantModel,
            'programs' => $programs,
        ]);
    }
    
    public function programDetails($tenant, $id)
    {
        $tenantModel = $this->resolveOrProvisionTenant($tenant);
        $this->switchToTenantDB($tenantModel);
        
        $program = DB::connection('tenant')->table('programs')->where('program_id', $id)->where('is_archived', false)->firstOrFail();
        
        $courses = DB::table('courses')
            ->where('program_id', $id)
            ->where('is_archived', false)
            ->orderBy('course_order')
            ->get();
            
        foreach ($courses as $course) {
            $course->modules = DB::table('modules')
                ->where('course_id', $course->id)
                ->where('is_archived', false)
                ->orderBy('module_order')
                ->get();
        }
        
        return view('tenant.artc.program-details', [
            'tenant' => $tenantModel,
            'program' => $program,
            'courses' => $courses,
        ]);
    }
    
    private function switchToTenantDB($tenantModel)
    {
        $dbName = $tenantModel->database_name;
        if (!$dbName) {
            abort(500, 'Tenant database is not configured.');
        }
        // Use tenant connection slot if configured else fallback to mysql
        if (config()->has('database.connections.tenant')) {
            config(['database.connections.tenant.database' => $dbName]);
            DB::purge('tenant');
            DB::reconnect('tenant');
            // Ensure all subsequent DB::table() calls use the tenant connection
            config(['database.default' => 'tenant']);
        } else {
            config(['database.connections.mysql.database' => $dbName]);
            DB::purge('mysql');
            DB::reconnect('mysql');
            config(['database.default' => 'mysql']);
        }
    }

    /**
     * Resolve a tenant by slug; if missing, create it from a matching Client row.
     */
    private function resolveOrProvisionTenant(string $slug)
    {
        // Fast path: existing tenant
        $tenant = \App\Models\Tenant::where('slug', $slug)->first();
        if ($tenant) {
            return $tenant;
        }

        // Try to infer from clients only if a database already exists to avoid 500s
        $client = \App\Models\Client::where('slug', $slug)->first();
        if (!$client) {
            abort(404);
        }

        $dbName = $client->db_name;
        if (!$dbName) {
            $keyword = preg_replace('/^smartprep-/', '', $client->slug);
            $dbName = 'smartprep_' . Str::slug($keyword, '_');
        }

        // Verify database exists; if not, attempt provisioning from sample dump
        $existsRow = DB::selectOne('SELECT SCHEMA_NAME as s FROM information_schema.schemata WHERE SCHEMA_NAME = ?', [$dbName]);
        if (!$existsRow) {
            try {
                $conn = TenantProvisioner::createDatabaseFromSqlDump($client->name);
                $dbName = $conn['db_name'] ?? $dbName;
                // Persist db name back to client for future lookups
                $client->db_name = $dbName;
                $client->save();
            } catch (\Throwable $e) {
                // If provisioning fails, return 404 until admin approves/provisions
                abort(404);
            }
        }

        // Create tenant registry row
        return \App\Models\Tenant::updateOrCreate(
            ['slug' => $client->slug],
            [
                'name' => $client->name,
                'database_name' => $dbName,
                'domain' => $client->domain,
                'status' => $client->status ?? 'active',
                'settings' => ['client_id' => $client->id],
            ]
        );
    }
}
