-- Update chats table for encryption
-- Rename message column to body_cipher
ALTER TABLE chats CHANGE COLUMN message body_cipher TEXT NOT NULL;

-- Add is_read column if it doesn't exist
ALTER TABLE chats ADD COLUMN is_read BOOLEAN DEFAULT FALSE AFTER body_cipher;

-- Add indexes for better performance
CREATE INDEX chats_conversation_index ON chats(sender_id, receiver_id, sent_at);
CREATE INDEX chats_unread_index ON chats(receiver_id, is_read);
CREATE INDEX chats_sent_at_index ON chats(sent_at);

-- Add online status to users table
ALTER TABLE users ADD COLUMN is_online BOOLEAN DEFAULT FALSE AFTER role;
ALTER TABLE users ADD COLUMN last_seen TIMESTAMP NULL AFTER is_online;

-- Add indexes for users table
CREATE INDEX users_role_online_index ON users(role, is_online);
CREATE INDEX users_last_seen_index ON users(last_seen);
