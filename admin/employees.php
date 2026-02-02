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
  <title>Loving Bloom - Employees</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  
  <style>
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      .text-navy { color: #001f3f !important; }
      
      .img-avatar{
          width: 45px;
          height: 45px;
          object-fit: cover;
          object-position: center center;
          border-radius: 100%;
          border: 2px solid #ddd;
          background: #f4f6f9;
      }
      #cimg{
          max-width: 100%;
          max-height: 200px;
          object-fit: scale-down;
          object-position: center center;
      }
      #view_avatar {
          width: 150px;
          height: 150px;
          object-fit: cover;
          border-radius: 50%;
          border: 3px solid #007bff;
          box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      }
      .detail-label {
          font-weight: 600;
          color: #555;
      }
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
          <div class="col-sm-6"><h1 class="m-0 text-navy">Employee List</h1></div>
          <div class="col-sm-6 text-right">
              <button class="btn btn-primary shadow-sm" id="create_new"><i class="fa fa-plus"></i> Add New Employee</button>
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
                            <th>#</th>
                            <th>Avatar</th>
                            <th>Code</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM employees ORDER BY fullname ASC");
                        while($row = $qry->fetch_assoc()):
                            if(!empty($row['avatar']) && file_exists("../".$row['avatar'])){
                                $avatar = "../".$row['avatar'];
                            } else {
                                $avatar = "../uploads/default.png"; 
                            }
                        ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td class="text-center">
                                <img src="<?php echo $avatar ?>" alt="" class="img-avatar" onerror="this.src='../dist/img/avatar5.png'">
                            </td>
                            <td><b><?php echo $row['code'] ?></b></td>
                            <td><?php echo $row['fullname'] ?></td>
                            <td><?php echo $row['role'] ?></td>
                            <td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success rounded-pill">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger rounded-pill">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-flat btn-info view_data" 
                                        data-code="<?php echo $row['code'] ?>"
                                        data-fullname="<?php echo $row['fullname'] ?>"
                                        data-role="<?php echo $row['role'] ?>"
                                        data-contact="<?php echo $row['contact'] ?>"
                                        data-email="<?php echo $row['email'] ?>"
                                        data-address="<?php echo $row['address'] ?>"
                                        data-status="<?php echo $row['status'] == 1 ? 'Active' : 'Inactive' ?>"
                                        data-avatar="<?php echo $avatar ?>"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-flat btn-primary edit_data" 
                                        data-id="<?php echo $row['id'] ?>"
                                        data-fullname="<?php echo $row['fullname'] ?>"
                                        data-role="<?php echo $row['role'] ?>"
                                        data-contact="<?php echo $row['contact'] ?>"
                                        data-email="<?php echo $row['email'] ?>"
                                        data-address="<?php echo $row['address'] ?>"
                                        data-status="<?php echo $row['status'] ?>"
                                        data-avatar="<?php echo $avatar ?>"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-flat btn-danger delete_data" 
                                        data-id="<?php echo $row['id'] ?>" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
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

  <div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title text-white">Employee Profile</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 text-center mb-4">
                    <img src="" id="view_avatar" alt="Employee Image">
                    <h3 id="view_fullname" class="mt-2 text-primary font-weight-bold"></h3>
                    <p id="view_role" class="text-muted"></p>
                    <span id="view_status_badge" class="badge badge-success"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <span class="detail-label"><i class="fas fa-id-badge mr-2"></i> Employee Code:</span>
                            <span class="float-right" id="view_code"></span>
                        </li>
                        <li class="list-group-item">
                            <span class="detail-label"><i class="fas fa-phone mr-2"></i> Contact:</span>
                            <span class="float-right" id="view_contact"></span>
                        </li>
                        <li class="list-group-item">
                            <span class="detail-label"><i class="fas fa-envelope mr-2"></i> Email:</span>
                            <span class="float-right" id="view_email"></span>
                        </li>
                        <li class="list-group-item">
                            <span class="detail-label"><i class="fas fa-map-marker-alt mr-2"></i> Address:</span>
                            <p class="mt-1 mb-0 text-muted" id="view_address"></p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="bsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Manage Employee</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="bs-form" enctype="multipart/form-data">
            <input type="hidden" name="id">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Role/Position</label>
                        <input type="text" name="role" class="form-control" placeholder="e.g. Senior Nurse, Caregiver" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Contact #</label>
                        <input type="text" name="contact" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="customFile">Employee Image</label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg">
                      <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
                <div class="form-group d-flex justify-content-center">
                    <img src="" alt="" id="cimg" class="img-fluid img-thumbnail" style="background: #f4f6f9; min-height: 150px; min-width: 150px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Details</button>
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
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#cimg').attr('src', "../uploads/default.png");
        }
    }
  $(document).ready(function(){
    bsCustomFileInput.init();
    $('#list').DataTable();
    
    $('#create_new').click(function(){
        $('#bs-form')[0].reset();
        $('[name="id"]').val('');
        $('#cimg').attr('src', "../uploads/default.png");
        $('#bsModal .modal-title').text('Add Employee');
        $('#bsModal').modal('show');
    });

    $('.view_data').click(function(){
        var code = $(this).data('code');
        var fullname = $(this).data('fullname');
        var role = $(this).data('role');
        var contact = $(this).data('contact');
        var email = $(this).data('email');
        var address = $(this).data('address');
        var status = $(this).data('status');
        var avatar = $(this).data('avatar');

        $('#view_avatar').attr('src', avatar);
        $('#view_fullname').text(fullname);
        $('#view_role').text(role);
        $('#view_code').text(code);
        $('#view_contact').text(contact);
        $('#view_email').text(email);
        $('#view_address').text(address);
        
        var badge = $('#view_status_badge');
        badge.text(status);
        if(status === 'Active'){
            badge.removeClass('badge-danger').addClass('badge-success');
        } else {
            badge.removeClass('badge-success').addClass('badge-danger');
        }

        $('#viewModal').modal('show');
    });

    $('.edit_data').click(function(){
        var modal = $('#bsModal');
        modal.find('[name="id"]').val($(this).data('id'));
        modal.find('[name="fullname"]').val($(this).data('fullname'));
        modal.find('[name="role"]').val($(this).data('role'));
        modal.find('[name="contact"]').val($(this).data('contact'));
        modal.find('[name="email"]').val($(this).data('email'));
        modal.find('[name="address"]').val($(this).data('address'));
        modal.find('[name="status"]').val($(this).data('status'));
        var avatar = $(this).data('avatar');
        if(avatar == ''){
            avatar = "../uploads/default.png";
        }
        $('#cimg').attr('src', avatar);
        $('#bsModal .modal-title').text('Edit Employee');
        modal.modal('show');
    });

    $('.delete_data').click(function(){
        if(confirm("Are you sure you want to delete this record?")){
            var id = $(this).data('id');
            $.ajax({
                url: '../classes/Master.php?f=delete_employee', 
                method: 'POST',
                data: {id:id},
                dataType: 'json',
                success: function(resp){
                    if(resp.status == 'success'){
                        location.reload();
                    }else{
                        alert("Error deleting record");
                    }
                }
            })
        }
    });

    $('#bs-form').submit(function(e){
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: '../classes/Master.php?f=save_employee', 
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success'){
                    location.reload();
                }else{
                    alert("Error saving record: " + resp.msg);
                    console.log(resp);
                }
            }
        })
    })
  })
</script>
</body>
</html>