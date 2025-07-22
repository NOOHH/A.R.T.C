<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display pending payments
     */
    public function pending()
    {
        $payments = Payment::with(['enrollment.student', 'enrollment.program', 'enrollment.package'])
            ->where('payment_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.payments.pending', compact('payments'));
    }
    
    /**
     * Show specific payment details
     */
    public function show($id)
    {
        $payment = Payment::with(['enrollment.student', 'enrollment.program', 'enrollment.package'])
            ->findOrFail($id);
            
        // Decode payment details JSON
        $paymentDetails = json_decode($payment->payment_details, true) ?? [];
        
        return view('admin.payments.show', compact('payment', 'paymentDetails'));
    }
    
    /**
     * Approve payment
     */
    public function approve(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            $payment->update([
                'payment_status' => 'paid',
                'verified_by' => Auth::guard('admin')->id(),
                'verified_at' => now(),
                'receipt_number' => $request->receipt_number ?? 'ADM-' . time(),
                'notes' => $request->notes
            ]);
            
            // Update enrollment status
            $payment->enrollment->update([
                'enrollment_status' => 'approved',
                'payment_status' => 'paid'
            ]);
            
            return redirect()->route('admin.payments.pending')
                ->with('success', 'Payment approved successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve payment: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject payment
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);
        
        try {
            $payment = Payment::findOrFail($id);
            
            $payment->update([
                'payment_status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'rejected_by' => Auth::guard('admin')->id(),
                'rejected_at' => now()
            ]);
            
            // Update enrollment status
            $payment->enrollment->update([
                'enrollment_status' => 'payment_required',
                'payment_status' => 'rejected'
            ]);
            
            return redirect()->route('admin.payments.pending')
                ->with('success', 'Payment rejected. Student will be notified to resubmit.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject payment: ' . $e->getMessage());
        }
    }
    
    /**
     * Download payment proof file
     */
    public function downloadProof($id)
    {
        $payment = Payment::findOrFail($id);
        $paymentDetails = json_decode($payment->payment_details, true) ?? [];
        
        if (isset($paymentDetails['payment_proof_path'])) {
            $filePath = $paymentDetails['payment_proof_path'];
            
            if (Storage::exists($filePath)) {
                return Storage::download($filePath, 'payment_proof_' . $payment->payment_id . '.jpg');
            }
        }
        
        return redirect()->back()->with('error', 'Payment proof file not found.');
    }
    
    /**
     * Get payment history
     */
    public function history()
    {
        $payments = Payment::with(['enrollment.student', 'enrollment.program', 'enrollment.package'])
            ->whereIn('payment_status', ['paid', 'rejected', 'failed'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
            
        return view('admin.payments.history', compact('payments'));
    }
    
    /**
     * API: Get payment statistics
     */
    public function getStats()
    {
        $stats = [
            'pending' => Payment::where('payment_status', 'pending')->count(),
            'approved' => Payment::where('payment_status', 'paid')->count(),
            'rejected' => Payment::where('payment_status', 'rejected')->count(),
            'total_amount_pending' => Payment::where('payment_status', 'pending')->sum('amount'),
            'total_amount_approved' => Payment::where('payment_status', 'paid')->sum('amount')
        ];
        
        return response()->json($stats);
    }
}
