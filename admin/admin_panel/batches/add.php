<?php
require_once '../../../config/config.php';
require_once '../../../db_connect.php';
require_once '../../../classes/Batch.php';

// Check authentication and admin role
require_login();
if (!has_role([1, 2])) {
    redirect(ADMIN_URL . '/batches/index.php');
}

// Initialize Batch class
$batch = new Batch($conn);

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
    
    if (empty($errors)) {
        if ($batch->createBatch($data)) {
            set_flash('success', 'Batch created successfully!');
            redirect(ADMIN_URL . '/batches/index.php');
        } else {
            $errors[] = "Failed to create batch. Please check if this time slot already exists.";
        }
    }
}

$page_title = "Add Batch";
include '../../../includes/admin_header.php';
include '../../../includes/admin_sidebar.php';
include '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <div class="col-sm-6">
                <h4 class="m-0">Add New Batch</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_URL; ?>/batches/index.php">Batches</a></li>
                    <li class="breadcrumb-item active">Add Batch</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Batch Information</h3>
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
                                               value="<?php echo htmlspecialchars($_POST['batch_name'] ?? ''); ?>" 
                                               placeholder="e.g., Morning Batch" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="max_capacity">Maximum Capacity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="max_capacity" name="max_capacity" 
                                               value="<?php echo htmlspecialchars($_POST['max_capacity'] ?? '30'); ?>" 
                                               min="1" max="100" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="start_time" name="start_time" 
                                               value="<?php echo htmlspecialchars($_POST['start_time'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_time">End Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="end_time" name="end_time" 
                                               value="<?php echo htmlspecialchars($_POST['end_time'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                          placeholder="Optional description about this batch"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Create Batch
                                </button>
                                <a href="<?php echo ADMIN_URL; ?>/batches/index.php" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('start_time').addEventListener('change', function() {
    const startTime = this.value;
    const endTimeField = document.getElementById('end_time');
    
    // Auto-set end time to 1 hour after start time
    if (startTime) {
        const [hours, minutes] = startTime.split(':').map(Number);
        let endHours = hours + 1;
        let endMinutes = minutes;
        
        if (endHours >= 24) {
            endHours = endHours - 24;
        }
        
        const endTime = `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
        endTimeField.value = endTime;
    }
});
</script>

<?php include '../../../includes/admin_footer.php'; ?>
