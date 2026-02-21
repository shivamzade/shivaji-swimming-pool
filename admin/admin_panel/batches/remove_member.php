<?php
require_once '../../../config/config.php';
require_once '../../../db_connect.php';
require_once '../../../classes/Batch.php';

// Check authentication and admin role
require_login();
if (!has_role([1, 2])) {
    redirect(ADMIN_URL . '/batches/index.php');
}

// Get batch ID and member ID
$batch_id = $_GET['batch_id'] ?? 0;
$member_id = $_GET['member_id'] ?? 0;

if (!$batch_id || !$member_id) {
    redirect(ADMIN_URL . '/batches/index.php');
}

// Initialize Batch class
$batch = new Batch($conn);

// Remove member from batch
if ($batch->removeMemberFromBatch($member_id, $batch_id)) {
    $_SESSION['success'] = "Member removed from batch successfully!";
} else {
    $_SESSION['error'] = "Failed to remove member from batch.";
}

redirect(ADMIN_URL . '/batches/view.php?id=' . $batch_id);
