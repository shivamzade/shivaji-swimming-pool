<?php
/**
 * Guests Listing Page
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Set page title
$page_title = 'Guests Management';

// Pagination and filtering
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 25;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Get guests
$guests_data = Guest::get_all($page, $per_page, $search, $status_filter, $date_filter);
$guests = $guests_data['guests'];
$total = $guests_data['total'];
$last_page = $guests_data['last_page'];

// Get statistics
$stats = Guest::get_statistics();

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $stats['total_guests']; ?></h4>
                        <p class="card-text">Total Guests</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $stats['active_guests']; ?></h4>
                        <p class="card-text">Active Guests</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $stats['today_guests']; ?></h4>
                        <p class="card-text">Today's Guests</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $stats['avg_duration_hours']; ?>h</h4>
                        <p class="card-text">Avg. Duration</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="icon-list"></i> Guests Management
                        <div class="card-action">
                            <a href="add.php" class="btn btn-light btn-sm"><i class="icon-plus"></i> Add Guest</a>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Flash Messages -->
                        <?php echo display_flash(); ?>
                        
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form method="GET" action="" class="form-inline">
                                    <div class="form-group mr-2">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search by name, phone, or code..." 
                                               value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                    <div class="form-group mr-2">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="ACTIVE" <?php echo $status_filter == 'ACTIVE' ? 'selected' : ''; ?>>Active</option>
                                            <option value="CHECKED_OUT" <?php echo $status_filter == 'CHECKED_OUT' ? 'selected' : ''; ?>>Checked Out</option>
                                            <option value="CANCELLED" <?php echo $status_filter == 'CANCELLED' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <input type="date" name="date" class="form-control" 
                                               value="<?php echo htmlspecialchars($date_filter); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="icon-magnifier"></i> Search
                                    </button>
                                    <a href="index.php" class="btn btn-secondary ml-2">
                                        <i class="icon-refresh"></i> Reset
                                    </a>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Guests Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Guest Code</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Type</th>
                                        <th>Visit Date</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($guests)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No guests found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($guests as $guest): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($guest['guest_code']); ?></strong>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']); ?>
                                                    <?php if ($guest['email']): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($guest['email']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($guest['phone']); ?></td>
                                                <td>
                                                    <span class="badge badge-info"><?php echo htmlspecialchars($guest['guest_type']); ?></span>
                                                </td>
                                                <td><?php echo format_date($guest['visit_date']); ?></td>
                                                <td><?php echo format_datetime($guest['check_in_time'], 'H:i'); ?></td>
                                                <td>
                                                    <?php if ($guest['check_out_time']): ?>
                                                        <?php echo format_datetime($guest['check_out_time'], 'H:i'); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($guest['duration_hours']): ?>
                                                        <?php echo round($guest['duration_hours'], 1); ?>h
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($guest['status'] == 'ACTIVE'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php elseif ($guest['status'] == 'CHECKED_OUT'): ?>
                                                        <span class="badge badge-secondary">Checked Out</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Cancelled</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="view.php?id=<?php echo $guest['guest_id']; ?>" 
                                                           class="btn btn-info" title="View">
                                                            <i class="icon-eye"></i>
                                                        </a>
                                                        <?php if ($guest['status'] == 'ACTIVE'): ?>
                                                            <a href="edit.php?id=<?php echo $guest['guest_id']; ?>" 
                                                               class="btn btn-warning" title="Edit">
                                                                <i class="icon-pencil"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    onclick="confirmCheckout(<?php echo $guest['guest_id']; ?>)"
                                                                    class="btn btn-success" title="Check Out">
                                                                <i class="icon-logout"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button" 
                                                                onclick="confirmDelete(<?php echo $guest['guest_id']; ?>)"
                                                                class="btn btn-danger" title="Delete">
                                                            <i class="icon-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total > $per_page): ?>
                            <nav aria-label="Guests pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date=<?php echo urlencode($date_filter); ?>">
                                                Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($last_page, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date=<?php echo urlencode($date_filter); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $last_page): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date=<?php echo urlencode($date_filter); ?>">
                                                Next
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                        
                        <!-- Summary -->
                        <div class="mt-3 text-muted">
                            Showing <?php echo count($guests); ?> of <?php echo $total; ?> guests
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- JavaScript -->
<script>
function confirmCheckout(guestId) {
    if (confirm('Are you sure you want to check out this guest?')) {
        window.location.href = 'checkout.php?id=' + guestId;
    }
}

function confirmDelete(guestId) {
    if (confirm('Are you sure you want to delete this guest? This action cannot be undone.')) {
        window.location.href = 'delete.php?id=' + guestId;
    }
}
</script>

<?php
// Include footer
include_once '../../../includes/admin_footer.php';
?>
