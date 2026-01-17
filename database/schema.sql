-- =====================================================
-- SHIVAJI SWIMMING POOL MANAGEMENT SYSTEM
-- Database Schema - MySQL 8.x
-- Version: 1.0
-- Created: 2026-01-01
-- =====================================================

-- Drop existing database if exists (CAUTION: Use only in development)
DROP DATABASE IF EXISTS shivaji_pool;

-- Create fresh database
CREATE DATABASE shivaji_pool CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shivaji_pool;

-- =====================================================
-- 1. USERS TABLE (System Users: Admin, Staff)
-- =====================================================
CREATE TABLE users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL, -- bcrypt hash
    full_name VARCHAR(100) NOT NULL,
    role_id TINYINT UNSIGNED NOT NULL, -- 1=Super Admin, 2=Admin, 3=Staff
    phone VARCHAR(15),
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED NULL,
    
    INDEX idx_role (role_id),
    INDEX idx_status (is_active),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. ROLES TABLE
-- =====================================================
CREATE TABLE roles (
    role_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    role_description TEXT,
    permissions JSON, -- Store permissions as JSON
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. MEMBERSHIP PLANS TABLE
-- =====================================================
CREATE TABLE membership_plans (
    plan_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    plan_type ENUM('DAILY', 'WEEKLY', 'MONTHLY', 'QUARTERLY', 'HALF_YEARLY', 'YEARLY') NOT NULL,
    duration_days INT UNSIGNED NOT NULL, -- Number of days the plan is valid
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    features JSON, -- Store plan features as JSON
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED,
    
    INDEX idx_active (is_active),
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. MEMBERS TABLE (Swimming Pool Members)
-- =====================================================
CREATE TABLE members (
    member_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_code VARCHAR(20) UNIQUE NOT NULL, -- e.g., SPL2026-0001
    qr_code VARCHAR(255), -- Path to QR code image
    
    -- Personal Information
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15) NOT NULL,
    alternate_phone VARCHAR(15),
    date_of_birth DATE,
    gender ENUM('MALE', 'FEMALE', 'OTHER') NOT NULL,
    
    -- Address
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    
    -- Documents & Photo
    photo_path VARCHAR(255),
    id_proof_type ENUM('AADHAR', 'PAN', 'DRIVING_LICENSE', 'PASSPORT', 'OTHER'),
    id_proof_number VARCHAR(50),
    id_proof_document VARCHAR(255), -- Path to uploaded document
    
    -- Emergency Contact
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(15),
    emergency_contact_relation VARCHAR(50),
    
    -- Medical Information
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'),
    medical_conditions TEXT, -- Any medical conditions
    
    -- Status & Dates
    status ENUM('ACTIVE', 'EXPIRED', 'SUSPENDED', 'INACTIVE') DEFAULT 'ACTIVE',
    registration_date DATE NOT NULL,
    membership_start_date DATE,
    membership_end_date DATE,
    
    -- Meta
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED,
    
    INDEX idx_member_code (member_code),
    INDEX idx_status (status),
    INDEX idx_phone (phone),
    INDEX idx_email (email),
    INDEX idx_expiry (membership_end_date),
    INDEX idx_registration_date (registration_date),
    
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. MEMBER MEMBERSHIPS TABLE (Subscription History)
-- =====================================================
CREATE TABLE member_memberships (
    membership_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,
    plan_id INT UNSIGNED NOT NULL,
    
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_id INT UNSIGNED NULL, -- Link to payment
    
    status ENUM('ACTIVE', 'EXPIRED', 'CANCELLED') DEFAULT 'ACTIVE',
    remarks TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT UNSIGNED,
    
    INDEX idx_member (member_id),
    INDEX idx_plan (plan_id),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_status (status),
    
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES membership_plans(plan_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. ATTENDANCE TABLE (CRITICAL - High Performance)
-- =====================================================
CREATE TABLE attendance (
    attendance_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,
    attendance_date DATE NOT NULL,
    
    entry_time TIME NOT NULL,
    exit_time TIME NULL,
    duration_minutes INT UNSIGNED NULL, -- Auto-calculated
    
    marked_by INT UNSIGNED NOT NULL, -- Staff who marked entry
    exit_marked_by INT UNSIGNED NULL, -- Staff who marked exit
    
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- CRITICAL INDEXES FOR PERFORMANCE
    UNIQUE KEY unique_member_date (member_id, attendance_date), -- Prevent duplicate attendance
    INDEX idx_date (attendance_date),
    INDEX idx_member (member_id),
    INDEX idx_entry_time (entry_time),
    
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (exit_marked_by) REFERENCES users(user_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Partition by date for better performance (optional, for large-scale)
-- ALTER TABLE attendance PARTITION BY RANGE (TO_DAYS(attendance_date)) (
--     PARTITION p_2026 VALUES LESS THAN (TO_DAYS('2027-01-01')),
--     PARTITION p_future VALUES LESS THAN MAXVALUE
-- );

-- =====================================================
-- 7. PAYMENTS TABLE
-- =====================================================
CREATE TABLE payments (
    payment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,
    
    payment_type ENUM('REGISTRATION', 'RENEWAL', 'FINE', 'OTHER') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('CASH', 'UPI', 'CARD', 'NET_BANKING', 'CHEQUE') NOT NULL,
    
    receipt_number VARCHAR(50) UNIQUE NOT NULL,
    transaction_id VARCHAR(100), -- For digital payments
    
    payment_date DATE NOT NULL,
    payment_for_month VARCHAR(7), -- e.g., 2026-01 (for renewal)
    
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT UNSIGNED NOT NULL, -- Staff who received payment
    
    INDEX idx_member (member_id),
    INDEX idx_date (payment_date),
    INDEX idx_receipt (receipt_number),
    INDEX idx_type (payment_type),
    
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. STAFF TABLE
-- =====================================================
CREATE TABLE staff (
    staff_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED UNIQUE, -- Link to users table if staff has login
    
    staff_code VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(15) NOT NULL,
    
    designation ENUM('TRAINER', 'LIFEGUARD', 'FRONT_DESK', 'MAINTENANCE', 'OTHER') NOT NULL,
    date_of_joining DATE NOT NULL,
    
    address TEXT,
    photo_path VARCHAR(255),
    
    status ENUM('ACTIVE', 'INACTIVE', 'ON_LEAVE') DEFAULT 'ACTIVE',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED,
    
    INDEX idx_status (status),
    INDEX idx_designation (designation),
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. SHIFTS TABLE
-- =====================================================
CREATE TABLE shifts (
    shift_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shift_name VARCHAR(50) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. SHIFT ASSIGNMENTS TABLE
-- =====================================================
CREATE TABLE shift_assignments (
    assignment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    staff_id INT UNSIGNED NOT NULL,
    shift_id INT UNSIGNED NOT NULL,
    
    assignment_date DATE NOT NULL,
    status ENUM('SCHEDULED', 'COMPLETED', 'ABSENT', 'CANCELLED') DEFAULT 'SCHEDULED',
    
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT UNSIGNED,
    
    INDEX idx_staff (staff_id),
    INDEX idx_shift (shift_id),
    INDEX idx_date (assignment_date),
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES shifts(shift_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. NOTIFICATIONS TABLE
-- =====================================================
CREATE TABLE notifications (
    notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED,
    
    notification_type ENUM('EXPIRY_REMINDER', 'PAYMENT_DUE', 'PAYMENT_RECEIVED', 'GENERAL', 'CUSTOM') NOT NULL,
    channel ENUM('EMAIL', 'SMS', 'WHATSAPP', 'IN_APP') NOT NULL,
    
    subject VARCHAR(255),
    message TEXT NOT NULL,
    
    status ENUM('PENDING', 'SENT', 'FAILED') DEFAULT 'PENDING',
    sent_at DATETIME NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_member (member_id),
    INDEX idx_status (status),
    INDEX idx_type (notification_type),
    
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. AUDIT LOGS TABLE (Security & Tracking)
-- =====================================================
CREATE TABLE audit_logs (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED,
    
    action VARCHAR(100) NOT NULL, -- e.g., 'MEMBER_CREATED', 'ATTENDANCE_MARKED'
    table_name VARCHAR(50),
    record_id INT UNSIGNED,
    
    old_value JSON,
    new_value JSON,
    
    ip_address VARCHAR(45),
    user_agent TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_table (table_name),
    INDEX idx_date (created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. SETTINGS TABLE (System Configuration)
-- =====================================================
CREATE TABLE settings (
    setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('STRING', 'INTEGER', 'BOOLEAN', 'JSON') DEFAULT 'STRING',
    description TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Insert Roles
INSERT INTO roles (role_id, role_name, role_description, permissions) VALUES
(1, 'Super Admin', 'Full system access', '{"all": true}'),
(2, 'Admin', 'Pool manager with most permissions', '{"members": true, "attendance": true, "payments": true, "reports": true, "staff": false}'),
(3, 'Staff', 'Limited access for daily operations', '{"attendance": true, "members": "read", "payments": "create"}'),
(4, 'Member', 'Member portal access', '{"profile": "read", "attendance": "read"}');

-- Insert Default Super Admin User
-- Password: Admin@123 (hashed with bcrypt)
INSERT INTO users (user_id, username, email, password_hash, full_name, role_id, is_active) VALUES
(1, 'superadmin', 'admin@shivajipool.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', 1, 1);
-- NOTE: You MUST change this password after first login

-- Insert Default Membership Plans
INSERT INTO membership_plans (plan_name, plan_type, duration_days, price, description, is_active, created_by) VALUES
('Daily Pass', 'DAILY', 1, 50.00, 'Single day access to swimming pool', 1, 1),
('Weekly Pass', 'WEEKLY', 7, 300.00, 'One week unlimited access', 1, 1),
('Monthly Membership', 'MONTHLY', 30, 1000.00, 'One month unlimited access', 1, 1),
('Quarterly Membership', 'QUARTERLY', 90, 2700.00, 'Three months unlimited access (10% discount)', 1, 1),
('Half Yearly Membership', 'HALF_YEARLY', 180, 5100.00, '6 months unlimited access (15% discount)', 1, 1),
('Annual Membership', 'YEARLY', 365, 9600.00, 'One year unlimited access (20% discount)', 1, 1);

-- Insert Default Shifts
INSERT INTO shifts (shift_name, start_time, end_time, description) VALUES
('Morning Shift', '06:00:00', '14:00:00', 'Early morning operations'),
('Afternoon Shift', '14:00:00', '22:00:00', 'Evening operations'),
('Full Day', '06:00:00', '22:00:00', 'Full day duty');

-- Insert Default Settings
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('pool_name', 'Shivaji Swimming Pool', 'STRING', 'Swimming pool name'),
('pool_capacity', '200', 'INTEGER', 'Maximum pool capacity'),
('opening_time', '06:00', 'STRING', 'Pool opening time'),
('closing_time', '22:00', 'STRING', 'Pool closing time'),
('member_id_prefix', 'SPL', 'STRING', 'Prefix for member ID generation'),
('receipt_prefix', 'RCP', 'STRING', 'Prefix for receipt number'),
('expiry_reminder_days', '7', 'INTEGER', 'Days before expiry to send reminder'),
('timezone', 'Asia/Kolkata', 'STRING', 'System timezone'),
('currency', 'INR', 'STRING', 'Currency symbol');

-- =====================================================
-- CREATE VIEWS FOR COMMON QUERIES
-- =====================================================

-- Active Members with Current Plan
CREATE VIEW v_active_members AS
SELECT 
    m.*,
    mp.plan_name,
    mp.plan_type,
    DATEDIFF(m.membership_end_date, CURDATE()) as days_remaining,
    CASE 
        WHEN m.membership_end_date < CURDATE() THEN 'EXPIRED'
        WHEN DATEDIFF(m.membership_end_date, CURDATE()) <= 7 THEN 'EXPIRING_SOON'
        ELSE 'ACTIVE'
    END as membership_status
FROM members m
LEFT JOIN member_memberships mm ON m.member_id = mm.member_id AND mm.status = 'ACTIVE'
LEFT JOIN membership_plans mp ON mm.plan_id = mp.plan_id
WHERE m.status IN ('ACTIVE', 'EXPIRED');

-- Today's Attendance Summary
CREATE VIEW v_today_attendance AS
SELECT 
    COUNT(*) as total_attendance,
    COUNT(exit_time) as exited_count,
    COUNT(*) - COUNT(exit_time) as currently_inside,
    MIN(entry_time) as first_entry,
    MAX(entry_time) as last_entry
FROM attendance
WHERE attendance_date = CURDATE();

-- =====================================================
-- CREATE TRIGGERS
-- =====================================================

-- Auto-update member status based on expiry
DELIMITER $$
CREATE TRIGGER trg_update_member_status
BEFORE UPDATE ON members
FOR EACH ROW
BEGIN
    IF NEW.membership_end_date < CURDATE() AND NEW.status = 'ACTIVE' THEN
        SET NEW.status = 'EXPIRED';
    END IF;
END$$
DELIMITER ;

-- Auto-calculate attendance duration
DELIMITER $$
CREATE TRIGGER trg_calculate_attendance_duration
BEFORE UPDATE ON attendance
FOR EACH ROW
BEGIN
    IF NEW.exit_time IS NOT NULL AND NEW.entry_time IS NOT NULL THEN
        SET NEW.duration_minutes = TIMESTAMPDIFF(MINUTE, 
            CONCAT(NEW.attendance_date, ' ', NEW.entry_time), 
            CONCAT(NEW.attendance_date, ' ', NEW.exit_time)
        );
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

-- Generate Next Member Code
DELIMITER $$
CREATE PROCEDURE sp_generate_member_code(OUT new_code VARCHAR(20))
BEGIN
    DECLARE last_number INT;
    DECLARE prefix VARCHAR(10);
    
    SELECT setting_value INTO prefix FROM settings WHERE setting_key = 'member_id_prefix';
    
    SELECT COALESCE(MAX(CAST(SUBSTRING(member_code, LENGTH(prefix) + 6) AS UNSIGNED)), 0) + 1 
    INTO last_number 
    FROM members 
    WHERE member_code LIKE CONCAT(prefix, YEAR(CURDATE()), '-%');
    
    SET new_code = CONCAT(prefix, YEAR(CURDATE()), '-', LPAD(last_number, 4, '0'));
END$$
DELIMITER ;

-- Generate Next Receipt Number
DELIMITER $$
CREATE PROCEDURE sp_generate_receipt_number(OUT new_receipt VARCHAR(50))
BEGIN
    DECLARE last_number INT;
    DECLARE prefix VARCHAR(10);
    
    SELECT setting_value INTO prefix FROM settings WHERE setting_key = 'receipt_prefix';
    
    SELECT COALESCE(MAX(CAST(SUBSTRING(receipt_number, LENGTH(prefix) + 9) AS UNSIGNED)), 0) + 1 
    INTO last_number 
    FROM payments 
    WHERE receipt_number LIKE CONCAT(prefix, DATE_FORMAT(CURDATE(), '%Y%m%d'), '-%');
    
    SET new_receipt = CONCAT(prefix, DATE_FORMAT(CURDATE(), '%Y%m%d'), '-', LPAD(last_number, 4, '0'));
END$$
DELIMITER ;

-- =====================================================
-- PERFORMANCE OPTIMIZATION
-- =====================================================

-- Analyze tables for optimization
ANALYZE TABLE members, attendance, payments, member_memberships;

-- =====================================================
-- SECURITY NOTES
-- =====================================================
-- 1. Change default admin password immediately
-- 2. Use prepared statements in all queries
-- 3. Implement CSRF protection in forms
-- 4. Enable SSL for production
-- 5. Regular backups recommended
-- 6. Set proper file permissions for uploads directory

-- =====================================================
-- END OF SCHEMA
-- =====================================================

SELECT 'Database schema created successfully!' as status;
SELECT 'Default admin credentials - Username: superadmin, Password: Admin@123' as credentials;
SELECT 'IMPORTANT: Change default password immediately!' as warning;
