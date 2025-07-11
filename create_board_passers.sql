CREATE TABLE IF NOT EXISTS `board_passers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `board_exam` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exam_year` int(11) NOT NULL,
  `exam_date` date DEFAULT NULL,
  `result` enum('PASS','FAIL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `board_passers_student_id_foreign` (`student_id`),
  KEY `board_passers_student_id_board_exam_exam_year_index` (`student_id`,`board_exam`,`exam_year`),
  KEY `board_passers_result_index` (`result`),
  KEY `board_passers_exam_year_index` (`exam_year`),
  CONSTRAINT `board_passers_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
