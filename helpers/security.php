<?php
/**
 * Security Helper Functions
 * 
 * Functions for CSRF protection, password hashing, input validation
 * 
 * @package ShivajiPool
 * @version 1.0
 */

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Get CSRF token input field HTML
 * 
 * @return string HTML input field
 */
function csrf_token_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
}

/**
 * Require CSRF token (die if invalid)
 */
function require_csrf_token() {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        die('CSRF token validation failed');
    }
}

/**
 * Hash password
 * 
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
}

/**
 * Verify password
 * 
 * @param string $password Plain text password
 * @param string $hash Hashed password
 * @return bool True if password matches, false otherwise
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Validate email
 * 
 * @param string $email Email address
 * @return bool True if valid, false otherwise
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Indian format)
 * 
 * @param string $phone Phone number
 * @return bool True if valid, false otherwise
 */
function is_valid_phone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if it's 10 digits
    return preg_match('/^[6-9][0-9]{9}$/', $phone);
}

/**
 * Sanitize phone number
 * 
 * @param string $phone Phone number
 * @return string Sanitized phone number
 */
function sanitize_phone($phone) {
    return preg_replace('/[^0-9]/', '', $phone);
}

/**
 * Validate date
 * 
 * @param string $date Date string
 * @param string $format Date format (default: Y-m-d)
 * @return bool True if valid, false otherwise
 */
function is_valid_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Check if string contains only alphanumeric characters
 * 
 * @param string $string String to check
 * @return bool True if alphanumeric, false otherwise
 */
function is_alphanumeric($string) {
    return ctype_alnum($string);
}

/**
 * Check if request is POST
 * 
 * @return bool True if POST, false otherwise
 */
function is_post_request() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 * 
 * @return bool True if GET, false otherwise
 */
function is_get_request() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Check if request is AJAX
 * 
 * @return bool True if AJAX, false otherwise
 */
function is_ajax_request() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Send JSON response
 * 
 * @param mixed $data Data to send
 * @param int $status_code HTTP status code
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Validate required fields
 * 
 * @param array $fields Fields to validate
 * @param array $data Data array
 * @return array Array of missing fields
 */
function validate_required_fields($fields, $data) {
    $missing = [];
    
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $missing[] = $field;
        }
    }
    
    return $missing;
}

/**
 * Rate limiting check
 * 
 * @param string $key Unique key for rate limiting (e.g., 'login_' . $username)
 * @param int $max_attempts Maximum attempts allowed
 * @param int $time_window Time window in seconds
 * @return bool True if allowed, false if rate limited
 */
function check_rate_limit($key, $max_attempts, $time_window) {
    $key = 'rate_limit_' . $key;
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'attempts' => 0,
            'first_attempt' => time()
        ];
    }
    
    $data = $_SESSION[$key];
    
    // Reset if time window has passed
    if (time() - $data['first_attempt'] > $time_window) {
        $_SESSION[$key] = [
            'attempts' => 0,
            'first_attempt' => time()
        ];
        return true;
    }
    
    // Check if rate limited
    if ($data['attempts'] >= $max_attempts) {
        return false;
    }
    
    return true;
}

/**
 * Increment rate limit counter
 * 
 * @param string $key Unique key for rate limiting
 */
function increment_rate_limit($key) {
    $key = 'rate_limit_' . $key;
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'attempts' => 0,
            'first_attempt' => time()
        ];
    }
    
    $_SESSION[$key]['attempts']++;
}

/**
 * Reset rate limit
 * 
 * @param string $key Unique key for rate limiting
 */
function reset_rate_limit($key) {
    $key = 'rate_limit_' . $key;
    unset($_SESSION[$key]);
}

/**
 * Get remaining lockout time
 * 
 * @param string $key Unique key for rate limiting
 * @param int $time_window Time window in seconds
 * @return int Remaining seconds
 */
function get_remaining_lockout_time($key, $time_window) {
    $key = 'rate_limit_' . $key;
    
    if (!isset($_SESSION[$key])) {
        return 0;
    }
    
    $elapsed = time() - $_SESSION[$key]['first_attempt'];
    $remaining = $time_window - $elapsed;
    
    return max(0, $remaining);
}

/**
 * Prevent directory traversal in file paths
 * 
 * @param string $path File path
 * @return string Sanitized path
 */
function sanitize_path($path) {
    // Remove any ../ or ..\\ sequences
    $path = str_replace(['../', '..\\'], '', $path);
    
    // Remove any leading slashes
    $path = ltrim($path, '/\\');
    
    return $path;
}

/**
 * Secure file download
 * 
 * @param string $filepath File path
 * @param string $filename Download filename
 */
function secure_download($filepath, $filename = null) {
    // Check if file exists
    if (!file_exists($filepath)) {
        http_response_code(404);
        die('File not found');
    }
    
    // Get file info
    $filename = $filename ?? basename($filepath);
    $file_size = filesize($filepath);
    $mime_type = mime_content_type($filepath);
    
    // Set headers
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $file_size);
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Output file
    readfile($filepath);
    exit();
}

/**
 * XSS clean (aggressive)
 * 
 * @param string $data Data to clean
 * @return string Cleaned data
 */
function xss_clean($data) {
    // Fix &entity\n;
    $data = str_replace(['&amp;', '&lt;', '&gt;'], ['&amp;amp;', '&amp;lt;', '&amp;gt;'], $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    } while ($old_data !== $data);

    return $data;
}
