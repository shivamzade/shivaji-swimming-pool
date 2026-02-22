<?php
/**
 * Admin Logout
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../config/config.php';
require_once '../../db_connect.php';

// Logout user
Auth::logout();

// Set success message
set_flash('success', 'You have been logged out successfully');

// Redirect to login page
redirect(BASE_URL . '/admin/index.php');
