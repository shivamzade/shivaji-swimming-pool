<?php
require_once '../../../config/config.php';
require_once '../../../db_connect.php';
require_once '../../../classes/Batch.php';

// Check authentication and admin role
require_login();
if (!has_role([1, 2])) {
    redirect(ADMIN_URL . '/batches/index.php');
}

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'batch_name' => trim($_POST['batch_name']),
        'start_time' => $_POST['start_time'],
        'end_time' => $_POST['end_time'],
        'description' => trim($_POST['description']),
        'max_capacity' => intval($_POST['max_capacity'])
    ];
    
    // Validation
    $errors = [];
    
    if (empty($data['batch_name'])) {
        $errors[] = "Batch name is required";
    }
    
    if (empty($data['start_time']) || empty($data['end_time'])) {
        $errors[] = "Start time and end time are required";
    }
    
    if ($data['start_time'] >= $data['end_time']) {
        $errors[] = "End time must be after start time";
    }
    
    if ($data['max_capacity'] < 1) {
        $errors[] = "Maximum capacity must be at least 1";
    }
    
    // Check if new capacity is less than current assigned members
    if ($data['max_capacity'] < $batch_details['assigned_members']) {
        $errors[] = "Cannot reduce capacity below current assigned members ({$batch_details['assigned_members']})";
    }
    
    if (empty($errors)) {
        if ($batch->updateBatch($batch_id, $data)) {
            set_flash('success', 'Batch updated successfully!');
            redirect(ADMIN_URL . '/batches/index.php');
        } else {
            $errors[] = "Failed to update batch. Please check if this time slot already exists.";
        }
    }
}

$page_title = "Edit Batch";
include '../../../includes/admin_header.php';
include '../../../includes/admin_sidebar.php';
include '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-sm-6">
                <h4 class="m-0">Edit Batch</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/batches/index.php">Batches</a></li>
                    <li class="breadcrumb-item active">Edit Batch</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Batch Information</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="batch_name">Batch Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="batch_name" name="batch_name" 
                                               value="<?php echo htmlspecialchars($_POST['batch_name'] ?? $batch_details['batch_name']); ?>" 
                                               placeholder="e.g., Morning Batch" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="max_capacity">Maximum Capacity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="max_capacity" name="max_capacity" 
                                               value="<?php echo htmlspecialchars($_POST['max_capacity'] ?? $batch_details['max_capacity']); ?>" 
                                               min="<?php echo $batch_details['assigned_members']; ?>" max="100" required>
                                        <small class="text-muted">Current assigned: <?php echo $batch_details['assigned_members']; ?> members</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="start_time" name="start_time" 
                                               value="<?php echo htmlspecialchars($_POST['start_time'] ?? substr($batch_details['start_time'], 0, 5)); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_time">End Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="end_time" name="end_time" 
                                               value="<?php echo htmlspecialchars($_POST['end_time'] ?? substr($batch_details['end_time'], 0, 5)); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                          placeholder="Optional description about this batch"><?php echo htmlspecialchars($_POST['description'] ?? $batch_details['description']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update Batch
                                </button>
                                <a href="<?php echo ADMIN_URL; ?>/batches/index.php" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                                <a href="<?php echo ADMIN_URL; ?>/batches/view.php?id=<?php echo $batch_id; ?>" class="btn btn-info">
                                    <i class="fa fa-eye"></i> View Batch
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/admin_footer.php'; ?>
