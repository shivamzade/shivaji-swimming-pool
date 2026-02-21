<?php
/**
 * Members Listing
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'All Members';

// Get filter parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';

// Get members
$members_data = Member::get_all($page, RECORDS_PER_PAGE, $search, $status_filter);

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
                                <h5 class="mb-0"><i class="icon-people"></i> All Members</h5>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo ADMIN_URL; ?>/members/add.php" class="btn btn-light">
                                    <i class="icon-plus"></i> Add New Member
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <!-- Search and Filter -->
                        <form method="GET" action="" class="mb-3">
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search by code, name, or phone..." 
                                           value="<?php echo clean($search); ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="ACTIVE" <?php echo $status_filter === 'ACTIVE' ? 'selected' : ''; ?>>Active</option>
                                        <option value="EXPIRED" <?php echo $status_filter === 'EXPIRED' ? 'selected' : ''; ?>>Expired</option>
                                        <option value="SUSPENDED" <?php echo $status_filter === 'SUSPENDED' ? 'selected' : ''; ?>>Suspended</option>
                                        <option value="INACTIVE" <?php echo $status_filter === 'INACTIVE' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="icon-magnifier"></i> Search
                                    </button>
                                    <a href="<?php echo ADMIN_URL; ?>/members/index.php" class="btn btn-secondary">
                                        <i class="icon-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Results Info -->
                        <div class="mb-3">
                            <strong>Total Members: <?php echo $members_data['total']; ?></strong>
                            <?php if ($search || $status_filter): ?>
                                <span class="text-muted">(filtered)</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Members Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Member Code</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Registration Date</th>
                                        <th>Membership Expiry</th>
                                    <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($members_data['data'])): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <?php if ($search || $status_filter): ?>
                                                No members found matching your search criteria.
                                            <?php else: ?>
                                                No members registered yet. <a href="<?php echo ADMIN_URL; ?>/members/add.php">Add the first member</a>.
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php 
                                        $serial = ($page - 1) * RECORDS_PER_PAGE + 1;
                                        foreach ($members_data['data'] as $member): 
                                        ?>
                                        <tr>
                                            <td><?php echo $serial++; ?></td>
                                            <td><strong><?php echo clean($member['member_code']); ?></strong></td>
                                            <td><?php echo clean($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                            <td><?php echo clean($member['phone']); ?></td>
                                            <td><?php echo format_date($member['registration_date']); ?></td>
                                            <td>
                                                <?php if ($member['membership_end_date']): ?>
                                                    <?php echo format_date($member['membership_end_date']); ?>
                                                    <?php if ($member['days_remaining'] !== null): ?>
                                                        <?php if ($member['days_remaining'] < 0): ?>
                                                            <br><small class="text-danger">Expired <?php echo abs($member['days_remaining']); ?> days ago</small>
                                                        <?php elseif ($member['days_remaining'] <= 7): ?>
                                                            <br><small class="text-warning"><?php echo $member['days_remaining']; ?> days left</small>
                                                        <?php else: ?>
                                                            <br><small class="text-success"><?php echo $member['days_remaining']; ?> days left</small>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No plan assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_classes = [
                                                    'ACTIVE' => 'badge-success',
                                                    'EXPIRED' => 'badge-danger',
                                                    'SUSPENDED' => 'badge-warning',
                                                    'INACTIVE' => 'badge-secondary'
                                                ];
                                                $status_class = $status_classes[$member['status']] ?? 'badge-secondary';
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>">
                                                    <?php echo clean($member['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?php echo ADMIN_URL; ?>/members/view.php?id=<?php echo $member['member_id']; ?>" 
                                                       class="btn btn-info" title="View">
                                                        <i class="icon-eye"></i>
                                                    </a>
                                                    <a href="<?php echo ADMIN_URL; ?>/members/edit.php?id=<?php echo $member['member_id']; ?>" 
                                                       class="btn btn-primary" title="Edit">
                                                        <i class="icon-pencil"></i>
                                                    </a>
                                                    <?php if ($member['status'] === 'EXPIRED' || !$member['membership_end_date']): ?>
                                                    <a href="<?php echo ADMIN_URL; ?>/members/renew.php?id=<?php echo $member['member_id']; ?>" 
                                                       class="btn btn-success" title="Renew">
                                                        <i class="icon-refresh"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php 
                                                    // Show WhatsApp reminder for expired or expiring soon members
                                                    $show_wa = false;
                                                    $wa_message = '';
                                                    $member_name = clean($member['first_name']);
                                                    
                                                    if ($member['status'] === 'EXPIRED' && $member['membership_end_date']) {
                                                        $show_wa = true;
                                                        $expiry_date = date('d M Y', strtotime($member['membership_end_date']));
                                                        $wa_message = "Namaste {$member_name}! 🙏\n\nThis is a reminder from *" . POOL_NAME . "*.\n\nYour membership expired on *{$expiry_date}*. Kindly visit us and renew your membership at the earliest to continue enjoying our facilities.\n\nThank you! 🏊";
                                                    } elseif ($member['membership_end_date'] && isset($member['days_remaining']) && $member['days_remaining'] !== null && $member['days_remaining'] <= 7 && $member['days_remaining'] >= 0) {
                                                        $show_wa = true;
                                                        $expiry_date = date('d M Y', strtotime($member['membership_end_date']));
                                                        $days = $member['days_remaining'];
                                                        $wa_message = "Namaste {$member_name}! 🙏\n\nThis is a reminder from *" . POOL_NAME . "*.\n\nYour membership is expiring on *{$expiry_date}* ({$days} days left). Kindly renew before it expires to avoid any break in service.\n\nThank you! 🏊";
                                                    }
                                                    
                                                    if ($show_wa && !empty($member['phone'])):
                                                        $phone = preg_replace('/[^0-9]/', '', $member['phone']);
                                                        if (strlen($phone) === 10) $phone = '91' . $phone;
                                                        $wa_url = 'https://wa.me/' . $phone . '?text=' . urlencode($wa_message);
                                                    ?>
                                                    <a href="<?php echo $wa_url; ?>" 
                                                       target="_blank" class="btn btn-success" title="Send WhatsApp Reminder"
                                                       style="background-color: #25D366; border-color: #25D366;">
                                                        <i class="fa fa-whatsapp"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($members_data['total_pages'] > 1): ?>
                        <nav aria-label="Page navigation" class="mt-3">
                            <ul class="pagination justify-content-center">
                                <!-- Previous -->
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>">
                                        Previous
                                    </a>
                                </li>
                                <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
                                </li>
                                <?php endif; ?>
                                
                                <!-- Page Numbers -->
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($members_data['total_pages'], $page + 2);
                                
                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Next -->
                                <?php if ($page < $members_data['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>">
                                        Next
                                    </a>
                                </li>
                                <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">Next</span>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <div class="text-center text-muted">
                            <small>
                                Page <?php echo $page; ?> of <?php echo $members_data['total_pages']; ?>
                                (Showing <?php echo count($members_data['data']); ?> of <?php echo $members_data['total']; ?> members)
                            </small>
                        </div>
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
