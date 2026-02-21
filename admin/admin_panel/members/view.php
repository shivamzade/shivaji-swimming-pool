<?php
/**
 * View Member Profile
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Check if ID is provided
$member_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$member_id) {
    set_flash('error', 'Member ID is required');
    redirect(ADMIN_URL . '/members/index.php');
}

// Get member data
$member = Member::get_by_id($member_id);

if (!$member) {
    set_flash('error', 'Member not found');
    redirect(ADMIN_URL . '/members/index.php');
}

// Set page title
$page_title = 'Member Profile: ' . $member['first_name'] . ' ' . $member['last_name'];

// Include header
include_once '../../../includes/admin_header.php';
include_once '../../../includes/admin_sidebar.php';
include_once '../../../includes/admin_topbar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row mt-3">
            <!-- Profile Sidebar -->
            <div class="col-lg-4">
                <div class="card profile-card-2">
                    <div class="card-img-block text-center pt-4">
                        <div class="avatar-wrapper rounded-circle bg-light d-inline-block shadow-sm" style="width: 150px; height: 150px; line-height: 150px;">
                            <i class="icon-user fa-5x text-secondary" style="vertical-align: middle;"></i>
                        </div>
                    </div>
                    <div class="card-body pt-3 text-center">
                        <h5 class="card-title"><?php echo clean($member['first_name'] . ' ' . $member['last_name']); ?></h5>
                        <p class="card-text text-muted"><?php echo clean($member['member_code']); ?></p>
                        
                        <div class="mb-3">
                            <?php
                            $status_classes = [
                                'ACTIVE' => 'badge-success',
                                'EXPIRED' => 'badge-danger',
                                'SUSPENDED' => 'badge-warning',
                                'INACTIVE' => 'badge-secondary'
                            ];
                            $status_class = $status_classes[$member['status']] ?? 'badge-secondary';
                            ?>
                            <span class="badge <?php echo $status_class; ?> px-3 py-2">
                                <?php echo clean($member['status']); ?>
                            </span>
                        </div>
                        
                        <hr>
                        
                        <div class="row text-left">
                            <div class="col-6"><strong>Registration:</strong></div>
                            <div class="col-6"><?php echo format_date($member['registration_date']); ?></div>
                        </div>
                        <div class="row text-left mt-2">
                            <div class="col-6"><strong>Expiry:</strong></div>
                            <div class="col-6">
                                <?php echo $member['membership_end_date'] ? format_date($member['membership_end_date']) : 'N/A'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top-0 bg-transparent text-center">
                        <div class="btn-group w-100">
                            <a href="edit.php?id=<?php echo $member_id; ?>" class="btn btn-primary">
                                <i class="icon-pencil"></i> Edit
                            </a>
                            <a href="renew.php?id=<?php echo $member_id; ?>" class="btn btn-success">
                                <i class="icon-refresh"></i> Renew
                            </a>
                            <a href="print_form.php?id=<?php echo $member_id; ?>" target="_blank" class="btn btn-warning">
                                <i class="icon-printer"></i> Print
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Membership Status widget -->
                <div class="card mt-3">
                    <div class="card-header bg-dark text-white">Membership Details</div>
                    <div class="card-body">
                        <?php if ($member['plan_id']): ?>
                            <div class="text-center mb-3">
                                <h4 class="mb-1 text-primary"><?php echo clean($member['plan_name']); ?></h4>
                                <span class="badge badge-info"><?php echo clean($member['plan_type']); ?></span>
                            </div>
                            <div class="progress mb-2" style="height: 10px;">
                                <?php
                                $total_days = strtotime($member['end_date']) - strtotime($member['start_date']);
                                $passed_days = time() - strtotime($member['start_date']);
                                $percent = $total_days > 0 ? min(100, max(0, ($passed_days / $total_days) * 100)) : 0;
                                $rem_percent = 100 - $percent;
                                ?>
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percent; ?>%"></div>
                            </div>
                            <p class="text-center small text-muted">Plan Validity: <?php echo format_date($member['start_date']); ?> to <?php echo format_date($member['end_date']); ?></p>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <p class="text-muted mb-3">No active membership plan</p>
                                <a href="renew.php?id=<?php echo $member_id; ?>" class="btn btn-outline-primary btn-sm">Assign Plan</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Details Column -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-primary">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#personal-info"><i class="icon-user"></i> <span class="hidden-xs">Information</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#attendance-history"><i class="icon-calendar"></i> <span class="hidden-xs">Attendance</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#payment-history"><i class="icon-wallet"></i> <span class="hidden-xs">Payments</span></a>
                            </li>
                        </ul>
                        
                        <!-- Tab panes -->
                        <div class="tab-content p-3">
                            <div class="tab-pane active" id="personal-info">
                                <h5 class="mb-3">Personal Details</h5>
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <p class="mb-1 text-muted">Date of Birth</p>
                                        <h6><?php echo format_date($member['date_of_birth']); ?></h6>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="mb-1 text-muted">Gender</p>
                                        <h6><?php echo clean($member['gender']); ?></h6>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3">Contact Information</h5>
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <p class="mb-1 text-muted">Primary Phone</p>
                                        <h6><?php echo clean($member['phone']); ?></h6>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="mb-1 text-muted">Alternate Phone</p>
                                        <h6><?php echo $member['alternate_phone'] ?: 'N/A'; ?></h6>
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <p class="mb-1 text-muted">Email</p>
                                        <h6><?php echo $member['email'] ?: 'N/A'; ?></h6>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3">Batch Assignment</h5>
                                <div class="row mb-4">
                                    <?php
                                    // Get member's batches
                                    $batch_query = "SELECT b.*, mb.assigned_date, mb.remarks 
                                                   FROM batches b 
                                                   JOIN member_batches mb ON b.batch_id = mb.batch_id 
                                                   WHERE mb.member_id = ? AND mb.status = 'ACTIVE' 
                                                   ORDER BY b.start_time";
                                    $member_batches = db_fetch_all($batch_query, 'i', [$member_id]);
                                    ?>
                                    <div class="col-12">
                                        <?php if (empty($member_batches)): ?>
                                            <p class="text-muted">No batch assigned</p>
                                            <?php if (has_role([1, 2])): ?>
                                            <a href="<?php echo ADMIN_URL; ?>/batches/assign_member.php?member_id=<?php echo $member_id; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-plus"></i> Assign to Batch
                                            </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php foreach ($member_batches as $batch): ?>
                                                <div class="alert alert-info mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong><?php echo clean($batch['batch_name']); ?></strong><br>
                                                            <small class="text-muted">
                                                                <i class="fa fa-clock-o"></i> 
                                                                <?php echo date('h:i A', strtotime($batch['start_time'])); ?> - 
                                                                <?php echo date('h:i A', strtotime($batch['end_time'])); ?>
                                                            </small>
                                                            <?php if ($batch['remarks']): ?>
                                                                <br><small><em><?php echo clean($batch['remarks']); ?></em></small>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Assigned: <?php echo format_date($batch['assigned_date']); ?></small>
                                                            <?php if (has_role([1, 2])): ?>
                                                            <br><a href="<?php echo ADMIN_URL; ?>/batches/remove_member.php?batch_id=<?php echo $batch['batch_id']; ?>&member_id=<?php echo $member_id; ?>" 
                                                                   class="btn btn-xs btn-danger" onclick="return confirm('Remove from batch?')">
                                                                <i class="fa fa-trash"></i> Remove
                                                            </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3">Address Details</h5>
                                <div class="row mb-4">
                                    <div class="col-sm-12">
                                        <p class="mb-1 text-muted">Address</p>
                                        <h6><?php echo clean($member['address_line1']); ?><?php echo $member['address_line2'] ? ', ' . clean($member['address_line2']) : ''; ?></h6>
                                        <h6><?php echo clean($member['city']); ?>, <?php echo clean($member['state']); ?> - <?php echo clean($member['pincode']); ?></h6>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3">Identity & Health</h5>
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <p class="mb-1 text-muted">ID Proof (<?php echo clean($member['id_proof_type']); ?>)</p>
                                        <h6><?php echo clean($member['id_proof_number']); ?></h6>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="mb-1 text-muted">Blood Group</p>
                                        <h6><?php echo clean($member['blood_group']); ?></h6>
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <p class="mb-1 text-muted">Medical Conditions</p>
                                        <h6><?php echo $member['medical_conditions'] ?: 'No serious conditions reported'; ?></h6>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3">Emergency Contact</h5>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <p class="mb-1 text-muted">Name</p>
                                        <h6><?php echo clean($member['emergency_contact_name']); ?></h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <p class="mb-1 text-muted">Phone</p>
                                        <h6><?php echo clean($member['emergency_contact_phone']); ?></h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <p class="mb-1 text-muted">Relation</p>
                                        <h6><?php echo clean($member['emergency_contact_relation']); ?></h6>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="attendance-history">
                                <h5 class="mb-3">Record of Last 10 Visits</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Entry Time</th>
                                                <th>Exit Time</th>
                                                <th>Duration</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $att_query = "SELECT * FROM attendance WHERE member_id = ? ORDER BY attendance_date DESC, entry_time DESC LIMIT 10";
                                            $attendance_records = db_fetch_all($att_query, 'i', [$member_id]);
                                            
                                            if (empty($attendance_records)):
                                            ?>
                                                <tr><td colspan="4" class="text-center">No attendance records found</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($attendance_records as $att): ?>
                                                <tr>
                                                    <td><?php echo format_date($att['attendance_date']); ?></td>
                                                    <td><?php echo format_time($att['entry_time']); ?></td>
                                                    <td><?php echo $att['exit_time'] ? format_time($att['exit_time']) : '<span class="text-success">Inside</span>'; ?></td>
                                                    <td><?php echo $att['duration_minutes'] ? $att['duration_minutes'] . ' mins' : '-'; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="payment-history">
                                <h5 class="mb-3">Recent Payments</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Receipt #</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Payment Mode</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $pay_query = "SELECT * FROM payments WHERE member_id = ? ORDER BY payment_date DESC LIMIT 10";
                                            $payments = db_fetch_all($pay_query, 'i', [$member_id]);
                                            
                                            if (empty($payments)):
                                            ?>
                                                <tr><td colspan="5" class="text-center">No payment records found</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($payments as $pay): ?>
                                                <tr>
                                                    <td><?php echo clean($pay['receipt_number']); ?></td>
                                                    <td><?php echo format_date($pay['payment_date']); ?></td>
                                                    <td><?php echo format_currency($pay['amount']); ?></td>
                                                    <td><?php echo clean($pay['payment_method'] ?? $pay['payment_mode'] ?? 'N/A'); ?></td>
                                                    <td><span class="badge badge-success"><?php echo clean($pay['status']); ?></span></td>
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
        
    </div>
</div>

<?php
// Include footer
include_once '../../../includes/admin_footer.php';
?>
