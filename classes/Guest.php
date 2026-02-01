<?php
/**
 * Guest Class
 * 
 * Handles all guest-related operations
 * 
 * @package ShivajiPool
 * @version 1.0
 */

class Guest {
    
    /**
     * Generate new guest code
     * 
     * @return string Generated guest code (e.g., GST-2026-0001)
     */
    public static function generate_guest_code() {
        global $conn;
        
        $prefix = get_setting('guest_id_prefix', 'GST');
        $year = date('Y');
        
        // Get the last guest code for this year
        $query = "SELECT guest_code FROM guests 
                  WHERE guest_code LIKE ? 
                  ORDER BY guest_id DESC LIMIT 1";
        
        $pattern = $prefix . '-' . $year . '-%';
        $result = db_fetch_one($query, 's', [$pattern]);
        
        if ($result) {
            // Extract number and increment
            $last_code = $result['guest_code'];
            $parts = explode('-', $last_code);
            $number = intval($parts[2] ?? 0) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . '-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Create new guest
     * 
     * @param array $data Guest data
     * @return array ['success' => bool, 'message' => string, 'guest_id' => int|null]
     */
    public static function create($data) {
        // Validate required fields
        $required = ['first_name', 'last_name', 'phone', 'gender', 'date_of_birth'];
        $missing = validate_required_fields($required, $data);
        
        if (!empty($missing)) {
            return [
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missing),
                'guest_id' => null
            ];
        }
        
        // Validate phone
        if (!is_valid_phone($data['phone'])) {
            return [
                'success' => false,
                'message' => 'Invalid phone number',
                'guest_id' => null
            ];
        }
        
        // Validate email if provided
        if (!empty($data['email']) && !is_valid_email($data['email'])) {
            return [
                'success' => false,
                'message' => 'Invalid email address',
                'guest_id' => null
            ];
        }
        
        // Check for duplicate phone (same day)
        $check_query = "SELECT guest_id FROM guests 
                       WHERE phone = ? AND DATE(created_at) = CURDATE()";
        $existing = db_fetch_one($check_query, 's', [$data['phone']]);
        
        if ($existing) {
            return [
                'success' => false,
                'message' => 'Phone number already registered today',
                'guest_id' => null
            ];
        }
        
        // Generate guest code
        $guest_code = self::generate_guest_code();
        
        // Insert guest
        $query = "INSERT INTO guests (guest_code, first_name, last_name, email, phone, alternate_phone, date_of_birth, gender, address_line1, address_line2, city, state, pincode, id_proof_type, id_proof_number, emergency_contact_name, emergency_contact_phone, emergency_contact_relation, blood_group, medical_conditions, guest_type, visit_date, check_in_time, check_out_time, status, created_at, updated_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = db_query($query, 'ssssssssssssssssssssssssssss', [
            $guest_code,
            $data['first_name'],
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
            $data['guest_type'] ?? 'DAILY',
            date('Y-m-d'),
            date('Y-m-d H:i:s'),
            null,
            'ACTIVE',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
            get_user_id()
        ]);
        
        if ($result) {
            $guest_id = db_insert_id();
            
            log_activity('GUEST_CREATED', 'guests', $guest_id, null, [
                'guest_code' => $guest_code,
                'name' => $data['first_name'] . ' ' . $data['last_name']
            ]);
            
            return [
                'success' => true,
                'message' => 'Guest registered successfully',
                'guest_id' => $guest_id,
                'guest_code' => $guest_code
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to register guest',
            'guest_id' => null
        ];
    }
    
    /**
     * Get guest by ID
     * 
     * @param int $guest_id Guest ID
     * @return array|null Guest data or null
     */
    public static function get_by_id($guest_id) {
        $query = "SELECT g.*,
                  TIMESTAMPDIFF(HOUR, g.check_in_time, IFNULL(g.check_out_time, NOW())) as duration_hours
                  FROM guests g
                  WHERE g.guest_id = ?";
        
        return db_fetch_one($query, 'i', [$guest_id]);
    }
    
    /**
     * Get guest by code
     * 
     * @param string $guest_code Guest code
     * @return array|null Guest data or null
     */
    public static function get_by_code($guest_code) {
        $query = "SELECT * FROM guests WHERE guest_code = ?";
        return db_fetch_one($query, 's', [$guest_code]);
    }
    
    /**
     * Get all guests with pagination
     * 
     * @param int $page Current page
     * @param int $per_page Records per page
     * @param string $search Search term
     * @param string $status_filter Status filter
     * @param string $date_filter Date filter
     * @return array Paginated guest data
     */
    public static function get_all($page = 1, $per_page = 25, $search = '', $status_filter = '', $date_filter = '') {
        $offset = ($page - 1) * $per_page;
        
        // Build query
        $where_conditions = [];
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $where_conditions[] = "(g.first_name LIKE ? OR g.last_name LIKE ? OR g.phone LIKE ? OR g.guest_code LIKE ?)";
            $search_term = '%' . $search . '%';
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $types .= 'ssss';
        }
        
        if (!empty($status_filter)) {
            $where_conditions[] = "g.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        if (!empty($date_filter)) {
            $where_conditions[] = "DATE(g.visit_date) = ?";
            $params[] = $date_filter;
            $types .= 's';
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        // Count query
        $count_query = "SELECT COUNT(*) as total FROM guests g $where_clause";
        $count_result = db_fetch_one($count_query, $types, $params);
        $total = $count_result['total'] ?? 0;
        
        // Data query
        $query = "SELECT g.*, 
                  TIMESTAMPDIFF(HOUR, g.check_in_time, IFNULL(g.check_out_time, NOW())) as duration_hours
                  FROM guests g 
                  $where_clause
                  ORDER BY g.created_at DESC 
                  LIMIT ? OFFSET ?";
        
        $params[] = $per_page;
        $params[] = $offset;
        $types .= 'ii';
        
        $guests = db_fetch_all($query, $types, $params);
        
        return [
            'guests' => $guests,
            'total' => $total,
            'per_page' => $per_page,
            'current_page' => $page,
            'last_page' => ceil($total / $per_page)
        ];
    }
    
    /**
     * Update guest
     * 
     * @param int $guest_id Guest ID
     * @param array $data Guest data
     * @return array Update result
     */
    public static function update($guest_id, $data) {
        // Validate required fields
        $required = ['first_name', 'last_name', 'phone', 'gender', 'date_of_birth'];
        $missing = validate_required_fields($required, $data);
        
        if (!empty($missing)) {
            return [
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missing)
            ];
        }
        
        // Validate phone
        if (!is_valid_phone($data['phone'])) {
            return [
                'success' => false,
                'message' => 'Invalid phone number'
            ];
        }
        
        // Validate email if provided
        if (!empty($data['email']) && !is_valid_email($data['email'])) {
            return [
                'success' => false,
                'message' => 'Invalid email address'
            ];
        }
        
        // Check for duplicate phone (excluding current guest)
        $check_query = "SELECT guest_id FROM guests 
                       WHERE phone = ? AND guest_id != ? AND DATE(created_at) = CURDATE()";
        $existing = db_fetch_one($check_query, 'si', [$data['phone'], $guest_id]);
        
        if ($existing) {
            return [
                'success' => false,
                'message' => 'Phone number already registered today'
            ];
        }
        
        // Update guest
        $query = "UPDATE guests SET 
                    first_name = ?, last_name = ?, email = ?, phone = ?, alternate_phone = ?,
                    date_of_birth = ?, gender = ?, address_line1 = ?, address_line2 = ?, 
                    city = ?, state = ?, pincode = ?, id_proof_type = ?, id_proof_number = ?,
                    emergency_contact_name = ?, emergency_contact_phone = ?, 
                    emergency_contact_relation = ?, blood_group = ?, medical_conditions = ?,
                    guest_type = ?, visit_date = ?, updated_at = CURRENT_TIMESTAMP
                  WHERE guest_id = ?";
        
        $result = db_query($query, 'sssssssssssssssssssssi', [
            $data['first_name'],
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
            $data['guest_type'] ?? 'DAILY',
            $data['visit_date'] ?? date('Y-m-d'),
            $guest_id
        ]);
        
        if ($result) {
            log_activity('GUEST_UPDATED', 'guests', $guest_id, null, [
                'name' => $data['first_name'] . ' ' . $data['last_name']
            ]);
            
            return [
                'success' => true,
                'message' => 'Guest updated successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to update guest'
        ];
    }
    
    /**
     * Check out guest
     * 
     * @param int $guest_id Guest ID
     * @return array Check-out result
     */
    public static function check_out($guest_id) {
        $guest = self::get_by_id($guest_id);
        
        if (!$guest) {
            return [
                'success' => false,
                'message' => 'Guest not found'
            ];
        }
        
        if ($guest['check_out_time']) {
            return [
                'success' => false,
                'message' => 'Guest already checked out'
            ];
        }
        
        $query = "UPDATE guests SET 
                    check_out_time = NOW(), 
                    status = 'CHECKED_OUT',
                    updated_at = CURRENT_TIMESTAMP
                  WHERE guest_id = ?";
        
        $result = db_query($query, 'i', [$guest_id]);
        
        if ($result) {
            log_activity('GUEST_CHECKED_OUT', 'guests', $guest_id, null, [
                'guest_code' => $guest['guest_code'],
                'name' => $guest['first_name'] . ' ' . $guest['last_name']
            ]);
            
            return [
                'success' => true,
                'message' => 'Guest checked out successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to check out guest'
        ];
    }
    
    /**
     * Delete guest
     * 
     * @param int $guest_id Guest ID
     * @return array Delete result
     */
    public static function delete($guest_id) {
        $guest = self::get_by_id($guest_id);
        
        if (!$guest) {
            return [
                'success' => false,
                'message' => 'Guest not found'
            ];
        }
        
        $query = "DELETE FROM guests WHERE guest_id = ?";
        $result = db_query($query, 'i', [$guest_id]);
        
        if ($result) {
            log_activity('GUEST_DELETED', 'guests', $guest_id, null, [
                'guest_code' => $guest['guest_code'],
                'name' => $guest['first_name'] . ' ' . $guest['last_name']
            ]);
            
            return [
                'success' => true,
                'message' => 'Guest deleted successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to delete guest'
        ];
    }
    
    /**
     * Get guest statistics
     * 
     * @param string $date_from Start date
     * @param string $date_to End date
     * @return array Statistics
     */
    public static function get_statistics($date_from = null, $date_to = null) {
        $where_conditions = [];
        $params = [];
        $types = '';
        
        if ($date_from) {
            $where_conditions[] = "DATE(visit_date) >= ?";
            $params[] = $date_from;
            $types .= 's';
        }
        
        if ($date_to) {
            $where_conditions[] = "DATE(visit_date) <= ?";
            $params[] = $date_to;
            $types .= 's';
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $stats = [];
        
        // Total guests
        $query = "SELECT COUNT(*) as total FROM guests $where_clause";
        $result = db_fetch_one($query, $types, $params);
        $stats['total_guests'] = $result['total'] ?? 0;
        
        // Active guests (checked in)
        $active_where = $where_clause ? $where_clause . ' AND status = ?' : 'WHERE status = ?';
        $active_params = $params;
        $active_params[] = 'ACTIVE';
        $active_types = $types . 's';
        
        $query = "SELECT COUNT(*) as active FROM guests $active_where";
        $result = db_fetch_one($query, $active_types, $active_params);
        $stats['active_guests'] = $result['active'] ?? 0;
        
        // Today's guests
        $query = "SELECT COUNT(*) as today FROM guests WHERE DATE(visit_date) = CURDATE()";
        $result = db_fetch_one($query);
        $stats['today_guests'] = $result['today'] ?? 0;
        
        // Average duration
        $avg_where = $where_clause ? $where_clause . ' AND check_out_time IS NOT NULL' : 'WHERE check_out_time IS NOT NULL';
        $avg_params = $params;
        $avg_types = $types;
        
        $query = "SELECT AVG(TIMESTAMPDIFF(HOUR, check_in_time, check_out_time)) as avg_duration 
                  FROM guests $avg_where";
        $result = db_fetch_one($query, $avg_types, $avg_params);
        $stats['avg_duration_hours'] = round($result['avg_duration'] ?? 0, 2);
        
        return $stats;
    }
}
