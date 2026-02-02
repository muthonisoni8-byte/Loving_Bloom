<style>
    /* Desktop Button Styles */
    .btn-enroll-nav {
        background-color: #f39c12; 
        color: #fff;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-enroll-nav:hover {
        background-color: #e67e22;
        color: #fff;
        transform: translateY(-1px);
    }
    .btn-admin-nav {
        background-color: #001f3f; 
        color: #fff;
        border: 1px solid #001f3f;
    }
    .btn-admin-nav:hover {
        background-color: transparent;
        color: #001f3f;
    }

    /* Mobile Styles */
    @media (max-width: 768px) {
        .navbar-collapse {
            position: fixed;
            top: 0;
            right: -100%; 
            height: 100vh; 
            width: 280px;
            background: #ffffff;
            z-index: 1050;
            transition: right 0.3s ease-in-out;
            padding: 20px;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            
            /* Flex layout for mobile menu */
            display: flex !important;
            flex-direction: column; 
            visibility: hidden; 
        }
        
        .navbar-collapse.show {
            right: 0;
            visibility: visible;
        }

        /* Push buttons to the bottom */
        .navbar-nav.ml-auto {
            margin-top: auto !important; 
            width: 100%;
            padding-bottom: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        /* Dark Overlay */
        .navbar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        .navbar-overlay.show {
            display: block;
        }

        /* Close Button Styles */
        .close-menu-btn {
            display: block !important;
            font-size: 1.8rem;
            color: #333;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 20px;
            z-index: 1060;
        }

        /* Nav Link Spacing */
        .navbar-nav.mx-auto {
            margin-top: 50px; 
            width: 100%;
        }
        .navbar-nav.mx-auto .nav-item {
            border-bottom: 1px solid #f8f9fa;
        }
        .navbar-nav.mx-auto .nav-link {
            padding: 15px 0;
            font-size: 1.1rem;
        }

        .top-bar {
            font-size: 0.75rem;
        }
    }

    .close-menu-btn {
        display: none;
    }
</style>

<div class="navbar-overlay" id="navOverlay"></div>

<div class="bg-navy top-bar py-2">
    <div class="container d-flex justify-content-between align-items-center flex-wrap">
        <div class="mb-1 mb-md-0"><i class="fas fa-envelope mr-1"></i> <?php echo $contact_email; ?></div>
        
        <div>
            <span class="mr-3 border-right pr-3 d-none d-md-inline-block">
                <i class="fas fa-phone-alt mr-1"></i> <?php echo $contact_phone; ?>
            </span>
            
            <span class="d-inline-block">
                <a href="https://www.facebook.com" target="_blank" class="text-white mr-2"><i class="fab fa-facebook-f"></i></a>
                <a href="https://instagram.com" target="_blank" class="text-white mr-2"><i class="fab fa-instagram"></i></a>
                <a href="https://twitter.com" target="_blank" class="text-white"><i class="fab fa-twitter"></i></a>
            </span>
        </div>
    </div>
</div>

<nav class="main-header navbar navbar-expand-md navbar-light navbar-white sticky-top border-bottom" style="z-index: 1030;">
    <div class="container">
        <a href="index.php" class="navbar-brand text-navy">
            <img src="favicon.ico" alt="Loving Bloom Logo" class="brand-image img-circle" style="opacity: .9; height: 35px; width: 35px;">
            <span class="brand-text font-weight-bold">Loving Bloom</span>
        </a>
        
        <button class="navbar-toggler border-0" type="button" id="mobileMenuBtn" style="outline: none;">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="close-menu-btn" id="closeMenuBtn">&times;</div>
            
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="index.php#about" class="nav-link">About</a></li>
                <li class="nav-item"><a href="index.php#programs" class="nav-link">Programs</a></li>
                <li class="nav-item"><a href="index.php#resources" class="nav-link">Resources</a></li>
                <li class="nav-item"><a href="index.php#contact" class="nav-link">Contact</a></li>
            </ul>

            <ul class="navbar-nav ml-auto">
                
                <?php if(isset($is_enrollment_page) && $is_enrollment_page === true): ?>
                    <li class="nav-item mr-2 mb-2 mb-md-0">
                        <a href="index.php" class="btn btn-enroll-nav btn-sm rounded-pill px-4 btn-block">
                            <i class="fas fa-home mr-1"></i> Back to Home
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item mr-2 mb-2 mb-md-0">
                        <a href="enrollment.php" class="btn btn-enroll-nav btn-sm rounded-pill px-4 btn-block">Enroll</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="admin/login.php" class="btn btn-admin-nav btn-sm rounded-pill px-4 btn-block">Login</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const closeBtn = document.getElementById('closeMenuBtn');
        const navbar = document.getElementById('navbarCollapse');
        const overlay = document.getElementById('navOverlay');

        function toggleMenu() {
            navbar.classList.toggle('show');
            overlay.classList.toggle('show');
            document.body.style.overflow = navbar.classList.contains('show') ? 'hidden' : '';
        }

        if(mobileBtn) mobileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleMenu();
        });

        if(closeBtn) closeBtn.addEventListener('click', toggleMenu);
        if(overlay) overlay.addEventListener('click', toggleMenu);

        const navLinks = navbar.querySelectorAll('.nav-link, .btn');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if(navbar.classList.contains('show')) {
                    toggleMenu();
                }
            });
        });
    });
</script>