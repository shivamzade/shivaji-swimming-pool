<?php
/**
 * Add New Membership Plan
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'Add Membership Plan';

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
        $query = "INSERT INTO membership_plans (plan_name, plan_type, description, price, duration_days, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $result = db_query($query, 'sssdis', [$plan_name, $plan_type, $description, $price, $duration_days, $is_active]);
        
        if ($result) {
            log_activity('PLAN_CREATED', 'membership_plans', db_insert_id(), null, ['name' => $plan_name]);
            set_flash('success', 'Membership plan created successfully.');
            redirect('index.php');
        } else {
            set_flash('error', 'Failed to create membership plan.');
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
                        <i class="icon-plus"></i> Add New Membership Plan
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Plan Name <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="plan_name" class="form-control" placeholder="e.g. Monthly Silver" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Plan Type <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="plan_type" class="form-control" required>
                                        <option value="DAILY">Daily (Guest)</option>
                                        <option value="WEEKLY">Weekly</option>
                                        <option value="MONTHLY" selected>Monthly</option>
                                        <option value="QUARTERLY">Quarterly</option>
                                        <option value="HALFYEARLY">Half Yearly</option>
                                        <option value="YEARLY">Yearly</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Price (₹) <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" name="price" class="form-control" step="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Duration (Days) <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" name="duration_days" class="form-control" placeholder="e.g. 30" required>
                                    <small class="text-muted">Membership will automatically expire after these many days.</small>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Description</label>
                                <div class="col-sm-9">
                                    <textarea name="description" class="form-control" rows="3" placeholder="Brief details about the plan..."></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-3">
                                <label class="col-sm-3 col-form-label">Active Status</label>
                                <div class="col-sm-9">
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" checked>
                                        <label class="custom-control-label" for="is_active">Plan is available for sale</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-4">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary px-4">Save Plan</button>
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
