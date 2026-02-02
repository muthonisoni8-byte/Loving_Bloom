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
  <title>Loving Bloom - Fee Structure</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  
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
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block">
          <a href="../index.php" target="_blank" class="nav-link text-primary">
              <i class="fas fa-external-link-alt mr-1"></i> View Website
          </a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>
  
  <?php include 'includes/sidebar.php'; ?>
  
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0 text-navy">Fee Structure</h1></div>
          <div class="col-sm-6 text-right">
              <button class="btn btn-primary shadow-sm" id="new_fee"><i class="fas fa-plus"></i> Add New Fee</button>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm border-0">
          <div class="card-body">
            <table class="table table-bordered table-striped" id="fee_list">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Fee Name</th>
                  <th>Type</th>
                  <th>Description</th>
                  <th>Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $i = 1;
                $qry = $conn->query("SELECT * FROM fee_structure ORDER BY type ASC, name ASC");
                if($qry){
                    while($row = $qry->fetch_assoc()):
                ?>
                <tr>
                  <td><?php echo $i++ ?></td>
                  <td><?php echo $row['name'] ?></td>
                  <td>
                    <?php if($row['type'] == 'Program'): ?>
                        <span class="badge badge-primary">Program</span>
                    <?php elseif($row['type'] == 'Addon'): ?>
                        <span class="badge badge-warning">Addon</span>
                    <?php else: ?>
                        <span class="badge badge-info">Service</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo $row['description'] ?></td>
                  <td class="text-right"><?php echo number_format($row['amount'], 2) ?></td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-flat btn-primary edit_fee" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['name'] ?>" data-desc="<?php echo $row['description'] ?>" data-amount="<?php echo $row['amount'] ?>" data-type="<?php echo $row['type'] ?>">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-flat btn-danger delete_fee" data-id="<?php echo $row['id'] ?>">
                        <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <?php endwhile; } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="feeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Manage Fee</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <form id="fee-form">
            <input type="hidden" name="id" id="f_id">
            <div class="modal-body">
                <div class="form-group">
                    <label>Fee Name</label>
                    <input type="text" name="name" id="f_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="type" id="f_type" class="form-control">
                        <option value="Program">Program (Tuition)</option>
                        <option value="Service">Service (Transport/Meals)</option>
                        <option value="Addon">Add-on (Special Needs/Camp)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" name="amount" id="f_amount" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="f_desc" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function(){
        $('#fee_list').DataTable();

        $('#new_fee').click(function(){
            $('#fee-form')[0].reset();
            $('#f_id').val('');
            $('#feeModal').modal('show');
        });

        $('.edit_fee').click(function(){
            $('#f_id').val($(this).data('id'));
            $('#f_name').val($(this).data('name'));
            $('#f_type').val($(this).data('type'));
            $('#f_amount').val($(this).data('amount'));
            $('#f_desc').val($(this).data('desc'));
            $('#feeModal').modal('show');
        });

        $('.delete_fee').click(function(){
            if(confirm("Are you sure you want to delete this fee?")){
                var id = $(this).data('id');
                $.ajax({
                    url: '../classes/Master.php?f=delete_fee',
                    method: 'POST',
                    data: {id:id},
                    dataType: 'json',
                    success:function(resp){
                        if(resp.status == 'success') location.reload();
                        else alert("Error deleting fee.");
                    }
                })
            }
        });

        $('#fee-form').submit(function(e){
            e.preventDefault();
            $.ajax({
                url: '../classes/Master.php?f=save_fee',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success:function(resp){
                    if(resp.status == 'success') location.reload();
                    else alert("Error saving fee.");
                }
            })
        });
    });
</script>
</body>
</html>