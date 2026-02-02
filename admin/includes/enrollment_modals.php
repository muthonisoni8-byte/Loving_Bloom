<div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document"> <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold">Child Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-4 d-flex flex-column align-items-center text-center border-right pt-3">
                <img id="v_photo_display" src="../dist/img/no-image-available.png" class="img-circle elevation-3 shadow-sm" style="width: 160px; height: 160px; object-fit: cover; border: 4px solid #fff;">
                <h4 id="v_name_header" class="mt-3 font-weight-bold text-dark"></h4>
                <p class="text-muted text-sm">Child Profile</p>
            </div>

            <div class="col-md-8">
                <table class="table table-sm table-borderless table-striped mt-2">
                    <tbody>
                        <tr><th width="35%" class="text-secondary"><i class="fas fa-venus-mars mr-2"></i> Gender</th><td id="v_gender" class="font-weight-bold"></td></tr>
                        <tr><th class="text-secondary"><i class="fas fa-birthday-cake mr-2"></i> Date of Birth</th><td id="v_dob"></td></tr>
                        <tr><th class="text-secondary"><i class="fas fa-hourglass-half mr-2"></i> Age</th><td id="v_age"></td></tr>
                        <tr><th class="text-secondary"><i class="fas fa-phone mr-2"></i> Parent Contact</th><td id="v_phone"></td></tr>
                        <tr><th class="text-secondary"><i class="fas fa-map-marker-alt mr-2"></i> Address</th><td id="v_address"></td></tr>
                        <tr><th class="text-secondary"><i class="fas fa-tint mr-2"></i> Blood Type</th><td id="v_blood"></td></tr>
                        <tr><th class="text-secondary"><i class="fas fa-allergies mr-2"></i> Allergies</th><td id="v_allergy" class="text-danger"></td></tr>
                        <tr><th class="text-secondary"><i class="fas fa-notes-medical mr-2"></i> Medical Cond.</th><td id="v_cond"></td></tr>
                        <tr><th class="text-secondary"><i class="fas fa-hands-helping mr-2"></i> Special Needs</th><td id="v_needs"></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-success status_btn shadow-sm" data-status="1"><i class="fas fa-check"></i> Accept</button>
        <button type="button" class="btn btn-danger status_btn shadow-sm" data-status="2"><i class="fas fa-times"></i> Reject</button>
        <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Child Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form id="update-enrollment-form" enctype="multipart/form-data">
          <input type="hidden" name="id" id="e_id">
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-4 text-center">
                      <img id="e_photo_display" src="../dist/img/no-image-available.png" alt="Child Photo" style="width:150px; height:150px; object-fit:cover; border-radius:50%; margin-bottom:10px;">
                      <div class="custom-file text-left mt-2">
                          <input type="file" class="custom-file-input" id="e_child_photo" name="child_photo" accept="image/*" onchange="displayImg(this,$(this))">
                          <label class="custom-file-label" for="e_child_photo">Choose file</label>
                      </div>
                      
                      <hr>
                      <button type="button" class="btn btn-outline-success btn-block btn-sm" id="btn_register_biometrics">
                          <i class="fas fa-fingerprint"></i> Register Fingerprint
                      </button>
                      <small class="text-muted d-block mt-1" id="bio_status">Not Registered</small>

                  </div>
                  <div class="col-md-8">
                      <div class="row">
                          <div class="col-md-6 form-group">
                              <label>Child Name</label>
                              <input type="text" name="child_name" id="e_name" class="form-control" required>
                          </div>
                          <div class="col-md-6 form-group">
                              <label>Date of Birth</label>
                              <input type="date" name="birth_date" id="e_dob" class="form-control" required>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-6 form-group">
                              <label>Gender</label>
                              <select name="gender" id="e_gender" class="form-control">
                                  <option value="Male">Male</option>
                                  <option value="Female">Female</option>
                              </select>
                          </div>
                          <div class="col-md-6 form-group">
                              <label>Parent Name</label>
                              <input type="text" name="parent_name" id="e_parent" class="form-control" required>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-6 form-group">
                              <label>Phone Number</label>
                              <input type="text" name="phone" id="e_phone" class="form-control" required>
                          </div>
                          <div class="col-md-6 form-group">
                              <label>Address</label>
                              <input type="text" name="address" id="e_address" class="form-control" required>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12 form-group">
                       <label>Medical/Special Needs</label>
                       <textarea name="med_conditions" id="e_cond" class="form-control" placeholder="Conditions"></textarea>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Save Changes</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
      </form>
    </div>
  </div>
</div>