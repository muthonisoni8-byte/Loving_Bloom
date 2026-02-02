<?php 
session_start();
if(!isset($_SESSION['userdata'])){ header("Location: login.php"); exit; } 
require_once('../db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loving Bloom - Inquiries</title>
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
                <div class="col-sm-6"><h1 class="m-0 text-navy">Inquiries / Messages</h1></div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary shadow-sm border-0">
                <div class="card-body">
                    <table id="list" class="table table-hover table-striped">
                        <colgroup>
                            <col width="5%">
                            <col width="15%">
                            <col width="20%">
                            <col width="30%">
                            <col width="10%">
                            <col width="20%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i = 1;
                            $qry = $conn->query("SELECT * FROM messages ORDER BY status ASC, date_created DESC");
                            while($row = $qry->fetch_assoc()):
                            ?>
                            <tr id="row_<?php echo $row['id'] ?>" class="<?php echo ($row['status'] == 0) ? 'font-weight-bold bg-light' : '' ?>">
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
                                <td>
                                    <?php echo ucwords($row['fullname']) ?> 
                                    <br> 
                                    <small class="text-muted"><?php echo $row['email'] ?></small>
                                </td>
                                <td><?php echo $row['subject'] ?></td>
                                <td class="text-center status-cell">
                                    <?php if($row['status'] == 0): ?>
                                        <span class="badge badge-danger">Unread</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td align="center">
                                    <button type="button" class="btn btn-flat btn-default btn-sm view_data" 
                                            data-id="<?php echo $row['id'] ?>"
                                            data-name="<?php echo ucwords($row['fullname']) ?>"
                                            data-email="<?php echo $row['email'] ?>"
                                            data-subject="<?php echo $row['subject'] ?>"
                                            data-message="<?php echo htmlspecialchars($row['message']) ?>"
                                            data-date="<?php echo date("F d, Y h:i A", strtotime($row['date_created'])) ?>"
                                            data-status="<?php echo $row['status'] ?>">
                                        <span class="fa fa-eye text-primary"></span> View
                                    </button>
                                    
                                    <?php if($row['status'] == 0): ?>
                                    <button type="button" class="btn btn-flat btn-default btn-sm update_status" data-id="<?php echo $row['id'] ?>" data-status="1" title="Currently Unread - Click to Mark Read">
                                        <span class="fa fa-envelope text-info"></span> 
                                    </button>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-flat btn-default btn-sm update_status" data-id="<?php echo $row['id'] ?>" data-status="0" title="Currently Read - Click to Mark Unread">
                                        <span class="fa fa-envelope-open text-warning"></span> 
                                    </button>
                                    <?php endif; ?>

                                    <button type="button" class="btn btn-flat btn-default btn-sm delete_data" data-id="<?php echo $row['id'] ?>" title="Delete">
                                        <span class="fa fa-trash text-danger"></span>
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

  <footer class="main-footer no-print">
        <div class="float-right d-none d-sm-inline">Powered by <a href="https://github.com/" target="_blank">Grace Muthoni</a></div>
        <strong>Copyright &copy; <?php echo date("Y"); ?> <a href="/loving_bloom/index.php">Loving Bloom</a>.</strong> All rights reserved.
    </footer>
</div>

<div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Message Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function(){
        $('#list').DataTable();
        $('.delete_data').click(function(){
            var id = $(this).attr('data-id');
            if(confirm("Are you sure to delete this message permanently?")){
                $.ajax({
                    url: '../classes/Master.php?f=delete_message',
                    method: 'POST',
                    data: {id: id},
                    success:function(resp){
                        location.reload();
                    }
                });
            }
        });

        $('.update_status').click(function(){
            var btn = $(this);
            var id = btn.attr('data-id');
            var status = btn.attr('data-status');
            
            $.ajax({
                url: '../classes/Master.php?f=update_message_status',
                method: 'POST',
                data: {id: id, status: status},
                success:function(resp){
                    location.reload();
                }
            });
        });
        $('.view_data').click(function(){
            var btn = $(this);
            var id = btn.data('id');
            var name = btn.data('name');
            var email = btn.data('email');
            var subject = btn.data('subject');
            var message = btn.data('message');
            var date = btn.data('date');
            var status = btn.data('status');
            if(status == 0){
                $.ajax({
                    url: '../classes/Master.php?f=update_message_status',
                    method: 'POST',
                    data: {id: id, status: 1},
                    dataType: 'json',
                    success: function(resp){
                        if(resp.status == 'success'){
                            var row = $('#row_' + id);
                            row.removeClass('font-weight-bold bg-light');
                            
                            row.find('.status-cell').html('<span class="badge badge-success">Read</span>');
                            
                            var actionBtn = row.find('.update_status');
                            actionBtn.attr('data-status', 0);
                            actionBtn.attr('title', 'Mark as Unread');
                            actionBtn.find('span').removeClass('fa-envelope text-info').addClass('fa-envelope-open text-warning');
                            btn.data('status', 1);
                            var badge = $('a[href*="inquiries.php"] .badge');
                            if(badge.length > 0){
                                var count = parseInt(badge.text());
                                if(count > 1) { 
                                    badge.text(count - 1); 
                                } else { 
                                    badge.hide(); 
                                }
                            }
                        }
                    }
                });
            }

            var content = `
                <dl>
                    <dt>Sender Name:</dt><dd>${name}</dd>
                    <dt>Sender Email:</dt><dd><a href="mailto:${email}">${email}</a></dd>
                    <dt>Date Received:</dt><dd>${date}</dd>
                    <hr>
                    <dt>Subject:</dt><dd>${subject}</dd>
                    <dt class="mt-3">Message:</dt>
                    <dd class="p-3 bg-light border rounded" style="white-space: pre-wrap;">${message}</dd>
                </dl>
            `;
            $('#uni_modal .modal-body').html(content);
            $('#uni_modal').modal('show');
        });
    })
</script>
</body>
</html>