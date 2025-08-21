<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification - ASCENDO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #b71c1c 0%, #e53935 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .card-header {
            background: linear-gradient(135deg, #b71c1c, #e53935);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .verification-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .card-body {
            padding: 2rem;
        }
        .info-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            margin-bottom: 0.5rem;
        }
        .info-value {
            color: #333;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="verification-card">
                    <div class="card-header">
                        <div class="verification-icon">
                            <?php if($valid): ?>
                                <i class="bi bi-check-circle-fill text-success"></i>
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill text-danger"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="mb-0">Certificate Verification</h3>
                        <p class="mb-0 opacity-75">ASCENDO Review and Training Center</p>
                    </div>
                    <div class="card-body">
                        <?php if($valid): ?>
                            <div class="alert alert-success border-0" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Certificate Verified Successfully!</strong>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Student Name</div>
                                <div class="info-value"><?php echo e($student_name); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Program</div>
                                <div class="info-value"><?php echo e($program); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Completion Date</div>
                                <div class="info-value"><?php echo e($completion_date); ?></div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>
                                    This certificate has been digitally verified and is authentic.
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger border-0" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Certificate Verification Failed!</strong>
                            </div>
                            
                            <p class="text-center text-muted">
                                <?php echo e($message ?? 'This certificate could not be verified. Please contact ASCENDO Review and Training Center for assistance.'); ?>

                            </p>
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <button onclick="window.close()" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\certificates\verify.blade.php ENDPATH**/ ?>