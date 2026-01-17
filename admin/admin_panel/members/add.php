<?php
/**
 * Add New Member
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'Add New Member';

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    $result = Member::create($_POST);
    
    if ($result['success']) {
        set_flash('success', $result['message'] . ' (Member Code: ' . $result['member_code'] . ')');
        redirect(ADMIN_URL . '/members/view.php?id=' . $result['member_id']);
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
                    <div class="card-header bg-primary text-white">
                        <i class="icon-user-follow"></i> Add New Member
                        <div class="card-action">
                             <a href="print_form.php" target="_blank" class="btn btn-light btn-sm"><i class="icon-printer"></i> Print Blank Form</a>
                        </div>
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
                                        <input type="text" name="first_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control" required>
                                            <option value="">Select Gender</option>
                                            <option value="MALE">Male</option>
                                            <option value="FEMALE">Female</option>
                                            <option value="OTHER">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_birth" class="form-control" 
                                               max="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Blood Group</label>
                                        <select name="blood_group" class="form-control">
                                            <option value="">Select</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Medical Conditions</label>
                                        <input type="text" name="medical_conditions" class="form-control" 
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
                                               pattern="[0-9]{10}" placeholder="10-digit mobile number" required>
                                        <small class="form-text text-muted">Enter 10-digit number</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Alternate Phone</label>
                                        <input type="tel" name="alternate_phone" class="form-control" 
                                               pattern="[0-9]{10}" placeholder="Optional">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" 
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
                                        <input type="text" name="address_line1" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address Line 2</label>
                                        <input type="text" name="address_line2" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" name="city" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" name="state" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Pincode</label>
                                        <input type="text" name="pincode" class="form-control" 
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
                                            <option value="AADHAR">Aadhar Card</option>
                                            <option value="PAN">PAN Card</option>
                                            <option value="DRIVING_LICENSE">Driving License</option>
                                            <option value="PASSPORT">Passport</option>
                                            <option value="OTHER">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID Proof Number</label>
                                        <input type="text" name="id_proof_number" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Emergency Contact -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Emergency Contact</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Name</label>
                                        <input type="text" name="emergency_contact_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Phone</label>
                                        <input type="tel" name="emergency_contact_phone" class="form-control" 
                                               pattern="[0-9]{10}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Relation</label>
                                        <input type="text" name="emergency_contact_relation" class="form-control" 
                                               placeholder="e.g., Father, Mother, Spouse">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Remarks -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="3" 
                                                  placeholder="Any additional notes (optional)"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="icon-check"></i> Register Member
                                </button>
                                <a href="<?php echo ADMIN_URL; ?>/members/index.php" class="btn btn-secondary btn-lg">
                                    <i class="icon-close"></i> Cancel
                                </a>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <strong>Note:</strong> Member Code will be generated automatically after registration.
                                You can assign a membership plan after creating the member profile.
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
