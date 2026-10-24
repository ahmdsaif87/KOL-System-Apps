
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
        Kol Shipments        <small>Edit Kol Shipments</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/kol_shipments'); ?>">Kol Shipments</a></li>
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
                     <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">Kol Shipments</h3>
                     <div style="margin-top: 5px; color: #6b778c; font-size: 13px;">Edit Kol Shipments</div>
                  </div>
                  <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                  </div>
               </div>

                        <?= form_open(base_url('administrator/kol_shipments/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_kol_shipments', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_kol_shipments', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="campaign_kol_id" class="col-sm-2 control-label">Campaign Kol Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="campaign_kol_id" id="campaign_kol_id" data-placeholder="Select Campaign Kol Id" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('campaign_kols') as $row): ?>
                                    <option <?=  $row->id ==  $kol_shipments->campaign_kol_id ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->kol_id; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="recipient_name" class="col-sm-2 control-label">Recipient Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="recipient_name" id="recipient_name" placeholder="Recipient Name" value="<?= set_value('recipient_name', $kol_shipments->recipient_name); ?>">
                                <small class="info help-block">
                                <b>Input Recipient Name</b> Max Length : 150.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="recipient_phone" class="col-sm-2 control-label">Recipient Phone
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="recipient_phone" id="recipient_phone" placeholder="08xxxxxxxxxx" value="<?= set_value('recipient_phone', isset($kol_shipments->recipient_phone) ? $kol_shipments->recipient_phone : ''); ?>">
                                <small class="info help-block">No. HP / WhatsApp penerima.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="shipping_address" class="col-sm-2 control-label">Shipping Address 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="shipping_address" name="shipping_address" rows="10" cols="80"> <?= set_value('shipping_address', $kol_shipments->shipping_address); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="courier" class="col-sm-2 control-label">Courier 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="courier" id="courier" placeholder="Courier" value="<?= set_value('courier', $kol_shipments->courier); ?>">
                                <small class="info help-block">
                                <b>Input Courier</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tracking_no" class="col-sm-2 control-label">Tracking No 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tracking_no" id="tracking_no" placeholder="Tracking No" value="<?= set_value('tracking_no', $kol_shipments->tracking_no); ?>">
                                <small class="info help-block">
                                <b>Input Tracking No</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="shipping_fee" class="col-sm-2 control-label">Shipping Fee 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="shipping_fee" id="shipping_fee" placeholder="Shipping Fee" value="<?= set_value('shipping_fee', $kol_shipments->shipping_fee); ?>">
                                <small class="info help-block">
                                <b>Input Shipping Fee</b> Max Length : 15.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="status" class="col-sm-2 control-label">Status 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="status" id="status" data-placeholder="Select Status" >
                                    <option value=""></option>
                                    <option <?= $kol_shipments->status == "PENDING" ? 'selected' :''; ?> value="PENDING">PENDING</option>
                                    <option <?= $kol_shipments->status == "PROCESSING" ? 'selected' :''; ?> value="PROCESSING">PROCESSING</option>
                                    <option <?= $kol_shipments->status == "SHIPPED" ? 'selected' :''; ?> value="SHIPPED">SHIPPED</option>
                                    <option <?= $kol_shipments->status == "DELIVERED" ? 'selected' :''; ?> value="DELIVERED">DELIVERED</option>
                                    <option <?= $kol_shipments->status == "RETURN_PENDING" ? 'selected' :''; ?> value="RETURN_PENDING">RETURN PENDING</option>
                                    <option <?= $kol_shipments->status == "RETURNED" ? 'selected' :''; ?> value="RETURNED">RETURNED</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="shipped_at" class="col-sm-2 control-label">Shipped At 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="shipped_at"  placeholder="Shipped At" id="shipped_at" value="<?= set_value('shipped_at', $kol_shipments->shipped_at); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="delivered_at" class="col-sm-2 control-label">Delivered At 
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="delivered_at"  placeholder="Delivered At" id="delivered_at" value="<?= set_value('delivered_at', $kol_shipments->delivered_at); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="notes" class="col-sm-2 control-label">Notes 
                            </label>
                            <div class="col-sm-8">
                                <textarea id="notes" name="notes" rows="10" cols="80"> <?= set_value('notes', $kol_shipments->notes); ?></textarea>
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
      
      CKEDITOR.replace('shipping_address'); 
      var shipping_address = CKEDITOR.instances.shipping_address;
            CKEDITOR.replace('notes'); 
      var notes = CKEDITOR.instances.notes;
                   
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
              window.location.href = BASE_URL + 'administrator/kol_shipments';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#shipping_address').val(shipping_address.getData());
                $('#notes').val(notes.getData());
                    
        var form_kol_shipments = $('#form_kol_shipments');
        var data_post = form_kol_shipments.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_kol_shipments.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#kol_shipments_image_galery').find('li').attr('qq-file-id');
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