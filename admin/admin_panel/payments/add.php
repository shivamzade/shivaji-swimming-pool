<?php
/**
 * Add New Payment
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'Add New Payment';

// Handle form submission
if (is_post_request()) {
    require_csrf_token();
    
    // Validate required fields
    $required_fields = ['member_id', 'payment_type', 'amount', 'payment_mode', 'payment_date'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            set_flash('error', 'Please fill all required fields.');
            redirect(ADMIN_URL . '/payments/add.php');
        }
    }
    
    // Sanitize inputs
    $member_id = intval($_POST['member_id']);
    $payment_type = sanitize_input($_POST['payment_type']);
    $amount = floatval($_POST['amount']);
    $payment_mode = sanitize_input($_POST['payment_mode']);
    $payment_date = sanitize_input($_POST['payment_date']);
    $payment_for_month = !empty($_POST['payment_for_month']) ? sanitize_input($_POST['payment_for_month']) : null;
    $transaction_id = !empty($_POST['transaction_id']) ? sanitize_input($_POST['transaction_id']) : null;
    $remarks = !empty($_POST['remarks']) ? sanitize_input($_POST['remarks']) : null;
    
    // Generate receipt number
    $receipt_number = generate_receipt_number();
    
    // Get current user ID
    $created_by = $_SESSION['user_id'] ?? 1;
    
    // Insert payment
    $query = "INSERT INTO payments (member_id, payment_type, amount, payment_mode, receipt_number, 
              transaction_id, payment_date, payment_for_month, remarks, created_by) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [$member_id, $payment_type, $amount, $payment_mode, $receipt_number, 
               $transaction_id, $payment_date, $payment_for_month, $remarks, $created_by];
    $types = "iisssssssi";
    
    $result = db_query($query, $types, $params);
    
    if ($result) {
        set_flash('success', "Payment recorded successfully! Receipt Number: $receipt_number");
        redirect(ADMIN_URL . '/payments/view.php?id=' . db_insert_id());
    } else {
        set_flash('error', 'Failed to record payment. Please try again.');
    }
}

// Get members list for dropdown
$members_query = "SELECT member_id, first_name, last_name, member_code, status 
                  FROM members 
                  WHERE status = 'ACTIVE' 
                  ORDER BY first_name, last_name";
$members = db_fetch_all($members_query);

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
                    <div class="card-header bg-success text-white">
                        <i class="icon-credit-card"></i> Add New Payment
                        <div class="card-action">
                             <a href="index.php" class="btn btn-light btn-sm"><i class="icon-list"></i> Payment History</a>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <!-- Member Selection -->
                            <h5 class="border-bottom pb-2 mb-3">Member Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Select Member <span class="text-danger">*</span></label>
                                        <select name="member_id" class="form-control" required id="memberSelect">
                                            <option value="">Select Member</option>
                                            <?php foreach ($members as $member): ?>
                                                <option value="<?php echo $member['member_id']; ?>">
                                                    <?php echo clean($member['first_name'] . ' ' . $member['last_name']); ?> 
                                                    (<?php echo clean($member['member_code']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payment Type <span class="text-danger">*</span></label>
                                        <select name="payment_type" class="form-control" required id="paymentType">
                                            <option value="">Select Payment Type</option>
                                            <option value="REGISTRATION">Registration Fee</option>
                                            <option value="RENEWAL">Membership Renewal</option>
                                            <option value="FINE">Fine</option>
                                            <option value="OTHER">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Details -->
                            <h5 class="border-bottom pb-2 mb-3 mt-4">Payment Details</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Amount (₹) <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" class="form-control" 
                                               step="0.01" min="0" required id="amount">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Payment Method <span class="text-danger">*</span></label>
                                        <select name="payment_mode" class="form-control" required>
                                            <option value="">Select Method</option>
                                            <option value="CASH">Cash</option>
                                            <option value="UPI">UPI</option>
                                            <option value="CARD">Card</option>
                                            <option value="NET_BANKING">Net Banking</option>
                                            <option value="CHEQUE">Cheque</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="payment_date" class="form-control" 
                                               value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Payment For Month</label>
                                        <input type="month" name="payment_for_month" class="form-control" 
                                               placeholder="e.g., 2026-01 (for renewal)">
                                        <small class="form-text text-muted">For renewal payments only</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Transaction ID</label>
                                        <input type="text" name="transaction_id" class="form-control" 
                                               placeholder="For digital payments (optional)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="2" 
                                                  placeholder="Additional notes (optional)"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Preview Section -->
                            <div class="alert alert-info" id="paymentPreview" style="display: none;">
                                <h6><i class="icon-info"></i> Payment Preview</h6>
                                <div id="previewContent"></div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="icon-check"></i> Record Payment
                                </button>
                                <a href="<?php echo ADMIN_URL; ?>/payments/index.php" class="btn btn-secondary btn-lg">
                                    <i class="icon-close"></i> Cancel
                                </a>
                                <button type="button" class="btn btn-info btn-lg" onclick="previewPayment()">
                                    <i class="icon-eye"></i> Preview
                                </button>
                            </div>
                            
                            <div class="alert alert-warning mt-3">
                                <strong>Note:</strong> Receipt number will be generated automatically. 
                                Please verify all details before submitting.
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
function previewPayment() {
    const memberSelect = document.getElementById('memberSelect');
    const paymentType = document.getElementById('paymentType');
    const amount = document.getElementById('amount');
    const preview = document.getElementById('paymentPreview');
    const content = document.getElementById('previewContent');
    
    if (!memberSelect.value || !paymentType.value || !amount.value) {
        alert('Please select member, payment type, and enter amount to preview.');
        return;
    }
    
    const selectedOption = memberSelect.options[memberSelect.selectedIndex];
    const memberName = selectedOption.text;
    const paymentTypeName = paymentType.options[paymentType.selectedIndex].text;
    
    content.innerHTML = `
        <strong>Member:</strong> ${memberName}<br>
        <strong>Payment Type:</strong> ${paymentTypeName}<br>
        <strong>Amount:</strong> ₹${parseFloat(amount.value).toFixed(2)}<br>
        <strong>Receipt Number:</strong> Will be generated automatically
    `;
    
    preview.style.display = 'block';
}

// Auto-show payment for month field when renewal is selected
document.getElementById('paymentType').addEventListener('change', function() {
    const monthField = document.querySelector('input[name="payment_for_month"]').closest('.form-group');
    if (this.value === 'RENEWAL') {
        monthField.style.display = 'block';
        monthField.querySelector('input').setAttribute('required', 'required');
    } else {
        monthField.style.display = 'none';
        monthField.querySelector('input').removeAttribute('required');
    }
});
</script>

<?php
// Include footer
include_once '../../../includes/admin_footer.php';
?>
