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
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get monthly data
$query = "SELECT p.*, m.first_name, m.last_name, m.member_code
          FROM payments p
          JOIN members m ON p.member_id = m.member_id
          WHERE MONTH(p.payment_date) = ? AND YEAR(p.payment_date) = ?
          ORDER BY p.payment_date ASC";

$transactions = db_fetch_all($query, 'ii', [$month, $year]);

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
    $mode = $t['payment_method'] ?? $t['payment_mode'] ?? 'UNKNOWN';
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
                            <div class="col-md-6">
                                <h5 class="mb-0"><i class="icon-chart"></i> Revenue Report: <?php echo date('F', mktime(0, 0, 0, $month, 10)) . ' ' . $year; ?></h5>
                            </div>
                            <div class="col-md-6 text-right d-print-none">
                                <form method="GET" class="form-inline justify-content-end">
                                    <select name="month" class="form-control form-control-sm mr-2">
                                        <?php for($i=1; $i<=12; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i == $month ? 'selected' : ''; ?>><?php echo date('F', mktime(0, 0, 0, $i, 10)); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <select name="year" class="form-control form-control-sm mr-2">
                                        <?php for($i=date('Y'); $i>=2023; $i--): ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i == $year ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-light">Filter</button>
                                    <button onclick="window.print()" class="btn btn-sm btn-light ml-1"><i class="icon-printer"></i></button>
                                </form>
                            </div>
                        </div>
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
                                                        <td><?php echo clean($t['first_name'] . ' ' . $t['last_name']); ?></td>
                                                        <td><span class="badge badge-light"><?php echo clean($t['payment_method'] ?? $t['payment_mode'] ?? 'N/A'); ?></span></td>
                                                        <td class="text-right"><strong><?php echo format_currency($t['amount']); ?></strong></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr class="bg-light font-weight-bold">
                                                    <td colspan="4" class="text-right">Total Monthly Revenue</td>
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
