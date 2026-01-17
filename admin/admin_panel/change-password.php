<?php
/**
 * Change Password Page
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

$user_id = get_user_id();
$page_title = 'Change Password';

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        set_flash('error', 'All fields are required.');
    } elseif ($new_password !== $confirm_password) {
        set_flash('error', 'New password and confirm password do not match.');
    } elseif (strlen($new_password) < 6) {
        set_flash('error', 'New password must be at least 6 characters long.');
    } else {
        // Verify current password
        $user = db_fetch_one("SELECT password_hash FROM users WHERE user_id = ?", 'i', [$user_id]);
        
        if (!verify_password($current_password, $user['password_hash'])) {
            set_flash('error', 'Incorrect current password.');
        } else {
            // Update password
            $new_hash = hash_password($new_password);
            $query = "UPDATE users SET password_hash = ? WHERE user_id = ?";
            $result = db_query($query, 'si', [$new_hash, $user_id]);
            
            if ($result) {
                log_activity('PASSWORD_CHANGED', 'users', $user_id);
                set_flash('success', 'Password changed successfully.');
                redirect('profile.php');
            } else {
                set_flash('error', 'Failed to change password. Please try again.');
            }
        }
    }
}

// Include header
include_once '../../includes/admin_header.php';
include_once '../../includes/admin_sidebar.php';
include_once '../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-lg-6 mx-auto">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="icon-lock"></i> Change Security Password
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Current Password <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-4 col-form-label">New Password <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" name="new_password" class="form-control" required minlength="6">
                                    <small class="text-muted">Minimum 6 characters.</small>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-4 col-form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                                </div>
                            </div>
                            
                            <div class=" row ">
                                <div class="">
                                    
                                    <a href="profile.php" class="btn btn-secondary px-5 ml-2">Cancel</a>
                                    
                                </div>
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-warning px-5">Update Password</button>
                                </div>

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
include_once '../../includes/admin_footer.php';
?>
