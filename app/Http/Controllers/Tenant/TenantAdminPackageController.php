<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Services\TenantService;

class TenantAdminPackageController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display all packages for the tenant.
     */
    public function index($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Load packages with enrollments count
            $packages = DB::table('packages')
                ->orderBy('created_at', 'desc')
                ->get();

            // Load programs for dropdown
            $programs = DB::table('programs')
                ->orderBy('program_name', 'asc')
                ->get();

            // Load modules for dynamic selection
            $modules = DB::table('modules')
                ->orderBy('module_name', 'asc')
                ->get();

            // Calculate analytics
            $totalPackages = $packages->count();
            $totalEnrollments = DB::table('registrations')->count();
            $totalRevenue = 0; // No amount_paid column in registrations table

            // Get popular package
            $popularPackage = DB::table('packages')
                ->leftJoin('registrations', 'packages.package_id', '=', 'registrations.package_id')
                ->select('packages.*', DB::raw('COUNT(registrations.registration_id) as enrollment_count'))
                ->groupBy('packages.package_id')
                ->orderBy('enrollment_count', 'desc')
                ->first();

            $analytics = [
                'totalPackages' => $totalPackages,
                'totalEnrollments' => $totalEnrollments,
                'totalRevenue' => $totalRevenue,
                'popularPackage' => $popularPackage
            ];

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-packages.admin-packages', compact('packages', 'programs', 'modules', 'analytics', 'tenantModel'));

        } catch (\Exception $e) {
            Log::error('Tenant packages index error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return view('admin.admin-packages.admin-packages', [
                'packages' => collect(),
                'programs' => collect(),
                'modules' => collect(),
                'analytics' => [
                    'totalPackages' => 0,
                    'totalEnrollments' => 0,
                    'totalRevenue' => 0,
                    'popularPackage' => null
                ],
                'tenantModel' => null
            ]);
        }
    }

    /**
     * Store a newly created package in tenant database.
     */
    public function store(Request $request, $tenant)
    {
        try {
            $request->validate([
                'package_name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'package_type' => 'required|in:full,modular',
                'module_count' => 'nullable|integer|min:1|max:50',
                'selected_modules' => 'nullable|array',
                'selected_modules.*' => 'exists:modules,modules_id',
                'selected_courses' => 'nullable|array',
                'selected_courses.*' => 'exists:courses,subject_id',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Create package
            $packageId = DB::table('packages')->insertGetId([
                'package_name' => $request->package_name,
                'description' => $request->description,
                'amount' => $request->amount,
                'package_type' => $request->package_type,
                'module_count' => $request->module_count,
                'price' => $request->amount, // For compatibility
                'created_by_admin_id' => Auth::user()->admin_id ?? 1,
                'access_period_days' => $request->access_period_days ?? null,
                'access_period_months' => $request->access_period_months ?? null,
                'access_period_years' => $request->access_period_years ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Attach selected modules if any
            if ($request->selected_modules) {
                foreach ($request->selected_modules as $moduleId) {
                    DB::table('package_modules')->insert([
                        'package_id' => $packageId,
                        'modules_id' => $moduleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Attach selected courses if any
            if ($request->selected_courses) {
                foreach ($request->selected_courses as $courseId) {
                    DB::table('package_courses')->insert([
                        'package_id' => $packageId,
                        'subject_id' => $courseId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Switch back to main database
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => true,
                'message' => 'Package added successfully to tenant database.',
                'package_id' => $packageId
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant package creation error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create package: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get modules for a specific program.
     */
    public function getProgramModules(Request $request, $tenant)
    {
        try {
            $request->validate([
                'program_id' => 'required|integer'
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $programId = $request->program_id;

            // Get modules for the specified program
            $modules = DB::table('modules')
                ->where('program_id', $programId)
                ->where('is_archived', false)
                ->orderBy('module_order', 'asc')
                ->get();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => true,
                'modules' => $modules
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant get program modules error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => false,
                'message' => 'Failed to get modules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get courses for a specific module.
     */
    public function getModuleCourses(Request $request, $tenant)
    {
        try {
            $request->validate([
                'module_id' => 'required|integer'
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $moduleId = $request->module_id;

            // Get courses for the specified module
            $courses = DB::table('courses')
                ->where('module_id', $moduleId)
                ->where('is_archived', false)
                ->orderBy('subject_order', 'asc')
                ->get();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => true,
                'courses' => $courses
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant get module courses error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => false,
                'message' => 'Failed to get courses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific package.
     */
    public function show($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $package = DB::table('packages')->where('package_id', $id)->first();

            if (!$package) {
                $this->tenantService->switchToMain();
                return response()->json([
                    'success' => false,
                    'message' => 'Package not found.'
                ], 404);
            }

            // Get associated modules
            $modules = DB::table('package_modules')
                ->join('modules', 'package_modules.modules_id', '=', 'modules.modules_id')
                ->where('package_modules.package_id', $id)
                ->select('modules.*')
                ->get();

            // Get associated courses
            $courses = DB::table('package_courses')
                ->join('courses', 'package_courses.subject_id', '=', 'courses.subject_id')
                ->where('package_courses.package_id', $id)
                ->select('courses.*')
                ->get();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => true,
                'package' => $package,
                'modules' => $modules,
                'courses' => $courses
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant package show error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => false,
                'message' => 'Failed to get package: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a package.
     */
    public function update(Request $request, $tenant, $id)
    {
        try {
            $request->validate([
                'package_name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'package_type' => 'required|in:full,modular',
                'module_count' => 'nullable|integer|min:1|max:50',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update package
            DB::table('packages')
                ->where('package_id', $id)
                ->update([
                    'package_name' => $request->package_name,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'package_type' => $request->package_type,
                    'module_count' => $request->module_count,
                    'price' => $request->amount,
                    'access_period_days' => $request->access_period_days ?? null,
                    'access_period_months' => $request->access_period_months ?? null,
                    'access_period_years' => $request->access_period_years ?? null,
                    'updated_at' => now(),
                ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => true,
                'message' => 'Package updated successfully in tenant database.'
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant package update error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update package: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a package.
     */
    public function destroy($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Check if package exists
            $package = DB::table('packages')->where('package_id', $id)->first();

            if (!$package) {
                $this->tenantService->switchToMain();
                return response()->json([
                    'success' => false,
                    'message' => 'Package not found.'
                ], 404);
            }

            // Check if package has enrollments
            $enrollmentCount = DB::table('registrations')
                ->where('package_id', $id)
                ->count();

            if ($enrollmentCount > 0) {
                $this->tenantService->switchToMain();
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete package with active enrollments.'
                ], 400);
            }

            // Delete package relationships first
            DB::table('package_modules')->where('package_id', $id)->delete();
            DB::table('package_courses')->where('package_id', $id)->delete();

            // Delete the package
            DB::table('packages')->where('package_id', $id)->delete();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => true,
                'message' => 'Package deleted successfully from tenant database.'
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant package deletion error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete package: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive a package.
     */
    public function archive($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            DB::table('packages')
                ->where('package_id', $id)
                ->update([
                    'is_archived' => true,
                    'updated_at' => now()
                ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => true,
                'message' => 'Package archived successfully in tenant database.'
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant package archive error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => false,
                'message' => 'Failed to archive package: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore an archived package.
     */
    public function restore($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            DB::table('packages')
                ->where('package_id', $id)
                ->update([
                    'is_archived' => false,
                    'updated_at' => now()
                ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => true,
                'message' => 'Package restored successfully in tenant database.'
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant package restore error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return response()->json([
                'success' => false,
                'message' => 'Failed to restore package: ' . $e->getMessage()
            ], 500);
        }
    }
}
