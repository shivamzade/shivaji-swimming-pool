<?php
/**
 * Edit Member
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Check if ID is provided
$member_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$member_id) {
    set_flash('error', 'Member ID is required');
    redirect(ADMIN_URL . '/members/index.php');
}

// Get member data
$member = Member::get_by_id($member_id);

if (!$member) {
    set_flash('error', 'Member not found');
    redirect(ADMIN_URL . '/members/index.php');
}

// Set page title
$page_title = 'Edit Member: ' . $member['member_code'];

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    if (Member::update($member_id, $_POST)) {
        set_flash('success', 'Member details updated successfully');
        redirect(ADMIN_URL . '/members/view.php?id=' . $member_id);
    } else {
        set_flash('error', 'Failed to update member details');
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
                    <div class="card-header bg-primary text-white">
                        <i class="icon-pencil"></i> Edit Member: <?php echo clean($member['first_name'] . ' ' . $member['last_name']); ?> (<?php echo clean($member['member_code']); ?>)
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <!-- Personal Information -->
                            <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" value="<?php echo clean($member['first_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" value="<?php echo clean($member['last_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control" required>
                                            <option value="MALE" <?php echo $member['gender'] == 'MALE' ? 'selected' : ''; ?>>Male</option>
                                            <option value="FEMALE" <?php echo $member['gender'] == 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                                            <option value="OTHER" <?php echo $member['gender'] == 'OTHER' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_birth" class="form-control" 
                                               value="<?php echo $member['date_of_birth']; ?>" max="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Blood Group</label>
                                        <select name="blood_group" class="form-control">
                                            <option value="" <?php echo $member['blood_group'] == '' ? 'selected' : ''; ?>>Select</option>
                                            <?php 
                                            $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                            foreach($blood_groups as $bg): ?>
                                                <option value="<?php echo $bg; ?>" <?php echo $member['blood_group'] == $bg ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Medical Conditions</label>
                                        <input type="text" name="medical_conditions" class="form-control" 
                                               value="<?php echo clean($member['medical_conditions']); ?>">
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
                                               value="<?php echo clean($member['phone']); ?>" pattern="[0-9]{10}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Alternate Phone</label>
                                        <input type="tel" name="alternate_phone" class="form-control" 
                                               value="<?php echo clean($member['alternate_phone']); ?>" pattern="[0-9]{10}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" 
                                               value="<?php echo clean($member['email']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Address</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address Line 1</label>
                                        <input type="text" name="address_line1" class="form-control" value="<?php echo clean($member['address_line1']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address Line 2</label>
                                        <input type="text" name="address_line2" class="form-control" value="<?php echo clean($member['address_line2']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" name="city" class="form-control" value="<?php echo clean($member['city']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" name="state" class="form-control" value="<?php echo clean($member['state']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Pincode</label>
                                        <input type="text" name="pincode" class="form-control" 
                                               value="<?php echo clean($member['pincode']); ?>" pattern="[0-9]{6}">
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
                                            <option value="" <?php echo $member['id_proof_type'] == '' ? 'selected' : ''; ?>>Select</option>
                                            <option value="AADHAR" <?php echo $member['id_proof_type'] == 'AADHAR' ? 'selected' : ''; ?>>Aadhar Card</option>
                                            <option value="PAN" <?php echo $member['id_proof_type'] == 'PAN' ? 'selected' : ''; ?>>PAN Card</option>
                                            <option value="DRIVING_LICENSE" <?php echo $member['id_proof_type'] == 'DRIVING_LICENSE' ? 'selected' : ''; ?>>Driving License</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID Proof Number</label>
                                        <input type="text" name="id_proof_number" class="form-control" value="<?php echo clean($member['id_proof_number']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Emergency Contact -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Emergency Contact</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Name</label>
                                        <input type="text" name="emergency_contact_name" class="form-control" value="<?php echo clean($member['emergency_contact_name']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Phone</label>
                                        <input type="tel" name="emergency_contact_phone" class="form-control" value="<?php echo clean($member['emergency_contact_phone']); ?>" pattern="[0-9]{10}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Relation</label>
                                        <input type="text" name="emergency_contact_relation" class="form-control" value="<?php echo clean($member['emergency_contact_relation']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="3"><?php echo clean($member['remarks']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="icon-check"></i> Update Details
                                </button>
                                <a href="view.php?id=<?php echo $member_id; ?>" class="btn btn-secondary btn-lg">
                                    <i class="icon-close"></i> Cancel
                                </a>
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
