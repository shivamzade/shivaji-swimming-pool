<?php
/**
 * Membership Plans Listing
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'Membership Plans';

// Get all plans
$plans = db_fetch_all("SELECT * FROM membership_plans ORDER BY is_active DESC, price ASC");

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
                                <h5 class="mb-0"><i class="icon-tag"></i> Membership Plans</h5>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="add.php" class="btn btn-light btn-sm">
                                    <i class="icon-plus"></i> Add New Plan
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
                                        <th>Plan Name</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($plans)): ?>
                                        <tr><td colspan="7" class="text-center py-4">No plans configured yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($plans as $plan): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo clean($plan['plan_name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo clean($plan['description']); ?></small>
                                                </td>
                                                <td><span class="badge badge-info"><?php echo clean($plan['plan_type']); ?></span></td>
                                                <td><strong><?php echo format_currency($plan['price']); ?></strong></td>
                                                <td><?php echo $plan['duration_days']; ?> Days</td>
                                                <td>
                                                    <?php if ($plan['is_active']): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo format_date($plan['created_at']); ?></td>
                                                <td>
                                                    <a href="edit.php?id=<?php echo $plan['plan_id']; ?>" 
                                                       class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="icon-pencil"></i>
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
