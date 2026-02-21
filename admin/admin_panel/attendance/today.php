<?php
/**
 * Today's Attendance Dashboard
 * Shows who is currently in the pool and today's logs.
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

$page_title = "Today's Attendance";

// Get attendance settings
$attendance_settings = Attendance::get_attendance_settings();

// Get currently inside members (only for entry_exit mode)
$members_inside = [];
if ($attendance_settings['attendance_mode'] === 'entry_exit') {
    $query_inside = "SELECT a.*, m.member_code, m.first_name, m.last_name, m.phone 
                     FROM attendance a 
                     JOIN members m ON a.member_id = m.member_id 
                     WHERE a.attendance_date = CURDATE() AND a.exit_time IS NULL 
                     ORDER BY a.entry_time DESC";
    $members_inside = db_fetch_all($query_inside);
}

// Get today's total logs (including those who left)
$query_all = "SELECT a.*, m.member_code, m.first_name, m.last_name, m.phone 
              FROM attendance a 
              JOIN members m ON a.member_id = m.member_id 
              WHERE a.attendance_date = CURDATE() 
              ORDER BY a.entry_time DESC";
$today_logs = db_fetch_all($query_all);

// Stats
$stats = [
    'inside' => count($members_inside),
    'total'  => count($today_logs),
    'exited' => count($today_logs) - count($members_inside)
];

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <!-- Stats Row -->
        <div class="row mt-3">
            <div class="col-12 col-lg-4">
                <div class="card bg-pattern-success stat-card shadow-sm">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <h4 class="text-white"><?php echo $stats['inside']; ?></h4>
                                <span class="text-white">Currently Inside</span>
                            </div>
                            <div class="align-self-center w-circle-icon rounded-circle bg-contrast">
                                <i class="icon-people text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card bg-pattern-primary stat-card shadow-sm">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <h4 class="text-white"><?php echo $stats['total']; ?></h4>
                                <span class="text-white">Total Entries Today</span>
                            </div>
                            <div class="align-self-center w-circle-icon rounded-circle bg-contrast">
                                <i class="icon-calendar text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card bg-pattern-warning stat-card shadow-sm">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <h4 class="text-white"><?php echo $stats['exited']; ?></h4>
                                <span class="text-white">Total Exits Today</span>
                            </div>
                            <div class="align-self-center w-circle-icon rounded-circle bg-contrast">
                                <i class="icon-logout text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Mode Indicator -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card <?php echo $attendance_settings['attendance_mode'] === 'entry_exit' ? 'bg-success' : 'bg-info'; ?> text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    <i class="<?php echo $attendance_settings['attendance_mode'] === 'entry_exit' ? 'icon-login' : 'icon-clock'; ?>"></i>
                                    Current Mode: <?php echo $attendance_settings['attendance_mode'] === 'entry_exit' ? 'Entry & Exit' : 'Entry Only'; ?>
                                </h5>
                            </div>
                            <div class="col text-right">
                                <?php if ($attendance_settings['attendance_mode'] === 'entry_only'): ?>
                                    <small>Auto Exit: <?php echo $attendance_settings['auto_exit_duration']; ?> minutes</small>
                                <?php endif; ?>
                                <a href="../settings/attendance.php" class="btn btn-sm btn-light ml-2">
                                    <i class="icon-settings"></i> Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Currently Inside List -->
            <?php if ($attendance_settings['attendance_mode'] === 'entry_exit'): ?>
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <i class="icon-people"></i> Members Currently Inside
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Member Code</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Entry Time</th>
                                        <th>Duration So Far</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($members_inside)): ?>
                                        <tr><td colspan="6" class="text-center py-4">No members are currently in the pool.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($members_inside as $member): 
                                            $entry_timestamp = strtotime($member['entry_time']);
                                            $diff = time() - $entry_timestamp;
                                            $duration_str = floor($diff / 3600) . 'h ' . ($diff / 60 % 60) . 'm';
                                        ?>
                                            <tr>
                                                <td><a href="../members/view.php?id=<?php echo $member['member_id']; ?>" class="text-primary"><?php echo clean($member['member_code']); ?></a></td>
                                                <td><?php echo clean($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                                <td><?php echo clean($member['phone']); ?></td>
                                                <td><?php echo date('h:i A', $entry_timestamp); ?></td>
                                                <td><span class="badge badge-success"><?php echo $duration_str; ?></span></td>
                                                <td>
                                                    <form method="POST" action="mark.php" onsubmit="return confirm('Are you sure you want to mark this member as exited?')">
                                                        <?php echo csrf_token_field(); ?>
                                                        <input type="hidden" name="member_code" value="<?php echo $member['member_code']; ?>">
                                                        <input type="hidden" name="action" value="exit">
                                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                                                            <i class="icon-logout"></i> Manual Exit
                                                        </button>
                                                    </form>
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
            <?php else: ?>
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <i class="icon-info"></i> Entry Only Mode Information
                    </div>
                    <div class="card-body text-center py-5">
                        <i class="icon-clock" style="font-size: 3rem; color: #17a2b8;"></i>
                        <h4 class="mt-3">Entry Only Mode Active</h4>
                        <p class="text-muted">
                            In Entry Only mode, all entries are automatically completed after <?php echo $attendance_settings['auto_exit_duration']; ?> minutes.<br>
                            There are no "currently inside" members since exit is automatically marked.
                        </p>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5>Total Entries Today</h5>
                                        <h3 class="text-primary"><?php echo $stats['total']; ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5>Auto Exit Duration</h5>
                                        <h3 class="text-info"><?php echo $attendance_settings['auto_exit_duration']; ?> min</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5>Completed Sessions</h5>
                                        <h3 class="text-success"><?php echo $stats['exited']; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- All Today's Logs -->
            <div class="col-lg-12 mt-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <i class="icon-clock"></i> Today's Complete Timeline
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Member</th>
                                        <th>Action</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($today_logs)): ?>
                                        <tr><td colspan="4" class="text-center py-3">No activity recorded today yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($today_logs as $log): ?>
                                            <tr>
                                                <td><?php echo date('h:i A', strtotime($log['entry_time'])); ?></td>
                                                <td>
                                                    <strong><?php echo clean($log['first_name'] . ' ' . $log['last_name']); ?></strong><br>
                                                    <small><?php echo clean($log['member_code']); ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($log['exit_time']): ?>
                                                        <span class="text-muted"><i class="icon-check"></i> Completed Session</span><br>
                                                        <small>Exited at <?php echo date('h:i A', strtotime($log['exit_time'])); ?></small>
                                                    <?php else: ?>
                                                        <span class="text-success"><i class="icon-directions"></i> Entered Pool</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($log['duration_minutes']) {
                                                        echo floor($log['duration_minutes'] / 60) . 'h ' . ($log['duration_minutes'] % 60) . 'm';
                                                    } else if (!$log['exit_time']) {
                                                        echo '<span class="text-info">Ongoing</span>';
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
