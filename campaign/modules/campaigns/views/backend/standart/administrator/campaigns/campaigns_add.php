
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
        Campaigns        <small><?= cclang('new', ['Campaigns']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/campaigns'); ?>">Campaigns</a></li>
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
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="User Avatar">
                            </div>
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">Campaigns</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Campaigns']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_campaigns', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_campaigns', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                        <!-- SECTION 1: Informasi Utama & Jadwal -->
                        <div class="box box-default" style="border-top: 2px solid #3c8dbc; margin-bottom: 20px;">
                            <div class="box-header with-border">
                                <h4 class="box-title" style="font-size: 15px; font-weight: 600;">1. Informasi Utama & Jadwal Campaign</h4>
                            </div>
                            <div class="box-body" style="padding-top: 15px;">
                                <div class="form-group ">
                                    <label for="name" class="col-sm-2 control-label">Nama Campaign 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Nama Campaign" value="<?= set_value('name'); ?>">
                                        <small class="info help-block">Input Nama Campaign. Max Length: 200.</small>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="product_ids" class="col-sm-2 control-label">Produk 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control chosen chosen-select" name="product_ids[]" id="product_ids" data-placeholder="Pilih Produk..." multiple>
                                            <?php if (!empty($products)): ?>
                                            <?php foreach ($products as $p): ?>
                                            <option value="<?= $p->id; ?>">[<?= $p->sku ?: 'PROD'; ?>] <?= $p->name; ?> (<?= $p->category ?: 'General'; ?> - Rp <?= number_format($p->price, 0, ',', '.'); ?>)</option>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <small class="info help-block">Pilih satu atau beberapa produk campaign.</small>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="campaign_type" class="col-sm-2 control-label">Tipe Campaign 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control chosen chosen-select" name="campaign_type" id="campaign_type" data-placeholder="Pilih Tipe Campaign" >
                                            <option value=""></option>
                                            <option value="PAID">PAID</option>
                                            <option value="BARTER">BARTER</option>
                                            <option value="AFFILIATE">AFFILIATE</option>
                                            <option value="SEEDING">SEEDING</option>
                                            <option value="EVENT">EVENT</option>
                                        </select>
                                    </div>
                                </div>

                                 <!-- Status default DRAFT (hidden saat create) -->
                                 <input type="hidden" name="status" id="status" value="DRAFT">

                                <div class="form-group ">
                                    <label for="budget" class="col-sm-2 control-label">Budget 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" name="budget" id="budget" placeholder="Budget" value="<?= set_value('budget'); ?>">
                                        <small class="info help-block">Input Budget.</small>
                                    </div>
                                    <div class="col-sm-3">
                                        <select class="form-control chosen chosen-select" name="currency_code" id="currency_code" data-placeholder="Mata Uang">
                                            <option value="IDR" <?= set_value('currency_code', 'IDR') == 'IDR' ? 'selected' : ''; ?>>IDR</option>
                                            <option value="USD" <?= set_value('currency_code') == 'USD' ? 'selected' : ''; ?>>USD</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="start_date" class="col-sm-2 control-label">Start Campaign 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-4">
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" name="start_date" placeholder="Start Campaign" id="start_date" value="<?= set_value('start_date'); ?>">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>

                                    <label for="end_date" class="col-sm-1 control-label">Done Date 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-3">
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" name="end_date" placeholder="Done Date" id="end_date" value="<?= set_value('end_date'); ?>">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="description" class="col-sm-2 control-label">Deskripsi / Catatan 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-8">
                                        <textarea id="description" name="description" rows="3" class="form-control" placeholder="Deskripsi campaign"><?= set_value('description'); ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="terms_and_conditions" class="col-sm-2 control-label">Syarat &amp; Ketentuan (S&amp;K MoU)</label>
                                    <div class="col-sm-8">
                                        <textarea id="terms_and_conditions" name="terms_and_conditions" rows="4" class="form-control" placeholder="Klausul khusus S&K Brand untuk surat perjanjian / MoU (opsional, jika kosong menggunakan standar)"><?= set_value('terms_and_conditions'); ?></textarea>
                                        <small class="info help-block">Syarat &amp; Ketentuan khusus Brand ini akan otomatis disisipkan ke dalam draf Surat Perjanjian (MoU) sebelum penandatanganan kontrak.</small>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="pic_user_id" class="col-sm-2 control-label">Penanggung Jawab (PIC)</label>
                                    <div class="col-sm-8">
                                        <select class="form-control chosen chosen-select-deselect" name="pic_user_id" id="pic_user_id" data-placeholder="Pilih PIC User Id" >
                                            <option value=""></option>
                                            <?php 
                                            $staff_users = $this->db->select('u.id, u.full_name, GROUP_CONCAT(DISTINCT g.name ORDER BY g.id SEPARATOR " / ") as role_name')
                                                ->from('aauth_users u')
                                                ->join('aauth_user_to_group utg', 'utg.user_id = u.id')
                                                ->join('aauth_groups g', 'g.id = utg.group_id')
                                                ->where_in('g.name', ['Admin', 'Leader KOL', 'Operational'])
                                                ->where('u.banned', 0)
                                                ->group_by('u.id')
                                                ->order_by('u.full_name', 'ASC')
                                                ->get()->result();
                                            foreach ($staff_users as $row): 
                                            ?>
                                            <option value="<?= $row->id; ?>"><?= _ent($row->full_name); ?> (<?= _ent($row->role_name); ?>)</option>
                                            <?php endforeach; ?>  
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: Model Sourcing KOL & Kualifikasi -->
                        <div class="box box-default" style="border-top: 2px solid #00a65a; margin-bottom: 20px;">
                            <div class="box-header with-border">
                                <h4 class="box-title" style="font-size: 15px; font-weight: 600;">2. Model Sourcing KOL</h4>
                            </div>
                            <div class="box-body" style="padding-top: 15px;">
                                <div class="form-group">
                                    <label for="sourcing_model" class="col-sm-2 control-label">Sourcing Model 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-8">
                                        <label class="radio-inline" style="padding-left: 0; margin-right: 25px;">
                                            <input type="radio" name="sourcing_model" id="sourcing_model_appointed" value="APPOINTED" <?= set_radio('sourcing_model', 'APPOINTED', TRUE); ?>> Appointed
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="sourcing_model" id="sourcing_model_open_post" value="OPEN_POST" <?= set_radio('sourcing_model', 'OPEN_POST'); ?>> Open Post (Public Campaign)
                                        </label>
                                    </div>
                                </div>

                                <!-- Open Post Qualification Configuration Section (Collapsible) -->
                                <div id="open_post_settings" style="display: none; background: #fafafa; border: 1px solid #e5e5e5; border-radius: 3px; padding: 15px 15px 5px 15px; margin: 15px 0;">
                                    <div class="form-group">
                                        <label for="application_deadline" class="col-sm-2 control-label">Deadline</label>
                                        <div class="col-sm-4">
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" name="application_deadline" id="application_deadline" placeholder="YYYY-MM-DD" value="<?= set_value('application_deadline'); ?>">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>

                                        <label for="max_slots" class="col-sm-2 control-label">Target Slots</label>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" name="max_slots" id="max_slots" placeholder="e.g. 10" min="1" value="<?= set_value('max_slots'); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="required_platforms" class="col-sm-2 control-label">Platforms</label>
                                        <div class="col-sm-8">
                                            <select class="form-control chosen chosen-select" name="required_platforms[]" id="required_platforms" data-placeholder="Pilih platform..." multiple>
                                                <?php if (!empty($platforms)): ?>
                                                <?php foreach ($platforms as $plat): ?>
                                                <option value="<?= $plat->id; ?>"><?= _ent($plat->name); ?> (<?= _ent($plat->code); ?>)</option>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="allowed_tiers" class="col-sm-2 control-label">Eligible Tiers</label>
                                        <div class="col-sm-8">
                                            <select class="form-control chosen chosen-select" name="allowed_tiers[]" id="allowed_tiers" data-placeholder="Pilih tier KOL..." multiple>
                                                <?php if (!empty($tiers)): ?>
                                                <?php foreach ($tiers as $tier): ?>
                                                <option value="<?= $tier->id; ?>"><?= _ent($tier->name); ?></option>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="allowed_categories" class="col-sm-2 control-label">Eligible Categories</label>
                                        <div class="col-sm-8">
                                            <select class="form-control chosen chosen-select" name="allowed_categories[]" id="allowed_categories" data-placeholder="Pilih kategori..." multiple>
                                                <?php if (!empty($categories)): ?>
                                                <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat->id; ?>"><?= _ent($cat->name); ?></option>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="min_followers" class="col-sm-2 control-label">Min Followers</label>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" name="min_followers" id="min_followers" placeholder="0" min="0" value="<?= set_value('min_followers', 0); ?>">
                                        </div>

                                        <label for="min_er" class="col-sm-2 control-label">Min ER (%)</label>
                                        <div class="col-sm-4">
                                            <input type="number" step="0.01" class="form-control" name="min_er" id="min_er" placeholder="0.00" min="0" value="<?= set_value('min_er', '0.00'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: Target Performa & Brief Kreatif -->
                        <div class="box box-default" style="border-top: 2px solid #f39c12; margin-bottom: 20px;">
                            <div class="box-header with-border">
                                <h4 class="box-title" style="font-size: 15px; font-weight: 600;">3. Target Performa & Brief</h4>
                            </div>
                            <div class="box-body" style="padding-top: 15px;">
                                <div class="form-group ">
                                    <label for="sow" class="col-sm-2 control-label">Scope Of Work 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-8">
                                        <div id="campaigns_sow_galery"></div>
                                        <div id="campaigns_sow_galery_listed"></div>
                                        <small class="info help-block">Upload file dokumen Scope of Work.</small>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="target_views" class="col-sm-2 control-label">Target Views 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="target_views" id="target_views" placeholder="Target Views" value="<?= set_value('target_views'); ?>">
                                    </div>

                                    <label for="target_er" class="col-sm-2 control-label">Target ER (%) 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <?php 
                                                $target_er_val = '';
                                                if (set_value('target_er') !== '') {
                                                    $target_er_val = (float)set_value('target_er') * 100;
                                                }
                                            ?>
                                            <input type="number" step="any" class="form-control" name="target_er" id="target_er" placeholder="Target ER" value="<?= $target_er_val ?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="target_cpv" class="col-sm-2 control-label">Target CPV (Rp) 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="number" step="any" class="form-control" name="target_cpv" id="target_cpv" placeholder="Target CPV" value="<?= set_value('target_cpv'); ?>">
                                    </div>

                                    <label for="target_cpe" class="col-sm-2 control-label">Target CPE (Rp) 
                                    <i class="required">*</i>
                                    </label>
                                    <div class="col-sm-2">
                                        <input type="number" step="any" class="form-control" name="target_cpe" id="target_cpe" placeholder="Target CPE" value="<?= set_value('target_cpe'); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">ROI Calculator</label>
                                    <div class="col-sm-8">
                                        <div class="well well-sm" style="margin-bottom: 0; background: #f8f9fa;">
                                            <strong>Estimasi CPV:</strong> <span id="calc_cpv" style="font-weight: 600;">Rp 0</span> &nbsp;|&nbsp; 
                                            <strong>Estimasi CPE:</strong> <span id="calc_cpe" style="font-weight: 600;">Rp 0</span>
                                        </div>
                                        <small class="help-block">CPV = Budget / Target Views | CPE = Budget / (Target Views * Target ER%)</small>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="target_audience" class="col-sm-2 control-label">Target Audience</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="target_audience" id="target_audience" placeholder="Target Audience" value="<?= set_value('target_audience'); ?>">
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="objective" class="col-sm-2 control-label">Objective Target</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="objective" id="objective" placeholder="Objective Target" value="<?= set_value('objective'); ?>">
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="hashtag" class="col-sm-2 control-label">Hashtag</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="hashtag" id="hashtag" placeholder="#Hashtag" value="<?= set_value('hashtag'); ?>">
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="key_message" class="col-sm-2 control-label">Key Message</label>
                                    <div class="col-sm-8">
                                        <textarea id="key_message" name="key_message" rows="3" class="form-control" placeholder="Key Message"><?= set_value('key_message'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields for backward compatibility -->
                        <input type="hidden" name="brand_name" id="brand_name" value="<?= set_value('brand_name'); ?>">
                        <input type="hidden" name="client_name" id="client_name" value="<?= set_value('client_name'); ?>">
                        <input type="hidden" name="brand_category" id="brand_category" value="<?= set_value('brand_category'); ?>">
                                                
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
                   
      $('#budget').on('input', function() {
          var val = $(this).val().replace(/[^0-9]/g, '');
          if (val) {
              $(this).val(parseInt(val, 10).toLocaleString('id-ID'));
          } else {
              $(this).val('');
          }
          calculateROI();
      });

      $('#target_views, #target_er').on('input', function() {
          calculateROI();
      });

      function calculateROI() {
          var budgetStr = $('#budget').val().replace(/[^0-9]/g, '');
          var budget = parseFloat(budgetStr) || 0;
          var views = parseFloat($('#target_views').val()) || 0;
          var er = parseFloat($('#target_er').val()) || 0;

          var cpv = 0;
          var cpe = 0;

          if (budget > 0 && views > 0) {
              cpv = budget / views;
          }
          
          if (budget > 0 && views > 0 && er > 0) {
              var engagements = views * (er / 100);
              cpe = budget / engagements;
          }

          $('#calc_cpv').text('Rp ' + cpv.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2}));
          $('#calc_cpe').text('Rp ' + cpe.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2}));
      }

      // Initial calculate if fields are pre-filled
      calculateROI();

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
              window.location.href = BASE_URL + 'administrator/campaigns';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_campaigns = $('#form_campaigns');
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        
        if (start_date && end_date) {
            var startDateObj = new Date(start_date);
            var endDateObj = new Date(end_date);
            
            if (endDateObj < startDateObj) {
                $('.message').printMessage({message : 'End Date (Done Date) cannot be earlier than Start Campaign Date.', type : 'warning'});
                $('.message').fadeIn();
                return false;
            }
        }
        var sow_files = $('#campaigns_sow_galery_listed .listed_file_name').map(function(){ return $(this).val(); }).get().filter(Boolean);
        if (sow_files.length === 0) {
          $('.message').printMessage({message : 'Scope Of Work field is required.', type : 'warning'});
          $('.message').fadeIn();
          $('html, body').animate({ scrollTop: $('#campaigns_sow_galery').offset().top - 100 }, 500);
          return false;
        }
        var data_post = form_campaigns.serializeArray();
        $.each(data_post, function(i, field) {
            if (field.name === 'budget') {
                field.value = field.value.replace(/\./g, '');
            }
            if (field.name === 'target_er') {
                var er_val = parseFloat(field.value);
                if (!isNaN(er_val)) {
                    field.value = er_val / 100;
                }
            }
        });
        
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/campaigns/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          console.log('[Campaign Add Save Response]', res);
          if(res.success) {
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            $('#campaigns_sow_galery').find('li').each(function() {
               $('#campaigns_sow_galery').fineUploader('deleteFile', $(this).attr('qq-file-id'));
            });
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
                
          } else {
            console.warn('[Campaign Add Save Error]', res.message);
            $('.message').printMessage({message : res.message, type : 'warning'});
          }
    
        })
        .fail(function(xhr, status, error) {
          console.error('[Campaign Add Save AJAX Failed]', status, error, xhr.responseText);
          var errorMsg = 'Error save data';
          if (xhr.responseText) {
            var stripped = xhr.responseText.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim();
            if (stripped) {
              errorMsg += ' (' + xhr.status + '): ' + stripped.substring(0, 300);
            }
          }
          $('.message').printMessage({message : errorMsg, type : 'warning'});
        })
        .always(function() {
          $('.loading').hide();
          $('html, body').animate({ scrollTop: $(document).height() }, 2000);
        });
    
        return false;
      }); /*end btn save*/
      
       
 
              var params = {};
       params[csrf] = token;

       $('#campaigns_sow_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/campaigns/upload_sow_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/campaigns/delete_sow_file',
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
                   var uuid = $('#campaigns_sow_galery').fineUploader('getUuid', id);
                   $('#campaigns_sow_galery_listed').append('<input type="hidden" class="listed_file_uuid" name="campaigns_sow_uuid['+id+']" value="'+uuid+'" /><input type="hidden" class="listed_file_name" name="campaigns_sow_name['+id+']" value="'+xhr.uploadName+'" />');
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#campaigns_sow_galery_listed').find('.listed_file_uuid[name="campaigns_sow_uuid['+id+']"]').remove();
                  $('#campaigns_sow_galery_listed').find('.listed_file_name[name="campaigns_sow_name['+id+']"]').remove();
                }
              }
          }
      }); /*end sow galery*/
              
    
    
    // Sourcing Model Toggle
    function toggleSourcingModel() {
        if ($('input[name="sourcing_model"]:checked').val() === 'OPEN_POST') {
            $('#open_post_settings').slideDown(200, function() {
                $('#open_post_settings .chosen-select').trigger('chosen:updated');
            });
        } else {
            $('#open_post_settings').slideUp(200);
        }
    }

    $('input[name="sourcing_model"]').on('change', toggleSourcingModel);
    toggleSourcingModel();

    }); /*end doc ready*/
</script>