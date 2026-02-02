<?php
session_start();
if(!isset($_SESSION['userdata'])){ header("Location: login.php"); exit; }
if($_SESSION['userdata']['type'] != 1){
    echo "<script>
        alert('Access Denied: You do not have permission to access this page.');
        window.location.href = 'index.php';
    </script>";
    exit; 
}
require_once('../db_connect.php');

$settings = [];
$qry = $conn->query("SELECT * FROM system_info");
while($row = $qry->fetch_assoc()){
    $settings[$row['meta_field']] = $row['meta_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loving Bloom - Website Settings</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">
  
  <style>
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      .text-navy { color: #001f3f !important; }
      .card { border: none; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 shadow-sm">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block">
          <a href="../index.php" target="_blank" class="nav-link text-primary">
              <i class="fas fa-external-link-alt mr-1"></i> View Website
          </a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="nav-link" href="logout.php" role="button"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <?php include 'includes/sidebar.php'; ?>
  
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0 text-navy">Website Content Settings</h1></div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-outline card-navy shadow-sm border-0">
            <div class="card-body">
                <form id="settings-form" enctype="multipart/form-data">
                    
                    <fieldset class="border p-3 mb-3 rounded">
                        <legend class="w-auto px-2 text-primary h6">General Info</legend>
                        <div class="form-group">
                            <label>System Name</label>
                            <input type="text" name="system_name" class="form-control" value="<?php echo $settings['system_name'] ?? '' ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Contact Phone</label>
                                <input type="text" name="contact_phone" class="form-control" value="<?php echo $settings['contact_phone'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Contact Email</label>
                                <input type="email" name="contact_email" class="form-control" value="<?php echo $settings['contact_email'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Address</label>
                                <input type="text" name="contact_address" class="form-control" value="<?php echo $settings['contact_address'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Google Map Link (Embed URL)</label>
                            <textarea name="contact_map" class="form-control" rows="3" placeholder="Paste the src link here..."><?php echo $settings['contact_map'] ?? '' ?></textarea>
                            <small class="text-muted">
                                <strong>Instruction:</strong> Go to Google Maps -> Share -> Embed a Map -> Copy ONLY the link inside the <code>src="..."</code> quotes.
                            </small>
                        </div>
                    </fieldset>

                    <fieldset class="border p-3 mb-3 rounded">
                        <legend class="w-auto px-2 text-primary h6">Home Page Hero</legend>
                        <div class="form-group">
                            <label>Welcome Title</label>
                            <input type="text" name="welcome_title" class="form-control" value="<?php echo $settings['welcome_title'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Welcome Subtitle</label>
                            <textarea name="welcome_content" class="form-control" rows="2"><?php echo $settings['welcome_content'] ?? '' ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Hero Background Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="hero_image" onchange="displayImg(this, 'hero_prev')">
                                <label class="custom-file-label">Choose file</label>
                            </div>
                            <div class="mt-2">
                                <img id="hero_prev" src="../<?php echo $settings['hero_image'] ?? '' ?>" style="height:100px; object-fit:cover; border:1px solid #ccc;">
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border p-3 mb-3 rounded">
                        <legend class="w-auto px-2 text-primary h6">About Us Section</legend>
                        <div class="form-group">
                            <label>About Title</label>
                            <input type="text" name="about_title" class="form-control" value="<?php echo $settings['about_title'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>About Content</label>
                            <textarea name="about_content" class="form-control summernote"><?php echo $settings['about_content'] ?? '' ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>About Section Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="about_image" onchange="displayImg(this, 'about_prev')">
                                <label class="custom-file-label">Choose file</label>
                            </div>
                            <div class="mt-2">
                                <img id="about_prev" src="../<?php echo $settings['about_image'] ?? '' ?>" style="height:100px; object-fit:cover; border:1px solid #ccc;">
                            </div>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm">Update Settings</button>
                </form>
            </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer no-print">
        <div class="float-right d-none d-sm-inline">Powered by <a href="https://github.com/" target="_blank">Grace Muthoni</a></div>
        <strong>Copyright &copy; <?php echo date("Y"); ?> <a href="/loving_bloom/index.php">Loving Bloom</a>.</strong> All rights reserved.
    </footer>
  
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    function displayImg(input, targetId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#'+targetId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function(){
        bsCustomFileInput.init();
        $('.summernote').summernote({ height: 200 });

        $('#settings-form').submit(function(e){
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: '../classes/Master.php?f=update_settings',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp){
                    try {
                        resp = JSON.parse(resp);
                        if(resp.status == 'success'){
                            alert('Settings updated successfully!');
                            location.reload();
                        } else {
                            alert('Error updating settings.');
                        }
                    } catch (e) {
                        alert("Error parsing response: " + resp);
                    }
                }
            })
        })
    })
</script>
</body>
</html>