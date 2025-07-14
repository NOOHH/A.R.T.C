<?php

namespace App\Http\Controllers;

use App\Services\MayaPaymentService;
use App\Models\PaymentMethod;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    private $mayaService;

    public function __construct(MayaPaymentService $mayaService)
    {
        $this->mayaService = $mayaService;
    }

    /**
     * Process payment method selection and create payment
     */
    public function processPayment(Request $request)
    {
        try {
            $request->validate([
                'payment_method_id' => 'required|exists:payment_methods,id',
                'amount' => 'required|numeric|min:1',
                'student_id' => 'required'
            ]);

            $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);
            $amount = $request->amount;
            $studentId = $request->student_id;

            // For now, handle Maya/GCash payments
            if (in_array(strtolower($paymentMethod->name), ['maya', 'gcash', 'paymaya'])) {
                $result = $this->mayaService->createPaymentLink(
                    $amount,
                    'ARTC Enrollment Fee',
                    $studentId,
                    [
                        'success' => route('payment.success'),
                        'failure' => route('payment.failure'),
                        'cancel' => route('payment.cancel')
                    ]
                );

                if ($result['success']) {
                    // Store payment details in session
                    Session::put('payment_data', [
                        'checkout_id' => $result['checkout_id'],
                        'reference_number' => $result['reference_number'],
                        'amount' => $amount,
                        'student_id' => $studentId,
                        'payment_method_id' => $request->payment_method_id
                    ]);

                    return response()->json([
                        'success' => true,
                        'redirect_url' => $result['redirect_url'],
                        'message' => 'Redirecting to payment gateway...'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => $result['error']
                    ]);
                }
            }

            // Handle QR Code payments
            if ($request->payment_type === 'qr_code') {
                $qrResult = $this->mayaService->createQRCode($amount, 'ARTC Enrollment Fee', $studentId);
                
                if ($qrResult['success']) {
                    return response()->json([
                        'success' => true,
                        'qr_code_url' => $qrResult['qr_code_url'],
                        'qr_code_id' => $qrResult['qr_code_id'],
                        'reference_number' => $qrResult['reference_number']
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => $qrResult['error']
                    ]);
                }
            }

            // Handle other payment methods (manual verification)
            return response()->json([
                'success' => true,
                'manual_verification' => true,
                'payment_method' => $paymentMethod->name,
                'qr_code_path' => $paymentMethod->qr_code_path,
                'instructions' => $this->getPaymentInstructions($paymentMethod)
            ]);

        } catch (\Exception $e) {
            Log::error('Payment processing error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Payment processing failed. Please try again.'
            ]);
        }
    }

    /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request)
    {
        $paymentData = Session::get('payment_data');
        
        if ($paymentData) {
            // Verify payment with Maya
            $verification = $this->mayaService->verifyPayment($paymentData['checkout_id']);
            
            if ($verification['success'] && $verification['status'] === 'COMPLETED') {
                // Payment successful - update student status, create payment record, etc.
                Log::info('Payment completed successfully', $paymentData);
                
                // Clear session data
                Session::forget('payment_data');
                
                return view('student.payment-success', [
                    'reference_number' => $paymentData['reference_number'],
                    'amount' => $paymentData['amount']
                ]);
            }
        }
        
        return redirect()->route('student.paywall')->with('error', 'Payment verification failed');
    }

    /**
     * Handle failed payment
     */
    public function paymentFailure(Request $request)
    {
        $paymentData = Session::get('payment_data');
        Session::forget('payment_data');
        
        Log::warning('Payment failed', ['payment_data' => $paymentData]);
        
        return redirect()->route('student.paywall')->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Handle cancelled payment
     */
    public function paymentCancel(Request $request)
    {
        Session::forget('payment_data');
        
        return redirect()->route('student.paywall')->with('info', 'Payment cancelled.');
    }

    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(Request $request)
    {
        try {
            $request->validate([
                'payment_proof' => 'required|image|mimes:jpeg,jpg,png|max:5120',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'student_id' => 'required'
            ]);

            $file = $request->file('payment_proof');
            $filename = 'payment_proof_' . $request->student_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payment_proofs', $filename, 'public');

            // Here you would typically create a payment record for admin verification
            
            return response()->json([
                'success' => true,
                'message' => 'Payment proof uploaded successfully. Your payment will be verified within 24 hours.',
                'file_path' => $path
            ]);

        } catch (\Exception $e) {
            Log::error('Payment proof upload error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to upload payment proof. Please try again.'
            ]);
        }
    }

    /**
     * Get payment instructions for manual methods
     */
    private function getPaymentInstructions($paymentMethod)
    {
        $instructions = [
            'gcash' => 'Scan the QR code with your GCash app and complete the payment. Upload a screenshot of the payment confirmation.',
            'maya' => 'Scan the QR code with your Maya app and complete the payment. Upload a screenshot of the payment confirmation.',
            'bank_transfer' => 'Transfer the exact amount to the account shown. Upload a photo of the deposit slip or transfer confirmation.',
            'cash' => 'Pay the exact amount at our office during business hours. Upload a photo of the official receipt.'
        ];

        $methodKey = strtolower(str_replace(' ', '_', $paymentMethod->name));
        return $instructions[$methodKey] ?? 'Complete your payment using ' . $paymentMethod->name . ' and upload proof of payment.';
    }
}
