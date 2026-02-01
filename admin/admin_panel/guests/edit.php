<?php
/**
 * Edit Guest
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'Edit Guest';

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

// Check if guest can be edited (only active guests can be edited)
if ($guest['status'] != 'ACTIVE') {
    set_flash('error', 'Only active guests can be edited');
    redirect(ADMIN_URL . '/guests/view.php?id=' . $guest_id);
}

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    $result = Guest::update($guest_id, $_POST);
    
    if ($result['success']) {
        set_flash('success', $result['message']);
        redirect(ADMIN_URL . '/guests/view.php?id=' . $guest_id);
    } else {
        set_flash('error', $result['message']);
    }
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
                    <div class="card-header bg-warning text-white">
                        <i class="icon-pencil"></i> Edit Guest
                        <div class="card-action">
                             <a href="view.php?id=<?php echo $guest_id; ?>" class="btn btn-light btn-sm"><i class="icon-eye"></i> View</a>
                             <a href="index.php" class="btn btn-light btn-sm"><i class="icon-list"></i> List</a>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <!-- Guest Info -->
                            <div class="alert alert-info">
                                <strong>Guest Code:</strong> <?php echo htmlspecialchars($guest['guest_code']); ?> | 
                                <strong>Status:</strong> <span class="badge badge-success"><?php echo htmlspecialchars($guest['status']); ?></span>
                            </div>
                            
                            <!-- Personal Information -->
                            <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['first_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['last_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control" required>
                                            <option value="">Select Gender</option>
                                            <option value="MALE" <?php echo $guest['gender'] == 'MALE' ? 'selected' : ''; ?>>Male</option>
                                            <option value="FEMALE" <?php echo $guest['gender'] == 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                                            <option value="OTHER" <?php echo $guest['gender'] == 'OTHER' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_birth" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['date_of_birth']); ?>"
                                               max="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Blood Group</label>
                                        <select name="blood_group" class="form-control">
                                            <option value="">Select</option>
                                            <option value="A+" <?php echo $guest['blood_group'] == 'A+' ? 'selected' : ''; ?>>A+</option>
                                            <option value="A-" <?php echo $guest['blood_group'] == 'A-' ? 'selected' : ''; ?>>A-</option>
                                            <option value="B+" <?php echo $guest['blood_group'] == 'B+' ? 'selected' : ''; ?>>B+</option>
                                            <option value="B-" <?php echo $guest['blood_group'] == 'B-' ? 'selected' : ''; ?>>B-</option>
                                            <option value="AB+" <?php echo $guest['blood_group'] == 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                            <option value="AB-" <?php echo $guest['blood_group'] == 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                            <option value="O+" <?php echo $guest['blood_group'] == 'O+' ? 'selected' : ''; ?>>O+</option>
                                            <option value="O-" <?php echo $guest['blood_group'] == 'O-' ? 'selected' : ''; ?>>O-</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Medical Conditions</label>
                                        <input type="text" name="medical_conditions" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['medical_conditions'] ?? ''); ?>"
                                               placeholder="Any medical conditions (optional)">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Contact Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" name="phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['phone']); ?>"
                                               pattern="[0-9]{10}" placeholder="10-digit mobile number" required>
                                        <small class="form-text text-muted">Enter 10-digit number</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Alternate Phone</label>
                                        <input type="tel" name="alternate_phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['alternate_phone'] ?? ''); ?>"
                                               pattern="[0-9]{10}" placeholder="Optional">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['email'] ?? ''); ?>"
                                               placeholder="email@example.com (optional)">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Address</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address Line 1</label>
                                        <input type="text" name="address_line1" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['address_line1'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address Line 2</label>
                                        <input type="text" name="address_line2" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['address_line2'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" name="city" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['city'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" name="state" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['state'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Pincode</label>
                                        <input type="text" name="pincode" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['pincode'] ?? ''); ?>"
                                               pattern="[0-9]{6}" placeholder="6-digit pincode">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ID Proof -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Identity Proof</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID Proof Type</label>
                                        <select name="id_proof_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="AADHAR" <?php echo $guest['id_proof_type'] == 'AADHAR' ? 'selected' : ''; ?>>Aadhar Card</option>
                                            <option value="PAN" <?php echo $guest['id_proof_type'] == 'PAN' ? 'selected' : ''; ?>>PAN Card</option>
                                            <option value="DRIVING_LICENSE" <?php echo $guest['id_proof_type'] == 'DRIVING_LICENSE' ? 'selected' : ''; ?>>Driving License</option>
                                            <option value="PASSPORT" <?php echo $guest['id_proof_type'] == 'PASSPORT' ? 'selected' : ''; ?>>Passport</option>
                                            <option value="OTHER" <?php echo $guest['id_proof_type'] == 'OTHER' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID Proof Number</label>
                                        <input type="text" name="id_proof_number" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['id_proof_number'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Emergency Contact -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Emergency Contact</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Name</label>
                                        <input type="text" name="emergency_contact_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['emergency_contact_name'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Phone</label>
                                        <input type="tel" name="emergency_contact_phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['emergency_contact_phone'] ?? ''); ?>"
                                               pattern="[0-9]{10}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Relation</label>
                                        <input type="text" name="emergency_contact_relation" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['emergency_contact_relation'] ?? ''); ?>"
                                               placeholder="e.g., Father, Mother, Spouse">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Guest Information -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Visit Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Guest Type <span class="text-danger">*</span></label>
                                        <select name="guest_type" class="form-control" required>
                                            <option value="DAILY" <?php echo $guest['guest_type'] == 'DAILY' ? 'selected' : ''; ?>>Daily Guest</option>
                                            <option value="HOURLY" <?php echo $guest['guest_type'] == 'HOURLY' ? 'selected' : ''; ?>>Hourly Guest</option>
                                            <option value="SPECIAL" <?php echo $guest['guest_type'] == 'SPECIAL' ? 'selected' : ''; ?>>Special Guest</option>
                                            <option value="COMPANION" <?php echo $guest['guest_type'] == 'COMPANION' ? 'selected' : ''; ?>>Companion</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Visit Date <span class="text-danger">*</span></label>
                                        <input type="date" name="visit_date" class="form-control" 
                                               value="<?php echo htmlspecialchars($guest['visit_date']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Check-in Time</label>
                                        <input type="time" name="check_in_time" class="form-control" 
                                               value="<?php echo date('H:i', strtotime($guest['check_in_time'])); ?>">
                                        <small class="form-text text-muted">Original: <?php echo format_datetime($guest['check_in_time'], 'H:i'); ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="icon-check"></i> Update Guest
                                </button>
                                <a href="<?php echo ADMIN_URL; ?>/guests/view.php?id=<?php echo $guest_id; ?>" class="btn btn-secondary btn-lg">
                                    <i class="icon-close"></i> Cancel
                                </a>
                            </div>
                            
                            <div class="alert alert-warning mt-3">
                                <strong>Note:</strong> Only active guests can be edited. Check-out time cannot be modified here.
                            </div>
                        </form>
                        
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
