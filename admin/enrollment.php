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
  <title>Loving Bloom - Enrollments</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  
  <style>
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      .text-navy { color: #001f3f !important; }
      table.dataTable { width: 100% !important; }
      .text-nowrap { white-space: nowrap; }
      #v_photo_display, #e_photo_display {
          width: 150px;
          height: 150px;
          object-fit: cover;
          border-radius: 50%;
          border: 3px solid #007bff;
          display: block;
          margin-bottom: 15px;
          margin-left: auto;
          margin-right: auto;
      }
      
      .card { border: none; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
      .card-header { background-color: #fff; border-bottom: 1px solid #f0f0f0; }
      .nav-tabs .nav-link.active {
          border-top: 3px solid #007bff;
          font-weight: 600;
          color: #007bff;
      }
      .nav-tabs .nav-link {
          color: #555;
          font-weight: 500;
      }
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
          <div class="col-sm-6">
              <h1 class="m-0 text-navy">Enrollment Management</h1>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        
        <div class="card card-primary card-outline card-outline-tabs">
          <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
              <li class="nav-item"><a class="nav-link active" id="tab-pending-link" data-toggle="pill" href="#tab-pending" role="tab">Pending</a></li>
              <li class="nav-item"><a class="nav-link" id="tab-enrolled-link" data-toggle="pill" href="#tab-enrolled" role="tab">Enrolled Children</a></li>
              <li class="nav-item"><a class="nav-link" id="tab-unenrolled-link" data-toggle="pill" href="#tab-unenrolled" role="tab">Unenrolled History</a></li>
              <li class="nav-item"><a class="nav-link" id="tab-rejected-link" data-toggle="pill" href="#tab-rejected" role="tab">Rejected History</a></li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content" id="custom-tabs-four-tabContent">
              
              <div class="tab-pane fade show active" id="tab-pending" role="tabpanel">
                 <table class="table table-bordered table-striped text-nowrap datatable">
                    <thead><tr><th>#</th><th>Reg No</th><th>Child Name</th><th>Age</th><th>Parent</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT c.*, p.parent_name, p.contact_address FROM child_info c INNER JOIN parent_info p ON c.parentid = p.id WHERE c.status = 0 ORDER BY c.created_at DESC");
                        while($row = $qry->fetch_assoc()):
                            $dob = new DateTime($row['birth_date']); $age = $dob->diff(new DateTime('today'))->y;
                            $full_str = $row['contact_address']; $phone = "N/A"; $address = $full_str;
                            if(strpos($full_str, '(Contact:') !== false){ $parts = explode('(Contact:', $full_str); $address = trim($parts[0]); $phone = trim(str_replace(')', '', $parts[1])); }
                        ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $row['birth_reg_no'] ?></td>
                            <td><?php echo $row['child_name'] ?></td>
                            <td><?php echo $age ?> Years</td>
                            <td><?php echo $row['parent_name'] ?></td>
                            <td><?php echo $phone ?></td>
                            <td class="text-center"><span class="badge badge-warning">Pending</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-info view_details" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>" data-photo="<?php echo $row['child_photo'] ?>" data-gender="<?php echo $row['gender'] ?>" data-dob="<?php echo $row['birth_date'] ?>" data-age="<?php echo $age ?>" data-phone="<?php echo $phone ?>" data-address="<?php echo $address ?>" data-blood="<?php echo $row['blood_type'] ?>" data-allergy="<?php echo $row['allergies'] ?>" data-cond="<?php echo $row['med_conditions'] ?>" data-needs="<?php echo $row['special_needs'] ?>" data-status="<?php echo $row['status'] ?>">
                                    <i class="fas fa-eye"></i> Review
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                 </table>
              </div>

              <div class="tab-pane fade" id="tab-enrolled" role="tabpanel">
                 <table class="table table-bordered table-striped text-nowrap datatable">
                    <thead><tr><th>#</th><th>Reg No</th><th>Child Name</th><th>Age</th><th>Parent</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT c.*, p.parent_name, p.contact_address FROM child_info c INNER JOIN parent_info p ON c.parentid = p.id WHERE c.status = 1 ORDER BY c.child_name ASC");
                        while($row = $qry->fetch_assoc()):
                             $dob = new DateTime($row['birth_date']); $age = $dob->diff(new DateTime('today'))->y;
                             $full_str = $row['contact_address']; $phone = "N/A"; $address = $full_str;
                             if(strpos($full_str, '(Contact:') !== false){ $parts = explode('(Contact:', $full_str); $address = trim($parts[0]); $phone = trim(str_replace(')', '', $parts[1])); }
                        ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $row['birth_reg_no'] ?></td>
                            <td><?php echo $row['child_name'] ?></td>
                            <td><?php echo $age ?> Years</td>
                            <td><?php echo $row['parent_name'] ?></td>
                            <td><?php echo $phone ?></td>
                            <td class="text-center"><span class="badge badge-success">Enrolled</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-default view_details" title="View Details"
                                    data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>" data-photo="<?php echo $row['child_photo'] ?>" data-gender="<?php echo $row['gender'] ?>" data-dob="<?php echo $row['birth_date'] ?>" data-age="<?php echo $age ?>" data-phone="<?php echo $phone ?>" data-address="<?php echo $address ?>" data-blood="<?php echo $row['blood_type'] ?>" data-allergy="<?php echo $row['allergies'] ?>" data-cond="<?php echo $row['med_conditions'] ?>" data-needs="<?php echo $row['special_needs'] ?>" data-status="<?php echo $row['status'] ?>">
                                    <i class="fas fa-search"></i>
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-primary edit_data" title="Edit Details"
                                    data-id="<?php echo $row['id'] ?>" 
                                    data-name="<?php echo $row['child_name'] ?>" 
                                    data-dob="<?php echo $row['birth_date'] ?>" 
                                    data-gender="<?php echo $row['gender'] ?>" 
                                    data-photo="<?php echo $row['child_photo'] ?>" 
                                    data-parent="<?php echo $row['parent_name'] ?>" 
                                    data-phone="<?php echo $phone ?>" 
                                    data-address="<?php echo $address ?>" 
                                    data-blood="<?php echo $row['blood_type'] ?>" 
                                    data-allergy="<?php echo $row['allergies'] ?>" 
                                    data-cond="<?php echo $row['med_conditions'] ?>" 
                                    data-needs="<?php echo $row['special_needs'] ?>"
                                    data-biometric="<?php echo !empty($row['webauthn_id']) ? 1 : 0; ?>"> <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-warning unenroll_student" title="Unenroll Student" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                 </table>
              </div>

              <div class="tab-pane fade" id="tab-unenrolled" role="tabpanel">
                 <table class="table table-bordered table-striped text-nowrap datatable">
                    <thead><tr><th>#</th><th>Reg No</th><th>Child Name</th><th>Age</th><th>Parent</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT c.*, p.parent_name, p.contact_address FROM child_info c INNER JOIN parent_info p ON c.parentid = p.id WHERE c.status = 3 ORDER BY c.created_at DESC");
                        while($row = $qry->fetch_assoc()):
                             $dob = new DateTime($row['birth_date']); $age = $dob->diff(new DateTime('today'))->y;
                             $full_str = $row['contact_address']; $phone = "N/A"; $address = $full_str;
                             if(strpos($full_str, '(Contact:') !== false){ $parts = explode('(Contact:', $full_str); $address = trim($parts[0]); $phone = trim(str_replace(')', '', $parts[1])); }
                        ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $row['birth_reg_no'] ?></td>
                            <td><?php echo $row['child_name'] ?></td>
                            <td><?php echo $age ?> Years</td>
                            <td><?php echo $row['parent_name'] ?></td>
                            <td><?php echo $phone ?></td>
                            <td class="text-center"><span class="badge badge-secondary">Unenrolled</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-success reenroll_student" title="Re-enroll Child" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>">
                                    <i class="fas fa-user-check"></i> Re-enroll
                                </button>
                                <button type="button" class="btn btn-sm btn-default view_details" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>" data-photo="<?php echo $row['child_photo'] ?>" data-gender="<?php echo $row['gender'] ?>" data-dob="<?php echo $row['birth_date'] ?>" data-age="<?php echo $age ?>" data-phone="<?php echo $phone ?>" data-address="<?php echo $address ?>" data-blood="<?php echo $row['blood_type'] ?>" data-allergy="<?php echo $row['allergies'] ?>" data-cond="<?php echo $row['med_conditions'] ?>" data-needs="<?php echo $row['special_needs'] ?>" data-status="<?php echo $row['status'] ?>">
                                    <i class="fas fa-search"></i> Details
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete_child" title="Delete Record" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                 </table>
              </div>

              <div class="tab-pane fade" id="tab-rejected" role="tabpanel">
                 <table class="table table-bordered table-striped text-nowrap datatable">
                    <thead><tr><th>#</th><th>Reg No</th><th>Child Name</th><th>Age</th><th>Parent</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT c.*, p.parent_name, p.contact_address FROM child_info c INNER JOIN parent_info p ON c.parentid = p.id WHERE c.status = 2 ORDER BY c.created_at DESC");
                        while($row = $qry->fetch_assoc()):
                             $dob = new DateTime($row['birth_date']); $age = $dob->diff(new DateTime('today'))->y;
                             $full_str = $row['contact_address']; $phone = "N/A"; $address = $full_str;
                             if(strpos($full_str, '(Contact:') !== false){ $parts = explode('(Contact:', $full_str); $address = trim($parts[0]); $phone = trim(str_replace(')', '', $parts[1])); }
                        ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $row['birth_reg_no'] ?></td>
                            <td><?php echo $row['child_name'] ?></td>
                            <td><?php echo $age ?> Years</td>
                            <td><?php echo $row['parent_name'] ?></td>
                            <td><?php echo $phone ?></td>
                            <td class="text-center"><span class="badge badge-danger">Rejected</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-success accept_rejected" title="Accept Enrollment" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>">
                                    <i class="fas fa-check"></i> Accept
                                </button>
                                <button type="button" class="btn btn-sm btn-default view_details" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>" data-photo="<?php echo $row['child_photo'] ?>" data-gender="<?php echo $row['gender'] ?>" data-dob="<?php echo $row['birth_date'] ?>" data-age="<?php echo $age ?>" data-phone="<?php echo $phone ?>" data-address="<?php echo $address ?>" data-blood="<?php echo $row['blood_type'] ?>" data-allergy="<?php echo $row['allergies'] ?>" data-cond="<?php echo $row['med_conditions'] ?>" data-needs="<?php echo $row['special_needs'] ?>" data-status="<?php echo $row['status'] ?>">
                                    <i class="fas fa-search"></i> Details
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete_child" title="Delete Record" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>">
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
    </div>
  </div>

  <?php include 'includes/enrollment_modals.php'; ?>

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
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script src="js/enrollment.js"></script>
</body>
</html>