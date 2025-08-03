<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnrollmentDebugController extends Controller
{
    /**
     * Log click events for debugging enrollment navigation
     */
    public function logClick(Request $request)
    {
        $button = $request->query('button', 'unknown');
        $target = $request->query('target', 'unknown');
        
        Log::info('Enrollment button click detected', [
            'button' => $button,
            'target' => $target,
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Test the accessibility of the modular enrollment view directly
     */
    public function testModularView(Request $request)
    {
        try {
            Log::info('Testing modular enrollment view render');
            
            // Check if the view file exists
            if (!view()->exists('registration.Modular_enrollment')) {
                Log::error('Modular_enrollment view does not exist!');
                return response()->json([
                    'success' => false,
                    'message' => 'View file does not exist',
                    'view_name' => 'registration.Modular_enrollment'
                ], 404);
            }
            
            // Try to get mock data for rendering the view
            $programs = \App\Models\Program::take(2)->get();
            $packages = \App\Models\Package::take(2)->get();
            $educationLevels = \App\Models\EducationLevel::take(2)->get();
            $modularPlan = \App\Models\Plan::where('plan_id', 2)->first();
            
            // Try to render the view with minimal data
            $renderedView = view('registration.Modular_enrollment', [
                'programs' => $programs, 
                'packages' => $packages,
                'programId' => null,
                'formRequirements' => [],
                'educationLevels' => $educationLevels,
                'student' => null,
                'modularPlan' => $modularPlan
            ])->render();
            
            // If we got here, the view rendered successfully
            Log::info('Modular_enrollment view rendered successfully');
            
            return response()->json([
                'success' => true,
                'message' => 'View rendered successfully',
                'data_counts' => [
                    'programs' => count($programs),
                    'packages' => count($packages),
                    'education_levels' => count($educationLevels)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error testing modular view: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error rendering view: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create a direct redirect to the modular enrollment page
     */
    public function redirectToModular()
    {
        Log::info('Direct redirect to modular enrollment triggered');
        return redirect()->route('enrollment.modular');
    }
    
    /**
     * Force direct navigation to modular enrollment with headers
     */
    public function forceDirectNavigation()
    {
        Log::info('Force direct navigation to modular enrollment triggered');
        header('Location: /enrollment/modular');
        exit;
    }
}
