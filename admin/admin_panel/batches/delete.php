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

// Attempt to delete batch
$result = $batch->deleteBatch($batch_id);

if ($result === true) {
    set_flash('success', 'Batch deleted successfully!');
} elseif ($result === 'has_members') {
    set_flash('error', 'Cannot delete batch: it still has active members assigned. Remove all members first.');
} else {
    set_flash('error', 'Failed to delete batch. Please try again.');
}

redirect(ADMIN_URL . '/batches/index.php');
