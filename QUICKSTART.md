# Servon - Quick Start Guide

## What You Have

A complete, production-ready domestic service management platform with:

- ✅ Client-facing home page with lead generation form
- ✅ Admin panel with 4 user roles
- ✅ 9 management modules
- ✅ Complete database schema
- ✅ Security and authentication system
- ✅ Responsive design for all devices
- ✅ Hostinger-compatible deployment setup

## File Checklist

### Root Files

- [x] index.html - Home page
- [x] .htaccess - Server configuration
- [x] README.md - Full documentation
- [x] INSTALLATION.md - Setup guide
- [x] FEATURES_CHECKLIST.md - Feature status
- [x] PROJECT_OVERVIEW.md - Project details
- [x] QUICKSTART.md - This file

### API Files (api/)

- [x] submit-lead.php - Lead submission
- [x] login.php - User authentication
- [x] logout.php - User logout
- [x] add-comment.php - Add comments to leads
- [x] update-lead-status.php - Update lead status

### Admin Files (admin/)

- [x] login.html - Admin login page
- [x] dashboard.php - Dashboard & statistics
- [x] leads.php - Leads management
- [x] bookings.php - Bookings management
- [x] payments.php - Payments management
- [x] professionals.php - Professional management
- [x] phone-calls.php - Call tracking
- [x] follow-ups.php - Follow-up management
- [x] service-requests.php - Service request tracking
- [x] users.php - User management
- [x] includes/sidebar.php - Navigation sidebar

### Core Files (includes/)

- [x] config.php - Configuration & database connection
- [x] database.sql - Database schema

### Assets (assets/)

- [x] css/style.css - All styling (responsive)
- [x] js/main.js - Client-side functionality

### Directories (auto-created)

- [x] uploads/ - Document storage (755 permissions)

## Quick Installation (5 Steps)

### Step 1: Create Database

```
In Hostinger control panel:
- Create database: servon_db
- Create user: servon_user
- Import: includes/database.sql
```

### Step 2: Upload Files

```
Via FTP or File Manager:
Upload entire 'servon' folder to /public_html/
```

### Step 3: Configure

```
Edit includes/config.php:
- DB_USER: servon_user
- DB_PASS: [your database password]
- BASE_URL: https://yourdomain.com/servon/
```

### Step 4: Set Permissions

```
uploads/ folder: 755 (read-write)
All PHP files: 644
```

### Step 5: Test

```
Home Page: https://yourdomain.com/servon/index.html
Admin Login: https://yourdomain.com/servon/admin/login.html
Email: admin@servon.com
Password: admin123
```

## Default Credentials

**CHANGE IMMEDIATELY AFTER FIRST LOGIN!**

- Email: admin@servon.com
- Password: admin123

## Key Passwords to Change

1. Admin account password
2. Database user password
3. Create new admin user with different email

## Default Services (6)

1. All Rounder
2. Baby Caretaker
3. Cooking Maid
4. House Maid
5. Elderly Care
6. Security Guard

## Admin Roles (4)

1. **Admin** - Full system access
2. **Sales** - Leads, calls, follow-ups, bookings, payments
3. **Allocation** - Professionals, bookings, follow-ups
4. **Support** - Service requests, follow-ups

## Admin Modules (9)

1. **Dashboard** - Statistics and quick overview
2. **Leads** - Customer inquiries management
3. **Bookings** - Confirmed service bookings
4. **Payments** - Payment tracking & Razorpay
5. **Professionals** - Staff/service provider management
6. **Phone Calls** - Call logging & tracking
7. **Follow-ups** - Communication history
8. **Service Requests** - Professional assignments
9. **Users** - Team member management

## Database Tables (10)

1. users - Admin team members
2. leads - Customer inquiries
3. bookings - Confirmed bookings
4. payments - Payment records
5. professionals - Service professionals
6. phone_calls - Call logs
7. follow_ups - Follow-up records
8. service_requests - Service assignments
9. missed_calls - Missed call tracking
10. lead_comments - Notes & comments

## Contact Form Features

✓ Service selection dropdown
✓ Name input (required)
✓ Phone number (required, 10-digit)
✓ Real-time validation
✓ Error messaging
✓ Success confirmation
✓ Prevent duplicate submissions
✓ Mobile responsive

## Admin Panel Features

✓ Responsive sidebar navigation
✓ Role-based menu items
✓ Dashboard with statistics
✓ Advanced filtering in each module
✓ Color-coded status indicators
✓ Real-time search
✓ Date range filtering
✓ Pagination ready
✓ Mobile responsive tables

## Security Features

✓ Password hashing
✓ Session authentication
✓ Role-based access control
✓ Input validation & sanitization
✓ SQL injection prevention
✓ .htaccess protection
✓ Error logging
✓ Secure file permissions

## Hosting Requirements

✓ PHP 7.4+ (8+ recommended)
✓ MySQL 5.7+ (8+ recommended)
✓ cURL extension
✓ OpenSSL extension
✓ Write access to uploads/ folder
✓ .htaccess support

## Troubleshooting

### Form not submitting?

- Check uploads/ folder exists and is 755
- Verify api/submit-lead.php has correct path
- Check browser console for errors

### Login page blank?

- Clear browser cache (Ctrl+F5)
- Check PHP version (must be 7.4+)
- Verify all files uploaded correctly

### Database error?

- Check DB credentials in includes/config.php
- Verify database created in Hostinger
- Try importing database.sql again via phpMyAdmin

### Styles not showing?

- Clear browser cache
- Verify correct BASE_URL in config.php
- Check CSS file path in HTML

### Permission errors?

- uploads/ folder needs 755 permissions
- Use Hostinger file manager to change permissions
- Or use FTP with proper permission settings

## File Modifications Needed

Only modify these files:

1. **includes/config.php**
   - DB_USER, DB_PASS, DB_NAME, BASE_URL

2. **index.html**
   - Contact email address
   - Company information
   - Phone numbers

3. **admin/dashboard.php**
   - Site name and settings

Everything else works as-is!

## Testing Checklist

After installation, test:

- [ ] Home page loads
- [ ] Contact form displays all fields
- [ ] Form validation works
- [ ] Form submission succeeds
- [ ] Admin login page loads
- [ ] Login with default credentials works
- [ ] Dashboard displays
- [ ] All menu items visible based on role
- [ ] Leads module shows new submission
- [ ] Filters work correctly
- [ ] Tables are responsive on mobile

## What's Next?

1. ✓ Change admin password
2. ✓ Create additional user accounts
3. ✓ Configure Razorpay for payments
4. ✓ Set up email notifications
5. ✓ Update contact information
6. ✓ Customize service list (if needed)
7. ✓ Set up domain with SSL
8. ✓ Configure backups
9. ✓ Train team members
10. ✓ Go live!

## Support

- 📖 Read README.md for detailed documentation
- 📋 Check INSTALLATION.md for setup help
- ✅ Review FEATURES_CHECKLIST.md for feature status
- 📊 See PROJECT_OVERVIEW.md for architecture details

## Performance Tips

1. Optimize database: `OPTIMIZE TABLE leads;`
2. Enable Hostinger caching
3. Minify CSS and JavaScript
4. Compress images before upload
5. Use CDN for static files (optional)
6. Set up automated backups

## Backup Your Work

Before going live, backup:

- Database (weekly)
- Uploaded documents (monthly)
- Application files (after major changes)

Use Hostinger's backup features!

## Security Reminders

🔐 Change default password
🔐 Use HTTPS (enable SSL)
🔐 Regular backups
🔐 Monitor admin access logs
🔐 Update PHP regularly
🔐 Use strong passwords
🔐 Disable unused accounts

## Contact & Support

**Email**: support@servon.com
**Status**: Production Ready ✅

---

**You're all set! Servon is ready to deploy.**

For detailed information, refer to the other documentation files:

- README.md (Features & documentation)
- INSTALLATION.md (Step-by-step setup)
- PROJECT_OVERVIEW.md (Complete overview)
- FEATURES_CHECKLIST.md (Feature status)

Happy deploying! 🚀
