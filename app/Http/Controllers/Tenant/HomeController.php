<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index($tenant)
    {
        // Get tenant info
        $client = \App\Models\Client::where('slug', $tenant)->firstOrFail();
        
        // Switch to tenant database
        $this->switchToTenantDB($client);
        
        // Get basic data for the homepage
        $stats = [
            'total_programs' => DB::table('programs')->where('is_archived', false)->count(),
            'total_courses' => DB::table('courses')->where('is_archived', false)->count(),
            'total_modules' => DB::table('modules')->where('is_archived', false)->count(),
            'active_students' => DB::table('students')->where('is_archived', false)->count(),
        ];
        
        $programs = DB::table('programs')
            ->where('is_archived', false)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
            
        $announcements = DB::table('announcements')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('tenant.artc.home', compact('client', 'stats', 'programs', 'announcements'));
    }
    
    public function programs($tenant)
    {
        $client = \App\Models\Client::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($client);
        
        $programs = DB::table('programs')
            ->where('is_archived', false)
            ->orderBy('program_name')
            ->get();
            
        return view('tenant.artc.programs', compact('client', 'programs'));
    }
    
    public function programDetails($tenant, $id)
    {
        $client = \App\Models\Client::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($client);
        
        $program = DB::table('programs')->where('id', $id)->where('is_archived', false)->firstOrFail();
        
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
        
        return view('tenant.artc.program-details', compact('client', 'program', 'courses'));
    }
    
    private function switchToTenantDB($client)
    {
        $dbName = $client->db_name ?? $client->database ?? null;
        if (!$dbName) {
            abort(500, 'Client database is not configured.');
        }
        config(['database.connections.mysql.database' => $dbName]);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }
}
