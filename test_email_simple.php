<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;

try {
    echo "Current mail configuration:\n";
    echo "MAIL_MAILER: " . config('mail.default') . "\n";
    echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
    echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
    echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
    echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";
    
    // Try to send a test email
    $testEmail = 'vince03handsome11@gmail.com';
    $resetUrl = 'http://localhost/A.R.T.C/public/change-password?token=test123';
    
    echo "Attempting to send email to: $testEmail\n";
    
    Mail::to($testEmail)->send(new PasswordResetMail($resetUrl, $testEmail, 'Test User'));
    
    echo "Email sent successfully! Check your email or MailHog interface.\n";
    echo "Note: If using MailHog, emails won't reach real addresses.\n";
    echo "MailHog interface: http://localhost:8025\n";
    
} catch (Exception $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
