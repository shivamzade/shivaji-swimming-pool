<?php
/**
 * Database Connection File
 * 
 * This file establishes a connection to the MySQL database
 * and provides helper functions for database operations
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Database credentials
$server_name = "localhost:3306";
$user_name = "root";
$password = "";
$database_name = "shivaji_pool";

// Create connection
$conn = new mysqli($server_name, $user_name, $password, $database_name);

// Check connection
if ($conn->connect_error) {
    // Log error in production
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
        error_log("Database connection failed: " . $conn->connect_error);
        die("Database connection error. Please contact administrator.");
    } else {
        die("Connection failed: " . $conn->connect_error);
    }
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Configure MySQLi to throw exceptions (better error handling)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * Execute a prepared statement query
 * 
 * @param string $query SQL query with placeholders (?)
 * @param string $types Parameter types (s=string, i=integer, d=double, b=blob)
 * @param array $params Parameters to bind
 * @return mysqli_result|bool Query result or false on failure
 */
function db_query($query, $types = '', $params = []) {
    global $conn;
    
    try {
        $stmt = $conn->prepare($query);
        
        if ($types && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        
        // For queries that return a result set (SELECT, DESCRIBE, SHOW, etc.)
        $result = $stmt->get_result();
        if ($result !== false) {
            return $result;
        }
        
        // For INSERT/UPDATE/DELETE, return statement for affected_rows, insert_id, etc.
        return $stmt;
        
    } catch (mysqli_sql_exception $e) {
        // Log error
        error_log("Database query error: " . $e->getMessage());
        
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            throw $e;
        }
        
        return false;
    }
}

/**
 * Execute a SELECT query and return all rows
 * 
 * @param string $query SQL query
 * @param string $types Parameter types
 * @param array $params Parameters
 * @return array Array of rows
 */
function db_fetch_all($query, $types = '', $params = []) {
    $result = db_query($query, $types, $params);
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

/**
 * Execute a SELECT query and return single row
 * 
 * @param string $query SQL query
 * @param string $types Parameter types
 * @param array $params Parameters
 * @return array|null Single row or null
 */
function db_fetch_one($query, $types = '', $params = []) {
    $result = db_query($query, $types, $params);
    if ($result) {
        return $result->fetch_assoc();
    }
    return null;
}

/**
 * Get last insert ID
 * 
 * @return int Last inserted ID
 */
function db_insert_id() {
    global $conn;
    return $conn->insert_id;
}

/**
 * Get number of affected rows
 * 
 * @return int Number of affected rows
 */
function db_affected_rows() {
    global $conn;
    return $conn->affected_rows;
}

/**
 * Escape string for SQL (use prepared statements instead when possible)
 * 
 * @param string $value Value to escape
 * @return string Escaped value
 */
function db_escape($value) {
    global $conn;
    return $conn->real_escape_string($value);
}

/**
 * Begin transaction
 */
function db_begin_transaction() {
    global $conn;
    $conn->begin_transaction();
}

/**
 * Commit transaction
 */
function db_commit() {
    global $conn;
    $conn->commit();
}

/**
 * Rollback transaction
 */
function db_rollback() {
    global $conn;
    $conn->rollback();
}
