# Referral System Implementation

This document outlines the complete implementation of the referral system for the A.R.T.C application.

## Database Changes

### 1. Add referral_code column to directors and professors tables

```sql
-- Add referral_code to directors table
ALTER TABLE `directors` ADD COLUMN `referral_code` VARCHAR(20) UNIQUE DEFAULT NULL AFTER `directors_password`;

-- Add referral_code to professors table  
ALTER TABLE `professors` ADD COLUMN `referral_code` VARCHAR(20) UNIQUE DEFAULT NULL AFTER `professor_password`;
```

### 2. Create referrals table for tracking usage

```sql
CREATE TABLE `referrals` (
  `referral_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `referral_code` varchar(20) NOT NULL,
  `referrer_type` enum('director','professor') NOT NULL,
  `referrer_id` int(11) NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `registration_id` int(11) unsigned NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`referral_id`),
  UNIQUE KEY `unique_student_referral` (`student_id`),
  KEY `idx_referral_code` (`referral_code`),
  KEY `idx_referrer` (`referrer_type`, `referrer_id`),
  KEY `idx_student_registration` (`student_id`, `registration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Add referral_code column to registrations table

```sql
-- Add referral_code to registrations table to track which code was used
ALTER TABLE `registrations` ADD COLUMN `referral_code` VARCHAR(20) DEFAULT NULL AFTER `payment_status`;
```

### 4. Update admin_settings for referral configuration

```sql
-- Insert referral settings
INSERT INTO `admin_settings` (`setting_id`, `setting_key`, `setting_value`, `setting_description`, `is_active`) VALUES
(100, 'referral_enabled', '1', 'Enable/disable referral code field in registration', 1),
(101, 'referral_required', '0', 'Make referral code required in registration', 1);
```

## Implementation Steps

### Phase 1: Database Setup
- [x] Add referral_code columns to directors and professors tables
- [x] Create referrals tracking table
- [x] Add referral_code to registrations table
- [x] Add admin settings for referral configuration

### Phase 2: Backend Implementation
- [ ] Create referral code generation utility
- [ ] Update Director and Professor models
- [ ] Create Referral model
- [ ] Update registration controllers
- [ ] Create referral analytics

### Phase 3: Admin Interface Updates
- [ ] Add referral fields to director/professor creation forms
- [ ] Update admin layout for director access restrictions
- [ ] Create referral analytics page
- [ ] Update admin settings page

### Phase 4: User Registration Updates
- [ ] Add referral field to Full_enrollment.blade.php
- [ ] Add referral field to signup.blade.php
- [ ] Implement referral validation

### Phase 5: Director Access Restrictions
- [ ] Implement role-based sidebar visibility
- [ ] Create director-specific navigation
- [ ] Add authorization middleware

## Director Access Permissions

Directors will have access to:
- Dashboard
- All Registration (pending, history, payment pending, payment history, batch enroll, assign course)
- Accounts: Students & Professors only (no Directors access)
- Programs: Manage Programs & Manage Modules only (no Packages)
- All Analytics except Referrals (will be added later)
- Chat logs
- FAQ Management  
- Reports: Student Reports, Professor Reports, Enrollment Reports only

Directors will NOT have access to:
- Settings
- Directors management
- Packages management
- Financial Reports
- Referrals Analytics (until later implementation)
