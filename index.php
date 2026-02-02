<?php
require_once('db_connect.php');

$settings = [];
$chk_table = $conn->query("SHOW TABLES LIKE 'system_info'");
if($chk_table->num_rows > 0){
    $qry = $conn->query("SELECT * FROM system_info");
    while($row = $qry->fetch_assoc()){
        $settings[$row['meta_field']] = $row['meta_value'];
    }
}

$sys_name = $settings['system_name'] ?? 'Loving Bloom';
$welcome_title = $settings['welcome_title'] ?? 'Ensuring the Safety and Well-being of Your Children';
$welcome_content = $settings['welcome_content'] ?? 'A seamless platform for enrollment, attendance tracking, and parent communication.';
$about_title = $settings['about_title'] ?? 'Welcome to Loving Bloom';

$default_about = '<p>At Loving Bloom, we believe in creating a nurturing and supportive environment for your little ones. Our Daycare Management System is designed to streamline communication, enhance safety, and provide you with the tools you need to stay connected with your child\'s daily journey.</p>
<p>We understand that the early years are a critical time for development. That is why our dedicated team focuses on a holistic approach to care, combining structured learning with creative play. From our secure check-in processes to our real-time updates, every detail is crafted to give parents peace of mind while ensuring every child feels loved, valued, and inspired to learn.</p>';

$about_content = $settings['about_content'] ?? $default_about;

$hero_bg = !empty($settings['hero_image']) ? $settings['hero_image'] : 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80';
$about_img = !empty($settings['about_image']) ? $settings['about_image'] : 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60';

$contact_addr = $settings['contact_address'] ?? '123 Daycare Lane, Cityville';
$contact_phone = $settings['contact_phone'] ?? '+1 234 567 890';
$contact_email = $settings['contact_email'] ?? 'admin@lovingbloom.com';

$contact_map = !empty($settings['contact_map']) ? $settings['contact_map'] : "https://maps.google.com/maps?q=".urlencode($contact_addr)."&t=&z=13&ie=UTF8&iwloc=&output=embed";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $sys_name; ?> Daycare</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        html, body { height: auto; min-height: 100%; }
        body { font-family: 'Poppins', sans-serif; scroll-behavior: smooth; overflow-x: hidden; }
        html { scroll-padding-top: 80px; }
        .top-bar { font-size: 0.9rem; }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        
        .navbar-light .navbar-nav .nav-link { color: #555; font-weight: 500; position: relative; padding-bottom: 5px; margin-right: 15px; transition: color 0.3s ease; }
        .navbar-light .navbar-nav .nav-link:hover { color: #f39c12 !important; }
        .navbar-light .navbar-nav .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: 0; left: 0; background-color: #f39c12; transition: width 0.3s ease-in-out; }
        .navbar-light .navbar-nav .nav-link:hover::after { width: 100%; }

        .btn-custom { background-color: #f39c12; border: none; color: #fff; padding: 12px 30px; font-size: 1.1rem; border-radius: 50px; transition: all 0.3s; }
        .btn-custom:hover { background-color: #d35400; transform: translateY(-2px); color: #fff; }
        .btn-enroll-nav { background-color: #f39c12; color: white; border: none; font-weight: 600; }
        .btn-enroll-nav:hover { background-color: #d35400; color: white; }
        .btn-admin-nav { background-color: #001f3f; color: white; border: none; font-weight: 600; }
        .btn-admin-nav:hover { background-color: #001933; color: white; }
        
        .btn-load-more { border: 2px solid #001f3f; color: #001f3f; border-radius: 50px; font-weight: 600; padding: 8px 25px; transition: 0.3s; background: transparent; }
        .btn-load-more:hover { background: #001f3f; color: #fff; }

        .hero-section { background: linear-gradient(rgba(0, 31, 63, 0.8), rgba(0, 31, 63, 0.8)), url('<?php echo $hero_bg; ?>'); background-size: cover; background-position: center; height: 85vh; display: flex; align-items: center; color: white; text-align: center; }
        .about-image img { max-height: 400px; width: 100%; object-fit: cover; border-radius: 15px; }
        
        .program-card, .resource-card, .team-card { transition: transform 0.3s; border: none; }
        .program-card:hover, .resource-card:hover, .team-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .program-card img, .team-card img, .resource-card img { height: 200px; object-fit: cover; width: 100%; }
        
        .feature-icon { font-size: 2.5rem; margin-bottom: 15px; }
        .map-card { height: 100%; min-height: 450px; border-radius: 10px; overflow: hidden; }
        .map-card iframe { width: 100%; height: 100%; border: 0; min-height: 450px; }
        .contact-card { border-radius: 10px; height: 100%; }

        .main-footer.bg-navy { border-top: none; padding: 60px 0 30px; color: #fff; }
        .footer-links a { color: #c2c7d0; display: block; margin-bottom: 10px; transition: color 0.3s; }
        .footer-links a:hover { color: #f39c12; text-decoration: none; padding-left: 5px; }
        .footer-social a { color: #f39c12; margin-right: 15px; font-size: 1.2rem; transition: color 0.3s; }
        .footer-social a:hover { color: #fff; }
        .bottom-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 40px; font-size: 0.9rem; color: #adb5bd; display: flex; justify-content: space-between; align-items: center; }
        
        .hidden-item { display: none; }
    </style>
</head>
<body class="layout-top-nav">
<div class="wrapper">

<?php include 'navbar.php'; ?>

<div class="content-wrapper">
    
    <div class="hero-section">
        <div class="container hero-content" data-aos="fade-in" data-aos-duration="1200">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="font-weight-bold mb-4"><?php echo $welcome_title; ?></h1>
                    <p class="lead mb-5"><?php echo $welcome_content; ?></p>
                    <a href="enrollment.php" class="btn btn-custom shadow" data-aos="fade-up" data-aos-delay="200">Enroll Your Child Now</a>
                </div>
            </div>
        </div>
    </div>

    <div class="content py-5 bg-white" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-up" data-aos-duration="1000">
                    <h3 class="text-navy font-weight-bold mb-3"><?php echo $about_title; ?></h3>
                    <div class="text-muted text-justify">
                        <?php echo html_entity_decode($about_content); ?>
                    </div>
                </div>
                <div class="col-lg-6 text-center about-image" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <img src="<?php echo $about_img; ?>" alt="About Us" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <div class="content py-5 bg-light" id="features">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="text-navy font-weight-bold">Key Features</h2>
            <p class="text-muted">Designed to fit your busy lifestyle and ensure peace of mind.</p>
            <hr class="bg-warning w-25">
        </div>
        <div class="row">
          <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="0">
            <div class="card card-outline card-primary h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="fas fa-bell feature-icon text-primary"></i>
                    <h5 class="font-weight-bold">Real-Time Updates</h5>
                    <p class="text-muted small">Stay informed about your child's daily activities.</p>
                </div>
            </div>
          </div>
          <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card card-outline card-success h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="fas fa-comments feature-icon text-success"></i>
                    <h5 class="font-weight-bold">Easy Communication</h5>
                    <p class="text-muted small">Connect with caregivers easily.</p>
                </div>
            </div>
          </div>
          <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card card-outline card-navy h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="fas fa-lock feature-icon text-navy"></i>
                    <h5 class="font-weight-bold">Secure Access</h5>
                    <p class="text-muted small">Top-notch security for your child's data.</p>
                </div>
            </div>
          </div>
          <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card card-outline card-warning h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt feature-icon text-warning"></i>
                    <h5 class="font-weight-bold">Flexible Scheduling</h5>
                    <p class="text-muted small">Easily manage drop-off and pick-up times.</p>
                </div>
            </div>
          </div>
          <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
            <div class="card card-outline card-info h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="fas fa-book-reader feature-icon text-info"></i>
                    <h5 class="font-weight-bold">Resource Hub</h5>
                    <p class="text-muted small">Access educational materials and parenting tips.</p>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content py-5 bg-white" id="programs">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="text-navy font-weight-bold">Our Programs & Services</h2>
                <p class="text-muted">Explore our diverse range of nurturing programs.</p>
                <hr class="bg-warning w-25">
            </div>

            <div class="row justify-content-center mb-4" data-aos="fade-in">
                <div class="col-lg-6">
                    <div class="input-group">
                        <input type="text" id="programSearch" class="form-control rounded-left" placeholder="Search programs...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-warning text-white rounded-right border-0"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="programsList">
                <?php 
                $prog_qry = $conn->query("SELECT * FROM fee_structure WHERE type IN ('Program', 'Service') OR name LIKE '%Special Needs%' ORDER BY name ASC");
                $p_count = 0;
                $p_limit = 6;
                
                if($prog_qry->num_rows > 0):
                    while($row = $prog_qry->fetch_assoc()):
                        $p_count++;
                        $p_class = ($p_count > $p_limit) ? 'hidden-program hidden-item' : ''; 
                        
                        $img_path = $row['image_path'];
                        if(empty($img_path) || !file_exists($img_path)){
                             if(file_exists('admin/'.$img_path)) $img_path = 'admin/'.$img_path;
                             else $img_path = "https://via.placeholder.com/500x300?text=" . urlencode($row['name']);
                        }
                        $prog_desc = !empty($row['public_description']) ? $row['public_description'] : "Contact us for more details.";
                ?>
                <div class="col-lg-4 col-md-6 mb-4 program-item <?php echo $p_class; ?>" data-aos="fade-up">
                    <div class="card program-card shadow-sm h-100">
                        <img src="<?php echo $img_path; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title font-weight-bold text-navy"><?php echo $row['name']; ?></h5>
                            <p class="card-text text-muted small"><?php echo $prog_desc; ?></p>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div class="col-12 text-center"><p class="text-muted">No programs currently listed.</p></div>
                <?php endif; ?>
            </div>

            <?php if($p_count > $p_limit): ?>
            <div class="row justify-content-center mt-3">
                <button class="btn btn-load-more" id="loadMorePrograms">Load More Programs</button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="content py-5 bg-light" id="resources">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="text-navy font-weight-bold">Learning Resources</h2>
                <p class="text-muted">Downloadable materials to support learning at home.</p>
                <hr class="bg-warning w-25">
            </div>
            
            <div class="row justify-content-between mb-4" data-aos="fade-in">
                <div class="col-md-4 mb-2">
                    <select id="resourceAgeFilter" class="form-control custom-select">
                        <option value="">Filter by Age Group (All)</option>
                        <option value="0 - 12 Months">0 - 12 Months</option>
                        <option value="1 - 2 Years">1 - 2 Years</option>
                        <option value="2 - 3 Years">2 - 3 Years</option>
                        <option value="3 - 4 Years">3 - 4 Years</option>
                        <option value="4 - 5 Years">4 - 5 Years</option>
                        <option value="5+ Years">5+ Years</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="input-group">
                        <input type="text" id="resourceSearch" class="form-control" placeholder="Search resources...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-navy text-white"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="resourcesList">
                <?php 
                $res_qry = $conn->query("SELECT * FROM study_materials ORDER BY date_uploaded DESC");
                $r_count = 0;
                $r_limit = 8;

                if($res_qry->num_rows > 0):
                    while($res = $res_qry->fetch_assoc()):
                        $r_count++;
                        $r_class = ($r_count > $r_limit) ? 'hidden-resource hidden-item' : '';
                        $cover = !empty($res['cover_image']) ? $res['cover_image'] : "dist/img/no-book-cover.png";
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 resource-item <?php echo $r_class; ?>" 
                     data-age="<?php echo $res['class_level']; ?>" 
                     data-title="<?php echo strtolower($res['title'] . ' ' . $res['subject']); ?>"
                     data-aos="fade-up">
                    <div class="card resource-card shadow-sm h-100">
                        <img src="<?php echo $cover; ?>" class="card-img-top" alt="Resource Cover" style="height:180px; object-fit:contain; background:#f0f0f0; padding:10px;">
                        <div class="card-body text-center">
                            <h6 class="font-weight-bold text-dark text-truncate" title="<?php echo $res['title']; ?>"><?php echo $res['title']; ?></h6>
                            <span class="badge badge-info mb-2"><?php echo $res['class_level']; ?></span>
                            <p class="small text-muted"><?php echo $res['subject']; ?></p>
                            <a href="download.php?id=<?php echo $res['id']; ?>" class="btn btn-outline-primary btn-sm btn-block">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div class="col-12 text-center"><p class="text-muted">No resources uploaded yet.</p></div>
                <?php endif; ?>
            </div>

            <?php if($r_count > $r_limit): ?>
            <div class="row justify-content-center mt-3">
                <button class="btn btn-load-more" id="loadMoreResources">Load More Resources</button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="content py-5 bg-white">
        <div class="container">
            <h4 class="mb-4 text-navy border-bottom pb-2 text-center font-weight-bold" data-aos="fade-up">Our Dedicated Team</h4>
            
            <div class="row justify-content-center mb-4" data-aos="fade-in">
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" id="teamSearch" class="form-control" placeholder="Find a staff member...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-navy text-white"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center" id="teamList">
                <?php 
                $team_qry = $conn->query("SELECT * FROM employees WHERE status = 1");
                $t_count = 0;
                $t_limit = 8;

                if($team_qry->num_rows > 0):
                    while($staff = $team_qry->fetch_assoc()):
                        $t_count++;
                        $t_class = ($t_count > $t_limit) ? 'hidden-team hidden-item' : '';

                        $avatar = "https://randomuser.me/api/portraits/lego/1.jpg";
                        if(!empty($staff['avatar'])){
                            if(file_exists($staff['avatar'])) $avatar = $staff['avatar'];
                            elseif(file_exists('admin/'.$staff['avatar'])) $avatar = 'admin/'.$staff['avatar'];
                        }
                        
                        $name = '';
                        if(!empty($staff['firstname']) && !empty($staff['lastname'])) $name = $staff['firstname'] . ' ' . $staff['lastname'];
                        elseif (!empty($staff['fullname'])) $name = $staff['fullname'];
                        else $name = 'Staff Member';

                        $role = $staff['position'] ?? $staff['job_title'] ?? $staff['role'] ?? 'Staff'; 
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 team-item <?php echo $t_class; ?>" data-aos="fade-up">
                    <div class="card team-card border-0 text-center">
                        <img src="<?php echo $avatar; ?>" class="rounded-circle mx-auto d-block mt-3 shadow-sm" style="width:120px; height:120px; object-fit:cover;" alt="<?php echo $name; ?>">
                        <div class="card-body">
                            <h6 class="font-weight-bold team-name"><?php echo $name; ?></h6>
                            <p class="text-muted small text-uppercase font-weight-bold team-role"><?php echo $role; ?></p>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div class="col-12 text-center"><p class="text-muted">Our team is growing!</p></div>
                <?php endif; ?>
            </div>

            <?php if($t_count > $t_limit): ?>
            <div class="row justify-content-center mt-3">
                <button class="btn btn-load-more" id="loadMoreTeam">Load More Team</button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="content py-5 bg-light" id="contact" data-aos="fade-up">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-navy font-weight-bold">Get In Touch</h2>
                <p class="text-muted">We'd love to hear from you. Visit us or send a message.</p>
                <hr class="bg-warning w-25">
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6 mb-4">
                    <div class="card map-card shadow-sm border-0 h-100">
                        <div class="card-body p-0">
                            <iframe src="<?php echo $contact_map; ?>" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-6 mb-4">
                    <div class="card contact-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="font-weight-bold text-navy mb-3">Send us a Message</h5>
                            <form id="contact-form">
                                <div class="form-group">
                                    <label class="small text-muted">Your Name</label>
                                    <input type="text" name="fullname" class="form-control" placeholder="Name" required>
                                </div>
                                <div class="form-group">
                                    <label class="small text-muted">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                                </div>
                                <div class="form-group">
                                    <label class="small text-muted">Subject</label>
                                    <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                                </div>
                                <div class="form-group">
                                    <label class="small text-muted">Message</label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="How can we help you?" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-custom btn-block">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>

</div>

<?php include 'contact_fab.php'; ?>
<?php include 'scroll_to_top.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function(){
        AOS.init({ duration: 800, easing: 'ease-out-cubic', mirror: true, once: false, offset: 50 });
        window.addEventListener('load', function() { AOS.refresh(); });
        $('#contact-form').submit(function(e){
            e.preventDefault();
            var btn = $(this).find('button[type="submit"]');
            var originalText = btn.text();
            btn.text('Sending...').attr('disabled', true);

            $.ajax({
                url: 'classes/Master.php?f=save_message',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp){
                    if(resp.status == 'success'){
                        Swal.fire({
                            icon: 'success',
                            title: 'Message Sent!',
                            text: 'We have received your message and will get back to you soon.',
                            confirmButtonColor: '#001f3f'
                        });
                        $('#contact-form')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error: ' + (resp.msg || 'Unknown error'),
                            confirmButtonColor: '#001f3f'
                        });
                    }
                    btn.text(originalText).attr('disabled', false);
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Could not connect to the server.',
                        confirmButtonColor: '#001f3f'
                    });
                    btn.text(originalText).attr('disabled', false);
                }
            })
        });

        $("#programSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#programsList .program-item").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
            if(value.length > 0) { $("#loadMorePrograms").hide(); } 
            else { $("#loadMorePrograms").show(); }
        });

        $('#loadMorePrograms').click(function(){
            $('.hidden-program').removeClass('hidden-item').slideDown();
            $(this).fadeOut();
        });

        function filterResources() {
            var search = $("#resourceSearch").val().toLowerCase();
            var age = $("#resourceAgeFilter").val();
            $("#resourcesList .resource-item").each(function() {
                var title = $(this).data('title');
                var itemAge = $(this).data('age');
                var matchesSearch = title.indexOf(search) > -1;
                var matchesAge = (age === "") || (itemAge === age);
                if (matchesSearch && matchesAge) { $(this).removeClass('d-none').show(); } 
                else { $(this).addClass('d-none').hide(); }
            });
            if(search.length > 0 || age !== "") {
                $('#loadMoreResources').hide();
                $('.hidden-resource').removeClass('hidden-item').slideDown(); 
            }
        }
        $("#resourceSearch").on("keyup", filterResources);
        $("#resourceAgeFilter").on("change", filterResources);
        $('#loadMoreResources').click(function(){
            $('.hidden-resource').removeClass('hidden-item').slideDown();
            $(this).fadeOut();
        });

        $("#teamSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#teamList .team-item").filter(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(value) > -1)
            });
            if(value.length > 0) { 
                $("#loadMoreTeam").hide(); 
                $('.hidden-team').removeClass('hidden-item').slideDown(); 
            } 
        });
        $('#loadMoreTeam').click(function(){
            $('.hidden-team').removeClass('hidden-item').slideDown();
            $(this).fadeOut();
        });
    });
</script>
</body>
</html>