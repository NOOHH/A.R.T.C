# Payment Data Encryption Implementation

## Overview
This document outlines the encryption implementation for sensitive payment data in the A.R.T.C application to ensure payment screenshot uploads and related data are securely stored in the database.

## Encrypted Fields

### Payment Model (`payments` table)
The following fields are now encrypted using Laravel's `Crypt` facade:

1. **`reference_number`** - Payment reference numbers from payment providers
2. **`notes`** - Payment notes that may contain sensitive information
3. **`rejection_reason`** - Rejection reasons that may contain sensitive details
4. **`payment_details`** (JSON field) - Contains multiple encrypted sub-fields:
   - `payment_proof_path` - File path to uploaded payment screenshots
   - `payment_method_name` - Payment method names
   - `reference_number` - Backup reference number in payment details
   - `transaction_id` - External transaction IDs
   - `qr_code_data` - QR code data for payments
   - `qr_code_path` - QR code file paths

## Implementation Details

### Automatic Encryption/Decryption
- **Encryption**: Data is automatically encrypted when setting values using Laravel mutators
- **Decryption**: Data is automatically decrypted when retrieving values using Laravel accessors
- **Error Handling**: If decryption fails, the system returns `null` instead of crashing

### File Encryption
Payment proof screenshots uploaded by students are:
1. Stored in the `storage/app/public/payment_proofs/` directory
2. The file path is encrypted in the database
3. File contents remain as images (not encrypted) for admin viewing
4. Only the database reference to the file path is encrypted

### Backward Compatibility
- The system checks if data is already encrypted before applying encryption
- Existing unencrypted data is migrated during the migration process
- Mixed encrypted/unencrypted data is handled gracefully

## Security Benefits

1. **Data Protection**: Sensitive payment information is protected even if database access is compromised
2. **Compliance**: Helps meet data protection requirements for financial information
3. **Privacy**: Student payment details are kept confidential
4. **Audit Trail**: Encryption doesn't affect functionality while adding security layer

## Usage

### Creating Payment Records
```php
// Payment data is automatically encrypted when saving
$payment = Payment::create([
    'reference_number' => '123456789',
    'notes' => 'Payment uploaded by student',
    'payment_details' => [
        'payment_proof_path' => 'payment_proofs/screenshot.jpg',
        'payment_method_name' => 'GCash'
    ]
]);
```

### Retrieving Payment Data
```php
// Data is automatically decrypted when accessing
$payment = Payment::find(1);
echo $payment->reference_number; // Automatically decrypted
echo $payment->payment_details['payment_proof_path']; // Automatically decrypted
```

### Admin Views
Admin interfaces continue to work normally as decryption is handled transparently:
- Payment pending approval pages show decrypted data
- Payment proof images can be viewed normally
- Reference numbers are displayed in plain text to admins

## Migration

### Running the Encryption Migration
```bash
php artisan migrate
```

The migration `2025_08_04_000000_encrypt_existing_payment_data.php` will:
1. Encrypt all existing unencrypted payment data
2. Skip already encrypted data to prevent double encryption
3. Handle errors gracefully without data loss

### Rollback Considerations
- Rolling back encryption requires the same Laravel app key
- Always backup your database before running encryption migrations
- Test the migration in a development environment first

## Files Modified

1. **`app/Models/Payment.php`**
   - Added encryption mutators and accessors
   - Enhanced payment_details encryption coverage

2. **`database/migrations/2025_08_04_000000_encrypt_existing_payment_data.php`**
   - Migration to encrypt existing payment data
   - Includes rollback functionality

## Environment Requirements

- Laravel application key must be properly set (`APP_KEY` in `.env`)
- Same app key required for encryption and decryption
- Backup app key securely for data recovery

## Testing

### Verify Encryption
1. Upload a payment screenshot through student dashboard
2. Check database directly - payment_details should contain encrypted data
3. View payment in admin panel - data should display normally (decrypted)
4. Verify file path encryption doesn't break image viewing

### Test Cases
- New payment uploads with encryption
- Existing payment data migration
- Admin panel functionality with encrypted data
- Payment approval/rejection with encrypted data

## Troubleshooting

### Common Issues
1. **Decryption errors**: Usually caused by app key changes
2. **Double encryption**: Migration handles this automatically
3. **File access issues**: Only paths are encrypted, not file contents

### Recovery
- If app key is lost, encrypted data cannot be recovered
- Always maintain secure backups of both database and app key
- Consider key rotation strategies for enhanced security

## Future Enhancements

1. **Field-level encryption keys**: Different keys for different data types
2. **Encryption versioning**: Track encryption method versions
3. **Audit logging**: Log encryption/decryption events
4. **Performance optimization**: Cache decrypted data where appropriate

## Compliance Notes

This implementation helps with:
- PCI DSS compliance for payment data
- Data privacy regulations
- Internal security policies
- Audit requirements for financial data

## Support

For issues related to payment encryption:
1. Check Laravel logs for encryption/decryption errors
2. Verify APP_KEY is properly set and consistent
3. Ensure migration completed successfully
4. Test with fresh payment uploads to verify encryption is working
