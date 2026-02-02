function displayImg(input, _this) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $("#e_photo_display").attr("src", e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }
}

$(document).ready(function () {
  bsCustomFileInput.init();
  var table = $(".datatable").DataTable({ scrollX: true, autoWidth: false });

  var activeTab = localStorage.getItem("activeTab");
  if (activeTab) {
    $('#custom-tabs-four-tab a[href="' + activeTab + '"]').tab("show");
  }

  $('a[data-toggle="pill"]').on("shown.bs.tab", function (e) {
    var currentTab = $(e.target).attr("href");
    localStorage.setItem("activeTab", currentTab);
    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
  });

  $(document).on("click", ".view_details", function () {
    var photo_path = $(this).data("photo");
    if (photo_path == "" || photo_path == null) {
      $("#v_photo_display").attr("src", "../dist/img/no-image-available.png");
    } else {
      $("#v_photo_display").attr("src", "../" + photo_path);
    }
    $("#v_name_header").text($(this).data("name"));
    $("#v_gender").text($(this).data("gender"));
    $("#v_dob").text($(this).data("dob"));
    $("#v_age").text($(this).data("age"));
    $("#v_phone").text($(this).data("phone"));
    $("#v_address").text($(this).data("address"));
    $("#v_blood").text($(this).data("blood") || "N/A");
    $("#v_allergy").text($(this).data("allergy") || "None");
    $("#v_cond").text($(this).data("cond") || "None");
    $("#v_needs").text($(this).data("needs") || "None");

    var status = $(this).data("status");
    if (status != 0) {
      $(".status_btn").hide();
    } else {
      $(".status_btn").show();
    }
    $("#viewModal").modal("show");
  });

  var current_view_id = "";
  $(document).on("click", ".view_details", function () {
    current_view_id = $(this).data("id");
  });

  $(".status_btn").click(function () {
    var status = $(this).data("status");
    var action = status == 1 ? "Accept" : "Reject";
    if (confirm("Are you sure you want to " + action + " this enrollment?")) {
      update_status(current_view_id, status);
    }
  });

  $(document).on("click", ".accept_rejected", function () {
    var id = $(this).data("id");
    var name = $(this).data("name");
    if (
      confirm(
        "Are you sure you want to ACCEPT the rejected enrollment for " +
          name +
          "?"
      )
    ) {
      update_status(id, 1);
    }
  });

  $(document).on("click", ".unenroll_student", function () {
    var id = $(this).data("id");
    var name = $(this).data("name");
    if (confirm("Are you sure you want to UNENROLL " + name + "?")) {
      update_status(id, 3);
    }
  });

  $(document).on("click", ".reenroll_student", function () {
    var id = $(this).data("id");
    var name = $(this).data("name");
    if (confirm("Are you sure you want to RE-ENROLL " + name + "?")) {
      update_status(id, 1);
    }
  });

  $(document).on("click", ".delete_child", function () {
    var id = $(this).data("id");
    var name = $(this).data("name");
    if (
      confirm(
        "Are you sure you want to PERMANENTLY DELETE the record for " +
          name +
          "? This cannot be undone."
      )
    ) {
      $.ajax({
        url: "../classes/Master.php?f=delete_child",
        method: "POST",
        data: { id: id },
        dataType: "json",
        success: function (resp) {
          if (resp.status == "success") {
            location.reload();
          } else {
            alert("Error deleting record: " + resp.msg);
          }
        },
      });
    }
  });

  function update_status(id, status) {
    $.ajax({
      url: "../classes/Master.php?f=update_enrollment_status",
      method: "POST",
      data: { id: id, status: status },
      dataType: "json",
      success: function (resp) {
        if (resp.status == "success") {
          location.reload();
        } else {
          alert("Error updating status");
        }
      },
    });
  }

  $(document).on("click", ".edit_data", function () {
    $("#e_id").val($(this).data("id"));
    $("#e_name").val($(this).data("name"));
    $("#e_dob").val($(this).data("dob"));
    $("#e_gender").val($(this).data("gender"));
    $("#e_parent").val($(this).data("parent"));
    $("#e_phone").val($(this).data("phone"));
    $("#e_address").val($(this).data("address"));
    $("#e_cond").val($(this).data("cond"));

    var photo_path = $(this).data("photo");
    if (photo_path == "" || photo_path == null) {
      $("#e_photo_display").attr("src", "../dist/img/no-image-available.png");
    } else {
      $("#e_photo_display").attr("src", "../" + photo_path);
    }

    var has_bio = $(this).data("biometric");
    if (has_bio == 1) {
      $("#bio_status")
        .text("Fingerprint Registered!")
        .removeClass("text-muted text-danger")
        .addClass("text-success font-weight-bold");
    } else {
      $("#bio_status")
        .text("Not Registered")
        .removeClass("text-success font-weight-bold")
        .addClass("text-muted");
    }

    $("#editModal").modal("show");
  });

  $("#update-enrollment-form").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: "../classes/Master.php?f=update_child_details",
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (resp) {
        if (resp.status == "success") {
          location.reload();
        } else {
          alert("Error updating details: " + resp.error);
        }
      },
    });
  });

  function bufferDecode(value) {
    return Uint8Array.from(atob(value), (c) => c.charCodeAt(0));
  }

  function bufferEncode(value) {
    return btoa(String.fromCharCode.apply(null, new Uint8Array(value)))
      .replace(/\+/g, "-")
      .replace(/\//g, "_")
      .replace(/=/g, "");
  }
  $("#btn_register_biometrics").click(async function () {
    var child_id = $("#e_id").val();
    var child_name = $("#e_name").val();

    if (!child_id) {
      alert(
        "Error: No child selected. Please close and reopen the edit modal."
      );
      return;
    }

    $("#bio_status")
      .text("Touch sensor now...")
      .removeClass("text-danger text-success")
      .addClass("text-primary font-weight-bold");

    var challenge = new Uint8Array(32);
    window.crypto.getRandomValues(challenge);

    var userId = new Uint8Array(16);
    window.crypto.getRandomValues(userId);

    const publicKey = {
      challenge: challenge,
      rp: {
        name: "Loving Bloom Daycare",
        id: window.location.hostname,
      },
      user: {
        id: userId,
        name: child_name,
        displayName: child_name,
      },
      pubKeyCredParams: [
        { alg: -7, type: "public-key" },
        { alg: -257, type: "public-key" },
      ],
      authenticatorSelection: {
        authenticatorAttachment: "platform",
        userVerification: "preferred",
        residentKey: "required",
        requireResidentKey: true,
      },
      timeout: 60000,
    };

    try {
      const credential = await navigator.credentials.create({ publicKey });
      const credId = bufferEncode(credential.rawId);

      $.ajax({
        url: "../classes/Master.php?f=register_biometric",
        method: "POST",
        data: { child_id: child_id, credential_id: credId },
        dataType: "json",
        success: function (resp) {
          if (resp.status == "success") {
            $("#bio_status")
              .text("Fingerprint Registered!")
              .removeClass("text-primary")
              .addClass("text-success");
            alert("Success! Fingerprint linked to " + child_name);
            setTimeout(function () {
              location.reload();
            }, 1000);
          } else {
            $("#bio_status")
              .text("Registration failed.")
              .addClass("text-danger");
            alert("Server Error: " + resp.msg);
          }
        },
      });
    } catch (err) {
      console.error(err);
      $("#bio_status").text("Scan Canceled or Failed").addClass("text-danger");
    }
  });
});
