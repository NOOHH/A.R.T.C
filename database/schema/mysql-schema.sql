/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `activity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `professor_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `max_points` int(11) NOT NULL DEFAULT 100,
  `due_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `admin_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_settings` (
  `setting_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `announcement_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `professor_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('general','video','assignment','quiz') NOT NULL DEFAULT 'general',
  `video_link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assignment_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignment_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `module_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`files`)),
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('submitted','graded','returned') NOT NULL DEFAULT 'submitted',
  `grade` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `graded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignments` (
  `assignment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `professor_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `max_points` int(11) NOT NULL DEFAULT 100,
  `due_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`assignment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance` (
  `id` bigint(20) unsigned NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `program_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance` (`student_id`,`program_id`,`attendance_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chats` (
  `chat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `receiver_id` bigint(20) unsigned NOT NULL,
  `body_cipher` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`chat_id`),
  KEY `chats_sender_id_receiver_id_index` (`sender_id`,`receiver_id`),
  KEY `chats_sent_at_index` (`sent_at`),
  KEY `chats_sender_id_receiver_id_sent_at_index` (`sender_id`,`receiver_id`,`sent_at`),
  KEY `chats_receiver_id_read_at_index` (`receiver_id`,`read_at`),
  KEY `chats_conversation_index` (`sender_id`,`receiver_id`,`sent_at`),
  KEY `chats_unread_index` (`receiver_id`,`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `class_meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_meetings` (
  `meeting_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` bigint(20) unsigned NOT NULL,
  `professor_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `meeting_date` datetime NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 60,
  `meeting_url` varchar(255) DEFAULT NULL,
  `attached_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attached_files`)),
  `status` enum('scheduled','ongoing','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_by` int(11) NOT NULL,
  `url_visible_before_meeting` tinyint(1) NOT NULL DEFAULT 0,
  `url_visibility_minutes_before` int(11) NOT NULL DEFAULT 0,
  `actual_start_time` datetime DEFAULT NULL,
  `actual_end_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`meeting_id`),
  KEY `class_meetings_batch_id_meeting_date_index` (`batch_id`,`meeting_date`),
  KEY `class_meetings_professor_id_meeting_date_index` (`professor_id`,`meeting_date`),
  KEY `class_meetings_created_by_foreign` (`created_by`),
  CONSTRAINT `class_meetings_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `student_batches` (`batch_id`) ON DELETE CASCADE,
  CONSTRAINT `class_meetings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE,
  CONSTRAINT `class_meetings_professor_id_foreign` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `content_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_title` varchar(255) NOT NULL,
  `content_description` text DEFAULT NULL,
  `lesson_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `content_type` enum('assignment','quiz','test','link','video','document','lesson') DEFAULT NULL,
  `content_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content_data`)),
  `attachment_path` varchar(255) DEFAULT NULL,
  `max_points` decimal(8,2) DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `time_limit` int(11) DEFAULT NULL,
  `content_order` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_items_lesson_id_foreign` (`lesson_id`),
  KEY `content_items_course_id_foreign` (`course_id`),
  CONSTRAINT `content_items_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`subject_id`) ON DELETE CASCADE,
  CONSTRAINT `content_items_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `subject_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) NOT NULL,
  `subject_description` text DEFAULT NULL,
  `module_id` bigint(20) unsigned NOT NULL,
  `subject_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subject_order` int(11) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `deadlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deadlines` (
  `deadline_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('assignment','quiz','activity','exam') NOT NULL DEFAULT 'assignment',
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `status` enum('pending','completed','overdue') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`deadline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `director_program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `director_program` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `director_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `director_program_director_id_program_id_unique` (`director_id`,`program_id`),
  KEY `director_program_program_id_foreign` (`program_id`),
  CONSTRAINT `director_program_director_id_foreign` FOREIGN KEY (`director_id`) REFERENCES `directors` (`directors_id`) ON DELETE CASCADE,
  CONSTRAINT `director_program_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `directors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `directors` (
  `directors_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `directors_name` varchar(100) NOT NULL,
  `directors_first_name` varchar(100) NOT NULL,
  `directors_last_name` varchar(100) NOT NULL,
  `directors_email` varchar(100) NOT NULL,
  `directors_password` varchar(255) NOT NULL,
  `referral_code` varchar(20) DEFAULT NULL,
  `directors_archived` tinyint(1) DEFAULT 0,
  `has_all_program_access` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`directors_id`),
  UNIQUE KEY `directors_email` (`directors_email`),
  UNIQUE KEY `referral_code` (`referral_code`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `directors_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `education_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `education_levels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `level_name` varchar(255) NOT NULL,
  `file_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`file_requirements`)),
  `available_for_general` tinyint(1) NOT NULL DEFAULT 1,
  `available_for_professional` tinyint(1) NOT NULL DEFAULT 1,
  `available_for_review` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `level_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `education_levels_level_name_unique` (`level_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) unsigned DEFAULT NULL,
  `student_id` varchar(30) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `program_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `enrollment_type` enum('Modular','Full') NOT NULL DEFAULT 'Modular',
  `learning_mode` enum('Synchronous','Asynchronous') NOT NULL DEFAULT 'Synchronous',
  `batch_id` bigint(20) unsigned DEFAULT NULL,
  `individual_start_date` datetime DEFAULT NULL,
  `individual_end_date` datetime DEFAULT NULL,
  `enrollment_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','cancelled') DEFAULT 'pending',
  `batch_access_granted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Grants dashboard access for batch students regardless of enrollment/payment status',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`enrollment_id`),
  KEY `program_id` (`program_id`),
  KEY `package_id` (`package_id`),
  KEY `idx_enrollments_student` (`student_id`),
  KEY `idx_enroll_registration` (`registration_id`),
  KEY `idx_enroll_status` (`enrollment_status`),
  KEY `enrollments_batch_id_foreign` (`batch_id`),
  CONSTRAINT `enrollments_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `student_batches` (`batch_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `form_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_requirements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(255) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_type` enum('text','email','tel','date','file','select','textarea','checkbox','radio','number','section','module_selection') DEFAULT NULL,
  `entity_type` enum('student','professor','admin') NOT NULL DEFAULT 'student',
  `program_type` enum('full','modular','both','all') NOT NULL DEFAULT 'both',
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_bold` tinyint(1) NOT NULL DEFAULT 0,
  `field_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`field_options`)),
  `validation_rules` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `section_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lessons` (
  `lesson_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_name` varchar(255) NOT NULL,
  `lesson_description` text DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `lesson_price` decimal(10,2) DEFAULT NULL,
  `lesson_duration` int(11) DEFAULT NULL,
  `lesson_video_url` varchar(255) DEFAULT NULL,
  `lesson_order` int(11) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `learning_mode` enum('Synchronous','Asynchronous','Both') NOT NULL DEFAULT 'Both',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meeting_attendance_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_attendance_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `status` enum('present','absent','late') NOT NULL,
  `joined_at` timestamp NULL DEFAULT NULL,
  `left_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meeting_attendance_logs_meeting_id_index` (`meeting_id`),
  KEY `meeting_attendance_logs_student_id_index` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `sender_type` varchar(50) NOT NULL,
  `receiver_id` bigint(20) unsigned NOT NULL,
  `receiver_type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `content` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `module_completions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_completions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` varchar(30) NOT NULL,
  `program_id` int(11) NOT NULL,
  `module_id` int(11) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_mc_student_program` (`student_id`,`program_id`),
  KEY `idx_mc_module` (`module_id`),
  KEY `fk_mc_program` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `modules_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) NOT NULL,
  `module_description` text DEFAULT NULL,
  `program_id` int(11) NOT NULL,
  `batch_id` bigint(20) unsigned DEFAULT NULL,
  `learning_mode` enum('Synchronous','Asynchronous') NOT NULL DEFAULT 'Synchronous',
  `content_type` varchar(50) NOT NULL DEFAULT '',
  `content_data` text DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `additional_content` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `admin_override` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'overrides set by admins (stored as JSON)' CHECK (json_valid(`admin_override`)),
  `module_order` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`modules_id`),
  KEY `idx_modules_program` (`program_id`),
  KEY `idx_modules_plan` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `program_id` int(10) unsigned DEFAULT NULL,
  `created_by_admin_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `package_type` enum('full','modular') NOT NULL DEFAULT 'full',
  `module_count` int(11) DEFAULT 0,
  `allowed_modules` int(11) NOT NULL DEFAULT 2,
  `extra_module_price` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`package_id`),
  KEY `created_by_admin_id` (`created_by_admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_history` (
  `payment_history_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `program_id` int(10) unsigned NOT NULL,
  `package_id` int(10) unsigned NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded','cancelled','processing') DEFAULT NULL,
  `payment_method` enum('cash','card','bank_transfer','gcash','manual','other') DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `processed_by_admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_history_id`),
  KEY `idx_payment_history_enrollment` (`enrollment_id`,`payment_status`),
  KEY `idx_payment_history_user` (`user_id`),
  KEY `idx_payment_history_student` (`student_id`),
  KEY `idx_payment_history_date` (`payment_date`),
  KEY `processed_by_admin_id` (`processed_by_admin_id`),
  CONSTRAINT `payment_history_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE,
  CONSTRAINT `payment_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `payment_history_ibfk_3` FOREIGN KEY (`processed_by_admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `payment_method_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `method_name` varchar(255) NOT NULL,
  `method_type` enum('credit_card','gcash','maya','bank_transfer','cash','other') NOT NULL,
  `description` text DEFAULT NULL,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_by_admin_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `payment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `enrollment_id` bigint(20) unsigned NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `package_id` bigint(20) unsigned NOT NULL,
  `payment_method` enum('credit_card','gcash','bank_transfer','cash','admin_marked') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `receipt_number` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `payments_student_id_index` (`student_id`),
  KEY `payments_enrollment_id_index` (`enrollment_id`),
  KEY `payments_payment_status_index` (`payment_status`),
  KEY `payments_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plan` (
  `plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `enable_synchronous` tinyint(1) NOT NULL DEFAULT 1,
  `enable_asynchronous` tinyint(1) NOT NULL DEFAULT 1,
  `learning_mode_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`learning_mode_config`)),
  PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `professor_batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professor_batch` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` bigint(20) unsigned NOT NULL,
  `professor_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `professor_batch_batch_id_professor_id_unique` (`batch_id`,`professor_id`),
  KEY `professor_batch_professor_id_foreign` (`professor_id`),
  KEY `professor_batch_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `professor_batch_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `professor_batch_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `student_batches` (`batch_id`) ON DELETE CASCADE,
  CONSTRAINT `professor_batch_professor_id_foreign` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `professor_program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professor_program` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `professor_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `video_link` varchar(255) DEFAULT NULL,
  `video_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `professors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professors` (
  `professor_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `professor_name` varchar(100) NOT NULL,
  `professor_first_name` varchar(100) NOT NULL,
  `professor_last_name` varchar(100) NOT NULL,
  `professor_email` varchar(100) NOT NULL,
  `professor_password` varchar(255) NOT NULL,
  `referral_code` varchar(20) DEFAULT NULL,
  `professor_archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`professor_id`),
  UNIQUE KEY `professor_email` (`professor_email`),
  UNIQUE KEY `referral_code` (`referral_code`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_name` varchar(100) NOT NULL,
  `created_by_admin_id` int(11) NOT NULL,
  `director_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0,
  `program_description` text DEFAULT NULL,
  PRIMARY KEY (`program_id`),
  KEY `created_by_admin_id` (`created_by_admin_id`),
  KEY `programs_director_id_index` (`director_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quiz_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) unsigned NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quiz_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_questions` (
  `quiz_id` bigint(20) unsigned NOT NULL,
  `quiz_title` varchar(255) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','short_answer','essay') NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `correct_answer` text NOT NULL,
  `explanation` text DEFAULT NULL,
  `difficulty` varchar(50) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `source_file` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by_admin` int(11) DEFAULT NULL,
  `created_by_professor` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quizzes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quizzes` (
  `quiz_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `professor_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `quiz_title` varchar(255) NOT NULL,
  `instructions` text DEFAULT NULL,
  `difficulty` enum('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `total_questions` int(11) NOT NULL DEFAULT 10,
  `time_limit` int(11) NOT NULL DEFAULT 60,
  `document_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `referrals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  KEY `idx_referrer` (`referrer_type`,`referrer_id`),
  KEY `idx_student_registration` (`student_id`,`registration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `registration_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registration_modules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `registration_id` int(10) unsigned NOT NULL,
  `module_id` int(11) NOT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration_modules_registration_id_module_id_unique` (`registration_id`,`module_id`),
  KEY `registration_modules_module_id_foreign` (`module_id`),
  KEY `registration_modules_subject_id_foreign` (`subject_id`),
  KEY `registration_modules_package_id_foreign` (`package_id`),
  CONSTRAINT `registration_modules_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`modules_id`) ON DELETE CASCADE,
  CONSTRAINT `registration_modules_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE,
  CONSTRAINT `registration_modules_registration_id_foreign` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`registration_id`) ON DELETE CASCADE,
  CONSTRAINT `registration_modules_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `courses` (`subject_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registrations` (
  `registration_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) unsigned DEFAULT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `plan_id` int(11) unsigned DEFAULT NULL,
  `plan_name` varchar(255) DEFAULT NULL,
  `program_id` int(11) unsigned DEFAULT NULL,
  `program_name` varchar(255) DEFAULT NULL,
  `enrollment_type` varchar(20) DEFAULT NULL,
  `learning_mode` varchar(50) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `student_school` varchar(50) DEFAULT NULL,
  `street_address` varchar(50) DEFAULT NULL,
  `state_province` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `emergency_contact_number` varchar(255) DEFAULT NULL,
  `good_moral` varchar(255) DEFAULT NULL,
  `PSA` varchar(255) DEFAULT NULL,
  `Course_Cert` varchar(255) DEFAULT NULL,
  `TOR` varchar(255) DEFAULT NULL,
  `Cert_of_Grad` varchar(255) DEFAULT NULL,
  `dynamic_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dynamic_fields`)),
  `photo_2x2` varchar(255) DEFAULT NULL,
  `Start_Date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `phone_number` varchar(20) DEFAULT NULL,
  `telephone_number` varchar(20) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `work_experience` text DEFAULT NULL,
  `preferred_schedule` varchar(50) DEFAULT NULL,
  `emergency_contact_relationship` varchar(100) DEFAULT NULL,
  `health_conditions` text DEFAULT NULL,
  `disability_support` tinyint(1) DEFAULT 0,
  `valid_id` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `diploma_certificate` varchar(255) DEFAULT NULL,
  `medical_certificate` varchar(255) DEFAULT NULL,
  `passport_photo` varchar(255) DEFAULT NULL,
  `parent_guardian_name` varchar(255) DEFAULT NULL,
  `parent_guardian_contact` varchar(20) DEFAULT NULL,
  `previous_school` varchar(255) DEFAULT NULL,
  `graduation_year` year(4) DEFAULT NULL,
  `course_taken` varchar(255) DEFAULT NULL,
  `special_needs` text DEFAULT NULL,
  `scholarship_program` varchar(255) DEFAULT NULL,
  `employment_status` varchar(100) DEFAULT NULL,
  `monthly_income` decimal(10,2) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `selected_modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_modules`)),
  `test_field_auto` varchar(255) DEFAULT NULL,
  `testering` varchar(255) DEFAULT NULL,
  `master` varchar(255) DEFAULT NULL,
  `bagit` varchar(255) DEFAULT NULL,
  `real` varchar(255) DEFAULT NULL,
  `test_auto_column_1752439854` varchar(255) DEFAULT NULL,
  `nyan` varchar(255) DEFAULT NULL,
  `education_level` varchar(50) DEFAULT NULL,
  `sync_async_mode` enum('sync','async') DEFAULT 'sync',
  `Test` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `referral_code` varchar(20) DEFAULT NULL,
  `ama_namin` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`registration_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_batches` (
  `batch_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_name` varchar(255) NOT NULL,
  `program_id` int(11) NOT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `max_capacity` int(11) NOT NULL,
  `current_capacity` int(11) NOT NULL DEFAULT 0,
  `batch_status` enum('pending','available','ongoing','closed','completed') DEFAULT 'pending',
  `registration_deadline` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`batch_id`),
  KEY `student_batches_program_id_foreign` (`program_id`),
  KEY `student_batches_professor_id_foreign` (`professor_id`),
  KEY `student_batches_created_by_foreign` (`created_by`),
  CONSTRAINT `student_batches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `student_batches_professor_id_foreign` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`) ON DELETE SET NULL,
  CONSTRAINT `student_batches_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_grades` (
  `grade_id` bigint(20) unsigned NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `program_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `assignment_name` varchar(255) NOT NULL,
  `grade` decimal(5,2) NOT NULL,
  `max_points` decimal(5,2) NOT NULL,
  `feedback` text DEFAULT NULL,
  `graded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `student_id` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `student_school` varchar(50) DEFAULT NULL,
  `street_address` varchar(50) DEFAULT NULL,
  `state_province` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `good_moral` varchar(255) DEFAULT NULL,
  `PSA` varchar(255) DEFAULT NULL,
  `Course_Cert` varchar(255) DEFAULT NULL,
  `TOR` varchar(255) DEFAULT NULL,
  `Cert_of_Grad` varchar(255) DEFAULT NULL,
  `photo_2x2` varchar(255) DEFAULT NULL,
  `Start_Date` date DEFAULT NULL,
  `date_approved` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(100) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `package_id` int(11) unsigned DEFAULT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `plan_id` int(11) unsigned DEFAULT NULL,
  `plan_name` varchar(255) DEFAULT NULL,
  `program_id` int(11) unsigned DEFAULT NULL,
  `education_level` varchar(255) NOT NULL DEFAULT '',
  `program_name` varchar(255) DEFAULT NULL,
  `enrollment_type` varchar(20) DEFAULT NULL,
  `learning_mode` varchar(50) DEFAULT NULL,
  `Undergraduate` tinyint(1) DEFAULT 0,
  `Graduate` tinyint(1) DEFAULT 0,
  `dynamic_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dynamic_fields`)),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `phone_number` varchar(20) DEFAULT NULL,
  `telephone_number` varchar(20) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `work_experience` text DEFAULT NULL,
  `preferred_schedule` varchar(50) DEFAULT NULL,
  `emergency_contact_relationship` varchar(100) DEFAULT NULL,
  `health_conditions` text DEFAULT NULL,
  `disability_support` tinyint(1) DEFAULT 0,
  `valid_id` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `diploma_certificate` varchar(255) DEFAULT NULL,
  `medical_certificate` varchar(255) DEFAULT NULL,
  `passport_photo` varchar(255) DEFAULT NULL,
  `parent_guardian_name` varchar(255) DEFAULT NULL,
  `parent_guardian_contact` varchar(20) DEFAULT NULL,
  `referral_code` varchar(20) DEFAULT NULL,
  `ama_namin` varchar(255) DEFAULT NULL,
  `previous_school` varchar(255) DEFAULT NULL,
  `graduation_year` year(4) DEFAULT NULL,
  `course_taken` varchar(255) DEFAULT NULL,
  `special_needs` text DEFAULT NULL,
  `scholarship_program` varchar(255) DEFAULT NULL,
  `employment_status` varchar(100) DEFAULT NULL,
  `monthly_income` decimal(10,2) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `selected_modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_modules`)),
  `test_field_auto` varchar(255) DEFAULT NULL,
  `testering` varchar(255) DEFAULT NULL,
  `master` varchar(255) DEFAULT NULL,
  `bagit` varchar(255) DEFAULT NULL,
  `real` varchar(255) DEFAULT NULL,
  `test_auto_column_1752439854` varchar(255) DEFAULT NULL,
  `nyan` varchar(255) DEFAULT NULL,
  `Test` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ui_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ui_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(255) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_type` enum('color','file','text','boolean','json') NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ui_settings_section_setting_key_unique` (`section`,`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `directors_id` bigint(20) unsigned NOT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `last_seen` timestamp NULL DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_lastname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('unverified','student','professor') DEFAULT 'unverified',
  `enrollment_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `enrollment_id` (`enrollment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` VALUES (1,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` VALUES (2,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` VALUES (3,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` VALUES (4,'2025_06_27_000000_add_registration_fields_to_students_table',1);
INSERT INTO `migrations` VALUES (5,'2025_07_03_000003_add_director_id_to_programs_table',2);
INSERT INTO `migrations` VALUES (6,'2025_07_03_141550_add_all_program_access_to_directors_table',3);
INSERT INTO `migrations` VALUES (7,'2025_07_03_142031_create_director_program_pivot_table',4);
INSERT INTO `migrations` VALUES (8,'2025_07_03_144102_add_archived_field_to_students_table',5);
INSERT INTO `migrations` VALUES (9,'2025_07_03_160542_create_form_requirements_table',6);
INSERT INTO `migrations` VALUES (10,'2025_07_03_160620_create_ui_settings_table',7);
INSERT INTO `migrations` VALUES (11,'2025_07_03_163116_add_dynamic_fields_to_registrations_table',8);
INSERT INTO `migrations` VALUES (12,'2025_07_03_163603_update_field_type_enum_in_form_requirements',9);
INSERT INTO `migrations` VALUES (13,'2025_07_04_100222_update_form_requirements_field_type_enum',10);
INSERT INTO `migrations` VALUES (14,'2025_07_04_100602_add_dynamic_fields_to_registrations_table',11);
INSERT INTO `migrations` VALUES (15,'2025_07_04_120000_add_comprehensive_registration_fields',12);
INSERT INTO `migrations` VALUES (16,'2025_07_04_120100_add_is_bold_to_form_requirements',13);
INSERT INTO `migrations` VALUES (17,'2025_07_04_153947_add_module_selection_to_form_requirements_field_type',14);
INSERT INTO `migrations` VALUES (18,'2025_07_06_175157_make_registration_fields_nullable',15);
INSERT INTO `migrations` VALUES (19,'2025_07_07_144922_update_enrollment_type_complete_to_full',16);
INSERT INTO `migrations` VALUES (21,'2025_07_08_130803_add_entity_type_to_form_requirements_table',17);
INSERT INTO `migrations` VALUES (22,'2025_07_08_132024_fix_quizzes_table_foreign_keys',18);
INSERT INTO `migrations` VALUES (23,'2025_07_08_144500_add_batch_id_to_enrollments_table',19);
INSERT INTO `migrations` VALUES (24,'2025_07_09_000003_create_student_batches_table',20);
INSERT INTO `migrations` VALUES (26,'2025_07_09_000015_fix_payment_status_in_enrollments_table',21);
INSERT INTO `migrations` VALUES (27,'2025_07_09_000016_fix_program_type_in_form_requirements_table',22);
INSERT INTO `migrations` VALUES (28,'2025_07_09_184647_add_batch_access_to_enrollments_table',23);
INSERT INTO `migrations` VALUES (29,'2025_07_11_000001_add_pending_status_to_student_batches',24);
INSERT INTO `migrations` VALUES (30,'2025_07_11_000001_add_end_date_to_batches_table',25);
INSERT INTO `migrations` VALUES (34,'2025_07_11_135110_seed_plan_learning_mode_settings',27);
INSERT INTO `migrations` VALUES (35,'2025_07_11_100003_add_learning_mode_config_to_plan',28);
INSERT INTO `migrations` VALUES (36,'2025_07_12_000000_remove_undergraduate_graduate_columns',29);
INSERT INTO `migrations` VALUES (37,'2025_07_12_142000_enhance_messages_table',30);
INSERT INTO `migrations` VALUES (38,'2025_07_12_180254_create_chats_table',31);
INSERT INTO `migrations` VALUES (39,'2025_07_12_204857_add_sender_role_to_messages_table',32);
INSERT INTO `migrations` VALUES (41,'2025_07_16_181521_create_content_items_table',33);
INSERT INTO `migrations` VALUES (42,'2025_07_17_105200_add_course_id_to_content_items_table',34);
INSERT INTO `migrations` VALUES (43,'2025_07_17_125010_add_lesson_fields_to_lessons_table',35);
INSERT INTO `migrations` VALUES (44,'2025_07_17_125751_update_content_type_enum_in_content_items_table',36);
INSERT INTO `migrations` VALUES (45,'2025_07_17_200906_add_wizard_fields_to_packages_table',37);
INSERT INTO `migrations` VALUES (46,'2025_07_18_144157_create_registration_modules_table',38);
