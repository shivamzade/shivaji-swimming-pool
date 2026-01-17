<?php
/**
 * Edit Membership Plan
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Check if ID is provided
$plan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$plan_id) {
    set_flash('error', 'Plan ID is required');
    redirect('index.php');
}

// Get plan data
$plan = db_fetch_one("SELECT * FROM membership_plans WHERE plan_id = ?", 'i', [$plan_id]);

if (!$plan) {
    set_flash('error', 'Plan not found');
    redirect('index.php');
}

// Set page title
$page_title = 'Edit Membership Plan';

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    $plan_name = sanitize_input($_POST['plan_name'] ?? '');
    $plan_type = sanitize_input($_POST['plan_type'] ?? 'MONTHLY');
    $price = floatval($_POST['price'] ?? 0);
    $duration_days = intval($_POST['duration_days'] ?? 0);
    $description = sanitize_input($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    if (empty($plan_name) || $price <= 0 || $duration_days <= 0) {
        set_flash('error', 'Please fill all required fields and ensure values are valid.');
    } else {
        $query = "UPDATE membership_plans SET plan_name = ?, plan_type = ?, description = ?, price = ?, duration_days = ?, is_active = ? 
                  WHERE plan_id = ?";
        
        $result = db_query($query, 'sssdisi', [$plan_name, $plan_type, $description, $price, $duration_days, $is_active, $plan_id]);
        
        if ($result) {
            log_activity('PLAN_UPDATED', 'membership_plans', $plan_id, $plan, ['name' => $plan_name]);
            set_flash('success', 'Membership plan updated successfully.');
            redirect('index.php');
        } else {
            set_flash('error', 'Failed to update membership plan.');
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
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="icon-pencil"></i> Edit Membership Plan: <?php echo clean($plan['plan_name']); ?>
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Plan Name <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="plan_name" class="form-control" value="<?php echo clean($plan['plan_name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Plan Type <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="plan_type" class="form-control" required>
                                        <?php 
                                        $types = ['DAILY', 'WEEKLY', 'MONTHLY', 'QUARTERLY', 'HALFYEARLY', 'YEARLY'];
                                        foreach($types as $type): 
                                        ?>
                                            <option value="<?php echo $type; ?>" <?php echo $plan['plan_type'] == $type ? 'selected' : ''; ?>>
                                                <?php echo ucfirst(strtolower($type)); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Price (₹) <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $plan['price']; ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Duration (Days) <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" name="duration_days" class="form-control" value="<?php echo $plan['duration_days']; ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Description</label>
                                <div class="col-sm-9">
                                    <textarea name="description" class="form-control" rows="3"><?php echo clean($plan['description']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Active Status</label>
                                <div class="col-sm-9">
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" <?php echo $plan['is_active'] ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="is_active">Plan is available for sale</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-4">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary px-4">Update Plan</button>
                                    <a href="index.php" class="btn btn-secondary px-4">Cancel</a>
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
include_once '../../../includes/admin_footer.php';
?>
