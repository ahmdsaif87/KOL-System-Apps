
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
        Postings        <small>Edit Postings</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/postings'); ?>">Postings</a></li>
        <li class="active">Edit</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row" >
        <div class="col-md-12">
                    <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
                            <div class="box-body" style="padding: 20px;">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                                       <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px;">
                  <div>
                     <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">Postings</h3>
                     <div style="margin-top: 5px; color: #6b778c; font-size: 13px;">Edit Postings</div>
                  </div>
                  <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                  </div>
               </div>

                        <?= form_open(base_url('administrator/postings/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_postings', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_postings', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="task_id" class="col-sm-2 control-label">Task 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="task_id" id="task_id" data-placeholder="Select Task Id" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('tasks') as $row): ?>
                                    <option <?=  $row->id ==  $postings->task_id ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->title; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="platform_id" class="col-sm-2 control-label">Platforms 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="platform_id" id="platform_id" data-placeholder="Select Platform Id" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('platforms') as $row): ?>
                                    <option <?=  $row->id ==  $postings->platform_id ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="live_url" class="col-sm-2 control-label">Live URL 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="live_url" id="live_url" placeholder="Live URL" value="<?= set_value('live_url', $postings->live_url); ?>">
                                <small class="info help-block">
                                <b>Input Live Url</b> Max Length : 1000.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="actual_posted_at" class="col-sm-2 control-label">Actual Posted Date 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="actual_posted_at"  placeholder="Actual Posted Date" id="actual_posted_at" value="<?= set_value('actual_posted_at', $postings->actual_posted_at); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="posted_at_source" class="col-sm-2 control-label">Posted At Source 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="posted_at_source" id="posted_at_source" data-placeholder="Select Posted At Source" >
                                    <option value=""></option>
                                    <option <?= $postings->posted_at_source == "UNKNOWN" ? 'selected' :''; ?> value="UNKNOWN">UNKNOWN</option>
                                    <option <?= $postings->posted_at_source == "MANUAL" ? 'selected' :''; ?> value="MANUAL">MANUAL</option>
                                    <option <?= $postings->posted_at_source == "APIFY" ? 'selected' :''; ?> value="APIFY">APIFY</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="platform_validation_status" class="col-sm-2 control-label">Platform Validation Satus 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="platform_validation_status" id="platform_validation_status" data-placeholder="Select Platform Validation Status" >
                                    <option value=""></option>
                                    <option <?= $postings->platform_validation_status == "INVALID" ? 'selected' :''; ?> value="INVALID">INVALID</option>
                                    <option <?= $postings->platform_validation_status == "VALID" ? 'selected' :''; ?> value="VALID">VALID</option>
                                    <option <?= $postings->platform_validation_status == "PENDING" ? 'selected' :''; ?> value="PENDING">PENDING</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="date_validation_status" class="col-sm-2 control-label">Status Validation Posting 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="date_validation_status" id="date_validation_status" data-placeholder="Select Date Validation Status" >
                                    <option value=""></option>
                                    <option <?= $postings->date_validation_status == "PENDING" ? 'selected' :''; ?> value="PENDING">PENDING</option>
                                    <option <?= $postings->date_validation_status == "ON_TIME" ? 'selected' :''; ?> value="ON_TIME">ON TIME</option>
                                    <option <?= $postings->date_validation_status == "LATE" ? 'selected' :''; ?> value="LATE">LATE</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="caption" class="col-sm-2 control-label">Caption 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="caption" id="caption" placeholder="Caption" value="<?= set_value('caption', $postings->caption); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="thumbnail_url" class="col-sm-2 control-label">Thumbnail URL 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="thumbnail_url" id="thumbnail_url" placeholder="Thumbnail URL" value="<?= set_value('thumbnail_url', $postings->thumbnail_url); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="duration_seconds" class="col-sm-2 control-label">Duration Seconds 
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="duration_seconds" id="duration_seconds" placeholder="Duration Seconds" value="<?= set_value('duration_seconds', $postings->duration_seconds); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="validation_note" class="col-sm-2 control-label">Validation Note 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="validation_note" name="validation_note" rows="10" cols="80"> <?= set_value('validation_note', $postings->validation_note); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="url_locked_at" class="col-sm-2 control-label">Url Locked At 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="url_locked_at"  placeholder="Url Locked At" id="url_locked_at" value="<?= set_value('url_locked_at', $postings->url_locked_at); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="status" class="col-sm-2 control-label">Status Post 
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="status" id="status" data-placeholder="Select Status" >
                                    <option value=""></option>
                                    <option <?= $postings->status == "ACTIVE" ? 'selected' :''; ?> value="ACTIVE">ACTIVE</option>
                                    <option <?= $postings->status == "TAKEN_DOWN" ? 'selected' :''; ?> value="TAKEN_DOWN">TAKEN DOWN</option>
                                    <option <?= $postings->status == "DELETED" ? 'selected' :''; ?> value="DELETED">DELETED</option>
                                    </select>
                                <small class="info help-block">
                                </small>
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
      
      CKEDITOR.replace('validation_note'); 
      var validation_note = CKEDITOR.instances.validation_note;
                   
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
              window.location.href = BASE_URL + 'administrator/postings';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#validation_note').val(validation_note.getData());
                    
        var form_postings = $('#form_postings');
        var data_post = form_postings.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_postings.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#postings_image_galery').find('li').attr('qq-file-id');
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
      
       
       
           
    
    }); /*end doc ready*/
</script>