<?php
/**
 * User Profile Page
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../config/config.php';
require_once '../../db_connect.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect(BASE_URL . '/admin/index.php');
}

// Prevent browser caching/back-forward cache from showing stale form values
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$user_id = get_user_id();
$page_title = 'My Profile';

// Get current user data (used for display and for audit old_value)
$profile_user = db_fetch_one("SELECT u.*, r.role_name 
                              FROM users u 
                              JOIN roles r ON u.role_id = r.role_id 
                              WHERE u.user_id = ?", 'i', [$user_id]);

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    
    // Simple validation
    if (empty($full_name) || empty($email)) {
        set_flash('error', 'Full name and email are required.');
    } elseif (!is_valid_email($email)) {
        set_flash('error', 'Invalid email format.');
    } else {
        // Check if email is already used by another user
        $check = db_fetch_one("SELECT user_id FROM users WHERE email = ? AND user_id != ?", 'si', [$email, $user_id]);
        
        if ($check) {
            set_flash('error', 'This email is already registered with another account.');
        } else {
            $query = "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?";
            $result = db_query($query, 'sssi', [$full_name, $email, $phone, $user_id]);
            
            if ($result) {
                // Update session info if needed
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email;
                
                log_activity('PROFILE_UPDATED', 'users', $user_id, $profile_user, ['full_name' => $full_name, 'email' => $email, 'phone' => $phone]);
                set_flash('success', 'Profile updated successfully.');

                // Redirect to prevent form resubmission and force fresh DB fetch
                redirect(BASE_URL . '/admin/admin_panel/profile.php', 303);
            } else {
                set_flash('error', 'Failed to update profile.');
            }
        }
    }
}

// Get current user data (fresh fetch for GET after PRG redirect)
$profile_user = db_fetch_one("SELECT u.*, r.role_name 
                              FROM users u 
                              JOIN roles r ON u.role_id = r.role_id 
                              WHERE u.user_id = ?", 'i', [$user_id]);

// Include header
include_once '../../includes/admin_header.php';
include_once '../../includes/admin_sidebar.php';
include_once '../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-lg-4">
                <div class="card profile-card-2">
                    <div class="card-img-block p-4 text-center">
                        <div class="rounded-circle bg-light d-inline-block shadow-sm" style="width: 120px; height: 120px; line-height: 120px;">
                            <i class="icon-user fa-4x text-primary" style="vertical-align: middle;"></i>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        <h5 class="card-title text-center"><?php echo clean($profile_user['full_name'] ?? ''); ?></h5>
                        <p class="card-text text-center text-muted"><?php echo clean($profile_user['username'] ?? ''); ?></p>
                        <hr>
                        <div class="row">
                            <div class="col-5"><strong>Role:</strong></div>
                            <div class="col-7 text-primary"><?php echo clean($profile_user['role_name'] ?? ''); ?></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-5"><strong>Email:</strong></div>
                            <div class="col-7 small"><?php echo clean($profile_user['email'] ?? ''); ?></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-5"><strong>Phone:</strong></div>
                            <div class="col-7"><?php echo clean($profile_user['phone'] ?? '') ?: 'N/A'; ?></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-5"><strong>Joined:</strong></div>
                            <div class="col-7"><?php echo format_date($profile_user['created_at'] ?? null); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="icon-user"></i> Update Personal Information
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="" autocomplete="off">
                            <?php echo csrf_token_field(); ?>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Username</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo clean($profile_user['username'] ?? ''); ?>" readonly disabled>
                                    <small class="text-muted">Username cannot be changed.</small>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Full Name <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="full_name" class="form-control" value="<?php echo clean($profile_user['full_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Email ID <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" class="form-control" value="<?php echo clean($profile_user['email'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Phone Number</label>
                                <div class="col-sm-9">
                                    <input type="tel" name="phone" class="form-control" value="<?php echo clean($profile_user['phone'] ?? ''); ?>" autocomplete="tel" inputmode="tel">
                                </div>
                            </div>
                            
                            <div class="form-group row mt-5">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                                    <a href="index.php" class="btn btn-secondary px-5 ml-2">Back to Dashboard</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-3 border-danger">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-danger">Security Warning</h6>
                            <p class="mb-0 small text-muted">Keep your login credentials private. Do not share your password with anyone.</p>
                        </div>
                        <a href="change-password.php" class="btn btn-outline-danger btn-sm">Change Password</a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php
// Include footer
include_once '../../includes/admin_footer.php';
?>
