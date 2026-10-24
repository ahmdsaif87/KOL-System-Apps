
<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
    function domo(){
     
       // Binding keys
       $('*').bind('keydown', 'Ctrl+s', function assets() {
          $('#btn_save').trigger('click');
           return false;
       });
    
       $('*').bind('keydown', 'Ctrl+x', function assets() {
          $('#btn_cancel').trigger('click');
           return false;
       });
    
      $('*').bind('keydown', 'Ctrl+d', function assets() {
          $('.btn_save_back').trigger('click');
           return false;
       });
        
    }
    
    jQuery(document).ready(domo);
</script>
<style>
.step-wizard {
    display: flex;
    justify-content: space-between;
    margin: 15px 0 25px 0;
    padding: 0 40px;
    position: relative;
}
.step-wizard::before {
    content: '';
    position: absolute;
    top: 22px;
    left: 20%;
    right: 20%;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
    transition: background 0.4s ease;
}
.step-wizard.step-2-active::before {
    background: #00a65a;
}
.step-item {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}
.step-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    margin-bottom: 8px;
    transition: all 0.3s ease;
    border: 3px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.08);
}
.step-item.active .step-circle {
    background: #3c8dbc;
    color: #fff;
    box-shadow: 0 0 0 4px rgba(60, 141, 188, 0.25);
}
.step-item.completed .step-circle {
    background: #00a65a;
    color: #fff;
    box-shadow: 0 0 0 4px rgba(0, 166, 90, 0.2);
}
.step-label {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    transition: color 0.3s ease;
}
.step-item.active .step-label {
    color: #3c8dbc;
    font-weight: 700;
}
.step-item.completed .step-label {
    color: #00a65a;
    font-weight: 700;
}
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Kols        <small><?= cclang('new', ['Kols']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/kols'); ?>">Kols</a></li>
        <li class="active"><?= cclang('new'); ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row" >
        <div class="col-md-12">
            <div class="box box-warning" style="border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.08);">
                <div class="box-body" style="padding: 25px;">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2" style="margin-bottom: 0;">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header " style="padding-bottom: 15px;">
                            <h3 class="widget-user-username" style="font-weight: 700;">Add New KOL Profile</h3>
                            <h5 class="widget-user-desc" style="color: #64748b;">Two-phase registration: Fill KOL details, then configure portal login credentials.</h5>
                            <hr style="margin-top: 15px; margin-bottom: 15px;">
                        </div>

                        <!-- 2-Phase Wizard Indicator -->
                        <div class="step-wizard" id="step_wizard_bar">
                            <div class="step-item active" id="step_indicator_1">
                                <div class="step-circle" id="circle_1"><i class="fa fa-user"></i></div>
                                <div class="step-label">Phase 1: KOL Information</div>
                            </div>
                            <div class="step-item" id="step_indicator_2">
                                <div class="step-circle" id="circle_2"><i class="fa fa-key"></i></div>
                                <div class="step-label">Phase 2: User Account Setup</div>
                            </div>
                        </div>

                        <div id="phase_1_container">
                        <?= form_open('', [
                            'name'    => 'form_kols', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_kols', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="name" class="col-sm-2 control-label">Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?= set_value('name'); ?>">
                                <small class="info help-block">
                                <b>Input Name</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="phone" class="col-sm-2 control-label">Phone 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="phone" id="phone" placeholder="Phone" value="<?= set_value('phone'); ?>">
                                <small class="info help-block">
                                <b>Input Phone</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email" class="col-sm-2 control-label">Email 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?= set_value('email'); ?>">
                                <small class="info help-block">
                                <b>Input Email</b> Max Length : 200.</small>
                            </div>
                        </div>
                                                 

                                                <div class="form-group ">
                            <label for="gender" class="col-sm-2 control-label">Gender 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="gender" id="gender" data-placeholder="Select Gender" >
                                    <option value=""></option>
                                    <option value="MALE">MALE</option>
                                    <option value="FEMALE">FEMALE</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 


                                                <div class="form-group ">
                            <label for="address" class="col-sm-2 control-label">Address 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="address" name="address" rows="5" cols="80"><?= set_value('Address'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="country" class="col-sm-2 control-label">Country 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="country" id="country" placeholder="Country" value="<?= set_value('country'); ?>">
                                <small class="info help-block">
                                <b>Input Country</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 

                                                <div class="form-group ">
                            <label for="rate_card_file" class="col-sm-2 control-label">Rate Card File 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="kols_rate_card_file_galery"></div>
                                <input class="data_file" name="kols_rate_card_file_uuid" id="kols_rate_card_file_uuid" type="hidden" value="<?= set_value('kols_rate_card_file_uuid'); ?>">
                                <input class="data_file" name="kols_rate_card_file_name" id="kols_rate_card_file_name" type="hidden" value="<?= set_value('kols_rate_card_file_name'); ?>">
                                <small class="info help-block">
                                <b>Input Rate Card File</b> Max Length : 500.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="avatar_url" class="col-sm-2 control-label">Avatar URL 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="avatar_url" id="avatar_url" placeholder="Avatar URL" value="<?= set_value('avatar_url'); ?>">
                                <small class="info help-block">
                                <b>Input Avatar Url</b> Max Length : 2000.</small>
                            </div>
                        </div>
                                                 
                        <div class="form-group ">
                            <label for="bank_name" class="col-sm-2 control-label">Bank Name </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Bank Name" value="<?= set_value('bank_name'); ?>">
                                <small class="info help-block">Max Length : 100.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="bank_account_number" class="col-sm-2 control-label">Bank Account Number </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bank_account_number" id="bank_account_number" placeholder="Bank Account Number" value="<?= set_value('bank_account_number'); ?>">
                                <small class="info help-block">Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="bank_account_name" class="col-sm-2 control-label">Bank Account Name </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bank_account_name" id="bank_account_name" placeholder="Bank Account Name" value="<?= set_value('bank_account_name'); ?>">
                                <small class="info help-block">Max Length : 150.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="npwp_number" class="col-sm-2 control-label">NPWP Number </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npwp_number" id="npwp_number" placeholder="NPWP Number" value="<?= set_value('npwp_number'); ?>">
                                <small class="info help-block">Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="npwp_file" class="col-sm-2 control-label">NPWP File </label>
                            <div class="col-sm-8">
                                <div id="kols_npwp_file_galery"></div>
                                <input class="data_file" name="kols_npwp_file_uuid" id="kols_npwp_file_uuid" type="hidden" value="<?= set_value('kols_npwp_file_uuid'); ?>">
                                <input class="data_file" name="kols_npwp_file_name" id="kols_npwp_file_name" type="hidden" value="<?= set_value('kols_npwp_file_name'); ?>">
                                <small class="info help-block">Max Length : 500.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="ktp_number" class="col-sm-2 control-label">KTP Number </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="ktp_number" id="ktp_number" placeholder="KTP Number" value="<?= set_value('ktp_number'); ?>">
                                <small class="info help-block">Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="ktp_file" class="col-sm-2 control-label">KTP File </label>
                            <div class="col-sm-8">
                                <div id="kols_ktp_file_galery"></div>
                                <input class="data_file" name="kols_ktp_file_uuid" id="kols_ktp_file_uuid" type="hidden" value="<?= set_value('kols_ktp_file_uuid'); ?>">
                                <input class="data_file" name="kols_ktp_file_name" id="kols_ktp_file_name" type="hidden" value="<?= set_value('kols_ktp_file_name'); ?>">
                                <small class="info help-block">Max Length : 500.</small>
                            </div>
                        </div>




                        <hr>
                        <div class="form-group ">
                            <label class="col-sm-2 control-label">Categories
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="categories[]" multiple data-placeholder="Select Categories">
                                    <?php foreach ($this->db->where('is_active', 1)->get('categories')->result() as $row): ?>
                                    <option value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group ">
                            <label class="col-sm-2 control-label">Platforms (Links)</label>
                            <div class="col-sm-8">
                                <table class="table table-bordered" id="platforms_table">
                                    <thead>
                                        <tr>
                                            <th>Platform</th>
                                            <th>Username / Link</th>
                                            <th width="80"><button type="button" class="btn btn-sm btn-success" id="add_platform_btn"><i class="fa fa-plus"></i> Add</button></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="message"></div>
                        <div class="row-fluid col-md-10" style="padding-left: 0; margin-top: 15px;">
                            <button type="button" class="btn btn-flat btn-primary btn_next_phase" id="btn_next_phase" style="padding: 9px 22px; font-size: 14px; font-weight: 700; border-radius: 4px;">
                                Next: Setup User Account <i class="fa fa-arrow-right" style="margin-left: 6px;"></i>
                            </button>
                            <button type="button" class="btn btn-flat btn-default btn_save_kol_only" id="btn_save_kol_only" style="margin-left: 8px; padding: 9px 16px; font-size: 14px; border-radius: 4px;">
                                <i class="fa fa-save"></i> Save KOL Only
                            </button>
                            <a class="btn btn-flat btn-default" id="btn_cancel" href="<?= site_url('administrator/kols'); ?>" style="margin-left: 8px; padding: 9px 16px; font-size: 14px; border-radius: 4px;">
                                <i class="fa fa-undo"></i> Cancel
                            </a>
                            <span class="loading loading-hide" style="margin-left: 12px;">
                                <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> 
                                <i style="margin-left: 5px; color: #64748b; font-size: 13px;">Saving KOL data...</i>
                            </span>
                        </div>
                        <?= form_close(); ?>
                        </div> <!-- /#phase_1_container -->

                        <!-- Phase 2 Container: Setup User Account -->
                        <div id="phase_2_container" style="display: none; padding-top: 10px;">
                            <div class="alert alert-success" style="border-radius: 8px; background-color: #f0fdf4 !important; border: 1px solid #bbf7d0 !important; color: #166534 !important; padding: 16px 20px; margin-bottom: 25px;">
                                <h4 style="margin: 0 0 6px 0; font-size: 17px; font-weight: 700; color: #15803d;">
                                    <i class="fa fa-check-circle text-success"></i> Phase 1 Completed: KOL Profile Created!
                                </h4>
                                <p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #166534;">
                                    Profile for <strong><span id="p2_kol_name_disp"></span></strong> has been successfully saved with ID #<strong><span id="p2_kol_id_disp"></span></strong>.<br>
                                    Now create the user login account so this KOL can access their personal Performance Card.
                                </p>
                            </div>

                            <form class="form-horizontal" id="form_phase2_user">
                                <input type="hidden" name="kol_id" id="phase2_kol_id">

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" style="font-weight: 600;">Linked KOL Profile</label>
                                    <div class="col-sm-8" style="padding-top: 7px;">
                                        <span class="label label-primary" style="font-size: 13px; padding: 6px 12px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa fa-star"></i> <span id="p2_badge_name"></span> (ID #<span id="p2_badge_id"></span>)
                                        </span>
                                        <span class="text-muted" style="margin-left: 10px; font-size: 12.5px;"><i class="fa fa-lock"></i> Locked to this KOL profile</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" style="font-weight: 600;">Assigned Role</label>
                                    <div class="col-sm-8" style="padding-top: 7px;">
                                        <span class="label label-info" style="font-size: 13px; padding: 6px 12px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa fa-shield"></i> Kol
                                        </span>
                                        <span class="text-muted" style="margin-left: 10px; font-size: 12.5px;"><i class="fa fa-info-circle"></i> Can only log in and view/export their own Performance Card</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phase2_full_name" class="col-sm-2 control-label">Full Name <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="full_name" id="phase2_full_name" placeholder="Full Name">
                                        <small class="info help-block">Display name of the user account.</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phase2_username" class="col-sm-2 control-label">Username <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="username" id="phase2_username" placeholder="Username (letters, numbers, underscore)">
                                        <small class="info help-block">Unique username used to identify the user.</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phase2_email" class="col-sm-2 control-label">Login Email <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" name="email" id="phase2_email" placeholder="Login Email">
                                        <small class="info help-block">The email address used to log into the application (can be personal login email).</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phase2_password" class="col-sm-2 control-label">Password <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" name="password" id="phase2_password" placeholder="Password (minimum 5 characters)">
                                        <small class="info help-block">Password for this KOL to log into the portal.</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phase2_passconf" class="col-sm-2 control-label">Confirm Password <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" name="passconf" id="phase2_passconf" placeholder="Repeat Password">
                                        <small class="info help-block">Confirm the password.</small>
                                    </div>
                                </div>

                                <div class="phase2_message" style="margin-top: 15px;"></div>

                                <div class="row-fluid col-md-10" style="padding-left: 0; margin-top: 20px;">
                                    <button type="button" class="btn btn-flat btn-success" id="btn_save_user" style="padding: 10px 24px; font-size: 14px; font-weight: 700; border-radius: 4px;">
                                        <i class="fa fa-check-circle" style="margin-right: 5px;"></i> Create User Account & Finish
                                    </button>
                                    <a href="<?= site_url('administrator/kols'); ?>" class="btn btn-flat btn-default" id="btn_skip_user" style="margin-left: 10px; padding: 10px 18px; font-size: 14px; border-radius: 4px;">
                                        <i class="fa fa-forward" style="margin-right: 5px;"></i> Skip User Creation & Go to List
                                    </a>
                                    <span class="loading_user loading-hide" style="margin-left: 12px; display: none;">
                                        <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> 
                                        <i style="margin-left: 5px; color: #64748b; font-size: 13px;">Creating user account...</i>
                                    </span>
                                </div>
                            </form>
                        </div> <!-- /#phase_2_container -->
                    </div>
                </div>
                <!--/box body -->
            </div>
            <!--/box -->
        </div>
    </div>
</section>
<!-- /.content -->
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<!-- Page script -->
<script>
    $(document).ready(function(){
            CKEDITOR.replace('address'); 
      var address = CKEDITOR.instances.address;

      var platform_options = `<?php foreach ($this->db->where('is_active', 1)->get('platforms')->result() as $row): ?><option value="<?= $row->id ?>"><?= $row->name; ?></option><?php endforeach; ?>`;
      $('#add_platform_btn').click(function() {
          var tr = `<tr>
              <td><select class="form-control" name="platform_id[]"><option value="">- Platform -</option>${platform_options}</select></td>
              <td><input type="text" class="form-control" name="platform_username[]" placeholder="Username / URL"></td>
              <td><button type="button" class="btn btn-sm btn-danger remove_platform_btn"><i class="fa fa-trash"></i></button></td>
          </tr>`;
          $('#platforms_table tbody').append(tr);
      });
      $(document).on('click', '.remove_platform_btn', function() {
          $(this).closest('tr').remove();
      });
                   
      $('#btn_cancel').click(function(e){
        e.preventDefault();
        swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!",
            cancelButtonText: "No!",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm){
            if (isConfirm) {
              window.location.href = BASE_URL + 'administrator/kols';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      function submitPhase1(saveType) {
        $('.message').fadeOut();
        $('#address').val(address.getData());
                    
        var form_kols = $('#form_kols');
        var data_post = form_kols.serializeArray();
        data_post.push({name: 'save_type', value: saveType});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + 'administrator/kols/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            if (saveType === 'back') {
              window.location.href = res.redirect || (BASE_URL + 'administrator/kols');
              return;
            }

            // Move to Phase 2: Populate values
            $('#phase2_kol_id').val(res.kol_id);
            $('#phase2_full_name').val(res.kol_name);
            $('#phase2_email').val(res.kol_email);
            $('#phase2_username').val(res.suggested_username);
            $('#p2_kol_name_disp').text(res.kol_name);
            $('#p2_kol_id_disp').text(res.kol_id);
            $('#p2_badge_name').text(res.kol_name);
            $('#p2_badge_id').text(res.kol_id);

            // Update Wizard Indicator
            $('#step_wizard_bar').addClass('step-2-active');
            $('#step_indicator_1').removeClass('active').addClass('completed');
            $('#circle_1').html('<i class="fa fa-check"></i>');
            $('#step_indicator_2').addClass('active');

            // Switch view from Phase 1 to Phase 2
            $('#phase_1_container').slideUp(350, function() {
              $('#phase_2_container').slideDown(350);
              $('html, body').animate({ scrollTop: $('#step_wizard_bar').offset().top - 20 }, 500);
            });
                
          } else {
            $('.message').printMessage({message : res.message, type : 'warning'});
            $('html, body').animate({ scrollTop: $('.message').offset().top - 40 }, 1000);
          }
        })
        .fail(function() {
          $('.message').printMessage({message : 'Error saving KOL data. Please check your inputs.', type : 'warning'});
          $('html, body').animate({ scrollTop: $('.message').offset().top - 40 }, 1000);
        })
        .always(function() {
          $('.loading').hide();
        });
      }

      $('.btn_next_phase').click(function() {
        submitPhase1('phase1_next');
      });

      $('.btn_save_kol_only').click(function() {
        submitPhase1('back');
      });

      $('#btn_save_user').click(function() {
        $('.phase2_message').fadeOut();
        var form_user = $('#form_phase2_user');
        var data_post = form_user.serializeArray();
        
        // CSRF Token
        var csrf_name = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrf_hash = '<?= $this->security->get_csrf_hash(); ?>';
        data_post.push({name: csrf_name, value: csrf_hash});

        $('.loading_user').show();

        $.ajax({
          url: BASE_URL + 'administrator/kols/add_save_user',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if (res.success) {
            toastr['success'](res.message);
            setTimeout(function() {
              window.location.href = res.redirect || (BASE_URL + 'administrator/kols');
            }, 700);
          } else {
            $('.phase2_message').printMessage({message : res.message, type : 'warning'});
            $('html, body').animate({ scrollTop: $('.phase2_message').offset().top - 50 }, 500);
          }
        })
        .fail(function() {
          $('.phase2_message').printMessage({message : 'Error creating user account.', type : 'warning'});
        })
        .always(function() {
          $('.loading_user').hide();
        });
      });
      
              var params = {};
       params[csrf] = token;

       $('#kols_rate_card_file_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/kols/upload_rate_card_file_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/kols/delete_rate_card_file_file',
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
          multiple : false,
          validation: {
              allowedExtensions: ["*"],
              sizeLimit : 0,
                        },
          showMessage: function(msg) {
              toastr['error'](msg);
          },
          callbacks: {
              onComplete : function(id, name, xhr) {
                if (xhr.success) {
                   var uuid = $('#kols_rate_card_file_galery').fineUploader('getUuid', id);
                   $('#kols_rate_card_file_uuid').val(uuid);
                   $('#kols_rate_card_file_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#kols_rate_card_file_uuid').val();
                  $.get(BASE_URL + '/administrator/kols/delete_rate_card_file_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#kols_rate_card_file_uuid').val('');
                  $('#kols_rate_card_file_name').val('');
                }
              }
          }
      }); /*end rate_card_file galery*/
              
      $('#kols_npwp_file_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/kols/upload_npwp_file_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/kols/delete_npwp_file_file',
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
          multiple : false,
          validation: {
              allowedExtensions: ["*"],
              sizeLimit : 0,
                        },
          showMessage: function(msg) {
              toastr['error'](msg);
          },
          callbacks: {
              onComplete : function(id, name, xhr) {
                if (xhr.success) {
                   var uuid = $('#kols_npwp_file_galery').fineUploader('getUuid', id);
                   $('#kols_npwp_file_uuid').val(uuid);
                   $('#kols_npwp_file_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#kols_npwp_file_uuid').val();
                  $.get(BASE_URL + '/administrator/kols/delete_npwp_file_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#kols_npwp_file_uuid').val('');
                  $('#kols_npwp_file_name').val('');
                }
              }
          }
      });

      $('#kols_ktp_file_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/kols/upload_ktp_file_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/kols/delete_ktp_file_file',
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
          multiple : false,
          validation: {
              allowedExtensions: ["*"],
              sizeLimit : 0,
                        },
          showMessage: function(msg) {
              toastr['error'](msg);
          },
          callbacks: {
              onComplete : function(id, name, xhr) {
                if (xhr.success) {
                   var uuid = $('#kols_ktp_file_galery').fineUploader('getUuid', id);
                   $('#kols_ktp_file_uuid').val(uuid);
                   $('#kols_ktp_file_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#kols_ktp_file_uuid').val();
                  $.get(BASE_URL + '/administrator/kols/delete_ktp_file_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#kols_ktp_file_uuid').val('');
                  $('#kols_ktp_file_name').val('');
                }
              }
          }
      });
 
       
    
    
    }); /*end doc ready*/
</script>