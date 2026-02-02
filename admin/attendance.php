<?php
session_start();
if(!isset($_SESSION['userdata'])){ header("Location: login.php"); exit; }
require_once('../db_connect.php');

$attendance_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loving Bloom - Attendance</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <style>
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      
      #v_photo_display {
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

      @media print {
          .no-print, .main-footer, .navbar, .main-sidebar, .card-header-tools, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate { display: none !important; }
          .content-wrapper { margin-left: 0 !important; background: white !important; }
          .card { box-shadow: none !important; border: none !important; }
          
          .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
          .print-header img { height: 50px; width: auto; }
          .print-header h2 { margin: 10px 0 0; font-weight: bold; color: #001f3f; }
          
          table { width: 100% !important; border-collapse: collapse; }
          th, td { border: 1px solid #ddd !important; padding: 8px; }
      }
      .print-header { display: none; } 
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light no-print border-bottom-0 shadow-sm">
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
          <div class="col-sm-6"><h1 class="m-0 text-navy">Attendance Management</h1></div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        
        <div class="print-header">
            <img src="../favicon.ico" alt="Logo">
            <h2>Loving Bloom Daycare</h2>
            <p>Attendance Report: <?php echo $attendance_date; ?></p>
        </div>

        <div class="card card-outline card-success shadow-sm">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Attendance for: <b><?php echo $attendance_date; ?></b></h3>
                    <div class="card-tools d-flex align-items-stretch card-header-tools">
                        <input type="date" id="attendance_filter" class="form-control mr-2" value="<?php echo $attendance_date; ?>" style="height: 38px;">
                        
                        <button class="btn btn-dark no-print mr-2 text-nowrap" onclick="window.print()" style="height: 38px; display: flex; align-items: center;">
                            <i class="fas fa-print mr-1"></i> Print Report
                        </button>
                        
                        <a href="checkin.php" class="btn btn-primary no-print text-nowrap" style="height: 38px; display: flex; align-items: center;">
                            <i class="fas fa-fingerprint mr-1"></i> Smart Check-In
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="printable_area">
                    <table id="list" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Child Name</th>
                                <th>Reg No</th>
                                <th>Status</th>
                                <th class="no-print text-right">Action</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i = 1;
                            $qry = $conn->query("SELECT c.*, p.parent_name, p.contact_address, a.status as attendance_status FROM child_info c LEFT JOIN attendance a ON c.id = a.child_id AND a.attendance_date = '$attendance_date' LEFT JOIN parent_info p ON c.parentid = p.id WHERE c.status = 1 ORDER BY c.child_name ASC");
                            
                            while($row = $qry->fetch_assoc()):
                                $status = isset($row['attendance_status']) ? $row['attendance_status'] : '';
                                
                                $dob = new DateTime($row['birth_date']);
                                $age = $dob->diff(new DateTime('today'))->y;

                                $full_str = $row['contact_address']; 
                                $phone = "N/A"; 
                                $address = $full_str;
                                
                                if(strpos($full_str, '(Contact:') !== false){
                                    $parts = explode('(Contact:', $full_str);
                                    $address = trim($parts[0]);
                                    $phone = trim(str_replace(')', '', $parts[1]));
                                }
                            ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td><?php echo $row['child_name'] ?></td>
                                <td><?php echo $row['birth_reg_no'] ?></td>
                                <td class="text-center" id="status_cell_<?php echo $row['id']; ?>">
                                    <?php if($status == 1): ?>
                                        <span class="badge badge-success">Present</span>
                                    <?php elseif($status === '0'): ?> 
                                        <span class="badge badge-danger">Absent</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Not Marked</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right no-print">
                                    <button class="btn btn-sm btn-primary mark_attendance" data-id="<?php echo $row['id'] ?>" data-status="1">Present</button>
                                    <button class="btn btn-sm btn-danger mark_attendance" data-id="<?php echo $row['id'] ?>" data-status="0">Absent</button>
                                    
                                    <button class="btn btn-sm btn-default view_details" 
                                        data-id="<?php echo $row['id'] ?>" 
                                        data-name="<?php echo $row['child_name'] ?>" 
                                        data-photo="<?php echo $row['child_photo'] ?>" 
                                        data-gender="<?php echo $row['gender'] ?>" 
                                        data-dob="<?php echo $row['birth_date'] ?>" 
                                        data-age="<?php echo $age ?>" 
                                        data-phone="<?php echo $phone ?>" 
                                        data-address="<?php echo $address ?>" 
                                        data-blood="<?php echo $row['blood_type'] ?>" 
                                        data-allergy="<?php echo $row['allergies'] ?>" 
                                        data-cond="<?php echo $row['med_conditions'] ?>" 
                                        data-needs="<?php echo $row['special_needs'] ?>">
                                        <i class="fas fa-search"></i> Details
                                    </button>
                                    
                                    <button class="btn btn-sm btn-info view_history" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['child_name'] ?>"><i class="fas fa-history"></i> History</button>
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

  <div class="modal fade" id="historyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Attendance History: <span id="h_child_name"></span></h5>
          <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="row mb-3 no-print">
                <div class="col-md-5">
                    <label>Filter by Month:</label>
                    <input type="month" id="history_month_filter" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary" id="reset_history_filter">Show All</button>
                </div>
            </div>

            <div id="history_print_area">
                <table class="table table-bordered">
                    <thead><tr><th>Date</th><th>Status</th></tr></thead>
                    <tbody id="history_table_body"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer no-print">
            <button type="button" class="btn btn-dark" id="print_history_btn"><i class="fas fa-print"></i> Print History</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
<script src="js/attendance.js"></script>
</body>
</html>