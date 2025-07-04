INSERT INTO `form_requirements` (`field_name`, `field_label`, `field_type`, `program_type`, `is_required`, `is_active`, `field_options`, `validation_rules`, `sort_order`, `created_at`, `updated_at`, `section_name`) VALUES
('section_1', 'Personal Information', 'section', 'both', 0, 1, NULL, NULL, 1, NOW(), NOW(), 'Personal Information'),
('phone_number', 'Phone Number', 'tel', 'both', 1, 1, NULL, NULL, 2, NOW(), NOW(), 'Personal Information'),
('emergency_contact', 'Emergency Contact', 'tel', 'both', 1, 1, NULL, NULL, 3, NOW(), NOW(), 'Personal Information'),
('section_2', 'Educational Background', 'section', 'both', 0, 1, NULL, NULL, 4, NOW(), NOW(), 'Educational Background'),
('highest_education', 'Highest Educational Attainment', 'select', 'both', 1, 1, '["High School", "Bachelor Degree", "Master Degree", "Doctorate"]', NULL, 5, NOW(), NOW(), 'Educational Background'),
('section_3', 'Documents', 'section', 'both', 0, 1, NULL, NULL, 6, NOW(), NOW(), 'Documents'),
('tor_document', 'Transcript of Records (TOR)', 'file', 'both', 1, 1, NULL, NULL, 7, NOW(), NOW(), 'Documents'),
('diploma_document', 'Diploma/Certificate', 'file', 'both', 1, 1, NULL, NULL, 8, NOW(), NOW(), 'Documents');
