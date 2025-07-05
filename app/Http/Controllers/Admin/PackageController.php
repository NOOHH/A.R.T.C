<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    public function index()
    {
        try {
            $packages = Package::with('program')->get();
            $programs = Program::where('is_archived', false)->get();
            
            return view('admin.admin-packages.admin-packages', compact('packages', 'programs'));
        } catch (\Exception $e) {
            Log::error('Package index error: ' . $e->getMessage());
            return view('admin.admin-packages.admin-packages', [
                'packages' => collect(),
                'programs' => collect(),
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
                'amount' => 'required|numeric|min:0',
                'program_id' => 'required|exists:programs,program_id'
            ]);

            Package::create([
                'package_name' => $validated['package_name'],
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'program_id' => $validated['program_id'],
                'created_by_admin_id' => session('admin_id', 1)
            ]);

            return redirect()->route('admin.packages.index')
                           ->with('success', 'Package created successfully!');
        } catch (\Exception $e) {
            Log::error('Package creation error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error creating package. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $package = Package::findOrFail($id);
            
            $validated = $request->validate([
                'package_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'program_id' => 'required|exists:programs,program_id'
            ]);

            $package->update($validated);

            return redirect()->route('admin.packages.index')
                           ->with('success', 'Package updated successfully!');
        } catch (\Exception $e) {
            Log::error('Package update error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error updating package. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $package = Package::findOrFail($id);
            
            // Check if package has any enrollments
            if ($package->enrollments()->count() > 0) {
                return redirect()->back()
                               ->with('error', 'Cannot delete package with existing enrollments.');
            }
            
            $package->delete();

            return redirect()->route('admin.packages.index')
                           ->with('success', 'Package deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Package deletion error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error deleting package. Please try again.');
        }
    }
}