<?php
/**
 * Admin Panel Footer
 * 
 * @package ShivajiPool
 */
?>

    <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
    <!--End Back To Top Button-->
    
    <!--Start footer-->
    <footer class="footer">
        <div class="container">
            <div class="text-center">
                Copyright © <?php echo date('Y'); ?> <?php echo POOL_NAME; ?>
            </div>
        </div>
    </footer>
    <!--End footer-->
    
</div><!--wrapper-->

<!-- Bootstrap core JavaScript-->
<script src="<?php echo ADMIN_URL; ?>/assets/js/jquery.min.js"></script>
<script src="<?php echo ADMIN_URL; ?>/assets/js/popper.min.js"></script>
<script src="<?php echo ADMIN_URL; ?>/assets/js/bootstrap.min.js"></script>

<!-- simplebar js -->
<script src="<?php echo ADMIN_URL; ?>/assets/plugins/simplebar/js/simplebar.js"></script>

<!-- sidebar-menu js -->
<script src="<?php echo ADMIN_URL; ?>/assets/js/sidebar-menu.js"></script>

<!-- Custom scripts -->
<script src="<?php echo ADMIN_URL; ?>/assets/js/app-script.js"></script>

<!-- Page level custom scripts -->
<?php if (isset($custom_scripts)): ?>
    <?php echo $custom_scripts; ?>
<?php endif; ?>

<script>
// Auto-hide alerts after 5 seconds
$(document).ready(function() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>

</body>
</html>
