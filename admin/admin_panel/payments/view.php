<?php
/**
 * View Payment Receipt
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Check permissions
if (!is_logged_in()) {
    redirect(BASE_URL . '/admin/index.php');
}

$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch payment details along with member and staff info
$query = "SELECT p.*, m.first_name, m.last_name, m.member_code, m.phone, m.address_line1, m.city, m.pincode,
          u.full_name as collected_by
          FROM payments p
          JOIN members m ON p.member_id = m.member_id
          LEFT JOIN users u ON p.created_by = u.user_id
          WHERE p.payment_id = ?";

$pay = db_fetch_one($query, 'i', [$payment_id]);

if (!$pay) {
    set_flash('error', 'Payment record not found.');
    redirect('index.php');
}

$page_title = 'Receipt #' . $pay['receipt_number'];

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5" id="printableReceipt">
                        <!-- Receipt Header -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <h3 class="text-primary mb-0"><?php echo clean(get_setting('pool_name', 'Shivaji Swimming Pool')); ?></h3>
                                <p class="text-muted small">
                                    <?php echo clean(get_setting('pool_address', 'Shivaji Park, Mumbai')); ?><br>
                                    Phone: <?php echo clean(get_setting('pool_phone', '9876543210')); ?>
                                </p>
                            </div>
                            <div class="col-6 text-right">
                                <h4 class="text-uppercase font-weight-bold">Receipt</h4>
                                <p class="mb-0"><strong>Receipt No:</strong> <?php echo clean($pay['receipt_number']); ?></p>
                                <p class="mb-0"><strong>Date:</strong> <?php echo format_date($pay['payment_date']); ?></p>
                            </div>
                        </div>

                        <hr>

                        <!-- Member Info -->
                        <div class="row mb-5">
                            <div class="col-6">
                                <p class="text-muted small text-uppercase mb-1">Bill To:</p>
                                <h6 class="mb-0"><?php echo clean($pay['first_name'] . ' ' . $pay['last_name']); ?></h6>
                                <p class="text-muted small mb-0">Member Code: <?php echo clean($pay['member_code']); ?></p>
                                <p class="text-muted small mb-0">Phone: <?php echo clean($pay['phone']); ?></p>
                            </div>
                            <div class="col-6 text-right">
                                <p class="text-muted small text-uppercase mb-1">Payment Method:</p>
                                <h6 class="mb-0"><?php echo clean($pay['payment_mode']); ?></h6>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-right" style="width: 150px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>Membership Fees / Subscription</strong><br>
                                            <span class="text-muted small"><?php echo clean($pay['remarks'] ?: 'Pool membership access'); ?></span>
                                        </td>
                                        <td class="text-right"><?php echo format_currency($pay['amount']); ?></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-right">Total Amount:</th>
                                        <th class="text-right text-primary h5"><?php echo format_currency($pay['amount']); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-5 row">
                            <div class="col-6">
                                <p class="small text-muted mb-0 italic">This is a computer-generated receipt.</p>
                            </div>
                            <div class="col-6 text-right mt-4">
                                <div style="border-top: 1px solid #ddd; display: inline-block; width: 150px; padding-top: 5px;">
                                    <p class="small text-muted mb-0">Authorized Signatory</p>
                                </div><br>
                                <small>Handled by: <?php echo clean($pay['collected_by'] ?: 'System'); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light text-center no-print">
                        <button onclick="window.print()" class="btn btn-primary px-4">
                            <i class="icon-printer"></i> Print Receipt
                        </button>
                        <a href="index.php" class="btn btn-secondary px-4 ml-2">Back to History</a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    .content-wrapper { margin: 0 !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { background: white !important; }
}
</style>

<?php
// Include footer
include_once '../../../includes/admin_footer.php';
?>
