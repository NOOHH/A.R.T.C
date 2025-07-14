<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MayaPaymentService
{
    private $publicKey;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->publicKey = env('MAYA_PUBLIC_KEY', 'pk-MwYtykWe07JtvakYe0SPkYrA4pkb9IlkB8QsZlsslNT');
        $this->secretKey = env('MAYA_SECRET_KEY', ''); // Will be set in .env
        $this->baseUrl = env('MAYA_BASE_URL', 'https://pg-sandbox.paymaya.com');
    }

    /**
     * Create a Maya payment link
     */
    public function createPaymentLink($amount, $description, $studentId, $redirectUrls = [])
    {
        try {
            $payload = [
                'totalAmount' => [
                    'value' => $amount * 100, // Convert to centavos
                    'currency' => 'PHP'
                ],
                'buyer' => [
                    'firstName' => 'Student',
                    'lastName' => 'User',
                    'contact' => [
                        'email' => 'student@example.com'
                    ]
                ],
                'items' => [
                    [
                        'name' => $description,
                        'quantity' => 1,
                        'code' => 'ENROLLMENT_FEE',
                        'description' => $description,
                        'amount' => [
                            'value' => $amount * 100,
                            'currency' => 'PHP'
                        ],
                        'totalAmount' => [
                            'value' => $amount * 100,
                            'currency' => 'PHP'
                        ]
                    ]
                ],
                'redirectUrl' => [
                    'success' => $redirectUrls['success'] ?? route('payment.success'),
                    'failure' => $redirectUrls['failure'] ?? route('payment.failure'),
                    'cancel' => $redirectUrls['cancel'] ?? route('payment.cancel')
                ],
                'requestReferenceNumber' => 'ARTC_' . $studentId . '_' . time(),
                'metadata' => [
                    'student_id' => $studentId,
                    'payment_type' => 'enrollment_fee'
                ]
            ];

            $response = Http::withBasicAuth($this->publicKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($this->baseUrl . '/checkout/v1/checkouts', $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Maya payment link created successfully', ['response' => $data]);
                return [
                    'success' => true,
                    'checkout_id' => $data['checkoutId'],
                    'redirect_url' => $data['redirectUrl'],
                    'reference_number' => $payload['requestReferenceNumber']
                ];
            } else {
                Log::error('Maya payment link creation failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [
                    'success' => false,
                    'error' => 'Failed to create payment link: ' . $response->body()
                ];
            }
        } catch (Exception $e) {
            Log::error('Maya payment service error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Payment service temporarily unavailable: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment($checkoutId)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get($this->baseUrl . '/checkout/v1/checkouts/' . $checkoutId);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'unknown',
                    'payment_data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to verify payment'
                ];
            }
        } catch (Exception $e) {
            Log::error('Maya payment verification error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Payment verification failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create QR Code for Maya payment
     */
    public function createQRCode($amount, $description, $studentId)
    {
        try {
            $payload = [
                'amount' => [
                    'value' => $amount * 100,
                    'currency' => 'PHP'
                ],
                'redirectUrl' => [
                    'success' => route('payment.success'),
                    'failure' => route('payment.failure'),
                    'cancel' => route('payment.cancel')
                ],
                'requestReferenceNumber' => 'ARTC_QR_' . $studentId . '_' . time(),
                'metadata' => [
                    'student_id' => $studentId,
                    'payment_type' => 'qr_payment'
                ]
            ];

            $response = Http::withBasicAuth($this->publicKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($this->baseUrl . '/qrcode/v1/qrcodes', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'qr_code_url' => $data['qrCodeUrl'],
                    'qr_code_id' => $data['qrCodeId'],
                    'reference_number' => $payload['requestReferenceNumber']
                ];
            } else {
                Log::error('Maya QR code creation failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [
                    'success' => false,
                    'error' => 'Failed to create QR code'
                ];
            }
        } catch (Exception $e) {
            Log::error('Maya QR code service error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'QR code service temporarily unavailable'
            ];
        }
    }
}
