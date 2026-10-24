
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
        Campaign Kol Contracts        <small><?= cclang('new', ['Campaign Kol Contracts']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/campaign_kol_contracts'); ?>">Campaign Kol Contracts</a></li>
        <li class="active"><?= cclang('new'); ?></li>
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
                           <?= cclang('new', ['Campaign Kol Contracts']); ?>
                        </div>
                     </div>
                  </div>
                        <?= form_open('', [
                            'name'    => 'form_campaign_kol_contracts', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_campaign_kol_contracts', 
                            'enctype' => 'multipart/form-data', 
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
                                    <option value="<?= $row->id ?>"><?= $row->campaign_name; ?> - <?= $row->kol_name; ?></option>
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
                                <input type="text" class="form-control" name="contract_no" id="contract_no" placeholder="[AUTO GENERATED]" value="[AUTO GENERATED]" readonly>
                                <small class="info help-block">
                                <b>Contract No</b> will be auto-generated.</small>
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <label for="contract_template_id" class="col-sm-2 control-label">Contract Template 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="contract_template_id" id="contract_template_id" data-placeholder="Select Contract Template" >
                                    <option value=""></option>
                                    <?php 
                                    foreach ($this->db->get('contract_templates')->result() as $row): 
                                    ?>
                                    <option value="<?= $row->id ?>"><?= $row->title; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="expired_at" class="col-sm-2 control-label">Expired At 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" name="expired_at" id="expired_at" placeholder="Expired At" value="">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="contract_file" class="col-sm-2 control-label">Contract File 
                            </label>
                            <div class="col-sm-8">
                                <div id="campaign_kol_contracts_contract_file_galery"></div>
                                <div id="campaign_kol_contracts_contract_file_galery_listed"></div>
                                <small class="info help-block">
                                <b>Input Contract File</b> Max Length : 500.</small>
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
          url: BASE_URL + '/administrator/campaign_kol_contracts/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            $('#campaign_kol_contracts_contract_file_galery').find('li').each(function() {
               $('#campaign_kol_contracts_contract_file_galery').fineUploader('deleteFile', $(this).attr('qq-file-id'));
            });
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
                
          } else {
            $('.message').printMessage({message : res.message, type : 'warning'});
          }
    
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
          $('.message').printMessage({message : 'Error save data: ' + jqXHR.responseText, type : 'warning'});
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