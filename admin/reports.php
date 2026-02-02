<?php
session_start();
if(!isset($_SESSION['userdata'])){ header("Location: login.php"); exit; }
require_once('../db_connect.php');

$is_filtered = isset($_GET['month']) && !empty($_GET['month']);
$current_month = date('Y-m');

if ($is_filtered) {
    $filter_month = $_GET['month'];
    $month_start = $filter_month . '-01';
    $month_end   = date("Y-m-t", strtotime($month_start));
    $month_end_datetime = $month_end . ' 23:59:59';
    
    $report_title = "Report: " . date("F Y", strtotime($filter_month));
    $date_clause_enrollment = "AND date_created <= '$month_end_datetime'"; 
    $date_clause_finance    = "AND date_created BETWEEN '$month_start' AND '$month_end_datetime'";
} else {
    $filter_month = $current_month;
    $report_title = "Report: All Time (Overview)";
    $date_clause_enrollment = "";
    $date_clause_finance    = "";
}

$active_children = $conn->query("SELECT id FROM child_info WHERE status = 1 $date_clause_enrollment")->num_rows;
$pending_children = $conn->query("SELECT id FROM child_info WHERE status = 0 $date_clause_enrollment")->num_rows;
$rejected_children = $conn->query("SELECT id FROM child_info WHERE status = 2 $date_clause_enrollment")->num_rows;
$unenrolled_children = $conn->query("SELECT id FROM child_info WHERE status = 3 $date_clause_enrollment")->num_rows;

$display_total_children = $active_children;

$total_income = 0;
$total_invoiced = 0;
$pending_fees = 0;

$check_payments = $conn->query("SHOW COLUMNS FROM payments LIKE 'amount_paid'");
$col_paid = ($check_payments->num_rows > 0) ? 'amount_paid' : 'amount';

$query_income = $conn->query("SELECT SUM($col_paid) as total FROM payments WHERE status = 1 $date_clause_finance");
$total_income = $query_income->fetch_assoc()['total'] ?? 0;

$query_invoices = $conn->query("SELECT SUM(amount) as total FROM invoices WHERE 1=1 $date_clause_finance");
$total_invoiced = $query_invoices->fetch_assoc()['total'] ?? 0;

$pending_fees = $total_invoiced - $total_income;
if($pending_fees < 0) $pending_fees = 0;

$staff_count = $conn->query("SELECT id FROM employees WHERE status = 1 $date_clause_enrollment")->num_rows;

$trend_months = [];
$trend_revenues = [];

for ($i = 5; $i >= 0; $i--) {
    $loop_date = strtotime("$filter_month -$i months");
    $loop_start = date("Y-m-01", $loop_date);
    $loop_end = date("Y-m-t", $loop_date) . ' 23:59:59';
    $loop_name = date("M", $loop_date);
    
    $qry = $conn->query("SELECT SUM($col_paid) as total FROM payments WHERE status = 1 AND date_created BETWEEN '$loop_start' AND '$loop_end'");
    $row = $qry ? $qry->fetch_assoc() : ['total' => 0];
    
    $trend_months[] = $loop_name;
    $trend_revenues[] = $row['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loving Bloom - Reports</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      .text-navy { color: #001f3f !important; }
      
      .small-box { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
      .card { border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.05); border: none; }
      .card-header { background-color: #fff; border-bottom: 1px solid #f0f0f0; }
      
      @media print {
          .no-print, .main-header, .main-sidebar, .main-footer, .card-tools, .filters { display: none !important; }
          .content-wrapper { margin-left: 0 !important; width: 100% !important; background: white; }
          .card { box-shadow: none !important; border: 1px solid #ddd !important; }
          .print-col-3 { flex: 0 0 25% !important; max-width: 25% !important; }
          .print-col-8 { flex: 0 0 66.66% !important; max-width: 66.66% !important; }
          .print-col-4 { flex: 0 0 33.33% !important; max-width: 33.33% !important; }
          .bg-info, .bg-success, .bg-warning, .bg-danger { -webkit-print-color-adjust: exact !important; color: white !important; }
          .print-only-header { display: block !important; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #001f3f; }
      }
      .print-only-header { display: none; }
      .report-logo { width: 60px; height: 60px; object-fit: contain; }
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
        <div class="row mb-2 align-items-center">
          <div class="col-sm-6">
              <h1 class="m-0 text-navy">
                  <?php echo $report_title; ?>
              </h1>
          </div>
          <div class="col-sm-6 text-right no-print d-flex justify-content-end align-items-center">
              <form method="GET" class="form-inline mr-3">
                  <label class="mr-2 font-weight-normal">Filter Month:</label>
                  <input type="month" name="month" class="form-control form-control-sm" 
                         value="<?php echo isset($_GET['month']) ? $_GET['month'] : ''; ?>" 
                         onchange="this.form.submit()">
                  <?php if($is_filtered): ?>
                      <a href="reports.php" class="btn btn-outline-secondary btn-sm ml-2">Clear Filter</a>
                  <?php endif; ?>
              </form>
              <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> Print PDF</button>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        
        <div class="print-only-header">
            <img src="../favicon.ico" class="report-logo" alt="Logo">
            <h2 style="color:#001f3f; margin:0;">Loving Bloom Daycare</h2>
            <h4><?php echo $report_title; ?></h4>
        </div>

        <div class="row">
          <div class="col-lg-3 col-6 print-col-3">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $display_total_children; ?></h3>
                <p>Enrolled <?php echo $is_filtered ? '(End of Period)' : '(All Time)'; ?></p>
              </div>
              <div class="icon"><i class="fas fa-child"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6 print-col-3">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo number_format($total_income); ?><sup style="font-size: 20px">KES</sup></h3>
                <p>Revenue <?php echo $is_filtered ? '(This Month)' : '(All Time)'; ?></p>
              </div>
              <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6 print-col-3">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo number_format($pending_fees); ?><sup style="font-size: 20px">KES</sup></h3>
                <p>Pending Fees</p>
              </div>
              <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6 print-col-3">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo $staff_count; ?></h3>
                <p>Staff <?php echo $is_filtered ? '(End of Period)' : '(All Time)'; ?></p>
              </div>
              <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
            </div>
          </div>
        </div>

        <div class="row">
            <div class="col-md-8 print-col-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-navy"><i class="fas fa-chart-line mr-1"></i> Revenue Trend (Last 6 Months)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4 print-col-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-navy"><i class="fas fa-chart-pie mr-1"></i> Enrollment Status</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="enrollmentChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title text-navy">Recent Payments: <b><?php echo date("F Y", strtotime($filter_month)); ?></b></h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped table-valign-middle">
                            <thead>
                            <tr>
                                <th>Child Name</th>
                                <th>Amount Paid</th>
                                <th>Payment Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $month_start_pay = $filter_month . '-01';
                            $month_end_pay   = date("Y-m-t", strtotime($month_start_pay)) . ' 23:59:59';

                            $sql_pay = "SELECT p.*, s.child_name 
                                        FROM payments p 
                                        JOIN invoices i ON p.invoice_id = i.id 
                                        JOIN child_info s ON i.child_id = s.id 
                                        WHERE p.date_created BETWEEN '$month_start_pay' AND '$month_end_pay'
                                        ORDER BY p.date_created DESC";
                            
                            $recent_pay = $conn->query($sql_pay);

                            if($recent_pay && $recent_pay->num_rows > 0):
                                while($row = $recent_pay->fetch_assoc()):
                                    $amount_display = isset($row[$col_paid]) ? $row[$col_paid] : (isset($row['amount']) ? $row['amount'] : 0);
                            ?>
                            <tr>
                                <td><?php echo $row['child_name']; ?></td>
                                <td class="text-success font-weight-bold">KES <?php echo number_format($amount_display); ?></td>
                                <td><?php echo date("M d, Y", strtotime($row['date_created'])); ?></td>
                            </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    No payments recorded for <?php echo date("F Y", strtotime($filter_month)); ?>.
                                </td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </section>
  </div>

  <footer class="main-footer no-print">
        <div class="float-right d-none d-sm-inline">Powered by <a href="https://github.com/" target="_blank">Grace Muthoni</a></div>
        <strong>Copyright &copy; <?php echo date("Y"); ?> <a href="/loving_bloom/index.php">Loving Bloom</a>.</strong> All rights reserved.
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
$(function () {
    var ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($trend_months); ?>, 
            datasets: [{
                label: 'Revenue (KES)',
                data: <?php echo json_encode($trend_revenues); ?>, 
                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                borderColor: 'rgba(60, 141, 188, 1)',
                pointRadius: 4,
                pointBackgroundColor: '#3b8bba',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { display: true, color: '#f0f0f0' },
                    ticks: {
                        maxTicksLimit: 8, 
                        callback: function(value) {
                            if(value >= 1000000) return (value/1000000).toFixed(1) + 'M';
                            if(value >= 1000) return (value/1000).toFixed(1) + 'k';
                            return value;
                        }
                    }
                },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });

    var ctxEnroll = document.getElementById('enrollmentChart').getContext('2d');
    var enrollmentChart = new Chart(ctxEnroll, {
        type: 'doughnut',
        data: {
            labels: ['Enrolled', 'Pending', 'Unenrolled', 'Rejected'],
            datasets: [{
                data: [
                    <?php echo $active_children; ?>, 
                    <?php echo $pending_children; ?>, 
                    <?php echo $unenrolled_children; ?>,
                    <?php echo $rejected_children; ?>
                ],
                backgroundColor: ['#28a745', '#ffc107', '#6c757d', '#dc3545'],
                hoverOffset: 4
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
</body>
</html>