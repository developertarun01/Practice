# Servon Project - Complete Overview

## Executive Summary

**Servon** is a comprehensive domestic support solution platform designed specifically for the Delhi market. It provides a seamless experience for customers seeking trusted household help while offering a sophisticated admin management system for service coordination.

**Optimized for**: Hostinger Single Folder Hosting
**Technology Stack**: PHP 7.4+, MySQL, HTML5, CSS3, JavaScript
**Status**: Production Ready

---

## Project Components

### 1. Client-Side (Public Facing)

#### Home Page (`index.html`)

A professional landing page showcasing Servon's services with:

**Sections**:

- **Navigation Bar**: Logo and navigation links with smooth scrolling
- **Hero Section**: Eye-catching banner with call-to-action
- **Services Showcase**: Grid display of 6 service categories
  - All Rounder
  - Baby Caretaker
  - Cooking Maid
  - House Maid
  - Elderly Care
  - Security Guard
- **Contact Form**: Lead generation form with:
  - Service type dropdown (6 options)
  - Customer name (required)
  - Phone number (required, 10-digit validation)
  - Real-time validation
  - Success/error messaging
- **Why Choose Us**: Trust-building section with features
- **Footer**: Contact information and links

**Features**:

- Fully responsive design (mobile, tablet, desktop)
- Form validation prevents duplicate submissions
- Auto-clears form after successful submission
- Smooth animations and transitions
- Professional color scheme and typography

---

### 2. Admin Panel

#### Access Control

Four distinct user roles with specific permissions:

1. **Admin** - Full system access
2. **Sales** - Lead and call management
3. **Allocation** - Professional and booking management
4. **Support** - Service request handling

#### Module: Dashboard (All Roles)

**Purpose**: Central hub showing key metrics and quick actions

**Features**:

- Create Booking Link button
- Dashboard cards displaying:
  - Fresh Leads count
  - In Progress Leads count
  - Pending Bookings count
  - Pending Payments count
  - Missed Calls count (last 7 days)
- Recent Leads table (5 latest)
- Pending Payments table (5 latest)
- Quick action buttons for viewing details

#### Module: Leads Management (All Roles)

**Purpose**: Track and manage customer inquiries from home page

**Features**:

- **Comprehensive Filtering**:
  - Search by name or phone number
  - Filter by service type
  - Filter by responder (Admin only)
  - Filter by status (Fresh, In progress, Converted, Dropped)
  - Date range filtering

- **Leads Table Display**:
  - Serial number
  - Customer name
  - Phone number
  - Service requested
  - Current status (color-coded)
  - Creation timestamp
  - Action buttons (View, Edit)

- **Lead Details**:
  - Complete customer information
  - Service history
  - Comments and notes
  - Follow-up history
  - Editable fields
  - Status change capability

- **Data Synchronization**:
  - When customer fills form → Lead created
  - When status changed to "In progress" → Follow-up tracking starts
  - When status changed to "Converted" → Booking can be created
  - When status changed to "Dropped" → Archived with reason

#### Module: Bookings Management (Admin, Sales, Allocation)

**Purpose**: Manage confirmed service bookings

**Features**:

- **Create New Booking**: Link button to create bookings
- **Comprehensive Filtering**:
  - Search by customer name/phone
  - Filter by service type
  - Filter by location
  - Filter by status
  - Date range filtering

- **Bookings Table**:
  - Serial number
  - Customer name and phone
  - Service type
  - Status (In progress, Shortlisted, Assigned, Deployed, Canceled, Unpaid, Hold)
  - Start date
  - Created timestamp
  - Action buttons

- **Booking Details**:
  - Customer information (name, email, phone, address)
  - Service requirements (type, hours, salary bracket)
  - Preferences (gender, family size, replacements)
  - Professional assignments
  - Timeline tracking (start, expiry dates)
  - Payment status
  - Comments and notes

- **Status Workflow**:
  - In progress → Professional search phase
  - Shortlisted → Professionals identified
  - Assigned → Professional confirmed
  - Deployed → Service active
  - Canceled → Service rejected
  - Unpaid → Payment pending
  - Hold → Temporarily paused

#### Module: Payments Management

**Purpose**: Track and manage customer payments

**Features**:

- **New Payment Link Button**: Create Razorpay payment links
- **Comprehensive Filtering**:
  - Search by name/phone
  - Filter by purpose
  - Filter by payment method
  - Filter by status
  - Date range filtering

- **Payments Table**:
  - Serial number
  - Customer name and phone
  - Service type
  - Amount (in ₹)
  - Payment status (Pending, Completed, Failed, Refunded)
  - Creation timestamp
  - Action buttons

- **Payment Details**:
  - Payment link generation
  - Razorpay order tracking
  - Payment ID storage
  - Receipt information
  - Refund management
  - Comments and notes

#### Module: Phone Calls (Admin, Sales Only)

**Purpose**: Track and log all customer interactions via phone

**Features**:

- **Comprehensive Filtering**:
  - Number search
  - Filter by direction (Inbound/Outbound)
  - Filter by agent
  - Filter by tag
  - Date range filtering

- **Calls Table**:
  - Serial number
  - Direction (Inbound/Outbound)
  - Customer number
  - Agent name
  - Duration in seconds
  - Associated tag
  - Creation timestamp
  - Action buttons

- **Call Details**:
  - Recording link storage
  - Tag categorization (service type, status)
  - Agent assignment
  - Call notes and comments
  - Duration tracking
  - Follow-up requirement flagging

#### Module: Follow-ups (All Roles)

**Purpose**: Manage follow-up communications with customers

**Features**:

- **Follow-ups Table**:
  - Serial number
  - Lead/customer name
  - Phone number
  - Direction (Inbound/Outbound)
  - Channel (Phone, Email, WhatsApp, SMS)
  - Comments (truncated display)
  - Reminder timestamp
  - Creation timestamp
  - View button

- **Role-Based Access**:
  - Admin: Sees all follow-ups
  - Other roles: See only their own follow-ups

- **Follow-up Management**:
  - Automatic creation from calls/interactions
  - Reminder scheduling
  - Channel selection for next contact
  - Comments and context tracking
  - History maintenance

#### Module: Professionals (Admin, Allocation Only)

**Purpose**: Maintain professional/staff database

**Features**:

- **Add New Professional Button**: Create professional profiles
- **Comprehensive Filtering**:
  - Search by name/phone
  - Filter by service type
  - Filter by gender
  - Filter by status (Active, Inactive, On Leave)
  - Filter by verification status
  - Filter by experience, rating, language, location
  - Date range filtering

- **Professionals Table**:
  - Serial number
  - Name and phone
  - Service type
  - Experience (in years)
  - Rating (⭐)
  - Current status
  - Verification status (color-coded)
  - Creation timestamp
  - Action buttons

- **Professional Profile**:
  - Contact information
  - Service specializations
  - Experience and qualifications
  - Rating and reviews
  - Salary expectations
  - Language proficiency
  - Food preferences
  - Service area and radius
  - Document uploads:
    - Aadhaar verification
    - Police verification
  - Engagement status
  - Availability tracking

#### Module: Service Requests (Admin, Support Only)

**Purpose**: Track service assignments and deployment

**Features**:

- **Comprehensive Filtering**:
  - Search by professional/booking
  - Filter by status (Open/Closed)
  - Date range filtering

- **Service Requests Table**:
  - Serial number
  - Professional name and phone
  - Associated booking ID
  - Request status (Open/Closed)
  - Created by (staff member)
  - Deployment timestamp
  - Creation timestamp
  - Action buttons

- **Request Details**:
  - Professional assignment details
  - Booking linkage
  - Service remarks
  - Status tracking
  - Deployment tracking
  - Edit capability for status and remarks

#### Module: Users Management (Admin Only)

**Purpose**: Manage team member accounts and permissions

**Features**:

- **Add New User Button**: Create new team member accounts
- **Comprehensive Filtering**:
  - Search by name/email/phone
  - Filter by role
  - Filter by status (Enabled/Disabled)
  - Date range filtering

- **Users Table**:
  - Serial number
  - Name and email
  - Phone number
  - Assigned role
  - Account status (Enabled/Disabled)
  - Creation timestamp
  - Action buttons

- **User Management**:
  - Create new users with email
  - Assign roles (Admin, Sales, Allocation, Support)
  - Enable/disable accounts
  - Password reset capability
  - Activity logging
  - Edit capabilities

---

## Data Flow and Synchronization

### Lead Lifecycle

```
1. Customer submits home page form
   ↓
2. Lead created with status "Fresh"
   ↓
3. Sales team contacts customer
   ↓
4. Status updated to "In progress"
   ↓
5. Follow-ups created and tracked
   ↓
6a. Customer confirms → Status "Converted" → Booking created
6b. Customer rejects → Status "Dropped" → Archived
   ↓
7. Allocation assigns professional
   ↓
8. Service request created
   ↓
9. Payment collected
   ↓
10. Professional deployed
    ↓
11. Service completed
```

### Filter Synchronization Across Modules

**Search Filters**:

- Applied consistently across all modules
- Search by name, phone, email (where applicable)
- Case-insensitive matching

**Date Filters**:

- From Date / To Date pattern
- Applied to creation, deployment, deployment, expiry dates
- Prevents invalid date ranges (from > to)

**Status Filters**:

- Module-specific status options
- Color-coded for quick identification
- Consistent filter UI across all pages

**Service Filters**:

- All 6 services available consistently
- Prevents typos or invalid entries
- Synchronized with database ENUM values

---

## Technical Architecture

### File Organization

```
servon/
├── Frontend Assets
│   ├── index.html (Home page)
│   ├── assets/css/style.css (Global styling)
│   └── assets/js/main.js (Client-side logic)
│
├── Backend API
│   ├── api/submit-lead.php (Lead submission)
│   ├── api/login.php (Authentication)
│   ├── api/logout.php (Logout)
│   ├── api/add-comment.php (Comments)
│   └── api/update-lead-status.php (Status updates)
│
├── Admin Panel
│   ├── admin/login.html (Login page)
│   ├── admin/dashboard.php (Dashboard)
│   ├── admin/leads.php (Leads module)
│   ├── admin/bookings.php (Bookings module)
│   ├── admin/payments.php (Payments module)
│   ├── admin/professionals.php (Professionals module)
│   ├── admin/phone-calls.php (Calls module)
│   ├── admin/follow-ups.php (Follow-ups module)
│   ├── admin/service-requests.php (Service requests)
│   ├── admin/users.php (Users management)
│   └── admin/includes/sidebar.php (Navigation)
│
├── Core System
│   ├── includes/config.php (Configuration & helpers)
│   ├── includes/database.sql (Database schema)
│   ├── .htaccess (Web server config)
│   │
├── Documentation
│   ├── README.md (Complete documentation)
│   ├── INSTALLATION.md (Setup guide)
│   ├── FEATURES_CHECKLIST.md (Feature list)
│   └── PROJECT_OVERVIEW.md (This file)
│
└── Data Storage
    └── uploads/ (Document uploads)
```

### Database Schema

**10 Tables** with proper relationships and indexing:

1. **users** - Team member accounts with roles
2. **leads** - Customer inquiries
3. **bookings** - Confirmed service bookings
4. **payments** - Payment transactions
5. **professionals** - Service professional profiles
6. **phone_calls** - Call logging and tracking
7. **follow_ups** - Follow-up communications
8. **service_requests** - Professional deployments
9. **missed_calls** - Missed call tracking
10. **lead_comments** - Lead notes and comments

### Security Implementation

✓ Password hashing (SHA-256)
✓ Session-based authentication
✓ Role-based access control
✓ Input validation and sanitization
✓ SQL injection prevention
✓ CSRF protection ready
✓ Sensitive file access restricted
✓ Error logging configured

### Performance Optimization

✓ Database indexes on frequently queried fields
✓ Efficient query construction
✓ Browser caching headers
✓ Gzip compression enabled
✓ CSS and JavaScript optimization ready
✓ File permission optimization

---

## Deployment Information

### Hostinger Compatibility

✓ Single-folder hosting compatible
✓ PHP 7.4+ required
✓ MySQL database required
✓ .htaccess configured for security
✓ No external dependencies required
✓ cURL and OpenSSL extensions needed

### Installation Steps

1. Create database in Hostinger → Import schema
2. Upload files via FTP/File Manager
3. Update config.php with database credentials
4. Set folder permissions (755 for folders, 644 for files)
5. Access admin at `/admin/login.html`
6. Login with admin@servon.com / admin123
7. Change default password immediately
8. Create additional user accounts
9. Configure Razorpay for payments
10. Set up email notifications

---

## Feature Highlights

### For Customers

- ✓ Simple, intuitive form submission
- ✓ 10-digit phone number validation
- ✓ Clear service descriptions
- ✓ Fast response (within 2 hours promised)
- ✓ Professional appearance

### For Admin

- ✓ Complete lead lifecycle management
- ✓ Role-based access control
- ✓ Comprehensive filtering and search
- ✓ Real-time dashboard updates
- ✓ Multi-channel communication tracking
- ✓ Professional verification system
- ✓ Payment integration ready
- ✓ Detailed analytics ready

### For Team

- ✓ Clear role assignments
- ✓ Task-based modules
- ✓ Follow-up reminders
- ✓ Communication history
- ✓ Performance tracking

---

## Future Enhancement Opportunities

- SMS/WhatsApp integration
- Advanced analytics and reporting
- Bulk import/export functionality
- Mobile application
- Advanced scheduling system
- Geographic heatmaps
- AI-based matching
- Customer ratings system
- Automated reminders
- Multi-language support

---

## Support Resources

**Documentation Files**:

1. README.md - Complete feature documentation
2. INSTALLATION.md - Step-by-step installation guide
3. FEATURES_CHECKLIST.md - Feature list and status
4. PROJECT_OVERVIEW.md - This comprehensive overview

**Contact**: support@servon.com

---

## Version Information

- **Product Name**: Servon - Domestic Support Solution
- **Version**: 1.0 (Production Ready)
- **Released**: January 2024
- **License**: Proprietary
- **Target Platform**: Hostinger Single Folder Hosting
- **Technology**: PHP + MySQL + HTML5 + CSS3 + JavaScript

---

## Conclusion

Servon is a complete, production-ready solution for managing domestic services in the Delhi market. Built with security, scalability, and user experience in mind, it provides separate interfaces for customers and administrators while maintaining tight data synchronization and control. The system is fully compatible with Hostinger single-folder hosting and requires no external dependencies beyond standard web hosting features.

**Ready for deployment and immediate use.**

---

**Project Status**: ✅ COMPLETE AND READY FOR PRODUCTION
**Last Updated**: January 17, 2024
