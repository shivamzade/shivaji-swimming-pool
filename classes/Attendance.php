<?php
/**
 * Attendance Class
 * 
 * Handles all attendance-related operations
 * 
 * @package ShivajiPool
 * @version 1.0
 */

class Attendance {
    
    /**
     * Mark member entry
     * 
     * @param string $member_code Member code (from QR or manual input)
     * @return array ['success' => bool, 'message' => string]
     */
    public static function mark_entry($member_code) {
        // 1. Find member
        $member = Member::get_by_code($member_code);
        if (!$member) {
            return ['success' => false, 'message' => 'Member not found'];
        }
        
        $member_id = $member['member_id'];
        $today = date('Y-m-d');
        
        // 2. Check if member is active
        if ($member['status'] !== 'ACTIVE') {
            return ['success' => false, 'message' => 'Membership status: ' . $member['status']];
        }
        
        // 3. Check membership validity date
        if ($member['membership_end_date'] < $today) {
            // Auto-update status to EXPIRED
            db_query("UPDATE members SET status = 'EXPIRED' WHERE member_id = ?", 'i', [$member_id]);
            return ['success' => false, 'message' => 'Membership has expired on ' . $member['membership_end_date']];
        }
        
        // 4. Check if already inside (not exited yet)
        $inside_query = "SELECT attendance_id FROM attendance 
                         WHERE member_id = ? AND attendance_date = ? AND exit_time IS NULL";
        $is_inside = db_fetch_one($inside_query, 'is', [$member_id, $today]);
        if ($is_inside) {
            return ['success' => false, 'message' => 'Member is already inside'];
        }
        
        // 5. Check if already attended today (Restriction: 1 entry per day)
        // Note: For public pools, sometimes multiple entries are allowed. 
        // We'll enforce 1 entry per day unless configured otherwise.
        $attended_query = "SELECT attendance_id FROM attendance 
                           WHERE member_id = ? AND attendance_date = ?";
        $has_attended = db_fetch_one($attended_query, 'is', [$member_id, $today]);
        if ($has_attended) {
             return ['success' => false, 'message' => 'Member already entry for today'];
        }
        
        // 6. Mark entry
        $entry_time = date('H:i:s');
        $query = "INSERT INTO attendance (member_id, attendance_date, entry_time, status, created_by)
                  VALUES (?, ?, ?, 'PRESENT', ?)";
        
        $result = db_query($query, 'issi', [$member_id, $today, $entry_time, get_user_id()]);
        
        if ($result) {
            log_activity('ATTENDANCE_ENTRY', 'attendance', db_insert_id(), null, [
                'member_code' => $member_code,
                'name' => $member['first_name'] . ' ' . $member['last_name']
            ]);
            return ['success' => true, 'message' => 'Entry marked: ' . $member['first_name'] . ' @ ' . $entry_time];
        }
        
        return ['success' => false, 'message' => 'Database error marking entry'];
    }
    
    /**
     * Mark member exit
     * 
     * @param string $member_code Member code
     * @return array ['success' => bool, 'message' => string]
     */
    public static function mark_exit($member_code) {
        $member = Member::get_by_code($member_code);
        if (!$member) {
            return ['success' => false, 'message' => 'Member not found'];
        }
        
        $member_id = $member['member_id'];
        $today = date('Y-m-d');
        
        // Find the entry record for today that hasn't exited
        $query = "SELECT * FROM attendance 
                  WHERE member_id = ? AND attendance_date = ? AND exit_time IS NULL 
                  ORDER BY attendance_id DESC LIMIT 1";
        $attendance = db_fetch_one($query, 'is', [$member_id, $today]);
        
        if (!$attendance) {
            return ['success' => false, 'message' => 'No active entry found for this member today'];
        }
        
        $exit_time = date('H:i:s');
        
        // Calculate duration
        $start = strtotime($attendance['entry_time']);
        $end = strtotime($exit_time);
        $duration_minutes = round(($end - $start) / 60);
        
        // Update record
        $update_query = "UPDATE attendance SET exit_time = ?, duration_minutes = ? 
                         WHERE attendance_id = ?";
        $result = db_query($update_query, 'sii', [$exit_time, $duration_minutes, $attendance['attendance_id']]);
        
        if ($result) {
            log_activity('ATTENDANCE_EXIT', 'attendance', $attendance['attendance_id'], null, [
                'member_code' => $member_code,
                'duration' => $duration_minutes
            ]);
            return ['success' => true, 'message' => 'Exit marked: ' . $member['first_name'] . ' (Duration: ' . $duration_minutes . ' mins)'];
        }
        
        return ['success' => false, 'message' => 'Database error marking exit'];
    }
    
    /**
     * Get currently present members
     * 
     * @return array
     */
    public static function get_currently_inside() {
        $query = "SELECT a.*, m.member_code, m.first_name, m.last_name, m.phone
                  FROM attendance a
                  JOIN members m ON a.member_id = m.member_id
                  WHERE a.attendance_date = CURDATE() AND a.exit_time IS NULL
                  ORDER BY a.entry_time DESC";
        return db_fetch_all($query);
    }
    
    /**
     * Generate a new temporary attendance token
     * 
     * @return string The generated token
     */
    public static function generate_token() {
        $token = bin2hex(random_bytes(8)); // Random 16 char token
        $expiry = date('Y-m-d H:i:s', strtotime('+2 minutes')); // Valid for 2 mins
        
        // Save to settings (or a dedicated table if high volume)
        // We'll use the settings table for simplicity
        db_query("UPDATE settings SET setting_value = ? WHERE setting_key = 'attendance_token'", 's', [$token]);
        db_query("UPDATE settings SET setting_value = ? WHERE setting_key = 'attendance_token_expiry'", 's', [$expiry]);
        
        return $token;
    }

    /**
     * Get the Daily Attendance PIN
     * Automatically generates a new 4-digit PIN if the date has changed.
     * 
     * @return string 4-digit PIN
     */
    public static function get_daily_pin() {
        $today = date('Y-m-d');
        
        // Check if we have a pin for today
        $res = db_fetch_all("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('daily_pin', 'daily_pin_date')");
        $settings = [];
        foreach($res as $r) $settings[$r['setting_key']] = $r['setting_value'];
        
        if (($settings['daily_pin_date'] ?? '') === $today && !empty($settings['daily_pin'])) {
            return $settings['daily_pin'];
        }
        
        // New day or missing pin, generate a new one
        $new_pin = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        // Ensure settings exist (INSERT if missing, UPDATE if present)
        $keys = ['daily_pin', 'daily_pin_date'];
        foreach($keys as $key) {
            $check = db_fetch_one("SELECT * FROM settings WHERE setting_key = ?", 's', [$key]);
            if ($check) {
                $val = ($key === 'daily_pin') ? $new_pin : $today;
                db_query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", 'ss', [$val, $key]);
            } else {
                $val = ($key === 'daily_pin') ? $new_pin : $today;
                $desc = ($key === 'daily_pin') ? 'Daily rotating attendance PIN' : 'Date for the current daily PIN';
                db_query("INSERT INTO settings (setting_key, setting_value, setting_description) VALUES (?, ?, ?)", 'sss', [$key, $val, $desc]);
            }
        }
        
        return $new_pin;
    }

    /**
     * Verify the Daily Attendance PIN
     * 
     * @param string $pin PIN to verify
     * @return bool
     */
    public static function verify_daily_pin($pin) {
        return self::get_daily_pin() === $pin;
    }

    /**
     * Get attendance stats for today
     * 
     * @return array
     */
    public static function get_today_stats() {

        $stats = [
            'total_entries' => 0,
            'currently_inside' => 0,
            'exited' => 0
        ];
        
        $query = "SELECT 
                  COUNT(*) as total,
                  SUM(IF(exit_time IS NULL, 1, 0)) as inside,
                  SUM(IF(exit_time IS NOT NULL, 1, 0)) as exited
                  FROM attendance WHERE attendance_date = CURDATE()";
        
        $result = db_fetch_one($query);
        if ($result) {
            $stats['total_entries'] = (int)$result['total'];
            $stats['currently_inside'] = (int)$result['inside'];
            $stats['exited'] = (int)$result['exited'];
        }
        
        return $stats;
    }
}
