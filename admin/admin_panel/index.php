<?php
/**
 * Admin Dashboard - Main Page
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../config/config.php';
require_once '../../db_connect.php';

// Set page title
$page_title = 'Dashboard';

// Include header
include_once '../../includes/admin_header.php';
include_once '../../includes/admin_sidebar.php';
include_once '../../includes/admin_topbar.php';

// Get dashboard statistics
// Today's attendance
$today_attendance_query = "SELECT COUNT(*) as total FROM attendance WHERE attendance_date = CURDATE()";
$today_attendance_result = db_fetch_one($today_attendance_query);
$today_attendance = $today_attendance_result['total'] ?? 0;

// Currently inside (not exited yet)
$currently_inside_query = "SELECT COUNT(*) as total FROM attendance WHERE attendance_date = CURDATE() AND exit_time IS NULL";
$currently_inside_result = db_fetch_one($currently_inside_query);
$currently_inside = $currently_inside_result['total'] ?? 0;

// Active members (status = ACTIVE AND membership_end_date >= CURDATE())
$active_members_query = "SELECT COUNT(*) as total FROM members 
                        WHERE status = 'ACTIVE' 
                        AND membership_end_date >= CURDATE()";
$active_members_result = db_fetch_one($active_members_query);
$active_members = $active_members_result['total'] ?? 0;

// Total members
$total_members_query = "SELECT COUNT(*) as total FROM members";
$total_members_result = db_fetch_one($total_members_query);
$total_members = $total_members_result['total'] ?? 0;

// This month's revenue
$revenue_query = "SELECT SUM(amount) as total FROM payments WHERE MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())";
$revenue_result = db_fetch_one($revenue_query);
$this_month_revenue = $revenue_result['total'] ?? 0;

// Members expiring in 7 days
$expiring_soon_query = "SELECT COUNT(*) as total FROM members 
                        WHERE status = 'ACTIVE' 
                        AND membership_end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
$expiring_soon_result = db_fetch_one($expiring_soon_query);
$expiring_soon = $expiring_soon_result['total'] ?? 0;

// Expired members (status = EXPIRED OR membership_end_date < CURDATE())
$expired_query = "SELECT COUNT(*) as total FROM members 
                 WHERE status = 'EXPIRED' 
                 OR (status = 'ACTIVE' AND membership_end_date < CURDATE())";
$expired_result = db_fetch_one($expired_query);
$expired_members = $expired_result['total'] ?? 0;

// Recent members (last 5)
$recent_members_query = "SELECT member_id, member_code, first_name, last_name, phone, status, registration_date 
                         FROM members 
                         ORDER BY registration_date DESC 
                         LIMIT 5";
$recent_members = db_fetch_all($recent_members_query);

// Recent attendance (last 10)
$recent_attendance_query = "SELECT a.*, m.member_code, m.first_name, m.last_name 
                            FROM attendance a 
                            JOIN members m ON a.member_id = m.member_id 
                            WHERE a.attendance_date = CURDATE() 
                            ORDER BY a.entry_time DESC 
                            LIMIT 10";
$recent_attendance = db_fetch_all($recent_attendance_query);

?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <!-- Flash Messages -->
        <?php echo display_flash(); ?>
        
        <!--Start Dashboard Content-->
        
        <!--Start Dashboard Content-->
        
        <div class="card bg-transparent shadow-none mt-3 border border-secondary-light">
            <div class="card-content">
                <div class="row row-group m-0">
                    <!-- Today's Attendance -->
                    <div class="col-12 col-lg-6 col-xl-3 border-secondary-light">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h4 class="text-info"><?php echo $today_attendance; ?></h4>
                                    <span class="text-dark">Today's Attendance</span>
                                </div>
                                <div class="align-self-center w-circle-icon rounded bg-info shadow-info">
                                    <i class="icon-calendar text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Currently Inside -->
                    <div class="col-12 col-lg-6 col-xl-3 border-secondary-light">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h4 class="text-danger"><?php echo $currently_inside; ?></h4>
                                    <span class="text-dark">Currently Inside</span>
                                    
                                </div>
                                <div class="align-self-center w-circle-icon rounded bg-danger shadow-danger">
                                    <i class="icon-people text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Members -->
                    <div class="col-12 col-lg-6 col-xl-3 border-secondary-light">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h4 class="text-success"><?php echo $active_members; ?> <small class="text-muted" style="font-size: 0.6em;">/ <?php echo $total_members; ?></small></h4>
                                    <span class="text-dark">Active Members</span>
                                </div>
                                <div class="align-self-center w-circle-icon rounded bg-success shadow-success">
                                    <i class="icon-user text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- This Month Revenue -->
                    <div class="col-12 col-lg-6 col-xl-3 border-secondary-light">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h4 class="text-warning"><?php echo format_currency($this_month_revenue); ?></h4>
                                    <span class="text-dark">Monthly Revenue</span>
                                </div>
                                <div class="align-self-center w-circle-icon rounded bg-warning shadow-warning">
                                    <i class="icon-wallet text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--End Row-->
            </div>
        </div><!--End Card-->
                <!-- Quick Actions Row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="icon-rocket"></i> Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 col-6 mb-3">
                                <a href="<?php echo ADMIN_URL; ?>/members/add.php" class="btn btn-block btn-primary">
                                    <i class="icon-user-follow"></i><br>Add Member
                                </a>
                            </div>
                            <div class="col-md-2 col-6 mb-3">
                                <a href="<?php echo ADMIN_URL; ?>/guests/index.php" class="btn btn-block btn-secondary">
                                    <i class="icon-people"></i><br>Guests
                                </a>
                            </div>
                            <div class="col-md-2 col-6 mb-3">
                                <a href="<?php echo ADMIN_URL; ?>/payments/add.php" class="btn btn-block btn-warning">
                                    <i class="icon-credit-card"></i><br>Add Payment
                                </a>
                            </div>
                            <div class="col-md-2 col-6 mb-3">
                                <a href="<?php echo ADMIN_URL; ?>/batches/index.php" class="btn btn-block btn-danger">
                                    <i class="icon-clock"></i><br>Batches
                                </a>
                            </div>
                            <div class="col-md-2 col-6 mb-3">
                                <a href="<?php echo ADMIN_URL; ?>/members/index.php" class="btn btn-block btn-info">
                                    <i class="icon-list"></i><br>View All Members
                                </a>
                            </div>
                           
                             <div class="col-md-2 col-6 mb-3">
                                <a href="<?php echo ADMIN_URL; ?>/reports/attendance.php" class="btn btn-block btn-success">
                                    <i class="icon-calendar"></i><br>Attendance Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--End Row-->    
        
         <!-- Attendance QR Modal -->
         <div class="modal fade" id="qrModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-primary">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white"><i class="fa fa-qrcode"></i> Entry/Exit QR Scan</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center p-5">
                        <div class="mb-4">
                            <?php $qr_link = BASE_URL . '/mark_attendance.php'; ?>
                            <div class="bg-light p-3 rounded mb-3">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo urlencode($qr_link); ?>" 
                                        alt="Reception QR" class="img-fluid border p-2 shadow-sm bg-white">
                            </div>
                            <div class="alert alert-dark border-0 shadow-sm">
                                <h6 class="text-uppercase small mb-2 font-weight-bold">Today's Reception PIN</h6>
                                <h1 class="display-4 font-weight-bold text-warning mb-0" style="letter-spacing: 15px;"><?php echo Attendance::get_daily_pin(); ?></h1>
                            </div>
                        </div>
                        <h5 class="text-primary"><?php echo clean(POOL_NAME); ?></h5>
                        <p class="text-dark small mb-1"><strong><i class="icon-screen-smartphone"></i> Support Permanent Link</strong></p>
                        <p class="text-muted small">Members can scan this QR and enter their ID with the PIN shown above.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="location.reload()"><i class="icon-refresh"></i> Refresh QR</button>
                    </div>
                </div>
            </div>
        </div>

         <!-- Recent Data Row -->
        <div class="row">
            <!-- Recent Members -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="icon-user"></i> Recent Registrations
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Member Code</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_members)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No members registered yet</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($recent_members as $member): ?>
                                    <tr>
                                        <td><strong><?php echo clean($member['member_code']); ?></strong></td>
                                        <td><?php echo clean($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                        <td><?php echo clean($member['phone']); ?></td>
                                        <td><?php echo format_date($member['registration_date']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?php echo ADMIN_URL; ?>/members/add.php" class="btn btn-info btn-sm mt-2">
                            <i class="icon-plus"></i> Add New Member
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Today's Attendance -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                        <span> <i class="icon-calendar"></i> Today's Attendance Log</span> <span class="border px-2 rounded">Today's Code: <?php echo Attendance::get_daily_pin(); ?></span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Entry Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_attendance)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No attendance marked today</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($recent_attendance as $att): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo clean($att['member_code']); ?></strong><br>
                                            <small><?php echo clean($att['first_name'] . ' ' . $att['last_name']); ?></small>
                                        </td>
                                        <td><?php echo format_time($att['entry_time']); ?></td>
                                        <td>
                                            <?php if ($att['exit_time']): ?>
                                                <span class="badge badge-secondary">Exited</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Inside</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?php echo ADMIN_URL; ?>/attendance/today.php" class="btn btn-success btn-sm mt-2">
                            <i class="icon-eye"></i> View Full Dashboard
                        </a>
                        <a href="<?php echo ADMIN_URL; ?>/attendance/mark.php" class="btn btn-outline-secondary btn-sm mt-2 ml-1">
                            <i class="icon-plus"></i> Manual Entry
                        </a>
                        
                        <?php $live_token = Attendance::generate_token(); ?>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2 ml-1" data-toggle="modal" data-target="#qrModal">
                            <i class="icon-camera"></i> Show QR
                        </button>
                                    
                    </div>
                </div>
            </div>
        </div><!--End Row-->
        
        <!-- Alert Cards Row -->
        <div class="row">
            <!-- Expiring Soon -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <i class="icon-exclamation"></i> Memberships Expiring Soon (7 Days)
                    </div>
                    <div class="card-body">
                        <h3 class="text-warning"><?php echo $expiring_soon; ?> Members</h3>
                        <p>Need to send renewal reminders to these members.</p>
                        <a href="<?php echo ADMIN_URL; ?>/members/index.php?filter=expiring" class="btn btn-warning btn-sm">View Details</a>
                    </div>
                </div>
            </div>
            
            <!-- Expired Members -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <i class="icon-close"></i> Expired Memberships
                    </div>
                    <div class="card-body">
                        <h3 class="text-danger"><?php echo $expired_members; ?> Members</h3>
                        <p>These members need to renew their membership.</p>
                        <a href="<?php echo ADMIN_URL; ?>/members/index.php?filter=expired" class="btn btn-danger btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        </div><!--End Row-->
        
       
        

        
        <!--End Dashboard Content-->
        
        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->
        
    </div><!-- End container-fluid-->
</div><!--End content-wrapper-->

<?php
// Include footer
include_once '../../includes/admin_footer.php';
?>
