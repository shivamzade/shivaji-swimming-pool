<?php
/**
 * Payment History
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'Payment History';

// Get filter parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$payment_for = isset($_GET['payment_for']) ? sanitize_input($_GET['payment_for']) : '';
$per_page = 25;
$offset = ($page - 1) * $per_page;

// Build query
$where = "WHERE 1=1";
$params = [];
$types = "";

if (!empty($payment_for)) {
    if ($payment_for === 'MEMBER') {
        $where .= " AND p.payment_for_type = 'MEMBER'";
    } elseif ($payment_for === 'GUEST') {
        $where .= " AND p.payment_for_type = 'GUEST'";
    }
}

if (!empty($search)) {
    $where .= " AND (p.receipt_number LIKE ? OR 
                     (p.payment_for_type = 'MEMBER' AND (m.first_name LIKE ? OR m.last_name LIKE ? OR m.member_code LIKE ?)) OR
                     (p.payment_for_type = 'GUEST' AND (g.first_name LIKE ? OR g.last_name LIKE ? OR g.guest_code LIKE ?)))";
    $st = "%$search%";
    array_push($params, $st, $st, $st, $st, $st, $st, $st);
    $types .= "sssssss";
}

// Get total count
$total_query = "SELECT COUNT(*) as total 
                FROM payments p 
                LEFT JOIN members m ON p.member_id = m.member_id 
                LEFT JOIN guests g ON p.guest_id = g.guest_id 
                $where";
$total_res = db_fetch_one($total_query, $types, $params);
$total_records = $total_res['total'] ?? 0;
$total_pages = ceil($total_records / $per_page);

// Get data
$data_query = "SELECT p.*, 
                      m.first_name as member_first_name, m.last_name as member_last_name, m.member_code,
                      g.first_name as guest_first_name, g.last_name as guest_last_name, g.guest_code
               FROM payments p 
               LEFT JOIN members m ON p.member_id = m.member_id 
               LEFT JOIN guests g ON p.guest_id = g.guest_id 
               $where 
               ORDER BY p.payment_date DESC, p.created_at DESC 
               LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";

$payments = db_fetch_all($data_query, $types, $params);

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
                        <h5 class="mb-0"><i class="icon-wallet"></i> Payment History</h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Search and Filter -->
                        <form method="GET" action="" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search by receipt #, name or code..." value="<?php echo clean($search); ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit"><i class="icon-magnifier"></i> Search</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="payment_for" class="form-control">
                                        <option value="">All Payments</option>
                                        <option value="MEMBER" <?php echo $payment_for === 'MEMBER' ? 'selected' : ''; ?>>Member Payments</option>
                                        <option value="GUEST" <?php echo $payment_for === 'GUEST' ? 'selected' : ''; ?>>Guest Payments</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-outline-primary">Filter</button>
                                    <a href="index.php" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Date</th>
                                        <th>Payment For</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Mode</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($payments)): ?>
                                        <tr><td colspan="8" class="text-center py-4">No payment records found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($payments as $pay): ?>
                                            <tr>
                                                <td><strong><?php echo clean($pay['receipt_number']); ?></strong></td>
                                                <td><?php echo format_date($pay['payment_date']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $pay['payment_for_type'] === 'MEMBER' ? 'primary' : 'info'; ?>">
                                                        <?php echo clean($pay['payment_for_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($pay['payment_for_type'] === 'MEMBER'): ?>
                                                        <a href="<?php echo ADMIN_URL; ?>/members/view.php?id=<?php echo $pay['member_id']; ?>">
                                                            <?php echo clean($pay['member_first_name'] . ' ' . $pay['member_last_name']); ?>
                                                        </a><br>
                                                        <small class="text-muted"><?php echo clean($pay['member_code']); ?></small>
                                                    <?php else: ?>
                                                        <a href="<?php echo ADMIN_URL; ?>/guests/view.php?id=<?php echo $pay['guest_id']; ?>">
                                                            <?php echo clean($pay['guest_first_name'] . ' ' . $pay['guest_last_name']); ?>
                                                        </a><br>
                                                        <small class="text-muted"><?php echo clean($pay['guest_code']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light"><?php echo clean($pay['payment_type']); ?></span>
                                                </td>
                                                <td><strong><?php echo format_currency($pay['amount']); ?></strong></td>
                                                <td><span class="badge badge-light"><?php echo clean($pay['payment_method']); ?></span></td>
                                                <td>
                                                    <a href="view.php?id=<?php echo $pay['payment_id']; ?>" 
                                                       class="btn btn-sm btn-info" title="View Receipt">
                                                        <i class="icon-eye"></i>
                                                    </a>
                                                    <button onclick="window.print()" class="btn btn-sm btn-secondary" title="Print Receipt">
                                                        <i class="icon-printer"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Page Numbers -->
                        <?php if ($total_pages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php for($i=1; $i<=$total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                        
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
