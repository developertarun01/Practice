# 🚀 Quick Start Guide - Professional Enhancements

## STEP-BY-STEP IMPLEMENTATION

### Step 1: Backup Your Database ⚠️

```
Open phpMyAdmin → Select your database → Export
Save the SQL file somewhere safe
```

### Step 2: Update Files

All required files have been updated:

- ✓ `assets/js/main.js` - Form modals
- ✓ `api/create-professional.php` - API for creating professionals
- ✓ `api/update-professional.php` - API for editing professionals
- ✓ `admin/professionals.php` - Professionals list with new filter
- ✓ `database-migration.php` - NEW: Database migration tool

### Step 3: Run Database Migration 🗄️

**EASIEST METHOD (Recommended):**

1. Open your browser and go to:
   ```
   http://localhost/freelance/public_html/database-migration.php
   ```
2. You'll see a status page showing which columns exist
3. If columns are missing, click "Run Migration Now"
4. Wait for the success message

**MANUAL METHOD (If above doesn't work):**

1. Open phpMyAdmin
2. Select your database
3. Go to SQL tab
4. Copy and paste this:
   ```sql
   ALTER TABLE professionals ADD COLUMN skills TEXT DEFAULT NULL AFTER hours;
   ALTER TABLE professionals ADD COLUMN id_proof_front VARCHAR(255) DEFAULT NULL AFTER staff_image;
   ALTER TABLE professionals ADD COLUMN id_proof_back VARCHAR(255) DEFAULT NULL AFTER id_proof_front;
   ALTER TABLE professionals ADD COLUMN police_verification VARCHAR(255) DEFAULT NULL AFTER id_proof_back;
   ```
5. Click Execute

### Step 4: Test the New Features ✅

**Create a Test Professional:**

1. Login to admin panel
2. Go to Professionals → + Add Professional
3. Fill basic details
4. **NEW:** Select Status (Active/Inactive/On Leave)
5. **NEW:** Enter Skills (e.g., "Cooking, Cleaning")
6. **NEW:** Upload ID Proof Front
7. **NEW:** Upload ID Proof Back
8. **NEW:** Upload Police Verification
9. Submit

**Test Filters:**

1. Go to Professionals list
2. **NEW:** Set Hours Per Day Min: 4
3. **NEW:** Set Hours Per Day Max: 8
4. Apply filters - you should see the filter tags

**Test Edit:**

1. Click View on a professional
2. Click Edit
3. Change Status or Skills
4. Update files if needed
5. Save

### Step 5: Cleanup (Optional)

After everything is working, you can delete:

```
database-migration.php
```

---

## 📋 NEW FORM FIELDS SUMMARY

### When Creating Professional:

| Field                   | Type         | Required | Details                             |
| ----------------------- | ------------ | -------- | ----------------------------------- |
| Name                    | Text         | ✓        | Professional name                   |
| Phone                   | Tel          | ✓        | 10-digit number                     |
| Email                   | Email        |          | Email address                       |
| Service                 | Select       | ✓        | All Rounder, Baby Caretaker, etc.   |
| Gender                  | Select       | ✓        | Male, Female, Other                 |
| Experience              | Number       |          | Years of experience                 |
| Hours Per Day           | Number       |          | 1-24 hours                          |
| Location                | Text         |          | Work location                       |
| **Status**              | **Select**   |          | **NEW: Active, Inactive, On Leave** |
| **Skills**              | **TextArea** |          | **NEW: Comma-separated skills**     |
| Staff Photo             | File         |          | JPG, PNG, GIF (5MB max)             |
| **ID Proof Front**      | **File**     |          | **NEW: JPG, PNG (5MB max)**         |
| **ID Proof Back**       | **File**     |          | **NEW: JPG, PNG (5MB max)**         |
| **Police Verification** | **File**     |          | **NEW: JPG, PNG, PDF (5MB max)**    |

### When Filtering:

| Filter         | Options    | Details                        |
| -------------- | ---------- | ------------------------------ |
| Search         | Text       | Name, Phone, Location          |
| Service        | Dropdown   | 6 service types                |
| Status         | Dropdown   | Active, Inactive, On Leave     |
| Gender         | Dropdown   | Male, Female                   |
| Verification   | Dropdown   | Verified, Pending, Rejected    |
| Created Before | Date       | Date range                     |
| **Hours Min**  | **Number** | **NEW: Minimum hours per day** |
| **Hours Max**  | **Number** | **NEW: Maximum hours per day** |

---

## 🎯 COMMON QUESTIONS

**Q: What if my database already has these columns?**
A: The migration script will skip them automatically.

**Q: Can I upload different file types?**
A:

- Staff Photo: JPG, PNG, GIF
- ID Proof: JPG, PNG only
- Police Verification: JPG, PNG, PDF

**Q: Can I remove the old id_proof_image field?**
A: Not recommended for backward compatibility, but you can after updating all existing professionals.

**Q: What file size limit is enforced?**
A: 5MB per file. To increase, modify the API files.

**Q: Will an old professional's data still work?**
A: Yes! New fields are optional (NULL by default). Existing data is preserved.

**Q: How do I display skills on the frontend?**
A: The `skills` field is stored in the database and displays in the professional details view.

---

## 🔍 FILE CHECKLIST

After implementation, verify these files exist and are updated:

```
✓ admin/professionals.php (Updated - has new filters)
✓ api/create-professional.php (Updated - handles new fields)
✓ api/update-professional.php (Updated - handles new fields)
✓ assets/js/main.js (Updated - new form fields)
✓ database-migration.php (NEW - migration tool)
✓ PROFESSIONAL_ENHANCEMENTS.md (NEW - this documentation)
```

---

## 🆘 NEED HELP?

1. **Database migration fails:**
   - Check file permission: `uploads/professionals/` should be writable
   - Verify database user has ALTER permission
   - Check MySQL error in browser console

2. **Files not uploading:**
   - Check upload directory exists: `uploads/professionals/`
   - Set permissions: `chmod 755 uploads/professionals/`
   - Check PHP config: `upload_max_filesize` and `post_max_size`

3. **Filter not working:**
   - Verify database migration completed
   - Clear browser cache
   - Reload the page

4. **New fields not showing:**
   - Clear browser cache (Ctrl+F5)
   - Check if JavaScript loaded properly
   - Check browser console for errors

---

## ✨ FEATURES AT A GLANCE

**NEW in Create Form:**

- ✓ Status dropdown
- ✓ Skills textarea
- ✓ Separate ID proof front/back
- ✓ Police verification document

**NEW in Edit Form:**

- ✓ All features from create form
- ✓ Shows existing file status
- ✓ Can replace individual files

**NEW in View Form:**

- ✓ Shows skills
- ✓ Shows separate ID proof images
- ✓ Shows police verification link

**NEW in Filters:**

- ✓ Hours Per Day Range filter
- ✓ Shows in active filters
- ✓ Works with other filters

---

**Ready to go! Follow the 5 steps above and you're done.** 🎉
