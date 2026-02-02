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

if($_SESSION['userdata']['type'] != 1){ 
    header("Location: index.php"); 
    exit; 
}

require_once('../db_connect.php');

if(isset($_POST['save_user'])){
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $type = $_POST['type']; 

    if(empty($id)){
        $pass_enc = md5($password);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $username, $email, $pass_enc, $type);
    } else {
        if(!empty($password)){
            $pass_enc = md5($password);
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=?, type=? WHERE id=?");
            $stmt->bind_param("sssii", $username, $email, $pass_enc, $type, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, type=? WHERE id=?");
            $stmt->bind_param("ssii", $username, $email, $type, $id);
        }
    }
    $stmt->execute();
    header("Location: user_list.php");
    exit;
}

if(isset($_GET['delete_id'])){
    $del_id = $_GET['delete_id'];
    if($del_id == $_SESSION['userdata']['id']){
        echo "<script>alert('You cannot delete your own account from here.'); window.location='user_list.php';</script>";
        exit;
    }
    $conn->query("DELETE FROM users WHERE id = $del_id");
    header("Location: user_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loving Bloom - User Management</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
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
          <div class="col-sm-6"><h1 class="m-0 text-navy">User Management</h1></div>
          <div class="col-sm-6 text-right">
              <button class="btn btn-primary shadow-sm" onclick="openModal()"><i class="fa fa-plus"></i> Add New User</button>
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
                            <th>Date Created</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM users WHERE type IN (1, 2) ORDER BY username ASC");
                        while($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td><?php echo date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                            <td><?php echo $row['username'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td>
                                <?php if($row['type'] == 1): ?>
                                    <span class="badge badge-primary">Administrator</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Staff</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Action <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item edit_data" href="javascript:void(0)" 
                                       data-id="<?php echo $row['id'] ?>" 
                                       data-username="<?php echo $row['username'] ?>" 
                                       data-email="<?php echo $row['email'] ?>"
                                       data-type="<?php echo $row['type'] ?>">
                                       <span class="fa fa-edit text-primary"></span> Edit
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="user_list.php?delete_id=<?php echo $row['id'] ?>" onclick="return confirm('Are you sure?')">
                                        <span class="fa fa-trash text-danger"></span> Delete
                                    </a>
                                </div>
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

  <div class="modal fade" id="userModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add New User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="" method="POST">
            <input type="hidden" name="id" id="userId">
            <input type="hidden" name="save_user" value="1">
            <div class="modal-body">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="userName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="userEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="type" id="userType" class="form-control">
                        <option value="1">Administrator</option>
                        <option value="2">Staff</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-muted"><i>Leave blank if you don't want to change the password.</i></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save User</button>
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function(){
    $('#list').DataTable();
    
    window.openModal = function(){
        $('#userId').val('');
        $('#userName').val('');
        $('#userEmail').val('');
        $('#userType').val(2);
        $('#modalTitle').text('Add New User');
        $('#userModal').modal('show');
    };

    $('.edit_data').click(function(){
        var id = $(this).data('id');
        var username = $(this).data('username');
        var email = $(this).data('email');
        var type = $(this).data('type');
        
        $('#userId').val(id);
        $('#userName').val(username);
        $('#userEmail').val(email);
        $('#userType').val(type);
        $('#modalTitle').text('Edit User');
        $('#userModal').modal('show');
    });
  })
</script>
</body>
</html>