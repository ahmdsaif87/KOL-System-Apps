<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      KeyAPI TikTok Shop
      <small>Konfigurasi API & GMV Analytics</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Konfigurasi</a></li>
      <li class="active">KeyAPI</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-shopping-bag text-primary"></i> Pengaturan Kredensial & Sinkronisasi KeyAPI</h3>
               <div class="box-tools pull-right">
                  <a href="https://keyapi.ai" target="_blank" class="btn btn-xs btn-default">
                     <i class="fa fa-external-link"></i> KeyAPI Portal
                  </a>
               </div>
            </div>

            <form action="<?= base_url('administrator/keyapi_config/save'); ?>" method="POST" id="form_keyapi_config" class="form-horizontal">
               <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token_field">
               <div class="box-body" style="padding: 24px;">

                  <!-- KeyAPI API Credentials -->
                  <div class="panel panel-default" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                     <div class="panel-heading" style="background: #f8fafc; font-weight: 600; font-size: 14px;">
                        <i class="fa fa-key text-yellow"></i> Kredensial API KeyAPI
                     </div>
                     <div class="panel-body" style="padding: 20px;">
                        <div class="form-group">
                           <label class="col-sm-3 control-label">KeyAPI Bearer Key <span class="text-danger">*</span></label>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <input type="password" class="form-control" name="keyapi_api_key" id="keyapi_api_key"
                                    value="<?= _ent($config_data['keyapi_api_key']); ?>"
                                    placeholder="sk_live_xxxxxxxxxxxxx" autocomplete="off">
                                 <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="btn_toggle_keyapi_key" title="Tampilkan/Sembunyikan Token">
                                       <i class="fa fa-eye" id="icon_toggle_keyapi"></i>
                                    </button>
                                    <button class="btn btn-info" type="button" id="btn_test_keyapi">
                                       <i class="fa fa-plug"></i> Test Koneksi
                                    </button>
                                 </span>
                              </div>
                              <div id="test_keyapi_result" style="display: none; margin-top: 10px;"></div>
                              <span class="help-block text-muted" style="margin-top: 8px;">
                                 Bearer API Key format <code>sk_live_...</code> digunakan pada HTTP header <code>Authorization: Bearer &lt;KEY&gt;</code>. Biaya 1 kredit per panggilan.
                              </span>
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-3 control-label">Base API URL</label>
                           <div class="col-sm-8">
                              <input type="text" class="form-control" name="keyapi_base_url" id="keyapi_base_url"
                                 value="<?= _ent($config_data['keyapi_base_url']); ?>" placeholder="https://api.keyapi.ai">
                              <span class="help-block text-muted">Endpoint API KeyAPI. Default: <code>https://api.keyapi.ai</code></span>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Cache & Limits Settings -->
                  <div class="panel panel-default" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                     <div class="panel-heading" style="background: #f8fafc; font-weight: 600; font-size: 14px;">
                        <i class="fa fa-database text-green"></i> Pengaturan Cache & Batasan Sinkronisasi
                     </div>
                     <div class="panel-body" style="padding: 20px;">
                        <div class="form-group">
                           <label class="col-sm-3 control-label">Cache Duration (Hari)</label>
                           <div class="col-sm-4">
                              <div class="input-group">
                                 <input type="number" class="form-control" name="keyapi_cache_days" id="keyapi_cache_days"
                                    value="<?= (int)$config_data['keyapi_cache_days']; ?>" min="1" max="90">
                                 <span class="input-group-addon">Hari</span>
                              </div>
                              <span class="help-block text-muted">Data GMV yang lebih baru dari durasi ini akan di-skip untuk menghemat kuota credit (default: 7 hari).</span>
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-3 control-label">Maks. Produk per KOL</label>
                           <div class="col-sm-4">
                              <div class="input-group">
                                 <input type="number" class="form-control" name="keyapi_max_products" id="keyapi_max_products"
                                    value="<?= (int)$config_data['keyapi_max_products']; ?>" min="5" max="50">
                                 <span class="input-group-addon">Produk</span>
                              </div>
                              <span class="help-block text-muted">Jumlah produk etalase teratas yang disimpan per kreator (default: 20 produk, diambil via 2 halaman @ 10 produk).</span>
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-3 control-label">Kurs Konversi USD ke IDR</label>
                           <div class="col-sm-4">
                              <div class="input-group">
                                 <span class="input-group-addon">Rp</span>
                                 <input type="number" class="form-control" name="keyapi_usd_to_idr_rate" id="keyapi_usd_to_idr_rate"
                                    value="<?= (float)($config_data['keyapi_usd_to_idr_rate'] ?? 16150); ?>" min="1000" step="50">
                                 <span class="input-group-addon">/ USD</span>
                              </div>
                              <span class="help-block text-muted">Kurs nilai tukar untuk mengonversi angka GMV & harga produk dari USD (output KeyAPI) menjadi Rupiah IDR (default: 16.150).</span>
                           </div>
                        </div>
                     </div>
                  </div>

               </div>
               <!-- /.box-body -->

               <div class="box-footer" style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
                  <button type="submit" class="btn btn-primary" id="btn_save_keyapi">
                     <i class="fa fa-save"></i> Simpan Konfigurasi
                  </button>
               </div>
            </form>
         </div>
      </div>
   </div>
</section>

<script>
$(document).ready(function() {
   // Toggle API Key Visibility
   $('#btn_toggle_keyapi_key').on('click', function() {
      var $input = $('#keyapi_api_key');
      var $icon  = $('#icon_toggle_keyapi');
      if ($input.attr('type') === 'password') {
         $input.attr('type', 'text');
         $icon.removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
         $input.attr('type', 'password');
         $icon.removeClass('fa-eye-slash').addClass('fa-eye');
      }
   });

   function updateCsrf(res) {
      if (res && res.csrf_name && res.csrf_hash) {
         $('#csrf_token_field').attr('name', res.csrf_name).val(res.csrf_hash);
      }
   }

   // Save Form
   $('#form_keyapi_config').on('submit', function(e) {
      e.preventDefault();
      var $btn = $('#btn_save_keyapi');
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

      $.ajax({
         url: $(this).attr('action'),
         type: 'POST',
         dataType: 'json',
         data: $(this).serialize(),
         success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Konfigurasi');
            updateCsrf(res);
            if (res.success) {
               toastr.success(res.message);
            } else {
               toastr.error(res.message);
            }
         },
         error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Konfigurasi');
            toastr.error('Gagal menyimpan konfigurasi server error (HTTP ' + xhr.status + ').');
         }
      });
   });

   // Test Connection
   $('#btn_test_keyapi').on('click', function() {
      var $btn = $(this);
      var $res = $('#test_keyapi_result');
      var key  = $('#keyapi_api_key').val();
      var base = $('#keyapi_base_url').val();
      var csrfName = $('#csrf_token_field').attr('name') || '<?= $this->security->get_csrf_token_name(); ?>';
      var csrfHash = $('#csrf_token_field').val() || '<?= $this->security->get_csrf_hash(); ?>';

      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');
      $res.hide().removeClass('alert-success alert-danger');

      var postData = {
         keyapi_api_key: key,
         keyapi_base_url: base
      };
      postData[csrfName] = csrfHash;

      $.ajax({
         url: '<?= base_url("administrator/keyapi_config/test_connection"); ?>',
         type: 'POST',
         dataType: 'json',
         data: postData,
         success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-plug"></i> Test Koneksi');
            updateCsrf(res);
            $res.show();
            if (res.success) {
               $res.attr('class', 'alert alert-success').html('<i class="fa fa-check-circle"></i> ' + res.message);
            } else {
               $res.attr('class', 'alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> ' + res.message);
            }
         },
         error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fa fa-plug"></i> Test Koneksi');
            $res.show().attr('class', 'alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> Gagal menghubungi endpoint server (HTTP ' + xhr.status + ').');
         }
      });
   });
});
</script>
