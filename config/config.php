<?php
/**
 * Shivaji Swimming Pool Management System
 * Main Configuration File
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Prevent direct access
defined('APP_NAME') or define('APP_NAME', 'Shivaji Pool');

// =====================================================
// ENVIRONMENT CONFIGURATION
// =====================================================
define('ENVIRONMENT', 'development'); // development | production

// =====================================================
// PATH CONFIGURATION
// =====================================================
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CLASS_PATH', ROOT_PATH . '/classes');
define('HELPER_PATH', ROOT_PATH . '/helpers');
define('INCLUDE_PATH', ROOT_PATH . '/includes');
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');

// =====================================================
// URL CONFIGURATION
// =====================================================
// Manual base URL (change this if deployed to different location)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Set base URL to the project root
define('BASE_URL', $protocol . '://' . $host . '/shivaji_pool');
define('ADMIN_URL', BASE_URL . '/admin/admin_panel');
define('ASSETS_URL', BASE_URL . '/assets');

// =====================================================
// DATABASE CONFIGURATION
// =====================================================
define('DB_HOST', 'localhost:3306');
define('DB_NAME', 'shivaji_pool');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =====================================================
// SESSION CONFIGURATION
// =====================================================
define('SESSION_NAME', 'SHIVAJI_POOL_SESSION');
define('SESSION_LIFETIME', 7200); // 2 hours in seconds
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SECURE', false); // Set to true in production with SSL

// =====================================================
// SECURITY CONFIGURATION
// =====================================================
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 12);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes in seconds

// =====================================================
// APPLICATION SETTINGS
// =====================================================
define('POOL_NAME', 'Shivaji Swimming Pool');
define('POOL_CAPACITY', 200);
define('OPENING_TIME', '06:00');
define('CLOSING_TIME', '22:00');
define('TIMEZONE', 'Asia/Kolkata');
define('DATE_FORMAT', 'd-m-Y');
define('TIME_FORMAT', 'h:i A');
define('DATETIME_FORMAT', 'd-m-Y h:i A');

// =====================================================
// UPLOAD CONFIGURATION
// =====================================================
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);

// =====================================================
// PAGINATION CONFIGURATION
// =====================================================
define('RECORDS_PER_PAGE', 25);
define('PAGINATION_LINKS', 5);

// =====================================================
// ERROR HANDLING
// =====================================================
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . '/php_errors.log');
}

// =====================================================
// TIMEZONE SETTING
// =====================================================
date_default_timezone_set(TIMEZONE);

// =====================================================
// SESSION CONFIGURATION
// =====================================================
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => '',
        'secure' => SESSION_COOKIE_SECURE,
        'httponly' => SESSION_COOKIE_HTTPONLY,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// =====================================================
// AUTOLOAD HELPER FUNCTION
// =====================================================
function autoload_class($class_name) {
    $file = CLASS_PATH . '/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    return false;
}

// Register autoloader
spl_autoload_register('autoload_class');

// =====================================================
// LOAD HELPER FILES
// =====================================================
require_once HELPER_PATH . '/functions.php';
require_once HELPER_PATH . '/security.php';

/**
 * Get setting value from database
 * 
 * @param string $key Setting key
 * @param mixed $default Default value if not found
 * @return mixed Setting value
 */
function get_setting($key, $default = null) {
    static $settings = null;
    
    if ($settings === null) {
        // Load settings from database (cached)
        require_once ROOT_PATH . '/db_connect.php';
        global $conn;
        
        $settings = [];
        $result = $conn->query("SELECT setting_key, setting_value FROM settings");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
    }
    
    return $settings[$key] ?? $default;
}
