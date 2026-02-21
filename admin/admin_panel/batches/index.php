<?php
require_once '../../../config/config.php';
require_once '../../../db_connect.php';
require_once '../../../classes/Batch.php';

// Check authentication
require_login();

// Initialize Batch class
$batch = new Batch($conn);

// Get all batches
$batches = $batch->getAllBatches();

$page_title = "Batch Management";
include '../../../includes/admin_header.php';
include '../../../includes/admin_sidebar.php';
include '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <?php echo display_flash(); ?>
        
        <div class="row mt-3">
            <div class="col-sm-6">
                <h4 class="m-0">Batch Management</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Batches</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">All Batches</h3>
                            <div>
                                <?php if (has_role([1, 2])): ?>
                                <a href="add.php" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Add Batch
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($batches)): ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No batches found. 
                                    <?php if (has_role([1, 2])): ?>
                                    <a href="add.php">Add your first batch</a>.
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Batch Name</th>
                                                <th>Time</th>
                                                <th>Capacity</th>
                                                <th>Occupancy</th>
                                                <th>Available Slots</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($batches as $b): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($b['batch_name']); ?></strong>
                                                    <?php if ($b['description']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($b['description']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <i class="fa fa-clock-o"></i> 
                                                    <?php echo date('h:i A', strtotime($b['start_time'])); ?> - 
                                                    <?php echo date('h:i A', strtotime($b['end_time'])); ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info"><?php echo $b['max_capacity']; ?></span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <?php 
                                                        $occupancy = $b['occupancy_percentage'];
                                                        $color = $occupancy >= 90 ? 'danger' : ($occupancy >= 70 ? 'warning' : 'success');
                                                        ?>
                                                        <div class="progress-bar bg-<?php echo $color; ?>" 
                                                             style="width: <?php echo $occupancy; ?>%">
                                                            <?php echo $occupancy; ?>%
                                                        </div>
                                                    </div>
                                                    <small><?php echo $b['assigned_members']; ?> members</small>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $b['available_slots'] > 0 ? 'badge-success' : 'badge-danger'; ?>">
                                                        <?php echo $b['available_slots']; ?> slots
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($b['is_active']): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="view.php?id=<?php echo $b['batch_id']; ?>" 
                                                           class="btn btn-info" title="View Members">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <?php if (has_role([1, 2])): ?>
                                                        <a href="edit.php?id=<?php echo $b['batch_id']; ?>" 
                                                           class="btn btn-warning" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" 
                                                           onclick="toggleStatus(<?php echo $b['batch_id']; ?>)"
                                                           class="btn btn-<?php echo $b['is_active'] ? 'secondary' : 'success'; ?>" 
                                                           title="<?php echo $b['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fa fa-<?php echo $b['is_active'] ? 'pause' : 'play'; ?>"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" 
                                                           onclick="deleteBatch(<?php echo $b['batch_id']; ?>, '<?php echo htmlspecialchars($b['batch_name']); ?>')"
                                                           class="btn btn-danger" title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(batchId) {
    if (confirm('Are you sure you want to toggle the status of this batch?')) {
        window.location.href = 'toggle_status.php?id=' + batchId;
    }
}

function deleteBatch(batchId, batchName) {
    if (confirm(`Are you sure you want to delete the batch "${batchName}"? This action cannot be undone.`)) {
        window.location.href = 'delete.php?id=' + batchId;
    }
}
</script>

<?php include '../../../includes/admin_footer.php'; ?>
