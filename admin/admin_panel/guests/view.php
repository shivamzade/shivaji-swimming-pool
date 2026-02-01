<?php
/**
 * View Guest Details
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'View Guest Details';

// Get guest ID
$guest_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$guest_id) {
    set_flash('error', 'Invalid guest ID');
    redirect(ADMIN_URL . '/guests/index.php');
}

// Get guest details
$guest = Guest::get_by_id($guest_id);

if (!$guest) {
    set_flash('error', 'Guest not found');
    redirect(ADMIN_URL . '/guests/index.php');
}

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="icon-eye"></i> Guest Details
                        <div class="card-action">
                            <a href="index.php" class="btn btn-light btn-sm"><i class="icon-list"></i> Back to List</a>
                            <?php if ($guest['status'] == 'ACTIVE'): ?>
                                <a href="edit.php?id=<?php echo $guest['guest_id']; ?>" class="btn btn-warning btn-sm"><i class="icon-pencil"></i> Edit</a>
                                <a href="checkout.php?id=<?php echo $guest['guest_id']; ?>" class="btn btn-info btn-sm"><i class="icon-logout"></i> Check Out</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <!-- Guest Header -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h3><?php echo htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']); ?></h3>
                                <p class="text-muted">
                                    Guest Code: <strong><?php echo htmlspecialchars($guest['guest_code']); ?></strong> | 
                                    Status: <span class="badge badge-<?php echo $guest['status'] == 'ACTIVE' ? 'success' : ($guest['status'] == 'CHECKED_OUT' ? 'secondary' : 'danger'); ?>">
                                        <?php echo htmlspecialchars($guest['status']); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="badge badge-info badge-lg"><?php echo htmlspecialchars($guest['guest_type']); ?></div>
                            </div>
                        </div>
                        
                        <!-- Personal Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>First Name:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['first_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Name:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['last_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Gender:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['gender']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date of Birth:</strong></td>
                                        <td><?php echo format_date($guest['date_of_birth']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Blood Group:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['blood_group'] ?: 'Not specified'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Medical Conditions:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['medical_conditions'] ?: 'None'); ?></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Contact Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Phone:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['phone']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Alternate Phone:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['alternate_phone'] ?: 'Not provided'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['email'] ?: 'Not provided'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address:</strong></td>
                                        <td>
                                            <?php
                                            $address_parts = [];
                                            if ($guest['address_line1']) $address_parts[] = htmlspecialchars($guest['address_line1']);
                                            if ($guest['address_line2']) $address_parts[] = htmlspecialchars($guest['address_line2']);
                                            if ($guest['city']) $address_parts[] = htmlspecialchars($guest['city']);
                                            if ($guest['state']) $address_parts[] = htmlspecialchars($guest['state']);
                                            if ($guest['pincode']) $address_parts[] = htmlspecialchars($guest['pincode']);
                                            echo implode(', ', $address_parts) ?: 'Not provided';
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Visit Information -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Visit Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Guest Type:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['guest_type']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Visit Date:</strong></td>
                                        <td><?php echo format_date($guest['visit_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Check-in Time:</strong></td>
                                        <td><?php echo format_datetime($guest['check_in_time']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Check-out Time:</strong></td>
                                        <td>
                                            <?php if ($guest['check_out_time']): ?>
                                                <?php echo format_datetime($guest['check_out_time']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not checked out yet</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Duration:</strong></td>
                                        <td>
                                            <?php if ($guest['duration_hours']): ?>
                                                <?php echo round($guest['duration_hours'], 1); ?> hours
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">ID Proof</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>ID Type:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['id_proof_type'] ?: 'Not provided'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>ID Number:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['id_proof_number'] ?: 'Not provided'); ?></td>
                                    </tr>
                                </table>
                                
                                <h5 class="border-bottom pb-2 mb-3 mt-4">Emergency Contact</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Name:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['emergency_contact_name'] ?: 'Not provided'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['emergency_contact_phone'] ?: 'Not provided'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Relation:</strong></td>
                                        <td><?php echo htmlspecialchars($guest['emergency_contact_relation'] ?: 'Not provided'); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- System Information -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">System Information</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td width="20%"><strong>Guest ID:</strong></td>
                                        <td width="30%"><?php echo $guest['guest_id']; ?></td>
                                        <td width="20%"><strong>Created At:</strong></td>
                                        <td width="30%"><?php echo format_datetime($guest['created_at']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated At:</strong></td>
                                        <td><?php echo format_datetime($guest['updated_at']); ?></td>
                                        <td><strong>Created By:</strong></td>
                                        <td>
                                            <?php
                                            if ($guest['created_by']) {
                                                $creator = Auth::get_user_by_id($guest['created_by']);
                                                echo htmlspecialchars($creator['full_name'] ?? 'Unknown');
                                            } else {
                                                echo 'System';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="btn-group">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="icon-arrow-left"></i> Back to List
                                    </a>
                                    <?php if ($guest['status'] == 'ACTIVE'): ?>
                                        <a href="edit.php?id=<?php echo $guest['guest_id']; ?>" class="btn btn-warning">
                                            <i class="icon-pencil"></i> Edit Guest
                                        </a>
                                        <a href="checkout.php?id=<?php echo $guest['guest_id']; ?>" class="btn btn-info">
                                            <i class="icon-logout"></i> Check Out
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" onclick="window.print()" class="btn btn-primary">
                                        <i class="icon-printer"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php
// Include footer
include_once '../../../includes/admin_footer.php';
?>
