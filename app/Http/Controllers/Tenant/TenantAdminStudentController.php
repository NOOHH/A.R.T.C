<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Services\TenantService;

class TenantAdminStudentController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of students for the tenant.
     */
    public function index($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get students with enrollment information
            $students = DB::table('students')
                ->leftJoin('enrollments', 'students.student_id', '=', 'enrollments.student_id')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->select(
                    'students.*',
                    DB::raw('COUNT(enrollments.enrollment_id) as enrollment_count'),
                    DB::raw('GROUP_CONCAT(DISTINCT programs.program_name) as enrolled_programs')
                )
                ->groupBy('students.student_id', 'students.first_name', 'students.last_name', 'students.email', 'students.phone', 'students.created_at', 'students.updated_at')
                ->orderBy('students.created_at', 'desc')
                ->get();

            // Calculate statistics
            $totalStudents = $students->count();
            $activeStudents = $students->where('enrollment_count', '>', 0)->count();
            $newStudentsThisMonth = $students->where('created_at', '>=', now()->startOfMonth())->count();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-list-of-students.admin-list-of-students', compact(
                'students', 
                'totalStudents', 
                'activeStudents', 
                'newStudentsThisMonth',
                'tenantModel'
            ));

        } catch (\Exception $e) {
            Log::error('Tenant students index error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return view('admin.admin-list-of-students.admin-list-of-students', [
                'students' => collect(),
                'totalStudents' => 0,
                'activeStudents' => 0,
                'newStudentsThisMonth' => 0,
                'tenantModel' => null
            ]);
        }
    }

    /**
     * Display student enrollments for the tenant.
     */
    public function enrollments($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get enrollments with student and program information
            $enrollments = DB::table('enrollments')
                ->leftJoin('students', 'enrollments.student_id', '=', 'students.student_id')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->select(
                    'enrollments.*',
                    'students.first_name',
                    'students.last_name',
                    'students.email',
                    'programs.program_name'
                )
                ->orderBy('enrollments.created_at', 'desc')
                ->get();

            // Calculate statistics
            $totalEnrollments = $enrollments->count();
            $activeEnrollments = $enrollments->where('status', 'active')->count();
            $pendingEnrollments = $enrollments->where('status', 'pending')->count();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-student-enrollments.admin-student-enrollments', compact(
                'enrollments', 
                'totalEnrollments', 
                'activeEnrollments', 
                'pendingEnrollments',
                'tenantModel'
            ));

        } catch (\Exception $e) {
            Log::error('Tenant enrollments index error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return view('admin.admin-student-enrollments.admin-student-enrollments', [
                'enrollments' => collect(),
                'totalEnrollments' => 0,
                'activeEnrollments' => 0,
                'pendingEnrollments' => 0,
                'tenantModel' => null
            ]);
        }
    }

    /**
     * Display student registrations for the tenant.
     */
    public function registrations($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get registrations with student and package information
            $registrations = DB::table('registrations')
                ->leftJoin('students', 'registrations.student_id', '=', 'students.student_id')
                ->leftJoin('packages', 'registrations.package_id', '=', 'packages.package_id')
                ->select(
                    'registrations.*',
                    'students.first_name',
                    'students.last_name',
                    'students.email',
                    'packages.package_name'
                )
                ->orderBy('registrations.created_at', 'desc')
                ->get();

            // Calculate statistics
            $totalRegistrations = $registrations->count();
            $approvedRegistrations = $registrations->where('status', 'approved')->count();
            $pendingRegistrations = $registrations->where('status', 'pending')->count();
            $rejectedRegistrations = $registrations->where('status', 'rejected')->count();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-student-registration.admin-student-registration', compact(
                'registrations', 
                'totalRegistrations', 
                'approvedRegistrations', 
                'pendingRegistrations',
                'rejectedRegistrations',
                'tenantModel'
            ));

        } catch (\Exception $e) {
            Log::error('Tenant registrations index error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return view('admin.admin-student-registration.admin-student-registration', [
                'registrations' => collect(),
                'totalRegistrations' => 0,
                'approvedRegistrations' => 0,
                'pendingRegistrations' => 0,
                'rejectedRegistrations' => 0,
                'tenantModel' => null
            ]);
        }
    }

    /**
     * Approve a registration for the tenant.
     */
    public function approveRegistration(Request $request, $tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update registration status
            DB::table('registrations')
                ->where('registration_id', $id)
                ->update([
                    'status' => 'approved',
                    'approved_by' => Auth::user()->admin_id ?? 1,
                    'approved_at' => now(),
                    'updated_at' => now(),
                ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.students.registrations', ['tenant' => $tenant])
                ->with('success', 'Registration approved successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant approve registration error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.students.registrations', ['tenant' => $tenant])
                ->with('error', 'Error approving registration.');
        }
    }

    /**
     * Reject a registration for the tenant.
     */
    public function rejectRegistration(Request $request, $tenant, $id)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update registration status
            DB::table('registrations')
                ->where('registration_id', $id)
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'rejected_by' => Auth::user()->admin_id ?? 1,
                    'rejected_at' => now(),
                    'updated_at' => now(),
                ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.students.registrations', ['tenant' => $tenant])
                ->with('success', 'Registration rejected successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant reject registration error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.students.registrations', ['tenant' => $tenant])
                ->with('error', 'Error rejecting registration.');
        }
    }

    /**
     * Show student details for the tenant.
     */
    public function show($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get student with enrollments
            $student = DB::table('students')->find($id);
            if (!$student) {
                throw new \Exception('Student not found');
            }

            $enrollments = DB::table('enrollments')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('enrollments.student_id', $id)
                ->select('enrollments.*', 'programs.program_name')
                ->get();

            $registrations = DB::table('registrations')
                ->leftJoin('packages', 'registrations.package_id', '=', 'packages.package_id')
                ->where('registrations.student_id', $id)
                ->select('registrations.*', 'packages.package_name')
                ->get();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-student-enrollment.admin-student-enrollment', compact(
                'student', 
                'enrollments', 
                'registrations',
                'tenantModel'
            ));

        } catch (\Exception $e) {
            Log::error('Tenant student show error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.students.index', ['tenant' => $tenant])
                ->with('error', 'Student not found.');
        }
    }
}
