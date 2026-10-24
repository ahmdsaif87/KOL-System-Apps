<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>

<style>
.select2-container .select2-selection--single {
    height: 34px !important;
    border: 1px solid #d2d6de !important;
    border-radius: 0 !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 34px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px !important;
}
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
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    color: #000 !important;
}
</style>

<section class="content-header">
    <h1>
        KOL Registration
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">KOL Registration</li>
    </ol>
</section>

<section class="content">
    <div class="row" >
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body ">
                    <!-- WIZARD PROGRESS BAR -->
                    <div class="step-wizard" id="step_wizard_bar">
                        <div class="step-item active" id="step_indicator_1">
                            <div class="step-circle" id="circle_1">1</div>
                            <div class="step-label">KOL Profile</div>
                        </div>
                        <div class="step-item" id="step_indicator_2">
                            <div class="step-circle" id="circle_2">2</div>
                            <div class="step-label">Account Setup</div>
                        </div>
                    </div>
                    <hr>

                    <!-- PHASE 1: KOL PROFILE -->
                    <div id="phase_1_container">
                        <div class="callout callout-success" style="margin-bottom: 25px;">
                            <h4 style="font-weight: 600; margin-bottom: 10px;">Phase 1: Basic Profile</h4>
                            <p>Fill in KOL personal and professional details.</p>
                        </div>
                        
                        <?= form_open('', [
                            'name'    => 'form_kols', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_kols', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                        ]); ?>
                        
                        <div class="form-group ">
                            <label for="name" class="col-sm-2 control-label">Name <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="">
                                <small class="info help-block"><b>Input Name</b> Max Length : 150.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="phone" class="col-sm-2 control-label">Phone <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" value="">
                                <small class="info help-block"><b>Input Phone</b> Max Length : 30.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="email" class="col-sm-2 control-label">Email <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="">
                                <small class="info help-block"><b>Input Email</b> Max Length : 200.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="gender" class="col-sm-2 control-label">Gender <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control select2" name="gender" id="gender" data-placeholder="Select Gender">
                                    <option value=""></option>
                                    <option value="MALE">Male</option>
                                    <option value="FEMALE">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="address" class="col-sm-2 control-label">Address <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <textarea id="address" name="address" rows="5" cols="80"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <label for="country" class="col-sm-2 control-label">Country <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="country" id="country" placeholder="Country" value="Indonesia">
                                <small class="info help-block"><b>Input Country</b> Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="rate_card_file" class="col-sm-2 control-label">Rate Card File <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <div id="kols_rate_card_file_galery"></div>
                                <input class="data_file" name="kols_rate_card_file_uuid" id="kols_rate_card_file_uuid" type="hidden" value="">
                                <input class="data_file" name="kols_rate_card_file_name" id="kols_rate_card_file_name" type="hidden" value="">
                                <small class="info help-block"><b>Input Rate Card File</b> Max Length : 500.</small>
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <label for="avatar_url" class="col-sm-2 control-label">Avatar URL <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="avatar_url" id="avatar_url" placeholder="Avatar URL" value="">
                                <small class="info help-block"><b>Input Avatar Url</b> Max Length : 2000.</small>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group ">
                            <label class="col-sm-2 control-label">Categories <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control select2" name="categories[]" multiple data-placeholder="Select Categories">
                                    <?php foreach ($categories as $row): ?>
                                    <option value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group ">
                            <label class="col-sm-2 control-label">Platforms (Links) <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <div class="table-responsive"><table class="table table-bordered" id="platforms_table">
                                    <thead>
                                        <tr>
                                            <th>Platform</th>
                                            <th>Username / Link</th>
                                            <th width="80"><button type="button" class="btn btn-sm btn-success" id="add_platform_btn"><i class="fa fa-plus"></i> Add</button></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table></div>
                            </div>
                        </div>

                        <div class="message"></div>
                        <div class="row-fluid col-md-10" style="padding-left: 0; margin-top: 15px;">
                            <button type="button" class="btn btn-flat btn-primary btn_next_phase" id="btn_next_phase" style="padding: 9px 22px; font-size: 14px; font-weight: 700; border-radius: 4px;">
                                Next: Setup User Account <i class="fa fa-arrow-right" style="margin-left: 6px;"></i>
                            </button>
                        </div>
                        <?= form_close(); ?>
                    </div> <!-- /#phase_1_container -->

                    <!-- PHASE 2: USER ACCOUNT SETUP -->
                    <div id="phase_2_container" style="display: none;">
                        <div class="callout callout-info" style="margin-bottom: 25px;">
                            <h4 style="font-weight: 600; margin-bottom: 10px;">Phase 2: Login Credentials</h4>
                            <p>Create a portal login account for <b id="p2_kol_name_disp"></b>.</p>
                        </div>
                        
                        <div class="box-body" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 20px;">
                            <div class="phase2_message"></div>
                            
                            <form class="form-horizontal" id="form_phase2_user">
                                <input type="hidden" name="kol_id" id="phase2_kol_id">

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" style="font-weight: 600;">Linked KOL Profile</label>
                                    <div class="col-sm-8" style="padding-top: 7px;">
                                        <span class="label label-primary" style="font-size: 13px; padding: 6px 12px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa fa-star"></i> <span id="p2_badge_name"></span> (ID #<span id="p2_badge_id"></span>)
                                        </span>
                                    </div>
                                </div>
                                <hr style="border-top: 1px dashed #cbd5e1; margin-top: 10px; margin-bottom: 20px;">

                                <div class="form-group">
                                    <label for="phase2_full_name" class="col-sm-2 control-label">Full Name <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="full_name" id="phase2_full_name" placeholder="Full Name" readonly>
                                        <small class="info help-block">Automatically inherited from Phase 1.</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="phase2_username" class="col-sm-2 control-label">Username <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="username" id="phase2_username" placeholder="Username">
                                        <small class="info help-block">A unique username for the portal.</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="phase2_email" class="col-sm-2 control-label">Login Email <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" name="user_email" id="phase2_email" placeholder="Login Email">
                                        <small class="info help-block">The email address used to log into the application.</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="phase2_password" class="col-sm-2 control-label">Password <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" name="password" id="phase2_password" placeholder="Password (minimum 5 characters)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="phase2_passconf" class="col-sm-2 control-label">Confirm Password <i class="required text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" name="passconf" id="phase2_passconf" placeholder="Repeat Password">
                                    </div>
                                </div>
                                
                                <div class="form-group" style="margin-top: 25px;">
                                    <div class="col-sm-offset-2 col-sm-8">
                                        <button type="button" class="btn btn-flat btn-default" id="btn_back_phase1" style="padding: 10px 24px; font-size: 15px; font-weight: 700; border-radius: 4px; margin-right: 10px;">
                                            <i class="fa fa-arrow-left" style="margin-right: 6px;"></i> Back to Phase 1
                                        </button>
                                        <button type="button" class="btn btn-flat btn-success" id="btn_save_user" style="padding: 10px 24px; font-size: 15px; font-weight: 700; border-radius: 4px;">
                                            <i class="fa fa-user-plus" style="margin-right: 6px;"></i> Complete Registration
                                        </button>
                                        <span class="loading_user" style="display:none; margin-left:15px; color:#00a65a; font-weight:600;"><i class="fa fa-spinner fa-spin"></i> Finalizing account...</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function(){
      CKEDITOR.replace('address'); 
      var address = CKEDITOR.instances.address;

      var platform_options = `<?php foreach ($platforms as $row): ?><option value="<?= $row->id ?>"><?= $row->name; ?></option><?php endforeach; ?>`;
      if($.fn.select2) { $('.select2').select2({ width: '100%' }); }
      $('#add_platform_btn').click(function() {
          var tr = `<tr>
              <td><select class="form-control select2" name="platform_id[]"><option value="">- Platform -</option>${platform_options}</select></td>
              <td><input type="text" class="form-control" name="platform_username[]" placeholder="Username / URL"></td>
              <td><button type="button" class="btn btn-sm btn-danger remove_platform_btn"><i class="fa fa-trash"></i></button></td>
          </tr>`;
          var $tr = $(tr);
          $('#platforms_table tbody').append($tr);
          if($.fn.select2) {
              $tr.find('.select2').select2({ width: '100%' });
          }
      });
      $(document).on('click', '.remove_platform_btn', function() {
          $(this).closest('tr').remove();
      });
                   
      function submitPhase1() {
        var rows = $('#platforms_table tbody tr');
        if (rows.length === 0) {
            $('.message').printMessage({message : 'Harap tambahkan minimal 1 platform media sosial.', type : 'warning'});
            $('html, body').animate({ scrollTop: $('#platforms_table').offset().top - 100 }, 500);
            return false;
        }

        var hasEmptyRow = false;
        var validCount = 0;
        rows.each(function() {
            var pid = $(this).find('select[name="platform_id[]"]').val();
            var uname = $.trim($(this).find('input[name="platform_username[]"]').val());
            if (!pid || uname === '') {
                hasEmptyRow = true;
            } else {
                validCount++;
            }
        });

        if (hasEmptyRow || validCount === 0) {
            $('.message').printMessage({message : 'Harap pilih jenis platform dan isi Username / Link untuk setiap baris platform yang ditambahkan (minimal 1 platform terisi lengkap).', type : 'warning'});
            $('html, body').animate({ scrollTop: $('#platforms_table').offset().top - 100 }, 500);
            return false;
        }

        $('.message').fadeOut();
        $('#address').val(address.getData());
                    
        var form_kols = $('#form_kols');
        var data_post = form_kols.serializeArray();
    
        $('.loading').show();
        $('#btn_next_phase').prop('disabled', true);
    
        $.ajax({
          url: BASE_URL + 'web/register/validate_phase1',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            $('#phase2_kol_id').val(res.kol_id);
            $('#phase2_full_name').val(res.kol_name);
            $('#phase2_email').val(res.kol_email);
            $('#phase2_username').val(res.suggested_username);
            $('#p2_kol_name_disp').text(res.kol_name);
            $('#p2_kol_id_disp').text(res.kol_id);
            $('#p2_badge_name').text(res.kol_name);
            $('#p2_badge_id').text(res.kol_id);

            $('#step_wizard_bar').addClass('step-2-active');
            $('#step_indicator_1').removeClass('active').addClass('completed');
            $('#circle_1').html('<i class="fa fa-check"></i>');
            $('#step_indicator_2').addClass('active');

            $('#phase_1_container').slideUp(350, function() {
              $('#phase_2_container').slideDown(350);
              $('html, body').animate({ scrollTop: $('#step_wizard_bar').offset().top - 20 }, 500);
            });
          } else {
            $('.message').printMessage({message : res.message, type : 'warning'});
            $('html, body').animate({ scrollTop: $('.message').offset().top - 40 }, 1000);
            $('#btn_next_phase').prop('disabled', false);
          }
        })
        .fail(function() {
          $('.message').printMessage({message : 'Error saving KOL data. Please check your inputs.', type : 'warning'});
          $('html, body').animate({ scrollTop: $('.message').offset().top - 40 }, 1000);
          $('#btn_next_phase').prop('disabled', false);
        })
        .always(function() {
          $('.loading').hide();
        });
      }

      $('.btn_next_phase').click(function() {
        submitPhase1();
      });

      $('#btn_back_phase1').click(function() {
          $('#btn_next_phase').prop('disabled', false);
          $('#step_wizard_bar').removeClass('step-2-active');
          $('#step_indicator_2').removeClass('active');
          $('#step_indicator_1').removeClass('completed').addClass('active');
          $('#circle_1').html('1');

          $('#phase_2_container').slideUp(350, function() {
              $('#phase_1_container').slideDown(350);
              $('html, body').animate({ scrollTop: $('#step_wizard_bar').offset().top - 20 }, 500);
          });
      });

      $('#btn_save_user').click(function() {
        $('.phase2_message').fadeOut();
        var form_user = $('#form_phase2_user');
        var data_post = form_user.serializeArray();
        $('#address').val(address.getData());
        var data_post = $('#form_kols').serializeArray().concat($('#form_phase2_user').serializeArray());
        
        var csrf_name = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrf_hash = '<?= $this->security->get_csrf_hash(); ?>';
        data_post.push({name: csrf_name, value: csrf_hash});

        $('.loading_user').show();
        $('#btn_save_user').prop('disabled', true);

        $.ajax({
          url: BASE_URL + 'web/register/add_save_user',
          url: BASE_URL + 'web/register/submit_registration',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if (res.success) {
            toastr['success'](res.message);
            setTimeout(function() {
              window.location.href = res.redirect;
            }, 700);
          } else {
            $('.phase2_message').printMessage({message : res.message, type : 'warning'});
            $('html, body').animate({ scrollTop: $('.phase2_message').offset().top - 50 }, 500);
            $('#btn_save_user').prop('disabled', false);
          }
        })
        .fail(function() {
          $('.phase2_message').printMessage({message : 'Error creating user account.', type : 'warning'});
          $('#btn_save_user').prop('disabled', false);
        })
        .always(function() {
          $('.loading_user').hide();
        });
      });
      
      var params = {};
      var csrf = '<?= $this->security->get_csrf_token_name(); ?>';
      var token = '<?= $this->security->get_csrf_hash(); ?>';
      params[csrf] = token;

      $('#kols_rate_card_file_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + 'web/register/upload_rate_card_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + 'web/register/delete_rate_card_file',
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
                  $.get(BASE_URL + 'web/register/delete_rate_card_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#kols_rate_card_file_uuid').val('');
                  $('#kols_rate_card_file_name').val('');
                }
              }
          }
      });
    }); 
</script>
