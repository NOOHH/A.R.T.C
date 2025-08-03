<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class EmergencyModularController extends Controller
{
    /**
     * Emergency handler for modular enrollment
     * This is a backup in case the main controller has issues
     */
    public function showEmergencyModularForm(Request $request)
    {
        try {
            Log::info('Emergency Modular Enrollment form accessed', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);
            
            // First try the simplified view
            if (View::exists('registration.simplified_modular_enrollment')) {
                return view('registration.simplified_modular_enrollment');
            }
            // If that doesn't exist, try the full view
            else if (View::exists('registration.Modular_enrollment')) {
                // Return minimal data to render the view
                return view('registration.Modular_enrollment', [
                    'programs' => [],
                    'packages' => [],
                    'programId' => null,
                    'formRequirements' => [],
                    'educationLevels' => [],
                    'student' => null,
                    'modularPlan' => null
                ]);
            } else {
                Log::error('No modular enrollment view found');
                return response('Modular enrollment form is currently unavailable. Please contact support.', 500);
            }
        } catch (\Exception $e) {
            Log::error('Emergency modular enrollment error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            // Return a simple response
            return response('Error loading modular enrollment form: ' . $e->getMessage(), 500);
        }
    }
}
