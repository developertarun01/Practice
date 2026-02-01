# Servon - Domestic Support Solution

A complete web application for managing domestic services in Delhi. Built for Hostinger single-folder hosting with a client-side home page and comprehensive admin panel.

## Project Structure

```
servon/
├── index.html                          # Home page with contact form
├── admin/
│   ├── login.html                      # Admin login page
│   ├── dashboard.php                   # Admin dashboard
│   ├── leads.php                       # Leads management
│   ├── bookings.php                    # Bookings management
│   ├── payments.php                    # Payments management
│   ├── professionals.php               # Professionals management
│   ├── phone-calls.php                 # Phone calls tracking
│   ├── follow-ups.php                  # Follow-ups management
│   ├── service-requests.php            # Service requests
│   ├── users.php                       # Users management
│   └── includes/
│       └── sidebar.php                 # Admin sidebar navigation
├── api/
│   ├── submit-lead.php                 # Lead submission API
│   ├── login.php                       # Authentication API
│   └── logout.php                      # Logout handler
├── assets/
│   ├── css/
│   │   └── style.css                   # Global styles
│   └── js/
│       └── main.js                     # JavaScript functionality
├── includes/
│   ├── config.php                      # Database configuration
│   └── database.sql                    # Database schema
└── uploads/                            # Document uploads directory
```

## Features

### Client Side

- **Home Page**: Beautiful landing page with service information
- **Contact Form**: Quick form to submit service requests
  - Service type selection
  - Name field (required)
  - Phone number field (required, 10-digit validation)
  - Real-time form validation
  - Smooth submission with confirmation message

### Admin Panel (4 Roles)

#### Dashboard (All Roles)

- Statistics cards for key metrics
- Fresh and in-progress leads overview
- Pending payments display
- Quick action buttons

#### Leads Management

- Filter by name, phone, service, status, date range
- Status tracking (Fresh, In progress, Converted, Dropped)
- Lead details and history
- Follow-up management
- Comments and notes

#### Bookings Management (Admin, Sales, Allocation)

- Create new bookings from leads
- Comprehensive filtering
- Status tracking (In progress, Shortlisted, Assigned, Deployed, Canceled, Unpaid)
- Customer details and preferences
- Job details and requirements

#### Payments Management

- Razorpay integration ready
- Payment link creation
- Status tracking (Pending, Completed, Failed, Refunded)
- Filter and search capabilities
- Amount tracking

#### Professionals Management (Admin, Allocation)

- Professional profile creation and management
- Service categorization
- Experience and rating tracking
- Document verification (Aadhaar, Police Verification)
- Status management (Active, Inactive, On Leave)

#### Phone Calls Tracking (Admin, Sales)

- Call logging with direction (Inbound/Outbound)
- Agent assignment
- Call duration tracking
- Tag-based categorization
- Recording links storage

#### Follow-ups Management

- User-based follow-up tracking
- Channel-based logging (Phone, Email, WhatsApp, SMS)
- Reminder scheduling
- Comments and history

#### Service Requests (Admin, Support)

- Professional to booking assignment
- Status tracking (Open, Closed)
- Remarks and notes
- Deployment tracking

#### Users Management (Admin Only)

- User account creation
- Role assignment (Admin, Sales, Allocation, Support)
- Enable/Disable accounts
- Activity tracking

## Installation & Setup

### 1. Database Setup

```bash
# Connect to your MySQL server and create the database
mysql -u your_user -p

# In MySQL:
mysql> source includes/database.sql;
```

Or use phpMyAdmin to import the SQL file.

### 2. Configuration

Edit `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'servon_db');
define('BASE_URL', 'https://yourdomain.com/');
```

### 3. Upload to Hostinger

1. Create a new public_html folder or use existing
2. Upload all files maintaining the folder structure
3. Ensure `.htaccess` is configured (if needed)

### 4. First Login

- **Email**: admin@servon.com
- **Password**: admin123

**Change this password immediately after first login!**

### 5. Create Additional Users

1. Login to admin panel
2. Go to Users section
3. Create new users with appropriate roles

## Hostinger Hosting Tips

### .htaccess Configuration (if needed)

Create `.htaccess` in root directory:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Prevent direct access to API files
    <Files "*.sql">
        Deny from all
    </Files>

    # Set PHP version if needed
    AddType application/x-httpd-php74 .php
</IfModule>
```

### PHP Requirements

- PHP 7.4 or higher (PHP 8+ recommended)
- MySQL/MariaDB
- cURL extension
- OpenSSL extension

### File Permissions

- `uploads/` folder: 755 (writable)
- All other folders: 755
- PHP files: 644

## Key Functionality

### Lead Management Workflow

1. **Customer submits form** from home page
2. **Lead created** with "Fresh" status
3. **Sales team responds** and updates status to "In progress"
4. **Conversion** to booking or dropped status
5. **Follow-ups** tracked with reminders
6. **Booking created** when customer confirms
7. **Professional assigned** by Allocation team
8. **Payment collected** via Razorpay
9. **Service delivered** and tracked

### Filter Synchronization

Filters work seamlessly across modules:

- **Search filters** apply to name, phone, email
- **Date range filters** for created, deployed, and other timestamps
- **Status filters** show relevant records
- **Service filters** for quick categorization
- Results update dynamically with applied filters

### Role-Based Access

**Admin**: Full access to all modules and settings

**Sales**:

- Leads management
- Phone calls
- Follow-ups
- Bookings
- Payments

**Allocation**:

- Professionals management
- Bookings management
- Follow-ups (own only)

**Support**:

- Service requests
- Follow-ups (own only)
- Dashboard

## Security Features

- Password hashing with SHA-256
- Session-based authentication
- Role-based access control
- Input validation and sanitization
- SQL injection prevention
- CSRF protection ready

## API Endpoints

### Lead Submission

```
POST /api/submit-lead.php
Parameters: name, phone, service
Response: JSON with success status
```

### Authentication

```
POST /api/login.php
Parameters: email, password
Response: JSON with user data
```

### Logout

```
GET /api/logout.php
```

## Customization

### Modifying Services

Edit service list in:

1. `index.html` (home page form)
2. `admin/leads.php` (leads filter)
3. `admin/bookings.php` (bookings filter)
4. `admin/professionals.php` (professionals filter)
5. Database schema in `database.sql`

### Adding New Fields

1. Update database schema
2. Modify corresponding admin page
3. Update form validation in `assets/js/main.js`
4. Update API endpoints if needed

### Styling

All styles are in `assets/css/style.css` with CSS variables for easy theming:

```css
:root {
  --primary-color: #2563eb;
  --secondary-color: #1e40af;
  /* ... other colors ... */
}
```

## Razorpay Integration

To enable Razorpay payments:

1. Get API keys from Razorpay dashboard
2. Store them securely (not in code)
3. Implement payment processing in payments.php
4. Update payment status in database
5. Create order and capture payment

## Support & Maintenance

### Regular Tasks

- Database backups weekly
- User access reviews monthly
- Payment reconciliation daily
- Follow-up reminders setup

### Troubleshooting

**Database connection error**: Check DB credentials in config.php

**Login failure**: Verify users table has entries

**Form submission fails**: Check API file permissions

**Missing styles**: Clear browser cache and reload

## Mobile Responsive

The application is fully responsive with:

- Mobile-friendly navigation
- Touch-friendly buttons and forms
- Optimized table layouts for small screens
- Responsive admin panel

## Performance Tips

1. **Optimize Database**:

   ```sql
   ANALYZE TABLE leads;
   ANALYZE TABLE bookings;
   OPTIMIZE TABLE payments;
   ```

2. **Enable Caching**: Configure PHP caching in Hostinger

3. **Compress Assets**: Minify CSS and JavaScript

4. **Image Optimization**: Compress uploaded documents

## License

This project is proprietary and confidential.

## Contact

For support or customization requests, contact: support@servon.com

---

**Last Updated**: January 2024
**Version**: 1.0
