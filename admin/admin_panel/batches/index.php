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
        
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0"><i class="fa fa-clock-o"></i> Batch Management</h5>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (has_role([1, 2])): ?>
                                <a href="add.php" class="btn btn-light">
                                    <i class="fa fa-plus"></i> Add New Batch
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <?php if (empty($batches)): ?>
                            <div class="text-center text-muted py-4">
                                No batches found. 
                                <?php if (has_role([1, 2])): ?>
                                <a href="add.php">Add your first batch</a>.
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
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
                                        <?php $serial = 1; foreach ($batches as $b): ?>
                                        <tr>
                                            <td><?php echo $serial++; ?></td>
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
                                                <div class="btn-group btn-group-sm" role="group">
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
