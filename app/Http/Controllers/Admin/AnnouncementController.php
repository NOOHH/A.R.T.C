<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Program;
use App\Models\StudentBatch;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Director;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['program', 'admin', 'professor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        // Get all programs and batches for targeting, regardless of status
        $programs = Program::orderBy('program_name')->get();
        $batches = StudentBatch::orderBy('batch_name')->get();
        
        return view('admin.announcements.create', compact('programs', 'batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:general,urgent,event,system',
            'target_scope' => 'required|in:all,specific',
            'target_users' => 'nullable|array',
            'target_users.*' => 'in:students,professors,directors',
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

        $announcement = new Announcement();
        $announcement->admin_id = session('user_id'); // Get admin ID from session
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
            Log::info('Creating announcement with specific targeting', [
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
            Log::info('Creating announcement with all users targeting');
        }
        
        // Handle dates
        $announcement->publish_date = $request->publish_date ? Carbon::parse($request->publish_date) : now();
        $announcement->expire_date = $request->expire_date ? Carbon::parse($request->expire_date) : null;
        
        // Set program_id for compatibility (use first program if specific targeting)
        if ($request->target_programs && count($request->target_programs) > 0) {
            $announcement->program_id = $request->target_programs[0];
        }
        
        $announcement->save();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    public function show($id)
    {
        $announcement = Announcement::with(['program'])->findOrFail($id);
        
        // Get target audience stats
        $stats = $this->getAnnouncementStats($announcement);
        
        return view('admin.announcements.show', compact('announcement', 'stats'));
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        // Get all programs and batches for targeting, regardless of status
        $programs = Program::orderBy('program_name')->get();
        $batches = StudentBatch::orderBy('batch_name')->get();
        
        return view('admin.announcements.edit', compact('announcement', 'programs', 'batches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:general,urgent,event,system',
            'target_scope' => 'required|in:all,specific',
            'target_users' => 'nullable|array',
            'target_users.*' => 'in:students,professors,directors',
            'target_programs' => 'nullable|array',
            'target_programs.*' => 'exists:programs,program_id',
            'target_batches' => 'nullable|array',
            'target_batches.*' => 'exists:student_batches,batch_id',
            'target_plans' => 'nullable|array',
            'target_plans.*' => 'in:full,modular',
            'publish_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after:publish_date',
            'video_link' => 'nullable|url',
            'is_published' => 'boolean'
        ]);

        $announcement = Announcement::findOrFail($id);
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
        
        // Update program_id for compatibility
        if ($request->target_programs && count($request->target_programs) > 0) {
            $announcement->program_id = $request->target_programs[0];
        }
        
        $announcement->save();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement status updated successfully!',
            'is_active' => $announcement->is_active
        ]);
    }

    public function togglePublished($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->is_published = !$announcement->is_published;
        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement publish status updated successfully!',
            'is_published' => $announcement->is_published
        ]);
    }

    private function getAnnouncementStats($announcement)
    {
        $stats = [
            'target_students' => 0,
            'target_professors' => 0,
            'target_directors' => 0,
            'target_programs' => [],
            'target_batches' => []
        ];

        if ($announcement->target_scope === 'all') {
            $stats['target_students'] = Student::count();
            $stats['target_professors'] = Professor::count();
            $stats['target_directors'] = Director::count();
            $stats['target_programs'] = Program::where('is_active', true)->pluck('program_name')->toArray();
        } else {
            $targetUsers = $announcement->target_users ? json_decode($announcement->target_users, true) : [];
            $targetPrograms = $announcement->target_programs ? json_decode($announcement->target_programs, true) : [];
            $targetBatches = $announcement->target_batches ? json_decode($announcement->target_batches, true) : [];

            if (in_array('students', $targetUsers)) {
                if (!empty($targetPrograms)) {
                    $stats['target_students'] = Student::whereHas('enrollments', function($q) use ($targetPrograms) {
                        $q->whereIn('program_id', $targetPrograms);
                    })->count();
                } else {
                    $stats['target_students'] = Student::count();
                }
            }

            if (in_array('professors', $targetUsers)) {
                $stats['target_professors'] = Professor::count();
            }

            if (in_array('directors', $targetUsers)) {
                $stats['target_directors'] = Director::count();
            }

            if (!empty($targetPrograms)) {
                $stats['target_programs'] = Program::whereIn('program_id', $targetPrograms)->pluck('program_name')->toArray();
            }

            if (!empty($targetBatches)) {
                $stats['target_batches'] = StudentBatch::whereIn('batch_id', $targetBatches)->pluck('batch_name')->toArray();
            }
        }

        return $stats;
    }
}
