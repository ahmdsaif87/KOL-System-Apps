
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
        Kol Rate Cards        <small><?= cclang('new', ['Kol Rate Cards']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/kol_rate_cards'); ?>">Kol Rate Cards</a></li>
        <li class="active"><?= cclang('new'); ?></li>
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
                            
                            <h3 class="widget-user-username">Kol Rate Cards</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Kol Rate Cards']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_kol_rate_cards', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_kol_rate_cards', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="kol_platform_id" class="col-sm-2 control-label">Platform KOL 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="kol_platform_id" id="kol_platform_id" data-placeholder="Select Kol Platform Id" >
                                    <option value=""></option>
                                    <?php $ci =& get_instance(); $rate_plats = $ci->db->select('kol_platforms.id, kol_platforms.username, kol_platforms.followers, platforms.name as platform_name')->from('kol_platforms')->join('platforms', 'platforms.id = kol_platforms.platform_id', 'LEFT')->where('kol_platforms.is_active', 1)->order_by('kol_platforms.username', 'ASC')->get()->result(); ?>
                                    <?php foreach ($rate_plats as $row): ?>
                                    <option value="<?= $row->id ?>"><?= _ent($row->username); ?> — <?= _ent($row->platform_name ?? '-'); ?> (<?= number_format((int)$row->followers); ?> followers)</option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="content_type" class="col-sm-2 control-label">Content Type 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <?php $ct_opts = array_keys(kol_rate_content_types()); ?>
                                <select class="form-control" name="content_type" id="content_type">
                                    <option value="">- Pilih Tipe Konten -</option>
                                    <?php foreach ($ct_opts as $o): ?>
                                    <option value="<?= $o; ?>" <?= set_select('content_type', $o); ?>><?= $o; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                <b>Pilih Content Type</b> sesuai tipe task kanonik.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="rate" class="col-sm-2 control-label">Budget 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="rate" id="rate" placeholder="Budget" value="<?= set_value('rate'); ?>">
                                <small class="info help-block">
                                <b>Input Rate</b> Max Length : 15.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="currency_code" class="col-sm-2 control-label">Currency Code 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="currency_code" id="currency_code" placeholder="Currency Code" value="<?= set_value('currency_code'); ?>">
                                <small class="info help-block">
                                <b>Input Currency Code</b> Max Length : 3, In List : IDR,USD.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="notes" class="col-sm-2 control-label">Notes 
                            </label>
                            <div class="col-sm-8">
                                <textarea id="notes" name="notes" rows="5" cols="80"><?= set_value('Notes'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_active" class="col-sm-2 control-label">Status Active 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="is_active" id="is_active" data-placeholder="Select Is Active" >
                                    <option value=""></option>
                                    <option value="0">INACTIVE</option>
                                    <option value="1">Active</option>
                                    </select>
                                <small class="info help-block">
                                <b>Input Is Active</b> Max Length : 1.</small>
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
            CKEDITOR.replace('notes'); 
      var notes = CKEDITOR.instances.notes;
                   
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
              window.location.href = BASE_URL + 'administrator/kol_rate_cards';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#notes').val(notes.getData());
                    
        var form_kol_rate_cards = $('#form_kol_rate_cards');
        var data_post = form_kol_rate_cards.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/kol_rate_cards/add_save',
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
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
            notes.setData('');
                
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