        <!-- Navbar & Hero Start -->
        <div class="container-fluid nav-bar sticky-top px-4 py-2 py-lg-0">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a href="index.php" class="navbar-brand p-0 d-flex align-items-center">
                    <img src="assets/img/logo.jpg" alt="Shivaji Pool Logo" style="height: 60px;">
                    <h1 class="display-6 text-dark">Shivaji Swims</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="index.php" class="nav-item nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>">Home</a>
                        <a href="about.php" class="nav-item nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>">About</a>
                        <!-- <a href="service.php" class="nav-item nav-link <?php echo ($current_page == 'service') ? 'active' : ''; ?>">Service</a> -->
                        <!-- <a href="blog.php" class="nav-item nav-link <?php echo ($current_page == 'blog') ? 'active' : ''; ?>">Blog</a> -->
                        <a href="gallery.php" class="nav-item nav-link <?php echo ($current_page == 'gallery') ? 'active' : ''; ?>">Gallery</a>
                       
                        <a href="contact.php" class="nav-item nav-link <?php echo ($current_page == 'contact') ? 'active' : ''; ?>">Contact</a>
                    </div>
                    <div class="team-icon d-none d-xl-flex justify-content-center me-3">
                        <a class="btn btn-square btn-light rounded-circle mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-square btn-light rounded-circle mx-1" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-square btn-light rounded-circle mx-1" href="https://www.instagram.com/its_shivajis_pool/"><i class="fab fa-instagram"></i></a>
                        <a class="btn btn-square btn-light rounded-circle mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <a href="#" class="btn btn-primary rounded-pill py-2 px-4 flex-shrink-0">Get Started</a>
                </div>
            </nav>
        </div>
        <!-- Navbar & Hero End -->
