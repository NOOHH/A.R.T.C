-- Check if column exists and add it if not
ALTER TABLE chats ADD COLUMN is_read BOOLEAN DEFAULT FALSE AFTER body_cipher;

-- Add online status to users table
ALTER TABLE users ADD COLUMN is_online BOOLEAN DEFAULT FALSE AFTER role;
ALTER TABLE users ADD COLUMN last_seen TIMESTAMP NULL AFTER is_online;
