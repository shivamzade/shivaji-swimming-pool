<?php
/**
 * Mark Attendance (Entry/Exit)
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'Manual Attendance Entry';

// Handle form submission
$response = null;
if (is_post_request()) {
    require_csrf_token();
    
    $member_code = sanitize_input($_POST['member_code'] ?? '');
    $action = $_POST['action'] ?? 'entry';
    
    if (empty($member_code)) {
        $response = ['success' => false, 'message' => 'Please scan or enter a member code'];
    } else {
        if ($action === 'entry') {
            $response = Attendance::mark_entry($member_code);
        } else {
            $response = Attendance::mark_exit($member_code);
        }
    }
}

// Get stats for the top cards
$stats = Attendance::get_today_stats();
$inside_members = Attendance::get_currently_inside();
$attendance_settings = Attendance::get_attendance_settings();

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <!-- Stats Row -->
        <div class="row mt-3">
            <div class="col-12 col-md-4">
                <div class="card bg-primary text-white text-center">
                    <div class="card-body">
                        <h5>Today's Entries</h5>
                        <h2><?php echo $stats['total_entries']; ?></h2>
                    </div>
                </div>
            </div>
            <?php if ($attendance_settings['attendance_mode'] === 'entry_exit'): ?>
            <div class="col-12 col-md-4">
                <div class="card bg-success text-white text-center">
                    <div class="card-body">
                        <h5>Currently Inside</h5>
                        <h2><?php echo $stats['currently_inside']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card bg-dark text-white text-center">
                    <div class="card-body">
                        <h5>Total Exited</h5>
                        <h2><?php echo $stats['exited']; ?></h2>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="col-12 col-md-8">
                <div class="card bg-info text-white text-center">
                    <div class="card-body">
                        <h5>Mode: Entry Only (Auto Exit: <?php echo $attendance_settings['auto_exit_duration']; ?> min)</h5>
                        <h2>All entries completed</h2>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <!-- Attendance Scanner -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="icon-plus"></i> Manual Attendance Entry
                    </div>
                    <div class="card-body text-center">
                        <?php if ($response): ?>
                            <div class="alert <?php echo $response['success'] ? 'alert-success' : 'alert-danger'; ?> alert-dismissible mb-4" role="alert">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <h5 class="alert-heading text-left"><?php echo $response['success'] ? 'Success!' : 'Error!'; ?></h5>
                                <p class="mb-0 text-left"><?php echo $response['message']; ?></p>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="attendance-form">
                            <?php echo csrf_token_field(); ?>
                            
                            <div class="mb-4">
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <?php if ($attendance_settings['attendance_mode'] === 'entry_exit'): ?>
                                    <label class="btn btn-outline-success active">
                                        <input type="radio" name="action" value="entry" checked> <i class="icon-login"></i> ENTRY
                                    </label>
                                    <label class="btn btn-outline-danger">
                                        <input type="radio" name="action" value="exit"> <i class="icon-logout"></i> EXIT
                                    </label>
                                    <?php else: ?>
                                    <label class="btn btn-outline-success active w-100">
                                        <input type="radio" name="action" value="entry" checked> <i class="icon-clock"></i> ENTRY ONLY
                                    </label>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="h6">Member Code</label>
                                <input type="text" name="member_code" id="member_code" 
                                       class="form-control form-control-lg text-center" 
                                       placeholder="Scan QR or Enter Code" 
                                       autocomplete="off" autofocus>
                                <small class="text-muted">Place cursor here before scanning</small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                <i class="icon-check"></i> PROCESS
                            </button>
                        </form>
                        
                        <div class="mt-4 p-3 bg-light rounded text-left">
                            <h6><i class="icon-info"></i> Instructions:</h6>
                            <ul class="small mb-0 pl-3">
                                <?php if ($attendance_settings['attendance_mode'] === 'entry_exit'): ?>
                                <li>Select <strong>ENTRY</strong> or <strong>EXIT</strong> mode above.</li>
                                <li>Ensure the cursor is in the text field.</li>
                                <li>Scan the QR code on the member's card.</li>
                                <li>Wait for the confirmation message.</li>
                                <?php else: ?>
                                <li><strong>Entry Only Mode:</strong> Members scan only for entry.</li>
                                <li>Exit will be automatically marked after <?php echo $attendance_settings['auto_exit_duration']; ?> minutes.</li>
                                <li>Ensure the cursor is in the text field.</li>
                                <li>Scan the QR code on the member's card.</li>
                                <li>Wait for the confirmation message with auto-exit time.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Dashboard -->
            <?php if ($attendance_settings['attendance_mode'] === 'entry_exit'): ?>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0"><i class="icon-people text-success"></i> Currently Inside</h5>
                            </div>
                            <div class="col text-right">
                                <button onclick="location.reload()" class="btn btn-sm btn-outline-secondary">
                                    <i class="icon-refresh"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px;">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Member</th>
                                        <th>Code</th>
                                        <th>Entry Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($inside_members)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                No members currently inside the pool.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($inside_members as $member): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo clean($member['first_name'] . ' ' . $member['last_name']); ?></strong>
                                                    <div class="small text-muted"><?php echo clean($member['phone']); ?></div>
                                                </td>
                                                <td><span class="badge badge-light"><?php echo clean($member['member_code']); ?></span></td>
                                                <td><?php echo format_time($member['entry_time']); ?></td>
                                                <td>
                                                    <form method="POST" action="" style="display:inline;">
                                                        <?php echo csrf_token_field(); ?>
                                                        <input type="hidden" name="member_code" value="<?php echo $member['member_code']; ?>">
                                                        <input type="hidden" name="action" value="exit">
                                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                                                            Mark Exit
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
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="icon-info"></i> Entry Only Mode Active</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="py-5">
                            <i class="icon-clock" style="font-size: 4rem; color: #17a2b8;"></i>
                            <h4 class="mt-3">Entry Only Mode</h4>
                            <p class="text-muted">
                                All entries are automatically completed after <?php echo $attendance_settings['auto_exit_duration']; ?> minutes.<br>
                                No manual exit tracking is required in this mode.
                            </p>
                            <div class="alert alert-light">
                                <strong>Today's Total Entries:</strong> <?php echo $stats['total_entries']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<script>
// Auto-focus the input field on load and after submission
window.onload = function() {
    document.getElementById('member_code').focus();
};

// Play sound on success/error (Optional)
<?php if ($response): ?>
    let sound = new Audio('<?php echo $response['success'] ? ASSETS_URL . "/sounds/success.mp3" : ASSETS_URL . "/sounds/error.mp3"; ?>');
    // sound.play(); // Uncomment if you have sound files
<?php endif; ?>
</script>

<?php
// Include footer
include_once '../../../includes/admin_footer.php';
?>
