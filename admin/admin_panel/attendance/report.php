<?php
/**
 * Attendance History Report
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

$page_title = 'Attendance History';

// Filtering
$start_date = isset($_GET['start_date']) ? sanitize_input($_GET['start_date']) : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? sanitize_input($_GET['end_date']) : date('Y-m-d');
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 50;
$offset = ($page - 1) * $per_page;

$where = "WHERE a.attendance_date BETWEEN ? AND ?";
$params = [$start_date, $end_date];
$types = "ss";

if ($search) {
    $where .= " AND (m.first_name LIKE ? OR m.last_name LIKE ? OR m.member_code LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

// Count total
$count_query = "SELECT COUNT(*) as total FROM attendance a JOIN members m ON a.member_id = m.member_id $where";
$total_rows = db_fetch_one($count_query, $types, $params)['total'];
$total_pages = ceil($total_rows / $per_page);

// Fetch data
$query = "SELECT a.*, m.member_code, m.first_name, m.last_name 
          FROM attendance a 
          JOIN members m ON a.member_id = m.member_id 
          $where 
          ORDER BY a.attendance_date DESC, a.entry_time DESC 
          LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";

$logs = db_fetch_all($query, $types, $params);

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
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="icon-calendar"></i> Attendance History</h5>
                        <button onclick="window.print()" class="btn btn-light btn-sm"><i class="icon-printer"></i> Print Report</button>
                    </div>
                    <div class="card-body">
                        
                        <!-- Filter Form -->
                        <form method="GET" class="mb-4 bg-light p-3 rounded">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label>Search Member</label>
                                    <input type="text" name="search" class="form-control" placeholder="Name or Code..." value="<?php echo $search; ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Member Code</th>
                                        <th>Name</th>
                                        <th>Entry</th>
                                        <th>Exit</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($logs)): ?>
                                        <tr><td colspan="6" class="text-center py-4">No records found for the selected period.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($logs as $log): ?>
                                            <tr>
                                                <td><?php echo format_date($log['attendance_date']); ?></td>
                                                <td><code><?php echo clean($log['member_code']); ?></code></td>
                                                <td><?php echo clean($log['first_name'] . ' ' . $log['last_name']); ?></td>
                                                <td><?php echo date('h:i A', strtotime($log['entry_time'])); ?></td>
                                                <td><?php echo $log['exit_time'] ? date('h:i A', strtotime($log['exit_time'])) : '<span class="text-info">No exit</span>'; ?></td>
                                                <td>
                                                    <?php 
                                                    if ($log['duration_minutes']) {
                                                        echo floor($log['duration_minutes'] / 60) . 'h ' . ($log['duration_minutes'] % 60) . 'm';
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                            <a class="page-item" href="?page=<?php echo $i; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&search=<?php echo $search; ?>">
                                                <?php echo $i; ?>
                                            </a>
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
