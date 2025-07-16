-- Referral System Database Updates
-- Execute this script to add referral functionality

-- 1. Add referral_code to directors table
ALTER TABLE `directors` ADD COLUMN `referral_code` VARCHAR(20) UNIQUE DEFAULT NULL AFTER `directors_password`;

-- 2. Add referral_code to professors table  
ALTER TABLE `professors` ADD COLUMN `referral_code` VARCHAR(20) UNIQUE DEFAULT NULL AFTER `professor_password`;

-- 3. Create referrals tracking table
CREATE TABLE IF NOT EXISTS `referrals` (
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

-- 4. Add referral_code to registrations table (skip if column already exists)
-- Note: There's already a 'Referrals' column, so we'll add 'referral_code' as well
ALTER TABLE `registrations` ADD COLUMN `referral_code` VARCHAR(20) DEFAULT NULL;

-- 5. Insert referral admin settings
INSERT IGNORE INTO `admin_settings` (`setting_id`, `setting_key`, `setting_value`, `setting_description`, `is_active`) VALUES
(100, 'referral_enabled', '1', 'Enable/disable referral code field in registration', 1),
(101, 'referral_required', '0', 'Make referral code required in registration', 1);

-- 6. Update auto_increment for admin_settings if needed
SELECT MAX(setting_id) + 1 as next_id FROM admin_settings;
