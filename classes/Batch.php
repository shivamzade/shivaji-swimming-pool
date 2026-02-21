<?php
/**
 * Batch Class
 * Handles batch management operations
 */

class Batch {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get all batches
     */
    public function getAllBatches() {
        $sql = "
            SELECT b.*, 
                   COUNT(mb.assignment_id) as assigned_members,
                   (b.max_capacity - COUNT(mb.assignment_id)) as available_slots,
                   ROUND((COUNT(mb.assignment_id) / b.max_capacity) * 100) as occupancy_percentage
            FROM batches b
            LEFT JOIN member_batches mb ON b.batch_id = mb.batch_id AND mb.status = 'ACTIVE'
            GROUP BY b.batch_id
            ORDER BY b.start_time
        ";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    /**
     * Get batch by ID
     */
    public function getBatchById($batch_id) {
        $sql = "
            SELECT b.*, 
                   COUNT(mb.assignment_id) as assigned_members,
                   (b.max_capacity - COUNT(mb.assignment_id)) as available_slots
            FROM batches b
            LEFT JOIN member_batches mb ON b.batch_id = mb.batch_id AND mb.status = 'ACTIVE'
            WHERE b.batch_id = ?
            GROUP BY b.batch_id
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $batch_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Create new batch
     */
    public function createBatch($data) {
        $sql = "
            INSERT INTO batches (batch_name, start_time, end_time, description, max_capacity)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssi', 
            $data['batch_name'],
            $data['start_time'],
            $data['end_time'],
            $data['description'],
            $data['max_capacity']
        );
        return $stmt->execute();
    }
    
    /**
     * Update batch
     */
    public function updateBatch($batch_id, $data) {
        $sql = "
            UPDATE batches 
            SET batch_name = ?, start_time = ?, end_time = ?, description = ?, max_capacity = ?
            WHERE batch_id = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssii', 
            $data['batch_name'],
            $data['start_time'],
            $data['end_time'],
            $data['description'],
            $data['max_capacity'],
            $batch_id
        );
        return $stmt->execute();
    }
    
    /**
     * Assign member to batch
     * @return true|string Returns true on success, or error string ('already_assigned', 'batch_full') on failure
     */
    public function assignMemberToBatch($member_id, $batch_id, $remarks = '') {
        // Check if already assigned
        $check_sql = "SELECT assignment_id FROM member_batches WHERE member_id = ? AND batch_id = ? AND status = 'ACTIVE'";
        $check_stmt = $this->conn->prepare($check_sql);
        $check_stmt->bind_param('ii', $member_id, $batch_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            return 'already_assigned';
        }
        
        // Check if batch has capacity
        $batch = $this->getBatchById($batch_id);
        if (!$batch || $batch['assigned_members'] >= $batch['max_capacity']) {
            return 'batch_full';
        }
        
        // Assign member
        $sql = "
            INSERT INTO member_batches (member_id, batch_id, assigned_date, remarks, created_by)
            VALUES (?, ?, CURDATE(), ?, ?)
        ";
        $stmt = $this->conn->prepare($sql);
        $created_by = $_SESSION['user_id'] ?? 0;
        $stmt->bind_param('iisi', $member_id, $batch_id, $remarks, $created_by);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Remove member from batch
     */
    public function removeMemberFromBatch($member_id, $batch_id) {
        $sql = "
            UPDATE member_batches 
            SET status = 'CANCELLED' 
            WHERE member_id = ? AND batch_id = ? AND status = 'ACTIVE'
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $member_id, $batch_id);
        return $stmt->execute();
    }
    
    /**
     * Get members in a batch
     */
    public function getBatchMembers($batch_id) {
        $sql = "
            SELECT m.*, mb.assigned_date, mb.remarks, mb.status
            FROM members m
            JOIN member_batches mb ON m.member_id = mb.member_id
            WHERE mb.batch_id = ? AND mb.status = 'ACTIVE'
            ORDER BY m.first_name, m.last_name
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $batch_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get member's batches
     */
    public function getMemberBatches($member_id) {
        $sql = "
            SELECT b.*, mb.assigned_date, mb.remarks
            FROM batches b
            JOIN member_batches mb ON b.batch_id = mb.batch_id
            WHERE mb.member_id = ? AND mb.status = 'ACTIVE'
            ORDER BY b.start_time
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $member_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get available batches for member assignment
     */
    public function getAvailableBatches() {
        $sql = "
            SELECT b.*, 
                   (b.max_capacity - COUNT(mb.assignment_id)) as available_slots
            FROM batches b
            LEFT JOIN member_batches mb ON b.batch_id = mb.batch_id AND mb.status = 'ACTIVE'
            WHERE b.is_active = 1
            GROUP BY b.batch_id
            HAVING available_slots > 0
            ORDER BY b.start_time
        ";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    /**
     * Toggle batch status
     */
    public function toggleBatchStatus($batch_id) {
        $sql = "UPDATE batches SET is_active = NOT is_active WHERE batch_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $batch_id);
        return $stmt->execute();
    }
    
    /**
     * Delete a batch (only if no active members assigned)
     * @return true|string Returns true on success, or error message string
     */
    public function deleteBatch($batch_id) {
        // Check for active member assignments
        $check_sql = "SELECT COUNT(*) as active_count FROM member_batches WHERE batch_id = ? AND status = 'ACTIVE'";
        $check_stmt = $this->conn->prepare($check_sql);
        $check_stmt->bind_param('i', $batch_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result()->fetch_assoc();
        
        if ($result['active_count'] > 0) {
            return 'has_members';
        }
        
        // Delete the batch (also clean up any cancelled/inactive assignments)
        $this->conn->begin_transaction();
        try {
            // Remove old non-active assignments
            $del_assignments = "DELETE FROM member_batches WHERE batch_id = ? AND status != 'ACTIVE'";
            $stmt1 = $this->conn->prepare($del_assignments);
            $stmt1->bind_param('i', $batch_id);
            $stmt1->execute();
            
            // Delete the batch
            $del_batch = "DELETE FROM batches WHERE batch_id = ?";
            $stmt2 = $this->conn->prepare($del_batch);
            $stmt2->bind_param('i', $batch_id);
            $stmt2->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return 'error';
        }
    }
}
?>
