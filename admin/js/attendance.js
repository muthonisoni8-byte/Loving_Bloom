$(document).ready(function () {
  $("#list").DataTable();

  $("#attendance_filter").change(function () {
    var date = $(this).val();
    window.location.href = "attendance.php?date=" + date;
  });

  $(".mark_attendance").click(function () {
    var child_id = $(this).data("id");
    var status = $(this).data("status");
    var btn = $(this);
    btn.prop("disabled", true);
    var selected_date = $("#attendance_filter").val();

    $.ajax({
      url: "../classes/Master.php?f=save_attendance",
      method: "POST",
      data: { child_id: child_id, status: status, date: selected_date },
      dataType: "json",
      success: function (resp) {
        if (resp.status == "success") {
          location.reload();
        } else {
          alert("Error updating attendance: " + resp.msg);
          btn.prop("disabled", false);
        }
      },
    });
  });
  $(".view_details").click(function () {
    var btn = $(this);

    var photo_path = btn.data("photo");
    var photoSrc =
      photo_path && photo_path !== ""
        ? "../" + photo_path
        : "../dist/img/no-image-available.png";

    $("#v_photo_display").attr("src", photoSrc);
    $("#v_name_header").text(btn.data("name"));

    $("#v_child_name").text(btn.data("name"));
    $("#v_reg_no").text(btn.closest("tr").find("td:eq(2)").text());
    $("#v_gender").text(btn.data("gender"));
    $("#v_dob").text(btn.data("dob"));
    $("#v_age").text(btn.data("age") + " Years");
    $("#v_phone").text(btn.data("phone"));
    $("#v_address").text(btn.data("address"));
    $("#v_blood").text(btn.data("blood") || "N/A");
    $("#v_allergy").text(btn.data("allergy") || "None");
    $("#v_cond").text(btn.data("cond") || "None");
    $("#v_needs").text(btn.data("needs") || "None");

    $(".status_btn").hide();

    if ($("#viewDetailsModal").length) {
      $("#viewDetailsModal").modal("show");
    } else if ($("#viewModal").length) {
      $("#viewModal").modal("show");
    } else if ($("#uni_modal").length) {
      $("#uni_modal").modal("show");
    } else {
      alert("Error: Details modal not found in HTML.");
    }
  });

  var current_history_id = "";

  $(".view_history").click(function () {
    var child_id = $(this).data("id");
    var child_name = $(this).data("name");

    current_history_id = child_id;
    $("#h_child_name").text(child_name);

    var d = new Date();
    var monthStr = d.toISOString().slice(0, 7);
    $("#history_month_filter").val(monthStr);

    loadHistory(child_id, monthStr);
    $("#historyModal").modal("show");
  });

  $("#history_month_filter").change(function () {
    var month = $(this).val();
    loadHistory(current_history_id, month);
  });

  $("#reset_history_filter").click(function () {
    $("#history_month_filter").val("");
    loadHistory(current_history_id, "");
  });

  function loadHistory(id, month) {
    $("#history_table_body").html(
      '<tr><td colspan="2" class="text-center">Loading...</td></tr>'
    );
    $.ajax({
      url: "../classes/Master.php?f=get_attendance_history",
      method: "POST",
      data: { child_id: id, month: month },
      dataType: "json",
      success: function (resp) {
        if (resp.status == "success") {
          var rows = "";
          if (resp.data.length > 0) {
            $.each(resp.data, function (index, item) {
              var statusBadge = "";

              if (item.status == 1) {
                statusBadge =
                  '<span class="badge badge-success">Present</span>';
              } else if (item.status == 0) {
                statusBadge = '<span class="badge badge-danger">Absent</span>';
              } else if (item.status == "not_marked") {
                statusBadge =
                  '<span class="badge badge-secondary">Not Marked</span>';
              } else {
                statusBadge = '<span class="badge badge-light">-</span>';
              }

              rows +=
                "<tr><td>" +
                item.attendance_date +
                "</td><td>" +
                statusBadge +
                "</td></tr>";
            });
          } else {
            rows =
              '<tr><td colspan="2" class="text-center">No attendance records found.</td></tr>';
          }
          $("#history_table_body").html(rows);
        } else {
          alert("Error fetching history");
        }
      },
    });
  }

  $("#print_history_btn").click(function () {
    var content = $("#history_print_area").html();
    var name = $("#h_child_name").text();
    var printWindow = window.open("", "", "height=600,width=800");
    printWindow.document.write(
      "<html><head><title>Attendance History - " + name + "</title>"
    );
    printWindow.document.write(
      '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">'
    );
    printWindow.document.write(
      "<style>body{padding:20px; text-align:center;} table{width:100%; border-collapse:collapse;} th,td{border:1px solid #ddd; padding:8px;} img{height:50px; margin-bottom:10px;}</style>"
    );
    printWindow.document.write("</head><body>");

    printWindow.document.write('<img src="../favicon.ico" alt="Logo">');
    printWindow.document.write("<h2>Loving Bloom Daycare</h2>");
    printWindow.document.write("<h3>Attendance History: " + name + "</h3>");

    printWindow.document.write(content);
    printWindow.document.write("</body></html>");
    printWindow.document.close();
    printWindow.print();
  });
});
