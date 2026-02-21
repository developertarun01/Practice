-- Migration: Add image fields and hours to professionals table
-- Adds features for staff photo, ID proof, and hours management

ALTER TABLE professionals ADD COLUMN IF NOT EXISTS staff_image VARCHAR(500) AFTER police_verification_document;
ALTER TABLE professionals ADD COLUMN IF NOT EXISTS id_proof_image VARCHAR(500) AFTER staff_image;
ALTER TABLE professionals ADD COLUMN IF NOT EXISTS professional_slug VARCHAR(255) UNIQUE AFTER id_proof_image;
ALTER TABLE professionals ADD COLUMN IF NOT EXISTS hours INT DEFAULT 8 AFTER job_hours;
ALTER TABLE professionals ADD COLUMN IF NOT EXISTS updated_by INT AFTER verify_status;

-- Create index for slug (for public profile sharing)
CREATE INDEX IF NOT EXISTS idx_professional_slug ON professionals(professional_slug);

-- Create index for updated_by
ALTER TABLE professionals ADD CONSTRAINT fk_professionals_updated_by 
FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL;
