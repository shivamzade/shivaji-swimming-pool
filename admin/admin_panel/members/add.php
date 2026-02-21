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
    
    // Store form data for redisplay on error
    $form_data = [
        'first_name' => sanitize_input($_POST['first_name'] ?? ''),
        'last_name' => sanitize_input($_POST['last_name'] ?? ''),
        'gender' => sanitize_input($_POST['gender'] ?? ''),
        'date_of_birth' => sanitize_input($_POST['date_of_birth'] ?? ''),
        'blood_group' => sanitize_input($_POST['blood_group'] ?? ''),
        'medical_conditions' => sanitize_input($_POST['medical_conditions'] ?? ''),
        'phone' => sanitize_input($_POST['phone'] ?? ''),
        'alternate_phone' => sanitize_input($_POST['alternate_phone'] ?? ''),
        'email' => sanitize_input($_POST['email'] ?? ''),
        'address_line1' => sanitize_input($_POST['address_line1'] ?? ''),
        'address_line2' => sanitize_input($_POST['address_line2'] ?? ''),
        'city' => sanitize_input($_POST['city'] ?? ''),
        'state' => sanitize_input($_POST['state'] ?? ''),
        'pincode' => sanitize_input($_POST['pincode'] ?? ''),
        'id_proof_type' => sanitize_input($_POST['id_proof_type'] ?? ''),
        'id_proof_number' => sanitize_input($_POST['id_proof_number'] ?? ''),
        'emergency_contact_name' => sanitize_input($_POST['emergency_contact_name'] ?? ''),
        'emergency_contact_phone' => sanitize_input($_POST['emergency_contact_phone'] ?? ''),
        'emergency_contact_relation' => sanitize_input($_POST['emergency_contact_relation'] ?? ''),
        'remarks' => sanitize_input($_POST['remarks'] ?? '')
    ];
    
    $result = Member::create($_POST);
    
    if ($result['success']) {
        set_flash('success', $result['message'] . ' (Member Code: ' . $result['member_code'] . ')');
        redirect(ADMIN_URL . '/members/view.php?id=' . $result['member_id']);
    } else {
        set_flash('error', $result['message']);
    }
} else {
    // Initialize empty form data for GET requests
    $form_data = [
        'first_name' => '',
        'last_name' => '',
        'gender' => '',
        'date_of_birth' => '',
        'blood_group' => '',
        'medical_conditions' => '',
        'phone' => '',
        'alternate_phone' => '',
        'email' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'state' => '',
        'pincode' => '',
        'id_proof_type' => '',
        'id_proof_number' => '',
        'emergency_contact_name' => '',
        'emergency_contact_phone' => '',
        'emergency_contact_relation' => '',
        'remarks' => ''
    ];
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
                                        <input type="text" name="first_name" class="form-control" value="<?php echo $form_data['first_name']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" value="<?php echo $form_data['last_name']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-control" required>
                                            <option value="">Select Gender</option>
                                            <option value="MALE" <?php echo ($form_data['gender'] == 'MALE') ? 'selected' : ''; ?>>Male</option>
                                            <option value="FEMALE" <?php echo ($form_data['gender'] == 'FEMALE') ? 'selected' : ''; ?>>Female</option>
                                            <option value="OTHER" <?php echo ($form_data['gender'] == 'OTHER') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_birth" class="form-control" 
                                               value="<?php echo $form_data['date_of_birth']; ?>"
                                               max="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Blood Group</label>
                                        <select name="blood_group" class="form-control">
                                            <option value="">Select</option>
                                            <option value="A+" <?php echo ($form_data['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                                            <option value="A-" <?php echo ($form_data['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                                            <option value="B+" <?php echo ($form_data['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                                            <option value="B-" <?php echo ($form_data['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                                            <option value="AB+" <?php echo ($form_data['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                            <option value="AB-" <?php echo ($form_data['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                                            <option value="O+" <?php echo ($form_data['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                                            <option value="O-" <?php echo ($form_data['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Medical Conditions</label>
                                        <input type="text" name="medical_conditions" class="form-control" 
                                               value="<?php echo $form_data['medical_conditions']; ?>"
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
                                               value="<?php echo $form_data['phone']; ?>"
                                               pattern="[0-9]{10}" placeholder="10-digit mobile number" required>
                                        <small class="form-text text-muted">Enter 10-digit number</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Alternate Phone</label>
                                        <input type="tel" name="alternate_phone" class="form-control" 
                                               value="<?php echo $form_data['alternate_phone']; ?>"
                                               pattern="[0-9]{10}" placeholder="Optional">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" 
                                               value="<?php echo $form_data['email']; ?>"
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
                                        <input type="text" name="address_line1" class="form-control" value="<?php echo $form_data['address_line1']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address Line 2</label>
                                        <input type="text" name="address_line2" class="form-control" value="<?php echo $form_data['address_line2']; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" name="city" class="form-control" value="<?php echo $form_data['city']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" name="state" class="form-control" value="<?php echo $form_data['state']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Pincode</label>
                                        <input type="text" name="pincode" class="form-control" 
                                               value="<?php echo $form_data['pincode']; ?>"
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
                                            <option value="AADHAR" <?php echo ($form_data['id_proof_type'] == 'AADHAR') ? 'selected' : ''; ?>>Aadhar Card</option>
                                            <option value="PAN" <?php echo ($form_data['id_proof_type'] == 'PAN') ? 'selected' : ''; ?>>PAN Card</option>
                                            <option value="DRIVING_LICENSE" <?php echo ($form_data['id_proof_type'] == 'DRIVING_LICENSE') ? 'selected' : ''; ?>>Driving License</option>
                                            <option value="PASSPORT" <?php echo ($form_data['id_proof_type'] == 'PASSPORT') ? 'selected' : ''; ?>>Passport</option>
                                            <option value="OTHER" <?php echo ($form_data['id_proof_type'] == 'OTHER') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID Proof Number</label>
                                        <input type="text" name="id_proof_number" class="form-control" value="<?php echo $form_data['id_proof_number']; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Batch Assignment -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Batch Assignment</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Preferred Batch</label>
                                        <select name="batch_id" class="form-control">
                                            <option value="">-- Select Batch (Optional) --</option>
                                            <?php
                                            try {
                                                $batch_sql = "
                                                    SELECT b.*, (b.max_capacity - COUNT(mb.assignment_id)) as available_slots
                                                    FROM batches b
                                                    LEFT JOIN member_batches mb ON b.batch_id = mb.batch_id AND mb.status = 'ACTIVE'
                                                    WHERE b.is_active = 1
                                                    GROUP BY b.batch_id
                                                    HAVING available_slots > 0
                                                    ORDER BY b.start_time
                                                ";
                                                $batch_result = $conn->query($batch_sql);
                                                $batches = $batch_result ? $batch_result->fetch_all(MYSQLI_ASSOC) : [];
                                                
                                                foreach ($batches as $b) {
                                                    $selected = (isset($form_data['batch_id']) && $form_data['batch_id'] == $b['batch_id']) ? 'selected' : '';
                                                    echo "<option value='{$b['batch_id']}' {$selected}>";
                                                    echo htmlspecialchars($b['batch_name']) . " (" . date('h:i A', strtotime($b['start_time'])) . " - " . date('h:i A', strtotime($b['end_time'])) . ")";
                                                    echo " [{$b['available_slots']} slots available]";
                                                    echo "</option>";
                                                }
                                            } catch (Exception $e) {
                                                echo "<option value=''>Error loading batches</option>";
                                            }
                                            ?>
                                        </select>
                                        <small class="form-text text-muted">Assign member to a preferred batch (optional)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Batch Remarks</label>
                                        <input type="text" name="batch_remarks" class="form-control" 
                                               value="<?php echo $form_data['batch_remarks'] ?? ''; ?>"
                                               placeholder="Any remarks about batch assignment (optional)">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Emergency Contact -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Emergency Contact</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Name</label>
                                        <input type="text" name="emergency_contact_name" class="form-control" value="<?php echo $form_data['emergency_contact_name']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact Phone</label>
                                        <input type="tel" name="emergency_contact_phone" class="form-control" 
                                               value="<?php echo $form_data['emergency_contact_phone']; ?>"
                                               pattern="[0-9]{10}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Relation</label>
                                        <input type="text" name="emergency_contact_relation" class="form-control" 
                                               value="<?php echo $form_data['emergency_contact_relation']; ?>"
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
                                                  placeholder="Any additional notes (optional)"><?php echo $form_data['remarks']; ?></textarea>
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
