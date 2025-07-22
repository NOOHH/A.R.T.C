<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentPaymentModalController extends Controller
{
    public function __construct()
    {
        $this->middleware('student.auth');
    }

    /**
     * Get enabled payment methods for student
     */
    public function getPaymentMethods()
    {
        try {
            $paymentMethods = PaymentMethod::where('is_enabled', true)
                ->with('fields') // Include payment method fields
                ->orderBy('sort_order')
                ->get(['payment_method_id', 'method_name', 'method_type', 'description', 'qr_code_path', 'instructions']);

            return response()->json([
                'success' => true,
                'data' => $paymentMethods
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching payment methods for student: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch payment methods'], 500);
        }
    }

    /**
     * Process payment proof upload
     */
    public function uploadPaymentProof(Request $request)
    {
        try {
            $request->validate([
                'payment_proof' => 'required|image|mimes:jpeg,jpg,png|max:5120',
                'reference_number' => 'nullable|string|max:255',
                'payment_method_id' => 'required|exists:payment_methods,payment_method_id',
                'enrollment_id' => 'required|exists:enrollments,enrollment_id',
                'amount' => 'required|numeric|min:0'
            ]);

            // Get current user and student
            $userId = session('user_id');
            $student = Student::where('user_id', $userId)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => 'Student record not found'
                ], 404);
            }

            // Verify enrollment belongs to student
            $enrollment = Enrollment::where('enrollment_id', $request->enrollment_id)
                ->where(function($query) use ($userId, $student) {
                    $query->where('user_id', $userId)
                          ->orWhere('student_id', $student->student_id);
                })
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'error' => 'Enrollment not found or access denied'
                ], 403);
            }

            // Store payment proof file
            $file = $request->file('payment_proof');
            $filename = 'payment_proof_' . $enrollment->enrollment_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payment_proofs', $filename, 'public');

            // Get payment method details
            $paymentMethod = PaymentMethod::find($request->payment_method_id);

            // Create payment record
            $payment = Payment::create([
                'enrollment_id' => $enrollment->enrollment_id,
                'student_id' => $student->student_id,
                'program_id' => $enrollment->program_id,
                'package_id' => $enrollment->package_id,
                'payment_method' => $paymentMethod->method_type,
                'amount' => $request->amount,
                'payment_status' => 'pending',
                'payment_details' => json_encode([
                    'payment_proof_path' => $path,
                    'reference_number' => $request->reference_number,
                    'payment_method_name' => $paymentMethod->method_name,
                    'uploaded_at' => now()->toISOString()
                ]),
                'reference_number' => $request->reference_number,
                'notes' => 'Payment proof uploaded by student'
            ]);

            // Update enrollment payment status to pending verification
            $enrollment->update([
                'payment_status' => 'pending'
            ]);

            Log::info('Payment proof uploaded successfully', [
                'enrollment_id' => $enrollment->enrollment_id,
                'student_id' => $student->student_id,
                'payment_id' => $payment->payment_id,
                'file_path' => $path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment proof uploaded successfully. Your payment will be verified within 24-48 hours.',
                'payment_id' => $payment->payment_id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Payment proof upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to upload payment proof. Please try again.'
            ], 500);
        }
    }

    /**
     * Get enrollment payment details
     */
    public function getEnrollmentPaymentDetails($enrollmentId)
    {
        try {
            $userId = session('user_id');
            $student = Student::where('user_id', $userId)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => 'Student record not found'
                ], 404);
            }

            $enrollment = Enrollment::with(['program', 'package'])
                ->where('enrollment_id', $enrollmentId)
                ->where(function($query) use ($userId, $student) {
                    $query->where('user_id', $userId)
                          ->orWhere('student_id', $student->student_id);
                })
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'error' => 'Enrollment not found or access denied'
                ], 403);
            }

            $amount = 0;
            if ($enrollment->package) {
                $amount = $enrollment->package->package_price ?? $enrollment->package->price ?? 0;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'enrollment_id' => $enrollment->enrollment_id,
                    'program_name' => $enrollment->program->program_name ?? 'N/A',
                    'package_name' => $enrollment->package->package_name ?? 'N/A',
                    'amount' => $amount,
                    'enrollment_status' => $enrollment->enrollment_status,
                    'payment_status' => $enrollment->payment_status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching enrollment payment details', [
                'enrollment_id' => $enrollmentId,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch enrollment details'
            ], 500);
        }
    }
}
