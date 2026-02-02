<?php
session_start();
if(!isset($_SESSION['userdata'])){ header("Location: login.php"); exit; }
require_once('../db_connect.php');

$id = $_SESSION['userdata']['id'];
$usertype = $_SESSION['userdata']['type']; // Get User Type
$msg = "";
$msg_type = "";

$qry = $conn->query("SELECT * FROM users WHERE id = $id");
$meta = $qry->fetch_assoc();
$display_img = !empty($meta['avatar']) ? '../'.$meta['avatar'] : 'https://via.placeholder.com/150';

if(isset($_POST['update_profile'])){
    $username = $_POST['username'];
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];
    
    $avatar_path = $meta['avatar']; 
    if(!empty($_FILES['img']['name'])){
        $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
        $new_name = "admin_".$id."_".time().".".$ext;
        $upload_target = "../uploads/".$new_name;
        
        if(move_uploaded_file($_FILES['img']['tmp_name'], $upload_target)){
            $avatar_path = "uploads/".$new_name;
            $_SESSION['userdata']['avatar'] = $avatar_path;
        }
    }

    $update_success = false;

    if(!empty($new_pass)){
        if(md5($current_pass) !== $meta['password']){
            $msg = "Error: The current password you entered is incorrect.";
            $msg_type = "danger";
        } 
        elseif($new_pass !== $confirm_pass){
            $msg = "Error: New password and Confirmation do not match.";
            $msg_type = "danger";
        } else {
            $pass_enc = md5($new_pass);
            $stmt = $conn->prepare("UPDATE users SET username=?, password=?, avatar=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $pass_enc, $avatar_path, $id);
            if($stmt->execute()) $update_success = true;
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, avatar=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $avatar_path, $id);
        if($stmt->execute()) $update_success = true;
    }

    if($update_success){
        $msg = "Profile updated successfully!";
        $msg_type = "success";
        $_SESSION['userdata']['username'] = $username;
        
        $qry = $conn->query("SELECT * FROM users WHERE id = $id");
        $meta = $qry->fetch_assoc();
        $display_img = !empty($meta['avatar']) ? '../'.$meta['avatar'] : 'https://via.placeholder.com/150';
    } elseif(empty($msg)) {
        $msg = "An error occurred while saving.";
        $msg_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loving Bloom | My Profile</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  
  <style>
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      .text-navy { color: #001f3f !important; }
      
      .profile-user-img {
          width: 120px; height: 120px; object-fit: cover;
          border: 3px solid #adb5bd;
      }
      .card { border: none; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">
  
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 shadow-sm">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block">
          <a href="../index.php" target="_blank" class="nav-link text-primary">
              <i class="fas fa-external-link-alt mr-1"></i> View Website
          </a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto"><li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li></ul>
  </nav>

  <?php include 'includes/sidebar.php'; ?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
              <h1 class="m-0 text-navy">
                  <?php echo ($usertype == 1) ? 'Administrator Profile' : 'Staff Profile'; ?>
              </h1>
          </div>
        </div>
      </div>
    </div>
    
    <div class="content">
      <div class="container-fluid">
        <div class="row d-flex align-items-stretch">
            
            <div class="col-md-4 d-flex flex-column">
                
                <div class="card card-primary card-outline shadow-sm mb-3">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" src="<?php echo $display_img; ?>" alt="User profile picture" id="sidebar_img_preview">
                        </div>
                        <h3 class="profile-username text-center"><?php echo ucwords($meta['username']); ?></h3>
                        
                        <p class="text-muted text-center">
                            <?php echo ($usertype == 1) ? 'System Administrator' : 'Staff Member'; ?>
                        </p>
                        
                        <hr>
                        
                        <?php if($usertype == 1): ?>
                            <strong><i class="fas fa-shield-alt mr-1"></i> Admin Privileges</strong>
                            <p class="text-muted">Full Access to Billing, Enrollment, and System Configuration.</p>
                        <?php else: ?>
                            <strong><i class="fas fa-user-tag mr-1"></i> Staff Privileges</strong>
                            <p class="text-muted">Restricted access to daily operations.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($usertype == 1): ?>
                <div class="card card-navy shadow-sm flex-fill">
                    <div class="card-header"><h3 class="card-title">Administrative Actions</h3></div>
                    <div class="card-body p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="user_list.php" class="nav-link">
                                    <i class="fas fa-users-cog mr-2"></i> Manage Staff Privileges
                                    <span class="float-right badge bg-primary">Go</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="fee_structure.php" class="nav-link">
                                    <i class="fas fa-file-invoice-dollar mr-2"></i> Update Fee Structures
                                    <span class="float-right badge bg-info">Go</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-8 d-flex flex-column">
                <div class="card shadow-sm border-0 flex-fill">
                    <div class="card-header p-2 bg-white border-bottom-0">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Account Settings</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($msg)): ?>
                            <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
                        <?php endif; ?>
                        
                        <form class="form-horizontal" action="" method="POST" enctype="multipart/form-data" onsubmit="return validatePassword()">
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Username</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="username" value="<?php echo $meta['username']; ?>" required>
                                </div>
                            </div>
                            
                            <hr>
                            <p class="text-muted mb-3"><i class="fas fa-lock"></i> <b>Change Password</b> (Optional)</p>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">New Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Leave blank if not changing">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Confirm New Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Re-type new password">
                                    <small id="pass_match_msg" class="text-danger" style="display:none; font-weight:bold;">Passwords do not match!</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-danger">Current Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control border-danger" name="current_password" id="current_password" placeholder="Required ONLY if changing password">
                                    <small class="text-muted">For security, you must enter your current password to set a new one.</small>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Profile Photo</label>
                                <div class="col-sm-9">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))">
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                    </div>
                                    <div class="mt-2">
                                        <img src="" alt="" id="cimg" class="img-fluid img-thumbnail" style="max-height: 150px; display: none;">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="offset-sm-3 col-sm-9">
                                    <button type="submit" name="update_profile" class="btn btn-danger shadow-sm">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer no-print">
        <div class="float-right d-none d-sm-inline">Loving Bloom Admin</div>
        <strong>Copyright &copy; <?php echo date("Y"); ?> <a href="/loving_bloom/index.php">Loving Bloom</a>.</strong> All rights reserved.
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

<script>
$(function () {
  bsCustomFileInput.init();
});

function displayImg(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cimg').attr('src', e.target.result);
            $('#cimg').show();
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function validatePassword() {
    var newPass = document.getElementById('new_password').value;
    var confirmPass = document.getElementById('confirm_password').value;
    var currentPass = document.getElementById('current_password').value;
    var msg = document.getElementById('pass_match_msg');

    if(newPass !== "") {
        if(newPass !== confirmPass) {
            msg.style.display = 'block';
            document.getElementById('confirm_password').classList.add('is-invalid');
            return false;
        } else {
            msg.style.display = 'none';
            document.getElementById('confirm_password').classList.remove('is-invalid');
        }
        if(currentPass === "") {
            alert("Please enter your Current Password to save changes.");
            document.getElementById('current_password').focus();
            return false;
        }
    }
    return true; 
}
</script>
</body>
</html>