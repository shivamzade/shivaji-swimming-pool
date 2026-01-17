<?php
/**
 * Add New Staff Member
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

// Set page title
$page_title = 'Add Staff Member';

// Get roles except Super Admin
$roles = db_fetch_all("SELECT * FROM roles WHERE role_id != 1");

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    // User data
    $full_name = sanitize_input($_POST['full_name']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $role_id = intval($_POST['role_id']);
    $phone = sanitize_input($_POST['phone']);
    
    // Staff details
    $employee_id = sanitize_input($_POST['employee_id']);
    $designation = sanitize_input($_POST['designation']);
    $join_date = $_POST['join_date'] ?: date('Y-m-d');
    $salary = floatval($_POST['salary'] ?: 0);
    
    // Validation
    if (empty($full_name) || empty($username) || empty($password) || empty($role_id)) {
        set_flash('error', 'Please fill all required fields.');
    } else {
        // Check if username/email exists
        $check = db_fetch_one("SELECT user_id FROM users WHERE username = ? OR email = ?", 'ss', [$username, $email]);
        
        if ($check) {
            set_flash('error', 'Username or Email already exists.');
        } else {
            db_begin_transaction();
            
            try {
                // 1. Create User
                $pass_hash = hash_password($password);
                $query_u = "INSERT INTO users (username, email, password_hash, full_name, role_id, phone, is_active, created_by)
                            VALUES (?, ?, ?, ?, ?, ?, 1, ?)";
                
                db_query($query_u, 'ssssisi', [$username, $email, $pass_hash, $full_name, $role_id, $phone, get_user_id()]);
                $user_id = db_insert_id();
                
                // 2. Create Staff Entry
                $query_s = "INSERT INTO staff (user_id, employee_id, designation, join_date, salary, created_by)
                            VALUES (?, ?, ?, ?, ?, ?)";
                
                db_query($query_s, 'isssdi', [$user_id, $employee_id, $designation, $join_date, $salary, get_user_id()]);
                
                db_commit();
                log_activity('STAFF_CREATED', 'users', $user_id, null, ['name' => $full_name]);
                set_flash('success', 'Staff member added successfully.');
                redirect('index.php');
                
            } catch (Exception $e) {
                db_rollback();
                set_flash('error', 'Failed to add staff: ' . $e->getMessage());
            }
        }
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
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="icon-user-follow"></i> Add New Staff Member
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3">Login Credentials</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Username <span class="text-danger">*</span></label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email ID <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>System Role <span class="text-danger">*</span></label>
                                        <select name="role_id" class="form-control" required>
                                            <option value="">Select Role</option>
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?php echo $role['role_id']; ?>"><?php echo clean($role['role_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3 mt-4">Personal & Employment Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Employee ID / Code</label>
                                        <input type="text" name="employee_id" class="form-control" placeholder="e.g. EMP001">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Designation</label>
                                        <input type="text" name="designation" class="form-control" placeholder="e.g. Lifeguard">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Joining Date</label>
                                        <input type="date" name="join_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Monthly Salary (₹)</label>
                                        <input type="number" name="salary" class="form-control" step="0.01">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-5">
                                <button type="submit" class="btn btn-primary px-5">Save Staff Member</button>
                                <a href="index.php" class="btn btn-secondary px-5">Cancel</a>
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
