-- =====================================================
-- BATCHES TABLE FOR SHIVAJI SWIMMING POOL
-- Hourly batches from 6 AM to 9 PM
-- =====================================================

-- Create batches table
CREATE TABLE batches (
    batch_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_name VARCHAR(50) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    
    description TEXT,
    max_capacity INT DEFAULT 50, -- Maximum members per batch
    current_count INT DEFAULT 0, -- Current number of assigned members
    is_active TINYINT(1) DEFAULT 1,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED,
    
    UNIQUE KEY unique_time_slot (start_time, end_time),
    INDEX idx_active (is_active),
    INDEX idx_time (start_time),
    
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create member_batches table for assignments
CREATE TABLE member_batches (
    assignment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,
    batch_id INT UNSIGNED NOT NULL,
    
    assigned_date DATE NOT NULL,
    status ENUM('ACTIVE', 'INACTIVE', 'CANCELLED') DEFAULT 'ACTIVE',
    remarks TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT UNSIGNED,
    
    UNIQUE KEY unique_member_batch (member_id, batch_id, status),
    INDEX idx_member (member_id),
    INDEX idx_batch (batch_id),
    INDEX idx_status (status),
    
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (batch_id) REFERENCES batches(batch_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert hourly batches from 6 AM to 9 PM
INSERT INTO batches (batch_name, start_time, end_time, description, max_capacity, created_by) VALUES
('6 AM - 7 AM Batch', '06:00:00', '07:00:00', 'Early morning batch for fitness enthusiasts', 30, 1),
('7 AM - 8 AM Batch', '07:00:00', '08:00:00', 'Morning batch for working professionals', 40, 1),
('8 AM - 9 AM Batch', '08:00:00', '09:00:00', 'Late morning batch', 40, 1),
('9 AM - 10 AM Batch', '09:00:00', '10:00:00', 'Mid-morning batch', 35, 1),
('10 AM - 11 AM Batch', '10:00:00', '11:00:00', 'Late morning batch', 35, 1),
('11 AM - 12 PM Batch', '11:00:00', '12:00:00', 'Pre-noon batch', 30, 1),
('12 PM - 1 PM Batch', '12:00:00', '13:00:00', 'Noon batch', 25, 1),
('1 PM - 2 PM Batch', '13:00:00', '14:00:00', 'Afternoon batch', 25, 1),
('2 PM - 3 PM Batch', '14:00:00', '15:00:00', 'Late afternoon batch', 30, 1),
('3 PM - 4 PM Batch', '15:00:00', '16:00:00', 'Afternoon batch for students', 35, 1),
('4 PM - 5 PM Batch', '16:00:00', '17:00:00', 'Evening batch', 40, 1),
('5 PM - 6 PM Batch', '17:00:00', '18:00:00', 'Early evening batch', 45, 1),
('6 PM - 7 PM Batch', '18:00:00', '19:00:00', 'Prime time evening batch', 50, 1),
('7 PM - 8 PM Batch', '19:00:00', '20:00:00', 'Evening batch', 45, 1),
('8 PM - 9 PM Batch', '20:00:00', '21:00:00', 'Late evening batch', 35, 1);

-- Create view for batch statistics
CREATE VIEW v_batch_stats AS
SELECT 
    b.*,
    COUNT(mb.assignment_id) as assigned_members,
    (b.max_capacity - COUNT(mb.assignment_id)) as available_slots,
    ROUND((COUNT(mb.assignment_id) * 100.0 / b.max_capacity), 2) as occupancy_percentage
FROM batches b
LEFT JOIN member_batches mb ON b.batch_id = mb.batch_id AND mb.status = 'ACTIVE'
WHERE b.is_active = 1
GROUP BY b.batch_id
ORDER BY b.start_time;

-- Create trigger to update current_count when member assignment changes
DELIMITER $$
CREATE TRIGGER trg_update_batch_count_after_insert
AFTER INSERT ON member_batches
FOR EACH ROW
BEGIN
    IF NEW.status = 'ACTIVE' THEN
        UPDATE batches 
        SET current_count = (
            SELECT COUNT(*) 
            FROM member_batches 
            WHERE batch_id = NEW.batch_id AND status = 'ACTIVE'
        )
        WHERE batch_id = NEW.batch_id;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER trg_update_batch_count_after_update
AFTER UPDATE ON member_batches
FOR EACH ROW
BEGIN
    -- Update old batch if status changed from ACTIVE
    IF OLD.status = 'ACTIVE' AND NEW.status != 'ACTIVE' THEN
        UPDATE batches 
        SET current_count = (
            SELECT COUNT(*) 
            FROM member_batches 
            WHERE batch_id = OLD.batch_id AND status = 'ACTIVE'
        )
        WHERE batch_id = OLD.batch_id;
    END IF;
    
    -- Update new batch if status changed to ACTIVE
    IF NEW.status = 'ACTIVE' AND OLD.status != 'ACTIVE' THEN
        UPDATE batches 
        SET current_count = (
            SELECT COUNT(*) 
            FROM member_batches 
            WHERE batch_id = NEW.batch_id AND status = 'ACTIVE'
        )
        WHERE batch_id = NEW.batch_id;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER trg_update_batch_count_after_delete
AFTER DELETE ON member_batches
FOR EACH ROW
BEGIN
    IF OLD.status = 'ACTIVE' THEN
        UPDATE batches 
        SET current_count = (
            SELECT COUNT(*) 
            FROM member_batches 
            WHERE batch_id = OLD.batch_id AND status = 'ACTIVE'
        )
        WHERE batch_id = OLD.batch_id;
    END IF;
END$$
DELIMITER ;

SELECT 'Batches table created successfully with hourly slots from 6 AM to 9 PM!' as status;
