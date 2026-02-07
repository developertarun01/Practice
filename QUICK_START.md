# Servon - Quick Start Guide for New Features

## What's New?

All incomplete functions have been completed! The Servon admin panel now has full functionality for managing leads, bookings, payments, professionals, and users.

## Quick Navigation

### For Admin Users

1. **Dashboard** - View all key metrics and recent activity
2. **Leads** - Search, filter, view, and manage customer inquiries
3. **Bookings** - Create and manage service bookings
4. **Payments** - Create payment links and track payment status
5. **Professionals** - Manage service professionals and verify credentials
6. **Users** - Create and manage admin users
7. **Phone Calls** - Log and track phone interactions
8. **Follow-ups** - Schedule and track customer follow-ups
9. **Service Requests** - Manage professional deployments

## Using the New Modal Features

### Creating New Records

1. **Create Booking**
   - Click "Create Booking Link" button on dashboard or "New Booking" on bookings page
   - Fill in customer details (name, phone, service)
   - Set address and job hours
   - Click "Create Booking"

2. **Create Payment**
   - Click "New Payment Link" on payments page
   - Enter customer information
   - Specify purpose and amount
   - Select payment method
   - Click "Create Payment Link"

3. **Add Professional**
   - Click "Add Professional" on professionals page
   - Enter name, phone, service type
   - Select gender and experience level
   - Click "Add Professional"

4. **Create User**
   - Click "Add User" on users page (Admin only)
   - Enter name, email, phone
   - Set password (min 6 characters)
   - Select role (Admin, Sales, Allocation, Support)
   - Click "Add User"

### Viewing Records

1. Click any **"View"** button in a table
2. A modal window will open with complete details
3. For leads, you can:
   - View all comments
   - Add new comments inline
   - Click "Close" to return to list

### Editing Records

1. Click **"View"** to open record details
2. Click **"Edit"** button
3. Modify the information
4. Click **"Save Changes"**
5. The page will refresh with updated data

### Adding Comments to Leads

1. Open a lead by clicking "View"
2. Scroll to comments section
3. Type comment in the text area
4. Click "Add Comment"
5. Comment will appear immediately

## Keyboard Shortcuts

- **Escape** - Close any open modal
- **Tab** - Navigate between form fields
- **Enter** - Submit forms (when focused on a button)

## Form Validation

All forms validate data before submission. Common requirements:

- **Names:** Minimum 3 characters
- **Phone Numbers:** Exactly 10 digits (format: 1234567890)
- **Email:** Valid email format (format: user@domain.com)
- **Passwords:** Minimum 6 characters
- **Amounts:** Must be greater than 0
- **Services:** Must select from predefined list

If validation fails, you'll see error messages above the form fields.

## Filtering Data

All list pages support multiple filters:

1. **Search** - Find by name or phone number
2. **Service** - Filter by service type
3. **Status** - Filter by current status
4. **Date Range** - Filter by creation date
5. **Responder** (Leads only) - Filter by assigned staff

Click "Filter" to apply, or leave fields blank to show all.

## Role-Based Access

### Admin

- Full access to all features and data
- Can create/edit/delete users
- Can verify professionals
- Can view all leads and bookings

### Sales

- Can view and manage leads
- Can create bookings
- Can create payments
- Can log phone calls
- Cannot manage professionals or users

### Allocation

- Can manage professionals
- Can create bookings
- Can view all data
- Cannot manage users

### Support

- Can view all data
- Can create payments
- Can manage service requests
- Cannot modify bookings or professionals

## Common Tasks

### Convert a Lead to Booking

1. Go to Leads page
2. Search for the lead
3. Click "View"
4. Note the lead details
5. Go to Bookings page
6. Click "New Booking"
7. Fill in the lead's information
8. Set the lead_id to link them
9. Click "Create Booking"

### Generate Payment Link

1. Go to Payments page
2. Click "New Payment Link"
3. Enter customer details
4. Set the amount and purpose
5. Select payment method
6. Share the payment link with customer

### Update Professional Status

1. Go to Professionals page
2. Click "View" on the professional
3. Click "Edit"
4. Change the status (Active/Inactive/On Leave)
5. Update verification status if needed
6. Click "Save Changes"

### Track a Follow-up

1. Go to Follow-ups page
2. View follow-ups for a lead
3. Set reminder date/time
4. Select communication channel
5. Add comments about the interaction

## Troubleshooting

### Modal Won't Open

- Check that JavaScript is enabled
- Try refreshing the page
- Check browser console for errors (F12)

### Form Won't Submit

- Verify all required fields are filled
- Check for validation errors (red messages)
- Ensure phone numbers are exactly 10 digits
- Try refreshing and resubmitting

### Data Not Updating

- Check that you have permission for that action
- Verify the database connection is working
- Look for error messages after clicking save
- Try logging out and back in

### Missing Comments

- Ensure you have permission to add comments
- Check that the lead ID is correct
- Verify the comment text is not empty

## Tips & Best Practices

1. **Use Clear Descriptions** - Add meaningful comments to leads
2. **Set Reminders** - Schedule follow-ups to avoid missing calls
3. **Verify Professionals** - Check documents before marking as verified
4. **Tag Calls** - Use consistent tags for phone calls (e.g., "Interested", "Not Interested")
5. **Keep Updated** - Regularly update lead status to track progress
6. **Regular Backups** - Backup database regularly for data safety

## Getting Help

1. **Check Error Messages** - Most errors explain what went wrong
2. **Review Validation Rules** - Ensure data format matches requirements
3. **Check Permissions** - Verify your role allows this action
4. **Verify Connections** - Ensure database and internet connection are active
5. **Contact Administrator** - If you need access or permissions changed

## Next Steps

1. Test creating a new lead through the home page
2. Convert leads to bookings in the admin panel
3. Generate payment links for bookings
4. Add professionals to the system
5. Create follow-ups for important leads
6. Monitor dashboard metrics

## Support

For issues or questions:

- Check the API_DOCUMENTATION.md for technical details
- Review COMPLETION_REPORT.md for all completed functions
- Contact your administrator for permission issues
- Check browser console (F12) for detailed error information

---

**Enjoy using Servon!** Your domestic support management system is now fully functional.
