<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
function domo(){
   $('*').bind('keydown', 'Ctrl+e', function assets() {
      $('#btn_edit').trigger('click');
       return false;
   });
   $('*').bind('keydown', 'Ctrl+x', function assets() {
      $('#btn_back').trigger('click');
       return false;
   });
}
jQuery(document).ready(domo);
</script>


<style>
/* Clean & Compact UI Styles */
.kol-header {
    background: #fff; border: 1px solid #dfe6ee; border-radius: 4px; padding: 20px; margin-bottom: 20px;
    display: flex; gap: 20px; align-items: flex-start;
}
.kol-header-avatar {
    width: 64px; height: 64px; border-radius: 4px; object-fit: cover;
    background: #f1f5f9; color: #64748b; font-size: 24px; font-weight: bold;
    display: flex; align-items: center; justify-content: center;
}
.kol-header-info { flex: 1; }
.kol-header-title { font-size: 20px; font-weight: 600; margin: 0 0 5px 0; color: #1e293b; }
.kol-header-meta { font-size: 13px; color: #64748b; margin-bottom: 10px; }
.kol-header-actions { text-align: right; display: flex; gap: 8px; justify-content: flex-end; align-items: center; }

.nav-tabs-custom {
    margin-bottom: 20px;
    background: #fff;
    border: 1px solid #dfe6ee;
    border-radius: 6px;
    padding: 0 10px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.nav-tabs-custom > .nav-tabs {
    border-bottom: none;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
}
.nav-tabs-custom > .nav-tabs > li {
    border-top: none !important;
    margin: 0 !important;
    float: none;
}
.nav-tabs-custom > .nav-tabs > li.active {
    border-top: none !important;
    border-right-color: transparent !important;
    border-left-color: transparent !important;
}
.nav-tabs-custom > .nav-tabs > li > a {
    border: none !important;
    border-radius: 0 !important;
    color: #64748b;
    font-weight: 500;
    font-size: 13px;
    padding: 14px 18px !important;
    margin: 0 !important;
    background: transparent !important;
    border-bottom: 2px solid transparent !important;
    transition: color 0.15s ease, border-color 0.15s ease;
    display: inline-flex;
    align-items: center;
}
.nav-tabs-custom > .nav-tabs > li > a:hover {
    color: #1e293b !important;
    background: transparent !important;
    border-bottom: 2px solid #cbd5e1 !important;
}
.nav-tabs-custom > .nav-tabs > li.active > a,
.nav-tabs-custom > .nav-tabs > li.active > a:hover,
.nav-tabs-custom > .nav-tabs > li.active > a:focus {
    color: #2563eb !important;
    font-weight: 600;
    border: none !important;
    border-bottom: 2px solid #2563eb !important;
    background: transparent !important;
}

.tab-content { background: #fff; border: 1px solid #dfe6ee; border-radius: 4px; padding: 20px; }
.section-title { font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 15px; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }

dl.dl-horizontal dt { color: #64748b; font-weight: normal; text-align: left; width: 140px; }
dl.dl-horizontal dd { margin-left: 150px; color: #1e293b; font-weight: 500; margin-bottom: 10px; }

.social-account-card {
    border: 1px solid #e2e8f0; border-radius: 4px; padding: 15px; margin-bottom: 15px;
    display: flex; align-items: center; justify-content: space-between;
}
.social-account-info { display: flex; align-items: center; gap: 12px; }
.social-icon { width: 32px; height: 32px; border-radius: 4px; }
.social-stats { font-size: 12px; color: #64748b; margin-top: 4px; }

.doc-link { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 4px; color: #2563eb; font-weight: 500; text-decoration: none; margin-bottom: 10px; margin-right: 10px; }
.doc-link:hover { background: #f8fafc; }

.badge-status { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; }
.badge-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.badge-approved { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-default-soft { background: #f1f5f9; color: #475569; }
</style>

<!-- Content Header -->
<section class="content-header">
   <h1>Detail KOL <small><?= cclang('detail', ['Kols']); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/kols'); ?>">Kols</a></li>
      <li class="active"><?= cclang('detail'); ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         
         <input type="hidden" id="kol-view-csrf-token" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

         <?php
            $kol_name  = $kols->name ?? 'KOL';
            $initials  = strtoupper(mb_substr($kol_name, 0, 1) . (mb_substr(trim(strstr($kol_name, ' ')), 0, 1) ?: mb_substr($kol_name, 1, 1)));
            $kol_status = isset($kols->status) ? strtoupper($kols->status) : '';
         ?>

         <!-- HEADER COMPACT -->
         <div class="kol-header">
            <div class="kol-header-avatar">
               <?php if (!empty($kols->avatar_url)): ?>
                  <img src="<?= htmlspecialchars($kols->avatar_url); ?>" alt="<?= htmlspecialchars($kol_name); ?>" style="width:100%; height:100%; object-fit:cover; border-radius:4px;" />
               <?php else: ?>
                  <?= htmlspecialchars($initials); ?>
               <?php endif; ?>
            </div>
            <div class="kol-header-info">
               <h2 class="kol-header-title">
                  <?= htmlspecialchars($kol_name); ?>
                  <?php if ($kol_status === 'PENDING'): ?>
                     <span class="badge-status badge-pending" style="vertical-align: middle; margin-left: 8px;"><i class="fa fa-clock-o"></i> PENDING</span>
                  <?php elseif ($kol_status === 'APPROVED' || $kol_status === 'ACTIVE'): ?>
                     <span class="badge-status badge-approved" style="vertical-align: middle; margin-left: 8px;"><i class="fa fa-check"></i> APPROVED</span>
                  <?php endif; ?>
               </h2>
               <div class="kol-header-meta">
                  <?php if (!empty($kols->tier_id)): ?>
                     <?= htmlspecialchars($kols->tiers_name ?? 'Standard'); ?> &bull; 
                  <?php endif; ?>
                  <?php if (!empty($kols->city)): ?>
                     <?= htmlspecialchars($kols->city); ?><?= !empty($kols->province) ? ', ' . htmlspecialchars($kols->province) : ''; ?>
                  <?php endif; ?>
               </div>
               <div>
                  <?php if (!empty($kols->is_blacklisted)): ?>
                     <span class="badge-status" style="background: #fee2e2; color: #b91c1c;"><i class="fa fa-ban"></i> Blacklisted</span>
                  <?php endif; ?>
                  <?php if (!empty($kols->total_gmv_estimate)): ?>
                     <span class="badge-status" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;" title="Rp <?= number_format($kols->total_gmv_estimate, 0, ',', '.'); ?>"><i class="fa fa-shopping-bag"></i> GMV: <?= format_idr_friendly($kols->total_gmv_estimate); ?></span>
                  <?php endif; ?>
               </div>
            </div>
            <div class="kol-header-actions">
               <?php if ($kol_status == 'PENDING'): ?>
                  <?php is_allowed('kols_update', function() use ($kols){?>
                     <a class="btn btn-sm btn-success btn-flat" title="Accept KOL" href="<?= site_url('administrator/kols/approve/'.$kols->id); ?>" onclick="return confirm('Apakah Anda yakin ingin menerima (accept) pendaftaran KOL ini?');"><i class="fa fa-check"></i> Accept</a>
                     <a class="btn btn-sm btn-danger btn-flat" title="Reject KOL" href="<?= site_url('administrator/kols/reject/'.$kols->id); ?>" onclick="return confirm('Apakah Anda yakin ingin menolak (reject) pendaftaran KOL ini?');"><i class="fa fa-times"></i> Reject</a>
                  <?php }) ?>
               <?php endif; ?>
               <a class="btn btn-sm btn-flat" style="background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; border: none; padding: 5px 10px;" title="View Performance Card" href="<?= site_url('administrator/performance_card/view/'.$kols->id); ?>"><i class="fa fa-line-chart"></i> Perf. Card</a>
            </div>
         </div>

         <!-- TABS -->
         <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
               <li class="active"><a href="#tab_overview" data-toggle="tab">Overview</a></li>
               <li><a href="#tab_social" data-toggle="tab">Social Media</a></li>
               <li><a href="#tab_gmv" data-toggle="tab">TikTok Shop &amp; GMV</a></li>
            </ul>
         </div>

         <div class="tab-content">
            
            <!-- TAB OVERVIEW -->
            <div class="tab-pane active" id="tab_overview">
               <div class="row">
                  <div class="col-md-6">
                     <h4 class="section-title">Profile Information</h4>
                     <dl class="dl-horizontal">
                        <dt>Phone</dt><dd><?= _ent($kols->phone ?? '-'); ?></dd>
                        <dt>Email</dt><dd><?= _ent($kols->email ?? '-'); ?></dd>
                        <dt>Gender</dt><dd><?= _ent($kols->gender ?? '-'); ?></dd>
                        <dt>Address</dt><dd>
                           <?= _ent($kols->address ?? '-'); ?>
                           <?php if (isset($kols->country) && $kols->country): ?>
                              <div style="color:#6b778c; font-size:12px; margin-top:2px;">
                                 <?= _ent($kols->country); ?>
                              </div>
                           <?php endif; ?>
                        </dd>
                        <dt>Category</dt>
                        <dd>
                           <?php 
                             $kol_categories = isset($kol_categories) ? $kol_categories : [];
                             if (!empty($kol_categories)): 
                           ?>
                              <?= implode(', ', array_map(function($c) { return _ent($c->category_name); }, $kol_categories)); ?>
                           <?php else: ?>
                              -
                           <?php endif; ?>
                        </dd>
                     </dl>

                     <h4 class="section-title" style="margin-top:20px;">Commercial / Financial</h4>
                     <dl class="dl-horizontal">
                        <dt>Bank Name</dt><dd><?= _ent($kols->bank_name ?? '-'); ?></dd>
                        <dt>Account Number</dt><dd><?= _ent($kols->bank_account_number ?? '-'); ?></dd>
                        <dt>Account Name</dt><dd><?= _ent($kols->bank_account_name ?? '-'); ?></dd>
                     </dl>
                  </div>

                  <div class="col-md-6">
                     <h4 class="section-title">Identity & Profile Documents</h4>
                     <div style="margin-bottom: 20px;">
                        
                        <?php if (!empty($kols->ktp_number)): ?>
                           <div style="margin: 10px 0;"><strong>No. KTP:</strong> <?= _ent($kols->ktp_number); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($kols->ktp_file)): ?>
                           <a href="<?= BASE_URL . 'administrator/file/download/kols/' . $kols->ktp_file; ?>" class="doc-link">
                              <i class="fa fa-download text-muted"></i> File KTP
                           </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($kols->npwp_number)): ?>
                           <div style="margin: 10px 0;"><strong>No. NPWP:</strong> <?= _ent($kols->npwp_number); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($kols->npwp_file)): ?>
                           <a href="<?= BASE_URL . 'administrator/file/download/kols/' . $kols->npwp_file; ?>" class="doc-link">
                              <i class="fa fa-download text-muted"></i> File NPWP
                           </a>
                        <?php endif; ?>
                     </div>

                     <h4 class="section-title">Rate Card</h4>
                     <div>
                        <?php if (!empty($kols->rate_card_file)): ?>
                           <?php foreach (explode(',', $kols->rate_card_file) as $filename): ?>
                              <?php if (!empty($filename)): ?>
                                 <?php if (is_image($filename)): ?>
                                    <a class="fancybox doc-link" rel="group" href="<?= BASE_URL . 'uploads/kols/' . $filename; ?>">
                                       <i class="fa fa-image text-muted"></i> <?= $filename ?>
                                    </a>
                                 <?php else: ?>
                                    <a href="<?= BASE_URL . 'administrator/file/download/kols/' . $filename; ?>" class="doc-link">
                                       <img src="<?= get_icon_file($filename); ?>" style="width: 16px;"> <?= $filename ?>
                                    </a>
                                 <?php endif; ?>
                              <?php endif; ?>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <p class="text-muted">Tidak ada rate card.</p>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>

            <!-- TAB SOCIAL MEDIA -->
            <div class="tab-pane" id="tab_social">
               <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                  <h4 class="section-title" style="margin:0; border:none;">Connected Accounts</h4>
               </div>
               
               <div class="row">
               <?php 
                 $kol_platforms = isset($kol_platforms) ? $kol_platforms : [];
                 if (!empty($kol_platforms)): 
               ?>
                  <?php foreach ($kol_platforms as $kp): ?>
                     <?php $kp_profile_url = get_kol_profile_url($kp->profile_url, $kp->username, isset($kp->platform_code) ? $kp->platform_code : '', isset($kp->platform_name) ? $kp->platform_name : ''); ?>
                     <div class="col-md-6">
                        <div class="social-account-card">
                           <div class="social-account-info">
                              <?php if (!empty($kp->icon_url)): ?>
                                 <img src="<?= $kp->icon_url ?>" class="social-icon" alt="<?= _ent($kp->platform_name); ?>" />
                              <?php else: ?>
                                 <div class="social-icon" style="background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#64748b;"><i class="fa fa-share-alt"></i></div>
                              <?php endif; ?>
                              <div>
                                 <div style="font-weight: 600; font-size: 14px; color: #1e293b;">
                                    <a href="<?= _ent($kp_profile_url); ?>" target="_blank" style="color:inherit;">@<?= _ent($kp->username); ?></a>
                                    <span class="label label-default" style="font-size:10px; margin-left:5px; font-weight:normal;"><?= _ent($kp->platform_name); ?></span>
                                 </div>
                                 <div class="social-stats" id="platform-stats-<?= $kp->id ?>">
                                    <strong class="plat-followers"><?= number_format($kp->followers); ?></strong> Followers &bull; 
                                    <strong class="plat-er"><?= number_format($kp->avg_er * 100, 2); ?>%</strong> ER
                                 </div>
                              </div>
                           </div>
                           <button type="button" class="btn btn-xs btn-default btn-sync-single-platform" 
                                   data-platform-id="<?= $kp->platform_id ?>" 
                                   data-platform-name="<?= _ent($kp->platform_name) ?>"
                                   data-username="<?= _ent($kp->username) ?>" 
                                   data-kp-id="<?= $kp->id ?>">
                              <i class="fa fa-refresh text-blue"></i> Sync
                           </button>
                        </div>
                     </div>
                  <?php endforeach; ?>
               <?php else: ?>
                  <div class="col-md-12 text-muted">Belum ada akun platform / media sosial yang terhubung.</div>
               <?php endif; ?>
               </div>
            </div>

         
             <!-- TAB TIKTOK SHOP & GMV -->
             <div class="tab-pane" id="tab_gmv">
                <?php
                   $tiktok_kp = null;
                   if (!empty($kol_platforms)) {
                      foreach ($kol_platforms as $kp) {
                         if (strtolower($kp->platform_code ?? '') === 'tiktok' || strtolower($kp->platform_name ?? '') === 'tiktok') {
                            $tiktok_kp = $kp;
                            break;
                         }
                      }
                   }
                   $gmv_status = $kols->gmv_sync_status ?? 'NEVER';
                   $total_gmv  = (float)($kols->total_gmv_estimate ?? ($gmv_summary->total_gmv ?? 0));
                   $total_sold = (int)($gmv_summary->total_sold ?? 0);
                   $prod_count = (int)($gmv_summary->product_count ?? 0);
                   $last_sync  = !empty($kols->gmv_updated_at) ? date('d M Y, H:i', strtotime($kols->gmv_updated_at)) : '-';
                ?>

                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                   <div>
                      <h4 class="section-title" style="margin:0; border:none;">
                         <i class="fa fa-shopping-bag" style="color: #10b981; margin-right: 6px;"></i>
                         TikTok Shop Showcase &amp; GMV Portfolio
                      </h4>
                      <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">
                         Data etalase produk dan estimasi omset penjualan (GMV) powered by KeyAPI.
                         <?php if ($tiktok_kp): ?>
                            &bull; Akun: <strong>@<?= _ent($tiktok_kp->username); ?></strong>
                         <?php endif; ?>
                      </p>
                   </div>
                   <div>
                      <button type="button" class="btn btn-sm btn-success btn-flat btn-sync-gmv" 
                              data-kol-id="<?= $kols->id ?>"
                              style="background: #10b981; border-color: #059669; font-weight: 600; padding: 7px 16px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                         <i class="fa fa-refresh"></i> Sync GMV (KeyAPI)
                      </button>
                   </div>
                </div>

                <!-- KPI METRICS ROW -->
                <div class="row" style="margin-bottom: 20px;">
                   <div class="col-md-3 col-sm-6">
                      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px;">
                         <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px;">Total GMV (All-Time)</div>
                         <div style="font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 5px;" id="gmv-stat-total" title="Rp <?= number_format($total_gmv, 0, ',', '.'); ?>">
                            <?= format_idr_friendly($total_gmv); ?>
                         </div>
                         <div style="font-size: 11px; color: #10b981; margin-top: 4px;">
                            <i class="fa fa-line-chart"></i> All-Time (Rp <?= number_format($total_gmv, 0, ',', '.'); ?>)
                         </div>
                      </div>
                   </div>

                   <div class="col-md-3 col-sm-6">
                      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px;">
                         <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px;">Produk Terjual (Sold)</div>
                         <div style="font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 5px;" id="gmv-stat-sold">
                            <?= number_format($total_sold, 0, ',', '.'); ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">pcs</span>
                         </div>
                         <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                            Volume penjualan tercatat
                         </div>
                      </div>
                   </div>

                   <div class="col-md-3 col-sm-6">
                      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px;">
                         <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px;">Jumlah Produk Etalase</div>
                         <div style="font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 5px;" id="gmv-stat-count">
                            <?= number_format($prod_count, 0, ',', '.'); ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">item</span>
                         </div>
                         <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                            Snapshot KeyAPI aktif
                         </div>
                      </div>
                   </div>

                   <div class="col-md-3 col-sm-6">
                      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px;">
                         <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px;">Status Sinkronisasi</div>
                         <div style="margin-top: 6px;" id="gmv-stat-badge">
                            <?php if ($gmv_status === 'OK'): ?>
                               <span class="label label-success" style="font-size: 12px; padding: 4px 10px; border-radius: 4px;"><i class="fa fa-check-circle"></i> OK (Tersinkron)</span>
                            <?php elseif ($gmv_status === 'EMPTY'): ?>
                               <span class="label label-warning" style="font-size: 12px; padding: 4px 10px; border-radius: 4px;"><i class="fa fa-exclamation-triangle"></i> ETALASE KOSONG</span>
                            <?php elseif ($gmv_status === 'FAILED'): ?>
                               <span class="label label-danger" style="font-size: 12px; padding: 4px 10px; border-radius: 4px;"><i class="fa fa-times-circle"></i> GAGAL</span>
                            <?php else: ?>
                               <span class="label label-default" style="font-size: 12px; padding: 4px 10px; border-radius: 4px;"><i class="fa fa-clock-o"></i> BELUM SYNC</span>
                            <?php endif; ?>
                         </div>
                         <div style="font-size: 11px; color: #64748b; margin-top: 7px;">
                            Terakhir: <span id="gmv-stat-last-sync"><?= $last_sync; ?></span>
                         </div>
                      </div>
                   </div>
                </div>

                <!-- STATUS ALERT NOTICE (IF ANY) -->
                <?php if ($gmv_status === 'EMPTY'): ?>
                   <div class="alert alert-warning" style="background: #fffbeb; border-color: #fde68a; color: #92400e; border-radius: 6px; margin-bottom: 20px;">
                      <i class="fa fa-info-circle"></i> <strong>Etalase Kosong atau Disembunyikan:</strong> 
                      Akun TikTok terdeteksi, namun kreator tidak menampilkan produk di etalase showcase publik atau belum ada transaksi tercatat.
                   </div>
                <?php elseif ($gmv_status === 'FAILED' && !empty($kols->gmv_sync_message)): ?>
                   <div class="alert alert-danger" style="background: #fef2f2; border-color: #fecaca; color: #991b1b; border-radius: 6px; margin-bottom: 20px;">
                      <i class="fa fa-exclamation-circle"></i> <strong>Sinkronisasi Gagal:</strong> <?= _ent($kols->gmv_sync_message); ?>
                   </div>
                <?php endif; ?>

                <!-- PRODUCTS SHOWCASE TABLE -->
                <div style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; background: #fff;">
                   <div style="padding: 12px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                      <strong style="color: #1e293b; font-size: 13px;">Top Produk Etalase TikTok Shop (All-Time GMV)</strong>
                      <span style="font-size: 12px; color: #64748b;">Maks. 15 produk teratas</span>
                   </div>

                   <div class="table-responsive" style="margin: 0;">
                      <table class="table table-hover table-striped" style="margin-bottom: 0; font-size: 13px;">
                         <thead>
                            <tr style="background: #f1f5f9; color: #475569; font-size: 12px; text-transform: uppercase;">
                               <th style="width: 40px; text-align: center;">#</th>
                               <th>Produk</th>
                               <th>Toko / Seller</th>
                               <th style="text-align: right;">Harga Satuan</th>
                               <th style="text-align: right;">Terjual (KOL)</th>
                               <th style="text-align: right;">Estimasi GMV</th>
                               <th style="width: 60px; text-align: center;">Link</th>
                            </tr>
                         </thead>
                         <tbody>
                            <?php if (!empty($gmv_top)): ?>
                               <?php $no = 1; foreach ($gmv_top as $prod): ?>
                                  <tr>
                                     <td style="text-align: center; color: #64748b; font-weight: 600; vertical-align: middle;"><?= $no++; ?></td>
                                     <td style="vertical-align: middle;">
                                        <div style="font-weight: 600; color: #1e293b; max-width: 380px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= _ent($prod->title); ?>">
                                           <?php if (!empty($prod->product_url)): ?>
                                              <a href="<?= _ent($prod->product_url); ?>" target="_blank" rel="noopener noreferrer" referrerpolicy="no-referrer" style="color: #1e293b; text-decoration: none;" onmouseover="this.style.color='#0284c7';" onmouseout="this.style.color='#1e293b';"><?= _ent($prod->title); ?></a>
                                           <?php else: ?>
                                              <?= _ent($prod->title); ?>
                                           <?php endif; ?>
                                        </div>
                                        <?php if (!empty($prod->product_id)): ?>
                                           <div style="font-size: 11px; color: #94a3b8;">ID: <?= _ent($prod->product_id); ?></div>
                                        <?php endif; ?>
                                     </td>
                                     <td style="vertical-align: middle;">
                                        <span style="color: #334155; font-weight: 500;">
                                           <?= _ent($prod->shop_name ?: "-"); ?>
                                        </span>
                                     </td>
                                     <td style="text-align: right; color: #334155; font-weight: 500; vertical-align: middle;">
                                        Rp <?= number_format($prod->price, 0, ",", "."); ?>
                                     </td>
                                     <td style="text-align: right; vertical-align: middle;">
                                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 600; font-size: 11px; padding: 4px 8px;">
                                           <?= number_format($prod->sold_count, 0, ",", "."); ?> sold
                                        </span>
                                     </td>
                                     <td style="text-align: right; font-weight: 700; color: #059669; vertical-align: middle;" title="Rp <?= number_format($prod->gmv_estimate, 0, ",", "."); ?>">
                                        <?= format_idr_friendly($prod->gmv_estimate); ?>
                                     </td>
                                     <td style="text-align: center; vertical-align: middle;">
                                        <?php if (!empty($prod->product_url)): ?>
                                           <a href="<?= _ent($prod->product_url); ?>" target="_blank" rel="noopener noreferrer" referrerpolicy="no-referrer" class="btn btn-xs btn-default" title="Buka Produk di TikTok Shop" style="border-radius: 4px; color: #0284c7;">
                                              <i class="fa fa-external-link"></i>
                                           </a>
                                        <?php else: ?>
                                           <span style="color: #94a3b8;">-</span>
                                        <?php endif; ?>
                                     </td>
                                  </tr>
                               <?php endforeach; ?>
                            <?php else: ?>
                               <tr>
                                  <td colspan="7" style="text-align: center; padding: 40px 20px; color: #64748b;">
                                     <div style="font-size: 32px; color: #cbd5e1; margin-bottom: 10px;">
                                        <i class="fa fa-shopping-basket"></i>
                                     </div>
                                     <div style="font-weight: 600; font-size: 14px; color: #475569; margin-bottom: 4px;">
                                        Belum ada data etalase produk TikTok Shop
                                     </div>
                                     <div style="font-size: 12px; color: #94a3b8; max-width: 450px; margin: 0 auto 16px auto;">
                                        Klik tombol <strong>Sync GMV (KeyAPI)</strong> di atas untuk mengambil data etalase dan GMV kreator secara otomatis.
                                     </div>
                                     <button type="button" class="btn btn-sm btn-default btn-sync-gmv" data-kol-id="<?= $kols->id ?>">
                                        <i class="fa fa-refresh text-green"></i> Ambil Data Sekarang
                                     </button>
                                  </td>
                               </tr>
                            <?php endif; ?>
                         </tbody>
                      </table>
                   </div>
                </div>
             </div>
</div>

         <div class="view-nav" style="padding: 15px 0;">
            <?php is_allowed('kols_update', function() use ($kols){?>
            <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit kols (Ctrl+e)" href="<?= site_url('administrator/kols/edit/'.$kols->id); ?>"><i class="fa fa-edit"></i> <?= cclang('update', ['Kols']); ?></a>
            <?php }) ?>
            <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kols/'); ?>"><i class="fa fa-undo"></i> <?= cclang('go_list_button', ['Kols']); ?></a>
         </div>

      </div>
   </div>
</section>

<script type="text/javascript">
(function($) {
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

    function showSyncAlert(type, title, message, details) {
        var iconType = type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'error');
        var btnColor = type === 'success' ? '#10b981' : (type === 'warning' ? '#f59e0b' : '#ef4444');

        var html = '<div style="text-align: left; font-size: 13px; line-height: 1.5; color: #334155; margin-top: 6px;">';
        
        if (message) {
            var bgBox = type === 'success' ? '#f0fdf4' : (type === 'warning' ? '#fffbeb' : '#fef2f2');
            var borderBox = type === 'success' ? '#22c55e' : (type === 'warning' ? '#f59e0b' : '#ef4444');
            var textColor = type === 'success' ? '#15803d' : (type === 'warning' ? '#b45309' : '#b91c1c');

            html += '<div style="background: ' + bgBox + '; border-left: 4px solid ' + borderBox + '; padding: 10px 14px; border-radius: 4px; margin-bottom: 12px;">' +
                        '<div style="color: ' + textColor + '; font-weight: 600;">' + message + '</div>' +
                    '</div>';
        }

        if (details && details.solution) {
            html += '<div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px 14px; border-radius: 4px; margin-bottom: 10px;">' +
                        '<strong style="color: #0f172a; display: block; margin-bottom: 4px;"><i class="fa fa-lightbulb-o text-yellow"></i> Solusi / Langkah Berikutnya:</strong>' +
                        '<div style="color: #475569;">' + details.solution + '</div>' +
                    '</div>';
        }

        if (details && details.tech) {
            html += '<div style="font-size: 11px; color: #64748b; background: #f1f5f9; padding: 6px 10px; border-radius: 4px; word-break: break-all;">' +
                        '<strong>Info Teknis:</strong> ' + details.tech +
                    '</div>';
        }

        html += '</div>';

        if (typeof swal === 'function') {
            swal({
                title: title,
                text: html,
                html: true,
                type: iconType,
                confirmButtonColor: btnColor,
                confirmButtonText: 'Tutup'
            });
        } else {
            var plain = title + '\n\n' + message;
            if (details && details.solution) plain += '\n\nSolusi: ' + details.solution.replace(/<[^>]+>/g, '');
            if (details && details.tech) plain += '\n\nTeknis: ' + details.tech;
            alert(plain);
        }
    }

    function handleSyncError(xhr, status, error, contextTitle) {
        var httpCode = (xhr && xhr.status) ? xhr.status : 0;
        var statusText = (xhr && xhr.statusText) ? xhr.statusText : (status || error || '');
        var title = contextTitle || 'Gagal Sinkronisasi Platform';
        var message = '';
        var solution = '';
        var tech = '';

        var rawText = '';
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
            rawText = xhr.responseJSON.message;
        } else if (xhr && xhr.responseText) {
            var stripped = xhr.responseText
                .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
                .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
            
            var h1 = stripped.match(/<h1[^>]*>([\s\S]*?)<\/h1>/i);
            var p = stripped.match(/<p[^>]*>([\s\S]*?)<\/p>/i);
            if (h1 || p) {
                rawText = (h1 ? h1[1].replace(/<[^>]+>/g, '').trim() + ' — ' : '') + 
                          (p ? p[1].replace(/<[^>]+>/g, '').trim() : '');
            } else {
                rawText = stripped.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
            }
        } else if (typeof error === 'string' && error.length > 0) {
            rawText = error;
        }

        var lower = (rawText + ' ' + statusText + ' ' + httpCode).toLowerCase();

        if (httpCode === 404 || lower.indexOf('404') !== -1 || lower.indexOf('not found') !== -1) {
            title = 'Endpoint Tidak Ditemukan (404 Not Found)';
            message = 'Rute atau endpoint sinkronisasi di server tidak ditemukan.';
            solution = 'Pastikan method <code>sync_platform_ajax</code> telah terdaftar di controller <code>Kols.php</code>.';
            tech = 'HTTP 404 pada URL: ' + (BASE_URL + 'administrator/kols/sync_platform_ajax');
        } else if (lower.indexOf('usage-limit-exceeded') !== -1 || lower.indexOf('usage limit') !== -1 || lower.indexOf('kredit / kuota apify telah habis') !== -1 || lower.indexOf('kredit') !== -1 || httpCode === 402) {
            title = 'Kredit / Kuota Apify Habis';
            message = 'Akun Apify Anda telah mencapai batas pemakaian bulanan (Monthly usage limit reached) atau saldo kredit free tier telah habis.';
            solution = 'Silakan isi ulang (top up) saldo kredit akun Apify Anda melalui <a href="https://console.apify.com/billing" target="_blank" style="color: #0284c7; font-weight: bold; text-decoration: underline;">console.apify.com/billing</a>, atau gunakan API Token lain.';
            tech = rawText.substring(0, 200) || 'Apify API HTTP 402: usage-limit-exceeded';
        } else if (lower.indexOf('keyapi') !== -1 || lower.indexOf('unauthorized') !== -1 || lower.indexOf('sk_live') !== -1 || lower.indexOf('kredit habis') !== -1) {
            title = 'Autentikasi KeyAPI Gagal';
            message = rawText || 'API Key KeyAPI tidak valid atau saldo kredit telah habis.';
            solution = 'Periksa atau perbarui API Key KeyAPI melalui menu <a href="' + BASE_URL + 'administrator/keyapi_config" target="_blank" style="color: #0284c7; font-weight: bold; text-decoration: underline;">Configuration &rarr; KeyAPI TikTok Shop</a>.';
            tech = 'KeyAPI Analytics Error';
        } else if (httpCode === 401 || lower.indexOf('401') !== -1 || lower.indexOf('unauthorized') !== -1 || lower.indexOf('token is missing') !== -1) {
            title = 'Autentikasi Apify Gagal (401 Unauthorized)';
            message = 'Token API Apify tidak valid atau belum diisi pada konfigurasi sistem.';
            solution = 'Periksa dan atur token Apify yang valid pada menu Configuration &rarr; Apify Settings atau file <code>config/apify.php</code>.';
            tech = rawText.substring(0, 200) || 'HTTP 401 Unauthorized';
        } else if (httpCode === 403 || lower.indexOf('csrf') !== -1 || lower.indexOf('akses ditolak') !== -1) {
            title = 'Akses Ditolak / Sesi Kedaluwarsa (403)';
            message = 'Sesi login Anda atau token keamanan CSRF telah kedaluwarsa.';
            solution = 'Silakan refresh halaman ini (tekan F5) untuk memperbarui token keamanan CSRF lalu coba lagi.';
            tech = 'HTTP 403 Forbidden';
        } else if (status === 'timeout' || httpCode === 504) {
            title = 'Waktu Tunggu Habis (Request Timeout)';
            message = 'Server Apify membutuhkan waktu lebih dari 120 detik untuk mengumpulkan data platform ini.';
            solution = 'Coba sinkronkan kembali beberapa saat lagi. Jika akun memiliki puluhan ribu postingan, scraping cloud memerlukan waktu lebih lama.';
            tech = 'AJAX Timeout (120s)';
        } else if (httpCode === 500) {
            title = 'Kesalahan Server Internal (500 Server Error)';
            message = 'Terjadi kesalahan pada skrip PHP server saat memproses data.';
            solution = 'Silakan hubungi tim developer atau periksa log error PHP Apache.';
            tech = rawText.substring(0, 250) || 'HTTP 500 Internal Server Error';
        } else if (httpCode === 0) {
            title = 'Koneksi Terputus';
            message = 'Tidak dapat menghubungi server lokal atau koneksi internet terputus.';
            solution = 'Pastikan server Apache / Laragon sedang berjalan dan koneksi internet Anda aktif.';
            tech = 'Network Error / ReadyState: ' + (xhr ? xhr.readyState : 0);
        } else {
            message = rawText || 'Terjadi kesalahan yang tidak terduga saat menghubungi server.';
            solution = 'Silakan coba kembali dalam beberapa saat.';
            tech = (httpCode ? 'HTTP ' + httpCode + ': ' : '') + statusText;
        }

        showSyncAlert('error', title, message, { solution: solution, tech: tech });
    }

    // 1. Single Platform Sync
    $(document).on('click', '.btn-sync-single-platform', function() {
        var $btn = $(this);
        var platformId = $btn.data('platform-id');
        var platName   = $btn.data('platform-name');
        var username   = $btn.data('username');
        var kpId       = $btn.data('kp-id');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Syncing...');

        var postData = {
            platform_id: platformId,
            username: username,
            kol_platform_id: kpId
        };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/kols/sync_platform_ajax',
            type: 'POST',
            data: postData,
            dataType: 'json',
            timeout: 120000,
            success: function(res) {
                if (res.csrf_hash) {
                    csrfHash = res.csrf_hash;
                    $('#kol-view-csrf-token').val(csrfHash);
                }

                if (res.success && res.data) {
                    var d = res.data;
                    var followersFormatted = Number(d.followers).toLocaleString('id-ID');
                    var erPercent = (Number(d.avg_er) * 100).toFixed(2);
                    
                    $('#platform-stats-' + kpId).html(
                        '<strong class="plat-followers">' + followersFormatted + '</strong> followers | <strong class="plat-er">' + erPercent + '%</strong> ER'
                    );

                    showSyncAlert(
                        'success',
                        'Sinkronisasi Berhasil!',
                        'Data platform <b>' + platName + ' (@' + username + ')</b> berhasil disinkronkan via Apify.',
                        {
                            solution: '<div style="margin-top: 5px;">' +
                                '<span class="label label-info" style="font-size: 11px; padding: 3px 8px; margin-right: 5px;">Followers: ' + followersFormatted + '</span> ' +
                                '<span class="label label-primary" style="font-size: 11px; padding: 3px 8px; margin-right: 5px;">Avg ER: ' + erPercent + '%</span> ' +
                                '<span class="label label-success" style="font-size: 11px; padding: 3px 8px;">Endorse Ratio: ' + (d.endorse_ratio || 0) + '%</span>' +
                            '</div>'
                        }
                    );
                } else {
                    handleSyncError(null, null, res.message || 'Respons server tidak valid.', 'Gagal Sinkronisasi ' + platName);
                }
            },
            error: function(xhr, status, error) {
                handleSyncError(xhr, status, error, 'Gagal Sinkronisasi ' + platName);
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-refresh text-blue"></i> Sync');
            }
        });
    });

    // 2. TikTok Shop GMV Sync (KeyAPI)
    $(document).on('click', '.btn-sync-gmv', function() {
        var $btn = $(this);
        var kolId = $btn.data('kol-id') || '<?= $kols->id ?>';

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Syncing GMV...');

        var postData = {
            kol_id: kolId
        };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/kols/sync_gmv_ajax/' + kolId,
            type: 'POST',
            data: postData,
            dataType: 'json',
            timeout: 120000,
            success: function(res) {
                if (res.csrf_hash || res.csrf_token) {
                    csrfHash = res.csrf_hash || res.csrf_token;
                    $('#kol-view-csrf-token').val(csrfHash);
                }

                if (res.status === 'OK') {
                    showSyncAlert(
                        'success',
                        'Sinkronisasi GMV Berhasil!',
                        res.message || ('Berhasil mengimpor ' + (res.count || 0) + ' produk etalase TikTok Shop.'),
                        {
                            solution: 'Halaman akan diperbarui otomatis dalam 2 detik untuk menampilkan data terbaru.'
                        }
                    );
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else if (res.status === 'SKIP') {
                    showSyncAlert(
                        'success',
                        'Data Masih Segar (Cache)',
                        res.message || 'Data snapshot KeyAPI belum kedaluwarsa (cache masih berlaku).',
                        {
                            solution: 'Data tidak perlu ditarik ulang karena baru saja disinkronkan.'
                        }
                    );
                } else if (res.status === 'EMPTY') {
                    showSyncAlert(
                        'warning',
                        'Etalase Kosong / Disembunyikan',
                        res.message || 'Akun kreator terdeteksi namun tidak ada produk yang ditampilkan di etalase TikTok Shop.',
                        {
                            solution: 'Kreator mungkin belum menambahkan produk afiliasi atau etalase disetel private.'
                        }
                    );
                    setTimeout(function() {
                        location.reload();
                    }, 2500);
                } else {
                    handleSyncError(null, null, res.message || 'Gagal sinkronisasi GMV via KeyAPI.', 'Gagal Sinkronisasi TikTok Shop');
                }
            },
            error: function(xhr, status, error) {
                handleSyncError(xhr, status, error, 'Gagal Sinkronisasi TikTok Shop');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sync GMV (KeyAPI)');
            }
        });
    });

})(jQuery);
</script>
