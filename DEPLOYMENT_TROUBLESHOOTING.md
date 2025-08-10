# ARTC Deployment Troubleshooting Guide

## Database Connection Error: "Connection refused"

### Problem
When deploying with Sevalla/Forge, you get this error:
```
SQLSTATE[HY000] [2002] Connection refused
```

### Root Cause
The application cannot connect to the MySQL database server because:
1. Environment variables are not properly set
2. Database server is not running
3. Database credentials are incorrect
4. Network/firewall issues

## Database Permission Error: "Access denied for user"

### Problem
When deploying with Sevalla/Forge, you get this error:
```
SQLSTATE[HY000] [1044] Access denied for user 'smartprep'@'%' to database 'artc'
```

### Root Cause
The database user doesn't have proper permissions to access the database:
1. Database doesn't exist
2. User doesn't have privileges on the database
3. User doesn't have access to information_schema
4. Database permissions not properly configured

## Solutions

### 1. Check Environment Variables in Sevalla/Forge

**In your Sevalla/Forge dashboard:**

1. Go to your **Server** → **Sites** → **Your Site**
2. Click on **Environment** or **Environment Variables**
3. Make sure these variables are set:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=artc
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
APP_URL=https://your-domain.com
APP_ENV=production
APP_DEBUG=false
```

### 2. Verify Database Server

**Check if MySQL is running:**
```bash
sudo systemctl status mysql
# or
sudo service mysql status
```

**If not running, start it:**
```bash
sudo systemctl start mysql
# or
sudo service mysql start
```

### 3. Test Database Connection

**SSH into your server and test:**
```bash
mysql -u your_username -p -h your_host
```

**Or test from Laravel:**
```bash
php artisan tinker
DB::connection()->getPdo();
```

### 4. Check Database Permissions

**Make sure your database user has proper permissions:**
```sql
-- For localhost connections
GRANT ALL PRIVILEGES ON artc.* TO 'your_username'@'localhost';
FLUSH PRIVILEGES;

-- For any host connections (like Kubernetes)
GRANT ALL PRIVILEGES ON artc.* TO 'your_username'@'%';
GRANT SELECT ON information_schema.* TO 'your_username'@'%';
FLUSH PRIVILEGES;
```

**For Kubernetes/Sevalla deployments:**
```sql
-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `artc` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant all privileges to smartprep user
GRANT ALL PRIVILEGES ON `artc`.* TO 'smartprep'@'%';
GRANT SELECT ON `information_schema`.* TO 'smartprep'@'%';
FLUSH PRIVILEGES;
```

### 5. Common Database Host Values

- **Local MySQL:** `127.0.0.1` or `localhost`
- **Remote MySQL:** Your database server IP
- **Cloud Database (AWS RDS):** Your RDS endpoint
- **Cloud Database (DigitalOcean):** Your database cluster endpoint

### 6. Firewall Issues

**Check if port 3306 is open:**
```bash
sudo ufw status
```

**If needed, allow MySQL port:**
```bash
sudo ufw allow 3306
```

## Quick Fix Steps

1. **SSH into your server**
2. **Navigate to your project:**
   ```bash
   cd /path/to/your/project
   ```

3. **Check current .env:**
   ```bash
   cat .env | grep DB_
   ```

4. **Update database settings:**
   ```bash
   nano .env
   # Update DB_HOST, DB_USERNAME, DB_PASSWORD
   ```

5. **Test connection:**
   ```bash
   php artisan migrate:status
   ```

6. **If successful, deploy:**
   ```bash
   php artisan migrate
   ```

## Production Checklist

- [ ] Database server is running
- [ ] Environment variables are set correctly
- [ ] Database user has proper permissions
- [ ] Firewall allows database connections
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_URL is set to your domain

## Still Having Issues?

1. **Check Sevalla/Forge logs** for more detailed error messages
2. **Verify database exists:** `SHOW DATABASES;`
3. **Check user permissions:** `SHOW GRANTS FOR 'your_username'@'localhost';`
4. **Test with simple connection script**

## Emergency Database Setup

If you need to create the database:

```sql
CREATE DATABASE artc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'artc_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON artc.* TO 'artc_user'@'localhost';
FLUSH PRIVILEGES;
```

Then update your .env:
```bash
DB_DATABASE=artc
DB_USERNAME=artc_user
DB_PASSWORD=your_password
```
