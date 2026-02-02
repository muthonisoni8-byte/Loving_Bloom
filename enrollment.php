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
$contact_addr = $settings['contact_address'] ?? '123 Daycare Lane, Cityville';
$contact_phone = $settings['contact_phone'] ?? '+1 234 567 890';
$contact_email = $settings['contact_email'] ?? 'admin@lovingbloom.com';

$services_qry = $conn->query("SELECT * FROM fee_structure WHERE type = 'Service' ORDER BY name ASC");

$programs = [];
$prog_qry = $conn->query("SELECT * FROM fee_structure WHERE type = 'Program'");
while($p = $prog_qry->fetch_assoc()){
    if(stripos($p['name'], 'Infant') !== false) $programs['infant'] = $p['amount'];
    elseif(stripos($p['name'], 'Toddler') !== false) $programs['toddler'] = $p['amount'];
    elseif(stripos($p['name'], 'Preschool') !== false) $programs['preschool'] = $p['amount'];
}

$sn_qry = $conn->query("SELECT amount FROM fee_structure WHERE name LIKE '%Special Needs%' LIMIT 1");
$special_needs_fee = ($sn_qry->num_rows > 0) ? $sn_qry->fetch_assoc()['amount'] : 0;

$is_enrollment_page = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $sys_name; ?> | Online Enrollment</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        
        html { scroll-padding-top: 80px; }
        .top-bar { font-size: 0.9rem; }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        
        .navbar-light .navbar-nav .nav-link { color: #555; font-weight: 500; position: relative; padding-bottom: 5px; margin-right: 15px; transition: color 0.3s ease; }
        .navbar-light .navbar-nav .nav-link:hover { color: #f39c12 !important; }
        .navbar-light .navbar-nav .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: 0; left: 0; background-color: #f39c12; transition: width 0.3s ease-in-out; }
        .navbar-light .navbar-nav .nav-link:hover::after { width: 100%; }

        .btn-enroll-nav { background-color: #f39c12; color: white; border: none; font-weight: 600; }
        .btn-enroll-nav:hover { background-color: #d35400; color: white; }
        .btn-admin-nav { background-color: #001f3f; color: white; border: none; font-weight: 600; }
        .btn-admin-nav:hover { background-color: #001933; color: white; }

        .card-primary.card-outline { border-top: 3px solid #001f3f; }
        .btn-primary { background-color: #001f3f; border-color: #001f3f; }
        .btn-primary:hover { background-color: #f39c12; border-color: #f39c12; }
        
        .service-card { border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 10px; transition: 0.3s; background: #fff; height: 100%; cursor: pointer; }
        .service-card:hover { border-color: #001f3f; background-color: #f8f9fa; }
        .custom-control-label { cursor: pointer; font-weight: 600; width: 100%; }
        .price-tag { float: right; font-weight: bold; color: #28a745; background: #e8f5e9; padding: 2px 8px; border-radius: 4px; font-size: 0.9em; }
        
        .fee-estimation-box { background: #e8f5e9; border: 2px solid #28a745; padding: 15px; padding-right: 20px; border-radius: 8px; text-align: right; color: #155724; min-width: 250px; display: inline-block; }
        .total-amount { font-size: 1.6rem; font-weight: 800; display: block;}
        .fee-breakdown { font-size: 0.85rem; color: #555; display: block; margin-top: 5px; line-height: 1.4; }

        .biometric-box { border: 2px dashed #adb5bd; border-radius: 8px; padding: 20px; text-align: center; background-color: #f8f9fa; }
        .biometric-active { border-color: #28a745; background-color: #e8f5e9; }

        .main-footer.bg-navy { border-top: none; padding: 60px 0 30px; color: #fff; }
        .footer-links a { color: #c2c7d0; display: block; margin-bottom: 10px; transition: color 0.3s; }
        .footer-links a:hover { color: #f39c12; text-decoration: none; padding-left: 5px; }
        .footer-social a { color: #f39c12; margin-right: 15px; font-size: 1.2rem; transition: color 0.3s; }
        .footer-social a:hover { color: #fff; }
        .bottom-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 40px; font-size: 0.9rem; color: #adb5bd; display: flex; justify-content: space-between; align-items: center; }
        @media (max-width: 576px) { .bottom-footer { flex-direction: column; text-align: center; gap: 10px; } }
        
        #scrollTopBtn { display: none; position: fixed; bottom: 30px; right: 30px; z-index: 99; border: none; outline: none; background-color: #f39c12; color: white; cursor: pointer; padding: 10px 15px; border-radius: 5px; font-size: 18px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: background-color 0.3s, transform 0.3s; }
        #scrollTopBtn:hover { background-color: #d35400; transform: translateY(-3px); }
    </style>
</head>
<body class="layout-top-nav">
<div class="wrapper">

  <?php include 'navbar.php'; ?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-12"><h1 class="m-0 text-navy font-weight-bold">Online Enrollment Form</h1></div>
        </div>
      </div>
    </div>

    <div class="content pb-5">
      <div class="container">
        <div class="card card-primary card-outline shadow-lg border-0">
          <div class="card-header bg-white border-bottom-0">
            <h5 class="card-title m-0 font-weight-bold text-dark">Please fill in the details below</h5>
          </div>
          <form id="enrollment-form" enctype="multipart/form-data">
            <div class="card-body">
                
                <h4 class="text-navy border-bottom pb-2 mb-3">Parent/Guardian Information</h4>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>First Name <span class="text-danger">*</span></label>
                        <input type="text" name="parent_firstname" class="form-control" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Middle Name</label>
                        <input type="text" name="parent_middlename" class="form-control">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="parent_lastname" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Contact Number <span class="text-danger">*</span></label>
                        <input type="text" name="parent_contact" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="parent_email" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Home Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="2" required></textarea>
                </div>

                <h4 class="text-navy border-bottom pb-2 mb-3 mt-4">Child Information</h4>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>First Name <span class="text-danger">*</span></label>
                        <input type="text" name="child_firstname" class="form-control" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Middle Name</label>
                        <input type="text" name="child_middlename" class="form-control">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="child_lastname" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="child_dob" id="child_dob" class="form-control" required>
                        <small class="text-muted" id="age_display">Enter DOB to calculate Program Fee.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 form-group">
                        <label>Child's Photo <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="child_photo" name="child_photo" accept="image/*" required>
                            <label class="custom-file-label" for="child_photo">Choose file (Required)</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 form-group">
                        <label>Biometric Registration <small class="text-muted">(Optional)</small></label>
                        <div class="biometric-box" id="bio_box">
                            <i class="fas fa-fingerprint fa-3x text-muted mb-2" id="bio_icon"></i>
                            <p class="text-muted mb-2" id="bio_text">Register fingerprint for automated attendance.</p>
                            
                            <button type="button" class="btn btn-outline-primary" id="btn_scan_finger">
                                <i class="fas fa-qrcode mr-1"></i> Scan Fingerprint
                            </button>
                            
                            <input type="hidden" name="biometric_registered" id="biometric_registered" value="0">
                        </div>
                    </div>
                </div>

                <h4 class="text-navy border-bottom pb-2 mb-3 mt-4">Select Services <span class="text-danger">*</span></h4>
                <p class="text-muted small">Please select the services you require. If none, please select "None".</p>
                
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="service-card bg-light border-warning">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input service-check" type="checkbox" id="service_none" name="service_none" value="none">
                                <label for="service_none" class="custom-control-label">
                                    <i class="fas fa-ban text-muted mr-1"></i> I do not require any additional services
                                </label>
                            </div>
                        </div>
                    </div>

                    <?php 
                    if($services_qry->num_rows > 0):
                        while($row = $services_qry->fetch_assoc()):
                    ?>
                    <div class="col-md-6 mb-2">
                        <div class="service-card">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input service-check real-service" 
                                       type="checkbox" 
                                       id="service_<?php echo $row['id'] ?>" 
                                       name="service_ids[]" 
                                       value="<?php echo $row['id'] ?>"
                                       data-price="<?php echo $row['amount'] ?>">
                                
                                <label for="service_<?php echo $row['id'] ?>" class="custom-control-label">
                                    <?php echo $row['name'] ?>
                                    <span class="price-tag">+ <?php echo number_format($row['amount']) ?></span>
                                </label>
                            </div>
                            <small class="text-muted ml-4 d-block mt-1"><?php echo $row['description'] ?></small>
                        </div>
                    </div>
                    <?php 
                        endwhile; 
                    endif; 
                    ?>
                </div>

                <h4 class="text-navy border-bottom pb-2 mb-3 mt-4">Medical History</h4>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Blood Type <span class="text-danger">*</span></label>
                        <select name="blood_type" class="form-control" required>
                            <option value="">Select...</option>
                            <option value="A+">A+</option>
                            <option value="O+">O+</option>
                            <option value="B+">B+</option>
                            <option value="AB+">AB+</option>
                            <option value="A-">A-</option>
                            <option value="O-">O-</option>
                            <option value="B-">B-</option>
                            <option value="AB-">AB-</option>
                            <option value="Unknown">Unknown</option>
                        </select>
                    </div>
                    <div class="col-md-8 form-group">
                        <label>Allergies (Food, Meds, etc.) <small class="text-muted">(Optional)</small></label>
                        <input type="text" name="allergies" class="form-control" placeholder="e.g., Peanuts, Penicillin (Leave blank if none)">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label>Medical Conditions <span class="text-danger">*</span></label>
                        <textarea name="med_conditions" class="form-control" rows="2" placeholder="e.g., Asthma, Diabetes (Enter 'None' if applicable)" required></textarea>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="custom-control custom-checkbox mb-2">
                            <input class="custom-control-input" type="checkbox" id="has_special_needs" name="has_special_needs" value="1">
                            <label for="has_special_needs" class="custom-control-label font-weight-bold text-danger">
                                My child has Special Needs 
                                <span class="badge badge-danger ml-2">+ <?php echo number_format($special_needs_fee); ?></span>
                            </label>
                        </div>
                        <textarea name="special_needs" id="special_needs_desc" class="form-control" rows="3" placeholder="Please describe the specific care instructions..." disabled style="display:none;"></textarea>
                    </div>
                </div>

            </div>
            
            <div class="card-footer bg-white pb-4">
                <div class="row mb-4">
                    <div class="col-12 d-flex justify-content-end">
                        <div class="fee-estimation-box shadow-sm">
                            <small class="text-uppercase text-muted font-weight-bold">Expected Fee</small>
                            <span class="total-amount" id="total_fee_display">0.00</span>
                            <small class="fee-breakdown" id="fee_breakdown">Enter DOB to start</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow rounded-pill mb-4">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Application
                        </button>
                    </div>
                </div>
            </div>

          </form>
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
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script>
    const programFees = {
        infant: <?php echo $programs['infant'] ?? 0; ?>,
        toddler: <?php echo $programs['toddler'] ?? 0; ?>,
        preschool: <?php echo $programs['preschool'] ?? 0; ?>
    };
    const specialNeedsFee = <?php echo $special_needs_fee; ?>;

    $(document).ready(function(){
        bsCustomFileInput.init();
        $('#btn_scan_finger').click(async function(){
            var btn = $(this);
            
            if (!window.PublicKeyCredential) {
                simulateScan(btn, "Biometrics not supported by this browser. Simulating...");
                return;
            }

            btn.html('<i class="fas fa-spinner fa-spin"></i> Launching Security...');
            btn.prop('disabled', true);

            let challenge = new Uint8Array(32);
            window.crypto.getRandomValues(challenge);

            let userId = new Uint8Array(16);
            window.crypto.getRandomValues(userId);

            const publicKey = {
                challenge: challenge,
                rp: { name: "Loving Bloom Daycare" },
                user: {
                    id: userId,
                    name: "child_" + Date.now(),
                    displayName: "Child Biometric"
                },
                pubKeyCredParams: [
                    { alg: -7, type: "public-key" },
                    { alg: -257, type: "public-key" }
                ],
                timeout: 60000,
                attestation: "none"
            };

            try {
                const credential = await navigator.credentials.create({ publicKey });
                successScan(btn, "Biometric Registered via Windows Hello!");
            } catch (err) {
                console.error(err);
                Swal.fire({
                    title: 'Scanner Not Found',
                    text: 'Windows Hello/Fingerprint scanner not configured or detected. Do you want to simulate a successful scan for this Presentation?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Simulate it',
                    cancelButtonText: 'No, Cancel',
                    confirmButtonColor: '#001f3f'
                }).then((result) => {
                    if (result.isConfirmed) {
                        simulateScan(btn, "Simulating Fingerprint Scan...");
                    } else {
                        btn.html('<i class="fas fa-qrcode mr-1"></i> Scan Fingerprint');
                        btn.prop('disabled', false);
                    }
                });
            }
        });

        function successScan(btn, msg){
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: msg,
                timer: 2000,
                showConfirmButton: false
            });
            $('#bio_box').addClass('biometric-active');
            $('#bio_icon').removeClass('text-muted').addClass('text-success');
            $('#bio_text').html('<b class="text-success">Biometric Registered!</b>');
            $('#biometric_registered').val('1'); 
            btn.hide();
        }

        function simulateScan(btn, msg){
            btn.html('<i class="fas fa-circle-notch fa-spin"></i> ' + msg);
            setTimeout(function(){
                successScan(btn, "Fingerprint Captured (Simulated)");
            }, 2000);
        }

        $('#has_special_needs').change(function(){
            if($(this).is(':checked')){
                $('#special_needs_desc').prop('disabled', false).slideDown();
                $('#special_needs_desc').attr('required', true); 
            } else {
                $('#special_needs_desc').prop('disabled', true).slideUp();
                $('#special_needs_desc').attr('required', false);
                $('#special_needs_desc').val('');
            }
            calculateTotal(); 
        });

        $('#service_none').change(function(){
            if($(this).is(':checked')){
                $('.real-service').prop('checked', false); 
            }
            calculateTotal();
        });

        $('.real-service').change(function(){
            if($(this).is(':checked')){
                $('#service_none').prop('checked', false); 
            }
            calculateTotal();
        });

        $('#child_dob').change(function(){
            calculateTotal();
        });

        function calculateTotal() {
            let total = 0;
            let breakdown = [];
            
            let dobVal = $('#child_dob').val();
            if(dobVal) {
                let dob = new Date(dobVal);
                let today = new Date();
                let ageMonths = (today.getFullYear() - dob.getFullYear()) * 12 + (today.getMonth() - dob.getMonth());
                
                let tuition = 0;
                let programName = "";

                if(ageMonths <= 12) {
                    tuition = programFees.infant;
                    programName = "Infant Care";
                } else if(ageMonths < 36) { 
                    tuition = programFees.toddler;
                    programName = "Toddler Care";
                } else {
                    tuition = programFees.preschool;
                    programName = "Preschool";
                }
                
                total += tuition;
                breakdown.push(programName + ": " + tuition.toLocaleString());
            }

            $('.real-service:checked').each(function(){
                let price = parseFloat($(this).data('price')) || 0;
                total += price;
            });
            if($('.real-service:checked').length > 0){
                breakdown.push("Services Added");
            }

            if($('#has_special_needs').is(':checked')){
                total += specialNeedsFee;
                breakdown.push("Special Needs Care");
            }

            $('#total_fee_display').text(total.toLocaleString('en-US', {minimumFractionDigits: 2}));
            $('#fee_breakdown').html(breakdown.join('<br>') || "Select DOB and Services");
        }

        // --- SUBMIT ---
        $('#enrollment-form').submit(function(e){
            e.preventDefault();
            
            if(!$('#service_none').is(':checked') && $('.real-service:checked').length === 0){
                Swal.fire({
                    icon: 'warning',
                    title: 'Service Selection Required',
                    text: 'Please select a service or check "I do not require any additional services".'
                });
                return;
            }

            var _this = $(this);
            var btn = _this.find('button[type="submit"]');
            btn.attr('disabled',true).html('Sending...');
            
            var formData = new FormData(_this[0]);

            $.ajax({
                url: 'classes/Master.php?f=save_enrollment',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(resp){
                    if(resp.status == 'success'){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: resp.msg,
                            confirmButtonColor: '#001f3f'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: resp.msg || 'An error occurred',
                            confirmButtonColor: '#d33'
                        })
                    }
                    btn.attr('disabled',false).html('<i class="fas fa-paper-plane mr-2"></i> Submit Application');
                },
                error: function(err){
                    console.log(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while connecting to the server.',
                    })
                    btn.attr('disabled',false).html('<i class="fas fa-paper-plane mr-2"></i> Submit Application');
                }
            })
        })
    })
</script>
</body>
</html>