-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2025 at 08:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `artc`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `max_points` int(11) NOT NULL DEFAULT 100,
  `due_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@artc.com', '$2y$10$wVoqaCx2cyvujuym1wakQ.x8UqUSisfeNeXXsmm1HYhc2OclIn4bC', '2025-06-30 08:41:42', '2025-06-30 16:45:13');

-- --------------------------------------------------------

--
-- Table structure for table `admin_settings`
--

CREATE TABLE `admin_settings` (
  `setting_id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_settings`
--

INSERT INTO `admin_settings` (`setting_id`, `setting_key`, `setting_value`, `setting_description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ai_quiz_enabled', 'true', 'Enable or disable AI Quiz Generator feature for professors', 1, '2025-07-07 07:17:23', '2025-07-07 07:17:23'),
(2, 'meeting_creation_enabled', '1', 'Enable or disable meeting creation for professors', 1, '2025-07-18 12:52:43', '2025-07-29 10:59:29'),
(100, 'referral_enabled', '1', 'Enable/disable referral code field in registration', 1, NULL, NULL),
(101, 'referral_required', '0', 'Make referral code required in registration', 1, NULL, NULL),
(102, 'grading_enabled', 'false', NULL, 1, '2025-07-18 05:00:49', '2025-07-29 10:24:59'),
(103, 'upload_videos_enabled', 'true', NULL, 1, '2025-07-18 05:00:49', '2025-07-18 05:00:49'),
(104, 'attendance_enabled', 'false', NULL, 1, '2025-07-18 05:00:49', '2025-07-29 10:24:59'),
(106, 'meeting_whitelist_professors', '', NULL, 1, '2025-07-18 05:49:04', '2025-07-21 23:23:58'),
(108, 'view_programs_enabled', 'false', NULL, 1, '2025-07-18 06:14:52', '2025-07-18 07:28:40'),
(109, 'professor_module_management_enabled', '1', 'Enable/disable module management for professors', 1, '2025-07-29 05:22:19', '2025-07-29 12:07:00'),
(110, 'professor_module_management_whitelist', '', 'Comma-separated list of professor IDs allowed to manage modules', 1, '2025-07-29 05:22:19', '2025-07-29 05:22:19'),
(111, 'director_view_students', 'true', NULL, 1, '2025-07-29 08:38:21', '2025-07-29 08:39:27'),
(112, 'director_manage_programs', 'true', NULL, 1, '2025-07-29 08:38:21', '2025-07-29 08:38:44'),
(113, 'director_manage_modules', 'true', NULL, 1, '2025-07-29 08:38:21', '2025-07-29 08:38:32'),
(114, 'director_manage_enrollments', 'true', NULL, 1, '2025-07-29 08:38:21', '2025-07-29 08:39:27'),
(115, 'director_view_analytics', 'true', NULL, 1, '2025-07-29 08:38:21', '2025-07-29 08:38:52'),
(116, 'director_manage_professors', 'true', NULL, 1, '2025-07-29 08:38:21', '2025-07-29 08:39:28'),
(117, 'director_manage_batches', 'true', NULL, 1, '2025-07-29 08:38:21', '2025-07-29 08:38:52'),
(118, 'director_view_chat_logs', 'true', NULL, 1, '2025-07-29 08:38:21', '2025-07-29 08:38:21'),
(119, 'director_manage_settings', 'true', NULL, 1, '2025-07-29 08:38:21', '2025-07-29 08:38:21'),
(120, 'professor_announcement_management_enabled', '1', 'Enable announcement management for professors', 1, '2025-08-02 18:23:39', '2025-08-02 18:23:39');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `professor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `description` text DEFAULT NULL,
  `publish_date` timestamp NULL DEFAULT NULL,
  `expire_date` timestamp NULL DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `type` enum('general','urgent','event','system','video','assignment','quiz') DEFAULT 'general',
  `target_users` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_users`)),
  `target_programs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_programs`)),
  `target_batches` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_batches`)),
  `target_plans` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_plans`)),
  `target_scope` enum('all','specific') NOT NULL DEFAULT 'all',
  `video_link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `admin_id`, `professor_id`, `program_id`, `title`, `content`, `description`, `publish_date`, `expire_date`, `is_published`, `type`, `target_users`, `target_programs`, `target_batches`, `target_plans`, `target_scope`, `video_link`, `is_active`, `created_at`, `updated_at`) VALUES
(15, NULL, NULL, 39, 'w', 'w', 'w', '2025-07-25 14:38:22', NULL, 1, 'general', '[\"students\"]', '[\"39\"]', '[\"16\"]', '[\"modular\"]', 'specific', NULL, 1, '2025-07-25 14:38:22', '2025-07-25 14:39:07'),
(16, NULL, NULL, 38, 'w', 'w', 'w', '2025-07-25 14:38:43', NULL, 1, 'general', '[\"students\"]', '[\"38\"]', NULL, '[\"modular\"]', 'specific', NULL, 1, '2025-07-25 14:38:43', '2025-07-25 14:39:07'),
(17, NULL, 8, 39, 'Welcome to the New Semester!', 'Welcome students! This semester we have exciting new courses and projects. Please check your schedules and prepare for an engaging learning experience.', 'Important information about the upcoming semester', '2025-07-25 02:43:02', '2025-08-25 02:43:02', 1, 'general', NULL, '[39]', NULL, NULL, 'specific', NULL, 1, '2025-07-26 02:43:02', '2025-07-26 02:43:02'),
(21, 1, NULL, NULL, 'BIG W', 'W', NULL, '2025-08-02 17:24:04', NULL, 1, 'general', NULL, NULL, NULL, NULL, 'all', NULL, 1, '2025-08-02 17:24:04', '2025-08-02 17:24:04'),
(22, NULL, NULL, NULL, 'Test Professor Announcement', 'This is a test announcement for professors only.', NULL, NULL, NULL, 1, 'general', '\"[\\\"professors\\\"]\"', NULL, NULL, NULL, 'specific', NULL, 1, '2025-08-02 18:25:24', '2025-08-02 18:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `max_points` int(11) NOT NULL DEFAULT 100,
  `due_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `professor_id`, `program_id`, `title`, `description`, `instructions`, `max_points`, `due_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 8, 39, 'Database Schema Design', 'Design a comprehensive database schema for an e-commerce platform', 'Create an ER diagram and implement the schema with proper relationships, constraints, and indexes.', 100, '2025-07-31 23:59:59', 1, '2025-07-26 02:42:29', '2025-07-26 02:42:29'),
(2, 8, 39, 'Web Application Development', 'Build a full-stack web application using modern frameworks', 'Develop a CRUD application with user authentication, responsive design, and RESTful API.', 150, '2025-08-09 23:59:59', 1, '2025-07-26 02:42:29', '2025-07-26 02:42:29'),
(3, 8, 39, 'Algorithm Analysis Report', 'Analyze the time and space complexity of various sorting algorithms', 'Write a comprehensive report comparing bubble sort, quicksort, and mergesort with benchmark tests.', 75, '2025-08-03 23:59:59', 1, '2025-07-26 02:42:29', '2025-07-26 02:42:29'),
(4, 8, 39, 'Database Schema Design', 'Design a comprehensive database schema for an e-commerce platform', 'Create an ER diagram and implement the schema with proper relationships, constraints, and indexes.', 100, '2025-07-31 23:59:59', 1, '2025-07-26 02:43:02', '2025-07-26 02:43:02'),
(5, 8, 39, 'Web Application Development', 'Build a full-stack web application using modern frameworks', 'Develop a CRUD application with user authentication, responsive design, and RESTful API.', 150, '2025-08-09 23:59:59', 1, '2025-07-26 02:43:02', '2025-07-26 02:43:02'),
(6, 8, 39, 'Algorithm Analysis Report', 'Analyze the time and space complexity of various sorting algorithms', 'Write a comprehensive report comparing bubble sort, quicksort, and mergesort with benchmark tests.', 75, '2025-08-03 23:59:59', 1, '2025-07-26 02:43:02', '2025-07-26 02:43:02'),
(7, 8, 39, 'Database Schema Design', 'Design a comprehensive database schema for an e-commerce platform', 'Create an ER diagram and implement the schema with proper relationships, constraints, and indexes.', 100, '2025-07-31 23:59:59', 1, '2025-07-26 02:43:28', '2025-07-26 02:43:28'),
(8, 8, 39, 'Web Application Development', 'Build a full-stack web application using modern frameworks', 'Develop a CRUD application with user authentication, responsive design, and RESTful API.', 150, '2025-08-09 23:59:59', 1, '2025-07-26 02:43:28', '2025-07-26 02:43:28'),
(9, 8, 39, 'Algorithm Analysis Report', 'Analyze the time and space complexity of various sorting algorithms', 'Write a comprehensive report comparing bubble sort, quicksort, and mergesort with benchmark tests.', 75, '2025-08-03 23:59:59', 1, '2025-07-26 02:43:28', '2025-07-26 02:43:28');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `module_id` bigint(20) UNSIGNED NOT NULL,
  `content_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`files`)),
  `comments` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','draft','submitted','graded','returned','reviewed') DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `graded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `program_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `batch_professors`
--

CREATE TABLE `batch_professors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `board_passers`
--

CREATE TABLE `board_passers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `student_name` varchar(255) NOT NULL,
  `program` varchar(255) DEFAULT NULL,
  `board_exam` varchar(255) NOT NULL,
  `exam_year` int(11) NOT NULL,
  `exam_date` date DEFAULT NULL,
  `result` enum('PASS','FAIL','PENDING') NOT NULL DEFAULT 'PENDING',
  `rating` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `board_passers`
--

INSERT INTO `board_passers` (`id`, `student_id`, `student_name`, `program`, `board_exam`, `exam_year`, `exam_date`, `result`, `rating`, `notes`, `created_at`, `updated_at`) VALUES
(11, '2025-07-00001', 'Vince Michael Dela Vega', 'Mechanical Engineer', 'ME', 2025, '2025-08-01', 'PASS', NULL, 'W', '2025-07-31 08:32:30', '2025-07-31 08:32:30'),
(12, '2025-07-00001', 'Vince Michael Dela Vega', 'Mechanical Engineer', 'CPA', 2025, '2025-08-02', 'FAIL', NULL, 'L', '2025-07-31 08:36:27', '2025-07-31 08:36:27');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `certificate_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `certificate_number` varchar(255) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `completion_date` date NOT NULL,
  `final_score` decimal(5,2) DEFAULT NULL,
  `certificate_type` varchar(255) NOT NULL DEFAULT 'completion',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `certificate_data` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `issued_at` timestamp NULL DEFAULT NULL,
  `issued_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `chat_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `body_cipher` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_meetings`
--

CREATE TABLE `class_meetings` (
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_meetings`
--

INSERT INTO `class_meetings` (`meeting_id`, `batch_id`, `professor_id`, `title`, `description`, `meeting_date`, `duration_minutes`, `meeting_url`, `attached_files`, `status`, `created_by`, `url_visible_before_meeting`, `url_visibility_minutes_before`, `actual_start_time`, `actual_end_time`, `created_at`, `updated_at`) VALUES
(3, 16, 8, 'Introduction to Software Engineering', 'Overview of software development lifecycle', '2025-07-27 10:00:00', 90, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:42:29', '2025-07-26 02:42:29'),
(4, 16, 8, 'Database Design Workshop', 'Hands-on database design session', '2025-07-29 14:00:00', 120, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:42:29', '2025-07-26 02:42:29'),
(5, 16, 8, 'Project Review Session', 'Review of ongoing projects and assignments', '2025-08-02 09:00:00', 60, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:42:29', '2025-07-26 02:42:29'),
(6, 16, 8, 'Advanced Programming Concepts', 'Object-oriented programming and design patterns', '2025-08-05 13:00:00', 90, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:42:29', '2025-07-26 02:42:29'),
(7, 16, 8, 'Introduction to Software Engineering', 'Overview of software development lifecycle', '2025-07-27 10:00:00', 90, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:43:02', '2025-07-26 02:43:02'),
(8, 16, 8, 'Database Design Workshop', 'Hands-on database design session', '2025-07-29 14:00:00', 120, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:43:02', '2025-07-26 02:43:02'),
(9, 16, 8, 'Project Review Session', 'Review of ongoing projects and assignments', '2025-08-02 09:00:00', 60, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:43:02', '2025-07-26 02:43:02'),
(10, 16, 8, 'Advanced Programming Concepts', 'Object-oriented programming and design patterns', '2025-08-05 13:00:00', 90, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:43:02', '2025-07-26 02:43:02'),
(11, 16, 8, 'Introduction to Software Engineering', 'Overview of software development lifecycle', '2025-07-27 10:00:00', 90, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:43:28', '2025-07-26 02:43:28'),
(12, 16, 8, 'Database Design Workshop', 'Hands-on database design session', '2025-07-29 14:00:00', 120, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:43:28', '2025-07-26 02:43:28'),
(13, 16, 8, 'Project Review Session', 'Review of ongoing projects and assignments', '2025-08-02 09:00:00', 60, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:43:28', '2025-07-26 02:43:28'),
(14, 16, 8, 'Advanced Programming Concepts', 'Object-oriented programming and design patterns', '2025-08-05 13:00:00', 90, 'https://zoom.us/j/example', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-26 02:43:28', '2025-07-26 02:43:28');

-- --------------------------------------------------------

--
-- Table structure for table `content_completions`
--

CREATE TABLE `content_completions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `content_completions`
--

INSERT INTO `content_completions` (`id`, `student_id`, `content_id`, `course_id`, `module_id`, `completed_at`, `created_at`, `updated_at`) VALUES
(57, '2025-07-00001', 83, 48, 77, '2025-07-31 07:17:05', '2025-07-31 07:17:05', '2025-07-31 07:17:05'),
(58, '2025-07-00001', 81, 50, 78, '2025-07-31 07:17:13', '2025-07-31 07:17:13', '2025-07-31 07:17:13'),
(59, '2025-07-00001', 78, 51, 78, '2025-07-31 07:17:19', '2025-07-31 07:17:19', '2025-07-31 07:17:19'),
(60, '2025-07-00001', 80, 50, 78, '2025-07-31 07:17:27', '2025-07-31 07:17:27', '2025-07-31 07:17:27'),
(61, '2025-07-00001', 79, 50, 78, '2025-07-31 07:17:37', '2025-07-31 07:17:37', '2025-07-31 07:17:37'),
(62, '2025-07-00001', 82, 50, 78, '2025-07-31 07:17:46', '2025-07-31 07:17:46', '2025-07-31 07:17:46'),
(63, '2025-07-00001', 77, 52, 79, '2025-07-31 07:18:19', '2025-07-31 07:18:19', '2025-07-31 07:18:19'),
(64, '2025-07-00001', 87, 53, 79, '2025-07-31 07:19:22', '2025-07-31 07:19:22', '2025-07-31 07:19:22');

-- --------------------------------------------------------

--
-- Table structure for table `content_items`
--

CREATE TABLE `content_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `content_title` varchar(255) NOT NULL,
  `content_description` text DEFAULT NULL,
  `lesson_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content_type` enum('assignment','quiz','test','link','video','document','lesson') DEFAULT NULL,
  `created_by_professor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content_data`)),
  `content_url` varchar(255) DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `max_points` decimal(8,2) DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `time_limit` int(11) DEFAULT NULL,
  `content_order` int(11) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `enable_submission` tinyint(1) NOT NULL DEFAULT 0,
  `allowed_file_types` varchar(255) DEFAULT NULL,
  `max_file_size` int(11) DEFAULT NULL,
  `submission_instructions` text DEFAULT NULL,
  `allow_multiple_submissions` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `archived_by_professor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_override` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`admin_override`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `requires_prerequisite` tinyint(1) NOT NULL DEFAULT 0,
  `prerequisite_content_id` bigint(20) UNSIGNED DEFAULT NULL,
  `release_date` timestamp NULL DEFAULT NULL,
  `completion_criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`completion_criteria`)),
  `lock_reason` varchar(255) DEFAULT NULL,
  `locked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `file_mime` varchar(255) DEFAULT NULL,
  `has_multiple_files` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `content_items`
--

INSERT INTO `content_items` (`id`, `content_title`, `content_description`, `lesson_id`, `course_id`, `content_type`, `created_by_professor_id`, `content_data`, `content_url`, `attachment_path`, `max_points`, `due_date`, `time_limit`, `content_order`, `sort_order`, `enable_submission`, `allowed_file_types`, `max_file_size`, `submission_instructions`, `allow_multiple_submissions`, `order`, `is_required`, `is_active`, `is_archived`, `archived_at`, `archived_by_professor_id`, `admin_override`, `created_at`, `updated_at`, `is_locked`, `requires_prerequisite`, `prerequisite_content_id`, `release_date`, `completion_criteria`, `lock_reason`, `locked_by`, `file_path`, `file_name`, `file_size`, `file_mime`, `has_multiple_files`) VALUES
(77, 'Lessons 1', NULL, NULL, 52, 'lesson', NULL, '{\"lesson_video_url\":null}', NULL, '[\"content\\/1753907375_final-docu.pdf\"]', 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 12:29:35', '2025-07-30 12:29:35', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(78, 'Lessons 1', NULL, NULL, 51, 'lesson', NULL, '{\"lesson_video_url\":null}', NULL, '[\"content\\/1753907633_final-docu.pdf\"]', 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 12:33:53', '2025-07-30 12:33:53', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(79, 'LESSON 1 HOSPITALITY', NULL, NULL, 50, 'lesson', 8, '{\"lesson_video_url\":null}', NULL, '[\"content\\/1753908507_1753757055493_BRAVO_-_Chapter-2.docx\"]', NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 12:48:27', '2025-07-30 12:48:27', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '1753757055493_BRAVO_-_Chapter-2.docx', 496092, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 0),
(80, 'TEST VID', NULL, NULL, 50, 'lesson', NULL, '{\"lesson_video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=Why0mFxXF08\"}', 'https://www.youtube.com/watch?v=Why0mFxXF08', NULL, 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 13:25:49', '2025-07-30 13:25:49', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(81, 'TEST VID2', NULL, NULL, 50, 'video', NULL, '{\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=Why0mFxXF08\"}', 'https://www.youtube.com/watch?v=Why0mFxXF08', '[\"content\\/1753910811_final-docu.pdf\"]', 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 13:26:51', '2025-07-30 13:26:51', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(82, 'Lessons 1', NULL, NULL, 50, 'assignment', NULL, '{\"assignment_instructions\":null,\"due_date\":\"2025-07-11T06:08\",\"max_points\":0}', NULL, '[\"content\\/1753913330_final-docu.pdf\"]', 0.00, '2025-07-11 06:08:00', NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 14:08:50', '2025-07-30 14:08:50', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(83, 'Lessons 1', NULL, NULL, 48, 'lesson', NULL, '{\"lesson_video_url\":null}', NULL, '[\"content\\/1753929360_1753907633_final-docu.pdf\"]', 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 18:36:01', '2025-07-30 18:36:01', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(84, 'Lessons 1', NULL, NULL, 47, 'lesson', NULL, '{\"lesson_video_url\":null}', NULL, '[\"content\\/1753929387_Vince _Certificate (1).pdf\"]', 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 18:36:27', '2025-07-30 18:36:27', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(85, 'Lessons 1', NULL, NULL, 49, 'lesson', NULL, '{\"lesson_video_url\":null}', NULL, '[\"content\\/1753929402_Vince _Certificate (1).pdf\"]', 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 18:36:42', '2025-07-30 18:36:42', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(86, 'Lessons 1', NULL, NULL, 46, 'lesson', NULL, '{\"lesson_video_url\":null}', NULL, '[\"content\\/1753929422_Vince _Certificate (1).pdf\"]', 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-30 18:37:03', '2025-07-30 18:37:03', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(87, 'Lessons 1', 'W', NULL, 53, 'lesson', NULL, '{\"lesson_video_url\":null}', NULL, '[\"content\\/1753975145_Vince _Certificate (1).pdf\"]', 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-07-31 07:19:05', '2025-07-31 07:19:05', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(88, 'qqq', 'Manually Created Quiz', NULL, 47, 'quiz', NULL, '\"{\\\"quiz_id\\\":40}\"', NULL, NULL, NULL, '2025-08-06 03:55:00', NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-08-01 19:55:37', '2025-08-02 12:39:49', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(89, 'BREH', 'Manually Created Quiz', NULL, 48, 'quiz', NULL, '\"{\\\"quiz_id\\\":41}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL, NULL, '2025-08-02 12:42:20', '2025-08-02 12:42:20', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(90, 'asdasd', 'Manually Created Quiz', NULL, 53, 'quiz', NULL, '\"{\\\"quiz_id\\\":42}\"', NULL, NULL, NULL, '2025-08-11 20:49:00', NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL, NULL, '2025-08-02 12:49:15', '2025-08-02 12:49:15', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(91, 'Test', 'Manually Created Quiz', NULL, 48, 'quiz', NULL, '\"{\\\"quiz_id\\\":45}\"', NULL, NULL, NULL, '2025-08-25 22:27:00', NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL, NULL, '2025-08-02 14:25:56', '2025-08-02 15:48:14', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(93, 'qweqwe', 'Manually Created Quiz', NULL, 50, 'quiz', NULL, '\"{\\\"quiz_id\\\":47}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL, NULL, '2025-08-02 14:43:18', '2025-08-02 17:00:39', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(94, 'THIS IS PROFESSOR', 'Manually Created Quiz', NULL, 47, 'quiz', NULL, '\"{\\\"quiz_id\\\":48}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL, NULL, '2025-08-02 14:53:20', '2025-08-02 16:55:27', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(95, 'qwe', 'Admin Created Quiz', NULL, 52, 'quiz', NULL, '\"{\\\"quiz_id\\\":49}\"', NULL, NULL, NULL, '2025-08-04 17:28:00', NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-08-02 15:28:48', '2025-08-02 15:46:49', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(96, 'ww', 'Admin Created Quiz', NULL, 53, 'quiz', NULL, '\"{\\\"quiz_id\\\":50}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL, NULL, '2025-08-02 15:48:39', '2025-08-02 15:48:39', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(97, 'asdas', 'Manually Created Quiz', NULL, 52, 'quiz', NULL, '\"{\\\"quiz_id\\\":54}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL, NULL, '2025-08-02 16:36:46', '2025-08-02 16:36:46', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(98, 'qweqw', 'AI Generated Quiz', NULL, 52, 'quiz', NULL, '\"{\\\"quiz_id\\\":55}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-08-02 16:47:31', '2025-08-02 16:47:37', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(99, 'ssss', 'AI Generated Quiz', NULL, 52, 'quiz', NULL, '\"{\\\"quiz_id\\\":56}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-08-02 16:51:52', '2025-08-02 16:51:52', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(100, 'asdasdasaaa', 'Manually Created Quiz', NULL, 52, 'quiz', NULL, '\"{\\\"quiz_id\\\":57}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-08-02 16:52:17', '2025-08-02 16:55:33', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(101, 'AI?', 'AI Generated Quiz', NULL, 50, 'quiz', NULL, '\"{\\\"quiz_id\\\":58}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 17:00:52', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(102, 'asdasd', 'AI Generated Quiz', NULL, 52, 'quiz', NULL, '\"{\\\"quiz_id\\\":59}\"', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, NULL, NULL, NULL, '2025-08-02 17:01:09', '2025-08-02 17:01:09', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `subject_description` text DEFAULT NULL,
  `module_id` bigint(20) UNSIGNED NOT NULL,
  `subject_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subject_order` int(11) NOT NULL DEFAULT 0,
  `course_order` int(11) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `admin_override` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`admin_override`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `requires_prerequisite` tinyint(1) NOT NULL DEFAULT 0,
  `prerequisite_course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `release_date` timestamp NULL DEFAULT NULL,
  `completion_criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`completion_criteria`)),
  `lock_reason` varchar(255) DEFAULT NULL,
  `locked_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`subject_id`, `subject_name`, `subject_description`, `module_id`, `subject_price`, `subject_order`, `course_order`, `is_required`, `is_active`, `is_archived`, `admin_override`, `created_at`, `updated_at`, `is_locked`, `requires_prerequisite`, `prerequisite_course_id`, `release_date`, `completion_criteria`, `lock_reason`, `locked_by`) VALUES
(31, 'Advance Cooking Method', NULL, 70, 3500.00, 1, 0, 0, 1, 0, NULL, '2025-07-25 05:19:22', '2025-07-25 06:45:24', 0, 0, NULL, NULL, NULL, NULL, NULL),
(32, 'Advance Cooking Method 2', NULL, 70, 2500.00, 2, 0, 0, 1, 0, NULL, '2025-07-25 05:19:33', '2025-07-25 06:45:24', 0, 0, NULL, NULL, NULL, NULL, NULL),
(33, 'Civil Engineering 101', NULL, 67, 2500.00, 1, 0, 0, 1, 0, NULL, '2025-07-25 05:54:28', '2025-07-25 05:54:28', 0, 0, NULL, NULL, NULL, NULL, NULL),
(34, 'Mechanical Engineering 101', NULL, 66, 2500.00, 1, 0, 0, 1, 0, NULL, '2025-07-25 05:54:56', '2025-07-25 05:54:56', 0, 0, NULL, NULL, NULL, NULL, NULL),
(35, 'Mechanics', NULL, 69, 2500.00, 1, 0, 0, 1, 0, NULL, '2025-07-25 06:36:20', '2025-07-25 06:36:20', 0, 0, NULL, NULL, NULL, NULL, NULL),
(36, 'Advance Mechanics Method 2', NULL, 66, 2500.00, 2, 0, 0, 1, 0, NULL, '2025-07-25 08:13:02', '2025-07-25 08:13:02', 0, 0, NULL, NULL, NULL, NULL, NULL),
(37, 'Advance Cooking Method 2', NULL, 72, 2.00, 1, 0, 0, 1, 0, NULL, '2025-07-25 11:54:23', '2025-07-25 11:54:23', 0, 0, NULL, NULL, NULL, NULL, NULL),
(38, 'Math', NULL, 68, 2424.00, 1, 0, 0, 1, 0, NULL, '2025-07-25 15:16:55', '2025-07-25 15:16:55', 0, 0, NULL, NULL, NULL, NULL, NULL),
(39, 'MECHANICS 1', NULL, 74, 250.00, 1, 0, 0, 1, 0, NULL, '2025-07-25 15:34:11', '2025-07-25 15:34:11', 0, 0, NULL, NULL, NULL, NULL, NULL),
(40, 'MECHANICS 2', NULL, 73, 250.00, 1, 0, 0, 1, 0, NULL, '2025-07-25 15:34:51', '2025-07-25 15:34:51', 0, 0, NULL, NULL, NULL, NULL, NULL),
(41, 'Mechanics 3', NULL, 73, 2500.00, 2, 0, 0, 1, 0, NULL, '2025-07-25 15:37:33', '2025-07-25 15:37:33', 0, 0, NULL, NULL, NULL, NULL, NULL),
(42, 'BRUH 1', NULL, 74, 2500.00, 2, 0, 0, 1, 0, NULL, '2025-07-25 15:39:11', '2025-07-25 15:39:11', 0, 0, NULL, NULL, NULL, NULL, NULL),
(43, 'Civil Engineering 101', NULL, 76, 2500.00, 1, 0, 0, 1, 0, NULL, '2025-07-26 00:57:43', '2025-07-26 00:57:43', 0, 0, NULL, NULL, NULL, NULL, NULL),
(44, 'Civil Engineering 1011', NULL, 75, 2500.00, 1, 0, 0, 1, 0, NULL, '2025-07-26 00:57:54', '2025-07-26 00:57:54', 0, 0, NULL, NULL, NULL, NULL, NULL),
(45, 'Civil Engineering Physics', NULL, 76, 2500.00, 2, 0, 0, 1, 0, NULL, '2025-07-26 00:58:10', '2025-07-26 00:58:10', 0, 0, NULL, NULL, NULL, NULL, NULL),
(46, 'Nursing', NULL, 77, 2500.00, 4, 0, 0, 1, 0, NULL, '2025-07-27 06:17:24', '2025-07-28 08:17:54', 0, 0, NULL, NULL, NULL, NULL, NULL),
(47, 'HRM', NULL, 77, 3000.00, 2, 0, 0, 1, 0, NULL, '2025-07-27 06:18:58', '2025-07-28 08:17:54', 0, 0, NULL, NULL, NULL, NULL, NULL),
(48, 'Chemistry', NULL, 77, 2500.00, 1, 0, 0, 1, 0, NULL, '2025-07-27 06:19:44', '2025-07-28 08:17:54', 0, 0, NULL, NULL, NULL, NULL, NULL),
(49, 'Math', NULL, 77, 2500.00, 3, 0, 0, 1, 0, NULL, '2025-07-27 06:19:52', '2025-07-28 08:17:54', 0, 0, NULL, NULL, NULL, NULL, NULL),
(50, 'Hospitality', NULL, 78, 2500.00, 1, 0, 0, 1, 0, NULL, '2025-07-27 06:21:47', '2025-07-27 06:21:47', 0, 0, NULL, NULL, NULL, NULL, NULL),
(51, 'Advance Hospitality Method', NULL, 78, 5000.00, 2, 0, 0, 1, 0, NULL, '2025-07-27 06:22:18', '2025-07-27 06:22:18', 0, 0, NULL, NULL, NULL, NULL, NULL),
(52, 'Mechanical Engineering 101', NULL, 79, 2500.00, 1, 0, 0, 1, 0, NULL, '2025-07-27 06:27:26', '2025-07-27 06:27:26', 0, 0, NULL, NULL, NULL, NULL, NULL),
(53, 'Mechanics', NULL, 79, 5000.00, 2, 0, 0, 1, 0, NULL, '2025-07-27 06:27:51', '2025-07-27 06:27:51', 0, 0, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_completions`
--

CREATE TABLE `course_completions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `module_id` int(11) DEFAULT NULL,
  `content_id` bigint(20) UNSIGNED DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_completions`
--

INSERT INTO `course_completions` (`id`, `student_id`, `course_id`, `module_id`, `content_id`, `completed_at`, `created_at`, `updated_at`) VALUES
(65, '2025-07-00001', 48, 77, NULL, '2025-07-31 07:17:05', '2025-07-31 07:17:05', '2025-07-31 07:17:05'),
(66, '2025-07-00001', 51, 78, NULL, '2025-07-31 07:17:19', '2025-07-31 07:17:19', '2025-07-31 07:17:19'),
(67, '2025-07-00001', 50, 78, NULL, '2025-07-31 07:17:46', '2025-07-31 07:17:46', '2025-07-31 07:17:46'),
(68, '2025-07-00001', 52, 79, NULL, '2025-07-31 07:18:19', '2025-07-31 07:18:19', '2025-07-31 07:18:19'),
(69, '2025-07-00001', 53, 79, NULL, '2025-07-31 07:19:22', '2025-07-31 07:19:22', '2025-07-31 07:19:22');

-- --------------------------------------------------------

--
-- Table structure for table `deadlines`
--

CREATE TABLE `deadlines` (
  `deadline_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('assignment','quiz','activity','exam') NOT NULL DEFAULT 'assignment',
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `status` enum('pending','completed','overdue') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `directors`
--

CREATE TABLE `directors` (
  `directors_id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `directors`
--

INSERT INTO `directors` (`directors_id`, `admin_id`, `directors_name`, `directors_first_name`, `directors_last_name`, `directors_email`, `directors_password`, `referral_code`, `directors_archived`, `has_all_program_access`, `created_at`, `updated_at`) VALUES
(7, 1, 'alek piriz', 'alek', 'piriz', 'alek@gmail.com', '$2y$10$.eS0jFVC9.cpmtFI7J/bqe5/cwNumfI4JzgI8Hrtl7tcCBknTL/ey', NULL, 0, 1, '2025-07-09 12:04:55', '2025-07-09 12:21:25'),
(8, 1, '123123 weq', '123123', 'weq', 'Director1@gmail.com', '$2y$10$gWhR8euTSIkWAGcsLm83OueLzPoOCUQ41gh3VE/PfGRiN4bWKHiyC', NULL, 0, 1, '2025-07-12 05:32:17', '2025-07-12 05:41:43'),
(999, 1, 'Test Director', 'Test', 'Director', 'director@test.com', '$2y$10$NW5OepcYIwp9Aq0WFrthnOBTQ5shyTDkT9AhJo9gEpyebef/a6L8S', NULL, 0, 1, '2025-07-13 12:51:05', '2025-07-13 12:51:05');

-- --------------------------------------------------------

--
-- Table structure for table `director_program`
--

CREATE TABLE `director_program` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `director_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education_levels`
--

CREATE TABLE `education_levels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `level_name` varchar(255) NOT NULL,
  `file_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`file_requirements`)),
  `available_for_general` tinyint(1) NOT NULL DEFAULT 1,
  `available_for_professional` tinyint(1) NOT NULL DEFAULT 1,
  `available_for_review` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `level_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_levels`
--

INSERT INTO `education_levels` (`id`, `level_name`, `file_requirements`, `available_for_general`, `available_for_professional`, `available_for_review`, `created_at`, `updated_at`, `is_active`, `level_order`) VALUES
(1, 'Undergraduate', '[{\"field_name\":\"school_id\",\"document_type\":\"school_id\",\"file_type\":\"image\",\"custom_name\":null,\"is_required\":true,\"available_full_plan\":true,\"available_modular_plan\":true,\"description\":\"Valid school identification\"},{\"field_name\":\"TOR\",\"document_type\":\"TOR\",\"file_type\":\"pdf\",\"custom_name\":null,\"is_required\":true,\"available_full_plan\":true,\"available_modular_plan\":true,\"description\":\"Transcript of Records\"},{\"field_name\":\"good_moral\",\"document_type\":\"good_moral\",\"file_type\":\"pdf\",\"custom_name\":null,\"is_required\":true,\"available_full_plan\":true,\"available_modular_plan\":true,\"description\":\"Certificate of Good Moral Character\"},{\"field_name\":\"PSA\",\"document_type\":\"PSA\",\"file_type\":\"pdf\",\"custom_name\":null,\"is_required\":true,\"available_full_plan\":true,\"available_modular_plan\":true,\"description\":\"PSA Birth Certificate\"}]', 1, 1, 1, '2025-07-14 07:58:48', '2025-07-14 11:06:21', 1, 0),
(2, 'Graduate', '\"[{\\\"field_name\\\":\\\"school_id\\\",\\\"document_type\\\":\\\"school_id\\\",\\\"file_type\\\":\\\"image\\\",\\\"custom_name\\\":null,\\\"is_required\\\":false,\\\"available_full_plan\\\":true,\\\"available_modular_plan\\\":true},{\\\"field_name\\\":\\\"diploma\\\",\\\"document_type\\\":\\\"diploma\\\",\\\"file_type\\\":\\\"pdf\\\",\\\"custom_name\\\":null,\\\"is_required\\\":false,\\\"available_full_plan\\\":true,\\\"available_modular_plan\\\":true}]\"', 1, 1, 1, '2025-07-14 07:58:48', '2025-07-16 00:03:37', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `registration_id` int(11) UNSIGNED DEFAULT NULL,
  `student_id` varchar(30) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `enrollment_type` enum('Modular','Full') NOT NULL DEFAULT 'Modular',
  `learning_mode` enum('Synchronous','Asynchronous') NOT NULL DEFAULT 'Synchronous',
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `individual_start_date` datetime DEFAULT NULL,
  `individual_end_date` datetime DEFAULT NULL,
  `enrollment_status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `progress_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `completion_date` timestamp NULL DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT NULL,
  `total_modules` int(11) NOT NULL DEFAULT 0,
  `completed_modules` int(11) NOT NULL DEFAULT 0,
  `total_courses` int(11) NOT NULL DEFAULT 0,
  `completed_courses` int(11) NOT NULL DEFAULT 0,
  `certificate_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `certificate_requested` tinyint(1) NOT NULL DEFAULT 0,
  `certificate_issued` tinyint(1) NOT NULL DEFAULT 0,
  `payment_status` enum('pending','paid','failed','cancelled') DEFAULT 'pending',
  `batch_access_granted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Grants dashboard access for batch students regardless of enrollment/payment status',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_date` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` varchar(255) DEFAULT NULL,
  `diploma` varchar(255) DEFAULT NULL,
  `valid_school_identification` varchar(255) DEFAULT NULL,
  `transcript_of_records` varchar(255) DEFAULT NULL,
  `certificate_of_good_moral_character` varchar(255) DEFAULT NULL,
  `psa_birth_certificate` varchar(255) DEFAULT NULL,
  `photo_2x2` varchar(255) DEFAULT NULL,
  `diploma_certificate` varchar(255) DEFAULT NULL,
  `transcript_records` varchar(255) DEFAULT NULL,
  `moral_certificate` varchar(255) DEFAULT NULL,
  `birth_cert` varchar(255) DEFAULT NULL,
  `id_photo` varchar(255) DEFAULT NULL,
  `passport_photo` varchar(255) DEFAULT NULL,
  `medical_certificate` varchar(255) DEFAULT NULL,
  `barangay_clearance` varchar(255) DEFAULT NULL,
  `police_clearance` varchar(255) DEFAULT NULL,
  `nbi_clearance` varchar(255) DEFAULT NULL,
  `tor` varchar(255) DEFAULT NULL,
  `good_moral` varchar(255) DEFAULT NULL,
  `psa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `registration_id`, `student_id`, `user_id`, `program_id`, `package_id`, `enrollment_type`, `learning_mode`, `batch_id`, `individual_start_date`, `individual_end_date`, `enrollment_status`, `progress_percentage`, `completion_date`, `last_activity`, `total_modules`, `completed_modules`, `total_courses`, `completed_courses`, `certificate_eligible`, `certificate_requested`, `certificate_issued`, `payment_status`, `batch_access_granted`, `created_at`, `start_date`, `updated_at`, `school_id`, `diploma`, `valid_school_identification`, `transcript_of_records`, `certificate_of_good_moral_character`, `psa_birth_certificate`, `photo_2x2`, `diploma_certificate`, `transcript_records`, `moral_certificate`, `birth_cert`, `id_photo`, `passport_photo`, `medical_certificate`, `barangay_clearance`, `police_clearance`, `nbi_clearance`, `tor`, `good_moral`, `psa`) VALUES
(199, 1, '2025-07-00001', 1, 40, 34, 'Modular', 'Asynchronous', NULL, NULL, NULL, 'approved', 0.00, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 'paid', 0, '2025-07-31 07:13:51', NULL, '2025-07-31 07:14:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(200, 2, '2025-07-00001', 1, 41, 34, 'Modular', 'Asynchronous', NULL, NULL, NULL, 'approved', 0.00, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 'paid', 0, '2025-07-31 07:16:10', NULL, '2025-07-31 07:16:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_courses`
--

CREATE TABLE `enrollment_courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL,
  `enrollment_type` enum('module','course') NOT NULL DEFAULT 'course',
  `course_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollment_courses`
--

INSERT INTO `enrollment_courses` (`id`, `enrollment_id`, `course_id`, `module_id`, `enrollment_type`, `course_price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 40, 'course', 0.00, 1, '2025-07-18 09:26:46', '2025-07-18 09:26:46'),
(2, 169, 34, 66, 'course', 0.00, 1, '2025-07-25 09:02:54', '2025-07-25 09:02:54'),
(3, 169, 36, 66, 'course', 0.00, 1, '2025-07-25 09:02:54', '2025-07-25 09:02:54'),
(4, 170, 33, 67, 'course', 0.00, 1, '2025-07-25 09:07:58', '2025-07-25 09:07:58'),
(5, 167, 34, 66, 'course', 0.00, 1, '2025-07-25 09:18:39', '2025-07-25 09:18:39'),
(6, 167, 36, 66, 'course', 0.00, 1, '2025-07-25 09:18:39', '2025-07-25 09:18:39'),
(9, 168, 34, 66, 'course', 0.00, 1, '2025-07-25 09:18:39', '2025-07-25 09:18:39'),
(10, 168, 36, 66, 'course', 0.00, 1, '2025-07-25 09:18:39', '2025-07-25 09:18:39'),
(13, 171, 36, 66, 'course', 0.00, 1, '2025-07-25 09:23:27', '2025-07-25 09:23:27'),
(14, 171, 34, 66, 'course', 0.00, 1, '2025-07-25 09:23:27', '2025-07-25 09:23:27'),
(15, 166, 34, 66, 'course', 0.00, 1, '2025-07-25 09:28:00', '2025-07-25 09:28:00'),
(16, 166, 36, 66, 'course', 0.00, 1, '2025-07-25 09:28:00', '2025-07-25 09:28:00'),
(17, 172, 35, 69, 'course', 0.00, 1, '2025-07-25 09:34:23', '2025-07-25 09:34:23'),
(18, 172, 34, 66, 'course', 0.00, 1, '2025-07-25 09:34:23', '2025-07-25 09:34:23'),
(19, 172, 36, 66, 'course', 0.00, 1, '2025-07-25 09:34:23', '2025-07-25 09:34:23'),
(20, 165, 33, 67, 'course', 0.00, 1, '2025-07-25 12:04:56', '2025-07-25 12:04:56'),
(21, 167, 33, 67, 'course', 0.00, 1, '2025-07-25 12:04:56', '2025-07-25 12:04:56'),
(22, 173, 34, 66, 'course', 0.00, 1, '2025-07-25 15:30:04', '2025-07-25 15:30:04'),
(23, 173, 36, 66, 'course', 0.00, 1, '2025-07-25 15:30:05', '2025-07-25 15:30:05'),
(24, 173, 35, 69, 'course', 0.00, 1, '2025-07-25 15:30:05', '2025-07-25 15:30:05'),
(25, 174, 39, 74, 'course', 0.00, 1, '2025-07-25 15:37:56', '2025-07-25 15:37:56'),
(26, 174, 40, 73, 'course', 0.00, 1, '2025-07-25 15:37:56', '2025-07-25 15:37:56'),
(27, 175, 39, 74, 'course', 0.00, 1, '2025-07-25 15:41:26', '2025-07-25 15:41:26'),
(28, 175, 42, 74, 'course', 0.00, 1, '2025-07-25 15:41:26', '2025-07-25 15:41:26'),
(29, 175, 40, 73, 'course', 0.00, 1, '2025-07-25 15:41:26', '2025-07-25 15:41:26'),
(30, 176, 42, 74, 'course', 0.00, 1, '2025-07-26 00:53:51', '2025-07-26 00:53:51'),
(31, 176, 40, 73, 'course', 0.00, 1, '2025-07-26 00:53:51', '2025-07-26 00:53:51'),
(32, 176, 41, 73, 'course', 0.00, 1, '2025-07-26 00:53:51', '2025-07-26 00:53:51'),
(33, 177, 44, 75, 'course', 0.00, 1, '2025-07-26 01:14:11', '2025-07-26 01:14:11'),
(34, 177, 43, 76, 'course', 0.00, 1, '2025-07-26 01:14:11', '2025-07-26 01:14:11'),
(35, 177, 45, 76, 'course', 0.00, 1, '2025-07-26 01:14:11', '2025-07-26 01:14:11'),
(36, 178, 40, 73, 'course', 0.00, 1, '2025-07-26 01:15:02', '2025-07-26 01:15:02'),
(37, 178, 41, 73, 'course', 0.00, 1, '2025-07-26 01:15:02', '2025-07-26 01:15:02'),
(38, 178, 39, 74, 'course', 0.00, 1, '2025-07-26 01:15:02', '2025-07-26 01:15:02'),
(39, 183, 43, 76, 'course', 0.00, 1, '2025-07-26 02:26:57', '2025-07-26 02:26:57'),
(40, 183, 44, 75, 'course', 0.00, 1, '2025-07-26 02:26:57', '2025-07-26 02:26:57'),
(41, 184, 45, 76, 'course', 0.00, 1, '2025-07-26 02:28:53', '2025-07-26 02:28:53'),
(42, 186, 45, 76, 'course', 0.00, 1, '2025-07-26 10:46:50', '2025-07-26 10:46:50'),
(43, 186, 43, 76, 'course', 0.00, 1, '2025-07-26 10:46:50', '2025-07-26 10:46:50'),
(44, 186, 44, 75, 'course', 0.00, 1, '2025-07-26 10:46:50', '2025-07-26 10:46:50'),
(45, 188, 43, 76, 'course', 0.00, 1, '2025-07-26 11:22:10', '2025-07-26 11:22:10'),
(46, 188, 45, 76, 'course', 0.00, 1, '2025-07-26 11:22:10', '2025-07-26 11:22:10'),
(47, 188, 44, 75, 'course', 0.00, 1, '2025-07-26 11:22:10', '2025-07-26 11:22:10'),
(48, 189, 41, 73, 'course', 0.00, 1, '2025-07-26 11:47:37', '2025-07-26 11:47:37'),
(49, 189, 40, 73, 'course', 0.00, 1, '2025-07-26 11:47:37', '2025-07-26 11:47:37'),
(50, 189, 42, 74, 'course', 0.00, 1, '2025-07-26 11:47:37', '2025-07-26 11:47:37'),
(51, 190, 43, 76, 'course', 0.00, 1, '2025-07-26 12:03:23', '2025-07-26 12:03:23'),
(52, 190, 45, 76, 'course', 0.00, 1, '2025-07-26 12:03:23', '2025-07-26 12:03:23'),
(53, 190, 44, 75, 'course', 0.00, 1, '2025-07-26 12:03:23', '2025-07-26 12:03:23'),
(54, 192, 44, 75, 'course', 0.00, 1, '2025-07-26 12:18:26', '2025-07-26 12:18:26'),
(55, 192, 43, 76, 'course', 0.00, 1, '2025-07-26 12:18:26', '2025-07-26 12:18:26'),
(56, 192, 45, 76, 'course', 0.00, 1, '2025-07-26 12:18:26', '2025-07-26 12:18:26'),
(57, 193, 39, 74, 'course', 0.00, 1, '2025-07-26 12:32:08', '2025-07-26 12:32:08'),
(58, 193, 42, 74, 'course', 0.00, 1, '2025-07-26 12:32:08', '2025-07-26 12:32:08'),
(59, 193, 40, 73, 'course', 0.00, 1, '2025-07-26 12:32:08', '2025-07-26 12:32:08'),
(60, 193, 41, 73, 'course', 0.00, 1, '2025-07-26 12:32:08', '2025-07-26 12:32:08'),
(61, 194, 46, 77, 'course', 0.00, 1, '2025-07-27 06:23:17', '2025-07-27 06:23:17'),
(62, 194, 47, 77, 'course', 0.00, 1, '2025-07-27 06:23:17', '2025-07-27 06:23:17'),
(63, 194, 48, 77, 'course', 0.00, 1, '2025-07-27 06:23:17', '2025-07-27 06:23:17'),
(64, 194, 50, 78, 'course', 0.00, 1, '2025-07-27 06:23:17', '2025-07-27 06:23:17'),
(65, 195, 50, 78, 'course', 0.00, 1, '2025-07-27 09:32:39', '2025-07-27 09:32:39'),
(66, 195, 51, 78, 'course', 0.00, 1, '2025-07-27 09:32:39', '2025-07-27 09:32:39'),
(67, 195, 46, 77, 'course', 0.00, 1, '2025-07-27 09:32:39', '2025-07-27 09:32:39'),
(68, 196, 52, 79, 'course', 0.00, 1, '2025-07-31 01:41:38', '2025-07-31 01:41:38'),
(69, 196, 53, 79, 'course', 0.00, 1, '2025-07-31 01:41:38', '2025-07-31 01:41:38'),
(70, 197, 48, 77, 'course', 0.00, 1, '2025-07-31 01:57:52', '2025-07-31 01:57:52'),
(71, 197, 47, 77, 'course', 0.00, 1, '2025-07-31 01:57:52', '2025-07-31 01:57:52'),
(72, 197, 50, 78, 'course', 0.00, 1, '2025-07-31 01:57:52', '2025-07-31 01:57:52'),
(73, 197, 51, 78, 'course', 0.00, 1, '2025-07-31 01:57:52', '2025-07-31 01:57:52'),
(74, 199, 50, 78, 'course', 0.00, 1, '2025-07-31 07:14:15', '2025-07-31 07:14:15'),
(75, 199, 51, 78, 'course', 0.00, 1, '2025-07-31 07:14:15', '2025-07-31 07:14:15'),
(76, 199, 48, 77, 'course', 0.00, 1, '2025-07-31 07:14:15', '2025-07-31 07:14:15'),
(77, 200, 52, 79, 'course', 0.00, 1, '2025-07-31 07:16:21', '2025-07-31 07:16:21'),
(78, 200, 53, 79, 'course', 0.00, 1, '2025-07-31 07:16:21', '2025-07-31 07:16:21');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_requirements`
--

CREATE TABLE `form_requirements` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `section_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_attendance_logs`
--

CREATE TABLE `meeting_attendance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('present','absent','late') NOT NULL,
  `joined_at` timestamp NULL DEFAULT NULL,
  `left_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `sender_type` varchar(50) NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `content` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2025_06_27_000000_add_registration_fields_to_students_table', 1),
(5, '2025_07_03_000003_add_director_id_to_programs_table', 2),
(6, '2025_07_03_141550_add_all_program_access_to_directors_table', 3),
(7, '2025_07_03_142031_create_director_program_pivot_table', 4),
(8, '2025_07_03_144102_add_archived_field_to_students_table', 5),
(9, '2025_07_03_160542_create_form_requirements_table', 6),
(10, '2025_07_03_160620_create_ui_settings_table', 7),
(11, '2025_07_03_163116_add_dynamic_fields_to_registrations_table', 8),
(12, '2025_07_03_163603_update_field_type_enum_in_form_requirements', 9),
(13, '2025_07_04_100222_update_form_requirements_field_type_enum', 10),
(14, '2025_07_04_100602_add_dynamic_fields_to_registrations_table', 11),
(15, '2025_07_04_120000_add_comprehensive_registration_fields', 12),
(16, '2025_07_04_120100_add_is_bold_to_form_requirements', 13),
(17, '2025_07_04_153947_add_module_selection_to_form_requirements_field_type', 14),
(18, '2025_07_06_175157_make_registration_fields_nullable', 15),
(19, '2025_07_07_144922_update_enrollment_type_complete_to_full', 16),
(21, '2025_07_08_130803_add_entity_type_to_form_requirements_table', 17),
(22, '2025_07_08_132024_fix_quizzes_table_foreign_keys', 18),
(23, '2025_07_08_144500_add_batch_id_to_enrollments_table', 19),
(24, '2025_07_09_000003_create_student_batches_table', 20),
(26, '2025_07_09_000015_fix_payment_status_in_enrollments_table', 21),
(27, '2025_07_09_000016_fix_program_type_in_form_requirements_table', 22),
(28, '2025_07_09_184647_add_batch_access_to_enrollments_table', 23),
(29, '2025_07_11_000001_add_pending_status_to_student_batches', 24),
(30, '2025_07_11_000001_add_end_date_to_batches_table', 25),
(34, '2025_07_11_135110_seed_plan_learning_mode_settings', 27),
(35, '2025_07_11_100003_add_learning_mode_config_to_plan', 28),
(36, '2025_07_12_000000_remove_undergraduate_graduate_columns', 29),
(37, '2025_07_12_142000_enhance_messages_table', 30),
(38, '2025_07_12_180254_create_chats_table', 31),
(39, '2025_07_12_204857_add_sender_role_to_messages_table', 32),
(41, '2025_07_16_181521_create_content_items_table', 33),
(42, '2025_07_17_105200_add_course_id_to_content_items_table', 34),
(43, '2025_07_17_125010_add_lesson_fields_to_lessons_table', 35),
(44, '2025_07_17_125751_update_content_type_enum_in_content_items_table', 36),
(45, '2025_07_17_200906_add_wizard_fields_to_packages_table', 37),
(47, '2025_07_19_000001_create_package_courses_table', 38),
(48, '2025_07_19_000002_add_course_selection_to_packages', 39),
(49, '2025_07_19_000003_create_enrollment_courses_table', 40),
(51, '2025_07_18_180043_create_package_modules_pivot_table', 41),
(52, '2025_07_18_182506_add_course_selection_fields_to_packages_table', 42),
(53, '2025_07_19_160000_add_course_storage_to_registrations', 43),
(54, '2025_07_19_161000_fix_enrollment_courses_table', 44),
(55, '2025_07_21_000001_create_dynamic_columns_handler', 45),
(56, '2025_01_23_000001_add_rejection_workflow_fields', 46),
(57, '2025_07_22_000000_create_payment_method_fields_table', 47),
(58, '2025_07_21_183601_add_dynamic_fields_to_payment_methods_table', 48),
(59, '2025_07_22_150000_add_file_upload_columns_to_registrations_table', 49),
(60, '2025_07_25_000001_fix_course_completions_fk', 50),
(61, '2025_07_25_202830_update_announcements_table_add_targeting_options', 51),
(62, '2025_07_27_144425_add_profile_photo_to_students_table', 52),
(63, '2025_07_30_000001_add_admin_override_to_programs_table', 53),
(64, '2025_07_26_122227_add_randomize_mc_options_to_quizzes_table', 54),
(65, '2025_07_26_132905_add_randomize_mc_options_to_quizzes_table', 55),
(66, '2025_07_26_224508_add_missing_columns_to_quizzes_table', 56),
(67, '2025_07_27_192118_add_status_to_quizzes_table', 57),
(68, '2025_07_30_191443_add_archiving_fields_to_content_items_table', 58),
(69, '2025_07_31_000000_add_multiple_files_support_to_content_items', 59),
(70, '2025_07_20_101518_create_student_progress_table', 60),
(71, '2025_07_31_013337_add_progress_tracking_to_enrollments_table', 61),
(72, '2025_07_10_173838_create_board_passers_table', 62),
(73, '2025_08_03_022856_add_profile_photo_to_professors_table', 63),
(74, '2025_08_03_023555_add_dynamic_data_to_professors_table', 64);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `modules_id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `module_description` text DEFAULT NULL,
  `program_id` int(11) NOT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `learning_mode` enum('Synchronous','Asynchronous') NOT NULL DEFAULT 'Synchronous',
  `content_type` varchar(50) NOT NULL DEFAULT '',
  `content_data` text DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `content_url` text DEFAULT NULL,
  `additional_content` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `admin_override` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'overrides set by admins (stored as JSON)' CHECK (json_valid(`admin_override`)),
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `requires_prerequisite` tinyint(1) NOT NULL DEFAULT 0,
  `prerequisite_module_id` bigint(20) UNSIGNED DEFAULT NULL,
  `release_date` timestamp NULL DEFAULT NULL,
  `completion_criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`completion_criteria`)),
  `lock_reason` varchar(255) DEFAULT NULL,
  `locked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `module_order` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`modules_id`, `module_name`, `module_description`, `program_id`, `batch_id`, `learning_mode`, `content_type`, `content_data`, `plan_id`, `attachment`, `video_path`, `content_url`, `additional_content`, `created_at`, `updated_at`, `is_archived`, `order`, `admin_override`, `is_locked`, `requires_prerequisite`, `prerequisite_module_id`, `release_date`, `completion_criteria`, `lock_reason`, `locked_by`, `module_order`) VALUES
(73, 'Modules 1', NULL, 38, NULL, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, NULL, '2025-07-25 15:33:43', '2025-07-25 15:33:43', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(74, 'Modules 2', NULL, 38, NULL, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, NULL, '2025-07-25 15:33:56', '2025-07-25 15:33:56', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(75, 'Modules 1', NULL, 39, 16, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, NULL, '2025-07-26 00:57:19', '2025-07-26 00:57:19', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(76, 'Modules 2', NULL, 39, 16, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, NULL, '2025-07-26 00:57:33', '2025-07-26 00:57:33', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(77, 'Modules 1', NULL, 40, NULL, 'Asynchronous', 'module', '[]', NULL, NULL, NULL, NULL, NULL, '2025-07-27 06:17:12', '2025-07-30 18:36:11', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 1),
(78, 'Modules 2', NULL, 40, NULL, 'Asynchronous', 'module', '[]', NULL, 'modules/1753626089_BRAVO-MANUSCRIPT.pdf', NULL, NULL, NULL, '2025-07-27 06:21:29', '2025-07-30 18:36:11', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 2),
(79, 'Modules 1', NULL, 41, NULL, 'Asynchronous', 'module', '[]', NULL, NULL, NULL, NULL, NULL, '2025-07-27 06:27:10', '2025-07-27 06:27:10', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(80, 'Modules 3', 'TEST DECRIPTION', 40, NULL, 'Synchronous', 'lesson', '{\"lesson_video_url\":null}', NULL, NULL, NULL, NULL, NULL, '2025-08-02 17:45:31', '2025-08-02 17:45:31', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `module_completions`
--

CREATE TABLE `module_completions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `program_id` int(10) UNSIGNED DEFAULT NULL,
  `modules_id` int(11) DEFAULT NULL,
  `content_id` bigint(20) UNSIGNED DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_completions`
--

INSERT INTO `module_completions` (`id`, `student_id`, `program_id`, `modules_id`, `content_id`, `completed_at`, `created_at`, `updated_at`) VALUES
(25, '2025-07-00001', 40, 78, NULL, '2025-07-31 07:17:46', '2025-07-31 07:17:46', '2025-07-31 07:17:46'),
(26, '2025-07-00001', 41, 79, NULL, '2025-07-31 07:19:22', '2025-07-31 07:19:22', '2025-07-31 07:19:22');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `program_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by_admin_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `package_type` enum('full','modular') NOT NULL DEFAULT 'full',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `selection_type` enum('module','course','both') NOT NULL DEFAULT 'module',
  `selection_mode` enum('modules','courses') NOT NULL DEFAULT 'modules',
  `module_count` int(11) DEFAULT 0,
  `course_count` int(11) DEFAULT NULL,
  `min_courses` int(11) DEFAULT NULL,
  `max_courses` int(11) DEFAULT NULL,
  `allowed_modules` int(11) NOT NULL DEFAULT 2,
  `allowed_courses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_courses`)),
  `extra_module_price` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `access_period_days` int(11) DEFAULT NULL,
  `access_period_months` int(11) DEFAULT NULL,
  `access_period_years` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `package_name`, `description`, `amount`, `program_id`, `created_by_admin_id`, `created_at`, `updated_at`, `package_type`, `status`, `selection_type`, `selection_mode`, `module_count`, `course_count`, `min_courses`, `max_courses`, `allowed_modules`, `allowed_courses`, `extra_module_price`, `price`, `access_period_days`, `access_period_months`, `access_period_years`) VALUES
(29, 'Full Package', 'Tkae the full potential!', 399.00, NULL, 1, '2025-07-23 14:05:01', '2025-07-23 14:05:01', 'full', 'active', 'module', 'modules', NULL, NULL, NULL, NULL, 2, NULL, NULL, 399.00, NULL, 8, NULL),
(33, 'Modular', 'Modular', 250.00, NULL, 1, '2025-07-25 06:01:16', '2025-07-25 06:01:16', 'modular', 'active', 'module', 'modules', NULL, NULL, NULL, NULL, 2, NULL, NULL, 250.00, NULL, 1, NULL),
(34, 'Package 33', 'w', 2.00, NULL, 1, '2025-07-25 08:13:59', '2025-07-25 08:13:59', 'modular', 'active', 'module', 'modules', NULL, NULL, NULL, NULL, 2, NULL, NULL, 2.00, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `package_courses`
--

CREATE TABLE `package_courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_modules`
--

CREATE TABLE `package_modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `modules_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method` enum('credit_card','gcash','bank_transfer','cash','admin_marked') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed','cancelled','rejected','resubmitted') DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejected_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rejected_fields`)),
  `resubmitted_at` timestamp NULL DEFAULT NULL,
  `resubmission_count` int(11) DEFAULT 0,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `receipt_number` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `enrollment_id`, `student_id`, `program_id`, `package_id`, `payment_method`, `amount`, `payment_status`, `rejection_reason`, `rejected_by`, `rejected_at`, `rejected_fields`, `resubmitted_at`, `resubmission_count`, `payment_details`, `verified_by`, `verified_at`, `receipt_number`, `reference_number`, `notes`, `created_at`, `updated_at`) VALUES
(15, 200, '2025-07-00001', 41, 34, 'gcash', 2.00, 'paid', NULL, NULL, NULL, NULL, NULL, 0, '\"{\\\"payment_proof_path\\\":\\\"payment_proofs\\\\\\/payment_proof_200_1753974992.png\\\",\\\"reference_number\\\":null,\\\"payment_method_name\\\":\\\"GCash\\\",\\\"uploaded_at\\\":\\\"2025-07-31T15:16:32.879586Z\\\"}\"', 1, '2025-07-31 07:16:40', NULL, NULL, 'Payment proof uploaded by student', '2025-07-31 07:16:32', '2025-07-31 07:16:40');

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `payment_history_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `program_id` int(10) UNSIGNED NOT NULL,
  `package_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded','cancelled','processing') DEFAULT NULL,
  `payment_method` enum('cash','card','bank_transfer','gcash','manual','other') DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `processed_by_admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`payment_history_id`, `enrollment_id`, `user_id`, `student_id`, `program_id`, `package_id`, `amount`, `payment_status`, `payment_method`, `payment_notes`, `payment_date`, `processed_by_admin_id`, `created_at`, `updated_at`) VALUES
(1, 199, 1, '2025-07-00001', 40, 34, NULL, 'paid', 'manual', 'Payment marked as paid by administrator', '2025-07-31 07:14:38', 1, '2025-07-31 07:14:38', '2025-07-31 07:14:38');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `payment_method_id` bigint(20) UNSIGNED NOT NULL,
  `method_name` varchar(255) NOT NULL,
  `method_type` enum('credit_card','gcash','maya','bank_transfer','cash','other') NOT NULL,
  `description` text DEFAULT NULL,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `dynamic_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dynamic_fields`)),
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_by_admin_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`payment_method_id`, `method_name`, `method_type`, `description`, `qr_code_path`, `instructions`, `dynamic_fields`, `is_enabled`, `sort_order`, `created_by_admin_id`, `created_at`, `updated_at`) VALUES
(11, 'GCash', 'gcash', 'Mobile payment via GCash', 'payment_qr_codes/2B4tBsCsUZHFeHztpE0iVg7y5YLeWnCMGGFZfYH6.png', NULL, NULL, 1, 0, 0, NULL, '2025-07-22 04:59:48');

-- --------------------------------------------------------

--
-- Table structure for table `payment_method_fields`
--

CREATE TABLE `payment_method_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_method_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_type` enum('text','number','date','file','textarea','select') NOT NULL,
  `field_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`field_options`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_method_fields`
--

INSERT INTO `payment_method_fields` (`id`, `payment_method_id`, `field_name`, `field_label`, `field_type`, `field_options`, `is_required`, `sort_order`, `created_at`, `updated_at`) VALUES
(18, 11, 'gcash_reference_number', 'Gcash Reference Number', 'number', NULL, 0, 0, '2025-07-21 23:21:19', '2025-07-21 23:21:19');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan`
--

CREATE TABLE `plan` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `enable_synchronous` tinyint(1) NOT NULL DEFAULT 1,
  `enable_asynchronous` tinyint(1) NOT NULL DEFAULT 1,
  `learning_mode_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`learning_mode_config`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plan`
--

INSERT INTO `plan` (`plan_id`, `plan_name`, `description`, `enable_synchronous`, `enable_asynchronous`, `learning_mode_config`) VALUES
(1, 'Full Plan', 'Full/Complete Plan Description', 1, 1, NULL),
(2, 'Modular Plan', 'Modular Plan Description', 0, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `professor_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `professor_name` varchar(100) NOT NULL,
  `professor_first_name` varchar(100) NOT NULL,
  `professor_last_name` varchar(100) NOT NULL,
  `professor_email` varchar(100) NOT NULL,
  `professor_password` varchar(255) NOT NULL,
  `referral_code` varchar(20) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `dynamic_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dynamic_data`)),
  `professor_archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`professor_id`, `admin_id`, `professor_name`, `professor_first_name`, `professor_last_name`, `professor_email`, `professor_password`, `referral_code`, `profile_photo`, `dynamic_data`, `professor_archived`, `created_at`, `updated_at`) VALUES
(8, 1, 'robert SANS', 'robert', 'SANS', 'robert@gmail.com', '$2y$10$VoMnQqIuvSditKfIjnEoNuMWmB1FmLLOgaeTWvqNpld78kK/LfB3K', 'PROF08RSAN', 'profile_photos/JFuMcwmIxxFGaTySFx2BoovXUl0jVjE2hxpdjKJc.jpg', '{\"test\":\"value\"}', 0, '2025-07-09 12:02:17', '2025-08-02 18:41:21');

-- --------------------------------------------------------

--
-- Table structure for table `professor_batch`
--

CREATE TABLE `professor_batch` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `professor_batch`
--

INSERT INTO `professor_batch` (`id`, `batch_id`, `professor_id`, `assigned_at`, `assigned_by`, `created_at`, `updated_at`) VALUES
(1, 9, 8, NULL, NULL, '2025-07-20 11:32:15', '2025-07-20 11:32:15'),
(2, 8, 8, NULL, NULL, '2025-07-20 11:39:04', '2025-07-20 11:39:04'),
(3, 12, 8, NULL, NULL, '2025-07-20 13:28:41', '2025-07-20 13:28:41'),
(4, 7, 8, NULL, NULL, '2025-07-21 05:22:04', '2025-07-21 05:22:04'),
(5, 13, 8, '2025-07-21 05:22:28', NULL, '2025-07-21 05:22:28', '2025-07-21 05:22:28'),
(6, 14, 8, '2025-07-24 05:24:25', NULL, '2025-07-24 05:24:25', '2025-07-24 05:24:25'),
(7, 15, 8, '2025-07-25 05:10:11', NULL, '2025-07-25 05:10:11', '2025-07-25 05:10:11'),
(8, 16, 8, '2025-07-25 05:53:50', NULL, '2025-07-25 05:53:50', '2025-07-25 05:53:50');

-- --------------------------------------------------------

--
-- Table structure for table `professor_program`
--

CREATE TABLE `professor_program` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `video_link` varchar(255) DEFAULT NULL,
  `video_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `professor_program`
--

INSERT INTO `professor_program` (`id`, `professor_id`, `program_id`, `video_link`, `video_description`, `created_at`, `updated_at`) VALUES
(15, 8, 40, NULL, NULL, '2025-07-27 08:04:48', '2025-07-27 08:04:48'),
(16, 8, 41, NULL, NULL, '2025-07-27 08:04:48', '2025-07-27 08:04:48');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `created_by_admin_id` int(11) NOT NULL,
  `director_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` int(11) NOT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `program_description` text DEFAULT NULL,
  `admin_override` longtext DEFAULT NULL COMMENT 'overrides set by admins (stored as JSON)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `program_name`, `created_by_admin_id`, `director_id`, `created_at`, `updated_at`, `is_active`, `is_archived`, `program_description`, `admin_override`) VALUES
(40, 'Nursing', 1, NULL, '2025-07-27 05:44:50', '2025-07-27 05:44:50', 1, 0, 'Nurse', NULL),
(41, 'Mechanical Engineer', 1, NULL, '2025-07-27 06:26:58', '2025-07-27 06:26:58', 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `module_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quiz_title` varchar(255) NOT NULL,
  `instructions` text DEFAULT NULL,
  `quiz_description` text DEFAULT NULL,
  `total_questions` int(11) NOT NULL DEFAULT 10,
  `time_limit` int(11) NOT NULL DEFAULT 60,
  `document_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `allow_retakes` tinyint(1) NOT NULL DEFAULT 0,
  `infinite_retakes` tinyint(1) DEFAULT 0,
  `instant_feedback` tinyint(1) NOT NULL DEFAULT 0,
  `show_correct_answers` tinyint(1) NOT NULL DEFAULT 1,
  `max_attempts` int(11) DEFAULT NULL,
  `has_deadline` tinyint(1) DEFAULT 0,
  `due_date` datetime DEFAULT NULL,
  `is_draft` tinyint(1) NOT NULL DEFAULT 0,
  `randomize_order` tinyint(1) NOT NULL DEFAULT 0,
  `randomize_mc_options` tinyint(1) NOT NULL DEFAULT 0,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `professor_id`, `admin_id`, `program_id`, `module_id`, `course_id`, `content_id`, `quiz_title`, `instructions`, `quiz_description`, `total_questions`, `time_limit`, `document_path`, `is_active`, `status`, `allow_retakes`, `infinite_retakes`, `instant_feedback`, `show_correct_answers`, `max_attempts`, `has_deadline`, `due_date`, `is_draft`, `randomize_order`, `randomize_mc_options`, `tags`, `created_at`, `updated_at`) VALUES
(45, 8, NULL, 40, 77, 48, 91, 'Test', 'Please answer the following questions.', '1', 1, 60, NULL, 0, 'draft', 0, 1, 0, 1, 999, 1, '2025-08-25 22:27:00', 1, 0, 0, NULL, '2025-08-02 14:25:56', '2025-08-02 15:48:14'),
(47, 8, NULL, 40, 78, 50, 93, 'qweqwe', 'Please answer the following questions.', 'qeweqw', 1, 60, NULL, 0, 'draft', 0, 0, 0, 1, 1, 0, NULL, 1, 0, 0, NULL, '2025-08-02 14:43:18', '2025-08-02 17:00:39'),
(48, 8, NULL, 40, 77, 47, 94, 'THIS IS PROFESSOR', 'Please answer the following questions.', 'NANANANA', 1, 60, NULL, 0, 'draft', 0, 0, 0, 1, 1, 0, NULL, 1, 0, 0, NULL, '2025-08-02 14:53:20', '2025-08-02 16:55:27'),
(49, NULL, 1, 41, 79, 52, 95, 'qwe', 'Please answer the following questions.', 'qweqwe', 1, 60, NULL, 1, 'published', 0, 1, 0, 1, 999, 1, '2025-08-04 17:28:00', 0, 0, 0, NULL, '2025-08-02 15:28:48', '2025-08-02 15:46:49'),
(50, NULL, 1, 41, 79, 53, 96, 'ww', 'Please answer the following questions.', 'qweqwe', 1, 60, NULL, 1, 'published', 0, 0, 0, 1, 1, 0, NULL, 0, 0, 0, NULL, '2025-08-02 15:48:39', '2025-08-02 15:55:50'),
(54, 8, NULL, 41, 79, 52, 97, 'asdas', 'Please answer the following questions.', 'dasdasd', 1, 60, NULL, 0, 'draft', 0, 0, 0, 1, 1, 0, NULL, 1, 0, 0, NULL, '2025-08-02 16:36:46', '2025-08-02 16:36:46'),
(55, NULL, 1, 41, 79, 52, 98, 'qweqw', 'Please answer the following questions.', 'eqweqwe', 1, 60, NULL, 1, 'published', 0, 0, 0, 1, 1, 0, NULL, 0, 0, 0, NULL, '2025-08-02 16:47:31', '2025-08-02 16:47:37'),
(56, NULL, 1, 41, 79, 52, 99, 'ssss', 'Please answer the following questions.', 'ssss', 1, 60, NULL, 0, 'draft', 0, 1, 0, 1, 999, 0, NULL, 1, 0, 0, NULL, '2025-08-02 16:51:52', '2025-08-02 16:51:52'),
(57, 8, NULL, 41, 79, 52, 100, 'asdasdasaaa', 'Please answer the following questions.', 'dasdasd', 1, 60, NULL, 1, 'published', 0, 1, 0, 1, 999, 0, NULL, 0, 0, 0, NULL, '2025-08-02 16:52:17', '2025-08-02 16:55:33'),
(58, NULL, 1, 40, 78, 50, 101, 'AI?', 'Please answer the following questions.', 'AI?', 10, 60, NULL, 1, 'published', 0, 0, 0, 1, 1, 0, NULL, 0, 0, 0, NULL, '2025-08-02 16:53:00', '2025-08-02 17:00:52'),
(59, NULL, 1, 41, 79, 52, 102, 'asdasd', 'Please answer the following questions.', 'asdasd', 1, 60, NULL, 0, 'draft', 0, 0, 0, 1, 1, 0, NULL, 1, 0, 0, NULL, '2025-08-02 17:01:09', '2025-08-02 17:01:09');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `attempt_id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`answers`)),
  `score` decimal(5,2) DEFAULT NULL,
  `total_questions` int(11) NOT NULL,
  `correct_answers` int(11) NOT NULL DEFAULT 0,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `time_taken` int(11) DEFAULT NULL,
  `status` enum('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_options`
--

CREATE TABLE `quiz_options` (
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `quiz_title` varchar(255) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','short_answer','essay') NOT NULL,
  `question_order` int(11) DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `correct_answer` text DEFAULT '',
  `explanation` text DEFAULT NULL,
  `question_source` enum('generated','manual','quizapi') NOT NULL DEFAULT 'generated',
  `question_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`question_metadata`)),
  `instructions` text DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `source_file` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by_admin` int(11) DEFAULT NULL,
  `created_by_professor` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `quiz_title`, `program_id`, `question_text`, `question_type`, `question_order`, `options`, `correct_answer`, `explanation`, `question_source`, `question_metadata`, `instructions`, `points`, `source_file`, `is_active`, `created_by_admin`, `created_by_professor`, `created_at`, `updated_at`) VALUES
(395, 45, 'Test', 40, 'qweqwe', 'multiple_choice', NULL, '[\"ww\",\"ww\",\"w\",\"w\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, 8, '2025-08-02 14:25:56', '2025-08-02 14:25:56'),
(397, 47, 'qweqwe', 40, 'qweqweqewwqe', 'multiple_choice', NULL, '[\"qw\",\"eqwe\",\"qweqwe\",\"qweqwe\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, 8, '2025-08-02 14:43:18', '2025-08-02 14:43:18'),
(398, 48, 'THIS IS PROFESSOR', 40, 'PROF', 'multiple_choice', NULL, '[\"PROF\",\"PROF\",\"PROF\",\"PRFO\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, 8, '2025-08-02 14:53:20', '2025-08-02 14:53:20'),
(399, 49, 'qwe', 41, 'qweqwe', 'multiple_choice', NULL, '[\"Option A\",\"Option B\",\"Option Cqweqwe\",\"Option D\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 15:28:48', '2025-08-02 15:28:48'),
(402, 50, 'ww', 41, 'qqeqweqweqe', 'multiple_choice', NULL, '[\"Option AAAA\",\"Option B\",\"Option C\",\"Option D\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 15:55:50', '2025-08-02 15:55:50'),
(403, 54, 'asdas', 41, 'asdasdasd', 'multiple_choice', NULL, '[\"asdas\",\"adsasd\",\"dasdas\",\"dasdasdasd\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, 8, '2025-08-02 16:36:46', '2025-08-02 16:36:46'),
(404, 55, 'qweqw', 41, 'qweqwe', 'multiple_choice', NULL, '[\"Option A\",\"Option B\",\"Option C\",\"Option D\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:47:31', '2025-08-02 16:47:31'),
(405, 56, 'ssss', 41, 'asdsadssss', 'multiple_choice', NULL, '[\"Option A\",\"Option B\",\"Option C\",\"Option D\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:51:52', '2025-08-02 16:51:52'),
(406, 57, 'asdasdasaaa', 41, 'sadsd', 'multiple_choice', NULL, '[\"asda\",\"sdas\",\"sssaa\",\"sss\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, 8, '2025-08-02 16:52:17', '2025-08-02 16:52:17'),
(407, 58, 'AI?', 40, 'What is stress (S) defined as in the context of machine elements?', 'multiple_choice', NULL, '[\"The total force applied to a material.\",\"The total deformation a material undergoes under load.\",\"The total resistance a material offers to an applied load.\",\"The maximum stress a material can withstand before failure.\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(408, 58, 'AI?', 40, 'How is allowable stress (S<sub>allow</sub>) calculated?', 'multiple_choice', NULL, '[\"S<sub>allow<\\/sub> = Yield Stress \\/ Factor of Safety\",\"S<sub>allow<\\/sub> = Ultimate Stress \\/ Factor of Safety\",\"S<sub>allow<\\/sub> = Design Stress \\/ Factor of Safety\",\"S<sub>allow<\\/sub> = Tensile Stress \\/ Factor of Safety\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(409, 58, 'AI?', 40, 'For a solid circular cross-section, how is tensile stress (S<sub>t</sub>) calculated?', 'multiple_choice', NULL, '[\"S<sub>t<\\/sub> = F \\/ ( D\\u00b2\\/4)\",\"S<sub>t<\\/sub> = F \\/ ( D\\u00b2)\",\"S<sub>t<\\/sub> = 4F \\/ ( D\\u00b2)\",\"S<sub>t<\\/sub> = F \\/ (4 D\\u00b2)\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(410, 58, 'AI?', 40, 'What is the formula for shearing stress (S<sub>s</sub>) in a single bolt or rivet joining two plates?', 'multiple_choice', NULL, '[\"S<sub>s<\\/sub> = F \\/ (2 D\\u00b2)\",\"S<sub>s<\\/sub> = F \\/ ( D\\u00b2\\/4)\",\"S<sub>s<\\/sub> = F \\/ ( D\\u00b2)\",\"S<sub>s<\\/sub> = 2F \\/ ( D\\u00b2\\/4)\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(411, 58, 'AI?', 40, 'How is bearing stress (S<sub>b</sub>) calculated?', 'multiple_choice', NULL, '[\"S<sub>b<\\/sub> = F<sub>b<\\/sub> \\/ (D + L)\",\"S<sub>b<\\/sub> = F<sub>b<\\/sub> \\/ (DL)\",\"S<sub>b<\\/sub> = F<sub>b<\\/sub> \\/ (D\\/L)\",\"S<sub>b<\\/sub> = F<sub>b<\\/sub> \\/ (L\\/D)\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(412, 58, 'AI?', 40, 'The formula for torsional shear stress (S<sub>s</sub>) in a solid shaft is:', 'multiple_choice', NULL, '[\"S<sub>s<\\/sub> = 16T \\/ D\\u00b3\",\"S<sub>s<\\/sub> = 16T \\/ D\",\"S<sub>s<\\/sub> = 16T \\/ ( D )\",\"S<sub>s<\\/sub> = 16T \\/ ( D\\u00b3)\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(413, 58, 'AI?', 40, 'In the context of bending stress (S<sub>f</sub>) for a rectangular beam, what does \'c\' represent?', 'multiple_choice', NULL, '[\"The distance of the neutral axis from the farthest fiber.\",\"The length of the beam.\",\"The width of the beam.\",\"The height of the beam.\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(414, 58, 'AI?', 40, 'What is Poisson\'s Ratio ( )?', 'multiple_choice', NULL, '[\"The ratio of axial unit deformation to lateral unit deformation.\",\"The ratio of lateral unit deformation to axial unit deformation.\",\"The ratio of shear stress to tensile stress.\",\"The ratio of tensile stress to compressive stress.\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(415, 58, 'AI?', 40, 'For cylinders rolling in opposite directions, the center distance is calculated as:', 'multiple_choice', NULL, '[\"(D  + D ) \\/ 2\",\"(D  - D ) \\/ 2\",\"(D  + D ) \\/ 4\",\"(D  - D ) \\/ 4\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(416, 58, 'AI?', 40, 'According to the notes, what determines the direction of rotation of a driven cylinder with multiple gears and idlers?', 'multiple_choice', NULL, '[\"The number of idlers.\",\"The size of the gears.\",\"Whether the number of gears and idlers is even or odd.\",\"The friction loss in the system.   **III.\"]', 'A', 'Based on the document content.', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 16:53:00', '2025-08-02 16:53:00'),
(417, 59, 'asdasd', 41, 'asdasd', 'multiple_choice', NULL, '[\"Option Aasdasdasd\",\"Option Bdasd\",\"Option Casdas\",\"Option Dasdasd\"]', '0', '', 'manual', NULL, NULL, 1, NULL, 1, NULL, NULL, '2025-08-02 17:01:09', '2025-08-02 17:01:09');

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `referral_id` bigint(20) UNSIGNED NOT NULL,
  `referral_code` varchar(20) NOT NULL,
  `referrer_type` enum('director','professor') NOT NULL,
  `referrer_id` int(11) NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `registration_id` int(11) UNSIGNED NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) UNSIGNED DEFAULT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `plan_id` int(11) UNSIGNED DEFAULT NULL,
  `plan_name` varchar(255) DEFAULT NULL,
  `program_id` int(11) UNSIGNED DEFAULT NULL,
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
  `status` enum('pending','approved','rejected','resubmitted') DEFAULT 'pending',
  `approved_by` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `undo_reason` text DEFAULT NULL,
  `undone_at` timestamp NULL DEFAULT NULL,
  `undone_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `rejected_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rejected_fields`)),
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `resubmitted_at` timestamp NULL DEFAULT NULL,
  `original_submission` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`original_submission`)),
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
  `selected_courses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_courses`)),
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
  `school_id` varchar(255) DEFAULT NULL,
  `diploma` varchar(255) DEFAULT NULL,
  `valid_school_identification` varchar(255) DEFAULT NULL,
  `transcript_of_records` varchar(255) DEFAULT NULL,
  `certificate_of_good_moral_character` varchar(255) DEFAULT NULL,
  `psa_birth_certificate` varchar(255) DEFAULT NULL,
  `transcript_records` varchar(255) DEFAULT NULL,
  `moral_certificate` varchar(255) DEFAULT NULL,
  `birth_cert` varchar(255) DEFAULT NULL,
  `id_photo` varchar(255) DEFAULT NULL,
  `barangay_clearance` varchar(255) DEFAULT NULL,
  `police_clearance` varchar(255) DEFAULT NULL,
  `nbi_clearance` varchar(255) DEFAULT NULL,
  `form_137` varchar(255) DEFAULT NULL COMMENT 'Form 137 document path'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `user_id`, `package_id`, `package_name`, `plan_id`, `plan_name`, `program_id`, `program_name`, `enrollment_type`, `learning_mode`, `firstname`, `middlename`, `lastname`, `student_school`, `street_address`, `state_province`, `city`, `zipcode`, `contact_number`, `emergency_contact_number`, `good_moral`, `PSA`, `Course_Cert`, `TOR`, `Cert_of_Grad`, `dynamic_fields`, `photo_2x2`, `Start_Date`, `status`, `approved_by`, `approved_at`, `undo_reason`, `undone_at`, `undone_by`, `rejection_reason`, `rejected_fields`, `rejected_by`, `rejected_at`, `resubmitted_at`, `original_submission`, `created_at`, `updated_at`, `phone_number`, `telephone_number`, `religion`, `citizenship`, `civil_status`, `birthdate`, `gender`, `work_experience`, `preferred_schedule`, `emergency_contact_relationship`, `health_conditions`, `disability_support`, `valid_id`, `birth_certificate`, `diploma_certificate`, `medical_certificate`, `passport_photo`, `parent_guardian_name`, `parent_guardian_contact`, `previous_school`, `graduation_year`, `course_taken`, `special_needs`, `scholarship_program`, `employment_status`, `monthly_income`, `school_name`, `selected_modules`, `selected_courses`, `test_field_auto`, `testering`, `master`, `bagit`, `real`, `test_auto_column_1752439854`, `nyan`, `education_level`, `sync_async_mode`, `Test`, `last_name`, `referral_code`, `school_id`, `diploma`, `valid_school_identification`, `transcript_of_records`, `certificate_of_good_moral_character`, `psa_birth_certificate`, `transcript_records`, `moral_certificate`, `birth_cert`, `id_photo`, `barangay_clearance`, `police_clearance`, `nbi_clearance`, `form_137`) VALUES
(1, 1, 34, 'Package 33', NULL, NULL, 40, 'Nursing', 'Modular', 'asynchronous', 'Vince Michael', '', 'Dela Vega', NULL, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, 'uploads/education_requirements/1753974831_Vince _Certificate (1).pdf', '\"{\\\"referral_code\\\":\\\"\\\",\\\"registration_mode\\\":\\\"asynchronous\\\"}\"', NULL, '2025-07-31', 'approved', NULL, '2025-07-31 07:14:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-31 07:13:51', '2025-08-02 17:44:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'uploads/education_requirements/1753974831_1753907633_final-docu.pdf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"[{\\\"id\\\":78,\\\"name\\\":\\\"Modules 22 courses selected\\\",\\\"selected_courses\\\":[\\\"50\\\",\\\"51\\\"]},{\\\"id\\\":77,\\\"name\\\":\\\"Modules 11 course selected\\\",\\\"selected_courses\\\":[\\\"48\\\"]}]\"', '\"[\\\"50\\\",\\\"51\\\",\\\"48\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Graduate', 'sync', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 34, 'Package 33', NULL, NULL, 41, 'Mechanical Engineer', 'Modular', 'asynchronous', 'Vince Michael', NULL, 'Dela Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"{\\\"referral_code\\\":\\\"\\\",\\\"registration_mode\\\":\\\"asynchronous\\\"}\"', NULL, '2025-07-31', 'approved', NULL, '2025-07-31 07:16:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-31 07:16:10', '2025-07-31 07:16:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"[{\\\"id\\\":79,\\\"name\\\":\\\"Modules 12 courses selected\\\",\\\"selected_courses\\\":[\\\"52\\\",\\\"53\\\"]},{\\\"id\\\":\\\"79\\\",\\\"name\\\":\\\"Modules 12 courses selected\\\",\\\"selected_courses\\\":[\\\"52\\\",\\\"53\\\"]}]\"', '\"[\\\"52\\\",\\\"53\\\",\\\"52\\\",\\\"53\\\"]\"', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Graduate', 'sync', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

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
  `profile_photo` varchar(255) DEFAULT NULL,
  `Start_Date` date DEFAULT NULL,
  `date_approved` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(100) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `package_id` int(11) UNSIGNED DEFAULT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `plan_id` int(11) UNSIGNED DEFAULT NULL,
  `plan_name` varchar(255) DEFAULT NULL,
  `program_id` int(11) UNSIGNED DEFAULT NULL,
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
  `school_id` varchar(255) DEFAULT NULL,
  `diploma` varchar(255) DEFAULT NULL,
  `valid_school_identification` varchar(255) DEFAULT NULL,
  `transcript_of_records` varchar(255) DEFAULT NULL,
  `certificate_of_good_moral_character` varchar(255) DEFAULT NULL,
  `psa_birth_certificate` varchar(255) DEFAULT NULL,
  `transcript_records` varchar(255) DEFAULT NULL,
  `moral_certificate` varchar(255) DEFAULT NULL,
  `birth_cert` varchar(255) DEFAULT NULL,
  `id_photo` varchar(255) DEFAULT NULL,
  `barangay_clearance` varchar(255) DEFAULT NULL,
  `police_clearance` varchar(255) DEFAULT NULL,
  `nbi_clearance` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `firstname`, `middlename`, `lastname`, `student_school`, `street_address`, `state_province`, `city`, `zipcode`, `contact_number`, `emergency_contact_number`, `good_moral`, `PSA`, `Course_Cert`, `TOR`, `Cert_of_Grad`, `photo_2x2`, `profile_photo`, `Start_Date`, `date_approved`, `created_at`, `updated_at`, `email`, `is_archived`, `package_id`, `package_name`, `plan_id`, `plan_name`, `program_id`, `education_level`, `program_name`, `enrollment_type`, `learning_mode`, `Undergraduate`, `Graduate`, `dynamic_fields`, `status`, `phone_number`, `telephone_number`, `religion`, `citizenship`, `civil_status`, `birthdate`, `gender`, `work_experience`, `preferred_schedule`, `emergency_contact_relationship`, `health_conditions`, `disability_support`, `valid_id`, `birth_certificate`, `diploma_certificate`, `medical_certificate`, `passport_photo`, `parent_guardian_name`, `parent_guardian_contact`, `referral_code`, `ama_namin`, `previous_school`, `graduation_year`, `course_taken`, `special_needs`, `scholarship_program`, `employment_status`, `monthly_income`, `school_name`, `selected_modules`, `test_field_auto`, `testering`, `master`, `bagit`, `real`, `test_auto_column_1752439854`, `nyan`, `Test`, `last_name`, `school_id`, `diploma`, `valid_school_identification`, `transcript_of_records`, `certificate_of_good_moral_character`, `psa_birth_certificate`, `transcript_records`, `moral_certificate`, `birth_cert`, `id_photo`, `barangay_clearance`, `police_clearance`, `nbi_clearance`) VALUES
('2025-07-00001', 1, 'Vince Michael', '', 'Dela Vega', NULL, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'profile_1_1754156650.jpg', NULL, '2025-07-31 07:14:15', '2025-07-31 07:14:15', '2025-08-02 17:44:10', 'vince03handsome11@gmail.com', 0, 34, 'Package 33', NULL, NULL, 40, 'Graduate', 'Nursing', 'Modular', 'asynchronous', 0, 0, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_batches`
--

CREATE TABLE `student_batches` (
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `batch_name` varchar(255) NOT NULL,
  `program_id` int(11) NOT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `max_capacity` int(11) NOT NULL,
  `current_capacity` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `batch_status` enum('pending','available','ongoing','closed','completed') DEFAULT 'pending',
  `registration_deadline` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_batches`
--

INSERT INTO `student_batches` (`batch_id`, `batch_name`, `program_id`, `professor_id`, `max_capacity`, `current_capacity`, `is_active`, `batch_status`, `registration_deadline`, `start_date`, `end_date`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(16, 'Batch 1 Civil Engineer', 39, 8, 100, 0, 1, 'ongoing', '2025-08-07', '2025-07-20', NULL, NULL, NULL, '2025-07-25 05:53:50', '2025-07-25 05:53:50');

-- --------------------------------------------------------

--
-- Table structure for table `student_grades`
--

CREATE TABLE `student_grades` (
  `grade_id` bigint(20) UNSIGNED NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `student_progress`
--

CREATE TABLE `student_progress` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `content_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `module_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ui_settings`
--

CREATE TABLE `ui_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `section` varchar(255) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_type` enum('color','file','text','boolean','json') NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ui_settings`
--

INSERT INTO `ui_settings` (`id`, `section`, `setting_key`, `setting_value`, `setting_type`, `created_at`, `updated_at`) VALUES
(1, 'navbar', 'header_bg', '#ffffff', 'color', '2025-07-03 08:17:32', '2025-07-22 05:25:15'),
(2, 'navbar', 'header_text', '#d23737', 'color', '2025-07-03 08:17:32', '2025-07-22 05:25:15'),
(3, 'navbar', 'header_border', '#e0e0e0', 'color', '2025-07-03 08:17:32', '2025-07-14 23:18:10'),
(4, 'navbar', 'search_bg', '#f8f9fa', 'color', '2025-07-03 08:17:32', '2025-07-03 08:37:56'),
(5, 'navbar', 'sidebar_bg', '#343a40', 'color', '2025-07-03 08:17:32', '2025-07-22 02:57:33'),
(6, 'navbar', 'sidebar_text', '#ffffff', 'color', '2025-07-03 08:17:32', '2025-07-03 08:37:56'),
(7, 'navbar', 'active_link_bg', '#007bff', 'color', '2025-07-03 08:17:32', '2025-07-22 02:59:14'),
(8, 'navbar', 'active_link_text', '#ffffff', 'color', '2025-07-03 08:17:32', '2025-07-03 08:17:32'),
(9, 'navbar', 'hover_bg', '#c8147d', 'color', '2025-07-03 08:17:32', '2025-07-04 01:45:15'),
(10, 'navbar', 'hover_text', '#ffffff', 'color', '2025-07-03 08:17:32', '2025-07-03 08:17:32'),
(11, 'navbar', 'submenu_bg', '#2c3034', 'color', '2025-07-03 08:17:32', '2025-07-03 08:17:32'),
(12, 'navbar', 'submenu_text', '#18212a', 'color', '2025-07-03 08:17:32', '2025-07-04 01:46:07'),
(13, 'navbar', 'footer_bg', '#237ad1', 'color', '2025-07-03 08:17:32', '2025-07-03 09:24:31'),
(14, 'navbar', 'icon_color', '#6c757d', 'color', '2025-07-03 08:17:32', '2025-07-03 08:37:56'),
(15, 'global', 'logo_url', 'http://localhost/images/ARTC_logo.png', 'file', '2025-07-03 08:37:56', '2025-07-03 08:37:56'),
(16, 'global', 'favicon_url', 'http://localhost/favicon.ico', 'file', '2025-07-03 08:37:56', '2025-07-03 08:37:56'),
(17, 'global', 'site_title', 'A.R.T.C', 'text', '2025-07-03 08:37:56', '2025-07-03 08:37:56'),
(18, 'footer', 'footer_bg_color', '#ff1fe1', 'color', '2025-07-06 08:54:51', '2025-07-22 05:24:08'),
(19, 'footer', 'footer_text_color', '#ffffff', 'color', '2025-07-06 08:54:51', '2025-07-06 08:54:51'),
(20, 'footer', 'footer_text', '© Copyright Ascendo Review and Training Center. All Rights Reserved.', 'text', '2025-07-06 08:54:51', '2025-07-06 08:54:51'),
(21, 'footer', 'footer_link_color', '#adb5bd', 'color', '2025-07-06 08:54:51', '2025-07-15 23:47:44'),
(22, 'footer', 'footer_link_hover_color', '#ffffff', 'color', '2025-07-06 08:54:51', '2025-07-06 08:54:51'),
(23, 'homepage', 'hero_bg_color', '#29d1b5', 'color', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(24, 'homepage', 'hero_text_color', '#ffffff', 'color', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(25, 'homepage', 'hero_title', 'Review Smarter. Learn Better. Succeed Faster.', 'text', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(26, 'homepage', 'hero_subtitle', 'At Ascendo Review and Training Center, we guide future licensed professionals toward exam success with expert-led reviews and flexible learning options.', 'text', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(27, 'homepage', 'hero_button_text', 'ENROLL NOW', 'text', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(28, 'homepage', 'hero_button_color', '#4caf50', 'color', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(29, 'homepage', 'programs_bg_color', '#f8f9fa', 'color', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(30, 'homepage', 'programs_text_color', '#333333', 'color', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(31, 'homepage', 'programs_title', 'Programs Offered', 'text', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(32, 'homepage', 'programs_subtitle', 'Choose from our comprehensive review programs designed for success', 'text', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(33, 'homepage', 'modalities_bg_color', '#667eea', 'color', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(34, 'homepage', 'modalities_text_color', '#ffffff', 'color', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(35, 'homepage', 'modalities_title', 'Learning Modalities', 'text', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(36, 'homepage', 'modalities_subtitle', 'Choose the learning style that works best for you', 'text', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(37, 'homepage', 'about_bg_color', '#ffffff', 'color', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(38, 'homepage', 'about_text_color', '#333333', 'color', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(39, 'homepage', 'about_title', 'About Us', 'text', '2025-07-06 10:00:27', '2025-07-06 10:00:27'),
(40, 'homepage', 'about_subtitle', 'Learn more about our mission and values', 'text', '2025-07-06 10:00:27', '2025-07-06 10:00:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `directors_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `last_seen` timestamp NULL DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_lastname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('unverified','student','professor') DEFAULT 'unverified',
  `enrollment_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `admin_id`, `directors_id`, `is_online`, `last_seen`, `email`, `user_firstname`, `user_lastname`, `password`, `role`, `enrollment_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 0, NULL, 'vince03handsome11@gmail.com', 'Vince Michael', 'Dela Vega', '$2y$10$M.UIDtIlXTUCaIrz/MF8W.DD0mCXhTHdlVKTMvqexuT0gxdxHbpWS', 'student', 200, '2025-07-31 07:13:51', '2025-07-31 07:16:10');

-- --------------------------------------------------------

--
-- Table structure for table `websockets_statistics_entries`
--

CREATE TABLE `websockets_statistics_entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `app_id` varchar(255) NOT NULL,
  `peak_connection_count` int(11) NOT NULL,
  `websocket_message_count` int(11) NOT NULL,
  `api_message_count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`program_id`,`attendance_date`);

--
-- Indexes for table `batch_professors`
--
ALTER TABLE `batch_professors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `board_passers`
--
ALTER TABLE `board_passers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `board_passers_student_id_board_exam_exam_year_index` (`student_id`,`board_exam`,`exam_year`),
  ADD KEY `board_passers_result_exam_year_index` (`result`,`exam_year`),
  ADD KEY `board_passers_program_index` (`program`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD KEY `certificates_student_id_index` (`student_id`),
  ADD KEY `certificates_enrollment_id_index` (`enrollment_id`),
  ADD KEY `certificates_program_id_index` (`program_id`),
  ADD KEY `certificates_status_index` (`status`),
  ADD KEY `certificates_certificate_number_index` (`certificate_number`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `chats_sender_id_receiver_id_index` (`sender_id`,`receiver_id`),
  ADD KEY `chats_sent_at_index` (`sent_at`),
  ADD KEY `chats_sender_id_receiver_id_sent_at_index` (`sender_id`,`receiver_id`,`sent_at`),
  ADD KEY `chats_receiver_id_read_at_index` (`receiver_id`,`read_at`),
  ADD KEY `chats_conversation_index` (`sender_id`,`receiver_id`,`sent_at`),
  ADD KEY `chats_unread_index` (`receiver_id`,`is_read`);

--
-- Indexes for table `class_meetings`
--
ALTER TABLE `class_meetings`
  ADD PRIMARY KEY (`meeting_id`),
  ADD KEY `class_meetings_batch_id_meeting_date_index` (`batch_id`,`meeting_date`),
  ADD KEY `class_meetings_professor_id_meeting_date_index` (`professor_id`,`meeting_date`),
  ADD KEY `class_meetings_created_by_foreign` (`created_by`);

--
-- Indexes for table `content_completions`
--
ALTER TABLE `content_completions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_content_completion` (`student_id`,`content_id`,`course_id`,`module_id`),
  ADD KEY `idx_ccc_content_id` (`content_id`),
  ADD KEY `idx_ccc_course_id` (`course_id`),
  ADD KEY `idx_ccc_module_id` (`module_id`),
  ADD KEY `idx_ccc_student_id` (`student_id`);

--
-- Indexes for table `content_items`
--
ALTER TABLE `content_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_items_course_id_foreign` (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `course_completions`
--
ALTER TABLE `course_completions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_course_completion` (`student_id`,`course_id`,`module_id`,`content_id`),
  ADD KEY `idx_cc_course_id` (`course_id`),
  ADD KEY `idx_cc_module_id` (`module_id`),
  ADD KEY `idx_cc_student_id` (`student_id`),
  ADD KEY `idx_cc_content_id` (`content_id`);

--
-- Indexes for table `deadlines`
--
ALTER TABLE `deadlines`
  ADD PRIMARY KEY (`deadline_id`);

--
-- Indexes for table `directors`
--
ALTER TABLE `directors`
  ADD PRIMARY KEY (`directors_id`),
  ADD UNIQUE KEY `directors_email` (`directors_email`),
  ADD UNIQUE KEY `referral_code` (`referral_code`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `director_program`
--
ALTER TABLE `director_program`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `director_program_director_id_program_id_unique` (`director_id`,`program_id`),
  ADD KEY `director_program_program_id_foreign` (`program_id`);

--
-- Indexes for table `education_levels`
--
ALTER TABLE `education_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `education_levels_level_name_unique` (`level_name`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `idx_enrollments_student` (`student_id`),
  ADD KEY `idx_enroll_registration` (`registration_id`),
  ADD KEY `idx_enroll_status` (`enrollment_status`),
  ADD KEY `enrollments_batch_id_foreign` (`batch_id`);

--
-- Indexes for table `enrollment_courses`
--
ALTER TABLE `enrollment_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollment_courses_enrollment_id_course_id_unique` (`enrollment_id`,`course_id`),
  ADD KEY `enrollment_courses_enrollment_id_index` (`enrollment_id`),
  ADD KEY `enrollment_courses_course_id_index` (`course_id`),
  ADD KEY `enrollment_courses_module_id_index` (`module_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `form_requirements`
--
ALTER TABLE `form_requirements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meeting_attendance_logs`
--
ALTER TABLE `meeting_attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_attendance_logs_meeting_id_index` (`meeting_id`),
  ADD KEY `meeting_attendance_logs_student_id_index` (`student_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`modules_id`),
  ADD KEY `idx_modules_program` (`program_id`),
  ADD KEY `idx_modules_plan` (`plan_id`);

--
-- Indexes for table `module_completions`
--
ALTER TABLE `module_completions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mc_student_program` (`student_id`,`program_id`),
  ADD KEY `idx_mc_modules` (`modules_id`),
  ADD KEY `idx_mc_content` (`content_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`),
  ADD KEY `created_by_admin_id` (`created_by_admin_id`);

--
-- Indexes for table `package_courses`
--
ALTER TABLE `package_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_courses_package_id_course_id_unique` (`package_id`,`course_id`),
  ADD KEY `package_courses_package_id_index` (`package_id`),
  ADD KEY `package_courses_course_id_index` (`course_id`);

--
-- Indexes for table `package_modules`
--
ALTER TABLE `package_modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_modules_package_id_modules_id_unique` (`package_id`,`modules_id`),
  ADD KEY `package_modules_package_id_index` (`package_id`),
  ADD KEY `package_modules_modules_id_index` (`modules_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payments_student_id_index` (`student_id`),
  ADD KEY `payments_enrollment_id_index` (`enrollment_id`),
  ADD KEY `payments_payment_status_index` (`payment_status`),
  ADD KEY `payments_created_at_index` (`created_at`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`payment_history_id`),
  ADD KEY `idx_payment_history_enrollment` (`enrollment_id`,`payment_status`),
  ADD KEY `idx_payment_history_user` (`user_id`),
  ADD KEY `idx_payment_history_student` (`student_id`),
  ADD KEY `idx_payment_history_date` (`payment_date`),
  ADD KEY `processed_by_admin_id` (`processed_by_admin_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_method_id`);

--
-- Indexes for table `payment_method_fields`
--
ALTER TABLE `payment_method_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_method_fields_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `plan`
--
ALTER TABLE `plan`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`professor_id`),
  ADD UNIQUE KEY `professor_email` (`professor_email`),
  ADD UNIQUE KEY `referral_code` (`referral_code`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `professor_batch`
--
ALTER TABLE `professor_batch`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `professor_batch_batch_id_professor_id_unique` (`batch_id`,`professor_id`),
  ADD KEY `professor_batch_professor_id_foreign` (`professor_id`),
  ADD KEY `professor_batch_assigned_by_foreign` (`assigned_by`);

--
-- Indexes for table `professor_program`
--
ALTER TABLE `professor_program`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`),
  ADD KEY `created_by_admin_id` (`created_by_admin_id`),
  ADD KEY `programs_director_id_index` (`director_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `quiz_attempts_quiz_id_student_id_index` (`quiz_id`,`student_id`),
  ADD KEY `quiz_attempts_status_index` (`status`);

--
-- Indexes for table `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD PRIMARY KEY (`option_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_questions_quiz_id_foreign` (`quiz_id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`referral_id`),
  ADD UNIQUE KEY `unique_student_referral` (`student_id`),
  ADD KEY `idx_referral_code` (`referral_code`),
  ADD KEY `idx_referrer` (`referrer_type`,`referrer_id`),
  ADD KEY `idx_student_registration` (`student_id`,`registration_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_registrations_approved_at` (`approved_at`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_batches`
--
ALTER TABLE `student_batches`
  ADD PRIMARY KEY (`batch_id`),
  ADD KEY `student_batches_program_id_foreign` (`program_id`),
  ADD KEY `student_batches_professor_id_foreign` (`professor_id`),
  ADD KEY `student_batches_created_by_foreign` (`created_by`);

--
-- Indexes for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_content_progress` (`student_id`,`content_id`),
  ADD KEY `student_progress_content_id_foreign` (`content_id`);

--
-- Indexes for table `ui_settings`
--
ALTER TABLE `ui_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ui_settings_section_setting_key_unique` (`section`,`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `websockets_statistics_entries`
--
ALTER TABLE `websockets_statistics_entries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `setting_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `batch_professors`
--
ALTER TABLE `batch_professors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `board_passers`
--
ALTER TABLE `board_passers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificate_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `chat_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `class_meetings`
--
ALTER TABLE `class_meetings`
  MODIFY `meeting_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `content_completions`
--
ALTER TABLE `content_completions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `content_items`
--
ALTER TABLE `content_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `subject_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `course_completions`
--
ALTER TABLE `course_completions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `deadlines`
--
ALTER TABLE `deadlines`
  MODIFY `deadline_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `directors`
--
ALTER TABLE `directors`
  MODIFY `directors_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- AUTO_INCREMENT for table `director_program`
--
ALTER TABLE `director_program`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `education_levels`
--
ALTER TABLE `education_levels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `enrollment_courses`
--
ALTER TABLE `enrollment_courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_requirements`
--
ALTER TABLE `form_requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `meeting_attendance_logs`
--
ALTER TABLE `meeting_attendance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `modules_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `module_completions`
--
ALTER TABLE `module_completions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `package_courses`
--
ALTER TABLE `package_courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `package_modules`
--
ALTER TABLE `package_modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `payment_history_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payment_method_fields`
--
ALTER TABLE `payment_method_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan`
--
ALTER TABLE `plan`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `professor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `professor_batch`
--
ALTER TABLE `professor_batch`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `professor_program`
--
ALTER TABLE `professor_program`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_options`
--
ALTER TABLE `quiz_options`
  MODIFY `option_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=418;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `referral_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_batches`
--
ALTER TABLE `student_batches`
  MODIFY `batch_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `student_progress`
--
ALTER TABLE `student_progress`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ui_settings`
--
ALTER TABLE `ui_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `websockets_statistics_entries`
--
ALTER TABLE `websockets_statistics_entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `class_meetings`
--
ALTER TABLE `class_meetings`
  ADD CONSTRAINT `class_meetings_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `student_batches` (`batch_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_meetings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_meetings_professor_id_foreign` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`) ON DELETE CASCADE;

--
-- Constraints for table `content_completions`
--
ALTER TABLE `content_completions`
  ADD CONSTRAINT `fk_ccc_content` FOREIGN KEY (`content_id`) REFERENCES `content_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ccc_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`subject_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ccc_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`modules_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ccc_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_completions`
--
ALTER TABLE `course_completions`
  ADD CONSTRAINT `course_completions_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`subject_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cc_content` FOREIGN KEY (`content_id`) REFERENCES `content_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cc_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`modules_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cc_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `directors`
--
ALTER TABLE `directors`
  ADD CONSTRAINT `directors_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `director_program`
--
ALTER TABLE `director_program`
  ADD CONSTRAINT `director_program_director_id_foreign` FOREIGN KEY (`director_id`) REFERENCES `directors` (`directors_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `director_program_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `student_batches` (`batch_id`) ON DELETE SET NULL;

--
-- Constraints for table `module_completions`
--
ALTER TABLE `module_completions`
  ADD CONSTRAINT `fk_mc_content` FOREIGN KEY (`content_id`) REFERENCES `content_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mc_module` FOREIGN KEY (`modules_id`) REFERENCES `modules` (`modules_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mc_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_method_fields`
--
ALTER TABLE `payment_method_fields`
  ADD CONSTRAINT `payment_method_fields_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`payment_method_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD CONSTRAINT `student_progress_content_id_foreign` FOREIGN KEY (`content_id`) REFERENCES `content_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
