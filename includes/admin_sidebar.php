<?php
/**
 * Admin Panel Sidebar
 * 
 * @package ShivajiPool
 */

// Detect current section from URL path
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_path = $_SERVER['PHP_SELF'];

// Extract the section folder (e.g., 'members', 'batches', 'payments', etc.)
$current_section = '';
if (preg_match('/\/admin_panel\/([^\/]+)\//', $current_path, $matches)) {
    $current_section = $matches[1];
}

// Dashboard is only active when on the admin_panel root index.php (no subfolder)
$is_dashboard = ($current_page === 'index' && $current_section === '');

// Helper function to check if a section is active
function is_section_active($section, $current_section) {
    return $current_section === $section;
}
?>

<!--Start sidebar-wrapper-->
<div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
    <div class="brand-logo">
        <a href="<?php echo ADMIN_URL; ?>/index.php">
            <img src="<?php echo BASE_URL; ?>/assets/img/logo.jpg" class="logo-icon" alt="logo icon" style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;">
            <h5 class="logo-text">Admin Panel</h5>
        </a>
    </div>
    
    <ul class="sidebar-menu do-nicescrol">
        <li class="sidebar-header">MAIN NAVIGATION</li>
        
        <!-- Dashboard -->
        <li class="<?php echo $is_dashboard ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>/index.php" class="waves-effect">
                <i class="icon-home"></i> <span>Dashboard</span>
            </a>
        </li>
        
        <!-- Members Management -->
        <li class="<?php echo is_section_active('members', $current_section) ? 'active' : ''; ?>">
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-people"></i>
                <span>Members</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu" <?php echo is_section_active('members', $current_section) ? 'style="display: block;"' : ''; ?>>
                <li class="<?php echo ($current_section === 'members' && $current_page === 'index') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/members/index.php"><i class="fa fa-circle-o"></i> All Members</a></li>
                <li class="<?php echo ($current_section === 'members' && $current_page === 'add') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/members/add.php"><i class="fa fa-circle-o"></i> Add Member</a></li>
                <!-- <li class="<?php echo ($current_section === 'members' && $current_page === 'renew') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/members/renew.php"><i class="fa fa-circle-o"></i> Renew Membership</a></li> -->
            </ul>
        </li>
        
        <!-- Batches Management -->
        <li class="<?php echo is_section_active('batches', $current_section) ? 'active' : ''; ?>">
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-clock"></i>
                <span>Batches</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu" <?php echo is_section_active('batches', $current_section) ? 'style="display: block;"' : ''; ?>>
                <li class="<?php echo ($current_section === 'batches' && $current_page === 'index') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/batches/index.php"><i class="fa fa-circle-o"></i> All Batches</a></li>
                <?php if (has_role([1, 2])): ?>
                <li class="<?php echo ($current_section === 'batches' && $current_page === 'add') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/batches/add.php"><i class="fa fa-circle-o"></i> Add Batch</a></li>
                <?php endif; ?>
            </ul>
        </li>
        
        <!-- Guests Management -->
        <li class="<?php echo is_section_active('guests', $current_section) ? 'active' : ''; ?>">
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-user-follow"></i>
                <span>Guests</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu" <?php echo is_section_active('guests', $current_section) ? 'style="display: block;"' : ''; ?>>
                <li class="<?php echo ($current_section === 'guests' && $current_page === 'index') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/guests/index.php"><i class="fa fa-circle-o"></i> All Guests</a></li>
                <li class="<?php echo ($current_section === 'guests' && $current_page === 'add') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/guests/add.php"><i class="fa fa-circle-o"></i> Add Guest</a></li>
            </ul>
        </li>
        
        <!-- Attendance -->
        <li class="<?php echo (is_section_active('attendance', $current_section) || ($current_section === 'settings' && $current_page === 'attendance')) ? 'active' : ''; ?>">
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-calendar"></i>
                <span>Attendance</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu" <?php echo (is_section_active('attendance', $current_section) || ($current_section === 'settings' && $current_page === 'attendance')) ? 'style="display: block;"' : ''; ?>>
                <li class="<?php echo ($current_section === 'attendance' && $current_page === 'today') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/attendance/today.php"><i class="fa fa-circle-o"></i> Live Dashboard</a></li>
                <li class="<?php echo ($current_section === 'attendance' && $current_page === 'mark') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/attendance/mark.php"><i class="fa fa-circle-o"></i> Manual Entry (Backup)</a></li>
                <li class="<?php echo ($current_section === 'attendance' && $current_page === 'report') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/attendance/report.php"><i class="fa fa-circle-o"></i> Attendance Reports</a></li>
                <?php if (has_role([1, 2])): // Admin & Super Admin only ?>
                <li class="<?php echo ($current_section === 'settings' && $current_page === 'attendance') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/settings/attendance.php"><i class="fa fa-circle-o"></i> Attendance Settings</a></li>
                <?php endif; ?>
            </ul>
        </li>
        
        <!-- Payments -->
        <li class="<?php echo is_section_active('payments', $current_section) ? 'active' : ''; ?>">
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-wallet"></i>
                <span>Payments</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu" <?php echo is_section_active('payments', $current_section) ? 'style="display: block;"' : ''; ?>>
                <li class="<?php echo ($current_section === 'payments' && $current_page === 'add') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/payments/add.php"><i class="fa fa-circle-o"></i> Add Payment</a></li>
                <li class="<?php echo ($current_section === 'payments' && $current_page === 'index') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/payments/index.php"><i class="fa fa-circle-o"></i> Payment History</a></li>
                <li class="<?php echo ($current_section === 'payments' && $current_page === 'dues') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/payments/dues.php"><i class="fa fa-circle-o"></i> Pending Dues</a></li>
            </ul>
        </li>
        
        <!-- Membership Plans -->
        <li class="<?php echo is_section_active('plans', $current_section) ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>/plans/index.php" class="waves-effect">
                <i class="icon-tag"></i> <span>Membership Plans</span>
            </a>
        </li>
        
        <?php if (has_role([1, 2])): // Super Admin & Admin only ?>
        <!-- Staff Management -->
        <li class="<?php echo is_section_active('staff', $current_section) ? 'active' : ''; ?>">
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-user"></i>
                <span>Staff</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu" <?php echo is_section_active('staff', $current_section) ? 'style="display: block;"' : ''; ?>>
                <li class="<?php echo ($current_section === 'staff' && $current_page === 'index') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/staff/index.php"><i class="fa fa-circle-o"></i> All Staff</a></li>
                <li class="<?php echo ($current_section === 'staff' && $current_page === 'add') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/staff/add.php"><i class="fa fa-circle-o"></i> Add Staff</a></li>
                <li class="<?php echo ($current_section === 'staff' && $current_page === 'shifts') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/staff/shifts.php"><i class="fa fa-circle-o"></i> Shift Management</a></li>
            </ul>
        </li>
        
        <!-- Reports -->
        <li class="<?php echo is_section_active('reports', $current_section) ? 'active' : ''; ?>">
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-chart"></i>
                <span>Reports</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu" <?php echo is_section_active('reports', $current_section) ? 'style="display: block;"' : ''; ?>>
                <li class="<?php echo ($current_section === 'reports' && $current_page === 'daily') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/reports/daily.php"><i class="fa fa-circle-o"></i> Daily Report</a></li>
                <li class="<?php echo ($current_section === 'reports' && $current_page === 'revenue') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/reports/revenue.php"><i class="fa fa-circle-o"></i> Revenue Report</a></li>
                <li class="<?php echo ($current_section === 'reports' && $current_page === 'members') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/reports/members.php"><i class="fa fa-circle-o"></i> Member Report</a></li>
                <li class="<?php echo ($current_section === 'reports' && $current_page === 'attendance') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/reports/attendance.php"><i class="fa fa-circle-o"></i> Attendance Report</a></li>
            </ul>
        </li>
        <?php endif; ?>
        
        <?php if (has_role(1)): // Super Admin only ?>
        <!-- Settings -->
        <li class="<?php echo ($current_section === 'settings' && $current_page === 'index') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>/settings/index.php" class="waves-effect">
                <i class="icon-settings"></i> <span>Settings</span>
            </a>
        </li>
        <?php endif; ?>
        
        <li class="sidebar-header">QUICK LINKS</li>
        <li class="<?php echo ($current_page === 'profile' && $current_section === '') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/profile.php" class="waves-effect"><i class="fa fa-circle-o text-info"></i> <span>My Profile</span></a></li>
        <li class="<?php echo ($current_page === 'change-password' && $current_section === '') ? 'active' : ''; ?>"><a href="<?php echo ADMIN_URL; ?>/change-password.php" class="waves-effect"><i class="fa fa-circle-o text-warning"></i> <span>Change Password</span></a></li>
        <li><a href="<?php echo ADMIN_URL; ?>/logout.php" class="waves-effect"><i class="fa fa-circle-o text-danger"></i> <span>Logout</span></a></li>
    </ul>
</div>
<!--End sidebar-wrapper-->

