
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
        Payments        <small><?= cclang('new', ['Payments']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/payments'); ?>">Payments</a></li>
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
                        <h3 class="widget-user-username" style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">Payments</h3>
                        <div class="widget-user-desc" style="margin-top: 5px; color: #6b778c; font-size: 13px;">
                           <?= cclang('new', ['Payments']); ?>
                        </div>
                     </div>
                  </div>
                        <?= form_open('', [
                            'name'    => 'form_payments', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_payments', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="invoice_id" class="col-sm-2 control-label">Invoice Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="invoice_id" id="invoice_id" data-placeholder="Select Invoice Id" >
                                    <option value=""></option>
                                    <?php foreach ($this->db->where('status !=', 'PAID')->get('invoices')->result() as $row): ?>
                                    <option value="<?= $row->id ?>"><?= $row->invoice_no; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="payment_date" class="col-sm-2 control-label">Payment Date 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="payment_date"  id="payment_date">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="amount" class="col-sm-2 control-label">Amount 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount" value="<?= set_value('amount'); ?>">
                                <small class="info help-block">
                                <b>Input Amount</b> Max Length : 15.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="payment_method" class="col-sm-2 control-label">Payment Method 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="payment_method" id="payment_method" placeholder="Payment Method" value="<?= set_value('payment_method'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="payment_proof_file" class="col-sm-2 control-label">Payment Proof File 
                            </label>
                            <div class="col-sm-8">
                                <div id="payments_payment_proof_file_galery"></div>
                                <div id="payments_payment_proof_file_galery_listed"></div>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="notes" class="col-sm-2 control-label">Notes 
                            </label>
                            <div class="col-sm-8">
                                <textarea id="notes" name="notes" rows="5" class="textarea form-control"><?= set_value('notes'); ?></textarea>
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
              window.location.href = BASE_URL + 'administrator/payments';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_payments = $('#form_payments');
        var data_post = form_payments.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/payments/add_save',
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
            $('#payments_payment_proof_file_galery').find('li').each(function() {
               $('#payments_payment_proof_file_galery').fineUploader('deleteFile', $(this).attr('qq-file-id'));
            });
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
                
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

       $('#payments_payment_proof_file_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/payments/upload_payment_proof_file_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/payments/delete_payment_proof_file_file',
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
                   var uuid = $('#payments_payment_proof_file_galery').fineUploader('getUuid', id);
                   $('#payments_payment_proof_file_galery_listed').append('<input type="hidden" class="listed_file_uuid" name="payments_payment_proof_file_uuid['+id+']" value="'+uuid+'" /><input type="hidden" class="listed_file_name" name="payments_payment_proof_file_name['+id+']" value="'+xhr.uploadName+'" />');
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#payments_payment_proof_file_galery_listed').find('.listed_file_uuid[name="payments_payment_proof_file_uuid['+id+']"]').remove();
                  $('#payments_payment_proof_file_galery_listed').find('.listed_file_name[name="payments_payment_proof_file_name['+id+']"]').remove();
                }
              }
          }
      }); /*end payment_proof_file galery*/
              
      $('#invoice_id').change(function() {
          var invoice_id = $(this).val();
          if (invoice_id) {
              $.ajax({
                  url: BASE_URL + '/administrator/payments/get_invoice_balance/' + invoice_id,
                  type: 'GET',
                  dataType: 'json',
                  success: function(res) {
                      if (res.success) {
                          $('#amount').val(res.balance);
                      }
                  }
              });
          } else {
              $('#amount').val('');
          }
      });
    
    }); /*end doc ready*/
</script>