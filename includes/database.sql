-- Servon Database Schema

CREATE DATABASE IF NOT EXISTS servon_db;
USE servon_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Sales', 'Allocation', 'Support') NOT NULL,
    enabled BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Leads Table
CREATE TABLE IF NOT EXISTS leads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    service ENUM('All Rounder', 'Baby Caretaker', 'Cooking Maid', 'House Maid', 'Elderly Care', 'Security Guard') NOT NULL,
    status ENUM('Fresh', 'In progress', 'Converted', 'Dropped') DEFAULT 'Fresh',
    responder_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responder_id) REFERENCES users(id),
    INDEX idx_phone (phone),
    INDEX idx_status (status),
    INDEX idx_service (service),
    INDEX idx_created_at (created_at)
);

-- Phone Calls Table
CREATE TABLE IF NOT EXISTS phone_calls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_number VARCHAR(20) NOT NULL,
    agent_id INT,
    direction ENUM('Inbound', 'Outbound') NOT NULL,
    duration INT DEFAULT 0,
    recording_url VARCHAR(500),
    tag VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id),
    INDEX idx_customer_number (customer_number),
    INDEX idx_agent_id (agent_id),
    INDEX idx_direction (direction),
    INDEX idx_created_at (created_at)
);

-- Follow Ups Table
CREATE TABLE IF NOT EXISTS follow_ups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    user_id INT NOT NULL,
    direction ENUM('Inbound', 'Outbound') NOT NULL,
    channel ENUM('Phone', 'Email', 'WhatsApp', 'SMS') NOT NULL,
    comments TEXT,
    reminder_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_lead_id (lead_id),
    INDEX idx_user_id (user_id),
    INDEX idx_reminder_at (reminder_at)
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255),
    customer_phone VARCHAR(20) NOT NULL,
    service ENUM('All Rounder', 'Baby Caretaker', 'Cooking Maid', 'House Maid', 'Elderly Care', 'Security Guard') NOT NULL,
    status ENUM('In progress', 'Shortlisted', 'Assigned', 'Deployed', 'Canceled', 'Unpaid', 'Hold') DEFAULT 'In progress',
    gender_preference VARCHAR(50),
    family_size INT,
    full_address TEXT,
    starts_at DATETIME,
    expires_at DATETIME,
    no_of_replacements INT DEFAULT 0,
    validity INT,
    job_hours INT,
    salary_bracket VARCHAR(50),
    shortlisted_from DATETIME,
    shortlisted_up_to DATETIME,
    deployed_at DATETIME,
    expiring_at DATETIME,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id),
    INDEX idx_customer_phone (customer_phone),
    INDEX idx_status (status),
    INDEX idx_service (service),
    INDEX idx_created_at (created_at)
);

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    service VARCHAR(100) NOT NULL,
    purpose VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50),
    razorpay_order_id VARCHAR(255),
    razorpay_payment_id VARCHAR(255),
    status ENUM('Pending', 'Completed', 'Failed', 'Refunded') DEFAULT 'Pending',
    received_at DATETIME,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    INDEX idx_razorpay_payment_id (razorpay_payment_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Professionals Table
CREATE TABLE IF NOT EXISTS professionals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    service ENUM('All Rounder', 'Baby Caretaker', 'Cooking Maid', 'House Maid', 'Elderly Care', 'Security Guard') NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    experience INT,
    rating DECIMAL(3, 2) DEFAULT 0,
    salary DECIMAL(10, 2),
    food_type VARCHAR(100),
    job_hours VARCHAR(50),
    language VARCHAR(100),
    location VARCHAR(255),
    radius INT,
    engaged BOOLEAN DEFAULT 0,
    status ENUM('Active', 'Inactive', 'On Leave') DEFAULT 'Active',
    verify_status ENUM('Verified', 'Pending', 'Rejected') DEFAULT 'Pending',
    aadhaar_number VARCHAR(20),
    aadhaar_document VARCHAR(500),
    police_verification_document VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone),
    INDEX idx_service (service),
    INDEX idx_status (status),
    INDEX idx_verify_status (verify_status),
    INDEX idx_created_at (created_at)
);

-- Service Requests Table
CREATE TABLE IF NOT EXISTS service_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    professional_id INT NOT NULL,
    status ENUM('Open', 'Closed') DEFAULT 'Open',
    remarks TEXT,
    created_by INT NOT NULL,
    deployed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (professional_id) REFERENCES professionals(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_booking_id (booking_id),
    INDEX idx_professional_id (professional_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Missed Calls Table
CREATE TABLE IF NOT EXISTS missed_calls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_number VARCHAR(20) NOT NULL,
    agent_id INT,
    missed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id),
    INDEX idx_customer_number (customer_number),
    INDEX idx_missed_at (missed_at)
);

-- Lead Comments Table
CREATE TABLE IF NOT EXISTS lead_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_lead_id (lead_id)
);

-- Create default admin user
INSERT INTO users (name, email, phone, password, role, enabled) 
VALUES ('Admin User', 'admin@servon.com', '9999999999', SHA2('admin123', 256), 'Admin', 1)
ON DUPLICATE KEY UPDATE id=id;
