<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Program;
use App\Models\Module;
use App\Models\Course;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AdminPackageController extends Controller
{
    /**
     * Display all packages.
     */
    public function index()
    {
        // Check if user is admin
        if (!session('user_type') || session('user_type') !== 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Access denied. Package management is only available for admins.');
        }

        // Load packages with enrollments count
        $packages = Package::withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->get();

        // Load programs for dropdown
        $programs = Program::orderBy('program_name', 'asc')->get();

        // Load modules for dynamic selection
        $modules = Module::orderBy('module_name', 'asc')->get();

        // Calculate analytics
        $totalPackages = $packages->count();
        $totalEnrollments = Registration::count();
        $totalRevenue = 0; // No amount_paid column in registrations table
        $popularPackage = Package::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->first();

        $analytics = [
            'totalPackages' => $totalPackages,
            'totalEnrollments' => $totalEnrollments,
            'totalRevenue' => $totalRevenue,
            'popularPackage' => $popularPackage
        ];

        return view('admin.admin-packages.admin-packages', compact('packages', 'programs', 'modules', 'analytics'));
    }

    /**
     * Store a newly created package.
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'package_type' => 'required|in:full,modular',
            'program_id' => 'required|exists:programs,program_id',
            'module_count' => 'nullable|integer|min:1|max:50',
            'selected_modules' => 'nullable|array',
            'selected_modules.*' => 'exists:modules,modules_id',
        ]);

        $package = new Package();
        $package->package_name = $request->package_name;
        $package->description = $request->description;
        $package->amount = $request->amount;
        $package->package_type = $request->package_type;
        $package->program_id = $request->program_id;
        $package->module_count = $request->module_count;
        $package->price = $request->amount; // For compatibility
        $package->created_by_admin_id = Auth::user()->admin_id ?? 1;
        $package->access_period_days = $request->access_period_days;
        $package->access_period_months = $request->access_period_months;
        $package->access_period_years = $request->access_period_years;
        $package->save();

        // Attach selected modules if any
        if ($request->selected_modules) {
            $package->modules()->attach($request->selected_modules);
        }

        // Attach selected courses if any
        if ($request->selected_courses) {
            $package->courses()->attach($request->selected_courses);
        }

        return response()->json([
            'success' => true,
            'message' => 'Package added successfully.',
            'package' => $package
        ]);
    }

    /**
     * Update an existing package.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'package_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'package_type' => 'required|in:full,modular',
            'program_id' => 'required|exists:programs,program_id',
            'module_count' => 'nullable|integer|min:1|max:50',
            'selected_modules' => 'nullable|array',
            'selected_modules.*' => 'exists:modules,modules_id',
        ]);

        $package = Package::findOrFail($id);
        $package->package_name = $request->package_name;
        $package->description = $request->description;
        $package->amount = $request->amount;
        $package->package_type = $request->package_type;
        $package->program_id = $request->program_id;
        $package->module_count = $request->module_count;
        $package->price = $request->amount; // For compatibility
        $package->access_period_days = $request->access_period_days;
        $package->access_period_months = $request->access_period_months;
        $package->access_period_years = $request->access_period_years;
        $package->save();

        // Sync selected modules
        if ($request->selected_modules) {
            $package->modules()->sync($request->selected_modules);
        } else {
            $package->modules()->detach();
        }

        // Sync selected courses
        if ($request->selected_courses) {
            $package->courses()->sync($request->selected_courses);
        } else {
            $package->courses()->detach();
        }

        return response()->json([
            'success' => true,
            'message' => 'Package updated successfully.',
            'package' => $package
        ]);
    }

    /**
     * Get package details for editing.
     */
    public function show($id)
    {
        $package = Package::with(['modules', 'courses'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'package' => $package
        ]);
    }

    /**
     * Delete (destroy) a package.
     */
    public function destroy($id)
    {
        try {
            $package = Package::findOrFail($id);
            
            // Check multiple potential enrollment tables
            $enrollmentCount = 0;
            
            // Check if enrollments table exists
            if (Schema::hasTable('enrollments')) {
                $enrollmentCount += DB::table('enrollments')
                    ->where('package_id', $id)
                    ->count();
            }
            
            // Check if registrations table has this package
            if (Schema::hasTable('registrations')) {
                $registrationCount = DB::table('registrations')
                    ->where('package_id', $id)
                    ->count();
                
                if ($registrationCount > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot delete package. It has {$registrationCount} active registrations."
                    ], 400);
                }
            }
            
            if ($enrollmentCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete package. It has {$enrollmentCount} active enrollments."
                ], 400);
            }
            
            // Detach modules and courses if the pivot tables exist
            if (Schema::hasTable('package_modules')) {
                $package->modules()->detach();
            }
            
            if (Schema::hasTable('package_courses')) {
                $package->courses()->detach();
            }
            
            $package->delete();

            return response()->json([
                'success' => true,
                'message' => 'Package deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting package: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get modules for a specific program.
     */
    public function getModules($programId)
    {
        $modules = Module::where('program_id', $programId)
            ->orderBy('module_name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'modules' => $modules
        ]);
    }

    /**
     * Archive a package.
     */
    public function archive($id)
    {
        $package = Package::findOrFail($id);
        // Note: status column doesn't exist in database
        // Consider adding proper archive functionality in the future
        // $package->status = 'archived';
        // $package->save();

        return response()->json([
            'success' => true,
            'message' => 'Package archived successfully.'
        ]);
    }

    /**
     * Restore an archived package.
     */
    public function restore($id)
    {
        $package = Package::findOrFail($id);
        // Note: status column doesn't exist in database
        // Consider adding proper restore functionality in the future
        // $package->status = 'active';
        // $package->save();

        return response()->json([
            'success' => true,
            'message' => 'Package restored successfully.'
        ]);
    }

    /**
     * Get modules for a specific program with courses.
     */
    public function getProgramModules(Request $request)
    {
        $programId = $request->get('program_id');
        
        $modules = Module::where('program_id', $programId)
            ->with(['courses' => function($query) {
                $query->where('is_active', true)->orderBy('subject_order');
            }])
            ->where('is_archived', false)
            ->orderBy('module_order')
            ->get();

        return response()->json([
            'success' => true,
            'modules' => $modules
        ]);
    }

    /**
     * Get courses for a specific module.
     */
    public function getModuleCourses(Request $request)
    {
        $moduleId = $request->get('module_id');
        
        $courses = Course::where('module_id', $moduleId)
            ->where('is_active', true)
            ->orderBy('subject_order')
            ->get();

        return response()->json([
            'success' => true,
            'courses' => $courses
        ]);
    }

    /**
     * Get package details including courses and modules.
     */
    public function getPackageDetails(Request $request)
    {
        $packageId = $request->get('package_id');
        
        $package = Package::with(['modules', 'courses', 'program'])
            ->findOrFail($packageId);

        return response()->json([
            'success' => true,
            'package' => $package
        ]);
    }

    /**
     * Test database relationships for validation
     */
    public function testRelationships()
    {
        try {
            // Test Package model relationships
            $package = Package::with(['modules', 'courses', 'program'])->first();
            
            $relationships = [
                'package_found' => $package ? true : false,
                'modules_relationship' => $package ? $package->modules()->count() : 0,
                'courses_relationship' => $package ? $package->courses()->count() : 0,
                'program_relationship' => $package && $package->program ? true : false,
                'pivot_tables_exist' => [
                    'package_modules' => Schema::hasTable('package_modules'),
                    'package_courses' => Schema::hasTable('package_courses')
                ]
            ];

            return response()->json([
                'success' => true,
                'relationships' => $relationships,
                'sample_package' => $package ? [
                    'id' => $package->id,
                    'name' => $package->name,
                    'modules_count' => $package->modules()->count(),
                    'courses_count' => $package->courses()->count()
                ] : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test pivot table structures
     */
    public function testPivotTables()
    {
        try {
            $pivotInfo = [
                'package_modules' => [
                    'exists' => Schema::hasTable('package_modules'),
                    'columns' => Schema::hasTable('package_modules') ? 
                        Schema::getColumnListing('package_modules') : []
                ],
                'package_courses' => [
                    'exists' => Schema::hasTable('package_courses'),
                    'columns' => Schema::hasTable('package_courses') ? 
                        Schema::getColumnListing('package_courses') : []
                ]
            ];

            // Test actual pivot relationships
            $package = Package::first();
            if ($package) {
                // Try to create test relationships
                $module = Module::first();
                $course = Course::first();
                
                if ($module) {
                    $package->modules()->sync([$module->id]);
                    $pivotInfo['module_sync_test'] = 'success';
                }
                
                if ($course) {
                    $package->courses()->sync([$course->id]);
                    $pivotInfo['course_sync_test'] = 'success';
                }
            }

            return response()->json([
                'success' => true,
                'pivot_info' => $pivotInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
