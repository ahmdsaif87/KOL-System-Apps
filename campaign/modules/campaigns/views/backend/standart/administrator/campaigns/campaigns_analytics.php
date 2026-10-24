<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style type="text/css">
.analytics-container {
    font-family: inherit;
    color: #334155;
}

/* ── Clean Header Card ────────────────────────────────── */
.analytics-header-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 20px 24px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
}

/* ── Metric Stat Cards ────────────────────────────────── */
.metric-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 18px 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.metric-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
}
.metric-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.metric-title {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    margin: 0;
}
.metric-icon-badge {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    background: #f8fafc;
    color: #475569;
}
.metric-value {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.15;
    margin-bottom: 8px;
}
.metric-footer {
    font-size: 12px;
    color: #64748b;
}
.metric-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    margin-bottom: 4px;
}

/* Slim progress bars */
.progress-slim {
    height: 5px;
    border-radius: 3px;
    background: #f1f5f9;
    overflow: hidden;
    margin: 8px 0 4px 0;
}
.progress-slim-bar {
    height: 100%;
    border-radius: 3px;
    transition: width 0.4s ease;
}

/* ── Content Card / Chart Box ─────────────────────────── */
.content-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
}
.content-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.content-card-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ── Minimalist Tables ────────────────────────────────── */
.table-clean {
    width: 100%;
    margin: 0;
    font-size: 12px;
    border-collapse: separate;
    border-spacing: 0;
}
.table-clean thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 10px 14px;
    border-bottom: 1px solid #e2e8f0;
    border-top: none;
    vertical-align: middle;
}
.table-clean tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #334155;
}
.table-clean tbody tr:last-child td {
    border-bottom: none;
}
.table-clean tbody tr:hover td {
    background: #fafafa;
}

/* ── Soft Badges ──────────────────────────────────────── */
.badge-soft {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    line-height: 1.3;
}
.badge-soft-emerald { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.badge-soft-amber   { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.badge-soft-rose    { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
.badge-soft-blue    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.badge-soft-slate   { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

.section-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #64748b;
    margin: 0 0 12px 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>

<!-- Content Header -->
<section class="content-header" style="padding: 15px 15px 10px 15px;">
   <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0;">
      Campaign Financial &amp; Performance Analytics
      <small style="font-size: 13px; color: #64748b; font-weight: normal; margin-left: 6px;"><?= htmlspecialchars($campaign->name); ?></small>
   </h1>
   <ol class="breadcrumb" style="font-size: 12px; background: transparent; padding: 0; margin-top: 4px;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>" style="color: #64748b;"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/campaigns'); ?>" style="color: #64748b;">Campaigns</a></li>
      <li class="active" style="color: #0f172a; font-weight: 600;">Analytics</li>
   </ol>
</section>

<!-- Main content -->
<section class="content analytics-container" style="padding: 10px 15px;">
   <div class="row">
      <div class="col-md-12">

         <!-- 1. HEADER SUMMARY CARD -->
         <div class="analytics-header-card">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
               <div>
                  <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 6px;">
                     <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: #0f172a;">
                        <?= htmlspecialchars($campaign->name); ?>
                     </h2>
                     <span class="badge-soft <?= $campaign->status === 'ACTIVE' ? 'badge-soft-emerald' : 'badge-soft-slate'; ?>">
                        <?= htmlspecialchars($campaign->status); ?>
                     </span>
                     <?php if (!empty($campaign->campaign_type)): ?>
                        <span class="badge-soft badge-soft-blue">
                           <?= htmlspecialchars($campaign->campaign_type); ?>
                        </span>
                     <?php endif; ?>
                     <?php if (!empty($campaign->brand_name)): ?>
                        <span class="badge-soft badge-soft-slate">
                           <i class="fa fa-tag" style="color: #94a3b8;"></i> <?= htmlspecialchars($campaign->brand_name); ?>
                           <?= !empty($campaign->brand_category) ? ' &bull; ' . htmlspecialchars($campaign->brand_category) : ''; ?>
                        </span>
                     <?php endif; ?>
                  </div>
                  <div style="color: #64748b; font-size: 12px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                     <span><i class="fa fa-calendar-o" style="color: #94a3b8;"></i> Periode: <strong><?= date('d M Y', strtotime($campaign->start_date)); ?></strong> &mdash; <strong><?= date('d M Y', strtotime($campaign->end_date)); ?></strong></span>
                     <?php if (!empty($campaign->client_name)): ?>
                        <span>&bull;</span>
                        <span><i class="fa fa-building-o" style="color: #94a3b8;"></i> Client: <strong><?= htmlspecialchars($campaign->client_name); ?></strong></span>
                     <?php endif; ?>
                     <span>&bull;</span>
                     <span><i class="fa fa-users" style="color: #94a3b8;"></i> Assigned KOL: <strong><?= count($kol_analytics); ?> Talent</strong></span>
                  </div>
               </div>
               <div style="display: flex; gap: 6px;">
                  <a href="<?= site_url('administrator/campaigns/single_pdf/' . $campaign->id); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; padding: 6px 12px; font-size: 12px;">
                     <i class="fa fa-file-pdf-o text-danger"></i> Export PDF
                  </a>
                  <a href="<?= site_url('administrator/campaigns/view/' . $campaign->id); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; padding: 6px 12px; font-size: 12px;">
                     <i class="fa fa-eye"></i> Detail
                  </a>
                  <a href="<?= site_url('administrator/campaigns'); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; padding: 6px 12px; font-size: 12px;">
                     <i class="fa fa-undo"></i> Kembali
                  </a>
               </div>
            </div>
         </div>

         <!-- 2. SIX FINANCIAL & PERFORMANCE KPI METRICS -->
         <div class="row" style="margin-bottom: 5px;">
            
            <!-- Metric 1: Total Budget -->
            <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
               <div class="metric-card">
                  <div>
                     <div class="metric-header">
                        <span class="metric-title">Total Budget Dialokasikan</span>
                        <div class="metric-icon-badge"><i class="fa fa-database"></i></div>
                     </div>
                     <div class="metric-value">
                        Rp <?= number_format($total_budget, 0, ',', '.'); ?>
                     </div>
                  </div>
                  <div class="metric-footer">
                     <div class="metric-row">
                        <span>Sisa Budget Bebas:</span>
                        <strong style="color: <?= $remaining_budget >= 0 ? '#059669' : '#dc2626'; ?>;">
                           Rp <?= number_format($remaining_budget, 0, ',', '.'); ?>
                        </strong>
                     </div>
                     <div class="progress-slim">
                        <div class="progress-slim-bar" style="width: <?= min(100, $budget_utilization_pct); ?>%; background: <?= $budget_utilization_pct > 100 ? '#dc2626' : '#2563eb'; ?>;"></div>
                     </div>
                     <div style="font-size: 11px; color: #94a3b8; text-align: right; margin-top: 2px;">
                        <?= $budget_utilization_pct; ?>% teralokasi ke deals
                     </div>
                  </div>
               </div>
            </div>

            <!-- Metric 2: Deal Commitments -->
            <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
               <div class="metric-card">
                  <div>
                     <div class="metric-header">
                        <span class="metric-title">Total Komitmen Deals (SOW)</span>
                        <div class="metric-icon-badge"><i class="fa fa-handshake-o"></i></div>
                     </div>
                     <div class="metric-value">
                        Rp <?= number_format($total_deals, 0, ',', '.'); ?>
                     </div>
                  </div>
                  <div class="metric-footer">
                     <div class="metric-row">
                        <span>Rata-rata Deal / KOL:</span>
                        <strong>Rp <?= count($kol_analytics) > 0 ? number_format($total_deals / count($kol_analytics), 0, ',', '.') : 0; ?></strong>
                     </div>
                     <div class="progress-slim">
                        <div class="progress-slim-bar" style="width: 100%; background: #6366f1;"></div>
                     </div>
                     <div style="font-size: 11px; color: #94a3b8; text-align: right; margin-top: 2px;">
                        <?= count($kol_analytics); ?> Kontrak KOL aktif
                     </div>
                  </div>
               </div>
            </div>

            <!-- Metric 3: Realisasi Kas / Terbayar -->
            <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
               <div class="metric-card">
                  <div>
                     <div class="metric-header">
                        <span class="metric-title">Realisasi Kas / Terbayar</span>
                        <div class="metric-icon-badge"><i class="fa fa-money"></i></div>
                     </div>
                     <div class="metric-value" style="color: #059669;">
                        Rp <?= number_format($total_paid, 0, ',', '.'); ?>
                     </div>
                  </div>
                  <div class="metric-footer">
                     <div class="metric-row">
                        <span>Ditagih (Invoice):</span>
                        <span>Rp <?= number_format($total_invoiced, 0, ',', '.'); ?></span>
                     </div>
                     <div class="metric-row" style="margin-bottom: 0;">
                        <span>Sisa Tagihan:</span>
                        <strong style="color: <?= $unpaid_balance > 0 ? '#dc2626' : '#059669'; ?>;">
                           Rp <?= number_format($unpaid_balance, 0, ',', '.'); ?>
                        </strong>
                     </div>
                     <div class="progress-slim">
                        <div class="progress-slim-bar" style="width: <?= min(100, $invoice_paid_pct); ?>%; background: #059669;"></div>
                     </div>
                     <div style="font-size: 11px; color: #94a3b8; text-align: right; margin-top: 2px;">
                        <?= $invoice_paid_pct; ?>% invoice terbayar
                     </div>
                  </div>
               </div>
            </div>

            <!-- Metric 4: Views Target & Actual -->
            <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
               <div class="metric-card">
                  <div>
                     <div class="metric-header">
                        <span class="metric-title">Pencapaian Views Aktual</span>
                        <div class="metric-icon-badge"><i class="fa fa-eye"></i></div>
                     </div>
                     <div class="metric-value">
                        <?= number_format($total_views, 0, ',', '.'); ?>
                     </div>
                  </div>
                  <div class="metric-footer">
                     <div class="metric-row">
                        <span>Target Campaign:</span>
                        <strong><?= $target_views > 0 ? number_format($target_views, 0, ',', '.') . ' views' : '-'; ?></strong>
                     </div>
                     <div class="progress-slim">
                        <div class="progress-slim-bar" style="width: <?= min(100, $views_achievement_pct); ?>%; background: <?= $views_achievement_pct >= 100 ? '#059669' : '#0284c7'; ?>;"></div>
                     </div>
                     <div style="font-size: 11px; text-align: right; margin-top: 2px;">
                        <strong style="color: <?= $views_achievement_pct >= 100 ? '#059669' : '#0284c7'; ?>;"><?= $views_achievement_pct; ?>%</strong> tercapai
                     </div>
                  </div>
               </div>
            </div>

            <!-- Metric 5: Engagements & ER -->
            <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
               <div class="metric-card">
                  <div>
                     <div class="metric-header">
                        <span class="metric-title">Interaksi &amp; Engagement Rate</span>
                        <div class="metric-icon-badge"><i class="fa fa-heart-o"></i></div>
                     </div>
                     <div class="metric-value">
                        <?= number_format($total_engagements, 0, ',', '.'); ?>
                     </div>
                  </div>
                  <div class="metric-footer">
                     <div class="metric-row">
                        <span>Rata-rata ER Aktual:</span>
                        <strong><?= round($avg_er * 100, 2); ?>%</strong>
                     </div>
                     <div class="metric-row" style="margin-bottom: 0;">
                        <span>Target ER:</span>
                        <span style="color: #64748b;"><?= round($target_er * 100, 2); ?>% (<?= $er_achievement_pct; ?>% target)</span>
                     </div>
                     <div class="progress-slim">
                        <div class="progress-slim-bar" style="width: <?= min(100, $er_achievement_pct); ?>%; background: <?= $er_achievement_pct >= 100 ? '#059669' : '#d97706'; ?>;"></div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Metric 6: Cost Efficiency (CPV & CPE) -->
            <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
               <div class="metric-card">
                  <div>
                     <div class="metric-header">
                        <span class="metric-title">Efisiensi Biaya (Blended CPV)</span>
                        <div class="metric-icon-badge"><i class="fa fa-pie-chart"></i></div>
                     </div>
                     <div class="metric-value">
                        <?= $blended_cpv > 0 ? 'Rp ' . number_format($blended_cpv, 0, ',', '.') : '-'; ?>
                        <span style="font-size: 13px; font-weight: normal; color: #64748b;">/ view</span>
                     </div>
                  </div>
                  <div class="metric-footer">
                     <div class="metric-row">
                        <span>Target CPV:</span>
                        <strong><?= $target_cpv > 0 ? 'Rp ' . number_format($target_cpv, 0, ',', '.') : '-'; ?></strong>
                     </div>
                     <div class="metric-row" style="margin-bottom: 6px;">
                        <span>Blended CPE:</span>
                        <span><?= $blended_cpe > 0 ? 'Rp ' . number_format($blended_cpe, 0, ',', '.') . ' / eng' : '-'; ?></span>
                     </div>
                     <div>
                        <?php if ($target_cpv > 0 && $blended_cpv > 0): ?>
                           <?php if ($blended_cpv <= $target_cpv): ?>
                              <span class="badge-soft badge-soft-emerald"><i class="fa fa-check"></i> Efisien (Sesuai / Di Bawah Target)</span>
                           <?php else: ?>
                              <span class="badge-soft badge-soft-rose"><i class="fa fa-exclamation-triangle"></i> Di Atas Target CPV</span>
                           <?php endif; ?>
                        <?php else: ?>
                           <span class="badge-soft badge-soft-slate">Belum ada target CPV</span>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>

         </div>

         <!-- 3. CHARTS SECTION -->
         <div class="row">
            
            <!-- Donut: Financial Allocation -->
            <div class="col-md-5">
               <div class="content-card">
                  <div class="content-card-header">
                     <h4 class="content-card-title">
                        <i class="fa fa-pie-chart" style="color: #2563eb;"></i> Alokasi Finansial Campaign
                     </h4>
                  </div>
                  <div style="position: relative; height: 230px;">
                     <canvas id="chartFinancialDonut"></canvas>
                  </div>
                  <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid #f1f5f9; font-size: 11px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; color: #475569;">
                     <div><span style="display:inline-block;width:8px;height:8px;background:#059669;border-radius:2px;margin-right:6px;"></span>Terbayar: <strong>Rp <?= number_format($total_paid, 0, ',', '.'); ?></strong></div>
                     <div><span style="display:inline-block;width:8px;height:8px;background:#d97706;border-radius:2px;margin-right:6px;"></span>Tagihan Pending: <strong>Rp <?= number_format($unpaid_balance, 0, ',', '.'); ?></strong></div>
                     <div><span style="display:inline-block;width:8px;height:8px;background:#6366f1;border-radius:2px;margin-right:6px;"></span>Sisa Komitmen: <strong>Rp <?= number_format(max(0, $total_deals - $total_invoiced), 0, ',', '.'); ?></strong></div>
                     <div><span style="display:inline-block;width:8px;height:8px;background:#3b82f6;border-radius:2px;margin-right:6px;"></span>Budget Bebas: <strong>Rp <?= number_format($remaining_budget, 0, ',', '.'); ?></strong></div>
                  </div>
               </div>
            </div>

            <!-- Bar Chart: Views & Engagements per KOL -->
            <div class="col-md-7">
               <div class="content-card">
                  <div class="content-card-header">
                     <h4 class="content-card-title">
                        <i class="fa fa-bar-chart" style="color: #059669;"></i> Performa Views &amp; Engagements per KOL
                     </h4>
                  </div>
                  <div style="position: relative; height: 260px;">
                     <canvas id="chartKolPerformance"></canvas>
                  </div>
               </div>
            </div>

         </div>

         <!-- 4. DETAILED TALENT TABLE -->
         <div class="content-card" style="padding: 0; overflow: hidden; margin-bottom: 30px;">
            <div style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
               <h4 class="content-card-title">
                  <i class="fa fa-table" style="color: #475569;"></i> Rincian Finansial &amp; Metrik per KOL Talent
               </h4>
               <span class="badge-soft badge-soft-slate"><?= count($kol_analytics); ?> KOL Terdaftar</span>
            </div>

            <div class="table-responsive" style="margin: 0;">
               <table class="table-clean">
                  <thead>
                     <tr>
                        <th>KOL Talent</th>
                        <th class="text-right">Nominal Deal</th>
                        <th>Status Tagihan</th>
                        <th class="text-right">Terbayar / Sisa</th>
                        <th class="text-center" style="width: 140px;">Progress Tugas</th>
                        <th class="text-right">Views</th>
                        <th class="text-right">Engagements</th>
                        <th class="text-center">Avg ER</th>
                        <th class="text-right">CPV Aktual</th>
                        <th class="text-center">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if (empty($kol_analytics)): ?>
                        <tr>
                           <td colspan="10" class="text-center text-muted" style="padding: 30px;">
                              Belum ada KOL yang ditugaskan ke campaign ini.
                           </td>
                        </tr>
                     <?php else: ?>
                        <?php foreach ($kol_analytics as $ka): ?>
                           <?php $ck = $ka['campaign_kol']; ?>
                           <tr>
                              <td>
                                 <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if (!empty($ck->kol_avatar)): ?>
                                       <img src="<?= htmlspecialchars($ck->kol_avatar); ?>" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;">
                                    <?php else: ?>
                                       <div style="width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">
                                          <?= strtoupper(substr($ck->kol_name ?: 'K', 0, 1)); ?>
                                       </div>
                                    <?php endif; ?>
                                    <div>
                                       <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($ck->kol_name ?: 'KOL #' . $ck->kol_id); ?></div>
                                       <div style="font-size: 11px; color: #64748b;">
                                          <span class="badge-soft badge-soft-slate" style="font-size: 9px; padding: 1px 4px;"><?= htmlspecialchars($ck->tier_name ?: 'Standard'); ?></span>
                                          <?php if (!empty($ck->bank_name)): ?>
                                             &bull; <?= htmlspecialchars($ck->bank_name); ?> <?= htmlspecialchars($ck->bank_account_number); ?>
                                          <?php endif; ?>
                                       </div>
                                    </div>
                                 </div>
                              </td>
                              <td class="text-right" style="font-weight: 600; color: #0f172a;">
                                 Rp <?= number_format($ka['deal_amount'], 0, ',', '.'); ?>
                              </td>
                              <td>
                                 <?php if ($ka['payment_status'] === 'PAID'): ?>
                                    <span class="badge-soft badge-soft-emerald">Lunas</span>
                                 <?php elseif ($ka['payment_status'] === 'PARTIAL'): ?>
                                    <span class="badge-soft badge-soft-amber">Sebagian</span>
                                 <?php elseif ($ka['payment_status'] === 'UNPAID'): ?>
                                    <span class="badge-soft badge-soft-rose">Belum Bayar</span>
                                 <?php else: ?>
                                    <span class="badge-soft badge-soft-slate">Belum Invoice</span>
                                 <?php endif; ?>
                                 <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                    Inv: Rp <?= number_format($ka['invoiced_amount'], 0, ',', '.'); ?>
                                 </div>
                              </td>
                              <td class="text-right">
                                 <div style="font-weight: 600; color: #059669;">
                                    Rp <?= number_format($ka['paid_amount'], 0, ',', '.'); ?>
                                 </div>
                                 <?php if ($ka['unpaid_balance'] > 0): ?>
                                    <div style="font-size: 11px; color: #dc2626;">
                                       Sisa: Rp <?= number_format($ka['unpaid_balance'], 0, ',', '.'); ?>
                                    </div>
                                 <?php endif; ?>
                              </td>
                              <td class="text-center">
                                 <div style="font-size: 11px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                                    <?= $ka['task_completed']; ?> / <?= $ka['task_total']; ?> (<?= $ka['task_progress_pct']; ?>%)
                                 </div>
                                 <div class="progress-slim" style="margin: 0;">
                                    <div class="progress-slim-bar" style="width: <?= $ka['task_progress_pct']; ?>%; background: <?= $ka['task_progress_pct'] >= 100 ? '#059669' : '#2563eb'; ?>;"></div>
                                 </div>
                              </td>
                              <td class="text-right" style="font-weight: 600; color: #0284c7;">
                                 <?= number_format($ka['views'], 0, ',', '.'); ?>
                              </td>
                              <td class="text-right" style="color: #475569;">
                                 <?= number_format($ka['engagements'], 0, ',', '.'); ?>
                              </td>
                              <td class="text-center">
                                 <span class="badge-soft <?= ($ka['avg_er'] >= $target_er && $target_er > 0) ? 'badge-soft-emerald' : 'badge-soft-blue'; ?>">
                                    <?= round($ka['avg_er'] * 100, 2); ?>%
                                 </span>
                              </td>
                              <td class="text-right" style="font-weight: 600;">
                                 <?= $ka['cpv'] > 0 ? 'Rp ' . number_format($ka['cpv'], 0, ',', '.') : '-'; ?>
                              </td>
                              <td class="text-center" style="white-space: nowrap;">
                                 <a href="<?= site_url('administrator/campaign_kols/view/' . $ck->id); ?>" class="btn btn-xs btn-default btn-flat" title="Lihat Campaign KOL"><i class="fa fa-eye"></i></a>
                                 <a href="<?= site_url('administrator/finance_hub?q=' . urlencode($ck->kol_name)); ?>" class="btn btn-xs btn-default btn-flat" title="Keuangan / Pembayaran" style="color: #059669;"><i class="fa fa-money"></i></a>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                     <?php endif; ?>
                  </tbody>
               </table>
            </div>
         </div>

      </div>
   </div>
</section>

<!-- CHART.JS SCRIPTS -->
<script>
$(document).ready(function(){
    // 1. Donut Chart for Financial Allocation
    var ctxDonut = document.getElementById('chartFinancialDonut');
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Terbayar (Paid)', 'Invoice Belum Lunas', 'Komitmen Belum Ditagih', 'Sisa Budget Bebas'],
                datasets: [{
                    data: [
                        <?= (float)$total_paid; ?>,
                        <?= (float)$unpaid_balance; ?>,
                        <?= (float)max(0, $total_deals - $total_invoiced); ?>,
                        <?= (float)$remaining_budget; ?>
                    ],
                    backgroundColor: ['#059669', '#d97706', '#6366f1', '#3b82f6'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var val = context.raw || 0;
                                return ' ' + context.label + ': Rp ' + val.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }

    // 2. Bar Chart for Views & Engagements per KOL
    var ctxBar = document.getElementById('chartKolPerformance');
    if (ctxBar) {
        var kolNames = <?= json_encode(array_map(function($k){ return $k['campaign_kol']->kol_name ?: ('KOL #' . $k['campaign_kol']->kol_id); }, $kol_analytics)); ?>;
        var kolViews = <?= json_encode(array_map(function($k){ return $k['views']; }, $kol_analytics)); ?>;
        var kolEngs  = <?= json_encode(array_map(function($k){ return $k['engagements']; }, $kol_analytics)); ?>;

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: kolNames,
                datasets: [
                    {
                        label: 'Actual Views',
                        data: kolViews,
                        backgroundColor: '#0284c7',
                        borderRadius: 4
                    },
                    {
                        label: 'Total Engagements',
                        data: kolEngs,
                        backgroundColor: '#f59e0b',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: 'inherit' }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(val) {
                                return val.toLocaleString('id-ID');
                            },
                            font: { size: 10, family: 'inherit' },
                            color: '#94a3b8'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 10, padding: 12, font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.dataset.label + ': ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
