<?php
require_once '../../../config/config.php';
require_once '../../../db_connect.php';
require_once '../../../classes/Batch.php';

// Check authentication
require_login();

// Get batch ID
$batch_id = $_GET['id'] ?? 0;
if (!$batch_id) {
    redirect(ADMIN_URL . '/batches/index.php');
}

// Initialize Batch class
$batch = new Batch($conn);

// Get batch details
$batch_details = $batch->getBatchById($batch_id);
if (!$batch_details) {
    set_flash('error', 'Batch not found');
    redirect(ADMIN_URL . '/batches/index.php');
}

// Get batch members
$members = $batch->getBatchMembers($batch_id);

$page_title = "Batch Details - " . htmlspecialchars($batch_details['batch_name']);
include '../../../includes/admin_header.php';
include '../../../includes/admin_sidebar.php';
include '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <?php echo display_flash(); ?>
        
        <div class="row mt-3">
            <div class="col-sm-6">
                <h4 class="m-0">Batch Details</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/batches/index.php">Batches</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($batch_details['batch_name']); ?></li>
                </ol>
            </div>
        </div>

        <!-- Batch Information Card -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Batch Information</h3>
                        <?php if (has_role([1, 2])): ?>
                        <div class="card-action">
                            <a href="edit.php?id=<?php echo $batch_id; ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Batch Name:</strong></td>
                                <td><?php echo htmlspecialchars($batch_details['batch_name']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Time:</strong></td>
                                <td>
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo date('h:i A', strtotime($batch_details['start_time'])); ?> - 
                                    <?php echo date('h:i A', strtotime($batch_details['end_time'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Capacity:</strong></td>
                                <td>
                                    <span class="badge badge-info"><?php echo $batch_details['max_capacity']; ?></span> members
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Assigned Members:</strong></td>
                                <td>
                                    <span class="badge badge-primary"><?php echo $batch_details['assigned_members']; ?></span> members
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Available Slots:</strong></td>
                                <td>
                                    <span class="badge <?php echo $batch_details['available_slots'] > 0 ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo $batch_details['available_slots']; ?> slots
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <?php if ($batch_details['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if ($batch_details['description']): ?>
                            <tr>
                                <td><strong>Description:</strong></td>
                                <td><?php echo htmlspecialchars($batch_details['description']); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Occupancy Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Occupancy Status</h3>
                    </div>
                    <div class="card-body">
                        <?php 
                        $occupancy_percentage = $batch_details['max_capacity'] > 0 
                            ? ($batch_details['assigned_members'] / $batch_details['max_capacity']) * 100 
                            : 0;
                        $color = $occupancy_percentage >= 90 ? 'danger' : ($occupancy_percentage >= 70 ? 'warning' : 'success');
                        ?>
                        <div class="text-center mb-3">
                            <h2><?php echo round($occupancy_percentage); ?>%</h2>
                            <p class="text-muted">Batch Occupancy</p>
                        </div>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-<?php echo $color; ?>" 
                                 style="width: <?php echo $occupancy_percentage; ?>%">
                                <?php echo $batch_details['assigned_members']; ?> / <?php echo $batch_details['max_capacity']; ?>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i>
                                <?php if ($batch_details['available_slots'] > 0): ?>
                                    <?php echo $batch_details['available_slots']; ?> slots available for assignment
                                <?php else: ?>
                                    Batch is currently full
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Batch Members -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Assigned Members (<?php echo count($members); ?>)</h3>
                        <?php if (has_role([1, 2]) && $batch_details['available_slots'] > 0): ?>
                        <a href="assign_member.php?id=<?php echo $batch_id; ?>" class="btn btn-primary btn-sm">
                            <i class="fa fa-user-plus"></i> Assign Member
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($members)): ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No members assigned to this batch yet.
                                <?php if (has_role([1, 2]) && $batch_details['available_slots'] > 0): ?>
                                <a href="assign_member.php?id=<?php echo $batch_id; ?>">Assign a member</a>.
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Member Code</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Assigned Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($members as $member): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-secondary"><?php echo htmlspecialchars($member['member_code']); ?></span>
                                            </td>
                                            <td>
                                                <a href="<?php echo ADMIN_URL; ?>/members/view.php?id=<?php echo $member['member_id']; ?>">
                                                    <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($member['phone']); ?></td>
                                            <td><?php echo date('d M Y', strtotime($member['assigned_date'])); ?></td>
                                            <td>
                                                <?php if ($member['status'] === 'ACTIVE'): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($member['status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo ADMIN_URL; ?>/members/view.php?id=<?php echo $member['member_id']; ?>" 
                                                       class="btn btn-info" title="View Member">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if (has_role([1, 2])): ?>
                                                    <a href="javascript:void(0);" 
                                                       onclick="removeMember(<?php echo $member['member_id']; ?>, '<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>')"
                                                       class="btn btn-danger" title="Remove from Batch">
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
function removeMember(memberId, memberName) {
    if (confirm(`Are you sure you want to remove ${memberName} from this batch?`)) {
        window.location.href = 'remove_member.php?batch_id=<?php echo $batch_id; ?>&member_id=' + memberId;
    }
}
</script>

<?php include '../../../includes/admin_footer.php'; ?>
