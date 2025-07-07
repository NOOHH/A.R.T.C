<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class StudentPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('student.auth');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,enrollment_id',
            'program_id' => 'required|exists:programs,program_id',
            'payment_method' => 'required|in:credit_card,gcash,bank_transfer,installment',
        ]);

        try {
            DB::beginTransaction();

            // Get enrollment
            $enrollment = Enrollment::findOrFail($request->enrollment_id);
            
            // Verify enrollment belongs to current user
            $student = \App\Models\Student::where('user_id', session('user_id'))->first();
            if (!$student || $enrollment->student_id !== $student->student_id) {
                return back()->with('error', 'Unauthorized access to enrollment.');
            }

            // Create payment record
            $payment = Payment::create([
                'enrollment_id' => $enrollment->enrollment_id,
                'student_id' => $student->student_id,
                'program_id' => $request->program_id,
                'package_id' => $enrollment->package_id,
                'payment_method' => $request->payment_method,
                'amount' => $this->calculateAmount($enrollment),
                'payment_status' => 'pending',
                'payment_details' => json_encode($this->getPaymentDetails($request)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update enrollment payment status
            $enrollment->update([
                'payment_status' => 'pending_verification'
            ]);

            DB::commit();

            return redirect()->route('student.dashboard')->with('success', 
                'Payment submitted successfully! Please wait for admin verification.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    private function calculateAmount($enrollment)
    {
        // Get package amount
        $package = $enrollment->package;
        return $package ? $package->package_price : 0;
    }

    private function getPaymentDetails($request)
    {
        $details = [
            'payment_method' => $request->payment_method,
            'timestamp' => now()->toISOString(),
        ];

        switch ($request->payment_method) {
            case 'credit_card':
                $details['card_number'] = '**** **** **** ' . substr($request->card_number, -4);
                $details['cardholder_name'] = $request->cardholder_name;
                break;
            case 'gcash':
                $details['gcash_number'] = $request->gcash_number;
                break;
            case 'bank_transfer':
                $details['reference_number'] = $request->reference_number;
                break;
            case 'installment':
                $details['installment_plan'] = $request->installment_plan . ' months';
                break;
        }

        return $details;
    }

    public function paymentHistory()
    {
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student record not found.');
        }

        $payments = Payment::where('student_id', $student->student_id)
            ->with(['enrollment.program', 'enrollment.package'])
            ->orderBy('created_at', 'desc')
            ->get();

        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];

        return view('student.payment-history', compact('user', 'payments'));
    }
}
