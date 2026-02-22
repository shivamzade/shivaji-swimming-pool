<?php
/**
 * Revenue Report
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
$page_title = 'Revenue Report';

// Get filter parameters
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$payment_method = $_GET['payment_method'] ?? '';
$plan_type = $_GET['plan_type'] ?? '';
$search = $_GET['search'] ?? '';

// Fallback to current month if no date range is provided
if (empty($start_date) && empty($end_date)) {
    $month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
    $start_date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
    $end_date = date("Y-m-t", strtotime($start_date));
}

// Build query
$query = "SELECT p.*, m.first_name, m.last_name, m.member_code, mp.plan_name, mp.plan_type
          FROM payments p
          JOIN members m ON p.member_id = m.member_id
          LEFT JOIN member_memberships mm ON p.payment_id = mm.payment_id
          LEFT JOIN membership_plans mp ON mm.plan_id = mp.plan_id
          WHERE 1=1";

$params = [];
$types = "";

if (!empty($start_date)) {
    $query .= " AND p.payment_date >= ?";
    $params[] = $start_date;
    $types .= "s";
}

if (!empty($end_date)) {
    $query .= " AND p.payment_date <= ?";
    $params[] = $end_date;
    $types .= "s";
}

if (!empty($payment_method)) {
    $query .= " AND p.payment_method = ?";
    $params[] = $payment_method;
    $types .= "s";
}

if (!empty($plan_type)) {
    $query .= " AND mp.plan_type = ?";
    $params[] = $plan_type;
    $types .= "s";
}

if (!empty($search)) {
    $query .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR m.member_code LIKE ? OR p.receipt_number LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ssss";
}

$query .= " ORDER BY p.payment_date ASC";

$transactions = db_fetch_all($query, $types, $params);

// Calculate totals
$total_revenue = 0;
$mode_breakdown = [
    'CASH' => 0,
    'UPI' => 0,
    'CARD' => 0,
    'NET_BANKING' => 0,
    'CHEQUE' => 0,
    'BANK_TRANSFER' => 0
];

foreach ($transactions as $t) {
    $total_revenue += $t['amount'];
    $mode = $t['payment_method'] ?? 'UNKNOWN';
    if (isset($mode_breakdown[$mode])) {
        $mode_breakdown[$mode] += $t['amount'];
    }
}

// Get monthly comparison for chart (last 6 months)
$chart_data = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('m', strtotime("-$i months"));
    $y = date('Y', strtotime("-$i months"));
    $name = date('M Y', strtotime("-$i months"));
    
    $res = db_fetch_one("SELECT SUM(amount) as total FROM payments WHERE MONTH(payment_date) = ? AND YEAR(payment_date) = ?", 'ii', [$m, $y]);
    $chart_data[] = [
        'name' => $name,
        'total' => $res['total'] ?? 0
    ];
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
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h5 class="mb-0"><i class="icon-chart"></i> Revenue Report</h5>
                                <small class="text-white-50">
                                    <?php 
                                    if (!empty($_GET['start_date']) || !empty($_GET['end_date'])) {
                                        echo "From: " . format_date($start_date) . " To: " . format_date($end_date);
                                    } else {
                                        echo date('F', mktime(0, 0, 0, $month, 10)) . ' ' . $year;
                                    }
                                    ?>
                                </small>
                            </div>
                            <div class="col-md-8 text-right d-print-none">
                                <form method="GET" class="form-inline justify-content-end">
                                    <div class="input-group input-group-sm mr-2" title="Start Date">
                                        <div class="input-group-prepend"><span class="input-group-text">From</span></div>
                                        <input type="date" name="start_date" class="form-control" value="<?php echo $_GET['start_date'] ?? ($start_date ?? ''); ?>">
                                    </div>
                                    <div class="input-group input-group-sm mr-2" title="End Date">
                                        <div class="input-group-prepend"><span class="input-group-text">To</span></div>
                                        <input type="date" name="end_date" class="form-control" value="<?php echo $_GET['end_date'] ?? ($end_date ?? ''); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-light">Filter</button>
                                    <a href="revenue.php" class="btn btn-sm btn-outline-light ml-1" title="Reset Filters"><i class="icon-refresh"></i></a>
                                    <button onclick="window.print()" class="btn btn-sm btn-light ml-1"><i class="icon-printer"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Filters Bar -->
                    <div class="card-body bg-light border-bottom d-print-none">
                        <form method="GET" class="row">
                            <!-- Preserve date filters if they were set via the header form -->
                            <input type="hidden" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>">
                            <input type="hidden" name="end_date" value="<?php echo $_GET['end_date'] ?? ''; ?>">
                            
                            <div class="col-md-3 mt-2">
                                <label class="small font-weight-bold">Payment Method</label>
                                <select name="payment_method" class="form-control form-control-sm">
                                    <option value="">All Methods</option>
                                    <?php foreach (array_keys($mode_breakdown) as $mode): ?>
                                        <option value="<?php echo $mode; ?>" <?php echo $payment_method == $mode ? 'selected' : ''; ?>><?php echo $mode; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="small font-weight-bold">Plan Type</label>
                                <select name="plan_type" class="form-control form-control-sm">
                                    <option value="">All Types</option>
                                    <option value="DAILY" <?php echo $plan_type == 'DAILY' ? 'selected' : ''; ?>>Daily</option>
                                    <option value="MONTHLY" <?php echo $plan_type == 'MONTHLY' ? 'selected' : ''; ?>>Monthly</option>
                                    <option value="QUARTERLY" <?php echo $plan_type == 'QUARTERLY' ? 'selected' : ''; ?>>Quarterly</option>
                                    <option value="HALF_YEARLY" <?php echo $plan_type == 'HALF_YEARLY' ? 'selected' : ''; ?>>Half Yearly</option>
                                    <option value="YEARLY" <?php echo $plan_type == 'YEARLY' ? 'selected' : ''; ?>>Yearly</option>
                                </select>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label class="small font-weight-bold">Search Member/Receipt</label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, code or receipt..." value="<?php echo clean($search); ?>">
                            </div>
                            <div class="col-md-2 mt-2">
                                <label class="small d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-primary btn-block">Apply Filters</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        
                        <!-- Mini Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="bg-primary text-white p-3 rounded text-center shadow-sm">
                                    <h6 class="text-uppercase small">Total Revenue</h6>
                                    <h3><?php echo format_currency($total_revenue); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-success text-white p-3 rounded text-center shadow-sm">
                                    <h6 class="text-uppercase small">Cash Collections</h6>
                                    <h3><?php echo format_currency($mode_breakdown['CASH']); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-info text-white p-3 rounded text-center shadow-sm">
                                    <h6 class="text-uppercase small">UPI / Online</h6>
                                    <h3><?php echo format_currency($mode_breakdown['UPI']); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-dark text-white p-3 rounded text-center shadow-sm">
                                    <h6 class="text-uppercase small">Transactions</h6>
                                    <h3><?php echo count($transactions); ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Main Grid -->
                        <div class="row">
                            <!-- Transactions Table -->
                            <div class="col-lg-8">
                                <h6 class="mb-3 text-uppercase font-weight-bold">Transaction Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Receipt #</th>
                                                <th>Member</th>
                                                <th>Plan</th>
                                                <th>Mode</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($transactions)): ?>
                                                <tr><td colspan="5" class="text-center py-4">No transactions recorded for this period.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($transactions as $t): ?>
                                                    <tr>
                                                        <td><?php echo format_date($t['payment_date']); ?></td>
                                                        <td><small><?php echo clean($t['receipt_number']); ?></small></td>
                                                        <td>
                                                            <div><?php echo clean($t['first_name'] . ' ' . $t['last_name']); ?></div>
                                                            <small class="text-muted"><?php echo clean($t['member_code']); ?></small>
                                                        </td>
                                                        <td>
                                                            <?php if ($t['plan_name']): ?>
                                                                <small class="d-block text-truncate" style="max-width: 120px;" title="<?php echo clean($t['plan_name']); ?>">
                                                                    <?php echo clean($t['plan_name']); ?>
                                                                </small>
                                                                <span class="badge badge-info" style="font-size: 70%;"><?php echo clean($t['plan_type']); ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted small">N/A</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><span class="badge badge-light"><?php echo clean($t['payment_method'] ?? 'N/A'); ?></span></td>
                                                        <td class="text-right"><strong><?php echo format_currency($t['amount']); ?></strong></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr class="bg-light font-weight-bold">
                                                    <td colspan="5" class="text-right">Total Revenue</td>
                                                    <td class="text-right text-primary"><?php echo format_currency($total_revenue); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Trends & Charts -->
                            <div class="col-lg-4">
                                <h6 class="mb-3 text-uppercase font-weight-bold">Last 6 Months Trend</h6>
                                <div class="list-group shadow-sm">
                                    <?php foreach ($chart_data as $data): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <?php echo $data['name']; ?>
                                            <span class="font-weight-bold"><?php echo format_currency($data['total']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="card mt-4 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-uppercase small font-weight-bold mb-3">Revenue by Mode</h6>
                                        <?php foreach ($mode_breakdown as $mode => $amt): 
                                            $percent = $total_revenue > 0 ? ($amt / $total_revenue) * 100 : 0;
                                        ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><?php echo $mode; ?></span>
                                                    <span><?php echo round($percent); ?>%</span>
                                                </div>
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-primary" style="width: <?php echo $percent; ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

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
