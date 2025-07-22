-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 06:58 AM
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
(2, 'meeting_creation_enabled', '0', 'Enable or disable meeting creation for professors', 1, '2025-07-18 12:52:43', '2025-07-18 07:47:52'),
(100, 'referral_enabled', '1', 'Enable/disable referral code field in registration', 1, NULL, NULL),
(101, 'referral_required', '0', 'Make referral code required in registration', 1, NULL, NULL),
(102, 'grading_enabled', 'false', NULL, 1, '2025-07-18 05:00:49', '2025-07-18 07:48:16'),
(103, 'upload_videos_enabled', 'true', NULL, 1, '2025-07-18 05:00:49', '2025-07-18 05:00:49'),
(104, 'attendance_enabled', 'false', NULL, 1, '2025-07-18 05:00:49', '2025-07-18 07:48:16'),
(106, 'meeting_whitelist_professors', '', NULL, 1, '2025-07-18 05:49:04', '2025-07-18 07:48:23'),
(108, 'view_programs_enabled', 'false', NULL, 1, '2025-07-18 06:14:52', '2025-07-18 07:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('general','video','assignment','quiz') NOT NULL DEFAULT 'general',
  `video_link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `module_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`files`)),
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('submitted','graded','returned') NOT NULL DEFAULT 'submitted',
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

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`chat_id`, `sender_id`, `receiver_id`, `body_cipher`, `is_read`, `is_encrypted`, `sent_at`, `read_at`, `created_at`, `updated_at`) VALUES
(31, 112, 8, 'test1', 0, 1, '2025-07-13 07:17:13', NULL, '2025-07-13 07:17:13', '2025-07-13 07:17:13'),
(32, 111, 112, 'eyJpdiI6IlAvQkpYdW83TXBwb2hJZHZYNlAwZkE9PSIsInZhbHVlIjoiYWh0VnZZOGFpeHNDQm1zV2M1UUg3RmNEaUtZMjNXTmZxQlhsQkdud1Jhd2RtRFdlTjNBbnFxNGpzQThkQXlNZiIsIm1hYyI6Ijk0OTAzMzhlMTE4MGM2ODA3ZmQ0ZjI5MWMwMjViY2I3ZTVkNWFjMjY2MGEwOGRkNmQ0ODZjMmVkMjNmNmNkZmMiLCJ0YWciOiIifQ==', 0, 1, '2025-07-13 15:45:40', NULL, '2025-07-13 07:45:40', '2025-07-13 07:45:40');

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
(1, 7, 8, 'naisu', 'aa', '2025-07-18 07:43:00', 60, 'https://www.youtube.com/watch?v=RG9sJDP36Go', NULL, 'scheduled', 1, 0, 0, NULL, NULL, '2025-07-17 14:42:35', '2025-07-17 14:42:35'),
(2, 8, 8, 'naisu', 'aa', '2025-07-18 07:43:00', 60, 'https://www.youtube.com/watch?v=RG9sJDP36Go', NULL, 'ongoing', 1, 0, 0, '2025-07-17 22:43:14', NULL, '2025-07-17 14:42:35', '2025-07-17 14:43:14');

-- --------------------------------------------------------

--
-- Table structure for table `content_items`
--

CREATE TABLE `content_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `content_title` varchar(255) NOT NULL,
  `content_description` text DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content_type` enum('assignment','quiz','test','link','video','document','lesson') DEFAULT NULL,
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
  `admin_override` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`admin_override`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `requires_prerequisite` tinyint(1) NOT NULL DEFAULT 0,
  `prerequisite_content_id` bigint(20) UNSIGNED DEFAULT NULL,
  `release_date` timestamp NULL DEFAULT NULL,
  `completion_criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`completion_criteria`)),
  `lock_reason` varchar(255) DEFAULT NULL,
  `locked_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `content_items`
--

INSERT INTO `content_items` (`id`, `content_title`, `content_description`, `course_id`, `content_type`, `content_data`, `content_url`, `attachment_path`, `max_points`, `due_date`, `time_limit`, `content_order`, `sort_order`, `enable_submission`, `allowed_file_types`, `max_file_size`, `submission_instructions`, `allow_multiple_submissions`, `order`, `is_required`, `is_active`, `admin_override`, `created_at`, `updated_at`, `is_locked`, `requires_prerequisite`, `prerequisite_content_id`, `release_date`, `completion_criteria`, `lock_reason`, `locked_by`) VALUES
(9, 'Lessons 1', NULL, 14, 'lesson', '{\"lesson_video_url\":null}', NULL, 'content/1752779161_Capstone1_ParticipantCount_Request.pdf', 0.00, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-17 11:06:01', '2025-07-17 11:06:01', 0, 0, NULL, NULL, NULL, NULL, NULL),
(10, 'yes', 'no', 16, 'assignment', '{\"assignment_instructions\":\"yesss\",\"due_date\":\"2025-07-19T00:16\",\"max_points\":\"10\"}', NULL, NULL, 10.00, '2025-07-19 00:16:00', NULL, 2, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-18 08:16:27', '2025-07-18 11:39:49', 0, 0, NULL, NULL, NULL, NULL, NULL),
(11, 'Chimken', '123', 16, 'lesson', '{\"lesson_video_url\":null}', NULL, NULL, 0.00, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-18 08:26:02', '2025-07-18 11:39:49', 0, 0, NULL, NULL, NULL, NULL, NULL),
(12, 'test', 'test', 11, 'lesson', '{\"lesson_video_url\":null}', NULL, NULL, 0.00, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-18 08:47:48', '2025-07-18 08:47:48', 0, 0, NULL, NULL, NULL, NULL, NULL),
(13, 'yes', 'yes', 11, 'assignment', '{\"assignment_instructions\":\"yes\",\"due_date\":\"2025-07-19T01:03\",\"max_points\":\"10\"}', NULL, NULL, 10.00, '2025-07-19 01:03:00', NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-18 09:03:17', '2025-07-18 09:03:17', 0, 0, NULL, NULL, NULL, NULL, NULL),
(14, 'Unang Content', 'yes', 17, 'lesson', '{\"lesson_video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=D7tdi0ql1mY&pp=ugUEEgJlbg%3D%3D\"}', NULL, NULL, 0.00, NULL, NULL, 2, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-18 09:06:13', '2025-07-19 03:53:35', 0, 0, NULL, NULL, NULL, NULL, NULL),
(16, 'Cook Beginner', 'cookerist', 18, 'lesson', '{\"lesson_video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=D7tdi0ql1mY&pp=ugUEEgJlbg%3D%3D\"}', NULL, NULL, 0.00, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-18 09:12:41', '2025-07-18 09:12:41', 0, 0, NULL, NULL, NULL, NULL, NULL),
(17, 'nakakalito', NULL, 19, 'lesson', '{\"lesson_video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=D7tdi0ql1mY&pp=ugUEEgJlbg%3D%3D\"}', NULL, NULL, 0.00, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-18 09:16:58', '2025-07-18 09:16:58', 0, 0, NULL, NULL, NULL, NULL, NULL),
(18, '3123', '1231', 20, 'lesson', '{\"lesson_video_url\":null}', NULL, NULL, 0.00, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-18 09:34:54', '2025-07-18 09:34:54', 0, 0, NULL, NULL, NULL, NULL, NULL),
(19, 'assignment magluto', 'yes', 17, 'assignment', '{\"assignment_instructions\":null,\"due_date\":null,\"max_points\":0}', NULL, NULL, 0.00, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-18 12:24:55', '2025-07-19 03:53:35', 0, 0, NULL, NULL, NULL, NULL, NULL),
(20, 'yes', '1aaa', 19, 'assignment', '{\"assignment_instructions\":null,\"due_date\":null,\"max_points\":0}', NULL, 'content/1752938462_Vince _Certificate.pdf', 0.00, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, NULL, '2025-07-19 07:21:02', '2025-07-19 07:21:02', 0, 0, NULL, NULL, NULL, NULL, NULL),
(21, '42342', '34ewrweraq', 23, 'lesson', '{\"lesson_video_url\":null}', NULL, NULL, 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, NULL, '2025-07-19 12:41:54', '2025-07-19 12:41:54', 0, 0, NULL, NULL, NULL, NULL, NULL),
(22, 'bann', 'yes', 23, 'lesson', '{\"lesson_video_url\":null}', NULL, NULL, 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, NULL, '2025-07-19 13:09:36', '2025-07-19 13:09:36', 0, 0, NULL, NULL, NULL, NULL, NULL),
(23, 'CCC', 'ccc', 24, 'lesson', '{\"lesson_video_url\":null}', NULL, NULL, 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, NULL, '2025-07-20 01:10:10', '2025-07-20 01:17:57', 0, 0, NULL, NULL, NULL, NULL, NULL),
(24, '2345234', '234234', 27, 'lesson', '{\"video_url\":null}', NULL, NULL, 0.00, NULL, NULL, 0, 0, 0, NULL, 10, NULL, 0, 0, 1, 1, NULL, '2025-07-21 05:23:27', '2025-07-21 05:23:27', 0, 0, NULL, NULL, NULL, NULL, NULL);

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

INSERT INTO `courses` (`subject_id`, `subject_name`, `subject_description`, `module_id`, `subject_price`, `subject_order`, `course_order`, `is_required`, `is_active`, `admin_override`, `created_at`, `updated_at`, `is_locked`, `requires_prerequisite`, `prerequisite_course_id`, `release_date`, `completion_criteria`, `lock_reason`, `locked_by`) VALUES
(1, 'Test Course', 'Test Description', 40, 99.99, 1, 0, 1, 1, NULL, '2025-07-16 10:30:42', '2025-07-16 10:30:42', 0, 0, NULL, NULL, NULL, NULL, NULL),
(2, 'Introduction to Programming', 'Basic programming concepts and fundamentals', 40, 199.99, 1, 0, 1, 1, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(3, 'Math', NULL, 40, 20.00, 2, 0, 1, 1, NULL, '2025-07-16 11:31:16', '2025-07-16 11:31:16', 0, 0, NULL, NULL, NULL, NULL, NULL),
(4, 'Math', NULL, 40, 20.00, 3, 0, 1, 1, NULL, '2025-07-16 11:31:16', '2025-07-16 11:31:16', 0, 0, NULL, NULL, NULL, NULL, NULL),
(5, 'Math', NULL, 40, 20.00, 4, 0, 1, 1, NULL, '2025-07-16 11:31:17', '2025-07-16 11:31:17', 0, 0, NULL, NULL, NULL, NULL, NULL),
(6, 'Math', NULL, 40, 20.00, 5, 0, 1, 1, NULL, '2025-07-16 11:31:17', '2025-07-16 11:31:17', 0, 0, NULL, NULL, NULL, NULL, NULL),
(7, 'Math', NULL, 40, 20.00, 6, 0, 1, 1, NULL, '2025-07-16 11:31:18', '2025-07-16 11:31:18', 0, 0, NULL, NULL, NULL, NULL, NULL),
(8, 'Math', NULL, 40, 20.00, 7, 0, 1, 1, NULL, '2025-07-16 11:31:18', '2025-07-16 11:31:18', 0, 0, NULL, NULL, NULL, NULL, NULL),
(11, 'Sinigang', NULL, 46, 250.00, 1, 0, 0, 1, NULL, '2025-07-17 02:28:01', '2025-07-17 02:28:01', 0, 0, NULL, NULL, NULL, NULL, NULL),
(14, 'KangKong Chips', NULL, 46, 250.00, 2, 0, 0, 1, NULL, '2025-07-17 04:27:25', '2025-07-17 04:27:25', 0, 0, NULL, NULL, NULL, NULL, NULL),
(15, 'Math', NULL, 45, 250.00, 1, 0, 0, 1, NULL, '2025-07-17 04:28:27', '2025-07-17 04:28:27', 0, 0, NULL, NULL, NULL, NULL, NULL),
(16, 'Advance Cooking Method', NULL, 47, 2500.00, 1, 0, 0, 1, NULL, '2025-07-17 06:36:19', '2025-07-17 06:36:19', 0, 0, NULL, NULL, NULL, NULL, NULL),
(17, 'Level 1', 'carbs maanghang', 48, 200.00, 2, 0, 0, 1, NULL, '2025-07-18 09:05:41', '2025-07-19 04:34:03', 0, 0, NULL, NULL, NULL, NULL, NULL),
(18, 'Lesson 1', 'beginning cooking 1', 49, 100.00, 1, 0, 0, 1, NULL, '2025-07-18 09:11:39', '2025-07-18 09:11:39', 0, 0, NULL, NULL, NULL, NULL, NULL),
(19, 'Apple Dev', NULL, 50, 100.00, 2, 0, 0, 1, NULL, '2025-07-18 09:15:59', '2025-07-20 01:00:42', 0, 0, NULL, NULL, NULL, NULL, NULL),
(20, 'tyes', 'asd', 51, 1.00, 1, 0, 0, 1, NULL, '2025-07-18 09:34:46', '2025-07-18 09:34:46', 0, 0, NULL, NULL, NULL, NULL, NULL),
(21, 'level 2', 'yes', 48, 1000.00, 1, 0, 0, 1, NULL, '2025-07-19 03:49:49', '2025-07-19 04:34:03', 0, 0, NULL, NULL, NULL, NULL, NULL),
(22, 'uploader', '1', 48, 100.00, 3, 0, 0, 1, NULL, '2025-07-19 10:07:31', '2025-07-19 10:07:31', 0, 0, NULL, NULL, NULL, NULL, NULL),
(23, 'Banana Dev', 'yes', 50, 100.00, 1, 0, 0, 1, NULL, '2025-07-19 10:08:02', '2025-07-20 01:00:35', 0, 0, NULL, NULL, NULL, NULL, NULL),
(24, 'BBB', 'BBB', 54, 99.00, 1, 0, 0, 1, NULL, '2025-07-20 01:09:31', '2025-07-20 01:21:13', 1, 0, NULL, NULL, NULL, NULL, NULL),
(25, 'yeas', 'fsfasdfas', 54, 11.00, 2, 0, 0, 1, NULL, '2025-07-20 01:55:29', '2025-07-20 01:55:29', 0, 0, NULL, NULL, NULL, NULL, NULL),
(27, 'asdwar', 'asdfasefa', 55, 100.00, 1, 0, 0, 1, NULL, '2025-07-21 05:23:05', '2025-07-21 05:23:05', 0, 0, NULL, NULL, NULL, NULL, NULL);

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
  `enrollment_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','cancelled') DEFAULT 'pending',
  `batch_access_granted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Grants dashboard access for batch students regardless of enrollment/payment status',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
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

INSERT INTO `enrollments` (`enrollment_id`, `registration_id`, `student_id`, `user_id`, `program_id`, `package_id`, `enrollment_type`, `learning_mode`, `batch_id`, `individual_start_date`, `individual_end_date`, `enrollment_status`, `payment_status`, `batch_access_granted`, `created_at`, `updated_at`, `school_id`, `diploma`, `valid_school_identification`, `transcript_of_records`, `certificate_of_good_moral_character`, `psa_birth_certificate`, `photo_2x2`, `diploma_certificate`, `transcript_records`, `moral_certificate`, `birth_cert`, `id_photo`, `passport_photo`, `medical_certificate`, `barangay_clearance`, `police_clearance`, `nbi_clearance`, `tor`, `good_moral`, `psa`) VALUES
(152, 1753045080, '2025-07-00009', 165, 36, 18, 'Modular', 'Synchronous', NULL, NULL, NULL, 'approved', 'pending', 0, '2025-07-21 13:42:05', '2025-07-21 13:42:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
(1, 1, 1, 40, 'course', 0.00, 1, '2025-07-18 09:26:46', '2025-07-18 09:26:46');

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
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `lesson_name`, `lesson_description`, `course_id`, `lesson_price`, `lesson_duration`, `lesson_video_url`, `lesson_order`, `is_required`, `is_active`, `learning_mode`, `created_at`, `updated_at`) VALUES
(1, 'Test Lesson', 'Test Lesson Description', 1, 29.99, NULL, NULL, 1, 1, 1, 'Both', '2025-07-16 10:31:08', '2025-07-16 10:31:08'),
(2, 'Variables and Data Types', 'Learn about variables and different data types', 2, NULL, NULL, NULL, 1, 1, 1, 'Both', NULL, NULL),
(3, 'Control Structures', 'Understanding if statements, loops, and conditions', 2, NULL, NULL, NULL, 2, 1, 1, 'Both', NULL, NULL),
(4, 'Functions and Methods', 'Creating and using functions in programming', 2, NULL, NULL, NULL, 3, 1, 1, 'Both', NULL, NULL),
(9, 'KangKong Chips - Main Lesson', 'Auto-generated lesson for KangKong Chips', 14, 0.00, 60, NULL, 0, 1, 1, 'Both', '2025-07-17 11:05:11', '2025-07-17 11:05:11');

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
(59, '2025_07_22_150000_add_file_upload_columns_to_registrations_table', 49);

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

INSERT INTO `modules` (`modules_id`, `module_name`, `module_description`, `program_id`, `batch_id`, `learning_mode`, `content_type`, `content_data`, `plan_id`, `attachment`, `video_path`, `additional_content`, `created_at`, `updated_at`, `is_archived`, `order`, `admin_override`, `is_locked`, `requires_prerequisite`, `prerequisite_module_id`, `release_date`, `completion_criteria`, `lock_reason`, `locked_by`, `module_order`) VALUES
(40, 'Modules 1', NULL, 32, 8, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-16 07:00:02', '2025-07-19 04:24:14', 0, 0, '[]', 0, 0, NULL, NULL, NULL, NULL, NULL, 7),
(41, 'Modules 2', NULL, 32, 8, 'Asynchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-16 07:00:30', '2025-07-19 04:24:14', 0, 0, '[]', 0, 0, NULL, NULL, NULL, NULL, NULL, 6),
(42, 'Modules 3', NULL, 32, 8, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-16 07:07:21', '2025-07-19 04:24:14', 0, 0, '[\"time_limits\",\"access_control\"]', 0, 0, NULL, NULL, NULL, NULL, NULL, 4),
(43, 'Modules 1', NULL, 33, 7, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-16 07:31:39', '2025-07-16 12:36:38', 1, 0, '[]', 0, 0, NULL, NULL, NULL, NULL, NULL, 1),
(44, 'Asignment', NULL, 32, 8, 'Synchronous', 'assignment', '{\"assignment_title\":null,\"assignment_instructions\":null,\"due_date\":null,\"max_points\":null,\"allow_late_submission\":false}', NULL, NULL, NULL, NULL, '2025-07-16 07:57:55', '2025-07-19 04:24:14', 0, 0, '[]', 0, 0, NULL, NULL, NULL, NULL, NULL, 5),
(45, 'Modules 4', NULL, 32, 8, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-16 12:46:04', '2025-07-19 04:24:14', 0, 0, '[\"completion\",\"prerequisites\",\"time_limits\"]', 0, 0, NULL, NULL, NULL, NULL, NULL, 2),
(46, 'Module 1 - Creation of Food', 'This module covers the fundamental principles of food creation, including ingredient selection, preparation techniques, and basic cooking methods. Students will learn essential culinary skills and food safety practices.', 33, 7, 'Synchronous', 'module', '{\"learning_objectives\":[\"Understand basic cooking techniques\",\"Learn food safety principles\",\"Master ingredient preparation\",\"Develop knife skills\"],\"estimated_duration\":\"2 hours\",\"difficulty_level\":\"Beginner\",\"materials_needed\":[\"Chef knife\",\"Cutting board\",\"Basic cooking utensils\",\"Fresh ingredients\"]}', NULL, NULL, NULL, NULL, '2025-07-17 02:08:11', '2025-07-19 04:26:58', 0, 0, '[\"completion\",\"prerequisites\",\"time_limits\",\"access_control\"]', 0, 0, NULL, NULL, NULL, NULL, NULL, 4),
(47, 'Modules 2', NULL, 33, 7, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-17 06:33:51', '2025-07-19 04:26:58', 0, 0, '[\"completion\",\"prerequisites\",\"time_limits\",\"access_control\"]', 0, 0, NULL, NULL, NULL, NULL, NULL, 3),
(48, 'Carbonaraa', 'carbss', 33, 7, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-18 09:04:21', '2025-07-19 04:26:58', 0, 0, '[\"completion\",\"prerequisites\",\"time_limits\",\"access_control\"]', 0, 0, NULL, NULL, NULL, NULL, NULL, 2),
(49, 'Cooking 1', 'Cooking lesson beginner', 33, 7, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-18 09:10:47', '2025-07-19 04:26:58', 0, 0, '[\"completion\",\"prerequisites\",\"time_limits\",\"access_control\"]', 0, 0, NULL, NULL, NULL, NULL, NULL, 5),
(50, 'Derivatives', 'derive', 33, 7, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-18 09:14:59', '2025-07-19 04:26:58', 0, 0, '[\"completion\",\"prerequisites\",\"time_limits\",\"access_control\"]', 0, 0, NULL, NULL, NULL, NULL, NULL, 1),
(51, 'test', 'test', 33, 7, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-18 09:34:35', '2025-07-18 09:34:56', 1, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(52, 'Mecha', '1', 32, 8, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-19 04:23:36', '2025-07-19 04:24:14', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 3),
(53, 'Mecha', '1', 32, 8, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-19 04:23:49', '2025-07-19 04:24:14', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 1),
(54, 'AAA', 'aaa', 33, 7, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-20 01:05:38', '2025-07-20 13:10:26', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(55, 'yess', 'aaaaa', 35, 7, 'Synchronous', 'module', '[]', NULL, NULL, NULL, NULL, '2025-07-21 05:22:55', '2025-07-21 05:22:55', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(56, 'modddd', '111', 35, 7, 'Synchronous', 'module', '[]', NULL, 'content/1753104250_Copy of MATH 1 - Algebra and Trigonometry.pdf', NULL, NULL, '2025-07-21 05:24:10', '2025-07-21 05:24:10', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(57, 'image', '1', 32, 8, 'Synchronous', 'module', '[]', NULL, 'content/1753127351_Copy of MATH 1 - Algebra and Trigonometry.pdf', NULL, NULL, '2025-07-21 11:49:11', '2025-07-21 11:49:11', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `module_completions`
--

CREATE TABLE `module_completions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `program_id` int(11) NOT NULL,
  `module_id` int(11) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

INSERT INTO `packages` (`package_id`, `package_name`, `description`, `amount`, `program_id`, `created_by_admin_id`, `created_at`, `updated_at`, `package_type`, `selection_type`, `selection_mode`, `module_count`, `course_count`, `min_courses`, `max_courses`, `allowed_modules`, `allowed_courses`, `extra_module_price`, `price`, `access_period_days`, `access_period_months`, `access_period_years`) VALUES
(18, 'Package 1', 'Consists of 2 Courses', 2500.00, 33, 1, '2025-07-18 11:50:42', '2025-07-18 11:54:05', 'modular', 'course', 'courses', NULL, 2, NULL, NULL, 2, NULL, NULL, 2500.00, NULL, NULL, NULL),
(19, 'Package 2', 'Consists of 3 Courses', 3000.00, 33, 1, '2025-07-18 11:53:46', '2025-07-18 11:53:46', 'modular', 'course', 'courses', NULL, 3, NULL, NULL, 2, NULL, NULL, 3000.00, NULL, NULL, NULL),
(20, 'Package 3', 'Consists of 2 Modules', 10000.00, 33, 1, '2025-07-18 11:55:42', '2025-07-18 11:55:42', 'modular', 'module', 'modules', 2, NULL, NULL, NULL, 2, NULL, NULL, 10000.00, NULL, NULL, NULL),
(21, 'Package 4', 'Consists of 1 modules 2 courses', 7500.00, 33, 1, '2025-07-18 11:56:37', '2025-07-18 11:56:37', 'modular', 'both', 'courses', 1, 2, NULL, NULL, 2, NULL, NULL, 7500.00, NULL, NULL, NULL),
(22, 'yes', 'n', 111.00, 36, 1, '2025-07-21 07:58:06', '2025-07-21 07:58:06', 'full', 'module', 'modules', NULL, NULL, NULL, NULL, 2, NULL, NULL, 111.00, NULL, NULL, NULL);

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

--
-- Dumping data for table `package_courses`
--

INSERT INTO `package_courses` (`id`, `package_id`, `course_id`, `created_at`, `updated_at`) VALUES
(9, 18, 11, '2025-07-18 11:50:42', '2025-07-18 11:50:42'),
(10, 18, 14, '2025-07-18 11:50:42', '2025-07-18 11:50:42'),
(11, 18, 16, '2025-07-18 11:50:42', '2025-07-18 11:50:42'),
(12, 19, 11, '2025-07-18 11:53:46', '2025-07-18 11:53:46'),
(13, 19, 14, '2025-07-18 11:53:46', '2025-07-18 11:53:46'),
(14, 19, 16, '2025-07-18 11:53:46', '2025-07-18 11:53:46'),
(15, 21, 14, '2025-07-18 11:56:37', '2025-07-18 11:56:37'),
(16, 21, 16, '2025-07-18 11:56:37', '2025-07-18 11:56:37'),
(17, 21, 11, '2025-07-18 11:56:37', '2025-07-18 11:56:37');

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

--
-- Dumping data for table `package_modules`
--

INSERT INTO `package_modules` (`id`, `package_id`, `modules_id`, `created_at`, `updated_at`) VALUES
(2, 20, 46, '2025-07-18 11:55:42', '2025-07-18 11:55:42'),
(3, 20, 47, '2025-07-18 11:55:42', '2025-07-18 11:55:42'),
(4, 21, 46, '2025-07-18 11:56:37', '2025-07-18 11:56:37');

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
  `resubmitted_at` timestamp NULL DEFAULT NULL,
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

INSERT INTO `payments` (`payment_id`, `enrollment_id`, `student_id`, `program_id`, `package_id`, `payment_method`, `amount`, `payment_status`, `rejection_reason`, `rejected_by`, `rejected_at`, `resubmitted_at`, `payment_details`, `verified_by`, `verified_at`, `receipt_number`, `reference_number`, `notes`, `created_at`, `updated_at`) VALUES
(1, 152, '2025-07-00009', 36, 18, 'gcash', 2500.00, 'pending', NULL, NULL, NULL, NULL, '\"{\\\"payment_proof_path\\\":\\\"payment_proofs\\\\\\/payment_proof_152_1753139606.png\\\",\\\"reference_number\\\":\\\"2637412734512\\\",\\\"payment_method_name\\\":\\\"GCash\\\",\\\"uploaded_at\\\":\\\"2025-07-21T23:13:27.150227Z\\\"}\"', NULL, NULL, NULL, NULL, 'Payment proof uploaded by student', '2025-07-21 15:13:27', '2025-07-21 15:13:27');

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
(21, 132, NULL, '2025-07-00001', 33, 21, NULL, 'paid', 'manual', 'Payment marked as paid by administrator', '2025-07-18 12:02:04', 1, '2025-07-18 12:02:04', '2025-07-18 12:02:04'),
(24, 138, NULL, '2025-07-00004', 33, 19, NULL, 'paid', 'manual', 'Payment marked as paid by administrator', '2025-07-19 08:17:21', 1, '2025-07-19 08:17:21', '2025-07-19 08:17:21'),
(27, 146, 163, '2025-07-00009', 34, 18, NULL, 'paid', 'manual', 'Payment marked as paid by administrator', '2025-07-20 12:37:00', 1, '2025-07-20 12:37:00', '2025-07-20 12:37:00');

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
(1, 'GCash', 'gcash', 'Pay via GCash mobile wallet', NULL, 'Send payment to GCash number and upload receipt', NULL, 1, 1, 1, '2025-07-14 13:05:09', '2025-07-14 13:05:09'),
(11, 'GCash', 'gcash', 'Mobile payment via GCash', NULL, NULL, NULL, 1, 0, 0, NULL, NULL),
(12, 'Maya (PayMaya)', 'maya', 'Mobile payment via Maya', NULL, NULL, NULL, 1, 0, 0, NULL, NULL),
(13, 'Bank Transfer', 'bank_transfer', 'Direct bank transfer', NULL, NULL, NULL, 1, 0, 0, NULL, NULL),
(14, 'Cash Payment', 'cash', 'Cash payment at office', NULL, NULL, NULL, 1, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_terms`
--

CREATE TABLE `payment_terms` (
  `id` int(11) NOT NULL,
  `term_name` varchar(100) NOT NULL,
  `content` longtext DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_terms`
--

INSERT INTO `payment_terms` (`id`, `term_name`, `content`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'General Payment Terms', '\r\n    <h3>Payment Terms and Conditions</h3>\r\n    <p>Please read the following terms carefully before making payment:</p>\r\n    <ul>\r\n        <li>All payments are non-refundable once enrollment is confirmed</li>\r\n        <li>Payment must be completed within 7 days of application submission</li>\r\n        <li>For installment payments, contact the admissions office</li>\r\n        <li>Payment confirmation is required for enrollment processing</li>\r\n    </ul>\r\n    ', 1, '2025-07-22 04:26:53', '2025-07-22 04:26:53');

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
  `professor_archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`professor_id`, `admin_id`, `professor_name`, `professor_first_name`, `professor_last_name`, `professor_email`, `professor_password`, `referral_code`, `professor_archived`, `created_at`, `updated_at`) VALUES
(8, 1, 'robert san', 'robert', 'san', 'robert@gmail.com', '$2y$10$VoMnQqIuvSditKfIjnEoNuMWmB1FmLLOgaeTWvqNpld78kK/LfB3K', 'PROF08RSAN', 0, '2025-07-09 12:02:17', '2025-07-17 18:55:31');

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
(5, 13, 8, '2025-07-21 05:22:28', NULL, '2025-07-21 05:22:28', '2025-07-21 05:22:28');

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
(10, 8, 33, NULL, NULL, '2025-07-09 12:21:08', '2025-07-09 12:21:08'),
(11, 8, 32, NULL, NULL, '2025-07-12 11:43:14', '2025-07-12 11:43:14'),
(12, 8, 34, NULL, NULL, '2025-07-12 11:43:14', '2025-07-12 11:43:14');

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
  `is_archived` tinyint(1) DEFAULT 0,
  `program_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `program_name`, `created_by_admin_id`, `director_id`, `created_at`, `updated_at`, `is_archived`, `program_description`) VALUES
(32, 'Engineer', 1, NULL, '2025-07-09 12:02:51', '2025-07-19 07:55:58', 0, NULL),
(33, 'Culinary', 1, NULL, '2025-07-09 12:02:58', '2025-07-19 12:36:48', 1, NULL),
(34, 'Nursing', 1, NULL, '2025-07-09 12:03:03', '2025-07-09 12:03:03', 0, NULL),
(35, 'Mechanical Engineer', 1, NULL, '2025-07-16 06:48:28', '2025-07-16 06:48:28', 0, NULL),
(36, 'Civil Engineer', 1, NULL, '2025-07-19 12:37:12', '2025-07-19 12:37:12', 0, 'Civil');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `quiz_title` varchar(255) NOT NULL,
  `instructions` text DEFAULT NULL,
  `difficulty` enum('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `total_questions` int(11) NOT NULL DEFAULT 10,
  `time_limit` int(11) NOT NULL DEFAULT 60,
  `document_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
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
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
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

INSERT INTO `registrations` (`registration_id`, `user_id`, `package_id`, `package_name`, `plan_id`, `plan_name`, `program_id`, `program_name`, `enrollment_type`, `learning_mode`, `firstname`, `middlename`, `lastname`, `student_school`, `street_address`, `state_province`, `city`, `zipcode`, `contact_number`, `emergency_contact_number`, `good_moral`, `PSA`, `Course_Cert`, `TOR`, `Cert_of_Grad`, `dynamic_fields`, `photo_2x2`, `Start_Date`, `status`, `rejection_reason`, `rejected_fields`, `rejected_by`, `rejected_at`, `resubmitted_at`, `original_submission`, `created_at`, `updated_at`, `phone_number`, `telephone_number`, `religion`, `citizenship`, `civil_status`, `birthdate`, `gender`, `work_experience`, `preferred_schedule`, `emergency_contact_relationship`, `health_conditions`, `disability_support`, `valid_id`, `birth_certificate`, `diploma_certificate`, `medical_certificate`, `passport_photo`, `parent_guardian_name`, `parent_guardian_contact`, `previous_school`, `graduation_year`, `course_taken`, `special_needs`, `scholarship_program`, `employment_status`, `monthly_income`, `school_name`, `selected_modules`, `selected_courses`, `test_field_auto`, `testering`, `master`, `bagit`, `real`, `test_auto_column_1752439854`, `nyan`, `education_level`, `sync_async_mode`, `Test`, `last_name`, `referral_code`, `school_id`, `diploma`, `valid_school_identification`, `transcript_of_records`, `certificate_of_good_moral_character`, `psa_birth_certificate`, `transcript_records`, `moral_certificate`, `birth_cert`, `id_photo`, `barangay_clearance`, `police_clearance`, `nbi_clearance`, `form_137`) VALUES
(132, 153, 18, 'Package 1', NULL, NULL, 33, 'Culinary', 'Modular', 'asynchronous', '12345678', NULL, '12345678', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"{\\\"referral_code\\\":\\\"\\\",\\\"registration_mode\\\":\\\"asynchronous\\\",\\\"first_name\\\":\\\"12345678\\\",\\\"last_name\\\":\\\"12345678\\\",\\\"test\\\":\\\"12345678\\\"}\"', NULL, '2025-07-26', 'rejected', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-18 13:04:26', '2025-07-19 07:38:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"[{\\\"id\\\":\\\"45\\\",\\\"name\\\":\\\"Modules 4\\\",\\\"selected_courses\\\":[]},{\\\"id\\\":\\\"44\\\",\\\"name\\\":\\\"Asignment\\\",\\\"selected_courses\\\":[]}]\"', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Undergraduate', 'sync', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 157, 19, 'Package 2', NULL, NULL, 33, 'Culinary', 'Modular', 'synchronous', 'Modular_enrollment1@gmail.com', NULL, 'Modular_enrollment1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"{\\\"referral_code\\\":\\\"\\\",\\\"registration_mode\\\":\\\"synchronous\\\",\\\"first_name\\\":\\\"Modular_enrollment1@gmail.com\\\",\\\"last_name\\\":\\\"Modular_enrollment1@gmail.com\\\",\\\"test\\\":\\\"Modular_enrollment1@gmail.com\\\"}\"', NULL, '2025-07-19', 'rejected', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-19 05:44:05', '2025-07-19 07:38:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"[]\"', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Graduate', 'sync', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 158, 18, NULL, NULL, NULL, 33, NULL, 'Full', 'synchronous', 'Modular_enrollment1@gmail.com', NULL, 'Modular_enrollment1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-02', 'rejected', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-19 09:33:53', '2025-07-19 13:00:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Graduate', 'sync', '12345678', 'Dela Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `registration_modules`
--

CREATE TABLE `registration_modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `registration_id` int(10) UNSIGNED NOT NULL,
  `module_id` int(11) NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

INSERT INTO `students` (`student_id`, `user_id`, `firstname`, `middlename`, `lastname`, `student_school`, `street_address`, `state_province`, `city`, `zipcode`, `contact_number`, `emergency_contact_number`, `good_moral`, `PSA`, `Course_Cert`, `TOR`, `Cert_of_Grad`, `photo_2x2`, `Start_Date`, `date_approved`, `created_at`, `updated_at`, `email`, `is_archived`, `package_id`, `package_name`, `plan_id`, `plan_name`, `program_id`, `education_level`, `program_name`, `enrollment_type`, `learning_mode`, `Undergraduate`, `Graduate`, `dynamic_fields`, `status`, `phone_number`, `telephone_number`, `religion`, `citizenship`, `civil_status`, `birthdate`, `gender`, `work_experience`, `preferred_schedule`, `emergency_contact_relationship`, `health_conditions`, `disability_support`, `valid_id`, `birth_certificate`, `diploma_certificate`, `medical_certificate`, `passport_photo`, `parent_guardian_name`, `parent_guardian_contact`, `referral_code`, `ama_namin`, `previous_school`, `graduation_year`, `course_taken`, `special_needs`, `scholarship_program`, `employment_status`, `monthly_income`, `school_name`, `selected_modules`, `test_field_auto`, `testering`, `master`, `bagit`, `real`, `test_auto_column_1752439854`, `nyan`, `Test`, `last_name`, `school_id`, `diploma`, `valid_school_identification`, `transcript_of_records`, `certificate_of_good_moral_character`, `psa_birth_certificate`, `transcript_records`, `moral_certificate`, `birth_cert`, `id_photo`, `barangay_clearance`, `police_clearance`, `nbi_clearance`) VALUES
('2025-07-00005', 159, 'Vince', NULL, 'Dela Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-19 17:56:41', '2025-07-19 09:56:41', '2025-07-19 09:56:41', 'vince03handsome11@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'Graduate', NULL, NULL, NULL, 0, 0, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Dela Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('2025-07-00006', 160, 'Vince', NULL, 'Dela Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-19 18:42:06', '2025-07-19 10:42:06', '2025-07-19 10:42:06', 'vince03handsome@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'Graduate', NULL, NULL, NULL, 0, 0, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Dela Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('2025-07-00007', 162, 'Vince', NULL, 'Dela Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-19 20:13:47', '2025-07-19 12:13:47', '2025-07-19 12:13:47', 'vmdelavega03@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'Graduate', NULL, NULL, NULL, 0, 0, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Dela Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('2025-07-00008', 161, 'Vince', NULL, 'Dela Vega', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-21', '2025-07-19 13:00:16', '2025-07-19 13:00:16', '2025-07-19 13:00:16', 'vince03handsome1q1@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('2025-07-00009', 165, 'alex', NULL, 'butas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/education_requirements/1753134125_sample1.png', NULL, '2025-07-21', '2025-07-21 13:42:39', '2025-07-21 13:42:39', '2025-07-21 13:42:39', 'yorushetzu@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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

INSERT INTO `student_batches` (`batch_id`, `batch_name`, `program_id`, `professor_id`, `max_capacity`, `current_capacity`, `batch_status`, `registration_deadline`, `start_date`, `end_date`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(7, 'Batch 1', 35, 8, 10, 3, 'ongoing', '2025-08-15', '2025-07-15', NULL, NULL, NULL, '2025-07-09 12:12:00', '2025-07-21 05:22:04'),
(8, 'Batch 1', 32, 8, 10, 0, 'available', '2025-07-15', '2025-07-29', NULL, NULL, NULL, '2025-07-11 01:49:36', '2025-07-20 11:39:17'),
(9, 'Batch Mech 1', 35, NULL, 10, 0, 'available', '2025-07-16', '2025-07-23', '2025-07-31', NULL, NULL, '2025-07-20 10:32:30', '2025-07-20 10:32:30'),
(12, 'Civil Engineer 1', 36, NULL, 30, 1, 'available', '2025-08-20', '2025-08-10', '2026-04-10', NULL, 1, '2025-07-20 13:02:05', '2025-07-20 13:28:41'),
(13, 'Batch 2', 35, 8, 10, 0, 'ongoing', '2025-07-22', '2025-07-16', '2025-07-22', NULL, NULL, '2025-07-21 05:22:28', '2025-07-21 05:22:28');

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
(1, 'navbar', 'header_bg', '#ffffff', 'color', '2025-07-03 08:17:32', '2025-07-14 23:18:10'),
(2, 'navbar', 'header_text', '#d23232', 'color', '2025-07-03 08:17:32', '2025-07-15 23:47:43'),
(3, 'navbar', 'header_border', '#e0e0e0', 'color', '2025-07-03 08:17:32', '2025-07-14 23:18:10'),
(4, 'navbar', 'search_bg', '#f8f9fa', 'color', '2025-07-03 08:17:32', '2025-07-03 08:37:56'),
(5, 'navbar', 'sidebar_bg', '#0f87ff', 'color', '2025-07-03 08:17:32', '2025-07-15 23:47:43'),
(6, 'navbar', 'sidebar_text', '#ffffff', 'color', '2025-07-03 08:17:32', '2025-07-03 08:37:56'),
(7, 'navbar', 'active_link_bg', '#007bff', 'color', '2025-07-03 08:17:32', '2025-07-14 23:18:10'),
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
(18, 'footer', 'footer_bg_color', '#467db4', 'color', '2025-07-06 08:54:51', '2025-07-15 23:47:44'),
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
(159, 1, 1, 0, NULL, 'vince03handsome11@gmail.com', 'Vince', 'Dela Vega', '$2y$10$/9Sq2sFRghI7TEmsaVnoyOXL/zYcj9P.6MMXiUUDHyEsvArw/b5hK', 'student', NULL, '2025-07-19 09:56:41', '2025-07-19 09:56:41'),
(160, 1, 1, 0, NULL, 'vince03handsome@gmail.com', 'Vince', 'Dela Vega', '$2y$10$Du8QNUf642.PP6Vwy4ZYc.6sMuQpPuWFeST2/huKvMGwLozeCsEJa', 'student', NULL, '2025-07-19 10:42:05', '2025-07-19 10:42:05'),
(161, 1, NULL, 0, NULL, 'vince03handsome1q1@gmail.com', 'Vince', 'Dela Vega', '$2y$10$bLLmOygKuFFLx7EXcQ4hkuxr2D07gxBIRkIAxt76dlfkKigSZI4RO', 'student', 145, '2025-07-19 12:09:56', '2025-07-19 12:27:22'),
(162, 1, 1, 0, NULL, 'vmdelavega03@gmail.com', 'Vince', 'Dela Vega', '$2y$10$EA6wkRVO60WXsA5dcS6wae0XbY4ntv2PpXjMnOLbQ9Bwyf6Qxslh2', 'student', NULL, '2025-07-19 12:13:47', '2025-07-19 12:13:47'),
(165, 1, NULL, 0, NULL, 'yorushetzu@gmail.com', 'alex', 'butas', '$2y$10$OdDEBGgvgDk5oHo6KMXE3eTiNPjyfr87Ow6kMoUJljsTKjPUqSUaW', 'student', 152, '2025-07-21 13:42:05', '2025-07-21 13:42:05');

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
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`);

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
  ADD KEY `idx_mc_module` (`module_id`),
  ADD KEY `fk_mc_program` (`program_id`);

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
-- Indexes for table `payment_terms`
--
ALTER TABLE `payment_terms`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD PRIMARY KEY (`option_id`);

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
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `setting_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `batch_professors`
--
ALTER TABLE `batch_professors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `chat_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `class_meetings`
--
ALTER TABLE `class_meetings`
  MODIFY `meeting_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `content_items`
--
ALTER TABLE `content_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `subject_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `enrollment_courses`
--
ALTER TABLE `enrollment_courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `modules_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `module_completions`
--
ALTER TABLE `module_completions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `payment_history_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payment_terms`
--
ALTER TABLE `payment_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `professor_program`
--
ALTER TABLE `professor_program`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_options`
--
ALTER TABLE `quiz_options`
  MODIFY `option_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `referral_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1753045081;

--
-- AUTO_INCREMENT for table `student_batches`
--
ALTER TABLE `student_batches`
  MODIFY `batch_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ui_settings`
--
ALTER TABLE `ui_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

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
-- Constraints for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD CONSTRAINT `payment_history_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_history_ibfk_3` FOREIGN KEY (`processed_by_admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL;

--
-- Constraints for table `professor_batch`
--
ALTER TABLE `professor_batch`
  ADD CONSTRAINT `professor_batch_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `professor_batch_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `student_batches` (`batch_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `professor_batch_professor_id_foreign` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_batches`
--
ALTER TABLE `student_batches`
  ADD CONSTRAINT `student_batches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_batches_professor_id_foreign` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_batches_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
