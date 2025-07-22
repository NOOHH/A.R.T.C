// Registration API routes
Route::get('/admin/registrations/{id}/details', [AdminController::class, 'getRegistrationDetails'])
     ->name('admin.registrations.details');
Route::get('/admin/registrations/{id}/original-data', [AdminController::class, 'getOriginalRegistrationData'])
     ->name('admin.registrations.original-data');
Route::post('/admin/registrations/{id}/approve', [AdminController::class, 'approveRegistration'])
     ->name('admin.registrations.approve');
Route::post('/admin/registrations/{id}/approve-resubmission', [AdminController::class, 'approveRegistrationResubmission'])
     ->name('admin.registrations.approve-resubmission');
Route::post('/admin/registrations/{id}/update-rejection', [AdminController::class, 'updateRegistrationRejection'])
     ->name('admin.registrations.update-rejection');

// Payment API routes
Route::get('/admin/payments/{id}/details', [AdminController::class, 'getPaymentDetails'])
     ->name('admin.payments.details');
Route::get('/admin/payments/{id}/original-data', [AdminController::class, 'getOriginalPaymentData'])
     ->name('admin.payments.original-data');
Route::post('/admin/payments/{id}/approve', [AdminController::class, 'approvePayment'])
     ->name('admin.payments.approve');
Route::post('/admin/payments/{id}/approve-resubmission', [AdminController::class, 'approvePaymentResubmission'])
     ->name('admin.payments.approve-resubmission');
Route::post('/admin/payments/{id}/update-rejection', [AdminController::class, 'updatePaymentRejection'])
     ->name('admin.payments.update-rejection');
