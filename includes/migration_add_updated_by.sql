-- Migration: Add updated_by column to track who last edited records

ALTER TABLE bookings ADD COLUMN updated_by INT DEFAULT NULL;
ALTER TABLE bookings ADD FOREIGN KEY (updated_by) REFERENCES users(id);

ALTER TABLE payments ADD COLUMN updated_by INT DEFAULT NULL;
ALTER TABLE payments ADD FOREIGN KEY (updated_by) REFERENCES users(id);

ALTER TABLE professionals ADD COLUMN updated_by INT DEFAULT NULL;
ALTER TABLE professionals ADD FOREIGN KEY (updated_by) REFERENCES users(id);

ALTER TABLE users ADD COLUMN updated_by INT DEFAULT NULL;
ALTER TABLE users ADD FOREIGN KEY (updated_by) REFERENCES users(id);

ALTER TABLE leads ADD COLUMN updated_by INT DEFAULT NULL;
ALTER TABLE leads ADD FOREIGN KEY (updated_by) REFERENCES users(id);
