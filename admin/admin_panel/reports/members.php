<?php
/**
 * Member Statistics & Demographics Report
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

$page_title = 'Member Reports';

// Statistics Overview
$stats = [
    'total' => db_fetch_one("SELECT COUNT(*) as c FROM members")['c'],
    'active' => db_fetch_one("SELECT COUNT(*) as c FROM members WHERE status = 'ACTIVE'")['c'],
    'expired' => db_fetch_one("SELECT COUNT(*) as c FROM members WHERE status = 'EXPIRED'")['c'],
    'expiring_soon' => db_fetch_one("SELECT COUNT(*) as c FROM members WHERE status = 'ACTIVE' AND membership_end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)")['c']
];

// Gender Breakdown
$gender_data = db_fetch_all("SELECT gender, COUNT(*) as count FROM members GROUP BY gender");

// Registration Trend (Last 6 months)
$trend_query = "SELECT DATE_FORMAT(registration_date, '%b %Y') as month, COUNT(*) as count 
                FROM members 
                WHERE registration_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY month 
                ORDER BY registration_date ASC";
$reg_trend = db_fetch_all($trend_query);

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-lg-3">
                <div class="card bg-primary shadow-sm text-white">
                    <div class="card-body text-center">
                        <h2 class="mb-0"><?php echo $stats['total']; ?></h2>
                        <p class="mb-0">Total Registrations</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card bg-success shadow-sm text-white">
                    <div class="card-body text-center">
                        <h2 class="mb-0"><?php echo $stats['active']; ?></h2>
                        <p class="mb-0">Active Members</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card bg-danger shadow-sm text-white">
                    <div class="card-body text-center">
                        <h2 class="mb-0"><?php echo $stats['expired']; ?></h2>
                        <p class="mb-0">Expired Members</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card bg-warning shadow-sm text-white">
                    <div class="card-body text-center">
                        <h2 class="mb-0"><?php echo $stats['expiring_soon']; ?></h2>
                        <p class="mb-0">Expiring in 7 Days</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Gender & Category Breakdown -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <i class="icon-pie-chart"></i> Gender Distribution
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Gender</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($gender_data as $row): 
                                        $percent = $stats['total'] > 0 ? round(($row['count'] / $stats['total']) * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><?php echo ucfirst($row['gender']); ?></td>
                                            <td><?php echo $row['count']; ?></td>
                                            <td>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar" style="width: <?php echo $percent; ?>%"></div>
                                                </div>
                                                <small><?php echo $percent; ?>%</small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Trends -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <i class="icon-graph"></i> New Registrations (Last 6 Months)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>New Members</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reg_trend as $row): ?>
                                        <tr>
                                            <td><?php echo $row['month']; ?></td>
                                            <td><span class="badge badge-primary px-3"><?php echo $row['count']; ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Needing Renewal Table -->
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <i class="icon-bulb"></i> Members Expiring Soon (Follow-up Required)
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Member</th>
                                        <th>Phone</th>
                                        <th>Expiry Date</th>
                                        <th>Days Left</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $exp_query = "SELECT *, DATEDIFF(membership_end_date, CURDATE()) as days_left 
                                                  FROM members 
                                                  WHERE status = 'ACTIVE' 
                                                  AND membership_end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
                                                  ORDER BY membership_end_date ASC";
                                    $exp_list = db_fetch_all($exp_query);
                                    
                                    if (empty($exp_list)):
                                    ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No members expiring within the next 14 days.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($exp_list as $mem): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo clean($mem['first_name'] . ' ' . $mem['last_name']); ?></strong><br>
                                                    <small><?php echo $mem['member_code']; ?></small>
                                                </td>
                                                <td><?php echo clean($mem['phone']); ?></td>
                                                <td><?php echo format_date($mem['membership_end_date']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo ($mem['days_left'] <= 3) ? 'badge-danger' : 'badge-warning'; ?>">
                                                        <?php echo $mem['days_left']; ?> days
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="../members/renew.php?id=<?php echo $mem['member_id']; ?>" class="btn btn-sm btn-outline-primary">Renew</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
