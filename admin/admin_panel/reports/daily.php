<?php
/**
 * Daily Attendance Report
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'Daily Attendance Report';

// Get filter date
$report_date = isset($_GET['report_date']) ? sanitize_input($_GET['report_date']) : date('Y-m-d');

// Get attendance for the date
$query = "SELECT a.*, m.first_name, m.last_name, m.member_code, m.phone
          FROM attendance a
          JOIN members m ON a.member_id = m.member_id
          WHERE a.attendance_date = ?
          ORDER BY a.entry_time DESC";

$records = db_fetch_all($query, 's', [$report_date]);

// Stats for the chosen date
$stats = db_fetch_one("SELECT COUNT(*) as total, 
                       SUM(IF(exit_time IS NOT NULL, 1, 0)) as exited,
                       AVG(duration_minutes) as avg_duration 
                       FROM attendance WHERE attendance_date = ?", 's', [$report_date]);

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
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0"><i class="icon-calendar"></i> Attendance Report for <?php echo format_date($report_date); ?></h5>
                            </div>
                            <div class="col-md-6 text-right d-print-none">
                                <form method="GET" class="form-inline justify-content-end">
                                    <label class="mr-2">Date:</label>
                                    <input type="date" name="report_date" class="form-control form-control-sm mr-2" value="<?php echo $report_date; ?>">
                                    <button type="submit" class="btn btn-sm btn-light">View Report</button>
                                    <button onclick="window.print()" class="btn btn-sm btn-light ml-1"><i class="icon-printer"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Summary Row -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="bg-light p-3 border rounded text-center">
                                    <h6 class="text-muted">Total Entries</h6>
                                    <h3><?php echo $stats['total']; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 border rounded text-center">
                                    <h6 class="text-muted">Completed Visits</h6>
                                    <h3><?php echo (int)$stats['exited']; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 border rounded text-center">
                                    <h6 class="text-muted">Avg. Duration</h6>
                                    <h3><?php echo round($stats['avg_duration'] ?? 0); ?> <small>mins</small></h3>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Member Name</th>
                                        <th>Phone</th>
                                        <th>Entry Time</th>
                                        <th>Exit Time</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($records)): ?>
                                        <tr><td colspan="6" class="text-center py-4">No attendance recorded for this date.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($records as $rec): ?>
                                            <tr>
                                                <td><?php echo clean($rec['member_code']); ?></td>
                                                <td><?php echo clean($rec['first_name'] . ' ' . $rec['last_name']); ?></td>
                                                <td><?php echo clean($rec['phone']); ?></td>
                                                <td><?php echo format_time($rec['entry_time']); ?></td>
                                                <td><?php echo $rec['exit_time'] ? format_time($rec['exit_time']) : '<span class="text-success badge badge-light">Inside</span>'; ?></td>
                                                <td><?php echo $rec['duration_minutes'] ? $rec['duration_minutes'] . 'm' : '-'; ?></td>
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
