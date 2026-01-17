<?php
/**
 * Advanced Attendance Analytics Report
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

$page_title = 'Attendance Analytics';

// Filtering
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// 1. Daily Entries Trend for the Selected Month
$trend_query = "SELECT DAY(attendance_date) as day, COUNT(*) as count 
                FROM attendance 
                WHERE MONTH(attendance_date) = ? AND YEAR(attendance_date) = ? 
                GROUP BY day ORDER BY day ASC";
$daily_trend = db_fetch_all($trend_query, 'ii', [$month, $year]);

// 2. Attendance by Hour of Day (Peak Hours)
$peak_query = "SELECT HOUR(entry_time) as hr, COUNT(*) as count 
               FROM attendance 
               GROUP BY hr ORDER BY hr ASC";
$peak_hours = db_fetch_all($peak_query);

// 3. Average Duration
$avg_duration = db_fetch_one("SELECT AVG(duration_minutes) as avg_min FROM attendance WHERE duration_minutes IS NOT NULL")['avg_min'];

// 4. Most Frequent Visitors
$freq_query = "SELECT m.member_code, m.first_name, m.last_name, COUNT(a.attendance_id) as visits 
               FROM attendance a 
               JOIN members m ON a.member_id = m.member_id 
               WHERE a.attendance_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()
               GROUP BY m.member_id 
               ORDER BY visits DESC 
               LIMIT 5";
$top_visitors = db_fetch_all($freq_query);

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="icon-speedometer"></i> Attendance Analytics</h5>
                        <form class="form-inline">
                            <select name="month" class="form-control form-control-sm mr-1">
                                <?php for($m=1; $m<=12; $m++): ?>
                                    <option value="<?php echo $m; ?>" <?php echo ($month == $m) ? 'selected' : ''; ?>><?php echo date('F', mktime(0,0,0,$m,1)); ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="year" class="form-control form-control-sm mr-2">
                                <?php for($y=date('Y'); $y>=date('Y')-2; $y--): ?>
                                    <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                            <button type="submit" class="btn btn-light btn-sm">Load</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Peak Hours Table -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Peak Hours (All Time)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Hour</th>
                                        <th>Visitors</th>
                                        <th>Density</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $max_peak = empty($peak_hours) ? 1 : max(array_column($peak_hours, 'count'));
                                    foreach ($peak_hours as $row): 
                                        $h = $row['hr'];
                                        $display_h = ($h == 0) ? '12 AM' : (($h < 12) ? $h.' AM' : (($h == 12) ? '12 PM' : ($h-12).' PM'));
                                        $percent = ($row['count'] / $max_peak) * 100;
                                    ?>
                                        <tr>
                                            <td><?php echo $display_h; ?></td>
                                            <td><?php echo $row['count']; ?></td>
                                            <td>
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-info" style="width: <?php echo $percent; ?>%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Trend in Month -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light text-dark">
                        Monthly Trend: Daily Entries (<?php echo date('F Y', mktime(0,0,0,$month,1,$year)); ?>)
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4">Daily entry counts for each day of the selected month.</p>
                        <div class="table-responsive">
                           <table class="table table-sm table-striped">
                               <thead>
                                   <tr>
                                       <?php foreach ($daily_trend as $row): ?>
                                           <th class="text-center small"><?php echo $row['day']; ?></th>
                                       <?php endforeach; ?>
                                   </tr>
                               </thead>
                               <tbody>
                                   <tr>
                                       <?php foreach ($daily_trend as $row): ?>
                                           <td class="text-center font-weight-bold"><?php echo $row['count']; ?></td>
                                       <?php endforeach; ?>
                                   </tr>
                               </tbody>
                           </table>
                        </div>
                        <div class="mt-4 p-3 bg-light rounded shadow-sm border-left border-primary">
                            <h6 class="text-primary mb-1">Observation:</h6>
                            <p class="mb-0 small">The average duration per visit is <strong><?php echo round($avg_duration, 1); ?> minutes</strong>.
                            Total entries for this month: <strong><?php echo array_sum(array_column($daily_trend, 'count')); ?></strong>.</p>
                        </div>
                    </div>
                </div>

                <!-- Top Frequent Visitors -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-light">
                        <i class="icon-star"></i> Top 5 Frequent Visitors (Last 30 Days)
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Total Visits</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_visitors as $visitor): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo clean($visitor['first_name'] . ' ' . $visitor['last_name']); ?></strong><br>
                                            <small><?php echo $visitor['member_code']; ?></small>
                                        </td>
                                        <td><span class="badge badge-pill badge-warning px-3"><?php echo $visitor['visits']; ?> visits</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
