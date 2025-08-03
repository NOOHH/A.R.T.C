<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Program;
use App\Models\Student;
use App\Models\AdminSetting;
use App\Models\FormRequirement;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfessorDashboardController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated professors can access these methods
        $this->middleware('professor.auth');
    }

    public function index()
    {
        // Get professor from session
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->with(['modules'])->get();
        
        // Calculate students for each program
        foreach ($assignedPrograms as $program) {
            $program->students = Student::whereHas('enrollments', function ($query) use ($program) {
                $query->where('program_id', $program->program_id);
            })->get();
        }
        
        // Get statistics
        $totalPrograms = $assignedPrograms->count();
        $totalStudents = $assignedPrograms->sum(function($program) {
            return $program->students->count();
        });
        $totalModules = $assignedPrograms->sum(function($program) {
            return $program->modules->count();
        });
        
        // Check if AI Quiz feature is enabled
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';

        // Check if announcement management is enabled for professors
        $announcementManagementEnabled = AdminSetting::where('setting_key', 'professor_announcement_management_enabled')->value('setting_value') === '1';

        // Placeholder for upcoming meetings (set to 0 for now)
        $upcomingMeetings = 0;
        // Placeholder for pending assignments (set to 0 for now)
        $pendingAssignments = 0;
        
        // Get targeted announcements for professor
        $programIds = $assignedPrograms->pluck('program_id')->toArray();
        $announcements = $this->getTargetedAnnouncementsForProfessor($programIds);
        
        return view('professor.dashboard', compact('professor', 'assignedPrograms', 'totalPrograms', 'totalStudents', 'totalModules', 'aiQuizEnabled', 'announcementManagementEnabled', 'upcomingMeetings', 'pendingAssignments', 'announcements'));
    }

    public function programs()
    {
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->with(['modules'])->get();
        
        // Calculate students for each program
        foreach ($assignedPrograms as $program) {
            $program->students = Student::whereHas('enrollments', function ($query) use ($program) {
                $query->where('program_id', $program->program_id);
            })->get();
        }
        
        return view('professor.programs', compact('professor', 'assignedPrograms'));
    }

    public function programDetails($programId)
    {
        $professor = Professor::find(session('professor_id'));
        $program = $professor->programs()
            ->where('professor_program.program_id', $programId)
            ->with(['modules.courses'])
            ->firstOrFail();
        
        // Calculate students for this program
        $program->students = Student::whereHas('enrollments', function ($query) use ($program) {
            $query->where('program_id', $program->program_id);
        })->get();
        
        // Get the video for this professor-program combination
        $videoData = $professor->programs()->where('professor_program.program_id', $programId)->first()->pivot;
        
        return view('professor.program-details', compact('professor', 'program', 'videoData'));
    }

    public function updateVideo(Request $request, $programId)
    {
        $request->validate([
            'video_link' => 'required|url',
            'video_description' => 'nullable|string|max:500'
        ]);

        $professor = Professor::find(session('professor_id'));
        
        // Update the pivot table
        $professor->programs()->updateExistingPivot($programId, [
            'video_link' => $request->video_link,
            'video_description' => $request->video_description,
            'updated_at' => now()
        ]);

        // Create announcement for students about the new video
        $program = Program::find($programId);
        
        Announcement::create([
            'professor_id' => $professor->professor_id,
            'program_id' => $programId,
            'title' => 'New Video Available: ' . $program->program_name,
            'content' => $request->video_description ?? 'A new video has been uploaded for this program.',
            'type' => 'video',
            'video_link' => $request->video_link,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Video link updated successfully and announcement sent to students!');
    }

    public function profile()
    {
        $professor = Professor::with(['programs', 'batches'])->find(session('professor_id'));
        
        // Get dynamic form fields for professors (if any exist)
        $dynamicFields = FormRequirement::where('entity_type', 'professor')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return view('professor.profile', compact('professor', 'dynamicFields'));
    }

    public function updateProfile(Request $request)
    {
        $professor = Professor::find(session('professor_id'));
        
        // Get dynamic fields for validation
        $dynamicFields = FormRequirement::where('entity_type', 'professor')
            ->where('is_active', true)
            ->get();
        
        $validationRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:professors,professor_email,' . $professor->professor_id . ',professor_id',
        ];
        
        // Add dynamic field validation rules
        foreach ($dynamicFields as $field) {
            $rule = [];
            if ($field->is_required) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }
            
            switch ($field->field_type) {
                case 'email':
                    $rule[] = 'email';
                    break;
                case 'phone':
                    $rule[] = 'string|max:20';
                    break;
                case 'date':
                    $rule[] = 'date';
                    break;
                case 'number':
                    $rule[] = 'numeric';
                    break;
                case 'text':
                case 'textarea':
                default:
                    $rule[] = 'string|max:500';
                    break;
            }
            
            $validationRules[$field->field_name] = implode('|', $rule);
        }
        
        $validatedData = $request->validate($validationRules);
        
        // Update basic professor fields
        $professor->professor_first_name = $validatedData['first_name'];
        $professor->professor_last_name = $validatedData['last_name'];
        $professor->professor_name = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        // Email is kept readonly for security, but validate just in case
        
        // Prepare dynamic data
        $dynamicData = $professor->dynamic_data ?: [];
        
        // Add dynamic form fields only
        foreach ($dynamicFields as $field) {
            if (isset($validatedData[$field->field_name])) {
                $dynamicData[$field->field_name] = $validatedData[$field->field_name];
            }
        }
        
        $professor->dynamic_data = $dynamicData;
        $professor->save();
        
        // Update session data
        session([
            'professor_name' => $professor->professor_name,
            'professor_email' => $professor->professor_email,
        ]);
        
        return redirect()->route('professor.profile')->with('success', 'Profile updated successfully!');
    }

    public function updateProfilePhoto(Request $request)
    {
        $professor = Professor::find(session('professor_id'));
        
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        // Delete old photo if exists
        if ($professor->profile_photo && Storage::disk('public')->exists($professor->profile_photo)) {
            Storage::disk('public')->delete($professor->profile_photo);
        }

        // Store new photo
        $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        
        $professor->profile_photo = $photoPath;
        $professor->save();

        return redirect()->route('professor.profile')->with('success', 'Profile photo updated successfully!');
    }

    public function removeProfilePhoto()
    {
        $professor = Professor::find(session('professor_id'));
        
        if ($professor->profile_photo && Storage::disk('public')->exists($professor->profile_photo)) {
            Storage::disk('public')->delete($professor->profile_photo);
        }
        
        $professor->profile_photo = null;
        $professor->save();

        return response()->json(['success' => true, 'message' => 'Profile photo removed successfully!']);
    }

    public function studentList()
    {
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        // Get all students enrolled in professor's programs
        $students = Student::whereHas('enrollments', function ($query) use ($assignedPrograms) {
            $query->whereIn('program_id', $assignedPrograms->pluck('program_id'));
        })->with(['enrollments.program', 'enrollments.package'])->get();
        
        return view('professor.students', compact('professor', 'students', 'assignedPrograms'));
    }

    public function gradeStudent(Request $request, $studentId)
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $professor = Professor::find(session('professor_id'));
        $student = Student::findOrFail($studentId);
        
        // Check if professor is assigned to any of the student's programs
        $studentPrograms = $student->enrollments()->pluck('program_id');
        $professorPrograms = $professor->programs()->pluck('program_id');
        
        if ($studentPrograms->intersect($professorPrograms)->isEmpty()) {
            return redirect()->back()->with('error', 'You are not authorized to grade this student.');
        }
        
        // Here you can implement grading logic
        // For now, we'll just return success
        return redirect()->back()->with('success', 'Grade assigned successfully!');
    }

    public function calendar()
    {
        $professor = Professor::find(session('professor_id'));
        
        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }

        // Get professor's upcoming meetings
        $upcomingMeetings = $professor->upcomingMeetings()->with('batch.program')->get();
        
        // Get today's meetings
        $todaysMeetings = $professor->todaysMeetings()->with('batch.program')->get();
        
        // Get all meetings for calendar display (next 3 months)
        $allMeetings = $professor->classMeetings()
            ->with('batch.program')
            ->where('meeting_date', '>=', now())
            ->where('meeting_date', '<=', now()->addMonths(3))
            ->orderBy('meeting_date', 'asc')
            ->get();

        return view('professor.calendar', compact('professor', 'upcomingMeetings', 'todaysMeetings', 'allMeetings'));
    }

    public function studentBatches()
    {
        $professor = Professor::find(session('professor_id'));

        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }

        // Get batches assigned to this professor
        $batches = $professor->batches()->with(['students', 'program'])->get();

        return view('professor.students.batches', compact('professor', 'batches'));
    }

    /**
     * Get targeted announcements for professors
     */
    private function getTargetedAnnouncementsForProfessor($programIds)
    {
        $query = Announcement::where('is_active', true)
            ->where('is_published', true)
            ->where(function($q) {
                $q->whereNull('publish_date')
                  ->orWhere('publish_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expire_date')
                  ->orWhere('expire_date', '>', now());
            });

        $query->where(function($mainQuery) use ($programIds) {
            // Include announcements for all users
            $mainQuery->where('target_scope', 'all');

            // Include specific targeting announcements
            $mainQuery->orWhere(function($specificQuery) use ($programIds) {
                $specificQuery->where('target_scope', 'specific');

                $specificQuery->where(function($targetQuery) use ($programIds) {
                    // Check if professor is in target users OR target_users is null
                    $targetQuery->where(function($userQuery) {
                        $userQuery->whereJsonContains('target_users', 'professors')
                                 ->orWhereNull('target_users');
                    });

                    // AND check if professor's programs are targeted OR target_programs is null
                    $targetQuery->where(function($programQuery) use ($programIds) {
                        $programQuery->whereNull('target_programs');
                        
                        if (!empty($programIds)) {
                            foreach ($programIds as $programId) {
                                $programQuery->orWhereJsonContains('target_programs', (string)$programId);
                            }
                        }
                    });
                });
            });
        });

        return $query->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
    }
}
