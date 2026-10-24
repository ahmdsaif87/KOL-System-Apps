
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
        Campaign Kol Contracts        <small>Edit Campaign Kol Contracts</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/campaign_kol_contracts'); ?>">Campaign Kol Contracts</a></li>
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
               <div class="box box-widget widget-user-2" style="box-shadow: none;">
                  <!-- Add the bg color to the header using any of the bg-* classes -->
                  <div class="widget-user-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 0; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px;">
                     <div>
                        <h3 class="widget-user-username" style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">Campaign Kol Contracts</h3>
                        <div class="widget-user-desc" style="margin-top: 5px; color: #6b778c; font-size: 13px;">
                           Edit Campaign Kol Contracts                        </div>
                     </div>
                  </div>
                        <?= form_open(base_url('administrator/campaign_kol_contracts/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_campaign_kol_contracts', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_campaign_kol_contracts', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="campaign_kol_id" class="col-sm-2 control-label">Campaign Kol Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="campaign_kol_id" id="campaign_kol_id" data-placeholder="Select Campaign Kol Id" >
                                    <option value=""></option>
                                    <?php 
                                    $this->db->select('campaign_kols.id, campaigns.name as campaign_name, kols.name as kol_name');
                                    $this->db->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT');
                                    $this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'LEFT');
                                    foreach ($this->db->get('campaign_kols')->result() as $row): 
                                    ?>
                                    <option <?=  $row->id ==  $campaign_kol_contracts->campaign_kol_id ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->campaign_name; ?> - <?= $row->kol_name; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="contract_no" class="col-sm-2 control-label">Contract No 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="contract_no" id="contract_no" placeholder="Contract No" value="<?= set_value('contract_no', $campaign_kol_contracts->contract_no); ?>" readonly>
                                <small class="info help-block">
                                <b>Contract No</b> (Auto-generated).</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="expired_at" class="col-sm-2 control-label">Expired At 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" name="expired_at" id="expired_at" placeholder="Expired At" value="<?= set_value('expired_at', $campaign_kol_contracts->expired_at); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="contract_file" class="col-sm-2 control-label">Contract File 
                            </label>
                            <div class="col-sm-8">
                                <div id="campaign_kol_contracts_contract_file_galery"></div>
                                <div id="campaign_kol_contracts_contract_file_galery_listed">
                                <?php foreach ((array) explode(',', $campaign_kol_contracts->contract_file) as $idx => $filename): ?>
                                    <input type="hidden" class="listed_file_uuid" name="campaign_kol_contracts_contract_file_uuid[<?= $idx ?>]" value="" /><input type="hidden" class="listed_file_name" name="campaign_kol_contracts_contract_file_name[<?= $idx ?>]" value="<?= $filename; ?>" />
                                <?php endforeach; ?>
                                </div>
                                <small class="info help-block">
                                <b>Input Contract File</b> Max Length : 500.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="status" class="col-sm-2 control-label">Status 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="status" id="status" data-placeholder="Select Status" >
                                    <option value=""></option>
                                    <option <?= $campaign_kol_contracts->status == "DRAFT" ? 'selected' :''; ?> value="DRAFT">DRAFT</option>
                                    <option <?= $campaign_kol_contracts->status == "WAITING_KOL_SIGNATURE" ? 'selected' :''; ?> value="WAITING_KOL_SIGNATURE">WAITING SIGNATURE</option>
                                    <option <?= $campaign_kol_contracts->status == "SIGNED" ? 'selected' :''; ?> value="SIGNED">SIGNED</option>
                                    <option <?= $campaign_kol_contracts->status == "VOID" ? 'selected' :''; ?> value="VOID">VOID</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="signed_at" class="col-sm-2 control-label">Signed At 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date col-sm-8">
                                  <input type="text" class="form-control pull-right" name="signed_at"  placeholder="Signed At" id="signed_at" value="<?= set_value('signed_at', $campaign_kol_contracts->signed_at); ?>" readonly>
                                </div>
                                <small class="info help-block">
                                <b>Signed At</b> (Auto-generated when status becomes SIGNED).</small>
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
<!-- Page script -->
<script>
    $(document).ready(function(){
      
             
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
              window.location.href = BASE_URL + 'administrator/campaign_kol_contracts';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_campaign_kol_contracts = $('#form_campaign_kol_contracts');
        var data_post = form_campaign_kol_contracts.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_campaign_kol_contracts.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#campaign_kol_contracts_image_galery').find('li').attr('qq-file-id');
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

       $('#campaign_kol_contracts_contract_file_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/campaign_kol_contracts/upload_contract_file_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/campaign_kol_contracts/delete_contract_file_file',
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/campaign_kol_contracts/get_contract_file_file/<?= $campaign_kol_contracts->id; ?>',
             refreshOnRequest:true
           },
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
                   var uuid = $('#campaign_kol_contracts_contract_file_galery').fineUploader('getUuid', id);
                   $('#campaign_kol_contracts_contract_file_galery_listed').append('<input type="hidden" class="listed_file_uuid" name="campaign_kol_contracts_contract_file_uuid['+id+']" value="'+uuid+'" /><input type="hidden" class="listed_file_name" name="campaign_kol_contracts_contract_file_name['+id+']" value="'+xhr.uploadName+'" />');
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#campaign_kol_contracts_contract_file_galery_listed').find('.listed_file_uuid[name="campaign_kol_contracts_contract_file_uuid['+id+']"]').remove();
                  $('#campaign_kol_contracts_contract_file_galery_listed').find('.listed_file_name[name="campaign_kol_contracts_contract_file_name['+id+']"]').remove();
                }
              }
          }
      }); /*end contract_file galery*/
                  
    
    }); /*end doc ready*/
</script>