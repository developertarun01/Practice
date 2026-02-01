# Servon Installation Guide for Hostinger

## Prerequisites

- Hostinger account with single-folder hosting
- MySQL database access
- FTP/File Manager access
- PHP 7.4+ support

## Step-by-Step Installation

### Step 1: Prepare Your Database

1. **Access Hostinger Control Panel**
   - Login to Hostinger dashboard
   - Navigate to Databases section
   - Create new database:
     - **Database Name**: servon_db
     - **Username**: servon_user
     - **Password**: Create a strong password

2. **Import Database Schema**
   - Go to phpMyAdmin
   - Select your new database
   - Click "Import"
   - Choose `includes/database.sql` file
   - Click "Go"

### Step 2: Upload Files to Hostinger

1. **Connect via FTP/File Manager**
   - Use File Manager in Hostinger control panel
   - Or connect via FTP with credentials from Hostinger

2. **Upload Servon Folder**
   - Create folder: `/public_html/servon/` (or your preferred location)
   - Upload all project files maintaining folder structure

3. **Directory Structure After Upload**
   ```
   public_html/
   └── servon/
       ├── index.html
       ├── admin/
       ├── api/
       ├── assets/
       ├── includes/
       ├── uploads/
       ├── .htaccess
       └── README.md
   ```

### Step 3: Configure the Application

1. **Edit `includes/config.php`**

   ```
   DB_HOST: localhost
   DB_USER: servon_user
   DB_PASS: [Your database password from Step 1]
   DB_NAME: servon_db
   BASE_URL: https://yourdomain.com/servon/
   ```

2. **Set Folder Permissions**
   - `uploads/` → 755 (read-write)
   - All other folders → 755
   - PHP files → 644

### Step 4: Verify Installation

1. **Visit Home Page**

   ```
   https://yourdomain.com/servon/index.html
   ```

2. **Access Admin Panel**

   ```
   https://yourdomain.com/servon/admin/login.html
   ```

3. **Login with Default Credentials**
   - Email: admin@servon.com
   - Password: admin123

### Step 5: Post-Installation Setup

1. **Change Admin Password**
   - Immediately change default password
   - Create strong password (min 12 characters)

2. **Update Contact Information**
   - Edit `index.html` - Update email address
   - Edit `admin/dashboard.php` - Update site settings

3. **Configure Email Notifications**
   - Set up email service for lead notifications
   - Update admin email in config.php

4. **Enable SSL Certificate**
   - Ensure your Hostinger domain has SSL
   - Update BASE_URL to use https://

### Step 6: Create Additional User Accounts

1. Login to admin panel
2. Navigate to Users section
3. Click "Add User" button
4. Fill in details:
   - Name
   - Email
   - Phone
   - Role (Admin, Sales, Allocation, Support)
5. System will generate temporary password

## Troubleshooting

### Issue: Database Connection Failed

**Solution**:

- Check DB credentials in `includes/config.php`
- Verify database user exists in Hostinger
- Ensure database is created
- Try importing SQL file again

### Issue: Login Page Shows Blank

**Solution**:

- Check PHP version (must be 7.4+)
- Look at browser console for errors
- Check Hostinger error logs
- Verify file permissions

### Issue: Form Not Submitting

**Solution**:

- Check uploads folder exists and is writable
- Verify API path in `assets/js/main.js`
- Check file permissions (755 for folders)
- Review Hostinger error logs

### Issue: Styles Not Loading

**Solution**:

- Clear browser cache (Ctrl+F5)
- Verify correct BASE_URL in config.php
- Check CSS file path in HTML
- Ensure .htaccess is uploaded

### Issue: 404 Errors on Admin Pages

**Solution**:

- Verify folder structure matches exactly
- Check file names are correct (case-sensitive)
- Ensure all PHP files are uploaded
- Review .htaccess configuration

## Security Best Practices

1. **Change Default Credentials**
   - Change admin password immediately
   - Update DB password periodically

2. **Enable HTTPS**
   - Update BASE_URL to https://
   - Redirect HTTP to HTTPS in .htaccess

3. **Regular Backups**
   - Backup database weekly
   - Backup uploaded files monthly
   - Use Hostinger backup features

4. **Monitor Access**
   - Review user access regularly
   - Check admin logs
   - Monitor failed login attempts

5. **Keep Software Updated**
   - Update PHP version when possible
   - Monitor for security patches
   - Review file permissions regularly

## Performance Optimization

### Database Optimization

```sql
OPTIMIZE TABLE leads;
OPTIMIZE TABLE bookings;
OPTIMIZE TABLE payments;
OPTIMIZE TABLE professionals;
```

### Caching Configuration

- Enable Gzip compression in .htaccess
- Use Hostinger caching features
- Implement browser caching headers

### Asset Minification

- Minify CSS in `assets/css/style.css`
- Minify JavaScript in `assets/js/main.js`
- Compress images before upload

## Useful Hostinger Features

### Auto-installer (Alternative Method)

1. Hostinger → Auto-installer
2. Search for PHP apps
3. Deploy custom PHP application
4. Upload files via installer

### Cron Jobs (For Reminders)

Set up cron job to send follow-up reminders:

```
0 9 * * * php /home/user/public_html/servon/api/send-reminders.php
```

### File Manager

- Browse and manage files
- Edit PHP files directly
- Create/delete folders
- Change permissions

### phpMyAdmin

- Direct database management
- Run SQL queries
- Export/import databases
- Create backups

## Domain Configuration

1. **Point Domain to Hostinger**
   - Update nameservers to Hostinger's
   - Or use A records pointing to Hostinger

2. **SSL Certificate**
   - Enable free AutoSSL in Hostinger
   - Update BASE_URL to https://

3. **Add-on Domain (Optional)**
   - If you want subdomain for admin
   - Create addon domain in Hostinger

## Support Resources

- Hostinger Support: https://www.hostinger.com/support
- PHP Documentation: https://www.php.net/docs.php
- MySQL Documentation: https://dev.mysql.com/doc/

## Next Steps After Installation

1. Test all functionality thoroughly
2. Create test leads and bookings
3. Set up payment gateway (Razorpay)
4. Configure email notifications
5. Train team members on usage
6. Set up regular backups
7. Monitor performance and errors

---

**Installation Date**: ******\_\_\_******
**Installer Name**: ******\_\_\_******
**Support Email**: support@servon.com
