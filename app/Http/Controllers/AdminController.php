<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Registration;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Module;
use App\Models\Enrollment;
use App\Models\PaymentHistory;
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
            $registration = Registration::with(['user', 'program', 'package', 'plan'])->findOrFail($id);
            
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
                'learning_mode' => $registration->learning_mode,
                'Start_Date' => $registration->Start_Date,
                'status' => $registration->status,
                'PSA' => $registration->PSA,
                'TOR' => $registration->TOR,
                'Course_Cert' => $registration->Course_Cert,
                'good_moral' => $registration->good_moral,
                'photo_2x2' => $registration->photo_2x2,
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

                // Create the Student record
                $student = Student::create([
                    'student_id'               => $studentId,
                    'user_id'                  => $user?->user_id,
                    'firstname'                => $registration->firstname,
                    'middlename'               => $registration->middlename,
                    'lastname'                 => $registration->lastname,
                    'student_school'           => $registration->student_school,
                    'street_address'           => $registration->street_address,
                    'state_province'           => $registration->state_province,
                    'city'                     => $registration->city,
                    'zipcode'                  => $registration->zipcode,
                    'contact_number'           => $registration->contact_number,
                    'emergency_contact_number' => $registration->emergency_contact_number,
                    'good_moral'               => $registration->good_moral,
                    'PSA'                      => $registration->PSA,
                    'Course_Cert'              => $registration->Course_Cert,
                    'TOR'                      => $registration->TOR,
                    'Cert_of_Grad'             => $registration->Cert_of_Grad,
                    'Undergraduate'            => $registration->Undergraduate,
                    'Graduate'                 => $registration->Graduate,
                    'photo_2x2'                => $registration->photo_2x2,
                    'Start_Date'               => $registration->Start_Date,
                    'date_approved'            => $now,
                    'email'                    => $user?->email,
                ]);
            }

            // Find existing enrollment for this registration (created during registration process)
            $enrollment = Enrollment::where('registration_id', $registration->registration_id)->first();
            
            // Get batch_id from session if it was stored during registration
            $batchId = session('selected_batch_id');
            
            if ($enrollment) {
                // Update enrollment with student_id and user_id now that student is approved
                $enrollment->student_id = $student->student_id;
                $enrollment->user_id = $user?->user_id;
                $enrollment->enrollment_status = 'approved';
                
                // Include batch_id if it was selected during registration
                if ($batchId) {
                    $enrollment->batch_id = $batchId;
                    Log::info('Setting batch_id on existing enrollment', ['batch_id' => $batchId, 'enrollment_id' => $enrollment->enrollment_id]);
                }
                
                $enrollment->save();
            } else {
                // Fallback: Create enrollment record if it doesn't exist
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
                
                // Include batch_id if it was selected during registration
                if ($batchId) {
                    $enrollmentData['batch_id'] = $batchId;
                    Log::info('Creating new enrollment with batch_id', ['batch_id' => $batchId, 'student_id' => $student->student_id]);
                }
                
                Enrollment::create($enrollmentData);
            }
            
            // Clear the batch selection from session after using it
            if ($batchId) {
                session()->forget('selected_batch_id');
                Log::info('Cleared batch_id from session after enrollment creation');
            }

            // Remove from pending
            $registration->delete();

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

    public function studentRegistration()
    {
        $registrations = Registration::with(['user', 'package', 'program', 'plan'])->get();
        return view('admin.admin-student-registration.admin-student-registration', [
            'registrations' => $registrations,
            'history'       => false,
        ]);
    }

    public function studentRegistrationHistory()
    {
        $registrations = Student::with(['user', 'enrollments.program', 'enrollments.package'])->get();
        return view('admin.admin-student-registration.admin-student-registration', [
            'registrations' => $registrations,
            'history'       => true,
        ]);
    }

    public function paymentPending()
    {
        // Get enrollments with pending payments - include both user and student relationships
        $enrollments = Enrollment::with(['user', 'student', 'program', 'package'])
                                ->where('payment_status', 'pending')
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->map(function ($enrollment) {
                                    // Determine student name from either user or student relationship
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
        ]);
    }

    public function paymentHistory()
    {
        // Get all enrollments with completed payments (paid status)
        $enrollments = Enrollment::with(['student.user', 'program', 'package'])
                                ->where('payment_status', 'paid')
                                ->orderBy('updated_at', 'desc')
                                ->get();

        return view('admin.admin-student-registration.admin-payment-history', [
            'enrollments' => $enrollments,
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
}
