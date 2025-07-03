<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
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

        return view('admin.admin-packages.admin-packages', compact('packages'));
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
        ]);

        $package = new Package();
        $package->package_name = $request->package_name;
        $package->description = $request->description;
        $package->amount = $request->amount;
        $package->created_by_admin_id = Auth::user()->admin_id ?? 1; // fallback to 1 if no admin_id
        $package->save();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package added successfully.');
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
        ]);

        $package = Package::findOrFail($id);
        $package->package_name = $request->package_name;
        $package->description = $request->description;
        $package->amount = $request->amount;
        $package->save();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Delete (destroy) a package.
     */
    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }
}
