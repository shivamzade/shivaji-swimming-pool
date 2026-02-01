<?php
/**
 * Delete Guest
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Get guest ID
$guest_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$guest_id) {
    set_flash('error', 'Invalid guest ID');
    redirect(ADMIN_URL . '/guests/index.php');
}

// Handle deletion
$result = Guest::delete($guest_id);

if ($result['success']) {
    set_flash('success', $result['message']);
} else {
    set_flash('error', $result['message']);
}

redirect(ADMIN_URL . '/guests/index.php');
?>
