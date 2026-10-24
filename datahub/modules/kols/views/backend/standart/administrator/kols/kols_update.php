
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
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Kols        <small>Edit Kols</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/kols'); ?>">Kols</a></li>
        <li class="active">Edit</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row" >
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body ">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header ">
                            
                            <h3 class="widget-user-username">Kols</h3>
                            <h5 class="widget-user-desc">Edit Kols</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/kols/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_kols', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_kols', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="name" class="col-sm-2 control-label">Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?= set_value('name', $kols->name); ?>">
                                <small class="info help-block">
                                <b>Input Name</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="phone" class="col-sm-2 control-label">Phone 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="phone" id="phone" placeholder="Phone" value="<?= set_value('phone', $kols->phone); ?>">
                                <small class="info help-block">
                                <b>Input Phone</b> Max Length : 30.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="email" class="col-sm-2 control-label">Email 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?= set_value('email', $kols->email); ?>">
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
                                    <option <?= $kols->gender == "MALE" ? 'selected' :''; ?> value="MALE">MALE</option>
                                    <option <?= $kols->gender == "FEMALE" ? 'selected' :''; ?> value="FEMALE">FEMALE</option>
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
                                <textarea id="address" name="address" rows="10" cols="80"> <?= set_value('address', $kols->address); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="country" class="col-sm-2 control-label">Country 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="country" id="country" placeholder="Country" value="<?= set_value('country', $kols->country); ?>">
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
                                <input class="data_file data_file_uuid" name="kols_rate_card_file_uuid" id="kols_rate_card_file_uuid" type="hidden" value="<?= set_value('kols_rate_card_file_uuid'); ?>">
                                <input class="data_file" name="kols_rate_card_file_name" id="kols_rate_card_file_name" type="hidden" value="<?= set_value('kols_rate_card_file_name', $kols->rate_card_file); ?>">
                                <small class="info help-block">
                                <b>Input Rate Card File</b> Max Length : 500.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="avatar_url" class="col-sm-2 control-label">Avatar URL 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="avatar_url" id="avatar_url" placeholder="Avatar URL" value="<?= set_value('avatar_url', $kols->avatar_url); ?>">
                                <small class="info help-block">
                                <b>Input Avatar Url</b> Max Length : 2000.</small>
                            </div>
                        </div>
                                                 
                        <div class="form-group ">
                            <label for="bank_name" class="col-sm-2 control-label">Bank Name </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Bank Name" value="<?= set_value('bank_name', $kols->bank_name); ?>">
                                <small class="info help-block">Max Length : 100.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="bank_account_number" class="col-sm-2 control-label">Bank Account Number </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bank_account_number" id="bank_account_number" placeholder="Bank Account Number" value="<?= set_value('bank_account_number', $kols->bank_account_number); ?>">
                                <small class="info help-block">Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="bank_account_name" class="col-sm-2 control-label">Bank Account Name </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bank_account_name" id="bank_account_name" placeholder="Bank Account Name" value="<?= set_value('bank_account_name', $kols->bank_account_name); ?>">
                                <small class="info help-block">Max Length : 150.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="npwp_number" class="col-sm-2 control-label">NPWP Number </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="npwp_number" id="npwp_number" placeholder="NPWP Number" value="<?= set_value('npwp_number', $kols->npwp_number); ?>">
                                <small class="info help-block">Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="npwp_file" class="col-sm-2 control-label">NPWP File </label>
                            <div class="col-sm-8">
                                <div id="kols_npwp_file_galery"></div>
                                <input class="data_file" name="kols_npwp_file_uuid" id="kols_npwp_file_uuid" type="hidden" value="<?= set_value('kols_npwp_file_uuid'); ?>">
                                <input class="data_file" name="kols_npwp_file_name" id="kols_npwp_file_name" type="hidden" value="<?= set_value('kols_npwp_file_name', $kols->npwp_file); ?>">
                                <small class="info help-block">Max Length : 500.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="ktp_number" class="col-sm-2 control-label">KTP Number </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="ktp_number" id="ktp_number" placeholder="KTP Number" value="<?= set_value('ktp_number', $kols->ktp_number); ?>">
                                <small class="info help-block">Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="ktp_file" class="col-sm-2 control-label">KTP File </label>
                            <div class="col-sm-8">
                                <div id="kols_ktp_file_galery"></div>
                                <input class="data_file" name="kols_ktp_file_uuid" id="kols_ktp_file_uuid" type="hidden" value="<?= set_value('kols_ktp_file_uuid'); ?>">
                                <input class="data_file" name="kols_ktp_file_name" id="kols_ktp_file_name" type="hidden" value="<?= set_value('kols_ktp_file_name', $kols->ktp_file); ?>">
                                <small class="info help-block">Max Length : 500.</small>
                            </div>
                        </div>




                        <?php $selected_cats = array_column($kol_categories, 'category_id'); ?>
                        <hr>
                        <div class="form-group ">
                            <label class="col-sm-2 control-label">Categories
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="categories[]" multiple data-placeholder="Select Categories">
                                    <?php foreach ($this->db->where('is_active', 1)->get('categories')->result() as $row): ?>
                                    <option value="<?= $row->id ?>" <?= in_array($row->id, $selected_cats) ? 'selected' : '' ?>><?= $row->name; ?></option>
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
                                        <?php foreach ($kol_platforms as $kp): ?>
                                        <tr>
                                            <td>
                                                <select class="form-control" name="platform_id[]">
                                                    <option value="">- Platform -</option>
                                                    <?php foreach ($this->db->where('is_active', 1)->get('platforms')->result() as $row): ?>
                                                    <option value="<?= $row->id ?>" <?= $row->id == $kp->platform_id ? 'selected' : '' ?>><?= $row->name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control" name="platform_username[]" placeholder="Username / URL" value="<?= htmlspecialchars($kp->username) ?>"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger remove_platform_btn"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="message"></div>
                        <div class="row-fluid col-md-7">
                            <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="<?= cclang('save_button'); ?> (Ctrl+s)">
                            <i class="fa fa-save" ></i> <?= cclang('save_button'); ?>
                            </button>
                            <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype='back' title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
                            <i class="ion ion-ios-list-outline" ></i> <?= cclang('save_and_go_the_list_button'); ?>
                            </a>
                            <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
                            <i class="fa fa-undo" ></i> <?= cclang('cancel_button'); ?>
                            </a>
                            <span class="loading loading-hide">
                            <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> 
                            <i><?= cclang('loading_saving_data'); ?></i>
                            </span>
                        </div>
                        <?= form_close(); ?>
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
                   
      $('#btn_cancel').click(function(){
        swal({
            title: "Are you sure?",
            text: "the data that you have created will be in the exhaust!",
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
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#address').val(address.getData());
                    
        var form_kols = $('#form_kols');
        var data_post = form_kols.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_kols.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#kols_image_galery').find('li').attr('qq-file-id');
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            $('.data_file_uuid').val('');
    
          } else {
            $('.message').printMessage({message : res.message, type : 'warning'});
          }
    
        })
        .fail(function() {
          $('.message').printMessage({message : 'Error save data', type : 'warning'});
        })
        .always(function() {
          $('.loading').hide();
          $('html, body').animate({ scrollTop: $(document).height() }, 2000);
        });
    
        return false;
      }); /*end btn save*/
      
                     var params = {};
       params[csrf] = token;

       $('#kols_rate_card_file_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/kols/upload_rate_card_file_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/kols/delete_rate_card_file_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/kols/get_rate_card_file_file/<?= $kols->id; ?>',
             refreshOnRequest:true
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
      }); /*end rate_card_file galey*/
              
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
          session : {
             endpoint: BASE_URL + 'administrator/kols/get_npwp_file_file/<?= $kols->id; ?>',
             refreshOnRequest:true
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
          session : {
             endpoint: BASE_URL + 'administrator/kols/get_ktp_file_file/<?= $kols->id; ?>',
             refreshOnRequest:true
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