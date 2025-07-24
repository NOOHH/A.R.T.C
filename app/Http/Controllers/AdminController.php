<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Registration;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Package;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Module;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\AssignmentSubmission;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {
            // Get pending registrations
            $registrations = Registration::where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->get();

            // Calculate analytics data
            $analytics = [
                'total_students' => Student::count(),
                'total_programs' => Program::where('is_archived', false)->count(),
                'total_modules' => Module::where('is_archived', false)->count(),
                'total_enrollments' => Enrollment::count(),
                'pending_registrations' => Registration::where('status', 'pending')->count(),
                'new_students_this_month' => Student::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
                'modules_this_week' => Module::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
                'archived_programs' => Program::where('is_archived', true)->count(),
            ];

            $dbError = null;
        } catch (\Exception $e) {
            $registrations = collect();
            $analytics = [
                'total_students' => 0,
                'total_programs' => 0,
                'total_modules' => 0,
                'total_enrollments' => 0,
                'pending_registrations' => 0,
                'new_students_this_month' => 0,
                'modules_this_week' => 0,
                'archived_programs' => 0,
            ];
            $dbError = 'Database connection failed: ' . $e->getMessage();
        }

        return view('admin.admin-dashboard', compact('registrations', 'analytics', 'dbError'));
    }

    public function showRegistration($id)
    {
        try {
            $registration = Registration::where('registration_id', $id)->firstOrFail();
            $user = User::find($registration->user_id);

            $data = $registration->toArray();
            $data['email'] = $user ? $user->email : 'N/A';

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration not found or database error.'], 404);
        }
    }

    public function showRegistrationDetails($id)
    {
        $registration = Registration::findOrFail($id);
        // Redirect to main registration page with details modal or use existing view
        return redirect()->route('admin.student.registration.pending')->with('selected_registration', $registration);
    }

    public function getRegistrationDetailsJson($id)
    {
        try {
            // Check if the id is a registration_id (like "2025-07-00005") or a database id (numeric)
            if (is_numeric($id)) {
                $registration = Registration::with(['user', 'program', 'package', 'plan'])->findOrFail($id);
            } else {
                // Try to find by registration_id
                $registration = Registration::with(['user', 'program', 'package', 'plan'])
                    ->where('registration_id', $id)
                    ->firstOrFail();
            }
            
            // Parse course selections if modular
            $courseInfo = 'Full';
            if ($registration->enrollment_type === 'Modular' && $registration->selected_courses) {
                $selectedCourses = is_string($registration->selected_courses) 
                    ? json_decode($registration->selected_courses, true) 
                    : $registration->selected_courses;
                
                if (is_array($selectedCourses) && count($selectedCourses) > 0) {
                    // Get course names
                    $courseNames = [];
                    foreach ($selectedCourses as $courseData) {
                        if (is_array($courseData)) {
                            // Handle module with courses structure
                            if (isset($courseData['selected_courses']) && is_array($courseData['selected_courses'])) {
                                foreach ($courseData['selected_courses'] as $courseId) {
                                    $course = \App\Models\Course::find($courseId);
                                    if ($course) {
                                        $courseNames[] = $course->subject_name;
                                    }
                                }
                            }
                        } else {
                            // Handle direct course ID
                            $course = \App\Models\Course::find($courseData);
                            if ($course) {
                                $courseNames[] = $course->subject_name;
                            }
                        }
                    }
                    $courseInfo = count($courseNames) > 0 ? implode(', ', $courseNames) : 'Modular (courses not specified)';
                } else {
                    $courseInfo = 'Modular';
                }
            }
            
            return response()->json([
                'registration_id' => $registration->registration_id,
                'firstname' => $registration->firstname,
                'middlename' => $registration->middlename,
                'lastname' => $registration->lastname,
                'email' => $registration->user->email ?? $registration->email ?? 'N/A',
                'mobile_number' => $registration->contact_number ?? $registration->phone_number ?? 'N/A',
                'gender' => $registration->gender,
                'birthdate' => $registration->birthdate,
                'age' => $registration->birthdate ? now()->diffInYears($registration->birthdate) : 'N/A',
                'address' => $registration->address ?? $registration->street_address ?? 'N/A',
                'city' => $registration->city,
                'state_province' => $registration->state_province,
                'zipcode' => $registration->zipcode,
                'program_name' => $registration->program_name ?? ($registration->program ? $registration->program->program_name : 'N/A'),
                'package_name' => $registration->package_name ?? ($registration->package ? $registration->package->package_name : 'N/A'),
                'plan_name' => $registration->plan_name ?? ($registration->plan ? $registration->plan->plan_name : 'N/A'),
                'plan_type' => $registration->enrollment_type ?? 'Full',
                'course_info' => $courseInfo,
                'learning_mode' => $registration->learning_mode,
                'education_level' => $registration->education_level,
                'Start_Date' => $registration->Start_Date,
                'status' => $registration->status,
                'PSA' => $registration->PSA,
                'TOR' => $registration->TOR,
                'Course_Cert' => $registration->Course_Cert,
                'good_moral' => $registration->good_moral,
                'photo_2x2' => $registration->photo_2x2,
                'birth_certificate' => $registration->birth_certificate,
                'diploma_certificate' => $registration->diploma_certificate,
                'Cert_of_Grad' => $registration->Cert_of_Grad,
                'valid_id' => $registration->valid_id,
                'created_at' => $registration->created_at->format('M d, Y H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration not found'], 404);
        }
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $registration = Registration::findOrFail($id);

            // Upgrade user role
            $user = User::find($registration->user_id);
            if ($user) {
                $user->role = 'student';
                $user->save();
            }

            // Check if student already exists for this user (multiple enrollment scenario)
            $student = Student::where('user_id', $user->user_id)->first();
            
            if (!$student) {
                // Generate unique, non-duplicating student_id
                $now       = now();
                $yearMonth = $now->format('Y-m');

                $lastStudent = Student::where('student_id', 'like', "{$yearMonth}-%")
                                      ->orderBy('student_id', 'desc')
                                      ->first();

                $nextSeq = $lastStudent
                    ? ((int) substr($lastStudent->student_id, strlen($yearMonth) + 1)) + 1
                    : 1;

                $studentId = $yearMonth . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);

                $student = Student::create([
                    'student_id' => $studentId,
                    'user_id' => $user->user_id,
                    'firstname' => $registration->firstname,
                    'middlename' => $registration->middlename,
                    'lastname' => $registration->lastname,
                    'email' => $user->email ?? '',
                    'package_id' => $registration->package_id,
                    'package_name' => $registration->package_name,
                    'program_id' => $registration->program_id,
                    'program_name' => $registration->program_name,
                    'enrollment_type' => $registration->enrollment_type,
                    'learning_mode' => $registration->learning_mode,
                    'education_level' => $registration->education_level,
                    'status' => 'approved',
                    'date_approved' => now()
                ]);
            }

            // Find existing enrollment for this registration (created during registration process)
            $enrollment = Enrollment::where('registration_id', $registration->registration_id)->first();
            $batchId = session('selected_batch_id');
            if ($enrollment) {
                $enrollment->student_id = $student->student_id;
                $enrollment->user_id = $user?->user_id;
                $enrollment->enrollment_status = 'approved';
                if ($batchId) {
                    $enrollment->batch_id = $batchId;
                    Log::info('Setting batch_id on existing enrollment', ['batch_id' => $batchId, 'enrollment_id' => $enrollment->enrollment_id]);
                }
                $enrollment->save();
            } else {
                $enrollmentData = [
                    'student_id' => $student->student_id,
                    'user_id' => $user?->user_id,
                    'program_id' => $registration->program_id,
                    'package_id' => $registration->package_id,
                    'enrollment_type' => $registration->plan_name === 'Modular' ? 'Modular' : 'Full',
                    'learning_mode' => $registration->learning_mode ?? 'Synchronous',
                    'enrollment_status' => 'approved',
                    'payment_status' => 'pending',
                ];
                if ($batchId) {
                    $enrollmentData['batch_id'] = $batchId;
                    Log::info('Creating new enrollment with batch_id', ['batch_id' => $batchId, 'student_id' => $student->student_id]);
                }
                Enrollment::create($enrollmentData);
            }
            if ($batchId) {
                session()->forget('selected_batch_id');
                Log::info('Cleared batch_id from session after enrollment creation');
            }

            // Update registration status instead of deleting
            $registration->status = 'approved';
            $registration->approved_at = now();
            $registration->approved_by = auth()->guard('admin')->user()->admin_id ?? null;
            $registration->save();

            DB::commit();

            return redirect()
                ->route('admin.student.registration.history')
                ->with('success', "Student \"" . $student->student_id . "\" approved and moved to history.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            $registration->delete();

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration rejected and removed.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function rejectWithReason(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);

            $registration = Registration::findOrFail($id);
            
            // Store the rejection reason
            $registration->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason
            ]);

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration rejected: ' . $request->reason);
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function rejectWithFields(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
                'rejected_fields' => 'array'
            ]);

            $registration = Registration::findOrFail($id);
            
            // Store the current submission as original before updating
            $originalSubmission = $registration->toArray();
            
            // Store the rejection reason and fields
            $registration->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason,
                'rejected_fields' => json_encode($request->rejected_fields ?? []),
                'rejected_by' => auth()->guard('admin')->user()->admin_id,
                'rejected_at' => now(),
                'original_submission' => json_encode($originalSubmission)
            ]);

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration rejected with marked fields.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function approveResubmission(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            if ($registration->status !== 'resubmitted') {
                return redirect()->back()->with('error', 'Registration is not in resubmitted status.');
            }

            // Update to approved and clear rejection data
            $registration->update([
                'status' => 'approved',
                'rejection_reason' => null,
                'rejected_fields' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'resubmitted_at' => null
            ]);

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration resubmission approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function updateRejection(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
                'rejected_fields' => 'array'
            ]);

            $registration = Registration::findOrFail($id);
            
            $registration->update([
                'rejection_reason' => $request->reason,
                'rejected_fields' => json_encode($request->rejected_fields ?? []),
                'rejected_by' => auth()->guard('admin')->user()->admin_id,
                'rejected_at' => now()
            ]);

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Rejection details updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function getOriginalRegistrationData($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            if (!$registration->original_submission) {
                return response()->json(['error' => 'No original data found.'], 404);
            }

            $originalData = json_decode($registration->original_submission, true);
            $originalData['rejection_reason'] = $registration->rejection_reason;
            $originalData['rejected_fields'] = $registration->rejected_fields;

            return response()->json($originalData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load original data.'], 404);
        }
    }

    public function getStudentDetailsJson($id)
    {
        try {
            $student = Student::with(['user', 'enrollments.program', 'enrollments.package'])->findOrFail($id);
            
            return response()->json([
                'student_id' => $student->student_id,
                'firstname' => $student->firstname,
                'middlename' => $student->middlename,
                'lastname' => $student->lastname,
                'email' => $student->email ?? ($student->user->email ?? 'N/A'),
                'contact_number' => $student->contact_number,
                'emergency_contact_number' => $student->emergency_contact_number,
                'student_school' => $student->student_school,
                'street_address' => $student->street_address,
                'city' => $student->city,
                'state_province' => $student->state_province,
                'zipcode' => $student->zipcode,
                'good_moral' => $student->good_moral,
                'PSA' => $student->PSA,
                'Course_Cert' => $student->Course_Cert,
                'TOR' => $student->TOR,
                'Cert_of_Grad' => $student->Cert_of_Grad,
                'photo_2x2' => $student->photo_2x2,
                'Undergraduate' => $student->Undergraduate,
                'Graduate' => $student->Graduate,
                'Start_Date' => $student->Start_Date,
                'status' => $student->user->role ?? 'N/A',
                'date_approved' => $student->date_approved,
                'enrollments' => $student->enrollments->map(function ($enrollment) {
                    return [
                        'program_name' => $enrollment->program->program_name ?? 'N/A',
                        'package_name' => $enrollment->package->package_name ?? 'N/A',
                        'enrollment_type' => $enrollment->enrollment_type ?? 'N/A',
                        'enrollment_status' => $enrollment->enrollment_status ?? 'N/A',
                        'payment_status' => $enrollment->payment_status ?? 'N/A'
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Student not found or database error.'], 404);
        }
    }

    public function undoApproval(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $student = Student::findOrFail($id);
            $user = User::find($student->user_id);

            // Create a registration record from the student data
            $registration = Registration::create([
                'user_id' => $student->user_id,
                'firstname' => $student->firstname,
                'middlename' => $student->middlename,
                'lastname' => $student->lastname,
                'student_school' => $student->student_school,
                'street_address' => $student->street_address,
                'city' => $student->city,
                'state_province' => $student->state_province,
                'zipcode' => $student->zipcode,
                'contact_number' => $student->contact_number,
                'emergency_contact_number' => $student->emergency_contact_number,
                'good_moral' => $student->good_moral,
                'PSA' => $student->PSA,
                'Course_Cert' => $student->Course_Cert,
                'TOR' => $student->TOR,
                'Cert_of_Grad' => $student->Cert_of_Grad,
                'photo_2x2' => $student->photo_2x2,
                'Undergraduate' => $student->Undergraduate,
                'Graduate' => $student->Graduate,
                'Start_Date' => $student->Start_Date,
                'status' => 'pending'
            ]);

            // Downgrade user role back to guest
            if ($user) {
                $user->role = 'guest';
                $user->save();
            }

            // Delete enrollments
            Enrollment::where('student_id', $student->student_id)->delete();

            // Delete student record
            $student->delete();

            DB::commit();

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Student approval has been undone. Student moved back to pending registrations.']);
            }
            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Student approval has been undone. Student moved back to pending registrations.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Undo approval failed: ' . $e->getMessage()], 500);
            }
            return redirect()->back()
                             ->with('error', 'Undo approval failed: ' . $e->getMessage());
        }
    }

    public function getEnrollmentDetailsJson($id)
    {
        try {
            $enrollment = Enrollment::with(['student.user', 'program', 'package', 'payment'])
                                  ->find($id);
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment not found.'
                ], 404);
            }
            $studentName = '';
            $email = '';
            $contactNumber = 'N/A';
            if ($enrollment->student) {
                $studentName = trim(
                    ($enrollment->student->firstname ?? '') . ' ' . 
                    ($enrollment->student->middlename ?? '') . ' ' . 
                    ($enrollment->student->lastname ?? '')
                );
                if ($enrollment->student->user) {
                    $email = $enrollment->student->user->email ?? $enrollment->student->email ?? '';
                } else {
                    $email = $enrollment->student->email ?? '';
                }
                $contactNumber = $enrollment->student->contact_number ?? 'N/A';
            } elseif ($enrollment->user_id) {
                $user = User::find($enrollment->user_id);
                if ($user) {
                    $studentName = trim(($user->user_firstname ?? '') . ' ' . ($user->user_lastname ?? ''));
                    $email = $user->email ?? '';
                }
                $student = Student::where('user_id', $enrollment->user_id)->first();
                if ($student) {
                    $contactNumber = $student->contact_number ?? 'N/A';
                    if (!$studentName) {
                        $studentName = trim(
                            ($student->firstname ?? '') . ' ' . 
                            ($student->middlename ?? '') . ' ' . 
                            ($student->lastname ?? '')
                        );
                    }
                    if (!$email) {
                        $email = $student->email ?? '';
                    }
                }
            }
            // Get payment details from payments table (for pending payments)
            $payment = $enrollment->payment ?? null;
            if (!$payment) {
                $payment = Payment::where('enrollment_id', $enrollment->enrollment_id)
                                 ->orderBy('created_at', 'desc')
                                 ->first();
            }
            // Get payment history (for completed/processed payments)
            $paymentHistory = $enrollment->enrollment_id ? PaymentHistory::where('enrollment_id', $enrollment->enrollment_id)
                                           ->orderBy('created_at', 'desc')
                                           ->get() : collect();
            // Determine current payment status and details
            $paymentStatus = $enrollment->payment_status ?? 'pending';
            $paymentMethod = 'N/A';
            $paymentAmount = 0;
            $paymentDate = null;
            $referenceNumber = 'N/A';
            $transactionId = 'N/A';
            $paymentNotes = '';
            // Get amount from package or enrollment
            if ($enrollment->package && isset($enrollment->package->price)) {
                $paymentAmount = $enrollment->package->price;
            } elseif ($enrollment->package && isset($enrollment->package->amount)) {
                $paymentAmount = $enrollment->package->amount;
            }
            if ($payment) {
                // Use data from payments table for pending payments
                $paymentStatus = $payment->payment_status ?? $paymentStatus;
                $paymentMethod = $payment->payment_method ?? 'N/A';
                if (isset($payment->amount) && $payment->amount > 0) {
                    $paymentAmount = $payment->amount;
                }
                $paymentDate = $payment->created_at ?? null;
                $paymentNotes = $payment->notes ?? '';
                // Extract payment details if JSON
                if ($payment->payment_details) {
                    $details = is_string($payment->payment_details) ? json_decode($payment->payment_details, true) : $payment->payment_details;
                    if (is_array($details)) {
                        $referenceNumber = $details['reference_number'] ?? $referenceNumber;
                        $transactionId = $details['transaction_id'] ?? $transactionId;
                    }
                }
            }
            // If there's payment history, get the latest one for display
            $latestHistory = $paymentHistory->first();
            if ($latestHistory && in_array($latestHistory->payment_status, ['completed', 'verified', 'approved'])) {
                $paymentStatus = $latestHistory->payment_status;
                $paymentMethod = $latestHistory->payment_method ?? $paymentMethod;
                $paymentAmount = $latestHistory->amount ?? $paymentAmount;
                $paymentDate = $latestHistory->payment_date ?? $latestHistory->created_at ?? $paymentDate;
                $paymentNotes = $latestHistory->payment_notes ?? $paymentNotes;
            }
            return response()->json([
                'student_name' => $studentName,
                'email' => $email,
                'contact_number' => $contactNumber,
                'program_name' => $enrollment->program->program_name ?? 'N/A',
                'package_name' => $enrollment->package->package_name ?? 'N/A',
                'enrollment_type' => $enrollment->enrollment_type ?? 'N/A',
                'amount' => $paymentAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'transaction_id' => $transactionId,
                'enrollment_date' => $enrollment->created_at ?? null,
                'payment_date' => $paymentDate,
                'updated_at' => $enrollment->updated_at ?? null,
                'payment_notes' => $paymentNotes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading enrollment details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignEnrollment(Request $request)
    {
        try {
            // Validate basic fields
            $request->validate([
                'program_id' => 'required|exists:programs,program_id', 
                'batch_id' => 'required|exists:batches,batch_id',
                'enrollment_type' => 'required|in:modular,full,accelerated',
                'learning_mode' => 'required|in:online,onsite,hybrid',
                'course_id' => 'nullable|exists:courses,course_id'
            ]);

            // Handle single or multiple student selection
            $studentIds = [];
            if ($request->has('student_id') && $request->student_id) {
                // Single student
                $request->validate(['student_id' => 'required|exists:students,student_id']);
                $studentIds = [$request->student_id];
            } elseif ($request->has('student_ids') && is_array($request->student_ids)) {
                // Multiple students
                $request->validate(['student_ids' => 'required|array', 'student_ids.*' => 'exists:students,student_id']);
                $studentIds = $request->student_ids;
            } else {
                return redirect()->back()->with('error', 'Please select at least one student.');
            }

            DB::beginTransaction();

            // Get the package for the program
            $package = Package::where('program_id', $request->program_id)->first();
            if (!$package) {
                return redirect()->back()->with('error', 'No package found for this program.');
            }

            $successCount = 0;
            $errors = [];

            // Process each student
            foreach ($studentIds as $studentId) {
                try {
                    // Check if student is already enrolled in this program
                    $existingEnrollment = Enrollment::where([
                        'student_id' => $studentId,
                        'program_id' => $request->program_id
                    ])->first();

                    if ($existingEnrollment) {
                        $student = Student::find($studentId);
                        $errors[] = "Student {$student->firstname} {$student->lastname} is already enrolled in this program.";
                        continue;
                    }

                    // Create enrollment
                    Enrollment::create([
                        'student_id' => $studentId,
                        'program_id' => $request->program_id,
                        'package_id' => $package->package_id,
                        'batch_id' => $request->batch_id,
                        'enrollment_type' => $request->enrollment_type,
                        'learning_mode' => $request->learning_mode,
                        'course_id' => $request->enrollment_type === 'modular' ? $request->course_id : null,
                        'enrollment_status' => 'enrolled',
                        'payment_status' => 'pending',
                        'amount' => $package->package_price,
                        'enrollment_date' => now()
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $student = Student::find($studentId);
                    $errors[] = "Failed to enroll {$student->firstname} {$student->lastname}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Prepare response message
            $message = '';
            if ($successCount > 0) {
                $message = "Successfully enrolled {$successCount} student(s).";
            }
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(' ', $errors);
            }

            return redirect()
                ->back()
                ->with($successCount > 0 ? 'success' : 'error', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Enrollment assignment failed: ' . $e->getMessage());
        }
    }

    public function studentRegistration()
    {
        $registrations = Registration::with(['user', 'package', 'program', 'plan'])
                                   ->where('status', 'pending')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        return view('admin.admin-student-registration.admin-student-registration', [
            'registrations' => $registrations,
            'history'       => false,
        ]);
    }

    public function studentRegistrationHistory()
    {
        $registrations = Registration::with(['user', 'package', 'program', 'plan'])
                                   ->where('status', 'approved')
                                   ->orderBy('approved_at', 'desc')
                                   ->get();
        return view('admin.admin-student-registration.admin-student-registration', [
            'registrations' => $registrations,
            'history'       => true,
        ]);
    }

    public function studentRegistrationRejected()
    {
        $registrations = Registration::with(['user', 'package', 'program', 'plan'])
                                   ->where('status', 'rejected')
                                   ->orderBy('rejected_at', 'desc')
                                   ->get();
        return view('admin.admin-student-registration.admin-student-registration-rejected', [
            'registrations' => $registrations,
            'type' => 'rejected'
        ]);
    }

    public function studentRegistrationResubmitted()
    {
        $registrations = Registration::with(['user', 'package', 'program', 'plan'])
                                   ->where('status', 'resubmitted')
                                   ->orderBy('resubmitted_at', 'desc')
                                   ->get();
        return view('admin.admin-student-registration.admin-student-registration-resubmitted', [
            'registrations' => $registrations,
            'type' => 'resubmitted'
        ]);
    }

    /**
     * Approve a rejected registration (undo rejection)
     */
    public function undoRejection(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            if ($registration->status !== 'rejected') {
                return redirect()->back()->with('error', 'Registration is not rejected.');
            }
            
            // Clear rejection data and set back to pending
            $registration->update([
                'status' => 'pending',
                'rejected_fields' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'updated_at' => now()
            ]);
            
            return redirect()->back()->with('success', 'Registration rejection undone successfully. Status changed to pending.');
            
        } catch (\Exception $e) {
            Log::error('Error undoing rejection: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to undo rejection.');
        }
    }

    /**
     * Approve a rejected registration directly
     */
    public function approveRejectedRegistration(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            if ($registration->status !== 'rejected') {
                return redirect()->back()->with('error', 'Registration is not rejected.');
            }
            
            // Clear rejection data and approve directly
            $registration->update([
                'status' => 'approved',
                'rejected_fields' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'approved_by' => auth()->guard('admin')->user()->admin_id,
                'approved_at' => now(),
                'updated_at' => now()
            ]);
            
            return redirect()->back()->with('success', 'Registration approved successfully.');
            
        } catch (\Exception $e) {
            Log::error('Error approving rejected registration: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve registration.');
        }
    }

    public function paymentRejected()
    {
        $payments = Payment::with([
            'enrollment.registration.user', 
            'enrollment.student', 
            'enrollment.program', 
            'enrollment.package',
            'registration' // Direct registration relationship
        ])
        ->where('payment_status', 'rejected')
        ->orderBy('rejected_at', 'desc')
        ->get();
        return view('admin.admin-student-registration.admin-payment-rejected', [
            'payments' => $payments,
            'type' => 'rejected'
        ]);
    }

    public function paymentPending()
    {
        $enrollments = Enrollment::with(['user', 'student', 'program', 'package', 'registration', 'enrollmentCourses.course', 'payment'])
            ->where('payment_status', 'pending')
            ->whereDoesntHave('payment', function($query) {
                $query->whereNotNull('payment_details');
            })
            ->where(function($query) {
                $query->whereNotNull('user_id')
                      ->orWhereNotNull('student_id');
            })
            ->whereHas('program')
            ->whereHas('package')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('enrollment_id')
            ->map(function ($enrollment) {
                // Determine student name and email from either user or student relationship
                $studentName = 'N/A';
                $studentEmail = 'N/A';
                if ($enrollment->user) {
                    $firstName = $enrollment->user->user_firstname ?? '';
                    $lastName = $enrollment->user->user_lastname ?? '';
                    $studentName = trim($firstName . ' ' . $lastName) ?: 'N/A';
                    $studentEmail = $enrollment->user->email ?? 'N/A';
                } elseif ($enrollment->student) {
                    $firstName = $enrollment->student->firstname ?? '';
                    $lastName = $enrollment->student->lastname ?? '';
                    $studentName = trim($firstName . ' ' . $lastName) ?: 'N/A';
                    $studentEmail = $enrollment->student->email ?? 'N/A';
                }
                $enrollment->student_name = $studentName;
                $enrollment->student_email = $studentEmail;
                return $enrollment;
            });
        return view('admin.admin-student-registration.admin-payment-pending', [
            'enrollments' => $enrollments,
            'pendingApprovals' => $this->getPaymentPendingApprovals(),
        ]);
    }

    public function getPaymentPendingApprovals()
    {
        $pendingApprovals = Enrollment::with(['user', 'student', 'program', 'package', 'registration', 'payment'])
            ->whereHas('payment', function($query) {
                $query->whereNotNull('payment_details')
                      ->where('payment_status', 'pending');
            })
            ->where(function($query) {
                $query->whereNotNull('user_id')
                      ->orWhereNotNull('student_id');
            })
            ->whereHas('program')
            ->whereHas('package')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('enrollment_id')
            ->map(function ($enrollment) {
                $studentName = 'N/A';
                $studentEmail = 'N/A';
                if ($enrollment->user) {
                    $firstName = $enrollment->user->user_firstname ?? '';
                    $lastName = $enrollment->user->user_lastname ?? '';
                    $studentName = trim($firstName . ' ' . $lastName) ?: 'N/A';
                    $studentEmail = $enrollment->user->email ?? 'N/A';
                } elseif ($enrollment->student) {
                    $firstName = $enrollment->student->firstname ?? '';
                    $lastName = $enrollment->student->lastname ?? '';
                    $studentName = trim($firstName . ' ' . $lastName) ?: 'N/A';
                    $studentEmail = $enrollment->student->email ?? 'N/A';
                }
                $enrollment->student_name = $studentName;
                $enrollment->student_email = $studentEmail;
                if ($enrollment->payment) {
                    $enrollment->payment_submitted_at = $enrollment->payment->created_at;
                    $enrollment->payment_method = $enrollment->payment->payment_method ?? 'Not specified';
                    $enrollment->payment_amount = $enrollment->payment->amount ?? 0;
                }
                return $enrollment;
            });
        return $pendingApprovals;
    }

    public function paymentHistory()
    {
        $paymentHistory = \App\Models\PaymentHistory::with(['enrollment.student', 'enrollment.program', 'enrollment.package'])
            ->orderBy('payment_date', 'desc')
            ->get();
        return view('admin.admin-student-registration.admin-payment-history', [
            'paymentHistory' => $paymentHistory
        ]);
    }

    public function getPaymentHistoryDetailsJson($id)
    {
        $payment = \App\Models\PaymentHistory::with(['enrollment.student', 'enrollment.program', 'enrollment.package'])->find($id);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment history not found.'], 404);
        }
        $enrollment = $payment->enrollment;
        $student = $enrollment && $enrollment->student ? $enrollment->student : null;
        $program = $enrollment && $enrollment->program ? $enrollment->program : null;
        $package = $enrollment && $enrollment->package ? $enrollment->package : null;
        return response()->json([
            'student_name' => $student ? trim(($student->firstname ?? '') . ' ' . ($student->middlename ?? '') . ' ' . ($student->lastname ?? '')) : 'N/A',
            'email' => $student ? ($student->email ?? 'N/A') : 'N/A',
            'program_name' => $program ? $program->program_name : 'N/A',
            'package_name' => $package ? $package->package_name : 'N/A',
            'amount' => $payment->amount,
            'payment_status' => $payment->payment_status,
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'transaction_id' => $payment->transaction_id,
            'payment_date' => $payment->payment_date,
            'notes' => $payment->payment_notes,
            'updated_at' => $payment->updated_at,
        ]);
    }

    public function markAsPaid($id)
    {
        try {
            DB::beginTransaction();
            
            Log::info('Mark as paid request received', ['enrollment_id' => $id]);
            
            // Find enrollment by ID (enrollment_id is the primary key)
            $enrollment = Enrollment::where('enrollment_id', $id)->first();
            
            if (!$enrollment) {
                Log::error('Enrollment not found for mark as paid', ['enrollment_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment not found'
                ], 404);
            }
            
            // Check if already paid
            if ($enrollment->payment_status === 'paid') {
                Log::warning('Attempted to mark already paid enrollment', ['enrollment_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment is already marked as paid'
                ], 400);
            }
            
            Log::info('Creating payment history record', [
                'enrollment_id' => $enrollment->enrollment_id,
                'user_id' => $enrollment->user_id,
                'student_id' => $enrollment->student_id
            ]);
            
            // Create payment history record before updating enrollment
            $paymentHistory = PaymentHistory::create([
                'enrollment_id' => $enrollment->enrollment_id,
                'user_id' => $enrollment->user_id,
                'student_id' => $enrollment->student_id,
                'program_id' => $enrollment->program_id,
                'package_id' => $enrollment->package_id,
                'payment_status' => 'paid',
                'payment_method' => 'manual', // Since it's marked by admin
                'payment_notes' => 'Payment marked as paid by administrator',
                'payment_date' => now(),
                'processed_by_admin_id' => session('admin_id') ?? session('user_id') ?? 1,
            ]);
            
            Log::info('Payment history created', ['payment_history_id' => $paymentHistory->payment_history_id]);
            
            // Update enrollment payment status to paid
            $enrollment->update([
                'payment_status' => 'paid',
                'updated_at' => now()
            ]);
            
            // Update batch capacity if enrollment is approved and has a batch
            if ($enrollment->batch_id && $enrollment->enrollment_status === 'approved') {
                $batch = \App\Models\StudentBatch::find($enrollment->batch_id);
                if ($batch) {
                    // Recalculate actual capacity based on approved and paid enrollments
                    $actualCapacity = \App\Models\Enrollment::where('batch_id', $batch->batch_id)
                        ->where('enrollment_status', 'approved')
                        ->where('payment_status', 'paid')
                        ->count();
                    
                    $batch->update(['current_capacity' => $actualCapacity]);
                    Log::info('Updated batch capacity after payment', ['batch_id' => $batch->batch_id, 'new_capacity' => $actualCapacity]);
                }
            }
            
            // Process referral if both enrollment and payment are approved/paid
            \App\Helpers\ReferralCodeGenerator::processPendingReferral($enrollment->enrollment_id);

            DB::commit();
            
            Log::info('Payment marked as paid successfully', [
                'enrollment_id' => $enrollment->enrollment_id,
                'admin_id' => session('admin_id') ?? session('user_id'),
                'payment_history_id' => $paymentHistory->payment_history_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as paid successfully and migrated to payment history'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error marking payment as paid', [
                'enrollment_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Payment rejection methods
    public function rejectPaymentWithFields(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
                'rejected_fields' => 'array'
            ]);

            // Find the payment record
            $payment = Payment::where('enrollment_id', $id)->firstOrFail();
            
            // Store the current payment data as original before updating
            $originalPaymentData = $payment->toArray();
            
            // Store the rejection reason and fields
            $payment->update([
                'payment_status' => 'rejected',
                'rejection_reason' => $request->reason,
                'rejected_fields' => json_encode($request->rejected_fields ?? []),
                'rejected_by' => auth()->guard('admin')->user()->admin_id ?? 1,
                'rejected_at' => now(),
                'original_payment_data' => json_encode($originalPaymentData)
            ]);

            return redirect()
                ->route('admin.student.registration.payment.pending')
                ->with('success', 'Payment rejected with marked fields.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Payment rejection failed: ' . $e->getMessage());
        }
    }

    public function approvePaymentResubmission(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            if ($payment->payment_status !== 'resubmitted') {
                return redirect()->back()->with('error', 'Payment is not in resubmitted status.');
            }

            // Update to paid and clear rejection data
            $payment->update([
                'payment_status' => 'paid',
                'rejection_reason' => null,
                'rejected_fields' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'resubmitted_at' => null,
                'verified_by' => auth()->guard('admin')->user()->admin_id ?? 1,
                'verified_at' => now()
            ]);

            // Also update the enrollment payment status
            if ($payment->enrollment) {
                $payment->enrollment->update(['payment_status' => 'paid']);
            }

            return redirect()
                ->route('admin.student.registration.payment.pending')
                ->with('success', 'Payment resubmission approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Payment approval failed: ' . $e->getMessage());
        }
    }

    public function updatePaymentRejection(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
                'rejected_fields' => 'array'
            ]);

            $payment = Payment::findOrFail($id);
            
            $payment->update([
                'rejection_reason' => $request->reason,
                'rejected_fields' => json_encode($request->rejected_fields ?? []),
                'rejected_by' => auth()->guard('admin')->user()->admin_id ?? 1,
                'rejected_at' => now()
            ]);

            return redirect()
                ->route('admin.student.registration.payment.pending')
                ->with('success', 'Payment rejection details updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function getPaymentDetailsJson($id)
    {
        try {
            $payment = Payment::with(['enrollment.program', 'enrollment.package'])->findOrFail($id);
            
            $paymentDetails = json_decode($payment->payment_details, true) ?? [];
            
            return response()->json([
                'payment_id' => $payment->payment_id,
                'enrollment_id' => $payment->enrollment_id,
                'student_id' => $payment->student_id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'reference_number' => $payment->reference_number,
                'rejection_reason' => $payment->rejection_reason,
                'rejected_fields' => $payment->rejected_fields,
                'payment_details' => $paymentDetails,
                'program_name' => $payment->enrollment->program->program_name ?? 'N/A',
                'package_name' => $payment->enrollment->package->package_name ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment not found or database error.'], 404);
        }
    }

    public function getOriginalPaymentData($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            if (!$payment->original_payment_data) {
                return response()->json(['error' => 'No original payment data found.'], 404);
            }

            $originalData = json_decode($payment->original_payment_data, true);
            $originalData['rejection_reason'] = $payment->rejection_reason;
            $originalData['rejected_fields'] = $payment->rejected_fields;

            return response()->json($originalData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load original payment data.'], 404);
        }
    }

    public function getEnrollmentPaymentDetails($id)
    {
        try {
            $enrollment = Enrollment::with(['program', 'package'])->findOrFail($id);
            $payment = Payment::where('enrollment_id', $id)->first();
            
            $data = [
                'enrollment_id' => $enrollment->enrollment_id,
                'program_name' => $enrollment->program->program_name ?? 'N/A',
                'package_name' => $enrollment->package->package_name ?? 'N/A',
                'amount' => $enrollment->package->amount ?? 0,
            ];

            if ($payment) {
                $paymentDetails = json_decode($payment->payment_details, true) ?? [];
                $data = array_merge($data, [
                    'payment_method' => $payment->payment_method,
                    'reference_number' => $payment->reference_number,
                    'payment_details' => $paymentDetails
                ]);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Enrollment not found or database error.'], 404);
        }
    }

    public function approveEnrollment($enrollmentId)
    {
        try {
            DB::beginTransaction();
            
            // Find enrollment by enrollment_id
            $enrollment = Enrollment::where('enrollment_id', $enrollmentId)->first();
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment not found'
                ], 404);
            }
            
            // Check if already approved
            if ($enrollment->enrollment_status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment is already approved'
                ], 400);
            }
            
            // Update enrollment status to approved
            $enrollment->update([
                'enrollment_status' => 'approved',
                'updated_at' => now()
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Enrollment approved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error approving enrollment', [
                'enrollment_id' => $enrollmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error approving enrollment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display chat logs for admin monitoring
     */
    public function chatIndex(Request $request)
    {
        // In a real application, this would fetch chat messages from a database
        // For now, we'll create a sample chat log interface
        
        $chatRooms = [
            [
                'id' => 1,
                'name' => 'General Support',
                'participants' => 15,
                'last_message' => 'Thanks for your help!',
                'last_activity' => Carbon::now()->subMinutes(5),
                'unread_count' => 3,
                'type' => 'support'
            ],
            [
                'id' => 2,
                'name' => 'Technical Issues',
                'participants' => 8,
                'last_message' => 'The system is working now',
                'last_activity' => Carbon::now()->subMinutes(15),
                'unread_count' => 1,
                'type' => 'technical'
            ],
            [
                'id' => 3,
                'name' => 'Course Inquiries',
                'participants' => 12,
                'last_message' => 'When does the next batch start?',
                'last_activity' => Carbon::now()->subMinutes(30),
                'unread_count' => 0,
                'type' => 'courses'
            ],
            [
                'id' => 4,
                'name' => 'Student Services',
                'participants' => 25,
                'last_message' => 'Payment confirmation received',
                'last_activity' => Carbon::now()->subHour(),
                'unread_count' => 7,
                'type' => 'services'
            ]
        ];
        
        $recentMessages = [
            [
                'id' => 1,
                'user_name' => 'John Doe',
                'user_type' => 'student',
                'room' => 'General Support',
                'message' => 'I need help with my course enrollment',
                'timestamp' => Carbon::now()->subMinutes(2),
                'status' => 'unread'
            ],
            [
                'id' => 2,
                'user_name' => 'Jane Smith',
                'user_type' => 'professor',
                'room' => 'Technical Issues',
                'message' => 'The quiz generator is not working properly',
                'timestamp' => Carbon::now()->subMinutes(10),
                'status' => 'read'
            ],
            [
                'id' => 3,
                'user_name' => 'Mike Johnson',
                'user_type' => 'student',
                'room' => 'Course Inquiries',
                'message' => 'What are the prerequisites for Advanced Programming?',
                'timestamp' => Carbon::now()->subMinutes(25),
                'status' => 'responded'
            ]
        ];
        
        $stats = [
            'total_conversations' => count($chatRooms),
            'active_users' => 45,
            'unread_messages' => collect($chatRooms)->sum('unread_count'),
            'response_time_avg' => '2.5 minutes'
        ];
        
        return view('admin.chat.index', compact('chatRooms', 'recentMessages', 'stats'));
    }
    
    /**
     * Display specific chat room
     */
    public function chatRoom($roomId)
    {
        // Sample chat messages for the room
        $messages = [
            [
                'id' => 1,
                'user_name' => 'John Doe',
                'user_type' => 'student',
                'message' => 'Hello, I need help with my course enrollment',
                'timestamp' => Carbon::now()->subMinutes(30),
                'avatar' => 'JD'
            ],
            [
                'id' => 2,
                'user_name' => 'Admin Support',
                'user_type' => 'admin',
                'message' => 'Hi John! I\'d be happy to help you with your enrollment. Which course are you trying to enroll in?',
                'timestamp' => Carbon::now()->subMinutes(28),
                'avatar' => 'AS'
            ],
            [
                'id' => 3,
                'user_name' => 'John Doe',
                'user_type' => 'student',
                'message' => 'I\'m looking at the Advanced Programming course, but I can\'t find the enrollment button',
                'timestamp' => Carbon::now()->subMinutes(25),
                'avatar' => 'JD'
            ],
            [
                'id' => 4,
                'user_name' => 'Admin Support',
                'user_type' => 'admin',
                'message' => 'Let me check your account status. Can you please provide your student ID?',
                'timestamp' => Carbon::now()->subMinutes(20),
                'avatar' => 'AS'
            ]
        ];
        
        $roomInfo = [
            'id' => $roomId,
            'name' => 'General Support',
            'participants' => 15,
            'type' => 'support'
        ];
        
        return view('admin.chat.room', compact('messages', 'roomInfo'));
    }
    
    /**
     * Display FAQ management page
     */
    public function faqIndex()
    {
        $faqs = [
            [
                'id' => 1,
                'question' => 'How do I enroll in a course?',
                'answer' => 'To enroll in a course, go to your dashboard, select "Available Courses", choose your desired course, and click "Enroll Now". Complete the payment process to finalize your enrollment.',
                'category' => 'Enrollment',
                'category_id' => 1,
                'keywords' => 'enroll, register, course, signup',
                'status' => 'active',
                'views' => 145,
                'updated_at' => Carbon::now()->subDays(2)->format('M j, Y')
            ],
            [
                'id' => 2,
                'question' => 'What are the payment options?',
                'answer' => 'We accept credit/debit cards, PayPal, bank transfers, and installment plans for select courses. All payments are processed securely.',
                'category' => 'Payment',
                'category_id' => 2,
                'keywords' => 'payment, pay, fee, money, cost',
                'status' => 'active',
                'views' => 98,
                'updated_at' => Carbon::now()->subDays(1)->format('M j, Y')
            ],
            [
                'id' => 3,
                'question' => 'How do I check my class schedule?',
                'answer' => 'Login to your dashboard, go to "My Courses", and click the "Schedule" tab. You can also export your schedule to your calendar.',
                'category' => 'Schedule',
                'category_id' => 3,
                'keywords' => 'schedule, time, class, timetable',
                'status' => 'active',
                'views' => 87,
                'updated_at' => Carbon::now()->subDays(3)->format('M j, Y')
            ],
            [
                'id' => 4,
                'question' => 'How do I get my certificate?',
                'answer' => 'Complete all course modules, pass assessments, maintain 80% attendance, and complete the final project. Certificates are generated automatically within 5-7 business days.',
                'category' => 'Certificate',
                'category_id' => 4,
                'keywords' => 'certificate, diploma, completion, graduate',
                'status' => 'active',
                'views' => 156,
                'updated_at' => Carbon::now()->subDays(5)->format('M j, Y')
            ],
            [
                'id' => 5,
                'question' => 'How do I contact support?',
                'answer' => 'Contact support via live chat, email (support@artc.edu), phone (+1-555-123-4567), or submit a ticket through the support portal.',
                'category' => 'Support',
                'category_id' => 5,
                'keywords' => 'support, help, contact, assistance',
                'status' => 'active',
                'views' => 234,
                'updated_at' => Carbon::now()->subDays(1)->format('M j, Y')
            ]
        ];
        
        $categories = [
            [
                'id' => 1,
                'name' => 'Enrollment',
                'count' => 1
            ],
            [
                'id' => 2,
                'name' => 'Payment',
                'count' => 1
            ],
            [
                'id' => 3,
                'name' => 'Schedule',
                'count' => 1
            ],
            [
                'id' => 4,
                'name' => 'Certificate',
                'count' => 1
            ],
            [
                'id' => 5,
                'name' => 'Support',
                'count' => 1
            ]
        ];
        
        return view('admin.faq.index', compact('faqs', 'categories'));
    }
    
    /**
     * Store new FAQ
     */
    public function storeFaq(Request $request)
    {
        // In a real application, this would save to database
        return response()->json(['message' => 'FAQ saved successfully']);
    }
    
    /**
     * Update FAQ
     */
    public function updateFaq(Request $request, $id)
    {
        // In a real application, this would update the database
        return response()->json(['message' => 'FAQ updated successfully']);
    }
    
    /**
     * Delete FAQ
     */
    public function deleteFaq($id)
    {
        // In a real application, this would delete from database
        return response()->json(['message' => 'FAQ deleted successfully']);
    }

    // New: Get payment details by enrollment ID
    public function getPaymentDetailsByEnrollment($enrollmentId)
    {
        try {
            $payment = Payment::where('enrollment_id', $enrollmentId)->first();
            if (!$payment) {
                return response()->json(['error' => 'Payment not found for this enrollment.'], 404);
            }
            $paymentDetails = $payment->payment_details;
            if (is_string($paymentDetails)) {
                $paymentDetails = json_decode($paymentDetails, true);
            }
            $response = [
                'payment_id' => $payment->payment_id,
                'enrollment_id' => $payment->enrollment_id,
                'student_id' => $payment->student_id,
                'amount' => $payment->amount ?? 0,
                'payment_method' => $payment->payment_method ?? 'N/A',
                'payment_status' => $payment->payment_status ?? 'N/A',
                'reference_number' => $payment->reference_number ?? ($paymentDetails['reference_number'] ?? 'N/A'),
                'payment_proof_path' => $paymentDetails['payment_proof_path'] ?? null,
                'payment_proof_url' => isset($paymentDetails['payment_proof_path']) 
                    ? asset('storage/' . $paymentDetails['payment_proof_path']) 
                    : null,
                'created_at' => $payment->created_at ? $payment->created_at->format('M d, Y g:i A') : 'N/A',
                'notes' => $payment->notes ?? '',
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading payment details: ' . $e->getMessage()], 500);
        }
    }

    // Reject registration method
    public function rejectRegistration(Request $request, $id)
    {
        $request->validate([
            'reason'          => 'required|string',
            'rejected_fields' => 'nullable|array',
        ]);
    
        try {
            $registration = Registration::findOrFail($id);
            $registration->update([
                'status'           => 'rejected',
                'rejection_reason' => $request->reason,
                'rejected_fields'  => json_encode($request->input('rejected_fields', [])),
                'rejected_at'      => now(),
            ]);
    
            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration rejected successfully.');
    
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    // Approve registration method
    public function approveRegistration(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            // Store original data for undo functionality
            $registration->original_submission = json_encode([
                'status' => $registration->status,
                'approved_at' => $registration->approved_at,
                'approved_by' => session('admin_id')
            ]);
            
            $registration->status = 'approved';
            $registration->approved_at = now();
            $registration->approved_by = session('admin_id');
            $registration->save();

            // Create student record if not exists
            $this->createStudentFromRegistration($registration);

            return response()->json([
                'success' => true,
                'message' => 'Registration approved successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving registration: ' . $e->getMessage()
            ], 500);
        }
    }

    // Undo registration approval - move back to pending with comment
    public function undoRegistrationApproval(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            if ($registration->status !== 'approved') {
                if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Only approved registrations can be undone.'], 400);
                }
                return redirect()->back()->with('error', 'Only approved registrations can be undone.');
            }
            $registration->status = 'pending';
            $registration->approved_at = null;
            $registration->approved_by = null;
            $registration->undo_reason = $request->input('undo_reason', null);
            $registration->undone_at = now();
            $registration->undone_by = auth()->guard('admin')->user()->admin_id ?? null;
            $registration->save();
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Registration approval undone successfully. Student moved back to pending status.']);
            }
            return redirect()->back()->with('success', 'Registration approval undone successfully. Student moved back to pending status.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error undoing registration approval: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error undoing registration approval: ' . $e->getMessage());
        }
    }

    // Mark payment as paid
    public function markPaymentAsPaid(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            $payment->payment_status = 'paid';
            $payment->verified_by = session('admin_id');
            $payment->verified_at = now();
            $payment->notes = $request->input('notes', 'Marked as paid by administrator');
            $payment->save();

            // Update enrollment payment status
            if ($payment->enrollment_id) {
                $enrollment = Enrollment::find($payment->enrollment_id);
                if ($enrollment) {
                    $enrollment->payment_status = 'paid';
                    $enrollment->save();
                }
            }

            // Create payment history record
            PaymentHistory::create([
                'enrollment_id' => $payment->enrollment_id,
                'user_id' => $payment->enrollment->user_id ?? null,
                'student_id' => $payment->student_id,
                'program_id' => $payment->program_id,
                'package_id' => $payment->package_id,
                'amount' => $payment->amount,
                'payment_status' => 'paid',
                'payment_method' => 'manual',
                'payment_notes' => 'Payment marked as paid by administrator',
                'payment_date' => now(),
                'processed_by_admin_id' => session('admin_id')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as paid successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking payment as paid: ' . $e->getMessage()
            ], 500);
        }
    }

    // View payment details
    public function viewPaymentDetails($id)
    {
        try {
            $payment = Payment::with(['enrollment.user', 'enrollment.student', 'enrollment.program', 'enrollment.package'])
                             ->findOrFail($id);

            return response()->json([
                'success' => true,
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payment details: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reject payment
    public function rejectPayment(Request $request, $id)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string',
                'rejected_fields' => 'nullable|array'
            ]);

            $payment = Payment::findOrFail($id);
            
            $payment->payment_status = 'rejected';
            $payment->rejection_reason = $request->input('rejection_reason');
            $payment->rejected_fields = json_encode($request->input('rejected_fields', []));
            $payment->rejected_by = session('admin_id');
            $payment->rejected_at = now();
            $payment->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting payment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper method to create student from registration
    private function createStudentFromRegistration($registration)
    {
        // Check if student already exists
        $existingStudent = Student::where('user_id', $registration->user_id)->first();
        
        if (!$existingStudent) {
            // Create student record
            $studentId = $this->generateStudentId();
            
            Student::create([
                'student_id' => $studentId,
                'user_id' => $registration->user_id,
                'firstname' => $registration->firstname,
                'middlename' => $registration->middlename,
                'lastname' => $registration->lastname,
                'email' => $registration->user->email ?? '',
                'package_id' => $registration->package_id,
                'package_name' => $registration->package_name,
                'program_id' => $registration->program_id,
                'program_name' => $registration->program_name,
                'enrollment_type' => $registration->enrollment_type,
                'learning_mode' => $registration->learning_mode,
                'education_level' => $registration->education_level,
                'status' => 'approved',
                'date_approved' => now()
            ]);
        }
    }

    // Helper method to generate student ID
    private function generateStudentId()
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last student ID for this year and month
        $lastStudent = Student::where('student_id', 'LIKE', "{$year}-{$month}-%")
                             ->orderBy('student_id', 'desc')
                             ->first();
        
        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_id, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s-%s-%05d', $year, $month, $newNumber);
    }

    public function undoPendingPayment(Request $request, $enrollmentId)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);
            $enrollment = Enrollment::findOrFail($enrollmentId);
            $payment = Payment::where('enrollment_id', $enrollmentId)->first();
            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'No payment record found for this enrollment.'], 404);
            }
            $payment->payment_status = 'rejected';
            $payment->rejection_reason = $request->input('reason');
            $payment->rejected_at = now();
            $payment->rejected_by = auth()->guard('admin')->user()->admin_id ?? 1;
            $payment->save();
            $enrollment->payment_status = 'rejected';
            $enrollment->save();
            return response()->json(['success' => true, 'message' => 'Payment has been undone and moved back to pending.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error undoing payment: ' . $e->getMessage()], 500);
        }
    }

    public function undoPaymentHistory(Request $request, $paymentHistoryId)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);
            $paymentHistory = PaymentHistory::findOrFail($paymentHistoryId);
            $enrollmentId = $paymentHistory->enrollment_id;
            // Delete the payment history record
            $paymentHistory->delete();
            // Also update the related Payment and Enrollment if they exist
            $payment = Payment::where('enrollment_id', $enrollmentId)->first();
            if ($payment) {
                $payment->payment_status = 'pending';
                $payment->rejection_reason = $request->input('reason');
                $payment->rejected_at = now();
                $payment->rejected_by = auth()->guard('admin')->user()->admin_id ?? 1;
                $payment->save();
            }
            $enrollment = Enrollment::find($enrollmentId);
            if ($enrollment) {
                $enrollment->payment_status = 'pending';
                $enrollment->save();
            }
            return response()->json(['success' => true, 'message' => 'Payment history undone and removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error undoing payment history: ' . $e->getMessage()], 500);
        }
    }

    /**
     * View assignment submissions for admin grading
     */
    public function viewSubmissions(Request $request)
    {
        try {
            // Get filter parameters
            $programId = $request->get('program_id');
            $moduleId = $request->get('module_id');
            $status = $request->get('status');

            // Build query for submissions with proper relationships
            $query = AssignmentSubmission::with([
                'student' => function($q) {
                    $q->with('user');
                }, 
                'program', 
                'module'
            ])->orderBy('submitted_at', 'desc');

            // Apply filters
            if ($programId) {
                $query->where('program_id', $programId);
            }

            if ($moduleId) {
                $query->where('module_id', $moduleId);
            }

            if ($status) {
                $query->where('status', $status);
            }

            // Get submissions with pagination
            $submissions = $query->paginate(10);

            // Decode files JSON to array for each submission
            foreach ($submissions as $submission) {
                if (is_string($submission->files)) {
                    $decoded = json_decode($submission->files, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $submission->files = $decoded;
                    }
                }
            }

            // Get all programs and modules for filter dropdowns
            $programs = Program::where('is_archived', false)
                ->orderBy('program_name')
                ->get();

            $modules = Module::where('is_archived', false)
                ->orderBy('module_name')
                ->get();

            return view('admin.admin-student-submissions.admin-submission', compact(
                'submissions',
                'programs',
                'modules'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading admin submissions: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error loading submissions: ' . $e->getMessage());
        }
    }

    /**
     * Grade an assignment submission
     */
    public function gradeSubmission(Request $request, $id)
    {
        try {
            $request->validate([
                'grade' => 'required|numeric|min:0|max:100',
                'feedback' => 'nullable|string|max:2000',
                'status' => 'required|in:graded,reviewed'
            ]);

            $submission = AssignmentSubmission::findOrFail($id);

            // Update submission with grade and feedback
            $submission->update([
                'grade' => $request->grade,
                'feedback' => $request->feedback,
                'status' => $request->status,
                'graded_at' => now(),
                'graded_by' => session('admin_id') ?? 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assignment graded successfully!',
                'submission' => $submission
            ]);

        } catch (\Exception $e) {
            Log::error('Error grading submission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error grading submission: ' . $e->getMessage()
            ], 500);
        }
    }
}
