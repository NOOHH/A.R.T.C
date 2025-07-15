<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Program;
use App\Models\Module;
use App\Models\Registration;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index()
    {
        try {
            $packages = Package::with(['program', 'modules'])
                ->withCount('registrations')
                ->get();
            
            $programs = Program::where('is_archived', false)->get();
            
            // Calculate analytics
            $totalPackages = $packages->count();
            $activeEnrollments = Registration::where('status', 'approved')->count();
            $totalRevenue = Registration::where('status', 'approved')
                ->join('packages', 'registrations.package_id', '=', 'packages.package_id')
                ->sum('packages.price');
            
            // Calculate popularity rate
            $popularityRate = $totalPackages > 0 ? 
                round(($activeEnrollments / ($totalPackages * 100)) * 100, 2) : 0;
            
            // Add enrollment counts to packages
            $packages->transform(function ($package) {
                $package->enrollments_count = Registration::where('package_id', $package->package_id)->count();
                return $package;
            });
            
            return view('admin.admin-packages.admin-packages', compact(
                'packages', 
                'programs', 
                'totalPackages', 
                'activeEnrollments', 
                'totalRevenue', 
                'popularityRate'
            ));
        } catch (\Exception $e) {
            Log::error('Package index error: ' . $e->getMessage());
            return view('admin.admin-packages.admin-packages', [
                'packages' => collect(),
                'programs' => collect(),
                'totalPackages' => 0,
                'activeEnrollments' => 0,
                'totalRevenue' => 0,
                'popularityRate' => 0,
                'dbError' => true
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'package_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'program_id' => 'required|exists:programs,program_id',
                'package_type' => 'required|in:full,modular',
                'module_count' => 'nullable|integer|min:1|max:50',
                'selected_modules' => 'nullable|array',
                'selected_modules.*' => 'exists:modules,modules_id'
            ]);

            DB::beginTransaction();

            $package = Package::create([
                'package_name' => $validated['package_name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'program_id' => $validated['program_id'],
                'package_type' => $validated['package_type'],
                'module_count' => $validated['package_type'] === 'modular' ? $validated['module_count'] : null,
                'created_by_admin_id' => session('admin_id', 1)
            ]);

            // Attach selected modules if provided
            if (!empty($validated['selected_modules'])) {
                $package->modules()->attach($validated['selected_modules']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Package created successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Package creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating package. Please try again.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $package = Package::with(['program', 'modules'])->findOrFail($id);
            
            return response()->json([
                'package_id' => $package->package_id,
                'package_name' => $package->package_name,
                'description' => $package->description,
                'price' => $package->price,
                'program_id' => $package->program_id,
                'package_type' => $package->package_type,
                'module_count' => $package->module_count,
                'selected_modules' => $package->modules->pluck('modules_id')->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('Package show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $package = Package::findOrFail($id);
            
            $validated = $request->validate([
                'package_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'program_id' => 'required|exists:programs,program_id',
                'package_type' => 'required|in:full,modular',
                'module_count' => 'nullable|integer|min:1|max:50',
                'selected_modules' => 'nullable|array',
                'selected_modules.*' => 'exists:modules,modules_id'
            ]);

            DB::beginTransaction();

            $package->update([
                'package_name' => $validated['package_name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'program_id' => $validated['program_id'],
                'package_type' => $validated['package_type'],
                'module_count' => $validated['package_type'] === 'modular' ? $validated['module_count'] : null,
            ]);

            // Update module associations
            if (isset($validated['selected_modules'])) {
                $package->modules()->sync($validated['selected_modules']);
            } else {
                $package->modules()->detach();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Package updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Package update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating package. Please try again.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $package = Package::findOrFail($id);
            
            // Check if package has any enrollments
            $enrollmentCount = Registration::where('package_id', $id)->count();
            if ($enrollmentCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete package with existing enrollments.'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Remove module associations
            $package->modules()->detach();
            
            // Delete the package
            $package->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Package deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Package deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting package. Please try again.'
            ], 500);
        }
    }

    public function getModules($programId)
    {
        try {
            $modules = Module::where('program_id', $programId)
                ->where('is_archived', false)
                ->orderBy('module_name')
                ->get(['modules_id as id', 'module_name as name', 'description', 'duration', 'level']);
            
            return response()->json([
                'success' => true,
                'modules' => $modules
            ]);
        } catch (\Exception $e) {
            Log::error('Get modules error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading modules'
            ], 500);
        }
    }
}