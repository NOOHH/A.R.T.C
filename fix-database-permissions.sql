-- Database Permissions Fix Script for ARTC Production
-- Run this as root/admin user in your MySQL database

-- 1. Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `artc` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Grant all privileges to smartprep user for the artc database
GRANT ALL PRIVILEGES ON `artc`.* TO 'smartprep'@'%';

-- 3. Grant specific privileges that might be needed
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, REFERENCES ON `artc`.* TO 'smartprep'@'%';

-- 4. Grant privileges for information_schema (needed for migrations)
GRANT SELECT ON `information_schema`.* TO 'smartprep'@'%';

-- 5. Flush privileges to apply changes
FLUSH PRIVILEGES;

-- 6. Verify the grants
SHOW GRANTS FOR 'smartprep'@'%';

-- 7. Test access
USE `artc`;
SHOW TABLES;
