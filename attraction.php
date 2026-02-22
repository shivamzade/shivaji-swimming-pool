<?php require_once 'config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Our Facilities - <?php echo POOL_NAME; ?></title>
        <meta content="swimming pool facilities, shivaji pool shegaon, pool amenities, olympic pool" name="keywords">
        <meta content="Explore our world-class swimming facilities at Shivaji Swimming Pool Shegaon including Olympic-size pool, training areas, and modern amenities." name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wdth,wght@0,75..100,300..800;1,75..100,300..800&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <?php 
        $current_page = 'attraction';
        require_once 'includes/navbar.php'; ?>

        <!-- Header Start -->
        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Our Facilities</h4>
                <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active text-primary">Facilities</li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->

        <!-- Facilities Start -->
        <div class="container-fluid attractions py-5" style="margin-top: 100px;">
            <div class="container attractions-section py-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Our Facilities</h4>
                    <h1 class="display-5 text-white mb-4">Explore Shivaji Swimming Pool</h1>
                    <p class="text-white mb-0">Our state-of-the-art swimming facility in Shegaon offers everything you need for a perfect swimming experience - from Olympic-size pools to modern amenities.
                    </p>
                </div>
                <div class="owl-carousel attractions-carousel wow fadeInUp" data-wow-delay="0.1s">
                    <div class="attractions-item wow fadeInUp" data-wow-delay="0.2s">
                        <img src="img/attraction-1.jpg" class="img-fluid rounded w-100" alt="Olympic Pool">
                        <a href="#" class="attractions-name">Olympic Pool</a>
                    </div>
                    <div class="attractions-item wow fadeInUp" data-wow-delay="0.4s">
                        <img src="img/attraction-2.jpg" class="img-fluid rounded w-100" alt="Training Area">
                        <a href="#" class="attractions-name">Training Area</a>
                    </div>
                    <div class="attractions-item wow fadeInUp" data-wow-delay="0.6s">
                        <img src="img/attraction-3.jpg" class="img-fluid rounded w-100" alt="Kids Pool">
                        <a href="#" class="attractions-name">Kids Pool</a>
                    </div>
                    <div class="attractions-item wow fadeInUp" data-wow-delay="0.8s">
                        <img src="img/attraction-4.jpg" class="img-fluid rounded w-100" alt="Locker Rooms">
                        <a href="#" class="attractions-name">Locker Rooms</a>
                    </div>
                    <div class="attractions-item wow fadeInUp" data-wow-delay="1s">
                        <img src="img/attraction-2.jpg" class="img-fluid rounded w-100" alt="Aqua Fitness">
                        <a href="#" class="attractions-name">Aqua Fitness</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Facilities End -->

        <?php require_once 'includes/footer.php'; ?>
    </body>
</html>
