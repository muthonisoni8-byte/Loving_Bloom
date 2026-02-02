<?php
session_start();
if(!isset($_SESSION['userdata'])){ header("Location: login.php"); exit; }
require_once('../db_connect.php');
$enrolled = $conn->query("SELECT id FROM child_info WHERE status = 1")->num_rows;
$pending_apps = $conn->query("SELECT id FROM child_info WHERE status = 0")->num_rows;
$present_today = $conn->query("SELECT id FROM attendance WHERE date(attendance_date) = CURDATE() AND status = 1")->num_rows;

$absent_today = $enrolled - $present_today;
if($absent_today < 0) { $absent_today = 0; }

$earnings_qry = $conn->query("SELECT SUM(amount_paid) as total FROM payments WHERE status = 1");
$total_earnings = $earnings_qry->fetch_assoc()['total'] ?? 0;

$arrears_qry = $conn->query("SELECT SUM(balance) as debt FROM invoices WHERE status != 3");
$total_arrears = $arrears_qry->fetch_assoc()['debt'] ?? 0;

$expected_qry = $conn->query("SELECT SUM(amount) as total FROM invoices WHERE status != 3");
$expected_earnings = $expected_qry->fetch_assoc()['total'] ?? 0;

$downloads_qry = $conn->query("SELECT SUM(downloads) as total FROM study_materials");
$total_downloads = $downloads_qry->fetch_assoc()['total'] ?? 0;

$months = [];
$data_invoiced = [];
$data_collected = [];

$total_inv_past = 0;
$total_col_past = 0;

for ($i = 2; $i >= 0; $i--) {
    $month_start = date("Y-m-01 00:00:00", strtotime("-$i months"));
    $month_end   = date("Y-m-t 23:59:59", strtotime("-$i months"));
    $month_label = date("M", strtotime("-$i months"));

    $inv_res = $conn->query("SELECT SUM(amount) as total FROM invoices WHERE date_created BETWEEN '$month_start' AND '$month_end'");
    $inv_val = $inv_res->fetch_assoc()['total'] ?? 0;

    $pay_res = $conn->query("SELECT SUM(amount_paid) as total FROM payments WHERE date_created BETWEEN '$month_start' AND '$month_end' AND status = 1");
    $pay_val = $pay_res->fetch_assoc()['total'] ?? 0;

    $months[] = $month_label;
    $data_invoiced[] = $inv_val;
    $data_collected[] = $pay_val;

    $total_inv_past += $inv_val;
    $total_col_past += $pay_val;
}

$avg_inv = $total_inv_past / 3;
$avg_col = $total_col_past / 3;

for ($j = 1; $j <= 3; $j++) {
    $months[] = date("M", strtotime("+$j months")) . " (Est)";
    $data_invoiced[] = $avg_inv; 
    $data_collected[] = $avg_col; 
}

$usertype = $_SESSION['userdata']['type'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loving Bloom - Dashboard</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      
      .small-box .inner h3 { font-weight: 500; font-size: 2.2rem; }
      .text-navy { color: #001f3f !important; }
      
      .card { border: none; }
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
              <h1 class="m-0 text-navy">Dashboard Overview</h1>
          </div>
        </div>
      </div>
    </div>
    
    <div class="content">
      <div class="container-fluid">
        
        <div class="row">
          
          <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-info shadow-sm">
              <div class="inner">
                <h3><?php echo number_format($enrolled); ?></h3>
                <p>Total Enrolled</p>
              </div>
              <div class="icon"><i class="fas fa-baby"></i></div>
              <a href="<?php echo getLink($usertype, 'enrollment.php'); ?>" 
                 class="small-box-footer" 
                 <?php echo getClick($usertype); ?>>
                 Manage <?php if($usertype!=1) echo '<i class="fas fa-lock ml-1"></i>'; else echo '<i class="fas fa-arrow-circle-right"></i>'; ?>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-secondary shadow-sm">
              <div class="inner">
                <h3><?php echo number_format($pending_apps); ?></h3>
                <p>Pending Enrollment</p>
              </div>
              <div class="icon"><i class="fas fa-user-clock"></i></div>
              <a href="<?php echo getLink($usertype, 'enrollment.php#tab-pending'); ?>" 
                 class="small-box-footer" 
                 <?php echo getClick($usertype); ?>>
                 Review <?php if($usertype!=1) echo '<i class="fas fa-lock ml-1"></i>'; else echo '<i class="fas fa-arrow-circle-right"></i>'; ?>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-success shadow-sm">
              <div class="inner">
                <h3><?php echo number_format($present_today); ?></h3>
                <p>Present Today</p>
              </div>
              <div class="icon"><i class="fas fa-check-circle"></i></div>
              <a href="attendance.php" class="small-box-footer">View List <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-purple shadow-sm">
              <div class="inner">
                <h3><?php echo number_format($absent_today); ?></h3>
                <p>Absent Today</p>
              </div>
              <div class="icon"><i class="fas fa-user-times"></i></div>
              <a href="attendance.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-primary shadow-sm">
              <div class="inner">
                <h3><?php echo number_format($expected_earnings, 2); ?></h3>
                <p>Expected Earnings</p>
              </div>
              <div class="icon"><i class="fas fa-file-invoice"></i></div>
              <a href="<?php echo getLink($usertype, 'billing.php'); ?>" 
                 class="small-box-footer" 
                 <?php echo getClick($usertype); ?>>
                 View Invoices <?php if($usertype!=1) echo '<i class="fas fa-lock ml-1"></i>'; else echo '<i class="fas fa-arrow-circle-right"></i>'; ?>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-warning shadow-sm">
              <div class="inner">
                <h3><?php echo number_format($total_earnings, 2); ?></h3>
                <p>Total Collected</p>
              </div>
              <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
              <a href="<?php echo getLink($usertype, 'billing.php'); ?>" 
                 class="small-box-footer" 
                 <?php echo getClick($usertype); ?>>
                 History <?php if($usertype!=1) echo '<i class="fas fa-lock ml-1"></i>'; else echo '<i class="fas fa-arrow-circle-right"></i>'; ?>
              </a>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-danger shadow-sm">
              <div class="inner">
                <h3><?php echo number_format($total_arrears, 2); ?></h3>
                <p>Pending Arrears</p>
              </div>
              <div class="icon"><i class="fas fa-bullhorn"></i></div>
              <a href="<?php echo getLink($usertype, 'billing.php'); ?>" 
                 class="small-box-footer" 
                 <?php echo getClick($usertype); ?>>
                 Follow Up <?php if($usertype!=1) echo '<i class="fas fa-lock ml-1"></i>'; else echo '<i class="fas fa-arrow-circle-right"></i>'; ?>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-teal shadow-sm">
              <div class="inner">
                <h3><?php echo number_format($total_downloads); ?></h3>
                <p>Total Downloads</p>
              </div>
              <div class="icon"><i class="fas fa-cloud-download-alt"></i></div>
              <a href="study_materials.php" class="small-box-footer">Manage Materials <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

        </div>
        
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header border-0 bg-white">
                        <h3 class="card-title text-navy">
                            <i class="fas fa-chart-line mr-2"></i>
                            Financial Performance & Trajectory
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="financeChart" style="min-height: 350px; height: 350px; max-height: 400px; max-width: 100%;"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function showAdminOnlyAlert(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Access Denied',
            text: 'You do not have administrative privileges to access this page.',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }

$(function () {
    const ctx = document.getElementById('financeChart').getContext('2d');
    
    // Gradients
    const gradientInvoice = ctx.createLinearGradient(0, 0, 0, 400);
    gradientInvoice.addColorStop(0, 'rgba(60, 141, 188, 0.5)');
    gradientInvoice.addColorStop(1, 'rgba(60, 141, 188, 0.05)');

    const gradientCollect = ctx.createLinearGradient(0, 0, 0, 400);
    gradientCollect.addColorStop(0, 'rgba(40, 167, 69, 0.5)');
    gradientCollect.addColorStop(1, 'rgba(40, 167, 69, 0.05)');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [
            {
                label: 'Expected Revenue (Invoiced)',
                data: <?php echo json_encode($data_invoiced); ?>,
                borderColor: '#3b8bba',
                backgroundColor: gradientInvoice,
                borderWidth: 2, // Thinner line
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b8bba',
                fill: true
            },
            {
                label: 'Actual Collected',
                data: <?php echo json_encode($data_collected); ?>,
                borderColor: '#28a745',
                backgroundColor: gradientCollect,
                borderWidth: 2, // Thinner line
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#28a745',
                fill: true
            }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: { usePointStyle: true, font: { size: 12, family: 'Poppins' } }
            },
            tooltip: {
                mode: 'index',
                intersect: false,
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleFont: { size: 13, family: 'Poppins', weight: 'normal' },
                bodyFont: { size: 13, family: 'Poppins' },
                padding: 10,
                cornerRadius: 4,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) { label += ': '; }
                        if (context.parsed.y !== null) {
                            label += context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false, drawBorder: false },
                ticks: { font: { size: 12, family: 'Poppins' } }
            },
            y: {
                beginAtZero: true,
                grid: { color: '#f0f0f0', borderDash: [5, 5] },
                ticks: {
                    font: { size: 11, family: 'Poppins' },
                    maxTicksLimit: 8,
                    callback: function(value) { 
                        if(value >= 1000000) return (value/1000000).toFixed(1) + 'M';
                        if(value >= 1000) return (value/1000).toFixed(0) + 'k';
                        return value;
                    }
                }
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
      }
    });
});
</script>
</body>
</html>