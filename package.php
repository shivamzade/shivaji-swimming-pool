<?php require_once 'config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Membership Plans - <?php echo POOL_NAME; ?></title>
        <meta content="swimming pool membership, shivaji pool plans, shegaon swimming fees, pool membership" name="keywords">
        <meta content="Choose the perfect membership plan at Shivaji Swimming Pool Shegaon. Affordable plans for individuals, families and students." name="description">

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
        $current_page = 'package';
        require_once 'includes/navbar.php'; ?>

        <!-- Header Start -->
        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Membership Plans</h4>
                <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active text-primary">Membership</li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->

        <!-- Membership Plans Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-12 col-xl-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="packages-item h-100">
                            <h4 class="text-primary">Membership Plans</h4>
                            <h1 class="display-5 mb-4">Choose Your Perfect Swimming Plan</h1>
                            <p class="mb-4">Join Shivaji Swimming Pool in Shegaon with flexible membership options designed for individuals, families, and students. All plans include full access to our Olympic-size pool and certified coach guidance.
                            </p>
                            <p><i class="fa fa-check text-primary me-2"></i>Olympic-size swimming pool</p>
                            <p><i class="fa fa-check text-primary me-2"></i>Certified coach guidance</p>
                            <p><i class="fa fa-check text-primary me-2"></i>Locker room facilities</p>
                            <p class="mb-5"><i class="fa fa-check text-primary me-2"></i>Flexible timing options</p>
                            <a href="contact.php" class="btn btn-primary rounded-pill py-3 px-5"> Contact Us</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="pricing-item bg-dark rounded text-center p-5 h-100">
                            <div class="pb-4 border-bottom">
                                <h2 class="mb-4 text-primary">Family Plan</h2>
                                <p class="mb-4">Perfect for families who want to swim together</p>
                                <h2 class="mb-0 text-primary">₹2,999<span class="text-body fs-5 fw-normal">/month</span></h2>
                            </div>
                            <div class="py-4">
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>Up to 4 family members</p>
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>Unlimited pool access</p>
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>4 Locker facilities</p>
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>Free assessment session</p>
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>Flexible timing</p>
                            </div>
                            <a href="contact.php" class="btn btn-light rounded-pill py-3 px-5"> Join Now</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="pricing-item bg-primary rounded text-center p-5 h-100">
                            <div class="pb-4 border-bottom">
                                <h2 class="text-dark mb-4">Individual Plan</h2>
                                <p class="text-white mb-4">Best for individuals and students</p>
                                <h2 class="text-dark mb-0">₹999<span class="text-white fs-5 fw-normal">/month</span></h2>
                            </div>
                            <div class="text-white py-4">
                                <p class="mb-4"><i class="fa fa-check text-dark me-2"></i>Full pool access</p>
                                <p class="mb-4"><i class="fa fa-check text-dark me-2"></i>Coach guidance</p>
                                <p class="mb-4"><i class="fa fa-check text-dark me-2"></i>1 Locker included</p>
                                <p class="mb-4"><i class="fa fa-check text-dark me-2"></i>Training programs</p>
                            </div>
                            <a href="contact.php" class="btn btn-dark rounded-pill py-3 px-5"> Join Now</a>
                        </div>
                    </div>
                </div>

                <!-- Quarterly and Annual Plans -->
                <div class="row g-5 mt-5">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="pricing-item border border-primary rounded text-center p-5 h-100">
                            <div class="pb-4 border-bottom">
                                <h2 class="mb-4 text-primary">Quarterly Plan</h2>
                                <p class="mb-4">Save more with 3 months commitment</p>
                                <h2 class="mb-0 text-primary">₹2,499<span class="text-body fs-5 fw-normal">/quarter</span></h2>
                            </div>
                            <div class="py-4">
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>Full pool access for 3 months</p>
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>Coach guidance included</p>
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>Locker facility</p>
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>1 Free assessment</p>
                                <p class="mb-4"><i class="fa fa-check text-primary me-2"></i>Save ₹498</p>
                            </div>
                            <a href="contact.php" class="btn btn-primary rounded-pill py-3 px-5"> Join Now</a>
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="pricing-item bg-success rounded text-center p-5 h-100">
                            <div class="pb-4 border-bottom">
                                <h2 class="mb-4 text-white">Annual Plan</h2>
                                <p class="text-white mb-4">Best value - Save big with yearly membership</p>
                                <h2 class="mb-0 text-white">₹8,999<span class="fs-5 fw-normal">/year</span></h2>
                            </div>
                            <div class="text-white py-4">
                                <p class="mb-4"><i class="fa fa-check text-white me-2"></i>Full pool access for 1 year</p>
                                <p class="mb-4"><i class="fa fa-check text-white me-2"></i>Priority coach booking</p>
                                <p class="mb-4"><i class="fa fa-check text-white me-2"></i>Premium locker facility</p>
                                <p class="mb-4"><i class="fa fa-check text-white me-2"></i>4 Free assessments</p>
                                <p class="mb-4"><i class="fa fa-check text-white me-2"></i>Save ₹2,989</p>
                            </div>
                            <a href="contact.php" class="btn btn-light rounded-pill py-3 px-5"> Join Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Membership Plans End -->

        <?php require_once 'includes/footer.php'; ?>
    </body>
</html>
