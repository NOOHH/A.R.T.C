<?php

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "tenant" middleware group. Make something great!
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\ModularRegistrationController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;

// Tenant-aware enrollment routes
Route::get('/enrollment/full', [StudentRegistrationController::class, 'showRegistrationForm'])->name('enrollment.full');
Route::get('/enrollment/modular', [ModularRegistrationController::class, 'showForm'])->name('enrollment.modular');
Route::post('/enrollment/modular/submit', [ModularRegistrationController::class, 'submitEnrollment'])->name('enrollment.modular.submit');
Route::post('/enrollment/modular/validate', [ModularRegistrationController::class, 'validateStep'])->name('enrollment.modular.validate');
Route::post('/enrollment/send-otp', [StudentRegistrationController::class, 'sendEnrollmentOTP'])->name('enrollment.send-otp');
Route::post('/enrollment/verify-otp', [StudentRegistrationController::class, 'verifyEnrollmentOTP'])->name('enrollment.verify-otp');
Route::post('/enrollment/validate-referral', [StudentRegistrationController::class, 'validateEnrollmentReferral'])->name('enrollment.validate-referral');
Route::post('/enrollment/check-email', [StudentRegistrationController::class, 'checkEmailAvailability'])->name('enrollment.check-email');
Route::post('/enrollment/create-auto-batch', [StudentRegistrationController::class, 'createAutoBatchPublic'])->name('enrollment.create-auto-batch');
Route::get('/student/payment/enrollment/{id}/details', [App\Http\Controllers\StudentPaymentController::class, 'getEnrollmentDetails'])->name('student.payment.enrollment.details');
Route::get('/student/enrollment/{id}/rejection-details', [StudentController::class, 'getRejectionDetails'])->name('student.enrollment.rejection-details');
Route::get('/student/enrollment/{id}/edit-form', [StudentController::class, 'getEditForm'])->name('student.enrollment.edit-form');
Route::put('/student/enrollment/{id}/resubmit', [StudentController::class, 'resubmitRegistration'])->name('student.enrollment.resubmit');
Route::delete('/student/enrollment/{id}/delete', [StudentController::class, 'deleteRegistration'])->name('student.enrollment.delete');
Route::get('/admin/student/enrollment/{id}/details', [AdminController::class, 'getEnrollmentDetailsJson']);
Route::get('/admin/enrollment/{id}/details', [AdminController::class, 'getEnrollmentDetailsJson']);
Route::post('/admin/enrollment/assign', [AdminController::class, 'assignEnrollment']);
Route::post('/admin/enrollment/{id}/mark-paid', [AdminController::class, 'markAsPaid']);
Route::get('/admin/enrollment/{id}/payment-details', [AdminController::class, 'getPaymentDetailsByEnrollment']);
Route::post('/admin/enrollment/{id}/mark-paid', [AdminController::class, 'markAsPaid']);
Route::post('/admin/enrollment/{id}/approve', [AdminController::class, 'approveEnrollment']);
Route::post('/admin/enrollment/{enrollmentId}/undo-payment', [App\Http\Controllers\AdminController::class, 'undoPendingPayment'])->name('admin.enrollment.undo-payment');
