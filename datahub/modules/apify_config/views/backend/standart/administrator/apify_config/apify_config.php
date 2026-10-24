<section class="content-header">
   <h1>
      Apify Scraper
      <small>Pengaturan API Key, Jaringan, dan Actor Scraper</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Konfigurasi</a></li>
      <li class="active">Apify Scraper</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title">Pengaturan Apify Scraper</h3>
               <div class="box-tools pull-right">
                  <a href="https://console.apify.com/account/integrations" target="_blank" class="btn btn-xs btn-default">
                     <i class="fa fa-external-link"></i> Apify Console
                  </a>
                  <a href="<?= site_url('administrator/scraping'); ?>" class="btn btn-xs btn-default">
                     <i class="fa fa-list"></i> Scraping Monitor
                  </a>
               </div>
            </div>

            <form id="form_apify_config" method="POST" action="<?= site_url('administrator/apify_config/save'); ?>">
               <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
               <div class="box-body">

                  <!-- Section 1: API Token & Request Settings -->
                  <div class="row">
                     <div class="col-md-7">
                        <div class="box box-solid" style="border: 1px solid #e5e7eb;">
                           <div class="box-header with-border" style="background: #fafafa;">
                              <h4 class="box-title" style="font-size: 14px; font-weight: 600;">Autentikasi API Key</h4>
                           </div>
                           <div class="box-body">
                              <div class="form-group">
                                 <label>Personal API Token <span class="text-danger">*</span></label>
                                 <div class="input-group">
                                    <input type="password" class="form-control" name="apify_api_token" id="apify_api_token" value="<?= _ent($config_data['apify_api_token']); ?>" placeholder="apify_api_..." autocomplete="off">
                                    <span class="input-group-btn">
                                       <button class="btn btn-default" type="button" id="btn_toggle_token" title="Lihat/Sembunyikan">
                                          <i class="fa fa-eye" id="icon_toggle_token"></i>
                                       </button>
                                       <button class="btn btn-info" type="button" id="btn_test_connection">
                                          Test Koneksi
                                       </button>
                                    </span>
                                 </div>
                                 <small class="help-block text-muted">
                                    Dapatkan token Anda pada Apify Console &gt; Settings &gt; Integrations &gt; API Tokens.
                                 </small>
                              </div>

                              <!-- Live Connection Feedback Box -->
                              <div id="test_connection_result" style="display: none; margin-top: 15px;"></div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-5">
                        <div class="box box-solid" style="border: 1px solid #e5e7eb;">
                           <div class="box-header with-border" style="background: #fafafa;">
                              <h4 class="box-title" style="font-size: 14px; font-weight: 600;">Parameter Request</h4>
                           </div>
                           <div class="box-body">
                              <div class="form-group">
                                 <label>Base URL API</label>
                                 <input type="text" class="form-control" name="apify_base_url" value="<?= _ent($config_data['apify_base_url']); ?>" placeholder="https://api.apify.com/v2">
                              </div>

                              <div class="row">
                                 <div class="col-xs-6">
                                    <div class="form-group">
                                       <label>cURL Timeout (Detik)</label>
                                       <input type="number" class="form-control" name="apify_timeout" value="<?= (int)$config_data['apify_timeout']; ?>" min="30" max="600">
                                       <small class="help-block text-muted">Default: 240s</small>
                                    </div>
                                 </div>
                                 <div class="col-xs-6">
                                    <div class="form-group">
                                       <label>Maksimal Retry</label>
                                       <input type="number" class="form-control" name="apify_max_retries" value="<?= (int)$config_data['apify_max_retries']; ?>" min="1" max="10">
                                       <small class="help-block text-muted">Default: 3x</small>
                                    </div>
                                 </div>
                              </div>

                              <div class="form-group" style="margin-bottom: 0;">
                                 <label>Results Limit (Items per Scrape)</label>
                                 <input type="number" class="form-control" name="apify_results_limit" value="<?= (int)$config_data['apify_results_limit']; ?>" min="5" max="200">
                                 <small class="help-block text-muted">Batas jumlah postingan per request (Default: 30).</small>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Section 2: Platform Actor Mappings with Cost Optimization Preset -->
                  <div class="row">
                     <div class="col-md-12">
                        <div class="box box-solid" style="border: 1px solid #e5e7eb;">
                           <div class="box-header with-border" style="background: #fafafa;">
                              <h4 class="box-title" style="font-size: 14px; font-weight: 600;">
                                 Pemetaan Actor ID per Platform
                              </h4>
                              <button type="button" class="btn btn-xs btn-warning pull-right" id="btn_reset_recommended" style="font-weight: 600;">
                                 <i class="fa fa-magic"></i> Terapkan Preset Hemat (API Dojo & Streamers)
                              </button>
                           </div>
                           <div class="box-body">
                              <div class="alert alert-info" style="margin-bottom: 15px; padding: 10px 14px; border-radius: 4px; font-size: 12.5px;">
                                 <i class="fa fa-lightbulb-o"></i> <b>Optimasi Biaya Scraping (~75-80% Lebih Hemat):</b> Sistem telah mendukung aktor generasi baru dari <b>API Dojo</b> dan <b>Streamers</b>. Biaya scraping turun dari <i>~$0.50/KOL</i> menjadi hanya <i>~$0.13/KOL</i>.
                              </div>

                              <div class="row">
                                 <!-- TikTok -->
                                 <div class="col-md-4" style="margin-bottom: 15px;">
                                    <div style="border: 1px solid #eee; padding: 12px; border-radius: 4px; background: #fff;">
                                       <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px;">
                                          <h5 style="margin: 0; font-weight: 600;"><i class="fa fa-music text-primary"></i> TikTok Scraper</h5>
                                          <span class="label label-success" style="font-size: 10px;">Hemat ~90% ($0.009/run)</span>
                                       </div>
                                       <div class="form-group">
                                          <label style="font-size: 12px;">Post Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="actor_tiktok" id="input_actor_tiktok" value="<?= _ent($config_data['actor_tiktok']); ?>">
                                       </div>
                                       <div class="form-group" style="margin-bottom: 0;">
                                          <label style="font-size: 12px;">Profile Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="profile_actor_tiktok" id="input_profile_actor_tiktok" value="<?= _ent($config_data['profile_actor_tiktok']); ?>">
                                       </div>
                                    </div>
                                 </div>

                                 <!-- Instagram -->
                                 <div class="col-md-4" style="margin-bottom: 15px;">
                                    <div style="border: 1px solid #eee; padding: 12px; border-radius: 4px; background: #fff;">
                                       <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px;">
                                          <h5 style="margin: 0; font-weight: 600;"><i class="fa fa-instagram text-danger"></i> Instagram Scraper</h5>
                                          <span class="label label-success" style="font-size: 10px;">Hemat ~78% ($0.015/run)</span>
                                       </div>
                                       <div class="form-group">
                                          <label style="font-size: 12px;">Post Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="actor_instagram" id="input_actor_instagram" value="<?= _ent($config_data['actor_instagram']); ?>">
                                       </div>
                                       <div class="form-group" style="margin-bottom: 0;">
                                          <label style="font-size: 12px;">Profile Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="profile_actor_instagram" id="input_profile_actor_instagram" value="<?= _ent($config_data['profile_actor_instagram']); ?>">
                                       </div>
                                    </div>
                                 </div>

                                 <!-- YouTube -->
                                 <div class="col-md-4" style="margin-bottom: 15px;">
                                    <div style="border: 1px solid #eee; padding: 12px; border-radius: 4px; background: #fff;">
                                       <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px;">
                                          <h5 style="margin: 0; font-weight: 600;"><i class="fa fa-youtube-play text-danger"></i> YouTube Scraper</h5>
                                          <span class="label label-success" style="font-size: 10px;">Hemat ~66% ($0.040/run)</span>
                                       </div>
                                       <div class="form-group">
                                          <label style="font-size: 12px;">Video Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="actor_youtube" id="input_actor_youtube" value="<?= _ent($config_data['actor_youtube']); ?>">
                                       </div>
                                       <div class="form-group" style="margin-bottom: 0;">
                                          <label style="font-size: 12px;">Channel Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="profile_actor_youtube" id="input_profile_actor_youtube" value="<?= _ent($config_data['profile_actor_youtube']); ?>">
                                       </div>
                                    </div>
                                 </div>

                                 <!-- Twitter / X -->
                                 <div class="col-md-6">
                                    <div style="border: 1px solid #eee; padding: 12px; border-radius: 4px; background: #fff;">
                                       <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px;">
                                          <h5 style="margin: 0; font-weight: 600;"><i class="fa fa-twitter text-info"></i> Twitter / X Scraper</h5>
                                          <span class="label label-default" style="font-size: 10px;">~$0.008/run</span>
                                       </div>
                                       <div class="form-group">
                                          <label style="font-size: 12px;">Post Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="actor_twitter" id="input_actor_twitter" value="<?= _ent($config_data['actor_twitter']); ?>">
                                       </div>
                                       <div class="form-group" style="margin-bottom: 0;">
                                          <label style="font-size: 12px;">Profile Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="profile_actor_twitter" id="input_profile_actor_twitter" value="<?= _ent($config_data['profile_actor_twitter']); ?>">
                                       </div>
                                    </div>
                                 </div>

                                 <!-- Facebook -->
                                 <div class="col-md-6">
                                    <div style="border: 1px solid #eee; padding: 12px; border-radius: 4px; background: #fff;">
                                       <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px;">
                                          <h5 style="margin: 0; font-weight: 600;"><i class="fa fa-facebook-official text-primary"></i> Facebook Scraper</h5>
                                          <span class="label label-default" style="font-size: 10px;">Post/Page Scraper</span>
                                       </div>
                                       <div class="form-group">
                                          <label style="font-size: 12px;">Post Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="actor_facebook" id="input_actor_facebook" value="<?= _ent($config_data['actor_facebook']); ?>">
                                       </div>
                                       <div class="form-group" style="margin-bottom: 0;">
                                          <label style="font-size: 12px;">Page Actor ID</label>
                                          <input type="text" class="form-control input-sm" name="profile_actor_facebook" id="input_profile_actor_facebook" value="<?= _ent($config_data['profile_actor_facebook']); ?>">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

               </div>


               <!-- Bottom Action Bar -->
               <div class="box-footer">
                  <button type="submit" class="btn btn-primary btn_save">
                     <i class="fa fa-save"></i> Simpan Konfigurasi
                  </button>
                  <span class="loading loading-hide" style="margin-left: 12px;">
                     <i class="fa fa-spinner fa-spin"></i> Menyimpan data...
                  </span>
               </div>
            </form>
         </div>
      </div>
   </div>
</section>

<script>
$(document).ready(function() {
   // Toggle password visibility
   $('#btn_toggle_token').click(function() {
      var input = $('#apify_api_token');
      var icon = $('#icon_toggle_token');
      if (input.attr('type') === 'password') {
         input.attr('type', 'text');
         icon.removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
         input.attr('type', 'password');
         icon.removeClass('fa-eye-slash').addClass('fa-eye');
      }
   });

   // Preset Reset to Recommended Cost-Optimized Actors
   $('#btn_reset_recommended').click(function() {
      if (!confirm('Terapkan Actor ID ekonomis rekomendasi (API Dojo & Streamers) untuk menghemat biaya hingga 80%?')) {
         return;
      }
      var btn = $(this);
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menerapkan...');

      $.ajax({
         url: BASE_URL + 'administrator/apify_config/reset_to_recommended',
         type: 'POST',
         dataType: 'json',
         data: {
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
         },
         success: function(res) {
            btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Terapkan Preset Hemat (API Dojo & Streamers)');
            if (res.success && res.actors) {
               $('#input_actor_tiktok').val(res.actors.actor_tiktok);
               $('#input_actor_instagram').val(res.actors.actor_instagram);
               $('#input_actor_youtube').val(res.actors.actor_youtube);
               $('#input_actor_twitter').val(res.actors.actor_twitter);
               $('#input_actor_facebook').val(res.actors.actor_facebook);

               $('#input_profile_actor_tiktok').val(res.actors.profile_actor_tiktok);
               $('#input_profile_actor_instagram').val(res.actors.profile_actor_instagram);
               $('#input_profile_actor_youtube').val(res.actors.profile_actor_youtube);
               $('#input_profile_actor_twitter').val(res.actors.profile_actor_twitter);
               $('#input_profile_actor_facebook').val(res.actors.profile_actor_facebook);

               toastr['success'](res.message);
            } else {
               toastr['error'](res.message || 'Gagal menerapkan preset.');
            }
         },
         error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Terapkan Preset Hemat (API Dojo & Streamers)');
            toastr['error']('Terjadi kesalahan: HTTP ' + xhr.status);
         }
      });
   });

   // Live Test Connection Button (Apify)
   $('#btn_test_connection').click(function() {
      var btn = $(this);
      var token = $('#apify_api_token').val().trim();
      var resultBox = $('#test_connection_result');

      if (!token) {
         toastr['warning']('Silakan isi token Apify terlebih dahulu.');
         return;
      }

      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghubungkan...');
      resultBox.slideUp();

      $.ajax({
         url: BASE_URL + 'administrator/apify_config/test_connection',
         type: 'POST',
         dataType: 'json',
         data: {
            token: token,
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
         },
         success: function(res) {
            btn.prop('disabled', false).html('<i class="fa fa-plug"></i> Test Koneksi');
            if (res.success && res.user) {
               var html = `
                  <div class="alert alert-success" style="margin: 0; padding: 12px 16px; border-radius: 6px;">
                     <h5 style="margin-top:0; font-weight:700;"><i class="fa fa-check-circle"></i> ${res.message}</h5>
                     <div style="font-size: 12.5px; line-height: 1.6;">
                        <b>Username:</b> ${res.user.username} (${res.user.email})<br>
                        <b>Paket (Plan):</b> <span class="label label-info" style="font-size:10px;">${res.user.plan}</span> &nbsp;|&nbsp; 
                        <b>Penggunaan Bulan Ini:</b> <b>${res.user.monthly_usage}</b>
                     </div>
                  </div>
               `;
               resultBox.html(html).slideDown();
               toastr['success']('Koneksi Apify Berhasil Valid!');
            } else {
               var html = `
                  <div class="alert alert-danger" style="margin: 0; padding: 12px 16px; border-radius: 6px;">
                     <h5 style="margin-top:0; font-weight:700;"><i class="fa fa-times-circle"></i> Koneksi Gagal</h5>
                     <div style="font-size: 12px;">${res.message || 'Token tidak valid.'}</div>
                  </div>
               `;
               resultBox.html(html).slideDown();
               toastr['error'](res.message || 'Gagal terhubung ke Apify.');
            }
         },
         error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fa fa-plug"></i> Test Koneksi');
            resultBox.html(`
               <div class="alert alert-danger" style="margin: 0; padding: 12px 16px; border-radius: 6px;">
                  <h5 style="margin-top:0; font-weight:700;"><i class="fa fa-exclamation-triangle"></i> Terjadi Kesalahan</h5>
                  <div style="font-size: 12px;">HTTP Status ${xhr.status}: ${xhr.statusText}</div>
               </div>
            `).slideDown();
            toastr['error']('Gagal melakukan tes koneksi.');
         }
      });
   });

   // AJAX Form Submission
   $('#form_apify_config').submit(function(e) {
      e.preventDefault();
      var form = $(this);
      var btn = form.find('.btn_save');
      var loading = form.find('.loading');

      btn.prop('disabled', true);
      loading.show();

      $.ajax({
         url: form.attr('action'),
         type: 'POST',
         dataType: 'json',
         data: form.serialize(),
         success: function(res) {
            btn.prop('disabled', false);
            loading.hide();
            if (res.success) {
               toastr['success'](res.message);
            } else {
               toastr['error'](res.message);
            }
         },
         error: function(xhr) {
            btn.prop('disabled', false);
            loading.hide();
            toastr['error']('Terjadi kesalahan saat menyimpan: HTTP ' + xhr.status);
         }
      });
   });
});
</script>
