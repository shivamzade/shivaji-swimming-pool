<?php
/**
 * System Settings Management
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Check permissions (Super Admin only)
if (!has_role(1)) {
    set_flash('error', 'Unauthorized access. Only Super Administrators can manage system settings.');
    redirect(ADMIN_URL . '/index.php');
}

$page_title = 'System Settings';

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    $settings_to_update = [
        'pool_name' => sanitize_input($_POST['pool_name']),
        'pool_phone' => sanitize_input($_POST['pool_phone']),
        'pool_email' => sanitize_input($_POST['pool_email']),
        'pool_address' => sanitize_input($_POST['pool_address']),
        'member_id_prefix' => sanitize_input($_POST['member_id_prefix']),
        'currency_symbol' => sanitize_input($_POST['currency_symbol']),
        'opening_time' => sanitize_input($_POST['opening_time']),
        'closing_time' => sanitize_input($_POST['closing_time']),
        'records_per_page' => intval($_POST['records_per_page']),
        'session_lifetime' => intval($_POST['session_lifetime'])
    ];
    
    $success_count = 0;
    foreach ($settings_to_update as $key => $value) {
        $query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
        if (db_query($query, 'ss', [$value, $key])) {
            $success_count++;
        }
    }
    
    if ($success_count > 0) {
        set_flash('success', 'System settings updated successfully.');
        log_activity('SETTINGS_UPDATED', 'settings', 0, null, ['count' => $success_count]);
        
        // Redirect to refresh constants
        redirect('index.php');
    } else {
        set_flash('error', 'No settings were updated.');
    }
}

// Fetch all settings
$settings_res = db_fetch_all("SELECT * FROM settings");
$current_settings = [];
foreach ($settings_res as $row) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
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
                    <div class="card-header bg-dark text-white">
                        <i class="icon-settings"></i> Global System Settings
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <div class="row">
                                <!-- General Information -->
                                <div class="col-lg-6">
                                    <h6 class="text-uppercase text-primary border-bottom pb-2 mb-3">General Information</h6>
                                    <div class="form-group mb-3">
                                        <label>Pool / Organization Name</label>
                                        <input type="text" name="pool_name" class="form-control" value="<?php echo clean($current_settings['pool_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Contact Phone</label>
                                        <input type="text" name="pool_phone" class="form-control" value="<?php echo clean($current_settings['pool_phone'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Contact Email</label>
                                        <input type="email" name="pool_email" class="form-control" value="<?php echo clean($current_settings['pool_email'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Address</label>
                                        <textarea name="pool_address" class="form-control" rows="3"><?php echo clean($current_settings['pool_address'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <!-- Application Configuration -->
                                <div class="col-lg-6">
                                    <h6 class="text-uppercase text-primary border-bottom pb-2 mb-3">System Configuration</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Member ID Prefix</label>
                                                <input type="text" name="member_id_prefix" class="form-control" value="<?php echo clean($current_settings['member_id_prefix'] ?? ''); ?>" placeholder="e.g. SPL">
                                                <small class="text-muted">Used for auto-generating IDs</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Currency Symbol</label>
                                                <input type="text" name="currency_symbol" class="form-control" value="<?php echo clean($current_settings['currency_symbol'] ?? ''); ?>" placeholder="e.g. ₹">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Opening Time</label>
                                                <input type="time" name="opening_time" class="form-control" value="<?php echo clean($current_settings['opening_time'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Closing Time</label>
                                                <input type="time" name="closing_time" class="form-control" value="<?php echo clean($current_settings['closing_time'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Records Per Page</label>
                                                <input type="number" name="records_per_page" class="form-control" value="<?php echo clean($current_settings['records_per_page'] ?? '25'); ?>">
                                                <small class="text-muted">Pagination limit for tables</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Session Lifetime (sec)</label>
                                                <input type="number" name="session_lifetime" class="form-control" value="<?php echo clean($current_settings['session_lifetime'] ?? '3600'); ?>">
                                                <small class="text-muted">Auto logout after inactivity</small>
                                            </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <!-- Additional settings can go here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="mt-4">
                            
                            <div class="form-group mt-4 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                    <i class="icon-check"></i> Save Application Settings
                                </button>
                                <a href="<?php echo ADMIN_URL; ?>/index.php" class="btn btn-secondary btn-lg px-5 ml-2">
                                    Cancel
                                </a>
                            </div>
                            
                            <div class="alert alert-info mt-4 mb-0">
                                <h6><i class="icon-info"></i> Note on Settings:</h6>
                                <p class="mb-0 small">Some settings may require a session refresh (logout/login) to take full effect across all components. Specifically, changes to the Pool Name and Currency Symbol are applied instantly on page reload.</p>
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
