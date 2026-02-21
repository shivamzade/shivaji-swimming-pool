<?php
require_once '../../../config/config.php';
require_once '../../../db_connect.php';
require_once '../../../classes/Batch.php';
require_once '../../../classes/Member.php';

// Check authentication and admin role
require_login();
if (!has_role([1, 2])) {
    redirect(ADMIN_URL . '/batches/index.php');
}

// Get batch ID and optional member ID
$batch_id = $_GET['id'] ?? 0;
$member_id = $_GET['member_id'] ?? 0;

if (!$batch_id) {
    redirect(ADMIN_URL . '/batches/index.php');
}

// Initialize classes
$batch = new Batch($conn);

// Get batch details
$batch_details = $batch->getBatchById($batch_id);
if (!$batch_details) {
    set_flash('error', 'Batch not found');
    redirect(ADMIN_URL . '/batches/index.php');
}

// Check if batch has available slots
if ($batch_details['available_slots'] <= 0) {
    set_flash('error', 'This batch is currently full. No slots available.');
    redirect(ADMIN_URL . '/batches/view.php?id=' . $batch_id);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_member_id = intval($_POST['member_id'] ?? 0);
    
    if (!$selected_member_id) {
        $error = "Please select a member to assign.";
    } else {
        $result = $batch->assignMemberToBatch($selected_member_id, $batch_id);
        
        if ($result === true) {
            set_flash('success', 'Member assigned to batch successfully!');
            redirect(ADMIN_URL . '/batches/view.php?id=' . $batch_id);
        } elseif ($result === 'already_assigned') {
            $error = "This member is already assigned to this batch.";
        } elseif ($result === 'batch_full') {
            $error = "This batch is currently full.";
        } else {
            $error = "Failed to assign member to batch.";
        }
    }
}

// Get available members for the dropdown (members NOT already in this batch)
$members_sql = "
    SELECT m.member_id, m.member_code, m.first_name, m.last_name, m.phone 
    FROM members m 
    WHERE m.member_id NOT IN (
        SELECT mb.member_id FROM member_batches mb 
        WHERE mb.batch_id = ? AND mb.status = 'ACTIVE'
    )
    AND m.status = 'active'
    ORDER BY m.first_name, m.last_name
";
$stmt = $conn->prepare($members_sql);
$stmt->bind_param('i', $batch_id);
$stmt->execute();
$result = $stmt->get_result();
$available_members = $result->fetch_all(MYSQLI_ASSOC);

$page_title = "Assign Member to " . htmlspecialchars($batch_details['batch_name']);
include '../../../includes/admin_header.php';
include '../../../includes/admin_sidebar.php';
include '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-sm-6">
                <h4 class="m-0">Assign Member to Batch</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/batches/index.php">Batches</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/batches/view.php?id=<?php echo $batch_id; ?>"><?php echo htmlspecialchars($batch_details['batch_name']); ?></a></li>
                    <li class="breadcrumb-item active">Assign Member</li>
                </ol>
            </div>
        </div>

        <!-- Batch Info Summary -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <strong><i class="fa fa-info-circle"></i> Batch:</strong> 
                    <?php echo htmlspecialchars($batch_details['batch_name']); ?> 
                    (<?php echo date('h:i A', strtotime($batch_details['start_time'])); ?> - <?php echo date('h:i A', strtotime($batch_details['end_time'])); ?>)
                    &nbsp;|&nbsp;
                    <strong>Available Slots:</strong> 
                    <span class="badge badge-success"><?php echo $batch_details['available_slots']; ?></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Select Member</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($available_members)): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> 
                                No available members to assign. All active members are already assigned to this batch.
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="member_id">Select Member <span class="text-danger">*</span></label>
                                    <select name="member_id" id="member_id" class="form-control" required>
                                        <option value="">-- Select a Member --</option>
                                        <?php foreach ($available_members as $m): ?>
                                            <option value="<?php echo $m['member_id']; ?>" 
                                                    <?php echo ($member_id == $m['member_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($m['member_code'] . ' - ' . $m['first_name'] . ' ' . $m['last_name'] . ' (' . $m['phone'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">
                                        <?php echo count($available_members); ?> members available for assignment
                                    </small>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-user-plus"></i> Assign Member
                                    </button>
                                    <a href="<?php echo ADMIN_URL; ?>/batches/view.php?id=<?php echo $batch_id; ?>" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Summary Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Batch Summary</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Max Capacity</span>
                                <strong><?php echo $batch_details['max_capacity']; ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Assigned</span>
                                <strong><?php echo $batch_details['assigned_members']; ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Available</span>
                                <strong class="text-success"><?php echo $batch_details['available_slots']; ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/admin_footer.php'; ?>
