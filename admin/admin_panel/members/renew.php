<?php
/**
 * Member Membership Renewal / Assignment
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

// Get available plans
$plans = db_fetch_all("SELECT * FROM membership_plans WHERE is_active = 1 ORDER BY price ASC");

// Set page title
$page_title = 'Membership Renewal: ' . $member['member_code'];

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    $plan_id = intval($_POST['plan_id'] ?? 0);
    $payment_mode = sanitize_input($_POST['payment_mode'] ?? 'CASH');
    $start_date = sanitize_input($_POST['start_date'] ?? date('Y-m-d'));
    
    // Get plan details
    $plan = db_fetch_one("SELECT * FROM membership_plans WHERE plan_id = ?", 'i', [$plan_id]);
    
    if (!$plan) {
        set_flash('error', 'Invalid membership plan selected');
    } else {
        $amount = $plan['price'];
        
        // Begin Transaction
        db_begin_transaction();
        
        try {
            // 1. Create Payment Record
            $receipt_number = 'RCP-' . date('Ymd') . '-' . rand(1000, 9999);
            $pay_query = "INSERT INTO payments (member_id, amount, payment_date, payment_mode, receipt_number, status, created_by)
                          VALUES (?, ?, CURDATE(), ?, ?, 'SUCCESS', ?)";
            
            db_query($pay_query, 'idssi', [
                $member_id,
                $amount,
                $payment_mode,
                $receipt_number,
                get_user_id()
            ]);
            
            $payment_id = db_insert_id();
            
            // 2. Assign Membership
            if (Member::assign_membership($member_id, $plan_id, $start_date, $amount, $payment_id)) {
                db_commit();
                set_flash('success', 'Membership renewed successfully! Receipt: ' . $receipt_number);
                redirect(ADMIN_URL . '/members/view.php?id=' . $member_id);
            } else {
                throw new Exception('Failed to assign membership');
            }
            
        } catch (Exception $e) {
            db_rollback();
            set_flash('error', 'Operation failed: ' . $e->getMessage());
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
                    <div class="card-header bg-success text-white">
                        <i class="icon-refresh"></i> Membership Plan Assignment / Renewal
                    </div>
                    <div class="card-body">
                        
                        <!-- Member Summary -->
                        <div class="alert alert-light border-success mb-4">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Member:</strong> <?php echo clean($member['first_name'] . ' ' . $member['last_name']); ?> (<?php echo clean($member['member_code']); ?>)
                                </div>
                                <div class="col-sm-6 text-right">
                                    <strong>Current Expiry:</strong> 
                                    <span class="<?php echo ($member['status'] == 'EXPIRED') ? 'text-danger' : 'text-success'; ?>">
                                        <?php echo $member['membership_end_date'] ? format_date($member['membership_end_date']) : 'None'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Select Plan <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <?php foreach($plans as $plan): ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="card plan-card h-100 border p-2 cursor-pointer" onclick="document.getElementById('plan_<?php echo $plan['plan_id']; ?>').checked = true;">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="plan_<?php echo $plan['plan_id']; ?>" name="plan_id" class="custom-control-input" value="<?php echo $plan['plan_id']; ?>" required>
                                                        <label class="custom-control-label font-weight-bold" for="plan_<?php echo $plan['plan_id']; ?>">
                                                            <?php echo clean($plan['plan_name']); ?>
                                                        </label>
                                                    </div>
                                                    <div class="pl-4 mt-1">
                                                        <h5 class="text-primary mb-0"><?php echo format_currency($plan['price']); ?></h5>
                                                        <small class="text-muted"><?php echo $plan['duration_days']; ?> Days Validity</small>
                                                        <div class="mt-1 small"><?php echo clean($plan['description']); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-4">
                                <label class="col-sm-3 col-form-label">Start Date <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <?php 
                                    // Default start date is tomorrow if current membership is active, else today
                                    $default_start = date('Y-m-d');
                                    if ($member['membership_end_date'] && strtotime($member['membership_end_date']) > time()) {
                                        $default_start = date('Y-m-d', strtotime($member['membership_end_date'] . ' + 1 day'));
                                    }
                                    ?>
                                    <input type="date" name="start_date" class="form-control" value="<?php echo $default_start; ?>" required>
                                    <small class="text-muted">Membership will automatically expire based on the selected plan's duration.</small>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-4">
                                <label class="col-sm-3 col-form-label">Payment Mode <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="payment_mode" class="form-control" required>
                                        <option value="CASH">Cash</option>
                                        <option value="UPI">UPI / QR Code</option>
                                        <option value="CARD">Credit / Debit Card</option>
                                        <option value="BANK_TRANSFER">Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row mt-5">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="icon-refresh"></i> Confirm Renewal & Payment
                                    </button>
                                    <a href="view.php?id=<?php echo $member_id; ?>" class="btn btn-secondary btn-lg ml-2">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
.plan-card:hover { border-color: #007bff !important; background-color: #f8f9fa; }
.custom-control-input:checked ~ .plan-card { border-color: #28a745 !important; background-color: #f0fff4; }
.cursor-pointer { cursor: pointer; }
</style>

<?php
// Include footer
include_once '../../../includes/admin_footer.php';
?>
