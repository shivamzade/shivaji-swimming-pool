<?php
/**
 * Admin Panel Header
 * 
 * @package ShivajiPool
 */

// Require authentication
require_login();

// Get user data
$user = get_user_data();
$page_title = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content="Admin Dashboard - <?php echo POOL_NAME; ?>"/>
    <meta name="author" content=""/>
    <title><?php echo clean($page_title); ?> - <?php echo POOL_NAME; ?></title>
    
    <!--favicon-->
    <link rel="icon" href="<?php echo ADMIN_URL; ?>/assets/images/favicon.ico" type="image/x-icon"/>
    
    <!-- Vector CSS -->
    <link href="<?php echo ADMIN_URL; ?>/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
    
    <!-- simplebar CSS-->
    <link href="<?php echo ADMIN_URL; ?>/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet"/>
    
    <!-- Bootstrap core CSS-->
    <link href="<?php echo ADMIN_URL; ?>/assets/css/bootstrap.min.css" rel="stylesheet"/>
    
    <!-- animate CSS-->
    <link href="<?php echo ADMIN_URL; ?>/assets/css/animate.css" rel="stylesheet" type="text/css"/>
    
    <!-- Icons CSS-->
    <link href="<?php echo ADMIN_URL; ?>/assets/css/icons.css" rel="stylesheet" type="text/css"/>
    
    <!-- Sidebar CSS-->
    <link href="<?php echo ADMIN_URL; ?>/assets/css/sidebar-menu.css" rel="stylesheet"/>
    
    <!-- Custom Style-->
    <link href="<?php echo ADMIN_URL; ?>/assets/css/app-style.css" rel="stylesheet"/>
    
    <!-- Custom Admin Styles -->
    <style>
        .user-info { font-size: 14px; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>

<body>

<!-- Start wrapper-->
<div id="wrapper">
