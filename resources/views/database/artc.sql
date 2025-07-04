-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 07:19 PM
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
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `enrollment_type` enum('Modular','Complete') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `program_id`, `package_id`, `enrollment_type`, `created_at`, `updated_at`) VALUES
(17, 17, 3, 'Complete', '2025-06-28 05:26:00', '2025-06-28 05:26:00'),
(18, 1, 1, 'Complete', '2025-06-28 08:05:13', '2025-06-28 08:05:13'),
(19, 1, 1, 'Complete', '2025-06-28 08:23:00', '2025-06-28 08:23:00'),
(20, 1, 1, 'Modular', '2025-06-28 09:12:36', '2025-06-28 09:12:36'),
(21, 2, 1, 'Modular', '2025-06-28 09:19:07', '2025-06-28 09:19:07'),
(22, 2, 1, 'Modular', '2025-06-28 09:30:15', '2025-06-28 09:30:15'),
(23, 1, 3, 'Modular', '2025-06-28 09:38:10', '2025-06-28 09:38:10'),
(24, 1, 3, 'Modular', '2025-06-28 09:39:54', '2025-06-28 09:39:54'),
(25, 1, 3, 'Complete', '2025-06-28 09:43:17', '2025-06-28 09:43:17'),
(26, 2, 5, 'Modular', '2025-06-28 09:44:32', '2025-06-28 09:44:32'),
(27, 17, 1, 'Complete', '2025-06-28 10:10:54', '2025-06-28 10:10:54'),
(28, 2, 1, 'Modular', '2025-06-28 10:23:20', '2025-06-28 10:23:20'),
(29, 1, 1, 'Complete', '2025-06-28 10:24:35', '2025-06-28 10:24:35'),
(30, 17, 1, 'Complete', '2025-06-28 10:25:30', '2025-06-28 10:25:30'),
(31, 2, 1, 'Complete', '2025-06-30 06:37:54', '2025-06-30 06:37:54');

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
(4, '2025_06_27_000000_add_registration_fields_to_students_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `modules_id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `module_description` text DEFAULT NULL,
  `program_id` int(11) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_by_admin_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `package_name`, `description`, `amount`, `created_by_admin_id`, `created_at`, `updated_at`) VALUES
(1, 'Basic Package', 'Access to lectures, quizzes, and PDF materials.', 2222.00, 1, '2025-06-27 15:14:54', '2025-06-28 06:55:36'),
(2, 'Premium Package', 'Includes Basic features + Live Zoom classes and Q&A sessions.', 0.00, 1, '2025-06-27 15:14:54', '2025-06-27 15:14:54'),
(3, 'Mock Exam Package', 'Includes full-length mock board exams and analytics.', 0.00, 1, '2025-06-27 15:14:54', '2025-06-27 15:14:54'),
(5, 'batman', 'aaa', 0.00, 1, '2025-06-28 06:52:11', '2025-06-28 06:52:11');

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
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plan`
--

INSERT INTO `plan` (`plan_id`, `plan_name`, `description`) VALUES
(1, 'Full Plan', 'Full/Complete Plan Description'),
(2, 'Modular Plan', 'Modular Plan Description');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `created_by_admin_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `program_name`, `created_by_admin_id`, `created_at`, `updated_at`) VALUES
(1, 'Nursing Board Review', 1, '2025-06-27 15:14:54', '2025-06-27 15:14:54'),
(2, 'Engineering Board Review', 1, '2025-06-27 15:14:54', '2025-06-27 15:14:54'),
(17, 'ni', 1, '2025-06-28 02:52:20', '2025-06-28 10:52:20');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `student_school` varchar(50) NOT NULL,
  `street_address` varchar(50) NOT NULL,
  `state_province` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `zipcode` varchar(20) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `emergency_contact_number` varchar(15) NOT NULL,
  `good_moral` varchar(255) DEFAULT NULL,
  `PSA` varchar(255) DEFAULT NULL,
  `Course_Cert` varchar(255) DEFAULT NULL,
  `TOR` varchar(255) DEFAULT NULL,
  `Cert_of_Grad` varchar(255) DEFAULT NULL,
  `Undergraduate` varchar(255) DEFAULT NULL,
  `Graduate` varchar(255) DEFAULT NULL,
  `photo_2x2` varchar(255) DEFAULT NULL,
  `Start_Date` date NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `package_name` varchar(100) DEFAULT NULL,
  `plan_name` varchar(50) DEFAULT NULL,
  `program_name` varchar(100) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `user_id`, `firstname`, `middlename`, `lastname`, `student_school`, `street_address`, `state_province`, `city`, `zipcode`, `contact_number`, `emergency_contact_number`, `good_moral`, `PSA`, `Course_Cert`, `TOR`, `Cert_of_Grad`, `Undergraduate`, `Graduate`, `photo_2x2`, `Start_Date`, `status`, `created_at`, `updated_at`, `package_name`, `plan_name`, `program_name`, `package_id`, `plan_id`, `program_id`) VALUES
(26, 29, 'bryan', 'm', 'justimbaste', 'lpu', 'General Trias Cavite', 'state', 'Cavite', '4107', '9090993452', '3546365436', NULL, NULL, NULL, NULL, NULL, 'yes', 'no', NULL, '2025-06-30', 'pending', '2025-06-30 06:37:54', '2025-06-30 06:37:54', 'Basic Package', 'Complete', 'Engineering Board Review', 1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `student_school` varchar(50) NOT NULL,
  `street_address` varchar(50) NOT NULL,
  `state_province` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `zipcode` varchar(20) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `emergency_contact_number` varchar(15) NOT NULL,
  `good_moral` varchar(255) DEFAULT NULL,
  `PSA` varchar(255) DEFAULT NULL,
  `Course_Cert` varchar(255) DEFAULT NULL,
  `TOR` varchar(255) DEFAULT NULL,
  `Cert_of_Grad` varchar(255) DEFAULT NULL,
  `Undergraduate` varchar(255) DEFAULT NULL,
  `Graduate` varchar(255) DEFAULT NULL,
  `photo_2x2` varchar(255) DEFAULT NULL,
  `Start_Date` date NOT NULL,
  `date_approved` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(100) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `package_name` varchar(100) DEFAULT NULL,
  `plan_name` varchar(50) DEFAULT NULL,
  `program_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_lastname` varchar(255) NOT NULL,
  `role` enum('unverified','student','professor') DEFAULT 'unverified',
  `enrollment_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `user_firstname`, `user_lastname`, `role`, `enrollment_id`, `created_at`, `updated_at`) VALUES
(29, 'bryan.justimbaste@gmail.com', '$2y$10$teKTw.9r5WjxBZ.8CN3mSOBIVYZnBCvKr9D.83tXK8fzXoFogZJPq', 'bryan', 'justimbaste', 'unverified', 31, '2025-06-30 06:37:54', '2025-06-30 06:37:54'),
(30, 'student@test.com', '$2y$10$D66uIkz94nWebIWEyfj2sec05TxRUSFHlXCYVDKyL1XFKJCcmQHCW', 'Test', 'Student', 'student', 1, '2025-06-30 08:07:03', '2025-06-30 08:07:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`),
  ADD KEY `created_by_admin_id` (`created_by_admin_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

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
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`),
  ADD KEY `created_by_admin_id` (`created_by_admin_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_registrations_package_id` (`package_id`),
  ADD KEY `fk_registrations_plan_id` (`plan_id`),
  ADD KEY `fk_registrations_program_id` (`program_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_students_package_id_2025` (`package_id`),
  ADD KEY `fk_students_plan_id_2025` (`plan_id`),
  ADD KEY `fk_students_program_id_2025` (`program_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `modules_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `fk_modules_plan` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`plan_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_modules_programs` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
