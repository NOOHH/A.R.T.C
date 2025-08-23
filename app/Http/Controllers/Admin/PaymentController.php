<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AdminPreviewCustomization;
use Illuminate\Http\Request;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    use AdminPreviewCustomization;
    /**
     * Display pending payments.
     */
    public function pending()
    {
        $payments = Payment::where('payment_status', 'pending')
                            ->orderBy('created_at', 'desc')
                            ->get();
        return view('admin.payments.pending', compact('payments'));
    }

    /**
     * Display payment history for completed/processed payments.
     */
    public function history()
    {
        $payments = Payment::whereIn('payment_status', ['paid', 'failed', 'cancelled'])
                            ->orderBy('updated_at', 'desc')
                            ->get();
        return view('admin.payments.history', compact('payments'));
    }

    /**
     * Show details for a single payment.
     */
    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Approve a pending payment.
     */
    public function approve(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->payment_status = 'paid';
        $payment->verified_by = Auth::id() ?? 1;
        $payment->verified_at = Carbon::now();
        $payment->save();

        // Also update the related enrollment's payment_status to 'paid'
        if ($payment->enrollment) {
            $payment->enrollment->payment_status = 'paid';
            $payment->enrollment->save();
        }

        // Always return JSON for now
        return response()->json([
            'success' => true,
            'message' => 'Payment approved successfully.'
        ]);
    }

    /**
     * Reject a pending payment.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
            'fields' => 'nullable|array'
        ]);

        $payment = Payment::findOrFail($id);
        $payment->payment_status = 'rejected';
        $payment->rejection_reason = $request->input('reason');
        $payment->rejected_fields = $request->input('fields', []);
        $payment->rejected_at = Carbon::now();
        $payment->rejected_by = Auth::id() ?? 1;
        $payment->save();

        return redirect()->route('admin.payments.pending')->with('success', 'Payment rejected successfully.');
    }

    /**
     * Download the payment proof file.
     */
    public function downloadProof($id)
    {
        $payment = Payment::findOrFail($id);
        $details = $payment->payment_details;
        if (isset($details['payment_proof_path'])) {
            $path = storage_path('app/public/' . $details['payment_proof_path']);
            return response()->download($path);
        }
        abort(404);
    }

    /**
     * Get detailed payment information
     */
    public function details($id)
    {
        try {
            $payment = Payment::with(['enrollment.registration.user'])->findOrFail($id);
            
            // Parse payment details if JSON
            $paymentDetails = $payment->payment_details;
            if (is_string($paymentDetails)) {
                $paymentDetails = json_decode($paymentDetails, true);
            }
            
            $response = [
                'success' => true,
                'payment' => [
                    'payment_id' => $payment->payment_id,
                    'enrollment_id' => $payment->enrollment_id,
                    'student_id' => $payment->student_id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status,
                    'reference_number' => $payment->reference_number ?? ($paymentDetails['reference_number'] ?? null),
                    'transaction_date' => $paymentDetails['transaction_date'] ?? null,
                    'payment_proof' => isset($paymentDetails['payment_proof_path']) 
                        ? asset('storage/' . $paymentDetails['payment_proof_path']) 
                        : null,
                    'rejection_reason' => $payment->rejection_reason,
                    'rejected_fields' => $payment->rejected_fields,
                    'rejected_at' => $payment->rejected_at,
                    'created_at' => $payment->created_at,
                    'notes' => $payment->notes
                ]
            ];
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading payment details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get original payment data (for comparison with resubmissions)
     */
    public function originalData($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            // For resubmitted payments, we might need to find the original rejected version
            // For now, just return the current payment data
            return $this->details($id);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading original payment data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a resubmitted payment
     */
    public function approveResubmission(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->payment_status = 'paid';
        $payment->verified_by = Auth::id() ?? 1;
        $payment->verified_at = Carbon::now();
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment resubmission approved successfully.'
        ]);
    }

    /**
     * Update rejection details
     */
    public function updateRejection(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
            'rejected_fields' => 'nullable|array'
        ]);

        $payment = Payment::findOrFail($id);
        $payment->rejection_reason = $request->input('reason');
        $payment->rejected_fields = json_encode($request->input('rejected_fields', []));
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment rejection details updated successfully.'
        ]);
    }

    /**
     * Undo payment approval (set payment and enrollment status back to pending)
     */
    public function undoApproval(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->payment_status = 'pending';
            $payment->verified_by = null;
            $payment->verified_at = null;
            $payment->save();

            // Also update the related enrollment's payment_status to 'pending'
            if ($payment->enrollment) {
                $payment->enrollment->payment_status = 'pending';
                $payment->enrollment->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment approval undone. Status set back to pending.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error undoing payment approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary statistics for payments.
     */
    public function getStats()
    {
        $stats = [
            'pending'     => Payment::where('payment_status', 'pending')->count(),
            'paid'        => Payment::where('payment_status', 'paid')->count(),
            'rejected'    => Payment::where('payment_status', 'rejected')->count(),
            'resubmitted' => Payment::where('payment_status', 'resubmitted')->count(),
        ];
        return response()->json($stats);
    }

    /**
     * Preview mode for tenant preview system - Payments
     */
    public function previewPending($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock payments data
            $paymentsCollection = $this->generateMockData('payments');
            view()->share('payments', $paymentsCollection);
            view()->share('isPreviewMode', true);

            $html = view('admin.payments.pending', [
                'payments' => $paymentsCollection,
                'isPreview' => true
            ])->render();

            return response($html);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin payments preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>Admin Payments Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Admin Payments Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        } finally {
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
        }
    }
}
