<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Services\TenantService;

class TenantAdminProgramController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of active programs for the tenant.
     */
    public function index($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get programs from tenant database with enrollment counts
            $programs = DB::table('programs')
                ->leftJoin('enrollments', 'programs.program_id', '=', 'enrollments.program_id')
                ->where('programs.is_archived', false)
                ->select('programs.*', DB::raw('COUNT(enrollments.enrollment_id) as enrollment_count'))
                ->groupBy('programs.program_id', 'programs.program_name', 'programs.program_description', 'programs.created_by_admin_id', 'programs.director_id', 'programs.created_at', 'programs.updated_at', 'programs.is_active', 'programs.is_archived', 'programs.program_image', 'programs.admin_override')
                ->orderBy('programs.created_at', 'desc')
                ->get();

            // Calculate statistics
            $totalPrograms = $programs->count();
            $totalEnrollments = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->count();

            $archivedPrograms = DB::table('programs')
                ->where('is_archived', true)
                ->count();

            // Calculate additional statistics
            $newProgramsThisMonth = DB::table('programs')
                ->where('is_archived', false)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $newEnrollmentsThisWeek = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->whereBetween('enrollments.created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count();

            $avgEnrollmentPerProgram = $totalPrograms > 0 ? round($totalEnrollments / $totalPrograms, 1) : 0;

            // Get most popular program
            $mostPopularProgram = DB::table('programs')
                ->leftJoin('enrollments', 'programs.program_id', '=', 'enrollments.program_id')
                ->where('programs.is_archived', false)
                ->select('programs.*', DB::raw('COUNT(enrollments.enrollment_id) as enrollment_count'))
                ->groupBy('programs.program_id')
                ->orderBy('enrollment_count', 'desc')
                ->first();

            // Get recent programs
            $recentProgramsCount = DB::table('programs')
                ->where('is_archived', false)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            // Mock data for charts (in real implementation, you'd calculate this from actual data)
            $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            $enrollmentData = [0, 0, 0, 0, 0, 0];
            $completionData = [0, 0, 0, 0, 0, 0];

            // Mock recent activities
            $recentActivities = [];

            // Get students for potential assignments
            $students = DB::table('students')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-programs.admin-programs', [
                'programs' => $programs,
                'totalPrograms' => $totalPrograms,
                'totalEnrollments' => $totalEnrollments,
                'archivedPrograms' => $archivedPrograms,
                'newProgramsThisMonth' => $newProgramsThisMonth,
                'newEnrollmentsThisWeek' => $newEnrollmentsThisWeek,
                'avgEnrollmentPerProgram' => $avgEnrollmentPerProgram,
                'completionRate' => 0, // Mock data
                'mostPopularProgram' => $mostPopularProgram,
                'recentProgramsCount' => $recentProgramsCount,
                'avgProgramRating' => 0, // Mock data
                'chartLabels' => $chartLabels,
                'enrollmentData' => $enrollmentData,
                'completionData' => $completionData,
                'recentActivities' => $recentActivities,
                'students' => $students,
                'tenant' => $tenantModel
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant programs index error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return view('admin.admin-programs.admin-programs', [
                'programs' => collect(),
                'totalPrograms' => 0,
                'totalEnrollments' => 0,
                'archivedPrograms' => 0,
                'newProgramsThisMonth' => 0,
                'newEnrollmentsThisWeek' => 0,
                'avgEnrollmentPerProgram' => 0,
                'completionRate' => 0,
                'mostPopularProgram' => null,
                'recentProgramsCount' => 0,
                'avgProgramRating' => 0,
                'chartLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'enrollmentData' => [0, 0, 0, 0, 0, 0],
                'completionData' => [0, 0, 0, 0, 0, 0],
                'recentActivities' => [],
                'students' => collect(),
                'tenant' => null
            ]);
        }
    }

    /**
     * Store a newly created program in tenant database.
     */
    public function store(Request $request, $tenant)
    {
        try {
            $request->validate([
                'program_name' => 'required|string|max:100',
                'program_description' => 'nullable|string|max:1000',
                'program_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $data = [
                'program_name' => $request->program_name,
                'program_description' => $request->program_description,
                'created_by_admin_id' => Auth::user()->admin_id ?? 1,
                'is_archived' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Handle image upload
            if ($request->hasFile('program_image')) {
                $image = $request->file('program_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                // Store the image in storage/app/public/program-images
                $image->storeAs('public/program-images', $imageName);
                
                $data['program_image'] = $imageName;
            }

            // Insert into tenant database
            DB::table('programs')->insert($data);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()
                ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                ->with('success', 'Program added successfully to tenant database!');

        } catch (\Exception $e) {
            Log::error('Tenant program creation error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()
                ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                ->with('error', 'Failed to create program: ' . $e->getMessage());
        }
    }

    /**
     * Store multiple programs in batch.
     */
    public function batchStore(Request $request, $tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Check if this is a CSV file upload or direct array data
            if ($request->hasFile('csv_file')) {
                // Handle CSV file upload
                $request->validate([
                    'csv_file' => 'required|file|mimes:csv,txt|max:2048',
                ]);

                $file = $request->file('csv_file');
                $programs = [];
                
                // Read CSV file
                if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
                    // Skip header row if it exists
                    $firstRow = fgetcsv($handle, 1000, ",");
                    
                    // Check if first row looks like a header
                    $isHeader = (stripos($firstRow[0] ?? '', 'name') !== false || 
                               stripos($firstRow[0] ?? '', 'program') !== false);
                    
                    if (!$isHeader) {
                        // First row is data, process it
                        if (!empty($firstRow[0])) {
                            $programs[] = [
                                'program_name' => trim($firstRow[0]),
                                'program_description' => !empty($firstRow[1]) ? trim($firstRow[1]) : null,
                            ];
                        }
                    }
                    
                    // Process remaining rows
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if (!empty($data[0])) {
                            $programs[] = [
                                'program_name' => trim($data[0]),
                                'program_description' => !empty($data[1]) ? trim($data[1]) : null,
                            ];
                        }
                    }
                    fclose($handle);
                }

                if (empty($programs)) {
                    $this->tenantService->switchToMain();
                    return redirect()
                        ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                        ->with('error', 'No valid program data found in the CSV file.');
                }

            } else {
                // Handle direct array data
                $request->validate([
                    'programs' => 'required|array|min:1',
                    'programs.*.program_name' => 'required|string|max:100',
                    'programs.*.program_description' => 'nullable|string|max:500',
                ]);
                
                $programs = $request->programs;
            }

            // Validate program data
            foreach ($programs as $index => $programData) {
                if (empty($programData['program_name']) || strlen($programData['program_name']) > 100) {
                    $this->tenantService->switchToMain();
                    return redirect()
                        ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                        ->with('error', "Invalid program name at row " . ($index + 1));
                }
                
                if (!empty($programData['program_description']) && strlen($programData['program_description']) > 500) {
                    $this->tenantService->switchToMain();
                    return redirect()
                        ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                        ->with('error', "Program description too long at row " . ($index + 1));
                }
            }

            $createdCount = 0;
            $adminId = Auth::user()->admin_id ?? 1;

            foreach ($programs as $programData) {
                DB::table('programs')->insert([
                    'program_name' => $programData['program_name'],
                    'program_description' => $programData['program_description'] ?? null,
                    'created_by_admin_id' => $adminId,
                    'is_archived' => false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $createdCount++;
            }

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()
                ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                ->with('success', "{$createdCount} programs created successfully in tenant database!");

        } catch (\Exception $e) {
            Log::error('Tenant batch program creation error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()
                ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                ->with('error', 'Batch creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete a program from tenant database.
     */
    public function destroy($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $program = DB::table('programs')->where('program_id', $id)->first();

            if (!$program) {
                $this->tenantService->switchToMain();
                return redirect()
                    ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                    ->with('error', 'Program not found.');
            }

            // Check if program has enrollments
            $enrollmentCount = DB::table('enrollments')
                ->where('program_id', $id)
                ->count();

            if ($enrollmentCount > 0) {
                $this->tenantService->switchToMain();
                return redirect()
                    ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                    ->with('error', 'Cannot delete program with active enrollments.');
            }

            // Delete the program
            DB::table('programs')->where('program_id', $id)->delete();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()
                ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                ->with('success', 'Program deleted successfully from tenant database!');

        } catch (\Exception $e) {
            Log::error('Tenant program deletion error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()
                ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                ->with('error', 'Failed to delete program: ' . $e->getMessage());
        }
    }

    /**
     * Toggle archive status of a program.
     */
    public function toggleArchive($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $program = DB::table('programs')->where('program_id', $id)->first();

            if (!$program) {
                $this->tenantService->switchToMain();
                return redirect()
                    ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                    ->with('error', 'Program not found.');
            }

            // Toggle archive status
            $newStatus = !$program->is_archived;
            DB::table('programs')
                ->where('program_id', $id)
                ->update([
                    'is_archived' => $newStatus,
                    'updated_at' => now()
                ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            $status = $newStatus ? 'archived' : 'unarchived';
            return redirect()
                ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                ->with('success', "Program {$status} successfully in tenant database!");

        } catch (\Exception $e) {
            Log::error('Tenant program archive toggle error: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()
                ->route('tenant.admin.programs.index', ['tenant' => $tenant])
                ->with('error', 'Failed to toggle archive status: ' . $e->getMessage());
        }
    }
}
