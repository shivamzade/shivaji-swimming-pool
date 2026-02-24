<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title><?php echo POOL_NAME; ?> - Best Swimming Pool in Shegaon, Maharashtra</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="swimming pool, swimming coaching, shivaji pool, swimming classes shegaon, swimming training maharashtra" name="keywords">
        <meta content="Professional swimming coaching and recreational swimming facility at Shivaji Swimming Pool in Shegaon, Maharashtra. Learn swimming from certified coaches." name="description">

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
        $current_page = 'home';
        require_once 'includes/navbar.php'; 
        ?>

        <!-- Hero Video Section Start -->
        <div class="hero-video-section position-relative" id="home">
            <!-- Video Background -->
            <video autoplay muted loop playsinline class="hero-video w-100" poster="img/gallery-1.jpg" id="heroVideo">
                <!-- Working Sample Video - Swimming Pool Theme -->
                <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                
                <!-- Fallback image if video not supported -->
                <img src="img/gallery-1.jpg" class="img-fluid w-100" alt="Swimming Pool">
            </video>
            
            <!-- Video Controls (optional) -->
            <div class="video-controls" style="position: absolute; bottom: 100px; right: 30px; z-index: 5;">
                <button onclick="toggleVideo()" class="btn btn-light btn-sm rounded-circle" style="width: 40px; height: 40px;" title="Play/Pause Video">
                    <i class="fa fa-pause" id="videoPlayPauseIcon"></i>
                </button>
            </div>
            
            <!-- Overlay -->
            <div class="hero-overlay"></div>
            
            <!-- Content -->
            <div class="hero-content">
                <div class="container align-items-center py-4">
                    <div class="row g-5 align-items-center">
                        <div class="col-xl-7 fadeInLeft animated" data-animation="fadeInLeft" data-delay="1s" style="animation-delay: 1s;">
                            <div class="text-start">
                                <h4 class="text-warning text-uppercase fw-bold mb-4">Welcome To <?php echo POOL_NAME; ?></h4>
                                <h1 class="display-4 text-uppercase text-white mb-4">Your Premier Swimming Destination in Shegaon</h1>
                                <p class=" text-light mb-4 fs-5">Join Shegaon's finest swimming facility. Whether you're a beginner learning to swim or an athlete training for competitions, our certified coaches and modern facilities are here for you.
                                </p>
                                <div class="d-flex flex-shrink-0">
                                    <a class="btn btn-primary rounded-pill text-white py-3 px-5" href="package.php">Our Packages</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 fadeInRight animated" data-animation="fadeInRight" data-delay="1s" style="animation-delay: 1s;">
                            <div class="ticket-form p-5">
                                <h2 class="text-dark text-uppercase mb-4">Join Now</h2>
                                <form>
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <input type="text" class="form-control border-0 py-2" id="name" placeholder="Your Name">
                                        </div>
                                        <div class="col-12 col-xl-6">
                                            <input type="email" class="form-control border-0 py-2" id="email" placeholder="Your Email">
                                        </div>
                                        <div class="col-12 col-xl-6">
                                            <input type="phone" class="form-control border-0 py-2" id="phone" placeholder="Phone">
                                        </div>
                                        <div class="col-12">
                                            <select class="form-select border-0 py-2" aria-label="Default select example">
                                                <option selected>Select Membership</option>
                                                <option value="1">Monthly Membership</option>
                                                <option value="2">Quarterly Membership</option>
                                                <option value="3">Annual Membership</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <input class="form-control border-0 py-2" type="date">
                                        </div>
                                        <div class="col-12">
                                            <select class="form-select border-0 py-2" aria-label="Select Batch">
                                                <option selected>Preferred Batch</option>
                                                <option value="1">Morning (6:00 AM - 9:00 AM)</option>
                                                <option value="2">Afternoon (2:00 PM - 5:00 PM)</option>
                                                <option value="3">Evening (5:00 PM - 9:00 PM)</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="btn btn-primary w-100 py-2 px-5">Join Now</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
            
            <!-- Down Arrow Button -->
            <a href="#feature-section" class="hero-scroll-btn">
                <i class="bi bi-arrow-down"></i>
            </a>
        </div>
        <!-- Hero Video Section End -->


        <!-- Feature Start -->
        <div class="container-fluid feature py-5" id="feature-section">
            <div class="container py-5">
                <div class="row g-4">
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="feature-item">
                            <img src="img/feature-1.jpg" class="img-fluid rounded w-100" alt="Swimming Pool">
                            <div class="feature-content p-4">
                                <div class="feature-content-inner">
                                    <h4 class="text-white">Olympic-Size Pool</h4>
                                    <p class="text-white">Our 50-meter Olympic-size swimming pool is perfect for both training and recreational swimming, with crystal clear water maintained daily.
                                    </p>
                                    <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Read More <i class="fa fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="feature-item">
                            <img src="img/feature-2.jpg" class="img-fluid rounded w-100" alt="Swimming Training">
                            <div class="feature-content p-4">
                                <div class="feature-content-inner">
                                    <h4 class="text-white">Expert Coaching</h4>
                                    <p class="text-white">Learn from certified swimming coaches with years of experience training beginners to competitive swimmers of all ages.
                                    </p>
                                    <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Read More <i class="fa fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="feature-item">
                            <img src="img/feature-3.jpg" class="img-fluid rounded w-100" alt="Safety Training">
                            <div class="feature-content p-4">
                                <div class="feature-content-inner">
                                    <h4 class="text-white">Safety First</h4>
                                    <p class="text-white">Certified lifeguards on duty at all times, with comprehensive safety protocols to ensure a secure swimming environment.
                                    </p>
                                    <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Read More <i class="fa fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Feature End -->

        <!-- About Start -->
        <div class="container-fluid about pb-5">
            <div class="container pb-5">
                <div class="row g-5">
                    <div class="col-xl-6 wow fadeInUp" data-wow-delay="0.2s">
                        <div>
                            <h4 class="text-primary">About <?php echo POOL_NAME; ?></h4>
                            <h1 class="display-5 mb-4">Your Trusted Swimming Destination in Shegaon</h1>
                            <p class="mb-5">Located in the heart of Shegaon, Maharashtra, Shivaji Swimming Pool has been serving the community for over 20 years. We provide a safe, clean, and professional environment for swimmers of all ages and skill levels. Our modern facility features an Olympic-sized pool, certified trainers, and comprehensive swimming programs to help you achieve your goals.
                            </p>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="me-3"><i class="fas fa-swimmer fa-3x text-primary"></i></div>
                                        <div>
                                            <h4>Expert Coaches</h4>
                                            <p>Certified trainers with years of experience in competitive and beginner swimming.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="me-3"><i class="fas fa-shield-alt fa-3x text-primary"></i></div>
                                        <div>
                                            <h4>Safety Certified</h4>
                                            <p>Full safety protocols with certified lifeguards and modern safety equipment.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="me-3"><i class="fas fa-hand-holding-usd fa-3x text-primary"></i></div>
                                        <div>
                                            <h4>Affordable Plans</h4>
                                            <p>Flexible membership options to suit individuals, families, and students.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="me-3"><i class="fas fa-lock fa-3x text-primary"></i></div>
                                        <div>
                                            <h4>Modern Lockers</h4>
                                            <p>Secure locker rooms with modern amenities for your comfort and safety.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="position-relative rounded">
                            <div class="rounded" style="margin-top: 40px;">
                                <div class="row g-0">
                                    <div class="col-lg-12">
                                        <div class="rounded mb-4">
                                            <img src="img/about.jpg" class="img-fluid rounded w-100" alt="">
                                        </div>
                                        <div class="row gx-4 gy-0">
                                            <div class="col-6">
                                                    <div class="counter-item bg-primary rounded text-center p-4 h-100">
                                                    <div class="counter-item-icon mx-auto mb-3">
                                                        <i class="fas fa-users fa-3x text-white"></i>
                                                    </div>
                                                    <div class="counter-counting mb-3">
                                                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">5000</span>
                                                        <span class="h1 fw-bold text-white"> +</span>
                                                    </div>
                                                    <h5 class="text-white mb-0">Happy Members</h5>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="counter-item bg-dark rounded text-center p-4 h-100">
                                                    <div class="counter-item-icon mx-auto mb-3">
                                                        <i class="fas fa-trophy fa-3x text-white"></i>
                                                    </div>
                                                    <div class="counter-counting mb-3">
                                                        <span class="text-white fs-2 fw-bold" data-toggle="counter-up">150</span>
                                                        <span class="h1 fw-bold text-white"> +</span>
                                                    </div>
                                                    <h5 class="text-white mb-0">Champions Trained</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded bg-primary p-4 position-absolute d-flex justify-content-center" style="width: 90%; height: 80px; top: -40px; left: 50%; transform: translateX(-50%);">
                                <h3 class="mb-0 text-white">20 Years Experience</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->


        <!-- Service Start -->
        <div class="container-fluid service py-5">
            <div class="container service-section py-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Our Services</h4>
                    <h1 class="display-5 text-white mb-4">Comprehensive Swimming Services</h1>
                    <p class="mb-0 text-white">We offer professional swimming training, recreational swimming, and water fitness programs for all age groups and skill levels at Shivaji Swimming Pool.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-0 col-md-1 col-lg-2 col-xl-2"></div>
                    <div class="col-md-10 col-lg-8 col-xl-8 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="service-days p-4">
                            <div class="py-2 border-bottom border-top d-flex align-items-center justify-content-between flex-wrap"><h4 class="mb-0 pb-2 pb-sm-0">Morning Sessions</h4> <p class="mb-0"><i class="fas fa-clock text-primary me-2"></i>06:00 AM - 10:00 AM</p></div>
                            <div class="py-2 border-bottom d-flex align-items-center justify-content-between flex-shrink-1 flex-wrap"><h4 class="mb-0 pb-2 pb-sm-0">Evening Sessions</h4> <p class="mb-0"><i class="fas fa-clock text-primary me-2"></i>04:00 PM - 09:00 PM</p></div>
                            <div class="py-2 border-bottom d-flex align-items-center justify-content-between flex-shrink-1 flex-wrap"><h4 class="mb-0">Weekend Special</h4> <p class="mb-0"><i class="fas fa-clock text-primary me-2"></i>06:00 AM - 09:00 PM</p></div>
                        </div>
                    </div>
                    <div class="col-0 col-md-1 col-lg-2 col-xl-2"></div>

                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="service-item p-4">
                            <div class="service-content">
                                <div class="mb-4">
                                    <i class="fas fa-swimmer fa-4x"></i>
                                </div>
                                <a href="#" class="h4 d-inline-block mb-3">Swimming Training</a>
                                <p class="mb-0">Professional coaching for beginners, intermediate and advanced swimmers with personalized training plans.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="service-item p-4">
                            <div class="service-content">
                                <div class="mb-4">
                                    <i class="fas fa-dumbbell fa-4x"></i>
                                </div>
                                <a href="#" class="h4 d-inline-block mb-3">Aqua Fitness</a>
                                <p class="mb-0">Water aerobics and aqua fitness classes for a full-body workout that's easy on the joints.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="service-item p-4">
                            <div class="service-content">
                                <div class="mb-4">
                                    <i class="fas fa-child fa-4x"></i>
                                </div>
                                <a href="#" class="h4 d-inline-block mb-3">Kids Swimming</a>
                                <p class="mb-0">Special swimming programs for children in a fun, safe environment with experienced instructors.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.8s">
                        <div class="service-item p-4">
                            <div class="service-content">
                                <div class="mb-4">
                                    <i class="fas fa-life-ring fa-4x"></i>
                                </div>
                                <a href="#" class="h4 d-inline-block mb-3">Safety Training</a>
                                <p class="mb-0">Water safety and lifesaving skills training for swimmers of all levels.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Service End -->


        <!-- Membership Packages Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-12 col-xl-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="packages-item h-100">
                            <h4 class="text-primary">Membership Plans</h4>
                            <h1 class="display-5 mb-4">Choose Your Perfect Swimming Plan</h1>
                            <p class="mb-4">Join Shivaji Swimming Pool with flexible membership options designed for individuals, families, and students. All plans include full access to our facilities.
                            </p>
                            <p><i class="fa fa-check text-primary me-2"></i>Olympic-size swimming pool</p>
                            <p><i class="fa fa-check text-primary me-2"></i>Certified coach guidance</p>
                            <p><i class="fa fa-check text-primary me-2"></i>Locker room facilities</p>
                            <p class="mb-5"><i class="fa fa-check text-primary me-2"></i>Flexible timing options</p>
                            <a href="#" class="btn btn-primary rounded-pill py-3 px-5"> Join Now</a>
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
                            <a href="#" class="btn btn-light rounded-pill py-3 px-5"> Join Now</a>
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
                            <a href="#" class="btn btn-dark rounded-pill py-3 px-5"> Join Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Membership Packages End -->


        <!-- Facilities Start -->
        <div class="container-fluid attractions py-5">
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


        <!-- Gallery Start -->
        <div class="container-fluid gallery pb-5">
            <div class="container pb-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Our Gallery</h4>
                    <h1 class="display-5 mb-4">Moments at Shivaji Swimming Pool</h1>
                    <p class="mb-0">Browse through our gallery to see our world-class facilities, swimming sessions, training programs, and happy members enjoying their time at Shivaji Swimming Pool in Shegaon.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="gallery-item">
                            <img src="img/gallery-1.jpg" class="img-fluid rounded w-100 h-100" alt="">
                            <div class="search-icon">
                                <a href="img/gallery-1.jpg" class="btn btn-light btn-lg-square rounded-circle" data-lightbox="Gallery-1"><i class="fas fa-search-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="gallery-item">
                            <img src="img/gallery-2.jpg" class="img-fluid rounded w-100 h-100" alt="">
                            <div class="search-icon">
                                <a href="img/gallery-2.jpg" class="btn btn-light btn-lg-square rounded-circle" data-lightbox="Gallery-2"><i class="fas fa-search-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="gallery-item">
                            <img src="img/gallery-3.jpg" class="img-fluid rounded w-100 h-100" alt="">
                            <div class="search-icon">
                                <a href="img/gallery-3.jpg" class="btn btn-light btn-lg-square rounded-circle" data-lightbox="Gallery-3"><i class="fas fa-search-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="gallery-item">
                            <img src="img/gallery-4.jpg" class="img-fluid rounded w-100 h-100" alt="">
                            <div class="search-icon">
                                <a href="img/gallery-4.jpg" class="btn btn-light btn-lg-square rounded-circle" data-lightbox="Gallery-4"><i class="fas fa-search-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="gallery-item">
                            <img src="img/gallery-5.jpg" class="img-fluid rounded w-100 h-100" alt="">
                            <div class="search-icon">
                                <a href="img/gallery-5.jpg" class="btn btn-light btn-lg-square rounded-circle" data-lightbox="Gallery-5"><i class="fas fa-search-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="gallery-item">
                            <img src="img/gallery-6.jpg" class="img-fluid rounded w-100 h-100" alt="">
                            <div class="search-icon">
                                <a href="img/gallery-6.jpg" class="btn btn-light btn-lg-square rounded-circle" data-lightbox="Gallery-6"><i class="fas fa-search-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Gallery End -->


        <!-- Blog Start -->
        <!-- <div class="container-fluid blog pb-5">
            <div class="container pb-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Our Blog</h4>
                    <h1 class="display-5 mb-4">Latest Blog & Articles</h1>
                    <p class="mb-0">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Tenetur adipisci facilis cupiditate recusandae aperiam temporibus corporis itaque quis facere, numquam, ad culpa deserunt sint dolorem autem obcaecati, ipsam mollitia hic.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="blog-item">
                            <div class="blog-img">
                                <a href="#">
                                    <img src="img/blog-2.jpg" class="img-fluid w-100 rounded-top" alt="Image">
                                </a>
                                <div class="blog-category py-2 px-4">Vacation</div>
                                <div class="blog-date"><i class="fas fa-clock me-2"></i>August 19, 2025</div>
                            </div>
                            <div class="blog-content p-4">
                                <a href="#" class="h4 d-inline-block mb-4">Why Children Dont Like Getting Out Of The Water</a>
                                <p class="mb-4">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Autem, quibusdam eveniet itaque provident sequi deserunt, nam iste eius velit perferendis voluptatem at atque neque soluta reiciendis doloremque.
                                </p>
                                <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Read More <i class="fas fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="blog-item">
                            <div class="blog-img">
                                <a href="#">
                                    <img src="img/blog-3.jpg" class="img-fluid w-100 rounded-top" alt="Image">
                                </a>
                                <div class="blog-category py-2 px-4">Insight</div>
                                <div class="blog-date"><i class="fas fa-clock me-2"></i>August 19, 2025</div>
                            </div>
                            <div class="blog-content p-4">
                                <a href="#" class="h4 d-inline-block mb-4">5 Ways To Enjoy Waterland This Spring Break</a>
                                <p class="mb-4">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Autem, quibusdam eveniet itaque provident sequi deserunt, nam iste eius velit perferendis voluptatem at atque neque soluta reiciendis doloremque.
                                </p>
                                <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Read More <i class="fas fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="blog-item">
                            <div class="blog-img">
                                <a href="#">
                                    <img src="img/blog-1.jpg" class="img-fluid w-100 rounded-top" alt="Image">
                                </a>
                                <div class="blog-category py-2 px-4">Insight</div>
                                <div class="blog-date"><i class="fas fa-clock me-2"></i>August 19, 2025</div>
                            </div>
                            <div class="blog-content p-4">
                                <a href="#" class="h4 d-inline-block mb-4">3 Tips for Your Family Spring Break at Amusement Park</a>
                                <p class="mb-4">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Autem, quibusdam eveniet itaque provident sequi deserunt, nam iste eius velit perferendis voluptatem at atque neque soluta reiciendis doloremque.
                                </p>
                                <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Read More <i class="fas fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Blog End -->


        <!-- Team Start -->
        <div class="container-fluid team pb-5">
            <div class="container pb-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Meet Our Team</h4>
                    <h1 class="display-5 mb-4">Our Dedicated Swimming Coaches</h1>
                    <p class="mb-0">Meet our team of certified swimming instructors and coaches at Shivaji Swimming Pool, dedicated to helping you become a confident swimmer.
                    </p>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="team-item p-4">
                            <div class="team-content">
                                <div class="d-flex justify-content-between border-bottom pb-4">
                                    <div class="text-start">
                                        <h4 class="mb-0">Shivaji Bajare</h4>
                                        <p class="mb-0">Director</p>
                                    </div>
                                    <div>
                                        <img src="img/team-1.jpg" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="Amit Bajare">
                                    </div>
                                </div>
                                <div class="team-icon rounded-pill my-4 p-3">
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-3" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-3" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-3" href=""><i class="fab fa-linkedin-in"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-0" href=""><i class="fab fa-instagram"></i></a>
                                </div>
                                <p class="text-center mb-0">Director at Shivaji Swimming Pool, dedicated to providing the best swimming experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="team-item p-4">
                            <div class="team-content">
                                <div class="d-flex justify-content-between border-bottom pb-4">
                                    <div class="text-start">
                                        <h4 class="mb-0">Amit Bajare</h4>
                                        <p class="mb-0">Sub Director</p>
                                    </div>
                                    <div>
                                        <img src="img/team-2.jpg" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                    </div>
                                </div>
                                <div class="team-icon rounded-pill my-4 p-3">
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-3" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-3" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-3" href=""><i class="fab fa-linkedin-in"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-0" href=""><i class="fab fa-instagram"></i></a>
                                </div>
                                <p class="text-center mb-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem, quibusdam eveniet itaque provident sequi deserunt.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="team-item p-4">
                            <div class="team-content">
                                <div class="d-flex justify-content-between border-bottom pb-4">
                                    <div class="text-start">
                                        <h4 class="mb-0">Michael John</h4>
                                        <p class="mb-0">Profession</p>
                                    </div>
                                    <div>
                                        <img src="img/team-3.jpg" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                    </div>
                                </div>
                                <div class="team-icon rounded-pill my-4 p-3">
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-3" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-3" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-3" href=""><i class="fab fa-linkedin-in"></i></a>
                                    <a class="btn btn-primary btn-sm-square rounded-circle me-0" href=""><i class="fab fa-instagram"></i></a>
                                </div>
                                <p class="text-center mb-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem, quibusdam eveniet itaque provident sequi deserunt.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Team End -->


        <!-- Testimonial Start -->
        <div class="container-fluid testimonial py-5">
            <div class="container py-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Testimonials</h4>
                    <h1 class="display-5 text-white mb-4">Our Clients Riviews</h1>
                    <p class="text-white mb-0">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Tenetur adipisci facilis cupiditate recusandae aperiam temporibus corporis itaque quis facere, numquam, ad culpa deserunt sint dolorem autem obcaecati, ipsam mollitia hic.
                    </p>
                </div>
                <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.2s">
                    <div class="testimonial-item p-4">
                        <p class="text-white fs-4 mb-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos mollitia fugiat, nihil autem reprehenderit aperiam maxime minima consequatur, nam iste eius velit perferendis voluptatem at atque neque soluta reiciendis doloremque.
                        </p>
                        <div class="testimonial-inner">
                            <div class="testimonial-img">
                                <img src="img/testimonial-1.jpg" class="img-fluid" alt="Image">
                                <div class="testimonial-quote btn-lg-square rounded-circle"><i class="fa fa-quote-right fa-2x"></i>
                                </div>
                            </div>
                            <div class="ms-4">
                                <h4>Person Name</h4>
                                <p class="text-start text-white">Profession</p>
                                <div class="d-flex text-primary">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item p-4">
                        <p class="text-white fs-4 mb-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos mollitia fugiat, nihil autem reprehenderit aperiam maxime minima consequatur, nam iste eius velit perferendis voluptatem at atque neque soluta reiciendis doloremque.
                        </p>
                        <div class="testimonial-inner">
                            <div class="testimonial-img">
                                <img src="img/testimonial-2.jpg" class="img-fluid" alt="Image">
                                <div class="testimonial-quote btn-lg-square rounded-circle"><i class="fa fa-quote-right fa-2x"></i>
                                </div>
                            </div>
                            <div class="ms-4">
                                <h4>Person Name</h4>
                                <p class="text-start text-white">Profession</p>
                                <div class="d-flex text-primary">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item p-4">
                        <p class="text-white fs-4 mb-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos mollitia fugiat, nihil autem reprehenderit aperiam maxime minima consequatur, nam iste eius velit perferendis voluptatem at atque neque soluta reiciendis doloremque.
                        </p>
                        <div class="testimonial-inner">
                            <div class="testimonial-img">
                                <img src="img/testimonial-3.jpg" class="img-fluid" alt="Image">
                                <div class="testimonial-quote btn-lg-square rounded-circle"><i class="fa fa-quote-right fa-2x"></i>
                                </div>
                            </div>
                            <div class="ms-4">
                                <h4>Person Name</h4>
                                <p class="text-start text-white">Profession</p>
                                <div class="d-flex text-primary">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Testimonial End -->

        <?php require_once 'includes/footer.php'; ?>
        
        <!-- Video Autoplay Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var video = document.getElementById('heroVideo');
                if (video) {
                    video.play().catch(function(error) {
                        console.log('Autoplay prevented, user interaction needed');
                    });
                }
            });
        </script>
    </body>
</html>