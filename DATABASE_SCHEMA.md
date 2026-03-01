# Database Schema Reference

## Current Professionals Table Structure

### Existing Columns:

```sql
CREATE TABLE professionals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100),
    service VARCHAR(50) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    experience INT DEFAULT 0,
    location VARCHAR(100),
    status VARCHAR(20) DEFAULT 'Active',  -- Active, Inactive, On Leave
    verify_status VARCHAR(20) DEFAULT 'Pending',  -- Pending, Verified, Rejected
    hours INT DEFAULT 8,
    rating FLOAT DEFAULT 0,
    staff_image VARCHAR(255),
    id_proof_image VARCHAR(255),  -- Old field, kept for compatibility
    professional_slug VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

---

## ✨ NEW Columns Being Added

### Column 1: skills

```sql
ALTER TABLE professionals ADD COLUMN skills TEXT DEFAULT NULL AFTER hours;

-- Type: TEXT
-- Position: After 'hours' column
-- Purpose: Store comma-separated professional skills
-- Example Data: "Cooking, Cleaning, Childcare, Laundry"
-- Nullable: Yes
```

### Column 2: id_proof_front

```sql
ALTER TABLE professionals ADD COLUMN id_proof_front VARCHAR(255) DEFAULT NULL AFTER staff_image;

-- Type: VARCHAR(255)
-- Position: After 'staff_image' column
-- Purpose: Path to front side of ID proof document
-- Example Data: "uploads/professionals/idproof_front_1677654321_abc123.jpg"
-- Nullable: Yes
-- File Types: JPG, PNG
```

### Column 3: id_proof_back

```sql
ALTER TABLE professionals ADD COLUMN id_proof_back VARCHAR(255) DEFAULT NULL AFTER id_proof_front;

-- Type: VARCHAR(255)
-- Position: After 'id_proof_front' column
-- Purpose: Path to back side of ID proof document
-- Example Data: "uploads/professionals/idproof_back_1677654321_xyz789.jpg"
-- Nullable: Yes
-- File Types: JPG, PNG
```

### Column 4: police_verification

```sql
ALTER TABLE professionals ADD COLUMN police_verification VARCHAR(255) DEFAULT NULL AFTER id_proof_back;

-- Type: VARCHAR(255)
-- Position: After 'id_proof_back' column
-- Purpose: Path to police verification document
-- Example Data: "uploads/professionals/police_verification_1677654321_pqr456.pdf"
-- Nullable: Yes
-- File Types: JPG, PNG, PDF
```

---

## 📊 Complete Updated Table Structure

After migration, your `professionals` table will have this structure:

```
Column Name              | Data Type        | Nullable | Default      | Notes
------------------------+------------------+----------+--------------+--------------------
id                       | INT              | NO       | AUTO_INCREMENT | Primary Key
name                     | VARCHAR(100)     | NO       | -            | Required
phone                    | VARCHAR(20)      | NO       | -            | Unique, Required
email                    | VARCHAR(100)     | YES      | NULL         | Optional
service                  | VARCHAR(50)      | NO       | -            | Required
gender                   | VARCHAR(20)      | NO       | -            | Required
experience               | INT              | -        | 0            | Years
location                 | VARCHAR(100)     | YES      | NULL         | City/Area
status                   | VARCHAR(20)      | -        | 'Active'     | Active/Inactive/OnLeave
verify_status            | VARCHAR(20)      | -        | 'Pending'    | Pending/Verified/Rejected
hours                    | INT              | -        | 8            | Hours per day
rating                   | FLOAT            | -        | 0            | Star rating
staff_image              | VARCHAR(255)     | YES      | NULL         | Photo path
id_proof_image           | VARCHAR(255)     | YES      | NULL         | OLD: Single image (kept for compat)
id_proof_front           | VARCHAR(255)     | YES      | NULL         | NEW: Front side
id_proof_back            | VARCHAR(255)     | YES      | NULL         | NEW: Back side
police_verification      | VARCHAR(255)     | YES      | NULL         | NEW: Police doc
professional_slug        | VARCHAR(255)     | YES      | -            | Public profile slug
created_at               | TIMESTAMP        | NO       | CURRENT_TIME | Auto timestamp
updated_at               | TIMESTAMP        | NO       | CURRENT_TIME | Auto timestamp
updated_by               | INT              | YES      | NULL         | Foreign Key to users
```

---

## 💾 Complete Migration SQL Script

Run this entire script to add all columns at once:

```sql
-- Add all new columns to professionals table
-- Safe to run multiple times (columns won't be added if they exist)

ALTER TABLE professionals ADD COLUMN skills TEXT DEFAULT NULL AFTER hours;
ALTER TABLE professionals ADD COLUMN id_proof_front VARCHAR(255) DEFAULT NULL AFTER staff_image;
ALTER TABLE professionals ADD COLUMN id_proof_back VARCHAR(255) DEFAULT NULL AFTER id_proof_front;
ALTER TABLE professionals ADD COLUMN police_verification VARCHAR(255) DEFAULT NULL AFTER id_proof_back;

-- Verify the columns were added
SHOW COLUMNS FROM professionals;
```

---

## 🔍 Verification Queries

### Check if columns exist:

```sql
-- Show all columns in professionals table
SHOW COLUMNS FROM professionals;

-- Or check specific columns:
SHOW COLUMNS FROM professionals WHERE Field IN
('skills', 'id_proof_front', 'id_proof_back', 'police_verification');
```

### Count professionals by status:

```sql
SELECT status, COUNT(*) as count FROM professionals GROUP BY status;
```

### View professionals with skills:

```sql
SELECT id, name, skills FROM professionals WHERE skills IS NOT NULL;
```

### Filter by hours worked:

```sql
-- Professionals working 8+ hours
SELECT id, name, hours FROM professionals WHERE hours >= 8;

-- Professionals working between 4-8 hours
SELECT id, name, hours FROM professionals
WHERE hours >= 4 AND hours <= 8 ORDER BY hours;
```

### Check file storage:

```sql
-- Count documents uploaded
SELECT
    COUNT(CASE WHEN staff_image IS NOT NULL THEN 1 END) as staff_photos,
    COUNT(CASE WHEN id_proof_front IS NOT NULL THEN 1 END) as id_fronts,
    COUNT(CASE WHEN id_proof_back IS NOT NULL THEN 1 END) as id_backs,
    COUNT(CASE WHEN police_verification IS NOT NULL THEN 1 END) as police_docs
FROM professionals;
```

---

## 📈 Data Statistics

### Useful queries for reporting:

```sql
-- Average hours per service type
SELECT service, AVG(hours) as avg_hours, COUNT(*) as count
FROM professionals
GROUP BY service;

-- Professionals with complete documentation
SELECT COUNT(*) as fully_documented
FROM professionals
WHERE staff_image IS NOT NULL
  AND id_proof_front IS NOT NULL
  AND id_proof_back IS NOT NULL
  AND police_verification IS NOT NULL;

-- Top skills
SELECT skills, COUNT(*) as count
FROM professionals
WHERE skills IS NOT NULL
GROUP BY skills
ORDER BY count DESC;
```

---

## ⚠️ Important Notes

1. **Column Order Matters:** Columns are inserted in specific positions to maintain data organization
   - `skills` after `hours`
   - `id_proof_front` after `staff_image`
   - `id_proof_back` after `id_proof_front`
   - `police_verification` after `id_proof_back`

2. **NULL vs Empty:**
   - All new columns default to NULL
   - This means existing professionals won't be affected
   - Fields are optional for existing data

3. **Backward Compatibility:**
   - Old `id_proof_image` field is kept
   - New `id_proof_front` and `id_proof_back` are separate
   - Both can be used together

4. **File Paths:**
   - Stored as relative paths: `uploads/professionals/filename.ext`
   - Files physically stored in: `/uploads/professionals/` directory
   - Use full path for display: `BASE_URL . file_path`

---

## 🔄 Rollback Instructions (If Needed)

If you need to remove the columns (not recommended):

```sql
-- WARNING: Only run if you need to rollback
ALTER TABLE professionals DROP COLUMN skills;
ALTER TABLE professionals DROP COLUMN id_proof_front;
ALTER TABLE professionals DROP COLUMN id_proof_back;
ALTER TABLE professionals DROP COLUMN police_verification;
```

---

## 📋 Before & After Example

### BEFORE an update:

```
Professional: John Doe
- Phone: 9876543210
- Service: House Maid
- Status: Active
- Hours: 8
- ID Proof: idproof_old.jpg (single file)
```

### AFTER migration:

```
Professional: John Doe
- Phone: 9876543210
- Service: House Maid
- Status: Active (can change)
- Hours: 8
- Skills: "Cleaning, Laundry, Cooking" (can now add)
- ID Proof Front: idproof_front_123.jpg (separate)
- ID Proof Back: idproof_back_123.jpg (separate)
- Police Verification: police_verification_123.pdf (new)
- Old ID Proof: idproof_old.jpg (still exists for compat)
```

---

**Last Updated:** March 1, 2026
**Schema Version:** 2.0
