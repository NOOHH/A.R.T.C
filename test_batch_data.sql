-- Test data for student_batches table
-- This will create some sample batches for testing

INSERT INTO student_batches (batch_name, program_id, max_capacity, current_capacity, batch_status, registration_deadline, start_date, description, created_at, updated_at) VALUES
('Culinary Batch 1', 23, 10, 0, 'available', '2025-07-20', '2025-07-25', 'First batch for Culinary program with comprehensive cooking fundamentals', NOW(), NOW()),
('Culinary Batch 2', 23, 15, 9, 'ongoing', '2025-07-15', '2025-07-18', 'Second batch currently in progress, still accepting new students', NOW(), NOW()),
('Culinary Batch 3', 23, 8, 8, 'closed', '2025-07-05', '2025-07-10', 'Third batch - fully enrolled and closed for new registrations', NOW(), NOW()),
('Engineering Batch A', 26, 12, 0, 'available', '2025-07-22', '2025-07-28', 'Engineering fundamentals batch focusing on core principles', NOW(), NOW()),
('Engineering Batch B', 26, 20, 5, 'available', '2025-07-25', '2025-08-01', 'Advanced engineering batch with project-based learning', NOW(), NOW());
