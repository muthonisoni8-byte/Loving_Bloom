<?php
require_once('../db_connect.php');
$settings = [];
$chk = $conn->query("SELECT * FROM system_info");
while($row = $chk->fetch_assoc()){
    $settings[$row['meta_field']] = $row['meta_value'];
}

$bg_image = !empty($settings['hero_image']) ? '../'.$settings['hero_image'] : '../dist/img/default-bg.jpg';
$sys_name = $settings['system_name'] ?? 'Loving Bloom';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $sys_name; ?> | Admin Login</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  
  <style>
    body {
        font-family: 'Source Sans Pro', sans-serif;
        background: url('<?php echo $bg_image; ?>') no-repeat center center fixed;
        background-size: cover;
        height: 100vh;
        overflow: hidden;
    }
    
    body::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 31, 63, 0.96);
        z-index: -1;
    }

    .login-box {
        width: 400px;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        border: none;
        overflow: hidden;
    }

    .card-header {
        background: transparent;
        border-bottom: none;
        padding-top: 30px;
    }

    .brand-logo {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        background: #fff;
        padding: 5px;
    }

    .btn-primary {
        background-color: #001f3f;
        border-color: #001f3f;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #f39c12;
        border-color: #f39c12;
        transform: translateY(-2px);
    }

    .form-control {
        border-radius: 5px;
        height: 45px;
    }
    
    .input-group-text {
        border-radius: 5px;
        background: #f4f6f9;
        border-left: 0;
    }
    
    .input-group input {
        border-right: 0;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card">
    <div class="card-header text-center">
      <img src="../favicon.ico" alt="Logo" class="brand-logo mb-3">
      <div class="h3 font-weight-bold text-navy"><?php echo $sys_name; ?></div>
      <p class="text-muted small">Login Portal</p>
    </div>
    <div class="card-body pt-0 pb-4">
      <p class="login-box-msg text-muted">Sign in to start your session</p>

      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user text-muted"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-4">
          <input type="password" class="form-control" name="password" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock text-muted"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                <a href="../index.php" class="text-muted small"><i class="fas fa-arrow-left mr-1"></i> Back to Website</a>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
  $(document).ready(function(){
    $('#login-frm').submit(function(e){
        e.preventDefault()
        var _this = $(this)
        var el = $('<div>')
            el.addClass('alert alert-danger err_msg')
            el.hide()
        $('.err_msg').remove()

        var btn = _this.find('button');
        var originalText = btn.text();
        btn.text('Signing in...').attr('disabled', true);

        $.ajax({
            url: '../classes/Login.php?f=login',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            error: err => {
                console.log(err)
                el.text('An error occurred')
                _this.prepend(el)
                el.show('slow')
                btn.text(originalText).attr('disabled', false);
            },
            success: function(resp){
                if(resp.status == 'success'){
                    location.href = 'index.php';
                }else if(!!resp.msg){
                    el.text(resp.msg)
                    _this.prepend(el)
                    el.show('slow')
                    btn.text(originalText).attr('disabled', false);
                }else{
                    el.text('An error occurred')
                    _this.prepend(el)
                    el.show('slow')
                    btn.text(originalText).attr('disabled', false);
                }
            }
        })
    })
  })
</script>
</body>
</html>