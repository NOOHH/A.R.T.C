<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Director;
use App\Models\Program;
use App\Models\Student;
use App\Models\Module;
use App\Models\Registration;
use App\Models\Enrollment;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Log;

class DirectorDashboardController extends Controller
{
    public function index()
    {
        try {
            // Get director information from session
            $directorId = session('user_id') ?? $_SESSION['user_id'] ?? null;
            
            if (!$directorId) {
                return redirect()->route('login')->with('error', 'Please log in to access this page.');
            }

            // Get director data
            $director = Director::find($directorId);
            
            if (!$director) {
                return redirect()->route('login')->with('error', 'Director not found.');
            }

            // Get director's accessible programs
            $programs = $director->has_all_program_access ? 
                        Program::where('is_archived', false)->get() : 
                        $director->assignedPrograms()->where('is_archived', false)->get();

            // Get analytics data based on director's access
            $programIds = $programs->pluck('program_id')->toArray();
            
            $analytics = [
                'accessible_programs' => $programs->count(),
                'total_students' => Student::whereHas('enrollments', function($query) use ($programIds) {
                    $query->whereIn('program_id', $programIds);
                })->count(),
                'total_modules' => Module::whereIn('program_id', $programIds)->count(),
                'total_enrollments' => Enrollment::whereIn('program_id', $programIds)->count(),
                'pending_registrations' => Registration::whereIn('program_id', $programIds)
                    ->where('status', 'pending')->count(),
                'new_students_this_month' => Student::whereHas('enrollments', function($query) use ($programIds) {
                    $query->whereIn('program_id', $programIds);
                })->whereMonth('created_at', now()->month)->count(),
                'modules_this_week' => Module::whereIn('program_id', $programIds)
                    ->where('created_at', '>=', now()->startOfWeek())->count(),
                'archived_programs' => $director->has_all_program_access ? 
                    Program::where('is_archived', true)->count() : 
                    $director->assignedPrograms()->where('is_archived', true)->count(),
            ];

            // Get recent registrations for director's programs
            $registrations = Registration::whereIn('program_id', $programIds)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Get director's feature permissions
            $directorFeatures = $this->getDirectorFeatures($directorId);

            return view('director.dashboard', compact('director', 'programs', 'analytics', 'registrations', 'directorFeatures'));
            
        } catch (\Exception $e) {
            Log::error('Director dashboard error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'An error occurred while loading the dashboard.');
        }
    }

    private function getDirectorFeatures($directorId)
    {
        // Get director-specific feature permissions from admin settings
        $features = AdminSetting::where('setting_key', 'director_features')->first();
        
        if (!$features) {
            // Default features if not set
            return [
                'view_students' => true,
                'manage_programs' => false,
                'manage_modules' => false,
                'manage_enrollments' => true,
                'view_analytics' => true,
                'manage_professors' => false,
                'manage_batches' => false,
                'view_chat_logs' => false,
                'manage_settings' => false,
            ];
        }

        return json_decode($features->setting_value, true) ?? [];
    }
}
