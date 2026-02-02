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
  <title>Loving Bloom - Resources</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  
  <style>
      body { font-family: 'Poppins', sans-serif; font-weight: 400; }
      h1, h2, h3, h4, h5, h6 { font-weight: 500; }
      .text-navy { color: #001f3f !important; }
      
      .book-cover-thumb {
          width: 40px; height: 60px; object-fit: cover;
          border-radius: 4px; border: 1px solid #ddd;
          background-color: #f0f0f0;
      }
      #cover_preview {
          max-height: 200px; border: 1px solid #ddd;
          padding: 3px; border-radius: 4px;
          margin-top: 10px; display: block;
          max-width: 100%; object-fit: contain;
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
    <ul class="navbar-nav ml-auto"><li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li></ul>
  </nav>

  <?php include 'includes/sidebar.php'; ?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0 text-navy">Study Materials Library</h1></div>
          <div class="col-sm-6 text-right">
              <button class="btn btn-primary shadow-sm" id="create_new"><i class="fa fa-plus"></i> Add New Material</button>
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
                            <th width="50">Cover</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Target Age</th>
                            <th>File</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT id, title, description, subject, class_level, file_path, cover_image, date_uploaded FROM study_materials ORDER BY date_uploaded DESC LIMIT 150");
                        while($row = $qry->fetch_assoc()):
                            $cover_path = !empty($row['cover_image']) ? "../".$row['cover_image'] : "";
                            $display_img = $cover_path ? $cover_path : "../dist/img/no-image-available.png";
                            
                            $file_name_display = !empty($row['file_path']) ? basename($row['file_path']) : "";
                            $cover_name_display = !empty($row['cover_image']) ? basename($row['cover_image']) : "";
                        ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td class="text-center">
                                <img src="<?php echo $display_img ?>" class="book-cover-thumb" loading="lazy" onerror="this.src='../dist/img/no-image-available.png'">
                            </td>
                            <td>
                                <b><?php echo $row['title'] ?></b>
                            </td>
                            <td><?php echo $row['subject'] ?></td>
                            <td><span class="badge badge-info"><?php echo $row['class_level'] ?></span></td>
                            <td>
                                <a href="../<?php echo $row['file_path'] ?>" target="_blank" class="text-danger">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </td>
                            <td><?php echo date("M d, Y", strtotime($row['date_uploaded'])) ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-flat btn-info edit_data" 
                                    data-id="<?php echo $row['id'] ?>"
                                    data-title="<?php echo $row['title'] ?>"
                                    data-subject="<?php echo $row['subject'] ?>"
                                    data-class_level="<?php echo $row['class_level'] ?>"
                                    data-description="<?php echo htmlspecialchars($row['description']) ?>"
                                    data-cover="<?php echo $cover_path ?>"
                                    data-file-name="<?php echo $file_name_display ?>"
                                    data-cover-name="<?php echo $cover_name_display ?>"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-flat btn-danger delete_data" data-id="<?php echo $row['id'] ?>">
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

  <div class="modal fade" id="uni_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document"> 
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal_title">Upload Study Material</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form id="material-form" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Subject/Topic</label>
                            <input type="text" name="subject" id="subject" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Target Age Group</label>
                            <select name="class_level" id="class_level" class="form-control">
                                <option>0 - 12 Months</option>
                                <option>1 - 2 Years</option>
                                <option>2 - 3 Years</option>
                                <option>3 - 4 Years</option>
                                <option>4 - 5 Years</option>
                                <option>5+ Years</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Book Cover Image</label>
                            <div class="custom-file mb-2">
                                <input type="file" class="custom-file-input" name="cover_img" id="coverFile" accept="image/*" onchange="displayCover(this)">
                                <label class="custom-file-label" for="coverFile">Select Image</label>
                            </div>
                            <div class="text-center p-2" style="background: #f8f9fa; border: 1px dashed #ced4da; border-radius: 5px;">
                                <img src="../dist/img/no-image-available.png" id="cover_preview" alt="Preview" style="max-height: 200px;">
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label>Material File (PDF)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="material_file" id="customFile" accept="application/pdf">
                                <label class="custom-file-label" for="customFile">Select PDF</label>
                            </div>
                            <small class="text-muted">Leave blank if keeping current file.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="submit_btn">Save</button>
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
    function displayCover(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cover_preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(function () {
        bsCustomFileInput.init();
        
        var table = $('#list').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "deferRender": true 
        }); 

        $('#create_new').click(function(){
            $('#material-form')[0].reset();
            $('#id').val('');
            $('#customFile').next('.custom-file-label').html('Select PDF');
            $('#coverFile').next('.custom-file-label').html('Select Image');
            $('#modal_title').text('Upload New Material');
            $('#cover_preview').attr('src', '../dist/img/no-image-available.png').show(); 
            
            $('#submit_btn').text('Upload');
            $('#uni_modal').modal('show');
        });

        $('body').on('click', '.edit_data', function(){
            var id = $(this).data('id');
            var title = $(this).data('title');
            var subject = $(this).data('subject');
            var class_level = $(this).data('class_level');
            var desc = $(this).data('description');
            var cover = $(this).data('cover');
            var fileName = $(this).data('file-name');
            var coverName = $(this).data('cover-name');

            $('#id').val(id);
            $('#title').val(title);
            $('#subject').val(subject);
            $('#class_level').val(class_level);
            $('#description').val(desc);

            if(fileName){
                $('#customFile').next('.custom-file-label').html(fileName);
            } else {
                $('#customFile').next('.custom-file-label').html('Select PDF');
            }

            if(coverName){
                $('#coverFile').next('.custom-file-label').html(coverName);
            } else {
                $('#coverFile').next('.custom-file-label').html('Select Image');
            }

            if(cover && cover !== ""){
                $('#cover_preview').attr('src', cover).show();
            } else {
                $('#cover_preview').attr('src', '../dist/img/no-image-available.png').show();
            }

            $('#modal_title').text('Update Study Material');
            $('#submit_btn').text('Update');
            $('#uni_modal').modal('show');
        });

        $('#material-form').submit(function(e){
            e.preventDefault();
            var btn = $('#submit_btn');
            var originalText = btn.text();
            
            if($('#id').val() == '' && $('#customFile').val() == ''){
                alert("Please select a PDF file.");
                return;
            }

            btn.attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            var formData = new FormData($(this)[0]);

            $.ajax({
                url: '../classes/Master.php?f=save_material',
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
                        alert("Error: " + (resp.msg || "Unknown error"));
                        btn.attr('disabled', false).text(originalText);
                    }
                },
                error: function(xhr, status, error){
                    alert("SERVER ERROR: " + xhr.responseText); 
                    btn.attr('disabled', false).text(originalText);
                }
            })
        });

        $('body').on('click', '.delete_data', function(){
            if(confirm("Are you sure you want to delete this file?")){
                var id = $(this).data('id');
                $.ajax({
                    url: '../classes/Master.php?f=delete_material',
                    method: 'POST',
                    data: {id:id},
                    dataType: 'json',
                    success: function(resp){
                        if(resp.status == 'success'){
                            location.reload();
                        }else{
                            alert("Error deleting file.");
                        }
                    }
                })
            }
        });
    });
</script>
</body>
</html>