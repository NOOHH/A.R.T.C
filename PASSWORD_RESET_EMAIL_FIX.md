# Password Reset Email Fix Guide

## Problem
The password reset email functionality is not working - emails are not being sent when users request password reset.

## Root Cause Analysis
The issue is likely due to missing or incorrect email configuration in the `.env` file.

## Solution Steps

### 1. Create/Update .env File
Create a `.env` file in your project root with the following email configuration:

```env
# Email Configuration for Gmail SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="A.R.T.C"
```

### 2. Gmail Setup Instructions
To use Gmail for sending emails:

1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate an App Password**:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate a new app password for "Mail"
   - Use this password as `MAIL_PASSWORD` in your .env file

### 3. Alternative Email Services
If you prefer other email services:

#### For Outlook/Hotmail:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@outlook.com
MAIL_FROM_NAME="A.R.T.C"
```

#### For Yahoo:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yahoo.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@yahoo.com
MAIL_FROM_NAME="A.R.T.C"
```

### 4. Clear Laravel Cache
After updating the .env file, clear Laravel's cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 5. Test Email Configuration
Run the email configuration test:

```bash
php check_email_config.php
```

### 6. Debug Steps
If emails still don't work:

1. **Check Laravel Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verify Email Exists in Database**:
   - Check if the email exists in any of these tables:
     - `students` (email column)
     - `admins` (email column)
     - `professors` (professor_email column)
     - `directors` (directors_email column)

3. **Test SMTP Connection**:
   ```bash
   php test_smtp_connection.php
   ```

### 7. Common Issues and Solutions

#### Issue: "Connection refused" or "Authentication failed"
- **Solution**: Check your email credentials and ensure 2FA is enabled with app password

#### Issue: "Email not found in database"
- **Solution**: Verify the email exists in one of the user tables

#### Issue: "Mailer not configured"
- **Solution**: Ensure all MAIL_* variables are set in .env file

### 8. Security Notes
- Never commit your .env file to version control
- Use app passwords instead of regular passwords for Gmail
- The system logs all password reset attempts for security

### 9. Testing the Fix
1. Go to `/password/reset` page
2. Enter your email address
3. Check your email for the reset link
4. Check Laravel logs for any errors

### 10. Additional Improvements Made
- Enhanced logging in `UnifiedLoginController::sendResetLinkEmail()`
- Better error handling and debugging information
- Comprehensive email configuration testing

## Files Modified
- `app/Http/Controllers/UnifiedLoginController.php` - Enhanced logging
- `check_email_config.php` - Email configuration test script
- `PASSWORD_RESET_EMAIL_FIX.md` - This guide

## Next Steps
1. Create/update your .env file with proper email configuration
2. Test the email functionality
3. Monitor logs for any remaining issues
4. Consider implementing email templates for better user experience 