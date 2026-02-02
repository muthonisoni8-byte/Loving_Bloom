<footer class="main-footer bg-navy pt-5 pb-3">
    <div class="container">
        <div class="row">
            
            <div class="col-lg-5 col-md-12 mb-5 mb-lg-0 text-center text-lg-left">
                
                <h4 class="font-weight-bold mb-3 d-flex align-items-center justify-content-center justify-content-lg-start">
                    <img src="favicon.ico" alt="Logo" class="mr-2"
                         style="width: 30px; height: 30px; object-fit: contain; 
                                filter: invert(1) grayscale(100%) brightness(200%); 
                                mix-blend-mode: screen;">
                    <?php echo $sys_name; ?>
                </h4>
                
                <p class="small text-white-50 pr-lg-5 mb-4">
                    A comprehensive management system designed to streamline daycare operations, enhance communication, and ensure the safety of every child in our care.
                </p>
                
                <div class="footer-social d-flex justify-content-center justify-content-lg-start mt-4">
                    <a href="https://www.facebook.com" target="_blank" class="text-white mr-4"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="https://twitter.com" target="_blank" class="text-white mr-4"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="https://instagram.com" target="_blank" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0 mt-4 mt-lg-0 text-left">
                <h5 class="font-weight-bold mb-3 text-warning">Quick Links</h5>
                <div class="d-flex flex-column align-items-start">
                    <a href="index.php" class="text-white-50 mb-2 text-decoration-none hover-warning">
                        <i class="fas fa-angle-right mr-2 text-warning"></i>Home
                    </a>
                    <a href="enrollment.php" class="text-white-50 mb-2 text-decoration-none hover-warning">
                        <i class="fas fa-angle-right mr-2 text-warning"></i>Online Enrollment
                    </a>
                    <a href="admin/login.php" class="text-white-50 mb-2 text-decoration-none hover-warning">
                        <i class="fas fa-angle-right mr-2 text-warning"></i>Staff Portal
                    </a>
                    <a href="/loving_bloom/index.php#programs" class="text-white-50 mb-2 text-decoration-none hover-warning">
                        <i class="fas fa-angle-right mr-2 text-warning"></i>Programs
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12 mt-4 mt-lg-0 text-left">
                <h5 class="font-weight-bold mb-3 text-warning">Contact Us</h5>
                <ul class="list-unstyled text-white-50 small">
                    <li class="mb-3 d-flex">
                        <i class="fas fa-map-marker-alt mr-3 mt-1 text-warning"></i> 
                        <span><?php echo $contact_addr; ?></span>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-phone mr-3 mt-1 text-warning"></i> 
                        <span><?php echo $contact_phone; ?></span>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-envelope mr-3 mt-1 text-warning"></i> 
                        <span><?php echo $contact_email; ?></span>
                    </li>
                </ul>
                <div class="mt-3">
                    <a href="https://github.com/Martha545/Loving-Bloom-Daycare-Management-System.git" target="_blank" class="btn btn-outline-warning btn-sm rounded-pill px-4">
                        <i class="fab fa-github mr-2"></i> View Project
                    </a>
                </div>
            </div>

        </div>
        
        <hr class="bg-white-50 my-4">
        
        <div class="row bottom-footer">
            <div class="col-md-6 text-center text-lg-left mb-2 mb-md-0">
                <span class="small text-white-50">© <?php echo date("Y"); ?> <strong><?php echo $sys_name; ?></strong>. All rights reserved.</span>
            </div>
            <div class="col-md-6 text-center text-lg-right">
                <span class="small text-white-50">Project By: <span class="text-warning">Grace Muthoni</span></span>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-warning:hover {
        color: #ffc107 !important;
        padding-left: 5px;
        transition: all 0.3s ease;
    }
</style>