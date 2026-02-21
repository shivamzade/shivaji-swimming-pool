<?php
/**
 * Attendance Settings Management
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

$page_title = 'Attendance Settings';

// Handle form submission
$response = null;
if (is_post_request()) {
    require_csrf_token();
    
    // Update attendance settings
    $settings = [
        'attendance_mode' => sanitize_input($_POST['attendance_mode'] ?? 'entry_exit'),
        'auto_exit_duration' => (int)($_POST['auto_exit_duration'] ?? 60),
        'allow_multiple_entries' => (int)($_POST['allow_multiple_entries'] ?? 0)
    ];
    
    // Validate settings
    if ($settings['attendance_mode'] !== 'entry_exit' && $settings['attendance_mode'] !== 'entry_only') {
        $response = ['success' => false, 'message' => 'Invalid attendance mode selected'];
    } elseif ($settings['auto_exit_duration'] < 15 || $settings['auto_exit_duration'] > 480) {
        $response = ['success' => false, 'message' => 'Auto exit duration must be between 15 and 480 minutes'];
    } else {
        // Update each setting
        $success = true;
        foreach ($settings as $key => $value) {
            $result = db_query(
                "UPDATE settings SET setting_value = ? WHERE setting_key = ?", 
                'ss', 
                [$value, $key]
            );
            if (!$result) {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            log_activity('SETTINGS_UPDATED', 'settings', null, null, $settings);
            $response = ['success' => true, 'message' => 'Attendance settings updated successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Error updating settings'];
        }
    }
}

// Get current settings
$settings_query = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('attendance_mode', 'auto_exit_duration', 'allow_multiple_entries')";
$settings_result = db_fetch_all($settings_query);
$current_settings = [];
foreach ($settings_result as $setting) {
    $current_settings[$setting['setting_key']] = $setting['setting_value'];
}

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="icon-settings"></i> Attendance Settings
                    </div>
                    <div class="card-body">
                        
                        <?php if ($response): ?>
                            <div class="alert <?php echo $response['success'] ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
                                <strong><?php echo $response['success'] ? 'Success!' : 'Error!'; ?></strong> 
                                <?php echo $response['message']; ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <?php echo csrf_token_field(); ?>
                            
                            <!-- Attendance Mode -->
                            <div class="form-group">
                                <label class="h6">Attendance Mode</label>
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="attendance_mode" 
                                                   id="mode_entry_exit" value="entry_exit" 
                                                   <?php echo ($current_settings['attendance_mode'] ?? 'entry_exit') === 'entry_exit' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="mode_entry_exit">
                                                <strong>Entry & Exit Mode</strong>
                                                <div class="text-muted small">Members must scan for both entry and exit. Duration is calculated based on actual time spent.</div>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="attendance_mode" 
                                                   id="mode_entry_only" value="entry_only" 
                                                   <?php echo ($current_settings['attendance_mode'] ?? '') === 'entry_only' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="mode_entry_only">
                                                <strong>Entry Only Mode</strong>
                                                <div class="text-muted small">Members scan only for entry. System automatically marks exit after specified duration.</div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Auto Exit Duration (shown only for entry-only mode) -->
                            <div class="form-group" id="auto_exit_group" style="display: <?php echo ($current_settings['attendance_mode'] ?? 'entry_exit') === 'entry_only' ? 'block' : 'none'; ?>;">
                                <label for="auto_exit_duration" class="h6">Auto Exit Duration (Minutes)</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" id="auto_exit_duration" 
                                               name="auto_exit_duration" 
                                               value="<?php echo $current_settings['auto_exit_duration'] ?? 60; ?>"
                                               min="15" max="480" step="5">
                                    </div>
                                    <div class="col-md-8">
                                        <small class="text-muted">
                                            After how many minutes should the system automatically mark exit? 
                                            <br>Recommended: 60 minutes (1 hour). Range: 15-480 minutes.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Multiple Entries Setting -->
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="allow_multiple_entries" 
                                           id="allow_multiple_entries" value="1"
                                           <?php echo ($current_settings['allow_multiple_entries'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="allow_multiple_entries">
                                        <strong>Allow Multiple Entries Per Day</strong>
                                        <div class="text-muted small">If checked, members can enter multiple times in a single day. If unchecked, only one entry per day is allowed.</div>
                                    </label>
                                </div>
                            </div>

                            <!-- Current Status Display -->
                            <div class="alert alert-info">
                                <h6><i class="icon-info"></i> Current Configuration:</h6>
                                <ul class="mb-0">
                                    <li><strong>Mode:</strong> 
                                        <?php 
                                        $mode = $current_settings['attendance_mode'] ?? 'entry_exit';
                                        echo $mode === 'entry_exit' ? 'Entry & Exit' : 'Entry Only'; 
                                        ?>
                                    </li>
                                    <li><strong>Auto Exit:</strong> 
                                        <?php echo $current_settings['auto_exit_duration'] ?? 60; ?> minutes
                                        <?php if ($mode === 'entry_exit') echo '(Not used in Entry & Exit mode)'; ?>
                                    </li>
                                    <li><strong>Multiple Entries:</strong> 
                                        <?php echo ($current_settings['allow_multiple_entries'] ?? '0') === '1' ? 'Allowed' : 'Not Allowed'; ?>
                                    </li>
                                </ul>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="icon-check"></i> Save Settings
                                </button>
                                <a href="../index.php" class="btn btn-secondary btn-lg ml-2">
                                    <i class="icon-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Explanation -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="icon-question"></i> Understanding Attendance Modes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="icon-login"></i> Entry & Exit Mode</h6>
                                <ul>
                                    <li>Members scan QR code when entering the pool</li>
                                    <li>Members scan QR code again when leaving the pool</li>
                                    <li>Actual duration is calculated based on entry and exit times</li>
                                    <li>Best for: Pools that want accurate time tracking</li>
                                    <li>Staff can manually mark exit if member forgets to scan</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success"><i class="icon-clock"></i> Entry Only Mode</h6>
                                <ul>
                                    <li>Members scan QR code only when entering the pool</li>
                                    <li>System automatically marks exit after specified duration</li>
                                    <li>Fixed duration is recorded for all entries</li>
                                    <li>Best for: High-traffic pools with quick turnover</li>
                                    <li>Reduces congestion at exit points</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Show/hide auto exit duration based on attendance mode
document.addEventListener('DOMContentLoaded', function() {
    const modeEntryExit = document.getElementById('mode_entry_exit');
    const modeEntryOnly = document.getElementById('mode_entry_only');
    const autoExitGroup = document.getElementById('auto_exit_group');
    
    function toggleAutoExit() {
        if (modeEntryOnly.checked) {
            autoExitGroup.style.display = 'block';
        } else {
            autoExitGroup.style.display = 'none';
        }
    }
    
    modeEntryExit.addEventListener('change', toggleAutoExit);
    modeEntryOnly.addEventListener('change', toggleAutoExit);
    
    // Initial state
    toggleAutoExit();
});
</script>

<?php
// Include footer
include_once '../../../includes/admin_footer.php';
?>
