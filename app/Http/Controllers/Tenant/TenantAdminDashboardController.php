<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Services\TenantService;
use Carbon\Carbon;

class TenantAdminDashboardController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display the admin dashboard with tenant-specific analytics.
     */
    public function index($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get real analytics data from tenant database
            $analytics = $this->calculateTenantAnalytics();
            
            // Get recent registrations
            $registrations = $this->getRecentRegistrations();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-dashboard.admin-dashboard', compact('analytics', 'registrations'))
                ->with('tenantModel', $tenantModel)
                ->with('dbError', null);

        } catch (\Exception $e) {
            Log::error('Tenant dashboard error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            // Return error view with mock data
            $analytics = [
                'total_students' => 0,
                'total_programs' => 0,
                'total_modules' => 0,
                'total_enrollments' => 0,
                'pending_registrations' => 0,
                'new_students_this_month' => 0,
                'modules_this_week' => 0,
                'archived_programs' => 0,
            ];
            
            return view('admin.admin-dashboard.admin-dashboard', compact('analytics'))
                ->with('registrations', collect())
                ->with('tenantModel', null)
                ->with('dbError', $e->getMessage());
        }
    }

    /**
     * Calculate real analytics from tenant database.
     */
    private function calculateTenantAnalytics()
    {
        // Total students
        $totalStudents = $this->safeQuery(function() {
            return DB::table('students')->where('is_archived', 0)->count();
        }, 0);

        // New students this month
        $newStudentsThisMonth = $this->safeQuery(function() {
            return DB::table('students')
                ->where('is_archived', 0)
                ->where('created_at', '>=', now()->startOfMonth())
                ->count();
        }, 0);

        // Total active programs
        $totalPrograms = $this->safeQuery(function() {
            return DB::table('programs')->where('is_archived', 0)->count();
        }, 0);

        // Archived programs
        $archivedPrograms = $this->safeQuery(function() {
            return DB::table('programs')->where('is_archived', 1)->count();
        }, 0);

        // Total modules
        $totalModules = $this->safeQuery(function() {
            return DB::table('modules')->where('is_archived', 0)->count();
        }, 0);

        // Modules added this week
        $modulesThisWeek = $this->safeQuery(function() {
            return DB::table('modules')
                ->where('is_archived', 0)
                ->where('created_at', '>=', now()->startOfWeek())
                ->count();
        }, 0);

        // Total enrollments (check for both 'enrollments' and 'student_enrollments' tables)
        $totalEnrollments = $this->safeQuery(function() {
            // Try enrollments table first
            if (DB::getSchemaBuilder()->hasTable('enrollments')) {
                return DB::table('enrollments')->count();
            }
            // Fallback to student_enrollments if it exists
            if (DB::getSchemaBuilder()->hasTable('student_enrollments')) {
                return DB::table('student_enrollments')->count();
            }
            // Count students with program assignments
            return DB::table('students')->whereNotNull('program_id')->count();
        }, 0);

        // Pending registrations
        $pendingRegistrations = $this->safeQuery(function() {
            // Check for registration approval workflow
            if (DB::getSchemaBuilder()->hasTable('student_registrations')) {
                return DB::table('student_registrations')->where('status', 'pending')->count();
            }
            // Alternative: count students pending approval
            return DB::table('students')->where('status', 'pending')->count();
        }, 0);

        return [
            'total_students' => $totalStudents,
            'total_programs' => $totalPrograms,
            'total_modules' => $totalModules,
            'total_enrollments' => $totalEnrollments,
            'pending_registrations' => $pendingRegistrations,
            'new_students_this_month' => $newStudentsThisMonth,
            'modules_this_week' => $modulesThisWeek,
            'archived_programs' => $archivedPrograms,
        ];
    }

    /**
     * Get recent registrations from tenant database.
     */
    private function getRecentRegistrations()
    {
        return $this->safeQuery(function() {
            if (DB::getSchemaBuilder()->hasTable('student_registrations')) {
                return DB::table('student_registrations')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function($registration) {
                        // Convert created_at string to Carbon instance
                        if (is_string($registration->created_at)) {
                            $registration->created_at = \Carbon\Carbon::parse($registration->created_at);
                        }
                        return $registration;
                    });
            }
            
            // Fallback to recent students
            return DB::table('students')
                ->select('student_id', 'firstname', 'lastname', 'email', 'created_at', 'status')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($student) {
                    // Convert created_at string to Carbon instance
                    if (is_string($student->created_at)) {
                        $student->created_at = \Carbon\Carbon::parse($student->created_at);
                    }
                    return $student;
                });
        }, collect());
    }

    /**
     * Safely execute database queries with error handling.
     */
    private function safeQuery(callable $query, $default = null)
    {
        try {
            return $query();
        } catch (\Exception $e) {
            Log::warning('Tenant analytics query failed: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * API endpoint for analytics data (for AJAX updates).
     */
    public function analyticsApi($tenant)
    {
        try {
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $analytics = $this->calculateTenantAnalytics();

            $this->tenantService->switchToMain();

            return response()->json([
                'success' => true,
                'analytics' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant analytics API error: ' . $e->getMessage());
            
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
