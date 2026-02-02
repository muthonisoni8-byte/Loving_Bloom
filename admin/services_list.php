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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loving Bloom - Website Service</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      .text-navy { color: #001f3f !important; }
      
      .service-img {
          width: 80px;
          height: 60px;
          object-fit: cover;
          border-radius: 5px;
          border: 1px solid #dee2e6;
      }
      #cimg {
          max-width: 100%;
          max-height: 200px;
          object-fit: contain;
          border: 1px solid #ddd;
          padding: 5px;
          border-radius: 5px;
          display: none; 
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
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="nav-link" href="logout.php" role="button"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <?php include 'includes/sidebar.php'; ?>
  
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0 text-navy">Website Service Content</h1></div>
          <div class="col-sm-6 text-right">
              <button class="btn btn-secondary shadow-sm" id="btn_add_redirect"><i class="fas fa-plus"></i> Add New Service</button>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-outline card-navy shadow-sm border-0">
            <div class="card-body">
                <table id="list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Website Image</th>
                            <th width="25%">Service Name</th>
                            <th width="35%">Public Description</th>
                            <th width="20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM fee_structure ORDER BY name ASC");
                        while($row = $qry->fetch_assoc()):
                            $img_src = !empty($row['image_path']) ? "../".$row['image_path'] : "https://via.placeholder.com/150?text=No+Image";
                            $desc_display = !empty($row['public_description']) ? $row['public_description'] : '<span class="text-muted font-italic">No description set</span>';
                        ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td class="text-center">
                                <img src="<?php echo $img_src ?>" class="service-img" alt="Service Image" onerror="this.src='../dist/img/no-image-available.png'">
                            </td>
                            <td><b><?php echo $row['name'] ?></b></td>
                            <td><small><?php echo $desc_display; ?></small></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-flat btn-primary btn-sm edit_data" 
                                   data-id="<?php echo $row['id'] ?>"
                                   data-service="<?php echo $row['name'] ?>"
                                   data-description="<?php echo $row['public_description'] ?>"
                                   data-image="<?php echo !empty($row['image_path']) ? "../".$row['image_path'] : "" ?>">
                                   <span class="fa fa-edit"></span> Edit Content
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="uni_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Website Content</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="service-form" enctype="multipart/form-data">
            <input type="hidden" name="id">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Service Name (Read Only)</label>
                            <input type="text" name="service" class="form-control" readonly style="background-color: #f4f6f9; cursor: not-allowed;">
                        </div>
                        <div class="form-group">
                            <label>Public Description</label>
                            <textarea name="description" class="form-control" rows="5" required placeholder="Enter the text parents will see..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Website Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this)" accept="image/*">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <img src="" id="cimg" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Content</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </form>
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
  function displayImg(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function (e) {
              $('#cimg').attr('src', e.target.result).show();
          }
          reader.readAsDataURL(input.files[0]);
      }
  }

  $(document).ready(function(){
    bsCustomFileInput.init();
    $('#list').DataTable();
    
    $('#btn_add_redirect').click(function(){
        Swal.fire({
            title: 'Manage Services',
            text: "This page controls how your services appear on the Public Website. To change prices or add new services, please go to the Fee Structure.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Go to Fee Structure',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'fee_structure.php';
            }
        })
    });

    $('.edit_data').click(function(){
        var modal = $('#uni_modal');
        modal.find('[name="id"]').val($(this).data('id'));
        modal.find('[name="service"]').val($(this).data('service'));
        modal.find('[name="description"]').val($(this).data('description'));
        
        var img = $(this).data('image');
        if(img != ''){
            $('#cimg').attr('src', img).show();
        } else {
            $('#cimg').hide();
        }
        
        modal.modal('show');
    });

    $('#service-form').submit(function(e){
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: '../classes/Master.php?f=save_service',
            method: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success'){
                    location.reload();
                }else{
                    alert("Error saving record: " + resp.msg);
                }
            }
        })
    })
  })
</script>
</body>
</html>