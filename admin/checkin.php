<?php
session_start();
if(!isset($_SESSION['userdata'])){ header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loving Bloom | Smart Check-In</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
      body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
      .checkin-card { width: 100%; max-width: 550px; text-align: center; padding: 40px; border-radius: 20px; border: none; }
      .child-photo { width: 160px; height: 160px; object-fit: cover; border-radius: 50%; border: 6px solid #28a745; margin: 0 auto 25px; display: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
      .status-msg { font-size: 24px; font-weight: 600; margin: 15px 0; min-height: 40px; color: #495057; }
      
      .manual-input { font-size: 20px; text-align: center; height: 60px; border-radius: 12px; border: 2px solid #e9ecef; box-shadow: none; transition: 0.3s; }
      .manual-input:focus { border-color: #007bff; box-shadow: 0 0 0 4px rgba(0,123,255,0.1); }
      
      .biometric-btn { font-size: 60px; color: #343a40; cursor: pointer; transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); margin: 20px; background: #fff; border-radius: 50%; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
      .biometric-btn:hover { color: #28a745; transform: scale(1.1); box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2); }
      
      .divider { display: flex; align-items: center; text-align: center; color: #adb5bd; margin: 30px 0; font-weight: 500; font-size: 14px; letter-spacing: 1px; }
      .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #dee2e6; }
      .divider:not(:empty)::before { margin-right: .5em; }
      .divider:not(:empty)::after { margin-left: .5em; }

      .btn-primary { border-radius: 10px; padding: 12px; font-weight: 600; letter-spacing: 0.5px; }
  </style>
</head>
<body>

<div class="card checkin-card shadow-lg">
    <div class="card-body">
        <h2 class="text-dark font-weight-bold mb-1">Smart Check-In</h2>
        <p class="text-muted mb-4">Loving Bloom Daycare</p>
        
        <img id="display_photo" class="child-photo" src="" alt="Student">
        <div id="status_text" class="status-msg">Ready to Scan...</div>

        <div class="row justify-content-center">
            <div class="col-auto text-center">
                <i class="fas fa-fingerprint biometric-btn" id="btn_webauthn" title="Touch Sensor to Scan"></i>
                <p class="small text-muted font-weight-bold mt-1">TAP SENSOR</p>
            </div>
            <div class="col-auto text-center" id="usb_status_area" style="display:none;">
                <i class="fas fa-hdd biometric-btn" style="color:#17a2b8;"></i>
                <p class="small text-muted font-weight-bold mt-1">USB READY</p>
            </div>
        </div>

        <div class="divider">OR USE REG NUMBER</div>

        <form id="manual_form">
            <div class="form-group">
                <input type="text" id="reg_no_input" class="manual-input form-control" placeholder="REG-XXXXXX" autocomplete="off" autofocus>
            </div>
            <button type="submit" class="btn btn-primary btn-block shadow-sm">Check In</button>
        </form>

        <div class="mt-4 pt-2">
            <a href="attendance.php" class="text-secondary small font-weight-bold" style="text-decoration: none;">
                <i class="fas fa-arrow-left mr-1"></i> DASHBOARD
            </a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function handleSuccess(child_name, photo_url) {
        $('#status_text').text('Welcome, ' + child_name + '!').css('color', '#28a745');
        var photo = photo_url ? '../' + photo_url : '../dist/img/no-image-available.png';
        $('#display_photo').attr('src', photo).fadeIn();
        $('#reg_no_input').val('');

        setTimeout(function(){
            $('#display_photo').fadeOut();
            $('#status_text').text('Ready to Scan...').css('color', '#495057');
        }, 3000);
    }

    function handleError(msg) {
        console.error(msg);
        $('#status_text').text(msg).css('color', '#dc3545');
    }

    $(document).ready(function(){
        $('#reg_no_input').focus();
        $('body').click(function(e) { if(e.target.id != 'btn_webauthn') $('#reg_no_input').focus(); });

        $('#manual_form').submit(function(e){
            e.preventDefault();
            var reg_no = $('#reg_no_input').val();
            if(reg_no == '') return;

            $.ajax({
                url: '../classes/Master.php?f=mark_attendance_by_reg_no',
                method: 'POST',
                data: {reg_no: reg_no},
                dataType: 'json',
                success: function(resp){
                    if(resp.status == 'success'){ handleSuccess(resp.child_name, resp.photo); }
                    else { handleError(resp.msg); $('#reg_no_input').val(''); }
                },
                error: function(xhr, status, error) { handleError("Server Error: " + error); }
            });
        });

        function bufferDecode(value) { return Uint8Array.from(atob(value), c => c.charCodeAt(0)); }
        function bufferEncode(value) { return btoa(String.fromCharCode.apply(null, new Uint8Array(value))).replace(/\+/g, "-").replace(/\//g, "_").replace(/=/g, ""); }

        $('#btn_webauthn').click(async function(){
            $('#status_text').text('Touch Sensor Now...').css('color', '#007bff');
            
            try {
                let challengeResp = await fetch('../classes/Master.php?f=get_webauthn_challenge');
                let challengeData = await challengeResp.json();

                if(challengeData.status !== 'success'){
                   handleError("Failed to init scanner.");
                   return;
                }

                const publicKey = {
                    challenge: bufferDecode(challengeData.challenge),
                    timeout: 60000,
                    userVerification: "required" 
                };

                const credential = await navigator.credentials.get({ publicKey });
                
                const authData = {
                    id: credential.id,
                    rawId: bufferEncode(credential.rawId),
                    type: credential.type,
                    response: {
                        authenticatorData: bufferEncode(credential.response.authenticatorData),
                        clientDataJSON: bufferEncode(credential.response.clientDataJSON),
                        signature: bufferEncode(credential.response.signature),
                        userHandle: credential.response.userHandle ? bufferEncode(credential.response.userHandle) : null
                    }
                };

                $.ajax({
                    url: '../classes/Master.php?f=verify_webauthn_attendance',
                    method: 'POST',
                    data: JSON.stringify(authData),
                    contentType: 'application/json',
                    success: function(rawResp){
                        try {
                            let resp = (typeof rawResp === 'object') ? rawResp : JSON.parse(rawResp);
                            if(resp.status == 'success'){ handleSuccess(resp.child_name, resp.photo); }
                            else { handleError(resp.msg || "Unknown error"); }
                        } catch (e) {
                            handleError("Invalid Server Response");
                            console.log(rawResp);
                        }
                    },
                    error: function(xhr, status, error) { handleError("Connection Failed"); }
                });

            } catch (err) { 
                if(err.name === 'NotAllowedError') {
                    handleError("Scan cancelled or timed out.");
                } else if(err.name === 'NotFoundError') {
                    handleError("Fingerprint not recognized.");
                } else {
                    handleError(err.name + ": " + err.message);
                }
            }
        });
    });
</script>
</body>
</html>