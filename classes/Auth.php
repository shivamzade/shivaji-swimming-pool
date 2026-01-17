<?php
/**
 * Authentication Class
 * 
 * Handles user authentication, login, logout, and session management
 * 
 * @package ShivajiPool
 * @version 1.0
 */

class Auth {
    
    /**
     * Attempt to login user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public static function login($username, $password) {
        // Rate limiting check
        $rate_limit_key = 'login_' . $username;
        
        if (!check_rate_limit($rate_limit_key, LOGIN_MAX_ATTEMPTS, LOGIN_LOCKOUT_TIME)) {
            $remaining_time = get_remaining_lockout_time($rate_limit_key, LOGIN_LOCKOUT_TIME);
            $minutes = ceil($remaining_time / 60);
            
            return [
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$minutes} minute(s).",
                'user' => null
            ];
        }
        
        // Find user by username or email
        $query = "SELECT u.*, r.role_name 
                  FROM users u 
                  LEFT JOIN roles r ON u.role_id = r.role_id 
                  WHERE (u.username = ? OR u.email = ?) 
                  AND u.is_active = 1 
                  LIMIT 1";
        
        $user = db_fetch_one($query, 'ss', [$username, $username]);
        
        if (!$user) {
            increment_rate_limit($rate_limit_key);
            
            return [
                'success' => false,
                'message' => 'Invalid username or password',
                'user' => null
            ];
        }
        
        // Verify password
        if (!verify_password($password, $user['password_hash'])) {
            increment_rate_limit($rate_limit_key);
            
            return [
                'success' => false,
                'message' => 'Invalid username or password',
                'user' => null
            ];
        }
        
        // Login successful - reset rate limit
        reset_rate_limit($rate_limit_key);
        
        // Create session
        self::create_session($user);
        
        // Update last login
        $update_query = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
        db_query($update_query, 'i', [$user['user_id']]);
        
        // Log activity
        log_activity('USER_LOGIN', 'users', $user['user_id']);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user
        ];
    }
    
    /**
     * Create user session
     * 
     * @param array $user User data
     */
    private static function create_session($user) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Store user data in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['logged_in_at'] = time();
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        $user_id = get_user_id();
        
        if ($user_id) {
            log_activity('USER_LOGOUT', 'users', $user_id);
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        // Start new session
        session_start();
        session_regenerate_id(true);
    }
    
    /**
     * Check if session is valid
     * 
     * @return bool True if valid, false otherwise
     */
    public static function is_valid_session() {
        if (!is_logged_in()) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity'])) {
            $inactive_time = time() - $_SESSION['last_activity'];
            
            if ($inactive_time > SESSION_LIFETIME) {
                self::logout();
                return false;
            }
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Get user by ID
     * 
     * @param int $user_id User ID
     * @return array|null User data or null
     */
    public static function get_user_by_id($user_id) {
        $query = "SELECT u.*, r.role_name 
                  FROM users u 
                  LEFT JOIN roles r ON u.role_id = r.role_id 
                  WHERE u.user_id = ? 
                  LIMIT 1";
        
        return db_fetch_one($query, 'i', [$user_id]);
    }
    
    /**
     * Get user by username
     * 
     * @param string $username Username
     * @return array|null User data or null
     */
    public static function get_user_by_username($username) {
        $query = "SELECT u.*, r.role_name 
                  FROM users u 
                  LEFT JOIN roles r ON u.role_id = r.role_id 
                  WHERE u.username = ? 
                  LIMIT 1";
        
        return db_fetch_one($query, 's', [$username]);
    }
    
    /**
     * Get user by email
     * 
     * @param string $email Email
     * @return array|null User data or null
     */
    public static function get_user_by_email($email) {
        $query = "SELECT u.*, r.role_name 
                  FROM users u 
                  LEFT JOIN roles r ON u.role_id = r.role_id 
                  WHERE u.email = ? 
                  LIMIT 1";
        
        return db_fetch_one($query, 's', [$email]);
    }
    
    /**
     * Create new user
     * 
     * @param array $data User data
     * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
     */
    public static function create_user($data) {
        // Validate required fields
        $required = ['username', 'email', 'password', 'full_name', 'role_id'];
        $missing = validate_required_fields($required, $data);
        
        if (!empty($missing)) {
            return [
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missing),
                'user_id' => null
            ];
        }
        
        // Validate email
        if (!is_valid_email($data['email'])) {
            return [
                'success' => false,
                'message' => 'Invalid email address',
                'user_id' => null
            ];
        }
        
        // Check if username exists
        if (self::get_user_by_username($data['username'])) {
            return [
                'success' => false,
                'message' => 'Username already exists',
                'user_id' => null
            ];
        }
        
        // Check if email exists
        if (self::get_user_by_email($data['email'])) {
            return [
                'success' => false,
                'message' => 'Email already exists',
                'user_id' => null
            ];
        }
        
        // Hash password
        $password_hash = hash_password($data['password']);
        
        // Insert user
        $query = "INSERT INTO users (username, email, password_hash, full_name, role_id, phone, is_active, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = db_query($query, 'ssssissi', [
            $data['username'],
            $data['email'],
            $password_hash,
            $data['full_name'],
            $data['role_id'],
            $data['phone'] ?? null,
            $data['is_active'] ?? 1,
            get_user_id()
        ]);
        
        if ($result) {
            $user_id = db_insert_id();
            
            log_activity('USER_CREATED', 'users', $user_id, null, $data);
            
            return [
                'success' => true,
                'message' => 'User created successfully',
                'user_id' => $user_id
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to create user',
            'user_id' => null
        ];
    }
    
    /**
     * Update user password
     * 
     * @param int $user_id User ID
     * @param string $new_password New password
     * @return bool True if successful, false otherwise
     */
    public static function update_password($user_id, $new_password) {
        $password_hash = hash_password($new_password);
        
        $query = "UPDATE users SET password_hash = ? WHERE user_id = ?";
        $result = db_query($query, 'si', [$password_hash, $user_id]);
        
        if ($result) {
            log_activity('PASSWORD_CHANGED', 'users', $user_id);
            return true;
        }
        
        return false;
    }
    
    /**
     * Change own password (requires old password verification)
     * 
     * @param int $user_id User ID
     * @param string $old_password Old password
     * @param string $new_password New password
     * @return array ['success' => bool, 'message' => string]
     */
    public static function change_password($user_id, $old_password, $new_password) {
        $user = self::get_user_by_id($user_id);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Verify old password
        if (!verify_password($old_password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Update password
        if (self::update_password($user_id, $new_password)) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to change password'];
    }
}
