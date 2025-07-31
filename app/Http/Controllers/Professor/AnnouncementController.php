<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Program;
use App\Models\StudentBatch;
use App\Models\Professor;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    /**
     * Check if professor announcement management is enabled and if professor is whitelisted
     */
    private function checkAnnouncementPermission()
    {
        try {
            // Use session-based authentication instead of Auth guard
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorAnnouncementController: Not authenticated as professor via session');
                abort(403, 'You are not authenticated as a professor.');
            }

            $professorId = session('professor_id');
            Log::info('ProfessorAnnouncementController: Professor authenticated via session', ['professor_id' => $professorId]);

            // Check if feature is globally enabled
            $isEnabled = AdminSetting::getValue('professor_announcement_management_enabled', '0') === '1';
            Log::info('ProfessorAnnouncementController: Announcement management enabled check', ['enabled' => $isEnabled]);

            // Check whitelist
            $whitelist = AdminSetting::getValue('professor_announcement_management_whitelist', '');
            Log::info('ProfessorAnnouncementController: Whitelist check', ['whitelist' => $whitelist]);

            // If feature is globally enabled
            if ($isEnabled) {
                // If whitelist is empty, allow all professors
                if (empty($whitelist) || trim($whitelist) === '') {
                    Log::info('ProfessorAnnouncementController: Feature enabled, whitelist empty - allowing all professors', ['professor_id' => $professorId]);
                    return;
                }
                
                // If whitelist has IDs, check if professor is in whitelist
                $whitelistedIds = array_filter(array_map('trim', explode(',', $whitelist)), function($id) {
                    return !empty($id) && $id !== '';
                });
                
                if (!empty($whitelistedIds) && !in_array((string)$professorId, $whitelistedIds)) {
                    Log::warning('ProfessorAnnouncementController: Feature enabled but professor not in whitelist', [
                        'professor_id' => $professorId,
                        'whitelist' => $whitelistedIds
                    ]);
                    abort(403, 'You are not authorized to manage announcements.');
                }
                
                Log::info('ProfessorAnnouncementController: Feature enabled and professor in whitelist', ['professor_id' => $professorId]);
                return;
            }
            
            // If feature is globally disabled, check if professor is specifically whitelisted
            if (!empty($whitelist) && trim($whitelist) !== '') {
                $whitelistedIds = array_filter(array_map('trim', explode(',', $whitelist)), function($id) {
                    return !empty($id) && $id !== '';
                });
                
                if (!empty($whitelistedIds) && in_array((string)$professorId, $whitelistedIds)) {
                    Log::info('ProfessorAnnouncementController: Feature disabled but professor whitelisted - allowing access', ['professor_id' => $professorId]);
                    return;
                }
            }
            
            // Feature is disabled and professor is not whitelisted
            Log::warning('ProfessorAnnouncementController: Announcement management not enabled and professor not whitelisted');
            abort(403, 'Announcement management is not enabled for professors.');

        } catch (\Exception $e) {
            Log::error('ProfessorAnnouncementController checkAnnouncementPermission error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check if professor can create/edit announcements (stricter check)
     */
    private function checkAnnouncementCreationPermission()
    {
        $this->checkAnnouncementPermission(); // This includes the whitelist check
    }

    /**
     * Check if professor can view announcements (always allow viewing)
     */
    private function checkAnnouncementViewPermission()
    {
        try {
            // Always allow viewing - no feature check needed
            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                abort(403, 'You are not authenticated as a professor.');
            }
        } catch (\Exception $e) {
            Log::error('ProfessorAnnouncementController checkAnnouncementViewPermission error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function index()
    {
        $this->checkAnnouncementViewPermission(); // Allow viewing if authenticated
        $professor = Professor::where('professor_id', session('professor_id'))->first();
        
        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }
        
        // Check if professor can create announcements using the new logic
        $canCreateAnnouncements = false;
        try {
            $this->checkAnnouncementCreationPermission();
            $canCreateAnnouncements = true;
            Log::info('AnnouncementController index: Professor can create announcements', ['professor_id' => $professor->professor_id]);
        } catch (\Exception $e) {
            // Professor can view but not create announcements
            Log::info('AnnouncementController index: Professor cannot create announcements', [
                'professor_id' => $professor->professor_id,
                'error' => $e->getMessage()
            ]);
            $canCreateAnnouncements = false;
        }
        
        // Get announcements created by this professor
        $announcements = Announcement::where('professor_id', $professor->professor_id)
            ->with(['program', 'admin', 'professor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('professor.announcements.index', compact('announcements', 'canCreateAnnouncements'));
    }

    public function create()
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for creation
        $professor = Professor::where('professor_id', session('professor_id'))->first();
        
        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }
        
        // Get only programs assigned to this professor
        $programs = $professor->assignedPrograms()->orderBy('program_name')->get();
        
        // Get batches for the assigned programs
        $programIds = $programs->pluck('program_id')->toArray();
        $batches = StudentBatch::whereIn('program_id', $programIds)
            ->with('program')
            ->orderBy('batch_name')
            ->get();
        
        return view('professor.announcements.create', compact('programs', 'batches'));
    }

    public function store(Request $request)
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for creation
        $professor = Professor::where('professor_id', session('professor_id'))->first();
        
        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:general,urgent,event,system',
            'target_scope' => 'required|in:all,specific',
            'target_users' => 'nullable|array',
            'target_users.*' => 'in:students', // Only students allowed for professors
            'target_programs' => 'nullable|array',
            'target_programs.*' => 'integer',
            'target_batches' => 'nullable|array',
            'target_batches.*' => 'integer',
            'target_plans' => 'nullable|array',
            'target_plans.*' => 'in:full,modular',
            'publish_date' => 'nullable|date|after_or_equal:today',
            'expire_date' => 'nullable|date|after:publish_date',
            'video_link' => 'nullable|url'
        ]);

        // Validate that selected programs are assigned to this professor
        if ($request->target_programs) {
            $assignedProgramIds = $professor->assignedPrograms()->pluck('program_id')->toArray();
            $invalidPrograms = array_diff($request->target_programs, $assignedProgramIds);
            
            if (!empty($invalidPrograms)) {
                return back()->withErrors(['target_programs' => 'You can only target programs assigned to you.']);
            }
        }

        // Validate that selected batches belong to assigned programs
        if ($request->target_batches) {
            $assignedProgramIds = $professor->assignedPrograms()->pluck('program_id')->toArray();
            $validBatches = StudentBatch::whereIn('program_id', $assignedProgramIds)
                ->pluck('batch_id')
                ->toArray();
            $invalidBatches = array_diff($request->target_batches, $validBatches);
            
            if (!empty($invalidBatches)) {
                return back()->withErrors(['target_batches' => 'You can only target batches from your assigned programs.']);
            }
        }

        $announcement = new Announcement();
        $announcement->professor_id = $professor->professor_id;
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->description = $request->description;
        $announcement->type = $request->type;
        $announcement->target_scope = $request->target_scope;
        $announcement->video_link = $request->video_link;
        
        // Handle targeting options
        if ($request->target_scope === 'specific') {
            Log::info('Professor creating announcement with specific targeting', [
                'professor_id' => $professor->professor_id,
                'target_users' => $request->target_users,
                'target_programs' => $request->target_programs,
                'target_batches' => $request->target_batches,
                'target_plans' => $request->target_plans
            ]);
            
            $announcement->target_users = $request->target_users ?: null;
            $announcement->target_programs = $request->target_programs ?: null;
            $announcement->target_batches = $request->target_batches ?: null;
            $announcement->target_plans = $request->target_plans ?: null;
        } else {
            Log::info('Professor creating announcement with all users targeting', [
                'professor_id' => $professor->professor_id
            ]);
        }
        
        // Handle dates
        $announcement->publish_date = $request->publish_date ? Carbon::parse($request->publish_date) : now();
        $announcement->expire_date = $request->expire_date ? Carbon::parse($request->expire_date) : null;
        
        // Set program_id for compatibility (use first program if specific targeting)
        if ($request->target_programs && count($request->target_programs) > 0) {
            $announcement->program_id = $request->target_programs[0];
        }
        
        $announcement->save();

        return redirect()->route('professor.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    public function show(Announcement $announcement)
    {
        $this->checkAnnouncementViewPermission(); // Use lenient check for viewing
        $professor = Professor::where('professor_id', session('professor_id'))->first();
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }
        
        return view('professor.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for editing
        $professor = Professor::where('professor_id', session('professor_id'))->first();
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }
        
        // Get only programs assigned to this professor
        $programs = $professor->assignedPrograms()->orderBy('program_name')->get();
        
        // Get batches for the assigned programs
        $programIds = $programs->pluck('program_id')->toArray();
        $batches = StudentBatch::whereIn('program_id', $programIds)
            ->with('program')
            ->orderBy('batch_name')
            ->get();
        
        return view('professor.announcements.edit', compact('announcement', 'programs', 'batches'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for updating
        $professor = Professor::where('professor_id', session('professor_id'))->first();
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:general,urgent,event,system',
            'target_scope' => 'required|in:all,specific',
            'target_users' => 'nullable|array',
            'target_users.*' => 'in:students', // Only students allowed for professors
            'target_programs' => 'nullable|array',
            'target_programs.*' => 'integer',
            'target_batches' => 'nullable|array',
            'target_batches.*' => 'integer',
            'target_plans' => 'nullable|array',
            'target_plans.*' => 'in:full,modular',
            'publish_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after:publish_date',
            'video_link' => 'nullable|url'
        ]);

        // Validate that selected programs are assigned to this professor
        if ($request->target_programs) {
            $assignedProgramIds = $professor->assignedPrograms()->pluck('program_id')->toArray();
            $invalidPrograms = array_diff($request->target_programs, $assignedProgramIds);
            
            if (!empty($invalidPrograms)) {
                return back()->withErrors(['target_programs' => 'You can only target programs assigned to you.']);
            }
        }

        // Validate that selected batches belong to assigned programs
        if ($request->target_batches) {
            $assignedProgramIds = $professor->assignedPrograms()->pluck('program_id')->toArray();
            $validBatches = StudentBatch::whereIn('program_id', $assignedProgramIds)
                ->pluck('batch_id')
                ->toArray();
            $invalidBatches = array_diff($request->target_batches, $validBatches);
            
            if (!empty($invalidBatches)) {
                return back()->withErrors(['target_batches' => 'You can only target batches from your assigned programs.']);
            }
        }

        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->description = $request->description;
        $announcement->type = $request->type;
        $announcement->target_scope = $request->target_scope;
        $announcement->video_link = $request->video_link;
        
        // Handle targeting options
        if ($request->target_scope === 'specific') {
            $announcement->target_users = $request->target_users ?: null;
            $announcement->target_programs = $request->target_programs ?: null;
            $announcement->target_batches = $request->target_batches ?: null;
            $announcement->target_plans = $request->target_plans ?: null;
        } else {
            $announcement->target_users = null;
            $announcement->target_programs = null;
            $announcement->target_batches = null;
            $announcement->target_plans = null;
        }
        
        // Handle dates
        $announcement->publish_date = $request->publish_date ? Carbon::parse($request->publish_date) : $announcement->publish_date;
        $announcement->expire_date = $request->expire_date ? Carbon::parse($request->expire_date) : null;
        
        // Set program_id for compatibility (use first program if specific targeting)
        if ($request->target_programs && count($request->target_programs) > 0) {
            $announcement->program_id = $request->target_programs[0];
        }
        
        $announcement->save();

        return redirect()->route('professor.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for deletion
        $professor = Professor::where('professor_id', session('professor_id'))->first();
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }
        
        $announcement->delete();

        return redirect()->route('professor.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }
}
