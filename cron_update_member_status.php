<?php
/**
 * Cron Job: Update Member Status
 * 
 * This script should be run daily (recommended: 12:00 AM)
 * to update member statuses based on membership expiry dates
 * 
 * Usage: php cron_update_member_status.php
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once 'config/config.php';
require_once 'db_connect.php';

echo "Starting member status update: " . date('Y-m-d H:i:s') . "\n";

// Update members whose membership has expired but status is still ACTIVE
$update_query = "UPDATE members 
                 SET status = 'EXPIRED' 
                 WHERE status = 'ACTIVE' 
                 AND membership_end_date < CURDATE()";

$result = db_query($update_query);

if ($result) {
    $affected_rows = db_affected_rows();
    echo "Updated {$affected_rows} members to EXPIRED status\n";
    
    // Log the batch update
    log_activity('BATCH_STATUS_UPDATE', 'members', null, null, [
        'action' => 'update_expired_members',
        'count' => $affected_rows,
        'date' => date('Y-m-d')
    ]);
} else {
    echo "Error updating member statuses\n";
}

// Update member_memberships table - expire active memberships past end date
$membership_update_query = "UPDATE member_memberships 
                           SET status = 'EXPIRED' 
                           WHERE status = 'ACTIVE' 
                           AND end_date < CURDATE()";

$membership_result = db_query($membership_update_query);

if ($membership_result) {
    $membership_affected = db_affected_rows();
    echo "Updated {$membership_affected} membership records to EXPIRED status\n";
}

// Get current statistics for logging
$stats_query = "SELECT 
    COUNT(*) as total_members,
    SUM(CASE WHEN status = 'ACTIVE' THEN 1 ELSE 0 END) as active_members,
    SUM(CASE WHEN status = 'EXPIRED' THEN 1 ELSE 0 END) as expired_members,
    SUM(CASE WHEN status = 'SUSPENDED' THEN 1 ELSE 0 END) as suspended_members,
    SUM(CASE WHEN status = 'INACTIVE' THEN 1 ELSE 0 END) as inactive_members
    FROM members";

$stats = db_fetch_one($stats_query);

echo "Current member statistics:\n";
echo "- Total: {$stats['total_members']}\n";
echo "- Active: {$stats['active_members']}\n";
echo "- Expired: {$stats['expired_members']}\n";
echo "- Suspended: {$stats['suspended_members']}\n";
echo "- Inactive: {$stats['inactive_members']}\n";

echo "Member status update completed: " . date('Y-m-d H:i:s') . "\n";
?>
