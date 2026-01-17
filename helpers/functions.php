<?php
/**
 * Helper Functions
 * 
 * Common utility functions used throughout the application
 * 
 * @package ShivajiPool
 * @version 1.0
 */

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 * @param int $status_code HTTP status code (default: 302)
 */
function redirect($url, $status_code = 302) {
    header("Location: $url", true, $status_code);
    exit();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

/**
 * Get current user ID
 * 
 * @return int|null User ID or null if not logged in
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role ID
 * 
 * @return int|null Role ID or null if not logged in
 */
function get_user_role() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Get current user data
 * 
 * @return array|null User data or null if not logged in
 */
function get_user_data() {
    if (!is_logged_in()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'full_name' => $_SESSION['full_name'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'role_id' => $_SESSION['user_role'] ?? 0,
        'role_name' => $_SESSION['role_name'] ?? ''
    ];
}

/**
 * Check if user has specific role
 * 
 * @param int|array $role_id Single role ID or array of role IDs
 * @return bool True if user has role, false otherwise
 */
function has_role($role_id) {
    if (!is_logged_in()) {
        return false;
    }
    
    $user_role = get_user_role();
    
    if (is_array($role_id)) {
        return in_array($user_role, $role_id);
    }
    
    return $user_role == $role_id;
}

/**
 * Require login (redirect to login if not logged in)
 * 
 * @param string $redirect_url URL to redirect after login
 */
function require_login($redirect_url = '') {
    if (!is_logged_in()) {
        if ($redirect_url) {
            $_SESSION['redirect_after_login'] = $redirect_url;
        }
        redirect(BASE_URL . '/admin/index.php');
    }
}

/**
 * Require specific role (redirect if user doesn't have role)
 * 
 * @param int|array $role_id Required role ID(s)
 * @param string $redirect_url URL to redirect if unauthorized
 */
function require_role($role_id, $redirect_url = '') {
    require_login();
    
    if (!has_role($role_id)) {
        if ($redirect_url) {
            redirect($redirect_url);
        } else {
            redirect(BASE_URL . '/admin/index.php');
        }
    }
}

/**
 * Set flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message text
 */
function set_flash($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null Flash message array or null
 */
function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message HTML
 * 
 * @return string HTML for flash message
 */
function display_flash() {
    $flash = get_flash();
    if (!$flash) {
        return '';
    }
    
    $type_classes = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $class = $type_classes[$flash['type']] ?? 'alert-info';
    
    return sprintf(
        '<div class="alert %s alert-dismissible fade show" role="alert">
            %s
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>',
        $class,
        htmlspecialchars($flash['message'])
    );
}

/**
 * Format date
 * 
 * @param string $date Date string
 * @param string $format Date format (default from config)
 * @return string Formatted date
 */
function format_date($date, $format = null) {
    if (!$date || $date == '0000-00-00') {
        return '-';
    }
    
    $format = $format ?? DATE_FORMAT;
    return date($format, strtotime($date));
}

/**
 * Format time
 * 
 * @param string $time Time string
 * @param string $format Time format (default from config)
 * @return string Formatted time
 */
function format_time($time, $format = null) {
    if (!$time) {
        return '-';
    }
    
    $format = $format ?? TIME_FORMAT;
    return date($format, strtotime($time));
}

/**
 * Format datetime
 * 
 * @param string $datetime Datetime string
 * @param string $format Datetime format (default from config)
 * @return string Formatted datetime
 */
function format_datetime($datetime, $format = null) {
    if (!$datetime || $datetime == '0000-00-00 00:00:00') {
        return '-';
    }
    
    $format = $format ?? DATETIME_FORMAT;
    return date($format, strtotime($datetime));
}

/**
 * Format currency
 * 
 * @param float $amount Amount
 * @param string $currency Currency symbol
 * @return string Formatted currency
 */
function format_currency($amount, $currency = '₹') {
    return $currency . number_format($amount, 2);
}

/**
 * Sanitize output (prevent XSS)
 * 
 * @param string $string String to sanitize
 * @return string Sanitized string
 */
function clean($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input
 * 
 * @param string $string String to sanitize
 * @return string Sanitized string
 */
function sanitize_input($string) {
    return trim(stripslashes($string));
}

/**
 * Generate random string
 * 
 * @param int $length Length of string
 * @return string Random string
 */
function generate_random_string($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Upload file
 * 
 * @param array $file $_FILES array element
 * @param string $destination Destination folder
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return array ['success' => bool, 'file' => filename, 'error' => message]
 */
function upload_file($file, $destination, $allowed_types = [], $max_size = null) {
    $max_size = $max_size ?? MAX_FILE_SIZE;
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File size exceeds maximum allowed size'];
    }
    
    // Check file type
    $file_type = mime_content_type($file['tmp_name']);
    if (!empty($allowed_types) && !in_array($file_type, $allowed_types)) {
        return ['success' => false, 'error' => 'File type not allowed'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $destination . '/' . $filename;
    
    // Create destination directory if not exists
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'file' => $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to move uploaded file'];
}

/**
 * Delete file
 * 
 * @param string $filepath File path
 * @return bool True if deleted, false otherwise
 */
function delete_file($filepath) {
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Paginate array
 * 
 * @param array $items Items to paginate
 * @param int $page Current page
 * @param int $per_page Items per page
 * @return array Paginated data with metadata
 */
function paginate($items, $page = 1, $per_page = null) {
    $per_page = $per_page ?? RECORDS_PER_PAGE;
    $total = count($items);
    $total_pages = ceil($total / $per_page);
    $page = max(1, min($page, $total_pages));
    $offset = ($page - 1) * $per_page;
    
    return [
        'data' => array_slice($items, $offset, $per_page),
        'current_page' => $page,
        'per_page' => $per_page,
        'total' => $total,
        'total_pages' => $total_pages,
        'has_prev' => $page > 1,
        'has_next' => $page < $total_pages
    ];
}

/**
 * Generate pagination HTML
 * 
 * @param int $current_page Current page
 * @param int $total_pages Total pages
 * @param string $base_url Base URL for pagination links
 * @return string HTML for pagination
 */
function generate_pagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination">';
    
    // Previous button
    if ($current_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . ($current_page - 1) . '">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . $i . '">' . $i . '</a></li>';
        }
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . ($current_page + 1) . '">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Log activity to audit log
 * 
 * @param string $action Action performed
 * @param string $table_name Table name
 * @param int $record_id Record ID
 * @param array $old_value Old value
 * @param array $new_value New value
 */
function log_activity($action, $table_name = null, $record_id = null, $old_value = null, $new_value = null) {
    $user_id = get_user_id();
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $query = "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_value, new_value, ip_address, user_agent) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    db_query($query, 'ississss', [
        $user_id,
        $action,
        $table_name,
        $record_id,
        json_encode($old_value),
        json_encode($new_value),
        $ip_address,
        $user_agent
    ]);
}
