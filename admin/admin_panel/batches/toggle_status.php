<?php
require_once '../../../config/config.php';
require_once '../../../db_connect.php';
require_once '../../../classes/Batch.php';

// Check authentication and admin role
require_login();
if (!has_role([1, 2])) {
    redirect(ADMIN_URL . '/batches/index.php');
}

// Get batch ID
$batch_id = $_GET['id'] ?? 0;
if (!$batch_id) {
    redirect(ADMIN_URL . '/batches/index.php');
}

// Initialize Batch class
$batch = new Batch($conn);

// Toggle batch status
if ($batch->toggleBatchStatus($batch_id)) {
    $_SESSION['success'] = "Batch status updated successfully!";
} else {
    $_SESSION['error'] = "Failed to update batch status.";
}

redirect(ADMIN_URL . '/batches/index.php');
