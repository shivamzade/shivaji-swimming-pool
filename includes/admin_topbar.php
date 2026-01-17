<?php
/**
 * Admin Panel Topbar (Navbar)
 * 
 * @package ShivajiPool
 */

$user = get_user_data();
?>

<!--Start topbar header-->
<header class="topbar-nav">
    <nav class="navbar navbar-expand fixed-top gradient-scooter">
        <ul class="navbar-nav mr-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link toggle-menu" href="javascript:void();">
                    <i class="icon-menu menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <h5 class="text-white mb-0 ml-3">Welcome, <?php echo clean($user['full_name']); ?>!</h5>
            </li>
        </ul>
        
        <ul class="navbar-nav align-items-center right-nav-link">
            
            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown-lg">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown" href="javascript:void();">
                    <i class="icon-bell"></i><span class="badge badge-primary badge-up" id="notification-count">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Notifications
                            <span class="badge badge-primary">0</span>
                        </li>
                        <li class="list-group-item text-center">
                            <small class="text-muted">No new notifications</small>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- User Profile Dropdown -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
                    <span class="user-profile">
                        <img src="<?php echo ADMIN_URL; ?>/assets/images/avatars/avatar-17.png" class="img-circle" alt="user avatar">
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li class="dropdown-item user-details">
                        <a href="javaScript:void();">
                            <div class="media">
                                <div class="avatar">
                                    <img class="align-self-start mr-3" src="<?php echo ADMIN_URL; ?>/assets/images/avatars/avatar-17.png" alt="user avatar">
                                </div>
                                <div class="media-body">
                                    <h6 class="mt-2 user-title"><?php echo clean($user['full_name']); ?></h6>
                                    <p class="user-subtitle"><?php echo clean($user['email']); ?></p>
                                    <p class="user-subtitle"><small><strong>Role:</strong> <?php echo clean($user['role_name']); ?></small></p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li class="dropdown-item"><a href="<?php echo ADMIN_URL; ?>/profile.php"><i class="icon-user mr-2"></i> My Profile</a></li>
                    <li class="dropdown-divider"></li>
                    <li class="dropdown-item"><a href="<?php echo ADMIN_URL; ?>/change-password.php"><i class="icon-lock mr-2"></i> Change Password</a></li>
                    <li class="dropdown-divider"></li>
                    <li class="dropdown-item"><a href="<?php echo ADMIN_URL; ?>/logout.php"><i class="icon-power mr-2"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
<!--End topbar header-->

<div class="clearfix"></div>
