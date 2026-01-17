<?php
/**
 * Shift Management
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

$page_title = 'Shift Management';

// Handle shift creation/deletion (if requested, but let's keep it simple for now as a list)
// Fetch all shifts
$shifts = db_fetch_all("SELECT * FROM shifts ORDER BY start_time ASC");

// Fetch staff assignments for today
$today = date('Y-m-d');
$assignments_query = "SELECT sa.*, u.full_name, s.designation, sh.shift_name, sh.start_time, sh.end_time
                      FROM shift_assignments sa
                      JOIN staff s ON sa.staff_id = s.staff_id
                      JOIN users u ON s.user_id = u.user_id
                      JOIN shifts sh ON sa.shift_id = sh.shift_id
                      WHERE sa.assignment_date = ?
                      ORDER BY sh.start_time ASC";
$assignments = db_fetch_all($assignments_query, 's', [$today]);

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
                        <h5 class="mb-0"><i class="icon-clock"></i> Staff Shift Management</h5>
                        <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#addShiftModal">
                            <i class="icon-plus"></i> New Shift
                        </button>
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>

                        <div class="row">
                            <!-- Available Shifts -->
                            <div class="col-lg-4">
                                <h6 class="text-uppercase text-primary mb-3">Defined Shifts</h6>
                                <?php foreach ($shifts as $shift): ?>
                                    <div class="card border-left border-primary mb-2">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold"><?php echo clean($shift['shift_name']); ?></h6>
                                                    <small class="text-muted">
                                                        <i class="icon-clock"></i> 
                                                        <?php echo format_time($shift['start_time']); ?> - <?php echo format_time($shift['end_time']); ?>
                                                    </small>
                                                </div>
                                                <span class="badge badge-light"><?php echo clean($shift['is_active'] ? 'Active' : 'Inactive'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Today's Assignments -->
                            <div class="col-lg-8">
                                <h6 class="text-uppercase text-primary mb-3">Today's Assignments (<?php echo format_date($today); ?>)</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Staff Member</th>
                                                <th>Shift</th>
                                                <th>Timing</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($assignments)): ?>
                                                <tr><td colspan="4" class="text-center py-4 text-muted">No shifts assigned for today.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($assignments as $as): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo clean($as['full_name']); ?></strong><br>
                                                            <small class="text-muted"><?php echo clean($as['designation']); ?></small>
                                                        </td>
                                                        <td><span class="badge badge-pill badge-primary"><?php echo clean($as['shift_name']); ?></span></td>
                                                        <td><?php echo format_time($as['start_time']); ?> - <?php echo format_time($as['end_time']); ?></td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo ($as['status'] == 'COMPLETED') ? 'success' : 
                                                                     (($as['status'] == 'ABSENT') ? 'danger' : 'warning'); 
                                                            ?> px-2">
                                                                <?php echo clean($as['status']); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-right mt-3">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#assignShiftModal">
                                        <i class="icon-user-follow"></i> Assign Shift
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Modal placeholders (For future implementation) -->
<div class="modal fade" id="addShiftModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Define New Shift</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Form to define shift timing and names would go here.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignShiftModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Assign Shift to Staff</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Form to select staff and assign them to a shift for a specific date.</p>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../../../includes/admin_footer.php';
?>
