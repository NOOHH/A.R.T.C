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
            // Check if feature is enabled
            $isEnabled = AdminSetting::getValue('professor_announcement_management_enabled', '0') === '1';
            Log::info('ProfessorAnnouncementController: Announcement management enabled check', ['enabled' => $isEnabled]);

            if (!$isEnabled) {
                Log::warning('ProfessorAnnouncementController: Announcement management not enabled');
                abort(403, 'Announcement management is not enabled for professors.');
            }

            // Check whitelist
            $whitelist = AdminSetting::getValue('professor_announcement_management_whitelist', '');
            Log::info('ProfessorAnnouncementController: Whitelist check', ['whitelist' => $whitelist]);

            // Use session-based authentication instead of Auth guard
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorAnnouncementController: Not authenticated as professor via session');
                abort(403, 'You are not authenticated as a professor.');
            }

            $professorId = session('professor_id');
            Log::info('ProfessorAnnouncementController: Professor authenticated via session', ['professor_id' => $professorId]);

            // If whitelist is not empty and has actual professor IDs, check if professor is in whitelist
            if (!empty($whitelist) && trim($whitelist) !== '') {
                $whitelistedIds = array_filter(array_map('trim', explode(',', $whitelist)), function($id) {
                    return !empty($id) && $id !== '';
                });
                
                Log::info('ProfessorAnnouncementController: Parsed whitelist IDs', ['whitelisted_ids' => $whitelistedIds]);
                
                if (!empty($whitelistedIds) && !in_array((string)$professorId, $whitelistedIds)) {
                    Log::warning('ProfessorAnnouncementController: Professor not in whitelist', [
                        'professor_id' => $professorId,
                        'whitelist' => $whitelistedIds
                    ]);
                    abort(403, 'You are not authorized to manage announcements.');
                }
            }

            Log::info('ProfessorAnnouncementController: Permission check passed');
        } catch (\Exception $e) {
            Log::error('ProfessorAnnouncementController checkAnnouncementPermission error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function index()
    {
        $this->checkAnnouncementPermission();
        $professor = Professor::where('professor_id', session('user_id'))->first();
        
        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }
        
        // Get announcements created by this professor
        $announcements = Announcement::where('professor_id', $professor->professor_id)
            ->with(['program', 'admin', 'professor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('professor.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->checkAnnouncementPermission();
        $professor = Professor::where('professor_id', session('user_id'))->first();
        
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
        $this->checkAnnouncementPermission();
        $professor = Professor::where('professor_id', session('user_id'))->first();
        
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
            'video_link' => 'nullable|url',
            'is_published' => 'boolean'
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
        $announcement->is_published = filter_var($request->input('is_published', false), FILTER_VALIDATE_BOOLEAN);
        $announcement->is_active = true;
        
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
        $this->checkAnnouncementPermission();
        $professor = Professor::where('professor_id', session('user_id'))->first();
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }
        
        return view('professor.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $this->checkAnnouncementPermission();
        $professor = Professor::where('professor_id', session('user_id'))->first();
        
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
        $this->checkAnnouncementPermission();
        $professor = Professor::where('professor_id', session('user_id'))->first();
        
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
            'video_link' => 'nullable|url',
            'is_published' => 'boolean'
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
        $announcement->is_published = filter_var($request->input('is_published', false), FILTER_VALIDATE_BOOLEAN);
        
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
        $this->checkAnnouncementPermission();
        $professor = Professor::where('professor_id', session('user_id'))->first();
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }
        
        $announcement->delete();

        return redirect()->route('professor.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    public function toggleStatus(Announcement $announcement)
    {
        $this->checkAnnouncementPermission();
        $professor = Professor::where('professor_id', session('user_id'))->first();
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.']);
        }
        
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement status updated successfully!',
            'is_active' => $announcement->is_active
        ]);
    }

    public function togglePublished(Announcement $announcement)
    {
        $this->checkAnnouncementPermission();
        $professor = Professor::where('professor_id', session('user_id'))->first();
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.']);
        }
        
        $announcement->is_published = !$announcement->is_published;
        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement publication status updated successfully!',
            'is_published' => $announcement->is_published
        ]);
    }
}
