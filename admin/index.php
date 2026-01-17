<?php
/**
 * Admin Login Page
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../config/config.php';
require_once '../db_connect.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(BASE_URL . '/admin/admin_panel/index.php');
}

// Handle login form submission
if (is_post_request()) {
    require_csrf_token();
    
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    
    if (empty($username) || empty($password)) {
        set_flash('error', 'Please enter username and password');
    } else {
        $result = Auth::login($username, $password);
        
        if ($result['success']) {
            // Set remember me cookie if checked
            if ($remember_me) {
                setcookie('remember_user', $username, time() + (86400 * 30), '/'); // 30 days
            }
            
            set_flash('success', 'Welcome back, ' . $result['user']['full_name'] . '!');
            
            // Redirect to intended page or dashboard
            $redirect = $_SESSION['redirect_after_login'] ?? BASE_URL . '/admin/admin_panel/index.php';
            unset($_SESSION['redirect_after_login']);
            
            redirect($redirect);
        } else {
            set_flash('error', $result['message']);
        }
    }
}

// Pre-fill username from remember me cookie
$remembered_username = $_COOKIE['remember_user'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content="Admin Login - <?php echo POOL_NAME; ?>"/>
    <meta name="author" content=""/>
    <title>Admin Login - <?php echo POOL_NAME; ?></title>
    
    <!--favicon-->
    <link rel="icon" href="admin_panel/assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap core CSS-->
    <link href="admin_panel/assets/css/bootstrap.min.css" rel="stylesheet"/>
    
    <!-- animate CSS-->
    <link href="admin_panel/assets/css/animate.css" rel="stylesheet" type="text/css"/>
    
    <!-- Icons CSS-->
    <link href="admin_panel/assets/css/icons.css" rel="stylesheet" type="text/css"/>
    
    <!-- Custom Style-->
    <link href="admin_panel/assets/css/app-style.css" rel="stylesheet"/>
    
    <style>
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-authentication1 {
            max-width: 450px;
            width: 100%;
            margin: 2rem;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
<!-- Start wrapper-->
<div id="wrapper">
    <div class="login-wrapper">
        <div class="card border-primary border-top-sm border-bottom-sm card-authentication1 animated bounceInDown">
            <div class="card-body">
                <div class="card-content p-2">
                    <div class="text-center">
                        <img src="admin_panel/assets/images/logo-icon.png" alt="Logo">
                        <div class="logo-text"><?php echo POOL_NAME; ?></div>
                    </div>
                    
                    <div class="card-title text-uppercase text-center py-3">Admin Login</div>
                    
                    <!-- Flash Messages -->
                    <?php echo display_flash(); ?>
                    
                    <form method="POST" action="">
                        <?php echo csrf_token_field(); ?>
                        
                        <div class="form-group">
                            <div class="position-relative has-icon-right">
                                <label for="username" class="sr-only">Username</label>
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       class="form-control form-control-rounded" 
                                       placeholder="Username or Email"
                                       value="<?php echo clean($remembered_username); ?>"
                                       required
                                       autofocus>
                                <div class="form-control-position">
                                    <i class="icon-user"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="position-relative has-icon-right">
                                <label for="password" class="sr-only">Password</label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="form-control form-control-rounded" 
                                       placeholder="Password"
                                       required>
                                <div class="form-control-position">
                                    <i class="icon-lock"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row mr-0 ml-0">
                            <div class="form-group col-12">
                                <div class="demo-checkbox">
                                    <input type="checkbox" 
                                           id="remember_me" 
                                           name="remember_me" 
                                           class="filled-in chk-col-primary"
                                           <?php echo $remembered_username ? 'checked' : ''; ?> />
                                    <label for="remember_me">Remember me</label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary shadow-primary btn-round btn-block waves-effect waves-light">
                            Sign In
                        </button>
                        
                        <div class="text-center pt-3">
                            <p class="text-muted">
                                <small>
                                    <strong>Demo Credentials:</strong><br>
                                    Username: superadmin | Password: Admin@123
                                </small>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
    <!--End Back To Top Button-->
</div><!--wrapper-->

<!-- Bootstrap core JavaScript-->
<script src="admin_panel/assets/js/jquery.min.js"></script>
<script src="admin_panel/assets/js/popper.min.js"></script>
<script src="admin_panel/assets/js/bootstrap.min.js"></script>

<script>
// Auto-focus on password if username is pre-filled
$(document).ready(function() {
    if ($('#username').val() !== '') {
        $('#password').focus();
    }
});
</script>

</body>
</html>
