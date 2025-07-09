<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;

class EnrollmentController extends Controller
{
    public function save(Request $request)
    {
        $validated = $request->validate([
            'enrollment_type' => 'required|in:full,modular',
            'course' => 'required|string',
            'package_id' => 'required|exists:packages,package_id',
        ]);

        $package = \App\Models\Package::find($validated['package_id']);

        $enrollment = new Enrollment();
        if ($validated['enrollment_type'] === 'modular') {
            $enrollment->Modular_enrollment = $validated['course'];
            $enrollment->Full_Program = '';
        } else {
            $enrollment->Modular_enrollment = '';
            $enrollment->Full_Program = $validated['course'];
        }
        $enrollment->package_id = $package->package_id;
        $enrollment->save();

        // Redirect to the appropriate page
        if ($validated['enrollment_type'] === 'full') {
            return redirect()->route('enrollment.full')->with('success', 'Enrollment saved!');
        } else {
            return redirect()->route('enrollment.modular')->with('success', 'Enrollment saved!');
        }
    }
}
