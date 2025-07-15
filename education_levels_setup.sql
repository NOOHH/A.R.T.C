-- Create education_levels table
CREATE TABLE IF NOT EXISTS education_levels (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    level_name VARCHAR(255) NOT NULL UNIQUE,
    file_requirements JSON,
    available_for_general BOOLEAN DEFAULT 1,
    available_for_professional BOOLEAN DEFAULT 1,
    available_for_review BOOLEAN DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Insert default education levels
INSERT INTO education_levels (level_name, file_requirements, available_for_general, available_for_professional, available_for_review, created_at, updated_at) VALUES
('Undergraduate', '{"School ID": {"required": true, "type": "image", "description": "Valid school identification"}, "TOR file upload": {"required": true, "type": "pdf", "description": "Transcript of Records"}, "Good Moral file upload": {"required": true, "type": "pdf", "description": "Certificate of Good Moral Character"}, "PSA file upload": {"required": true, "type": "pdf", "description": "PSA Birth Certificate"}}', 1, 1, 1, NOW(), NOW()),
('Graduate', '{"School ID": {"required": true, "type": "image", "description": "Valid school identification"}, "Diploma": {"required": true, "type": "pdf", "description": "College Diploma"}, "TOR file upload": {"required": true, "type": "pdf", "description": "Transcript of Records"}, "PSA file upload": {"required": true, "type": "pdf", "description": "PSA Birth Certificate"}}', 1, 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
level_name = VALUES(level_name),
file_requirements = VALUES(file_requirements),
available_for_general = VALUES(available_for_general),
available_for_professional = VALUES(available_for_professional),
available_for_review = VALUES(available_for_review),
updated_at = NOW();
