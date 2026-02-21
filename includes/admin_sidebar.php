<?php
/**
 * Admin Panel Sidebar
 * 
 * @package ShivajiPool
 */

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!--Start sidebar-wrapper-->
<div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
    <div class="brand-logo">
        <a href="<?php echo ADMIN_URL; ?>/index.php">
            <img src="<?php echo ADMIN_URL; ?>/assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
            <h5 class="logo-text"><?php echo POOL_NAME; ?></h5>
        </a>
    </div>
    
    <ul class="sidebar-menu do-nicescrol">
        <li class="sidebar-header">MAIN NAVIGATION</li>
        
        <!-- Dashboard -->
        <li class="<?php echo ($current_page == 'index') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>/index.php" class="waves-effect">
                <i class="icon-home"></i> <span>Dashboard</span>
            </a>
        </li>
        
        <!-- Members Management -->
        <li>
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-people"></i>
                <span>Members</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="<?php echo ADMIN_URL; ?>/members/index.php"><i class="fa fa-circle-o"></i> All Members</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/members/add.php"><i class="fa fa-circle-o"></i> Add Member</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/members/renew.php"><i class="fa fa-circle-o"></i> Renew Membership</a></li>
            </ul>
        </li>
        
        <!-- Batches Management -->
        <li>
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-clock"></i>
                <span>Batches</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="<?php echo ADMIN_URL; ?>/batches/index.php"><i class="fa fa-circle-o"></i> All Batches</a></li>
                <?php if (has_role([1, 2])): ?>
                <li><a href="<?php echo ADMIN_URL; ?>/batches/add.php"><i class="fa fa-circle-o"></i> Add Batch</a></li>
                <?php endif; ?>
            </ul>
        </li>
        
        <!-- Guests Management -->
        <li>
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-user-follow"></i>
                <span>Guests</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="<?php echo ADMIN_URL; ?>/guests/index.php"><i class="fa fa-circle-o"></i> All Guests</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/guests/add.php"><i class="fa fa-circle-o"></i> Add Guest</a></li>
            </ul>
        </li>
        
        <!-- Attendance -->
        <li>
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-calendar"></i>
                <span>Attendance</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="<?php echo ADMIN_URL; ?>/attendance/today.php"><i class="fa fa-circle-o"></i> Live Dashboard</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/attendance/mark.php"><i class="fa fa-circle-o"></i> Manual Entry (Backup)</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/attendance/report.php"><i class="fa fa-circle-o"></i> Attendance Reports</a></li>
                <?php if (has_role([1, 2])): // Admin & Super Admin only ?>
                <li><a href="<?php echo ADMIN_URL; ?>/settings/attendance.php"><i class="fa fa-circle-o"></i> Attendance Settings</a></li>
                <?php endif; ?>
            </ul>
        </li>
        
        <!-- Payments -->
        <li>
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-wallet"></i>
                <span>Payments</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="<?php echo ADMIN_URL; ?>/payments/add.php"><i class="fa fa-circle-o"></i> Add Payment</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/payments/index.php"><i class="fa fa-circle-o"></i> Payment History</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/payments/dues.php"><i class="fa fa-circle-o"></i> Pending Dues</a></li>
            </ul>
        </li>
        
        <!-- Membership Plans -->
        <li>
            <a href="<?php echo ADMIN_URL; ?>/plans/index.php" class="waves-effect">
                <i class="icon-tag"></i> <span>Membership Plans</span>
            </a>
        </li>
        
        <?php if (has_role([1, 2])): // Super Admin & Admin only ?>
        <!-- Staff Management -->
        <li>
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-user"></i>
                <span>Staff</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="<?php echo ADMIN_URL; ?>/staff/index.php"><i class="fa fa-circle-o"></i> All Staff</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/staff/add.php"><i class="fa fa-circle-o"></i> Add Staff</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/staff/shifts.php"><i class="fa fa-circle-o"></i> Shift Management</a></li>
            </ul>
        </li>
        
        <!-- Reports -->
        <li>
            <a href="javaScript:void();" class="waves-effect">
                <i class="icon-chart"></i>
                <span>Reports</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="<?php echo ADMIN_URL; ?>/reports/daily.php"><i class="fa fa-circle-o"></i> Daily Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/reports/revenue.php"><i class="fa fa-circle-o"></i> Revenue Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/reports/members.php"><i class="fa fa-circle-o"></i> Member Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/reports/attendance.php"><i class="fa fa-circle-o"></i> Attendance Report</a></li>
            </ul>
        </li>
        <?php endif; ?>
        
        <?php if (has_role(1)): // Super Admin only ?>
        <!-- Settings -->
        <li>
            <a href="<?php echo ADMIN_URL; ?>/settings/index.php" class="waves-effect">
                <i class="icon-settings"></i> <span>Settings</span>
            </a>
        </li>
        <?php endif; ?>
        
        <li class="sidebar-header">QUICK LINKS</li>
        <li><a href="<?php echo ADMIN_URL; ?>/profile.php" class="waves-effect"><i class="fa fa-circle-o text-info"></i> <span>My Profile</span></a></li>
        <li><a href="<?php echo ADMIN_URL; ?>/change-password.php" class="waves-effect"><i class="fa fa-circle-o text-warning"></i> <span>Change Password</span></a></li>
        <li><a href="<?php echo ADMIN_URL; ?>/logout.php" class="waves-effect"><i class="fa fa-circle-o text-danger"></i> <span>Logout</span></a></li>
    </ul>
</div>
<!--End sidebar-wrapper-->
