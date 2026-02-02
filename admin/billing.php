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
  <title>Loving Bloom - Billing</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
      
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      .text-navy { color: #001f3f !important; }
      
      .wrap-text { white-space: normal !important; max-width: 300px; word-wrap: break-word; }
      .action-group { display: flex; gap: 5px; justify-content: center; }
      .action-btn { white-space: nowrap; min-width: 85px; }
      
      .receipt-logo { height: 70px; width: 70px; object-fit: contain; margin-bottom: 10px; }
      .invoice-card { border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); background: #fff; }
      .invoice-header { background-color: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0; }
      
      @media print { 
          body * { visibility: hidden; }
          .modal-footer, .no-print, .action-group, .dataTables_wrapper .row:first-child, .dataTables_wrapper .row:last-child { display: none !important; }

          body.printing-receipt #print_area, body.printing-receipt #print_area * { 
              visibility: visible !important; 
          }
          body.printing-receipt #print_area { 
              position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; background: white; 
          }
          
          body.printing-receipt .modal-dialog { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
          body.printing-receipt .modal-content { border: none !important; box-shadow: none !important; width: 100% !important; }
          
          body.printing-receipt .invoice-card { width: 100% !important; max-width: 100% !important; border: 1px solid #dee2e6 !important; box-shadow: none !important; margin: 0 !important; }
          
          body.printing-receipt .table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #dee2e6 !important; }
          body.printing-receipt .table th, body.printing-receipt .table td { border: 1px solid #dee2e6 !important; padding: 10px !important; }
          body.printing-receipt .table thead th { background-color: #f8f9fa !important; border-bottom: 2px solid #dee2e6 !important; color: #333 !important; }
          
          body.printing-receipt .no-print-head { display: block !important; }

          body.printing-list .card-body, body.printing-list .card-body * { visibility: visible !important; }
          body.printing-list .list-print-header, body.printing-list .list-print-header * { visibility: visible !important; display: block !important; }
          body.printing-list .card-body { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; background: white; }
          body.printing-list #invoice_list { width: 100% !important; border-collapse: collapse !important; border: 1px solid #dee2e6 !important; }
          body.printing-list #invoice_list th, body.printing-list #invoice_list td { border: 1px solid #dee2e6 !important; padding: 8px !important; }
          .text-success { color: #28a745 !important; -webkit-print-color-adjust: exact; }
          .text-danger { color: #dc3545 !important; -webkit-print-color-adjust: exact; }
          
          .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate { display: none !important; }
      }
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
          <div class="col-sm-6"><h1 class="m-0 text-navy">Billing & Invoices</h1></div>
          <div class="col-sm-6 text-right">
              <button class="btn btn-success shadow" id="generate_bills">
                  <i class="fas fa-calendar-plus mr-2"></i> Generate Invoices for <?php echo date('F'); ?>
              </button>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm border-0">
            
            <div class="card-header bg-white border-bottom-0">
                <div class="row">
                    <div class="col-md-3">
                        <label>Filter Month:</label>
                        <input type="month" id="filter_month" class="form-control" value="<?php echo date('Y-m'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Payment Status:</label>
                        <select id="filter_status" class="form-control">
                            <option value="all">All Statuses</option>
                            <option value="unpaid">Unpaid / Arrears</option>
                            <option value="partial">Partial Payment</option>
                            <option value="paid">Fully Paid</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary mr-2" id="apply_filters"><i class="fas fa-filter"></i> Apply</button>
                        <button class="btn btn-secondary" id="print_table"><i class="fas fa-print"></i> Print List</button>
                    </div>
                </div>
            </div>

          <div class="card-body">
            
            <div class="list-print-header text-center mb-4" style="display:none;">
                <img src="../favicon.ico" class="receipt-logo">
                <h3 class="font-weight-bold">Loving Bloom Daycare</h3>
                <h5>Billing Status Report</h5>
                <p>Generated on: <?php echo date("F d, Y"); ?></p>
                <hr style="border-top: 1px solid #dee2e6;">
            </div>

            <table class="table table-bordered table-striped" id="invoice_list">
              <thead>
                <tr>
                  <th width="5%">#</th>
                  <th width="12%">Date</th>
                  <th width="20%">Child Name</th>
                  <th width="25%">Description</th>
                  <th width="10%">Total</th>
                  <th width="10%">Balance</th>
                  <th width="8%">Status</th>
                  <th width="15%" class="no-print">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $i = 1;
                $qry = $conn->query("SELECT i.*, c.child_name, c.birth_reg_no FROM invoices i INNER JOIN child_info c ON i.child_id = c.id WHERE i.status != 3 ORDER BY i.date_created DESC");
                if($qry){
                    while($row = $qry->fetch_assoc()):
                        $monthVal = date('Y-m', strtotime($row['date_created']));
                        $statusVal = ($row['status'] == 1) ? 'paid' : (($row['balance'] < $row['amount']) ? 'partial' : 'unpaid');
                        
                        $displayBalance = number_format($row['balance'], 2);
                        $balanceClass = "text-danger";
                        if($row['balance'] < 0) {
                            $displayBalance = "(" . number_format(abs($row['balance']), 2) . ") CR";
                            $balanceClass = "text-success";
                        }
                ?>
                <tr class="invoice-row" data-month="<?php echo $monthVal; ?>" data-status="<?php echo $statusVal; ?>">
                  <td><?php echo $i++ ?></td>
                  <td><?php echo date("Y-m-d", strtotime($row['date_created'])) ?></td>
                  <td>
                      <?php echo $row['child_name'] ?> 
                      <br><small class="text-muted"><?php echo $row['birth_reg_no'] ?></small>
                  </td>
                  <td class="wrap-text"><?php echo $row['title'] ?></td>
                  <td class="text-right"><?php echo number_format($row['amount'], 2) ?></td>
                  <td class="text-right font-weight-bold <?php echo $balanceClass; ?>"><?php echo $displayBalance; ?></td>
                  <td class="text-center">
                    <?php if($row['status'] == 1): ?>
                        <span class="badge badge-success">Paid</span>
                    <?php elseif($row['status'] == 2): ?>
                        <span class="badge badge-warning">Partial</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Unpaid</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center no-print">
                      <div class="action-group">
                        <button class="btn btn-sm btn-primary action-btn record_payment" 
                            data-id="<?php echo $row['id'] ?>" 
                            data-balance="<?php echo $row['balance'] ?>"
                            data-child-id="<?php echo $row['child_id'] ?>"
                            data-child-name="<?php echo $row['child_name'] ?>">
                            <i class="fas fa-hand-holding-usd mr-1"></i> Pay
                        </button>
                        
                        <button class="btn btn-sm btn-info action-btn view_full_history" 
                            data-child-id="<?php echo $row['child_id'] ?>" 
                            data-child-name="<?php echo $row['child_name'] ?>">
                            <i class="fas fa-history mr-1"></i> History
                        </button>
                      </div>
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

  <div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white"><h5 class="modal-title">Record Payment</h5></div>
        <form id="payment-form">
            <input type="hidden" name="invoice_id" id="p_invoice_id">
            <input type="hidden" id="p_child_id">
            <input type="hidden" id="p_child_name">
            <div class="modal-body">
                <div class="form-group">
                    <label>Balance Due</label>
                    <input type="text" id="p_balance_display" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Amount to Pay</label>
                    <input type="number" name="amount_paid" id="p_amount" class="form-control" required min="1" step="0.01">
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Confirm Payment</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="historyModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Billing History</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body" id="print_area">
            
            <div class="receipt-header no-print-head" style="display:none;">
                <center>
                    <img src="../favicon.ico" class="receipt-logo">
                    <h2 class="text-primary font-weight-bold" style="color: #001f3f !important;">Loving Bloom Daycare</h2>
                    <h4 class="font-weight-bold">Official Payment Receipt</h4>
                    <p style="font-size: 1.2rem; margin-top:10px;">Student: <span id="hist_child_name_print" class="font-weight-bold"></span></p>
                    <small class="text-muted">Generated on <?php echo date("F d, Y h:i A"); ?></small>
                </center>
                <hr style="border-top: 2px solid #dee2e6; margin: 20px 0;">
            </div>
            
            <h5 class="mb-3 text-center d-print-none">History for: <span id="hist_child_name" class="font-weight-bold text-primary"></span></h5>

            <div id="history_content"></div>
            
            <div class="text-center mt-5 text-muted small no-print-head" style="display:none;">
                <p>Thank you for your payment!</p>
                <p>Loving Bloom Daycare - 123 Daycare Lane, Cityville</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button class="btn btn-success" id="btn_print_receipt"><i class="fas fa-print"></i> Print Receipt</button>
        </div>
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
        var table = $('#invoice_list').DataTable();

        $('#historyModal').on('hidden.bs.modal', function () { location.reload(); });

        $('#apply_filters').click(function(){
            var month = $('#filter_month').val();
            var status = $('#filter_status').val();
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var row = $(table.row(dataIndex).node());
                var rowMonth = row.data('month');
                var rowStatus = row.data('status');
                if (month && rowMonth !== month) return false;
                if (status !== 'all' && rowStatus !== status) return false;
                return true;
            });
            table.draw();
            $.fn.dataTable.ext.search.pop();
        });

        $('#print_table').click(function(){
            $('body').addClass('printing-list');
            window.print();
            setTimeout(function(){ $('body').removeClass('printing-list'); }, 1000);
        });

        $('#generate_bills').click(function(){
            Swal.fire({
                title: 'Generate Monthly Invoices?',
                text: "This will create new invoices for <?php echo date('F Y'); ?>.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Generate',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../classes/Master.php?f=generate_monthly_bills',
                        dataType: 'json',
                        success: function(resp){
                            if(resp.status == 'success'){
                                Swal.fire('Success', resp.msg, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', resp.msg, 'error');
                            }
                        }
                    });
                }
            })
        });

        $(document).on('click', '.record_payment', function(){
            var id = $(this).data('id');
            var balance = $(this).data('balance');
            var childId = $(this).data('child-id');
            var childName = $(this).data('child-name');
            $('#p_invoice_id').val(id);
            $('#p_child_id').val(childId);
            $('#p_child_name').val(childName);
            $('#p_balance_display').val(parseFloat(balance).toLocaleString('en-US'));
            $('#p_amount').val(''); 
            $('#payModal').modal('show');
        });

        $('#payment-form').submit(function(e){
            e.preventDefault();
            var childId = $('#p_child_id').val();
            var childName = $('#p_child_name').val();
            $.ajax({
                url: '../classes/Master.php?f=save_payment',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp){
                    if(resp.status == 'success'){
                        $('#payModal').modal('hide'); 
                        Swal.fire({
                            title: 'Success!',
                            text: resp.msg,
                            icon: 'success',
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => { 
                            loadChildHistory(childId, childName, true); 
                        });
                    } else {
                        Swal.fire('Error', resp.msg, 'error');
                    }
                }
            })
        });

        $(document).on('click', '.view_full_history', function(){
            var childId = $(this).data('child-id');
            var childName = $(this).data('child-name');
            loadChildHistory(childId, childName);
        });

        $(document).on('click', '.delete_payment', function(){
            var payId = $(this).data('id');
            var childId = $(this).data('child-id');
            var childName = $(this).data('child-name');

            Swal.fire({
                title: 'Void Transaction?',
                text: "This will void the payment and restore the balance.",
                icon: 'warning',
                input: 'text',
                inputLabel: 'Reason for voiding',
                inputPlaceholder: 'e.g., Wrong amount entered',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!',
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('You need to write a reason!')
                    }
                    return reason
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../classes/Master.php?f=delete_payment',
                        method: 'POST',
                        data: { 
                            payment_id: payId,
                            reason: result.value 
                        },
                        dataType: 'json',
                        success: function(resp){
                            if(resp.status == 'success'){
                                Swal.fire('Voided!', resp.msg, 'success');
                                loadChildHistory(childId, childName); 
                            } else {
                                Swal.fire('Error', resp.msg, 'error');
                            }
                        }
                    });
                }
            })
        });

        function loadChildHistory(childId, childName, autoPrint = false){
            $('#hist_child_name').text(childName);
            $('#hist_child_name_print').text(childName);
            $('#history_content').html('<p class="text-center p-4">Loading receipts...</p>');
            $.ajax({
                url: '../classes/Master.php?f=get_child_billing_history',
                method: 'POST',
                data: {child_id: childId},
                dataType: 'json',
                success: function(resp){
                    var html = '';
                    if(resp.history.length > 0){
                        resp.history.forEach(function(inv){
                            html += '<div class="invoice-card">';
                            html += '<div class="invoice-header">';
                            html += '<span><strong>' + inv.title + '</strong></span>';
                            html += '<span class="badge badge-secondary" style="font-size:0.9em;">' + inv.date_formatted + '</span>';
                            html += '</div>';
                            html += '<div class="card-body p-3">';
                            html += '<div class="d-flex justify-content-between mb-3 border-bottom pb-2">';
                            html += '<span>Total: <b>' + inv.total_formatted + '</b></span>';
                            var balText = inv.balance_formatted;
                            var balClass = "text-danger";
                            if(parseFloat(inv.balance) < 0){
                                balText = "(" + Math.abs(parseFloat(inv.balance)).toLocaleString() + ") CR";
                                balClass = "text-success";
                            }
                            html += '<span>Balance: <b class="' + balClass + '">' + balText + '</b></span>';
                            html += '</div>';
                            if(inv.payments.length > 0){
                                html += '<h6 class="font-weight-bold text-muted small">Payment History</h6>';
                                html += '<table class="table table-sm table-bordered mb-0" style="font-size:0.9em;">';
                                html += '<thead class="bg-light"><tr><th>Date</th><th class="text-right">Amount</th><th class="text-center no-print" width="50">Action</th></tr></thead><tbody>';
                                inv.payments.forEach(function(pay){
                                    var rowClass = (pay.status == 0) ? 'text-muted' : '';
                                    var amountStyle = (pay.status == 0) ? 'text-decoration: line-through; color: #dc3545;' : 'color: #28a745; font-weight: bold;';
                                    var amountText = pay.amount_formatted;
                                    
                                    if(pay.status == 0) {
                                        amountText += ' <span class="badge badge-danger ml-1" style="font-size:0.7em;">VOID</span>';
                                        if(pay.void_reason) {
                                            amountText += '<div class="small text-danger font-italic">Reason: ' + pay.void_reason + '</div>';
                                        }
                                    }

                                    html += '<tr class="'+rowClass+'">';
                                    html += '<td>'+pay.date_formatted+'</td>';
                                    html += '<td class="text-right" style="'+amountStyle+'">'+amountText+'</td>';
                                    html += '<td class="text-center no-print">';
                                    if(pay.status == 1){
                                        html += '<button class="btn btn-xs btn-outline-danger delete_payment" data-id="'+pay.id+'" data-child-id="'+childId+'" data-child-name="'+childName+'" title="Void"><i class="fas fa-ban"></i></button>';
                                    } else {
                                        html += '<span class="text-muted"><i class="fas fa-ban"></i></span>';
                                    }
                                    html += '</td>';
                                    html += '</tr>';
                                });
                                html += '</tbody></table>';
                            } else {
                                html += '<div class="alert alert-light text-center small mb-0">No payments recorded.</div>';
                            }
                            html += '</div></div>';
                        });
                    } else {
                        html = '<div class="alert alert-info text-center">No billing history found.</div>';
                    }
                    $('#history_content').html(html);
                    $('#historyModal').modal('show');

                    if(autoPrint){
                        setTimeout(function(){
                            $('#btn_print_receipt').click();
                        }, 500);
                    }
                }
            });
        }

        $('#btn_print_receipt').click(function(){
            var childName = $('#hist_child_name_print').text().trim();
            var originalTitle = document.title;
            document.title = "Receipt - " + childName;
            $('body').addClass('printing-receipt');
            window.print();
            setTimeout(function(){ 
                document.title = originalTitle; 
                $('body').removeClass('printing-receipt');
            }, 1000);
        });
    });
</script>
</body>
</html>