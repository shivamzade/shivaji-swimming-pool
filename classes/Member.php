<?php
/**
 * Member Class
 * 
 * Handles all member-related operations
 * 
 * @package ShivajiPool
 * @version 1.0
 */

class Member {
    
    /**
     * Generate new member code
     * 
     * @return string Generated member code (e.g., SPL-2026-0001)
     */
    public static function generate_member_code() {
        global $conn;
        
        $prefix = get_setting('member_id_prefix', 'SPL');
        $year = date('Y');
        
        // Get the last member code for this year
        $query = "SELECT member_code FROM members 
                  WHERE member_code LIKE ? 
                  ORDER BY member_id DESC LIMIT 1";
        
        $pattern = $prefix . '-' . $year . '-%';
        $result = db_fetch_one($query, 's', [$pattern]);
        
        if ($result) {
            // Extract number and increment
            $last_code = $result['member_code'];
            $parts = explode('-', $last_code);
            $number = intval($parts[2] ?? 0) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . '-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Create new member
     * 
     * @param array $data Member data
     * @return array ['success' => bool, 'message' => string, 'member_id' => int|null]
     */
    public static function create($data) {
        // Validate required fields
        $required = ['first_name', 'last_name', 'phone', 'gender', 'date_of_birth'];
        $missing = validate_required_fields($required, $data);
        
        if (!empty($missing)) {
            return [
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missing),
                'member_id' => null
            ];
        }
        
        // Validate phone
        if (!is_valid_phone($data['phone'])) {
            return [
                'success' => false,
                'message' => 'Invalid phone number',
                'member_id' => null
            ];
        }
        
        // Validate email if provided
        if (!empty($data['email']) && !is_valid_email($data['email'])) {
            return [
                'success' => false,
                'message' => 'Invalid email address',
                'member_id' => null
            ];
        }
        
        // Check for duplicate phone
        $check_query = "SELECT member_id FROM members WHERE phone = ?";
        $existing = db_fetch_one($check_query, 's', [$data['phone']]);
        
        if ($existing) {
            return [
                'success' => false,
                'message' => 'Phone number already registered',
                'member_id' => null
            ];
        }
        
        // Generate member code
        $member_code = self::generate_member_code();
        
        // Insert member
        $query = "INSERT INTO members (
                    member_code, first_name, middle_name, last_name, email, phone, alternate_phone,
                    date_of_birth, gender, address_line1, address_line2, city, state, pincode,
                    id_proof_type, id_proof_number, emergency_contact_name, 
                    emergency_contact_phone, emergency_contact_relation, blood_group,
                    medical_conditions, status, registration_date, remarks, created_by
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = db_query($query, 'ssssssssssssssssssssssssi', [
            $member_code,
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['last_name'],
            $data['email'] ?? null,
            sanitize_phone($data['phone']),
            $data['alternate_phone'] ?? null,
            $data['date_of_birth'],
            $data['gender'],
            $data['address_line1'] ?? null,
            $data['address_line2'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['pincode'] ?? null,
            $data['id_proof_type'] ?? null,
            $data['id_proof_number'] ?? null,
            $data['emergency_contact_name'] ?? null,
            $data['emergency_contact_phone'] ?? null,
            $data['emergency_contact_relation'] ?? null,
            $data['blood_group'] ?? null,
            $data['medical_conditions'] ?? null,
            'ACTIVE',
            date('Y-m-d'),
            $data['remarks'] ?? null,
            get_user_id()
        ]);
        
        if ($result) {
            $member_id = db_insert_id();
            
            // Handle batch assignment if provided
            if (!empty($data['batch_id'])) {
                $batch_query = "INSERT INTO member_batches (member_id, batch_id, assigned_date, remarks, created_by) 
                                VALUES (?, ?, CURDATE(), ?, ?)";
                db_query($batch_query, 'iisi', [
                    $member_id,
                    $data['batch_id'],
                    $data['batch_remarks'] ?? null,
                    get_user_id()
                ]);
            }
            
            log_activity('MEMBER_CREATED', 'members', $member_id, null, [
                'member_code' => $member_code,
                'name' => $data['first_name'] . ' ' . ($data['middle_name'] ?? '') . ' ' . $data['last_name']
            ]);
            
            return [
                'success' => true,
                'message' => 'Member registered successfully',
                'member_id' => $member_id,
                'member_code' => $member_code
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to register member',
            'member_id' => null
        ];
    }
    
    /**
     * Get member by ID
     * 
     * @param int $member_id Member ID
     * @return array|null Member data or null
     */
    public static function get_by_id($member_id) {
        $query = "SELECT m.*, 
                  mm.plan_id, mm.start_date, mm.end_date, mm.amount_paid,
                  mp.plan_name, mp.plan_type,
                  DATEDIFF(m.membership_end_date, CURDATE()) as days_remaining
                  FROM members m
                  LEFT JOIN member_memberships mm ON m.member_id = mm.member_id AND mm.status = 'ACTIVE'
                  LEFT JOIN membership_plans mp ON mm.plan_id = mp.plan_id
                  WHERE m.member_id = ?";
        
        return db_fetch_one($query, 'i', [$member_id]);
    }
    
    /**
     * Get member by code
     * 
     * @param string $member_code Member code
     * @return array|null Member data or null
     */
    public static function get_by_code($member_code) {
        $query = "SELECT * FROM members WHERE member_code = ?";
        return db_fetch_one($query, 's', [$member_code]);
    }
    
    /**
     * Get all members with pagination
     * 
     * @param int $page Current page
     * @param int $per_page Records per page
     * @param string $search Search term
     * @param string $status_filter Status filter
     * @return array Paginated member data
     */
    public static function get_all($page = 1, $per_page = 25, $search = '', $status_filter = '') {
        $offset = ($page - 1) * $per_page;
        
        // Build query
        $where_conditions = [];
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $where_conditions[] = "(member_code LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?)";
            $search_term = '%' . $search . '%';
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $types .= 'ssss';
        }
        
        if (!empty($status_filter)) {
            $where_conditions[] = "status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        // Get total count
        $count_query = "SELECT COUNT(*) as total FROM members $where_clause";
        $count_result = db_fetch_one($count_query, $types, $params);
        $total = $count_result['total'] ?? 0;
        
        // Get paginated data
        $data_query = "SELECT m.*, 
                       DATEDIFF(m.membership_end_date, CURDATE()) as days_remaining
                       FROM members m
                       $where_clause
                       ORDER BY m.member_id DESC
                       LIMIT ? OFFSET ?";
        
        $params[] = $per_page;
        $params[] = $offset;
        $types .= 'ii';
        
        $members = db_fetch_all($data_query, $types, $params);
        
        return [
            'data' => $members,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ];
    }
    
    /**
     * Update member
     * 
     * @param int $member_id Member ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public static function update($member_id, $data) {
        $old_data = self::get_by_id($member_id);
        
        if (!$old_data) {
            return false;
        }
        
        $query = "UPDATE members SET
                  first_name = ?, middle_name = ?, last_name = ?, email = ?, phone = ?, alternate_phone = ?,
                  date_of_birth = ?, gender = ?, address_line1 = ?, address_line2 = ?,
                  city = ?, state = ?, pincode = ?, id_proof_type = ?, id_proof_number = ?,
                  emergency_contact_name = ?, emergency_contact_phone = ?, 
                  emergency_contact_relation = ?, blood_group = ?, medical_conditions = ?,
                  remarks = ?
                  WHERE member_id = ?";
        
        $result = db_query($query, 'sssssssssssssssssssssi', [
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['last_name'],
            $data['email'] ?? null,
            sanitize_phone($data['phone']),
            $data['alternate_phone'] ?? null,
            $data['date_of_birth'],
            $data['gender'],
            $data['address_line1'] ?? null,
            $data['address_line2'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['pincode'] ?? null,
            $data['id_proof_type'] ?? null,
            $data['id_proof_number'] ?? null,
            $data['emergency_contact_name'] ?? null,
            $data['emergency_contact_phone'] ?? null,
            $data['emergency_contact_relation'] ?? null,
            $data['blood_group'] ?? null,
            $data['medical_conditions'] ?? null,
            $data['remarks'] ?? null,
            $member_id
        ]);
        
        if ($result) {
            log_activity('MEMBER_UPDATED', 'members', $member_id, $old_data, $data);
            return true;
        }
        
        return false;
    }
    
    /**
     * Assign membership plan to member
     * 
     * @param int $member_id Member ID
     * @param int $plan_id Plan ID
     * @param string $start_date Start date
     * @param float $amount_paid Amount paid
     * @param int $payment_id Payment ID (optional)
     * @return bool Success status
     */
    public static function assign_membership($member_id, $plan_id, $start_date, $amount_paid, $payment_id = null) {
        // Get plan details
        $plan_query = "SELECT * FROM membership_plans WHERE plan_id = ?";
        $plan = db_fetch_one($plan_query, 'i', [$plan_id]);
        
        if (!$plan) {
            return false;
        }
        
        // Calculate end date
        $end_date = date('Y-m-d', strtotime($start_date . ' + ' . $plan['duration_days'] . ' days'));
        
        // Deactivate any existing active membership
        $deactivate_query = "UPDATE member_memberships SET status = 'EXPIRED' 
                             WHERE member_id = ? AND status = 'ACTIVE'";
        db_query($deactivate_query, 'i', [$member_id]);
        
        // Insert new membership
        $insert_query = "INSERT INTO member_memberships 
                         (member_id, plan_id, start_date, end_date, amount_paid, payment_id, status, created_by)
                         VALUES (?, ?, ?, ?, ?, ?, 'ACTIVE', ?)";
        
        $result = db_query($insert_query, 'iissdii', [
            $member_id,
            $plan_id,
            $start_date,
            $end_date,
            $amount_paid,
            $payment_id,
            get_user_id()
        ]);
        
        if ($result) {
            // Update member's membership dates
            $update_member = "UPDATE members SET 
                              membership_start_date = ?,
                              membership_end_date = ?,
                              status = 'ACTIVE'
                              WHERE member_id = ?";
            
            db_query($update_member, 'ssi', [$start_date, $end_date, $member_id]);
            
            log_activity('MEMBERSHIP_ASSIGNED', 'member_memberships', db_insert_id(), null, [
                'member_id' => $member_id,
                'plan_id' => $plan_id,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            
            return true;
        }
        
        return false;
    }
}
