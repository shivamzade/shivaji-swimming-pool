<?php
/**
 * Pending Dues / Expired Memberships
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

$page_title = 'Pending Dues & Expired Members';

// Fetch all members with status 'EXPIRED' or those whose membership ends today/yesterday
$query = "SELECT m.*, DATEDIFF(CURDATE(), m.membership_end_date) as days_overdue
          FROM members m
          WHERE m.status = 'EXPIRED' OR m.membership_end_date <= CURDATE()
          ORDER BY days_overdue DESC";

$expired_list = db_fetch_all($query);

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
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="icon-clock"></i> Expired Memberships (Pending Dues)</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">The following members have expired memberships and cannot enter the pool until they renew.</p>
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Member</th>
                                        <th>Phone</th>
                                        <th>Expired On</th>
                                        <th>Days Overdue</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($expired_list)): ?>
                                        <tr><td colspan="5" class="text-center py-4">Great! All active members are paid up.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($expired_list as $mem): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo clean($mem['first_name'] . ' ' . $mem['last_name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo clean($mem['member_code']); ?></small>
                                                </td>
                                                <td><?php echo clean($mem['phone']); ?></td>
                                                <td><span class="text-danger"><?php echo format_date($mem['membership_end_date']); ?></span></td>
                                                <td>
                                                    <span class="badge badge-pill badge-danger font-weight-normal px-3">
                                                        <?php echo $mem['days_overdue']; ?> days
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="../members/renew.php?id=<?php echo $mem['member_id']; ?>" class="btn btn-sm btn-success">
                                                        <i class="icon-refresh"></i> Renew & Pay
                                                    </a>
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
