<?php
/**
 * View Staff Member Profile
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

// Check if ID is provided
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    set_flash('error', 'Staff ID is required');
    redirect('index.php');
}

// Get staff/user data
$query = "SELECT u.*, r.role_name, s.employee_id, s.designation, s.join_date, s.salary,
          (SELECT full_name FROM users WHERE user_id = u.created_by) as creator_name
          FROM users u
          JOIN roles r ON u.role_id = r.role_id
          LEFT JOIN staff s ON u.user_id = s.user_id
          WHERE u.user_id = ?";

$staff = db_fetch_one($query, 'i', [$user_id]);

if (!$staff) {
    set_flash('error', 'Staff member not found');
    redirect('index.php');
}

// Set page title
$page_title = 'Staff Profile: ' . $staff['full_name'];

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-lg-4">
                <div class="card profile-card-2">
                    <div class="card-img-block p-4 text-center bg-dark">
                        <div class="rounded-circle bg-light d-inline-block shadow-sm" style="width: 120px; height: 120px; line-height: 120px;">
                            <i class="icon-user fa-4x text-dark" style="vertical-align: middle;"></i>
                        </div>
                    </div>
                    <div class="card-body pt-5 text-center">
                        <h5 class="card-title"><?php echo clean($staff['full_name']); ?></h5>
                        <p class="card-text text-muted"><?php echo clean($staff['designation'] ?: 'No Designation'); ?></p>
                        <div class="mt-3">
                            <span class="badge badge-pill badge-info px-3"><?php echo clean($staff['role_name']); ?></span>
                            <?php if ($staff['is_active']): ?>
                                <span class="badge badge-pill badge-success px-3">Active Account</span>
                            <?php else: ?>
                                <span class="badge badge-pill badge-danger px-3">Suspended</span>
                            <?php endif; ?>
                        </div>
                        <hr>
                        <div class="text-left">
                            <p class="mb-1"><strong>Phone:</strong> <?php echo clean($staff['phone'] ?: 'No phone provided'); ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?php echo clean($staff['email']); ?></p>
                            <p class="mb-0"><strong>Joined:</strong> <?php echo $staff['join_date'] ? format_date($staff['join_date']) : 'Unknown'; ?></p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="edit.php?id=<?php echo $user_id; ?>" class="btn btn-primary btn-sm btn-block">
                            <i class="icon-pencil"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Employment & System Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Employee ID</label>
                                <p class="h6"><?php echo clean($staff['employee_id'] ?: 'N/A'); ?></p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Username</label>
                                <p class="h6"><?php echo clean($staff['username']); ?></p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Designation</label>
                                <p class="h6"><?php echo clean($staff['designation'] ?: 'N/A'); ?></p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Join Date</label>
                                <p class="h6"><?php echo $staff['join_date'] ? format_date($staff['join_date']) : 'N/A'; ?></p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Monthly Salary</label>
                                <p class="h6 text-success"><?php echo $staff['salary'] ? format_currency($staff['salary']) : 'Not Disclosed'; ?></p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Role Access</label>
                                <p class="h6 text-primary"><?php echo clean($staff['role_name']); ?></p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Account Created</label>
                                <p class="mb-0"><?php echo format_date($staff['created_at']); ?></p>
                                <small class="text-muted">By: <?php echo clean($staff['creator_name'] ?: 'System'); ?></small>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="text-muted small text-uppercase font-weight-bold">Last Activity</label>
                                <p class="mb-0"><?php echo $staff['updated_at'] ? format_date($staff['updated_at']) : 'No activity logged'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Recent Activity Log</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $logs = db_fetch_all("SELECT * FROM audit_logs WHERE user_id = ? ORDER BY log_id DESC LIMIT 5", 'i', [$user_id]);
                                    if (empty($logs)):
                                    ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted">No recent activity found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($logs as $log): ?>
                                            <tr>
                                                <td><?php echo format_datetime($log['created_at']); ?></td>
                                                <td><span class="badge badge-light"><?php echo clean($log['action']); ?></span></td>
                                                <td class="small">
                                                    <?php echo clean($log['table_name'] ?? ''); ?> (ID: <?php echo clean($log['record_id'] ?? ''); ?>)<br>
                                                    <span class="text-muted">IP: <?php echo clean($log['ip_address'] ?? 'N/A'); ?></span>
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
