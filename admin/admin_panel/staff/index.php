<?php
/**
 * Staff Listing
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Check permissions (Super Admin/Admin only)
if (!has_role([1, 2])) {
    set_flash('error', 'You do not have permission to access staff management.');
    redirect(ADMIN_URL . '/index.php');
}

// Set page title
$page_title = 'Staff Management';

// Get all staff (users joined with staff table and roles)
$query = "SELECT u.*, r.role_name, s.employee_id, s.designation, s.join_date, s.salary
          FROM users u
          JOIN roles r ON u.role_id = r.role_id
          LEFT JOIN staff s ON u.user_id = s.user_id
          WHERE u.role_id != 1 -- Hide Super Admin from list for regular admins
          ORDER BY u.created_at DESC";

$staff_list = db_fetch_all($query);

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
                                <h5 class="mb-0"><i class="icon-user"></i> Staff Management</h5>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="add.php" class="btn btn-light btn-sm">
                                    <i class="icon-plus"></i> Add New Staff
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Username/Email</th>
                                        <th>Role</th>
                                        <th>Designation</th>
                                        <th>Join Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($staff_list)): ?>
                                        <tr><td colspan="7" class="text-center py-4">No staff members found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($staff_list as $staff): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo clean($staff['full_name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo clean($staff['phone']); ?></small>
                                                </td>
                                                <td>
                                                    <?php echo clean($staff['username']); ?><br>
                                                    <small class="text-muted"><?php echo clean($staff['email']); ?></small>
                                                </td>
                                                <td><span class="badge badge-info"><?php echo clean($staff['role_name']); ?></span></td>
                                                <td><?php echo $staff['designation'] ?: '<span class="text-muted">N/A</span>'; ?></td>
                                                <td><?php echo $staff['join_date'] ? format_date($staff['join_date']) : 'N/A'; ?></td>
                                                <td>
                                                    <?php if ($staff['is_active']): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="edit.php?id=<?php echo $staff['user_id']; ?>" class="btn btn-primary" title="Edit">
                                                            <i class="icon-pencil"></i>
                                                        </a>
                                                        <a href="view.php?id=<?php echo $staff['user_id']; ?>" class="btn btn-info" title="View Profile">
                                                            <i class="icon-eye"></i>
                                                        </a>
                                                    </div>
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
