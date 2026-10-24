
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
.kol-header-actions { text-align: right; }

.metric-summary-row { display: flex; gap: 15px; margin-bottom: 20px; }
.metric-summary-card {
    flex: 1;
    min-width: 0;
    background: #fff;
    border: 1px solid #dfe6ee;
    border-radius: 6px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    min-height: 104px;
    box-sizing: border-box;
}
.metric-label {
    font-size: 11px;
    text-transform: uppercase;
    color: #64748b;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.metric-value {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 4px;
}
.metric-sub {
    font-size: 11px;
    color: #94a3b8;
    margin-top: auto;
    padding-top: 10px;
    display: flex;
    align-items: center;
    min-height: 24px;
}

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

/* Status Badges */
.badge-status { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; line-height: 1.2; white-space: nowrap; }
.badge-danger-soft { background: #fee2e2; color: #b91c1c; }
.badge-warning-soft { background: #fef3c7; color: #b45309; }
.badge-success-soft { background: #dcfce7; color: #15803d; }
.badge-default-soft { background: #f1f5f9; color: #475569; }
</style>
<style>
/* ── Same styles as Recommendations Generator ──────────────── */
.rekom-result-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    height: 100%;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.rekom-result-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 14px rgba(37,99,235,0.10);
    transform: translateY(-2px);
}
.rekom-avatar-initials {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1f4b6e 0%, #2f80b5 100%);
    color: #ffffff;
    font-size: 16px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.rekom-avatar-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #cbd5e1;
    flex-shrink: 0;
}
.rekom-avatar-container {
    position: relative;
}
.rekom-match-score {
    position: absolute;
    top: 16px;
    right: 16px;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    padding: 4px 10px;
    border-radius: 16px;
    font-weight: 700;
    font-size: 12px;
}
.rekom-metrics-grid {
    background: #ffffff;
    border-radius: 6px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    font-size: 11px;
    margin: 12px 0;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.rekom-metric-item {
    display: flex;
    flex-direction: column;
    padding: 10px;
    border-bottom: 1px solid #e2e8f0;
}
.rekom-metric-item:nth-child(odd) {
    border-right: 1px solid #e2e8f0;
}
.rekom-metric-item:nth-child(3), .rekom-metric-item:nth-child(4) {
    border-bottom: none;
}
.rekom-metric-label {
    color: #64748b;
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.rekom-metric-value {
    color: #0f172a;
    font-weight: 700;
    font-size: 13px;
    margin-top: 2px;
    font-variant-numeric: tabular-nums;
}
.btn-ghost {
    background: transparent;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.btn-ghost:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}
</style>


<!-- Content Header -->
<section class="content-header">
   <h1>Detail KOL <small><?= cclang('detail', ['Kols']); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="#">Home</a></li>
      <li><a href="<?= site_url('administrator/kols'); ?>">Kols</a></li>
      <li class="active"><?= cclang('detail'); ?></li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         
         <!-- Hidden CSRF token -->
         <input type="hidden" id="kol-view-csrf-token" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

         <?php
            $kol_name  = $kols->name ?? 'KOL';
            $initials  = strtoupper(mb_substr($kol_name, 0, 1) . (mb_substr(trim(strstr($kol_name, ' ')), 0, 1) ?: mb_substr($kol_name, 1, 1)));
            $kol_er_pct = $kol_er !== null ? number_format($kol_er * 100, 2) : null;
            $benchmark_er_pct = $benchmark_er !== null ? number_format($benchmark_er * 100, 2) : null;
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
                     <span class="badge-status badge-danger-soft">Blacklisted</span>
                  <?php endif; ?>
                  <?php if ($anomaly_score !== null): ?>
                     <?php 
                        $risk_class = 'badge-default-soft';
                        if ($risk_level === 'CRITICAL') $risk_class = 'badge-danger-soft';
                        if ($risk_level === 'HIGH') $risk_class = 'badge-warning-soft';
                        if ($risk_level === 'LOW') $risk_class = 'badge-success-soft';
                     ?>
                     <span class="badge-status <?= $risk_class ?>"><?= $risk_level ?> Risk</span>
                  <?php endif; ?>
               </div>
            </div>
            <div class="kol-header-actions">
               <?php is_allowed('kols_update', function() use ($kols){ ?>
                  <?php if (!empty($kols->is_blacklisted)): ?>
                     <button type="button" class="btn btn-sm btn-default btn-toggle-blacklist-view" data-id="<?= $kols->id ?>" data-name="<?= _ent($kols->name) ?>" data-blacklisted="1">
                        Whitelist
                     </button>
                  <?php else: ?>
                     <button type="button" class="btn btn-sm btn-default btn-toggle-blacklist-view" data-id="<?= $kols->id ?>" data-name="<?= _ent($kols->name) ?>" data-blacklisted="0" style="color: #dc2626; border-color: #fca5a5;">
                        Blacklist
                     </button>
                  <?php endif; ?>
               <?php }); ?>
            </div>
         </div>

         <!-- PERFORMANCE SUMMARY -->
         <div class="metric-summary-row">
            <!-- ER -->
            <div class="metric-summary-card">
               <div class="metric-label">Engagement Rate</div>
               <div class="metric-value"><?= $kol_er_pct !== null ? $kol_er_pct . '%' : '-'; ?></div>
               <div class="metric-sub"><?= $benchmark_badge; ?></div>
            </div>
            <!-- Anomaly Detection -->
            <div class="metric-summary-card">
               <div class="metric-label">Anomaly Detection</div>
               <div class="metric-value"><?= $anomaly_score !== null ? $anomaly_score . '<span style="font-size:13px; font-weight:normal; color:#94a3b8;">/100</span>' : '-'; ?></div>
               <div class="metric-sub">
                  <?php if ($anomaly_score !== null): ?>
                     <?php if ((float)$anomaly_score <= 40): ?>
                        <span class="badge-status badge-success-soft">Low Risk</span>
                     <?php elseif ((float)$anomaly_score <= 70): ?>
                        <span class="badge-status badge-warning-soft">Moderate Risk</span>
                     <?php else: ?>
                        <span class="badge-status badge-danger-soft">High Risk</span>
                     <?php endif; ?>
                  <?php else: ?>
                     <span class="badge-status badge-default-soft">No Score Data</span>
                  <?php endif; ?>
               </div>
            </div>
            <!-- Internal Rating -->
            <div class="metric-summary-card">
               <div class="metric-label">Internal Rating</div>
               <div class="metric-value">
                  <?= $review_summary['avg_overall'] > 0 ? number_format($review_summary['avg_overall'], 1) . ' <span style="color:#f59e0b; font-size:16px;">★</span>' : '-'; ?> 
               </div>
               <div class="metric-sub">
                  <?php if ($review_summary['count'] > 0): ?>
                     <?php $r_class = $review_summary['avg_overall'] >= 3.5 ? 'badge-success-soft' : ($review_summary['avg_overall'] >= 2.5 ? 'badge-warning-soft' : 'badge-danger-soft'); ?>
                     <span class="badge-status <?= $r_class; ?>"><?= $review_summary['count']; ?> <?= $review_summary['count'] == 1 ? 'Review' : 'Reviews'; ?></span>
                  <?php else: ?>
                     <span class="badge-status badge-default-soft">No Reviews</span>
                  <?php endif; ?>
               </div>
            </div>
            
            <!-- GMV -->
            <div class="metric-summary-card">
               <div class="metric-label" style="color: #0284c7;">Total GMV (All-Time)</div>
               <div class="metric-value">
                  <span style="font-size:14px; font-weight:normal; margin-right:2px; color:#475569;">Rp</span><?= number_format(isset($kols->total_gmv_estimate) && $kols->total_gmv_estimate > 0 ? $kols->total_gmv_estimate : (isset($gmv_summary->total_gmv) ? $gmv_summary->total_gmv : 0), 0, ',', '.'); ?>
               </div>
               <div class="metric-sub">
                  <span class="badge-status" style="background:#000; color:#fff;">TikTok Shop</span>
               </div>
            </div>
         </div>

         <!-- TABS -->
         <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
               <li class="active"><a href="#tab_overview" data-toggle="tab">Overview</a></li>
               <li><a href="#tab_social" data-toggle="tab">Social Media</a></li>
               <li><a href="#tab_performance" data-toggle="tab">Performance</a></li>
               <li><a href="#tab_products" data-toggle="tab">Affiliate Products</a></li>
               <li><a href="#tab_documents" data-toggle="tab">Documents</a></li>
               <li><a href="#tab_reviews" data-toggle="tab">Internal Reviews</a></li>
               <li><a href="#tab_lookalikes" data-toggle="tab">Similar Lookalikes <span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:11px; margin-left:4px;"><?= !empty($similar_kols) ? count($similar_kols) : 0; ?></span></a></li>
            </ul>
         </div>

         <div class="tab-content">
            
            <!-- TAB OVERVIEW -->
            <div class="tab-pane active" id="tab_overview">
               <div class="row">
                  <div class="col-md-6">
                     <h4 class="section-title">Profile Information</h4>
                     <dl class="dl-horizontal">
                        <dt>Phone</dt><dd><?= _ent($kols->phone) ?: '-'; ?></dd>
                        <dt>Email</dt><dd><?= _ent($kols->email) ?: '-'; ?></dd>
                        <dt>Date of Birth</dt><dd><?= !empty($kols->date_of_birth) ? date('d M Y', strtotime($kols->date_of_birth)) : '-'; ?></dd>
                        <dt>Gender</dt><dd><?= _ent($kols->gender) ?: '-'; ?></dd>
                        <dt>Address</dt><dd><?= _ent($kols->address) ?: '-'; ?></dd>
                        <dt>Category</dt>
                        <dd>
                           <?php if (!empty($kol_categories)): ?>
                              <?= implode(', ', array_map(function($c) { return _ent($c->category_name); }, $kol_categories)); ?>
                           <?php else: ?>
                              -
                           <?php endif; ?>
                        </dd>
                     </dl>
                  </div>
                  <div class="col-md-6">
                     <h4 class="section-title">Commercial / Financial</h4>
                     <dl class="dl-horizontal">
                        <dt>Bank Name</dt><dd><?= _ent($kols->bank_name) ?: '-'; ?></dd>
                        <dt>Account Number</dt><dd><?= _ent($kols->bank_account_number) ?: '-'; ?></dd>
                        <dt>Account Name</dt><dd><?= _ent($kols->bank_account_name) ?: '-'; ?></dd>
                        <dt>Approval Note</dt><dd><?= isset($kols->approval_note) ? _ent($kols->approval_note) : '-'; ?></dd>
                        <dt>Created By</dt><dd><?= _ent(!empty($kols->created_by_name) ? $kols->created_by_name : ($kols->created_by ?? '-')); ?></dd>
                        <dt>Approved By</dt><dd><?= isset($kols->approved_by_name) ? _ent($kols->approved_by_name) : (isset($kols->approved_by) ? _ent($kols->approved_by) : '-'); ?></dd>
                     </dl>
                  </div>
               </div>
            </div>

            <!-- TAB SOCIAL MEDIA -->
            <div class="tab-pane" id="tab_social">
               <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                  <h4 class="section-title" style="margin:0; border:none;">Connected Accounts</h4>
                  <button type="button" class="btn btn-sm btn-default" id="btn-sync-apify" data-kol-id="<?= $kols->id ?>">
                     Sync All (Apify)
                  </button>
               </div>
               
               <div class="row">
               <?php if (!empty($kol_platforms)): ?>
                  <?php foreach ($kol_platforms as $kp): ?>
                     <div class="col-md-6">
                        <div class="social-account-card">
                           <div class="social-account-info">
                              <?php if (!empty($kp->icon_url)): ?>
                                 <img src="<?= $kp->icon_url ?>" class="social-icon" alt="<?= _ent($kp->platform_name); ?>" />
                              <?php else: ?>
                                 <div class="social-icon" style="background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#64748b; font-weight:700; font-size:12px;"><?= strtoupper(substr($kp->platform_name ?? 'P', 0, 1)); ?></div>
                              <?php endif; ?>
                              <div>
                                 <div style="font-weight: 600; font-size: 14px; color: #1e293b;">
                                    <a href="<?= _ent(get_kol_profile_url($kp->profile_url, $kp->username, $kp->platform_name)); ?>" target="_blank" style="color:inherit;">@<?= _ent($kp->username); ?></a>
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
                              Sync
                           </button>
                        </div>
                     </div>
                  <?php endforeach; ?>
               <?php else: ?>
                  <div class="col-md-12 text-muted">Belum ada akun platform / media sosial yang terhubung.</div>
               <?php endif; ?>
               </div>
            </div>

            <!-- TAB PERFORMANCE -->
            <div class="tab-pane" id="tab_performance">
               <div class="row">
                  <div class="col-md-6">
                     <h4 class="section-title" style="margin-bottom:15px;">Anomaly Detection Details</h4>
                     <?php if ($anomaly_score !== null): ?>
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:20px; margin-bottom:20px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                           <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                              <div>
                                 <span style="font-size:12px; color:#64748b; text-transform:uppercase; font-weight:600; letter-spacing:0.5px;">Total Anomaly Score</span>
                                 <div style="font-size:26px; font-weight:800; color:#1e293b; line-height:1.2;"><?= $anomaly_score ?> <small style="font-size:14px; font-weight:normal; color:#94a3b8;">/ 100</small></div>
                              </div>
                              <div>
                                 <?php 
                                    $rl_class = 'badge-default';
                                    if ($risk_level == 'LOW') $rl_class = 'badge-success';
                                    if ($risk_level == 'HIGH') $rl_class = 'badge-warning';
                                    if ($risk_level == 'CRITICAL') $rl_class = 'badge-danger';
                                 ?>
                                 <span class="badge <?= $rl_class ?>" style="font-size:13px; padding:6px 12px; border-radius:6px;"><?= $risk_level ?> Risk</span>
                              </div>
                           </div>
                           
                           <!-- Progress Bars -->
                           <div style="margin-bottom:16px;">
                              <div style="display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:6px;">
                                 <span style="color:#475569; font-weight:500;">Engagement Rate Component</span>
                                 <strong style="color:#1e293b;"><?= $er_score ?> pts</strong>
                              </div>
                              <div class="progress" style="height:8px; margin-bottom:0; box-shadow:none; background:#e2e8f0; border-radius:4px;">
                                 <div class="progress-bar <?= $er_score > 20 ? 'progress-bar-warning' : 'progress-bar-success' ?>" role="progressbar" style="width: <?= min(100, $er_score) ?>%; border-radius:4px;"></div>
                              </div>
                           </div>
                           <div style="margin-bottom:16px;">
                              <div style="display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:6px;">
                                 <span style="color:#475569; font-weight:500;">View Ratio Component</span>
                                 <strong style="color:#1e293b;"><?= $view_ratio_score ?> pts</strong>
                              </div>
                              <div class="progress" style="height:8px; margin-bottom:0; box-shadow:none; background:#e2e8f0; border-radius:4px;">
                                 <div class="progress-bar <?= $view_ratio_score > 20 ? 'progress-bar-warning' : 'progress-bar-success' ?>" role="progressbar" style="width: <?= min(100, $view_ratio_score) ?>%; border-radius:4px;"></div>
                              </div>
                           </div>
                           <div>
                              <div style="display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:6px;">
                                 <span style="color:#475569; font-weight:500;">Growth Component</span>
                                 <strong style="color:#1e293b;"><?= $growth_score ?> pts</strong>
                              </div>
                              <div class="progress" style="height:8px; margin-bottom:0; box-shadow:none; background:#e2e8f0; border-radius:4px;">
                                 <div class="progress-bar <?= $growth_score > 10 ? 'progress-bar-warning' : 'progress-bar-success' ?>" role="progressbar" style="width: <?= min(100, $growth_score) ?>%; border-radius:4px;"></div>
                              </div>
                           </div>
                        </div>
                     <?php else: ?>
                        <p class="text-muted" style="font-size:13px; font-style:italic;">Data Anomaly Detection belum tersedia untuk KOL ini.</p>
                     <?php endif; ?>
                  </div>
                  <div class="col-md-6">
                     <h4 class="section-title" style="margin-bottom:15px;">Engagement Trend (Last 5)</h4>
                     <?php if (!empty($er_trend)): ?>
                        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:16px; margin-bottom:20px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                           
                           <!-- Mini Chart Container -->
                           <div style="position:relative; height:140px; margin-bottom:15px;">
                              <canvas id="erTrendChart"></canvas>
                           </div>
                           
                           <!-- Historical List -->
                           <div style="border-top:1px dashed #e2e8f0; padding-top:12px;">
                              <ul class="list-unstyled" style="margin-bottom:0;">
                                 <?php foreach ($er_trend as $et): ?>
                                    <li style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #f8fafc; font-size:13px;">
                                       <span style="color:#64748b;"><?= date('d M Y', strtotime($et->snapshot_at)); ?></span>
                                       <strong style="color:#0f172a; background:#f1f5f9; padding:2px 8px; border-radius:4px; font-family:monospace; font-size:12px;"><?= number_format((float)($et->engagement_rate ?? 0) * 100, 2); ?>%</strong>
                                    </li>
                                 <?php endforeach; ?>
                              </ul>
                           </div>

                        </div>
                     <?php else: ?>
                        <p class="text-muted" style="font-size:13px; font-style:italic;">Belum ada data metrik trend historis yang direkam.</p>
                     <?php endif; ?>
                  </div>
               </div>
            </div>

            <!-- TAB PRODUCTS -->
            <div class="tab-pane" id="tab_products">
               <div style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; background: #fff;">
                  <div style="padding: 12px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                     <strong style="color: #1e293b; font-size: 13px;">Top Produk Etalase TikTok Shop (All-Time GMV)</strong>
                     <span style="font-size: 12px; color: #64748b;">Maks. 10 produk teratas</span>
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
                                           <?= _ent(isset($prod->shop_name) ? $prod->shop_name : "-"); ?>
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
                                  </td>
                               </tr>
                            <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>

            <!-- TAB DOCUMENTS -->
            <div class="tab-pane" id="tab_documents">
               <h4 class="section-title">Identity & Profile Documents</h4>
               <div style="margin-bottom: 20px;">
                  <?php if (!empty($kols->profile_url)): ?>
                     <?php if (is_image($kols->profile_url)): ?>
                        <a class="fancybox doc-link" rel="group" href="<?= BASE_URL . 'uploads/kols/' . $kols->profile_url; ?>">
                           Foto Profil
                        </a>
                     <?php else: ?>
                        <a href="<?= BASE_URL . 'administrator/file/download/kols/' . $kols->profile_url; ?>" class="doc-link">
                           File Profil
                        </a>
                     <?php endif; ?>
                  <?php endif; ?>
                  
                  <?php if (!empty($kols->ktp_number)): ?>
                     <div style="margin: 10px 0;"><strong>No. KTP:</strong> <?= _ent($kols->ktp_number); ?></div>
                  <?php endif; ?>
                  <?php if (!empty($kols->ktp_file)): ?>
                     <a href="<?= BASE_URL . 'administrator/file/download/kols/' . $kols->ktp_file; ?>" class="doc-link">
                        File KTP
                     </a>
                  <?php endif; ?>
                  
                  <?php if (!empty($kols->npwp_number)): ?>
                     <div style="margin: 10px 0;"><strong>No. NPWP:</strong> <?= _ent($kols->npwp_number); ?></div>
                  <?php endif; ?>
                  <?php if (!empty($kols->npwp_file)): ?>
                     <a href="<?= BASE_URL . 'administrator/file/download/kols/' . $kols->npwp_file; ?>" class="doc-link">
                        File NPWP
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
                                 <?= $filename ?>
                              </a>
                           <?php else: ?>
                              <a href="<?= BASE_URL . 'administrator/file/download/kols/' . $filename; ?>" class="doc-link">
                                 <?= $filename ?>
                              </a>
                           <?php endif; ?>
                        <?php endif; ?>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <p class="text-muted">Tidak ada rate card.</p>
                  <?php endif; ?>
               </div>
            </div>

            <!-- TAB INTERNAL REVIEWS -->
            <div class="tab-pane" id="tab_reviews">
               <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                  <h4 class="section-title" style="margin:0; border:none;">Review & Evaluasi Performa</h4>
                  <?php is_allowed('kols_update', function() use ($kols){ ?>
                     <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-add-review">
                        Tulis Review
                     </button>
                  <?php }); ?>
               </div>

               <div class="row" style="margin-bottom: 20px;">
                  <div class="col-md-3 col-sm-6">
                     <div class="metric-summary-card">
                        <div class="metric-label">Overall Rating</div>
                        <div class="metric-value"><?= $review_summary['avg_overall'] > 0 ? number_format($review_summary['avg_overall'], 1) . ' ' : 'N/A'; ?></div>
                        <div class="metric-sub"><?= $review_summary['count']; ?> ulasan</div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                     <div class="metric-summary-card">
                        <div class="metric-label">Komunikasi</div>
                        <div class="metric-value"><?= $review_summary['avg_comm'] > 0 ? number_format($review_summary['avg_comm'], 1) : '-'; ?></div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                     <div class="metric-summary-card">
                        <div class="metric-label">Deadline</div>
                        <div class="metric-value"><?= $review_summary['avg_dead'] > 0 ? number_format($review_summary['avg_dead'], 1) : '-'; ?></div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                     <div class="metric-summary-card">
                        <div class="metric-label">Kualitas</div>
                        <div class="metric-value"><?= $review_summary['avg_qual'] > 0 ? number_format($review_summary['avg_qual'], 1) : '-'; ?></div>
                     </div>
                  </div>
               </div>

               <?php if (!empty($reviews)): ?>
                  <div class="table-responsive">
                     <table class="table table-striped table-bordered" style="font-size: 13px;">
                        <thead>
                           <tr>
                              <th width="120">Tanggal</th>
                              <th width="150">Reviewer</th>
                              <th>Campaign</th>
                              <th width="80" class="text-center">Komm</th>
                              <th width="80" class="text-center">DL</th>
                              <th width="80" class="text-center">Kual</th>
                              <th width="80" class="text-center">Total</th>
                              <th>Catatan</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($reviews as $rev): ?>
                              <tr>
                                 <td><?= date('d M Y', strtotime($rev->created_at)); ?></td>
                                 <td><?= _ent($rev->reviewer_name ?: $rev->reviewer_username ?: 'User #' . $rev->reviewed_by); ?></td>
                                 <td><?= !empty($rev->campaign_name) ? _ent($rev->campaign_name) : '<i>Umum</i>'; ?></td>
                                 <td class="text-center"><?= $rev->communication_rating; ?></td>
                                 <td class="text-center"><?= $rev->deadline_adherence_rating; ?></td>
                                 <td class="text-center"><?= $rev->content_quality_rating; ?></td>
                                 <td class="text-center"><strong><?= number_format($rev->overall_rating, 1); ?></strong> </td>
                                 <td><?= !empty($rev->review_notes) ? nl2br(_ent($rev->review_notes)) : '-'; ?></td>
                              </tr>
                           <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>
               <?php else: ?>
                  <div class="text-muted text-center" style="padding: 30px 0;">Belum ada review internal.</div>
               <?php endif; ?>
            </div>
            <!-- TAB LOOKALIKES -->
            <div class="tab-pane" id="tab_lookalikes">
               <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                  <div>
                     <h4 class="section-title" style="margin: 0; border: none;">Similar KOL Recommendations (Lookalikes)</h4>
                     <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                        Matched using Hybrid Jaccard category similarity, audience scale, and performance metrics
                     </div>
                  </div>
                  <div>
                     <a href="<?= site_url('administrator/kols/rekomendasi?baseline_kol_id=' . $kols->id); ?>" class="btn btn-sm btn-default btn-flat" style="border-radius: 4px; font-weight: 600; color: #1f4b6e; border-color: #cbd5e1;">
                        Open in Full Generator
                     </a>
                  </div>
               </div>

               <?php if (!empty($similar_kols)): ?>
                  <div class="row" style="display: flex; flex-wrap: wrap;">
                     <?php foreach ($similar_kols as $sim): ?>
                        <?php
                           $sim_name = $sim->name ?? 'KOL';
                           $sim_initials = strtoupper(
                               mb_substr($sim_name, 0, 1) .
                               (mb_substr(trim(strstr($sim_name, ' ')), 0, 1) ?: mb_substr($sim_name, 1, 1))
                           );
                           $match = $sim->match_percentage ?? 50;
                           $match_badge_class = ($match >= 80) ? 'label-success' : (($match >= 65) ? 'label-info' : 'label-default');
                           $breakdown_title = "Category: {$sim->breakdown['category_match_pct']}% | Platform: {$sim->breakdown['platform_match_pct']}% | Audience Scale: {$sim->breakdown['audience_match_pct']}% | Performance: {$sim->breakdown['performance_match_pct']}%";
                        ?>
                        <div class="col-md-3 col-sm-6 col-xs-12" style="display: flex; flex-direction: column; margin-bottom: 16px;">
                           <div class="rekom-result-card">
                              <div>
                                 <!-- Match Score Top Right -->
                                 <div class="rekom-match-score" data-toggle="tooltip" title="<?= htmlspecialchars($breakdown_title); ?>">
                                    <?= $match; ?>% Match
                                 </div>

                                 <!-- Creator Header -->
                                 <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding-right: 80px;">
                                    <div class="rekom-avatar-container">
                                       <?php if (!empty($sim->avatar_url)): ?>
                                          <img src="<?= htmlspecialchars($sim->avatar_url); ?>" alt="<?= htmlspecialchars($sim_name); ?>" class="rekom-avatar-img" />
                                       <?php else: ?>
                                          <div class="rekom-avatar-initials"><?= htmlspecialchars($sim_initials); ?></div>
                                       <?php endif; ?>
                                    </div>

                                    <div style="flex: 1; min-width: 0;">
                                       <a href="<?= site_url('administrator/kols/view/' . $sim->id); ?>" style="font-weight: 700; color: #1e293b; font-size: 15px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($sim_name); ?>">
                                          <?= htmlspecialchars($sim_name); ?>
                                       </a>
                                       <div style="display: flex; gap: 6px; align-items: center; margin-top: 3px; flex-wrap: wrap;">
                                          <span class="label label-primary" style="font-size: 10px; font-weight: 600; padding: 2px 6px;">
                                             <?= htmlspecialchars($sim->tier_name ?? 'Standard'); ?>
                                          </span>
                                          <?php if (!empty($sim->categories) && isset($sim->categories[0])): ?>
                                             <span class="label" style="background: #f1f5f9; color: #334155; font-size: 10px; border: 1px solid #e2e8f0; border-radius: 3px;">
                                                <?= htmlspecialchars($sim->categories[0]); ?>
                                             </span>
                                          <?php endif; ?>
                                       </div>
                                    </div>
                                 </div>

                                 <!-- Metrics 2x2 Grid -->
                                 <div class="rekom-metrics-grid">
                                    <div class="rekom-metric-item">
                                       <span class="rekom-metric-label">Followers</span>
                                       <span class="rekom-metric-value"><?= number_format($sim->breakdown['total_followers']); ?></span>
                                    </div>
                                    <div class="rekom-metric-item">
                                       <span class="rekom-metric-label">Avg ER</span>
                                       <span class="rekom-metric-value"><?= number_format($sim->breakdown['avg_er'] * 100, 2); ?>%</span>
                                    </div>
                                    <div class="rekom-metric-item">
                                       <span class="rekom-metric-label">Avg Views</span>
                                       <span class="rekom-metric-value"><?= number_format($sim->breakdown['avg_views'] ?? 0); ?></span>
                                    </div>
                                    <div class="rekom-metric-item">
                                       <span class="rekom-metric-label">Rating</span>
                                       <span class="rekom-metric-value">
                                          <?= !empty($sim->review_summary['rating']) ? number_format($sim->review_summary['rating'], 1) . ' ★' : '-'; ?>
                                       </span>
                                    </div>
                                 </div>
                              </div>

                              <!-- Action Buttons -->
                              <div style="display: flex; gap: 8px; white-space: nowrap; margin-top: 12px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                                 <a href="<?= site_url('administrator/kols/view/' . $sim->id); ?>" class="btn btn-sm btn-ghost btn-flat" style="flex: 1; border-radius: 4px; font-weight: 600; text-align: center;">
                                    View
                                 </a>
                                 <button type="button" class="btn btn-sm btn-primary btn-flat btn-trigger-assign-single" 
                                         data-id="<?= $sim->id; ?>" 
                                         data-name="<?= htmlspecialchars($sim_name); ?>" 
                                         style="flex: 1.5; border-radius: 4px; font-weight: 600; background: #2563eb; border-color: #1d4ed8;">
                                    Invite
                                 </button>
                              </div>
                           </div>
                        </div>
                     <?php endforeach; ?>
                  </div>
               <?php else: ?>
                  <div style="text-align: center; padding: 36px 15px; background: #f8fafc; border-radius: 6px; border: 1px dashed #cbd5e1;">
                     
                     <h5 style="color: #475569; font-weight: 600; margin-bottom: 4px;">No Similar KOLs Found</h5>
                     <p style="color: #94a3b8; font-size: 12px; margin-bottom: 14px;">Try exploring the full generator to search across custom tiers and categories.</p>
                     <a href="<?= site_url('administrator/kols/rekomendasi'); ?>" class="btn btn-sm btn-primary btn-flat" style="border-radius: 4px;">
                        Open Recommendations Generator
                     </a>
                  </div>
               <?php endif; ?>
            </div>
         </div>
         
         <div class="view-nav" style="padding: 15px 0;">
            <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kols/'); ?>"><?= cclang('go_list_button', ['Kols']); ?></a>
         </div>

         <!-- Modal Add Review -->
         <div class="modal fade" id="modal-add-review" tabindex="-1" role="dialog" aria-labelledby="modalAddReviewLabel">
            <div class="modal-dialog" role="document">
               <div class="modal-content" style="border-radius: 4px; overflow: hidden;">
                  <form id="form-add-review" method="POST" action="<?= site_url('administrator/kols/add_review/' . $kols->id); ?>">
                     <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                     <div class="modal-header" style="background: #1e293b; color: #fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalAddReviewLabel" style="font-weight: 600;">
                           Tulis Review Performa KOL: <?= _ent($kols->name); ?>
                        </h4>
                     </div>
                     <div class="modal-body" style="padding: 20px;">
                        
                        <div class="form-group">
                           <label style="font-weight: 600; color: #334155;">Terkait Campaign (Opsional)</label>
                           <select class="form-control" name="campaign_kol_id" style="border-radius: 4px;">
                              <option value="">-- Tidak Terikat Campaign Tertentu (Umum) --</option>
                              <?php if (!empty($kol_campaigns)): ?>
                                 <?php foreach ($kol_campaigns as $kc): ?>
                                    <option value="<?= $kc->campaign_kol_id; ?>">
                                       <?= _ent($kc->campaign_name); ?>
                                    </option>
                                 <?php endforeach; ?>
                              <?php endif; ?>
                           </select>
                        </div>

                        <div class="row">
                           <div class="col-md-4 col-sm-12">
                              <div class="form-group">
                                 <label style="font-weight: 600; color: #334155;">Komunikasi (1-5)</label>
                                 <select class="form-control" name="communication_rating" required style="border-radius: 4px;">
                                    <option value="5">5 - Sangat Responsif</option>
                                    <option value="4" selected>4 - Responsif</option>
                                    <option value="3">3 - Cukup</option>
                                    <option value="2">2 - Lambat</option>
                                    <option value="1">1 - Ghosting/Sulit</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-4 col-sm-12">
                              <div class="form-group">
                                 <label style="font-weight: 600; color: #334155;">Deadline (1-5)</label>
                                 <select class="form-control" name="deadline_adherence_rating" required style="border-radius: 4px;">
                                    <option value="5">5 - Tepat Waktu</option>
                                    <option value="4" selected>4 - Toleran (1-2 hari)</option>
                                    <option value="3">3 - Terlambat Wajar</option>
                                    <option value="2">2 - Sangat Terlambat</option>
                                    <option value="1">1 - Melanggar Jadwal</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-4 col-sm-12">
                              <div class="form-group">
                                 <label style="font-weight: 600; color: #334155;">Kualitas (1-5)</label>
                                 <select class="form-control" name="content_quality_rating" required style="border-radius: 4px;">
                                    <option value="5">5 - Sangat Memuaskan</option>
                                    <option value="4" selected>4 - Bagus Sesuai Brief</option>
                                    <option value="3">3 - Standar</option>
                                    <option value="2">2 - Butuh Banyak Revisi</option>
                                    <option value="1">1 - Tidak Sesuai Brief</option>
                                 </select>
                              </div>
                           </div>
                        </div>

                        <div class="form-group">
                           <label style="font-weight: 600; color: #334155;">Catatan &amp; Evaluasi Tambahan</label>
                           <textarea class="form-control" name="review_notes" rows="4" placeholder="Tuliskan pengalaman kerja sama, kendala, atau keunggulan KOL ini..." style="border-radius: 4px;"></textarea>
                        </div>

                     </div>
                     <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 4px;">Batal</button>
                        <button type="submit" id="btn-submit-review" class="btn btn-primary btn-flat" style="border-radius: 4px; font-weight: 600; background-color: #2563eb; border-color: #1d4ed8;">
                           Simpan Review
                        </button>
                     </div>
                  </form>
               </div>
            </div>
         </div>


         <!-- Modal Quick Assign KOL to Campaign -->
         <div class="modal fade" id="modal-assign-kol-campaign" tabindex="-1" role="dialog" aria-labelledby="modalAssignKolCampaignLabel">
            <div class="modal-dialog" role="document">
               <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
                  <form id="form-assign-kol-campaign">
                     <input type="hidden" name="kol_id" id="assign-kol-id" value="">
                     <div class="modal-header" style="background: #1e293b; color: #fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalAssignKolCampaignLabel" style="font-weight: 600;">
                           Invite KOL to Campaign
                        </h4>
                     </div>
                     <div class="modal-body" style="padding: 20px;">
                        <div class="form-group">
                           <label style="font-weight: 600; color: #334155;">Selected KOL</label>
                           <input type="text" id="assign-kol-name" class="form-control" readonly style="background: #f8fafc; font-weight: 600;">
                        </div>

                        <div class="form-group">
                           <label style="font-weight: 600; color: #334155;">Target Campaign *</label>
                           <select class="form-control" name="campaign_id" id="assign-campaign-id" required style="border-radius: 4px;">
                              <option value="">-- Choose Active Campaign --</option>
                              <?php if (!empty($all_active_campaigns)): ?>
                                 <?php foreach ($all_active_campaigns as $ac): ?>
                                    <option value="<?= $ac->id; ?>">
                                       <?= htmlspecialchars($ac->name); ?> (<?= $ac->status; ?>)
                                    </option>
                                 <?php endforeach; ?>
                              <?php endif; ?>
                           </select>
                        </div>

                        <div class="form-group">
                           <label style="font-weight: 600; color: #334155;">Initial Dealing Status</label>
                           <select class="form-control" name="status" id="assign-deal-status" style="border-radius: 4px;">
                              <option value="NEGOTIATION" selected>NEGOTIATION (Drafting / Pitching)</option>
                              <option value="ASSIGNED">ASSIGNED (Deal Confirmed)</option>
                           </select>
                        </div>
                     </div>
                     <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 4px;">Cancel</button>
                        <button type="submit" id="btn-submit-assign-kol" class="btn btn-primary btn-flat" style="border-radius: 4px; font-weight: 600; background-color: #2563eb; border-color: #1d4ed8;">
                           Assign to Campaign
                        </button>
                     </div>
                  </form>
               </div>
            </div>
         </div>

      </div>
   </div>
</section>

<script type="text/javascript">
(function($) {
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

    function formatAjaxError(xhr, status, error) {
        var out = 'Terjadi Kesalahan:\n';
        out += '• HTTP Status: ' + (xhr.status || 0) + ' (' + (xhr.statusText || status || error || 'Unknown') + ')\n';
        
        if (xhr.status === 403) {
            out += '• Keterangan: Sesi login atau token CSRF kedaluwarsa. Silakan refresh halaman (F5) lalu coba lagi.\n';
        } else if (status === 'timeout' || xhr.status === 504) {
            out += '• Keterangan: Request timeout (proses Apify cloud melebihi batas waktu tunggu).\n';
        } else if (xhr.status === 0) {
            out += '• Keterangan: Koneksi terputus atau server tidak merespons.\n';
        }

        if (xhr.responseJSON) {
            if (xhr.responseJSON.message) out += '• Detail Server: ' + xhr.responseJSON.message + '\n';
        } else if (xhr.responseText) {
            var raw = xhr.responseText.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim();
            out += '• Raw Response: ' + raw.substring(0, 350) + '\n';
        }
        return out;
    }

    // 1. Single Platform Sync
    $(document).on('click', '.btn-sync-single-platform', function() {
        var $btn = $(this);
        var platformId = $btn.data('platform-id');
        var platName   = $btn.data('platform-name');
        var username   = $btn.data('username');
        var kpId       = $btn.data('kp-id');

        $btn.prop('disabled', true).html('Syncing...');

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

                    alert(platName + ' (@' + username + ') berhasil disinkronkan!\nFollowers: ' + followersFormatted + '\nAvg ER: ' + erPercent + '%');
                } else {
                    alert('Gagal Sinkronisasi:\n' + (res.message || 'Respons server tidak valid.'));
                }
            },
            error: function(xhr, status, error) {
                alert(formatAjaxError(xhr, status, error));
            },
            complete: function() {
                $btn.prop('disabled', false).html('Sync');
            }
        });
    });

    // 2. Full KOL Apify Sync (All connected accounts)
    $('#btn-sync-apify').on('click', function() {
        var kolId = $(this).data('kol-id');
        var $btn = $(this);

        $btn.prop('disabled', true).html('Menyinkronkan Apify...');

        var postData = { kol_id: kolId };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/kols/sync_apify_intelligence',
            type: 'POST',
            data: postData,
            dataType: 'json',
            timeout: 180000,
            success: function(res) {
                if (res.csrf_hash) {
                    csrfHash = res.csrf_hash;
                    $('#kol-view-csrf-token').val(csrfHash);
                }

                if (res.success) {
                    alert(res.message);
                } else {
                    alert('Gagal Sinkronisasi:\n' + (res.message || 'Respons server tidak valid.'));
                }
            },
            error: function(xhr, status, error) {
                alert(formatAjaxError(xhr, status, error));
            },
            complete: function() {
                $btn.prop('disabled', false).html('Sync Apify Intelligence');
            }
        });
    });

    // 3. Phase 3: Submit Review Form
    $('#form-add-review').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btn-submit-review');
        $btn.prop('disabled', true).html('Menyimpan...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.csrf_hash) {
                    csrfHash = res.csrf_hash;
                    $('#kol-view-csrf-token').val(csrfHash);
                    $form.find('input[name="' + csrfName + '"]').val(csrfHash);
                }
                if (res.success) {
                    $('#modal-add-review').modal('hide');
                    if (typeof swal !== 'undefined') {
                        swal({
                            title: "Tersimpan!",
                            text: res.message,
                            type: "success"
                        }, function() {
                            location.reload();
                        });
                    } else {
                        alert(res.message);
                        location.reload();
                    }
                } else {
                    alert(res.message || 'Gagal menyimpan review.');
                    $btn.prop('disabled', false).html('Simpan Review');
                }
            },
            error: function(xhr, status, error) {
                alert(formatAjaxError(xhr, status, error));
                $btn.prop('disabled', false).html('Simpan Review');
            }
        });
    });

    // 4. Phase 3: Toggle Blacklist from View
    $('.btn-toggle-blacklist-view').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var isBlacklisted = $(this).data('blacklisted') == 1;

        if (isBlacklisted) {
            if (typeof swal !== 'undefined') {
                swal({
                    title: "Hapus dari Blacklist?",
                    text: "KOL \"" + name + "\" akan dipulihkan ke status normal.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Pulihkan",
                    cancelButtonText: "Batal",
                    closeOnConfirm: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        var postData = {};
                        postData[csrfName] = csrfHash;
                        $.ajax({
                            url: BASE_URL + 'administrator/kols/toggle_blacklist/' + id,
                            type: 'POST',
                            dataType: 'json',
                            data: postData,
                            success: function(res) {
                                if (res.success) {
                                    swal({
                                        title: "Berhasil!",
                                        text: res.message,
                                        type: "success"
                                    }, function() {
                                        location.reload();
                                    });
                                } else {
                                    swal("Gagal", res.message, "error");
                                }
                            }
                        });
                    }
                });
            } else {
                if (confirm('Pulihkan KOL ' + name + ' dari blacklist?')) {
                    var postData = {};
                    postData[csrfName] = csrfHash;
                    $.post(BASE_URL + 'administrator/kols/toggle_blacklist/' + id, postData, function() {
                        location.reload();
                    });
                }
            }
        } else {
            if (typeof swal !== 'undefined') {
                swal({
                    title: "Blacklist KOL?",
                    text: "Masukkan alasan blacklist untuk \"" + name + "\":",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    inputPlaceholder: "Alasan blacklist...",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Blacklist Sekarang",
                    cancelButtonText: "Batal"
                }, function(inputValue) {
                    if (inputValue === false) return false;
                    if ($.trim(inputValue) === "") {
                        swal.showInputError("Harap masukkan alasan!");
                        return false;
                    }
                    var postData = { reason: inputValue };
                    postData[csrfName] = csrfHash;
                    $.ajax({
                        url: BASE_URL + 'administrator/kols/toggle_blacklist/' + id,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function(res) {
                            if (res.success) {
                                swal({
                                    title: "Di-Blacklist!",
                                    text: res.message,
                                    type: "warning"
                                }, function() {
                                    location.reload();
                                });
                            } else {
                                swal("Gagal", res.message, "error");
                            }
                        }
                    });
                });
            } else {
                var reason = prompt('Masukkan alasan blacklist untuk ' + name + ':');
                if (reason) {
                    var postData = { reason: reason };
                    postData[csrfName] = csrfHash;
                    $.post(BASE_URL + 'administrator/kols/toggle_blacklist/' + id, postData, function() {
                        location.reload();
                    });
                }
            }
        }
    });

    // Trigger Assign Similar KOL to Campaign Modal
    $(document).on('click', '.btn-trigger-assign-single', function() {
        var kolId   = $(this).data('id');
        var kolName = $(this).data('name');

        $('#assign-kol-id').val(kolId);
        $('#assign-kol-name').val(kolName);
        $('#assign-campaign-id').val('');
        $('#modal-assign-kol-campaign').modal('show');
    });

    // Submit Assign Single KOL to Campaign
    $('#form-assign-kol-campaign').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn-submit-assign-kol');
        var campaignId = $('#assign-campaign-id').val();
        var kolId = $('#assign-kol-id').val();
        var status = $('#assign-deal-status').val();

        if (!campaignId || !kolId) {
            alert('Please select a campaign.');
            return;
        }

        $btn.prop('disabled', true).html('Assigning...');

        var postData = {
            campaign_id: campaignId,
            kol_ids: [kolId],
            status: status
        };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/kols/assign_to_campaign_ajax',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(res) {
                if (res.csrf_hash) {
                    csrfHash = res.csrf_hash;
                    $('#kol-view-csrf-token').val(csrfHash);
                }

                if (res.success) {
                    alert(res.message);
                    $('#modal-assign-kol-campaign').modal('hide');
                } else {
                    alert('Assignment Failed:\n' + (res.message || 'Unknown server response.'));
                }
            },
            error: function(xhr, status, error) {
                alert(formatAjaxError(xhr, status, error));
            },
            complete: function() {
                $btn.prop('disabled', false).html('Assign to Campaign');
            }
        });
    });
})(jQuery);
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // ER Trend Chart Setup
    <?php if (!empty($er_trend)): ?>
    var ctxTrend = document.getElementById('erTrendChart');
    if (ctxTrend) {
        var labels = [];
        var dataPoints = [];
        <?php foreach($er_trend as $et): ?>
            labels.push('<?= date("d M", strtotime($et->snapshot_at)) ?>');
            dataPoints.push(<?= number_format((float)($et->engagement_rate ?? 0) * 100, 2) ?>);
        <?php endforeach; ?>

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Engagement Rate (%)',
                    data: dataPoints,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#3b82f6',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: {size: 10}, color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: '#f1f5f9', borderDash: [4, 4] },
                        ticks: { font: {size: 10}, color: '#64748b' }
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>
