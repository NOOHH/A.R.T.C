<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class AdminPackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('enrollments')->get();
        return view('admin.admin-packages.admin-packages', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:100',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);
        $package = new Package();
        $package->package_name = $request->package_name;
        $package->description = $request->description;
        $package->amount = $request->amount;
        $package->created_by_admin_id = Auth::id() ?? 1; // fallback to 1 if not using Auth
        $package->save();
        return redirect()->route('admin.packages.index')->with('success', 'Package added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'package_name' => 'required|string|max:100',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);
        $package = Package::findOrFail($id);
        $package->package_name = $request->package_name;
        $package->description = $request->description;
        $package->amount = $request->amount;
        $package->save();
        return redirect()->route('admin.packages.index')->with('success', 'Package updated successfully.');
    }

    public function delete($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Package deleted successfully.');
    }

    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Package deleted successfully.');
    }
}
