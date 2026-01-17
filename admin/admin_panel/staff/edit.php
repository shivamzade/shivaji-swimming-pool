<?php
/**
 * Edit Staff Member Profile
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Check permissions
if (!has_role([1, 2])) {
    set_flash('error', 'Unauthorized access.');
    redirect(ADMIN_URL . '/index.php');
}

// Check if ID is provided
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    set_flash('error', 'Staff ID is required');
    redirect('index.php');
}

// Get user data joined with staff data
$query = "SELECT u.*, s.employee_id, s.designation, s.join_date, s.salary 
          FROM users u
          LEFT JOIN staff s ON u.user_id = s.user_id
          WHERE u.user_id = ?";
$staff = db_fetch_one($query, 'i', [$user_id]);

if (!$staff) {
    set_flash('error', 'Staff member not found');
    redirect('index.php');
}

$page_title = 'Edit Staff: ' . $staff['full_name'];

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $role_id = intval($_POST['role_id']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $employee_id = sanitize_input($_POST['employee_id']);
    $designation = sanitize_input($_POST['designation']);
    $join_date = sanitize_input($_POST['join_date']);
    $salary = floatval($_POST['salary']);
    
    $new_password = $_POST['new_password'];
    
    // Validation
    if (empty($full_name) || empty($email)) {
        set_flash('error', 'Full name and Email are required.');
    } else {
        db_begin_transaction();
        try {
            // Update users table
            if (!empty($new_password)) {
                $password_hash = hash_password($new_password);
                $user_query = "UPDATE users SET full_name = ?, email = ?, phone = ?, role_id = ?, is_active = ?, password_hash = ? WHERE user_id = ?";
                $result = db_query($user_query, 'sssissi', [$full_name, $email, $phone, $role_id, $is_active, $password_hash, $user_id]);
            } else {
                $user_query = "UPDATE users SET full_name = ?, email = ?, phone = ?, role_id = ?, is_active = ? WHERE user_id = ?";
                $result = db_query($user_query, 'sssiii', [$full_name, $email, $phone, $role_id, $is_active, $user_id]);
            }
            
            // Update or Insert staff table
            $check_staff = db_fetch_one("SELECT staff_id FROM staff WHERE user_id = ?", 'i', [$user_id]);
            if ($check_staff) {
                $staff_query = "UPDATE staff SET employee_id = ?, designation = ?, join_date = ?, salary = ? WHERE user_id = ?";
                db_query($staff_query, 'ssssi', [$employee_id, $designation, $join_date, $salary, $user_id]);
            } else {
                $staff_query = "INSERT INTO staff (user_id, employee_id, designation, join_date, salary, created_by) VALUES (?, ?, ?, ?, ?, ?)";
                db_query($staff_query, 'isssdi', [$user_id, $employee_id, $designation, $join_date, $salary, get_user_id()]);
            }
            
            db_commit();
            log_activity('STAFF_UPDATED', 'users', $user_id, $staff, $_POST);
            set_flash('success', 'Staff profile updated successfully.');
            redirect('view.php?id=' . $user_id);
            
        } catch (Exception $e) {
            db_rollback();
            set_flash('error', 'Error updating staff: ' . $e->getMessage());
        }
    }
}

// Fetch roles for the dropdown
$roles = db_fetch_all("SELECT * FROM roles ORDER BY role_name ASC");

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <i class="icon-pencil"></i> Edit Staff Member Details
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <h6 class="text-uppercase text-primary border-bottom pb-2 mb-3">Login & Account Details</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" value="<?php echo clean($staff['full_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Username (System ID)</label>
                                    <input type="text" class="form-control" value="<?php echo clean($staff['username']); ?>" readonly disabled>
                                    <small class="text-muted">Username cannot be changed.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="<?php echo clean($staff['email']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="<?php echo clean($staff['phone']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Role</label>
                                    <select name="role_id" class="form-control">
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?php echo $role['role_id']; ?>" <?php echo ($staff['role_id'] == $role['role_id']) ? 'selected' : ''; ?>>
                                                <?php echo clean($role['role_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-center mt-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_active" class="custom-control-input" id="is_active_switch" <?php echo ($staff['is_active']) ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="is_active_switch">Active Account Status</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>New Password (Optional)</label>
                                    <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current">
                                    <small class="text-muted">Only if you want to reset their password.</small>
                                </div>
                            </div>

                            <h6 class="text-uppercase text-primary border-bottom pb-2 mt-4 mb-3">Professional & Payroll Details</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Employee ID</label>
                                    <input type="text" name="employee_id" class="form-control" value="<?php echo clean($staff['employee_id']); ?>" placeholder="e.g. EMP-101">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Designation</label>
                                    <input type="text" name="designation" class="form-control" value="<?php echo clean($staff['designation']); ?>" placeholder="e.g. Lifeguard, Manager">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Joining Date</label>
                                    <input type="date" name="join_date" class="form-control" value="<?php echo $staff['join_date']; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Monthly Salary</label>
                                    <input type="number" step="0.01" name="salary" class="form-control" value="<?php echo $staff['salary']; ?>">
                                </div>
                            </div>

                            <div class="form-group mt-5 text-center">
                                <button type="submit" class="btn btn-warning btn-lg px-5 shadow-sm">
                                    <i class="icon-check"></i> Update Staff Record
                                </button>
                                <a href="view.php?id=<?php echo $user_id; ?>" class="btn btn-secondary btn-lg px-5 ml-2">
                                    Cancel
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
