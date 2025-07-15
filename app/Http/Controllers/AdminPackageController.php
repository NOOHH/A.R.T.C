<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Program;
use App\Models\Module;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;

class AdminPackageController extends Controller
{
    /**
     * Display all packages.
     */
    public function index()
    {
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
            'selected_modules.*' => 'exists:modules,id',
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
        $package->save();

        // Attach selected modules if any
        if ($request->selected_modules) {
            $package->modules()->attach($request->selected_modules);
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
            'selected_modules.*' => 'exists:modules,id',
        ]);

        $package = Package::findOrFail($id);
        $package->package_name = $request->package_name;
        $package->description = $request->description;
        $package->amount = $request->amount;
        $package->package_type = $request->package_type;
        $package->program_id = $request->program_id;
        $package->module_count = $request->module_count;
        $package->price = $request->amount; // For compatibility
        $package->save();

        // Sync selected modules
        if ($request->selected_modules) {
            $package->modules()->sync($request->selected_modules);
        } else {
            $package->modules()->detach();
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
        $package = Package::with('modules')->findOrFail($id);
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
        $package = Package::findOrFail($id);
        
        // Check if package has enrollments
        if ($package->enrollments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete package with existing enrollments.'
            ], 400);
        }
        
        // Detach modules
        $package->modules()->detach();
        
        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Package deleted successfully.'
        ]);
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
        $package->status = 'archived';
        $package->save();

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
        $package->status = 'active';
        $package->save();

        return response()->json([
            'success' => true,
            'message' => 'Package restored successfully.'
        ]);
    }
}
