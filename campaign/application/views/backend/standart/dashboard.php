<style type="text/css">
   /* ── General Dashboard Styles & Margins ───────────────── */
   .content-header.dashboard-header {
      padding: 18px 28px 10px 28px !important;
   }
   .dashboard-container {
      font-family: inherit;
      color: #334155;
      padding: 10px 28px 35px 28px !important;
   }
   .dashboard-container > .row {
      margin-left: -10px;
      margin-right: -10px;
   }
   .dashboard-container > .row > [class*="col-"] {
      padding-left: 10px;
      padding-right: 10px;
   }

   /* ── Metric Summary Cards Grid (Baris 1) ───────────────── */
   .kpi-row {
      display: flex !important;
      flex-wrap: wrap !important;
   }
   .kpi-row:before,
   .kpi-row:after {
      display: none !important;
   }
   .kpi-row > [class*=col-] {
      display: flex !important;
      flex-direction: column !important;
   }
   .kpi-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 16px 18px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
      transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
      position: relative;
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
      height: calc(100% - 20px);
   }
   .kpi-card:hover {
      border-color: #cbd5e1;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
      transform: translateY(-1px);
   }
   .kpi-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
   }
   .kpi-card-title {
      font-size: 13px;
      font-weight: 600;
      color: #64748b;
      letter-spacing: 0.2px;
      margin: 0;
   }
   .kpi-card-icon {
      width: 32px;
      height: 32px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
   }
   .kpi-card-value {
      font-size: 26px;
      font-weight: 700;
      color: #0f172a;
      line-height: 1.15;
      margin: 8px 0 6px;
   }
   .kpi-card-sub {
      font-size: 12px;
      color: #64748b;
      display: flex;
      align-items: center;
      gap: 6px;
      margin: 0;
      flex-wrap: wrap;
      margin-top: auto;
   }

   /* Icon Themes */
   .icon-slate   { background: #f1f5f9; color: #475569; }
   .icon-blue    { background: #eff6ff; color: #2563eb; }
   .icon-amber   { background: #fffbeb; color: #d97706; }
   .icon-emerald { background: #ecfdf5; color: #059669; }

   /* ── Dashboard Cards & Containers ──────────────────────── */
   .dash-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
      margin-bottom: 20px;
      overflow: hidden;
   }
   .dash-card-header {
      padding: 14px 18px;
      border-bottom: 1px solid #f1f5f9;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #ffffff;
   }
   .dash-card-title {
      font-size: 14px;
      font-weight: 600;
      color: #0f172a;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
   }
   .dash-card-body {
      padding: 18px;
   }

   /* ── Financial Mini Grid ──────────────────────────────── */
   .finance-stat-grid {
      display: flex;
      flex-wrap: wrap;
      margin-left: -6px;
      margin-right: -6px;
      margin-bottom: 12px;
   }
   .finance-stat-col {
      padding: 0 6px;
      margin-bottom: 10px;
      flex: 1 1 50%;
      max-width: 50%;
   }
   @media (min-width: 768px) {
      .finance-stat-col {
         flex: 1 1 25%;
         max-width: 25%;
         margin-bottom: 0;
      }
   }
   .finance-stat-item {
      background: #f8fafc;
      border: 1px solid #f1f5f9;
      border-radius: 6px;
      padding: 10px 12px;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
   }
   .finance-stat-label {
      font-size: 11px;
      font-weight: 600;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      margin-bottom: 3px;
   }
   .finance-stat-value {
      font-size: 16px;
      font-weight: 700;
      color: #0f172a;
      line-height: 1.2;
   }

   /* ── Vertical Step Funnel Timeline ────────────────────── */
   .funnel-timeline {
      display: flex;
      flex-direction: column;
      gap: 8px;
      position: relative;
   }
   .funnel-step {
      display: flex;
      align-items: center;
      justify-content: space-between;
      text-decoration: none !important;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 10px 14px;
      transition: all 0.15s ease;
      position: relative;
   }
   .funnel-step:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
      transform: translateX(2px);
   }
   .funnel-step-left {
      display: flex;
      align-items: center;
      gap: 10px;
   }
   .funnel-step-badge {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #f1f5f9;
      color: #475569;
      font-size: 11px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
   }
   .funnel-step.step-1 .funnel-step-badge { background: #eff6ff; color: #2563eb; }
   .funnel-step.step-2 .funnel-step-badge { background: #e0e7ff; color: #4338ca; }
   .funnel-step.step-3 .funnel-step-badge { background: #fff7ed; color: #c2410c; }
   .funnel-step.step-4 .funnel-step-badge { background: #e0f2fe; color: #0284c7; }

   .funnel-step-name {
      font-size: 12px;
      font-weight: 600;
      color: #334155;
   }
   .funnel-step-right {
      display: flex;
      align-items: center;
      gap: 8px;
   }
   .funnel-step-count {
      font-size: 13px;
      font-weight: 700;
      color: #0f172a;
   }
   .funnel-step-pct {
      font-size: 11px;
      font-weight: 600;
      color: #64748b;
      background: #f1f5f9;
      padding: 2px 6px;
      border-radius: 4px;
   }

   /* ── Minimalist Clean Table ───────────────────────────── */
   .table-clean {
      width: 100%;
      margin: 0;
      font-size: 12px;
      border-collapse: collapse;
   }
   .table-clean thead th {
      background: #f8fafc;
      color: #64748b;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      padding: 10px 12px;
      border-bottom: 1px solid #e2e8f0;
      border-top: none;
   }
   .table-clean tbody td {
      padding: 10px 12px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
      color: #334155;
   }
   .table-clean tbody tr:last-child td {
      border-bottom: none;
   }
   .table-clean tbody tr:hover td {
      background: #f8fafc;
   }

   /* ── Soft Muted Badges ────────────────────────────────── */
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
   .badge-soft-rose    { background: #fef2f2; color: #e11d48; border: 1px solid #fecdd3; }
   .badge-soft-blue    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
   .badge-soft-slate   { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

   .badge-platform-ig  { background: #fdf4ff; color: #86198f; border: 1px solid #fae8ff; }
   .badge-platform-yt  { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }
   .badge-platform-tt  { background: #f8fafc; color: #0f172a; border: 1px solid #cbd5e1; }

   /* Activity Feed Item */
   .live-feed-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 9px 0;
      border-bottom: 1px solid #f1f5f9;
      font-size: 12px;
   }
   .live-feed-item:last-child {
      border-bottom: none;
      padding-bottom: 0;
   }

   /* ── Tablet Breakpoint (768px - 991px) ────────────────── */
   @media (min-width: 768px) and (max-width: 991px) {
      .content-header.dashboard-header {
         padding: 16px 20px 8px 20px !important;
      }
      .dashboard-container {
         padding: 8px 20px 30px 20px !important;
      }
      .kpi-row > [class*=col-] {
         width: 50% !important;
         flex: 0 0 50% !important;
         max-width: 50% !important;
      }
      .kpi-card {
         padding: 14px 16px !important;
         margin-bottom: 16px !important;
         height: calc(100% - 16px) !important;
      }
      .kpi-card-value {
         font-size: 24px !important;
      }
      .finance-stat-col {
         flex: 1 1 50% !important;
         max-width: 50% !important;
         margin-bottom: 10px !important;
      }
   }

   /* ── Mobile Responsive ────────────────────────────────── */
   @media (max-width: 767px) {
      .content-header h1 { font-size: 16px !important; }
      .content-header h1 small { display: block; margin-left: 0 !important; margin-top: 4px; font-size: 11px !important; }

      /* KPI Cards: 2 per row on mobile */
      .kpi-row > [class*=col-] {
         width: 50% !important;
         flex: 0 0 50% !important;
         max-width: 50% !important;
         padding-left: 6px !important;
         padding-right: 6px !important;
      }
      .kpi-card {
         padding: 12px 10px !important;
         margin-bottom: 12px !important;
         height: calc(100% - 12px) !important;
      }
      .kpi-card-value { font-size: 20px !important; }
      .kpi-card-title { font-size: 11px !important; }
      .kpi-card-icon { width: 26px !important; height: 26px !important; font-size: 11px !important; }
      .kpi-card-sub { font-size: 10px !important; }

      /* Main two-column grid: stack */
      .content .row > .col-md-8,
      .content .row > .col-md-4 { width: 100%; padding-left: 8px; padding-right: 8px; }

      /* Dash card adjustments */
      .dash-card-body { padding: 12px; }
      .dash-card-header { padding: 10px 12px; flex-wrap: wrap; gap: 8px; }
      .dash-card-title { font-size: 13px; }

      /* Finance stats: 2 cols on mobile */
      .finance-stat-col { flex: 1 1 50% !important; max-width: 50% !important; }
      .finance-stat-value { font-size: 13px; word-break: break-all; }
      .finance-stat-label { font-size: 10px; }

      /* Tables: horizontal scroll */
      .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
      .table-clean { min-width: 600px; }
      .table-clean thead th, .table-clean tbody td { padding: 8px 6px; font-size: 11px; white-space: nowrap; }

      /* Funnel timeline */
      .funnel-step { padding: 8px 10px; }
      .funnel-step-name { font-size: 11px; }

      /* Pending invoices and contract items */
      .dash-card-body > div[style*="display: flex"][style*="justify-content: space-between"] {
         flex-wrap: wrap;
         gap: 4px;
      }

      /* Live feed items */
      .live-feed-item { flex-wrap: wrap; gap: 6px; }

      /* Mobile margin & padding breathing room */
      .content-header.dashboard-header {
         padding: 14px 18px 8px 18px !important;
      }
      .dashboard-container {
         padding: 8px 16px 25px 16px !important;
      }
      .dashboard-container > .row {
         margin-left: -6px;
         margin-right: -6px;
      }
      .dashboard-container > .row > [class*="col-"] {
         padding-left: 6px;
         padding-right: 6px;
      }
   }

   @media (max-width: 420px) {
      /* KPI Cards: 1 per row on very small screens */
      .content .row > .col-md-3.col-sm-6 { width: 100%; }
      .kpi-card-value { font-size: 18px; }

      /* Finance stats: 1 col */
      .finance-stat-col { flex: 1 1 100% !important; max-width: 100% !important; }
   }
</style>

<!-- Content Header -->
<section class="content-header dashboard-header">
    <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0;">
        Dashboard <small style="font-size: 13px; color: #64748b; font-weight: normal; margin-left: 6px;">Ringkasan Kinerja &amp; Status Sistem KOL</small>
    </h1>
    <ol class="breadcrumb" style="font-size: 12px; background: transparent; padding: 0; margin-top: 4px;">
        <li><a href="#" style="color: #64748b;"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#" style="color: #64748b;">Evaluasi</a></li>
        <li class="active" style="color: #0f172a; font-weight: 600;">Dashboard</li>
    </ol>
</section>

<!-- Main content -->
<section class="content dashboard-container">

    <!-- ═══════════════════════════════════════════════
         BARIS 1: TOP 4 STAT CARDS (HORIZONTAL GRID)
         ═══════════════════════════════════════════════ -->
    <div class="row kpi-row">
        <!-- 1) Total Profil KOL -->
        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="kpi-card">
                <div class="kpi-card-header">
                    <span class="kpi-card-title">Total Profil KOL</span>
                    <div class="kpi-card-icon icon-slate"><i class="fa fa-users"></i></div>
                </div>
                <div class="kpi-card-value"><?= number_format($total_kols); ?></div>
                <div class="kpi-card-sub">
                    <span class="badge-soft badge-soft-emerald"><i class="fa fa-check-circle"></i> <?= number_format($active_kols); ?> Aktif</span>
                    <span style="color: #94a3b8;">Database KOL</span>
                </div>
            </div>
        </div>

        <!-- 2) Campaign Aktif -->
        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="kpi-card">
                <div class="kpi-card-header">
                    <span class="kpi-card-title">Campaign Aktif</span>
                    <div class="kpi-card-icon icon-blue"><i class="fa fa-bullhorn"></i></div>
                </div>
                <div class="kpi-card-value"><?= number_format($active_campaigns); ?></div>
                <div class="kpi-card-sub">
                    <span>Anggaran: <strong style="color: #0f172a;">Rp <?= number_format($total_budget, 0, ',', '.'); ?></strong></span>
                </div>
            </div>
        </div>

        <!-- 3) Menunggu Persetujuan -->
        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="kpi-card">
                <div class="kpi-card-header">
                    <span class="kpi-card-title">Menunggu Persetujuan</span>
                    <div class="kpi-card-icon icon-amber"><i class="fa fa-clock-o"></i></div>
                </div>
                <div class="kpi-card-value"><?= number_format($pending_approvals); ?></div>
                <div class="kpi-card-sub">
                    <span>KOL, Brief &amp; Draft Pending</span>
                </div>
            </div>
        </div>

        <!-- 4) Konten Live / Posted -->
        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="kpi-card">
                <div class="kpi-card-header">
                    <span class="kpi-card-title">Konten Live / Posted</span>
                    <div class="kpi-card-icon icon-emerald"><i class="fa fa-check-circle-o"></i></div>
                </div>
                <div class="kpi-card-value"><?= number_format($live_postings); ?></div>
                <div class="kpi-card-sub">
                    <span>Progress: <strong><?= number_format($live_postings); ?></strong> / <?= number_format($total_tasks); ?> Tasks</span>
                    <?php if ($total_tasks > 0): ?>
                        <span style="color: #059669; font-weight: 600;">(<?= round(($live_postings / $total_tasks) * 100); ?>%)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         BARIS 2: MAIN GRID (2 KOLOM: KIRI & KANAN)
         ═══════════════════════════════════════════════ -->
    <div class="row">
        
        <!-- KOLOM KIRI (col-md-8): Ringkasan Keuangan & Tabel Jadwal Posting -->
        <div class="col-md-8">
            
            <!-- A. Ringkasan Keuangan & Arus Kas -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-money" style="color: #059669;"></i> Ringkasan Keuangan &amp; Arus Kas
                    </h3>
                    <a href="<?= site_url('administrator/finance_hub'); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; font-weight: 500; font-size: 11px; color: #475569;">
                        Finance Hub <i class="fa fa-arrow-right" style="margin-left: 2px;"></i>
                    </a>
                </div>
                <div class="dash-card-body">
                    
                    <!-- 4 Mini Stats Grid -->
                    <div class="finance-stat-grid">
                        <div class="finance-stat-col">
                            <div class="finance-stat-item">
                                <div class="finance-stat-label">Total Deals</div>
                                <div class="finance-stat-value">Rp <?= number_format($total_deals ?? 0, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="finance-stat-col">
                            <div class="finance-stat-item">
                                <div class="finance-stat-label">Ter-Invoice</div>
                                <div class="finance-stat-value">Rp <?= number_format($total_invoiced ?? 0, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="finance-stat-col">
                            <div class="finance-stat-item">
                                <div class="finance-stat-label">Sudah Dibayar</div>
                                <div class="finance-stat-value" style="color: #059669;">Rp <?= number_format($total_paid ?? 0, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="finance-stat-col">
                            <div class="finance-stat-item">
                                <div class="finance-stat-label">Sisa Belum Bayar</div>
                                <?php $out_color = ($total_outstanding > 0) ? '#e11d48' : '#0f172a'; ?>
                                <div class="finance-stat-value" style="color: <?= $out_color; ?>;">Rp <?= number_format($total_outstanding ?? 0, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Cash Progress Bar -->
                    <?php
                        $pct_cash_paid = ($total_invoiced > 0) ? round(($total_paid / $total_invoiced) * 100, 1) : 0;
                    ?>
                    <div style="margin-top: 10px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                        <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 6px;">
                            <span>Realisasi Pembayaran Kas</span>
                            <span style="color: #059669;"><?= $pct_cash_paid; ?>% Terbayar</span>
                        </div>
                        <div style="display: flex; height: 6px; border-radius: 3px; overflow: hidden; background: #f1f5f9;">
                            <div style="width: <?= $pct_cash_paid; ?>%; background: #10b981;" title="Sudah Dibayar: <?= $pct_cash_paid; ?>%"></div>
                            <div style="width: <?= max(0, 100 - $pct_cash_paid); ?>%; background: <?= ($total_outstanding > 0) ? '#fecdd3' : '#e2e8f0'; ?>;" title="Sisa Outstanding"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 11px; color: #64748b; margin-top: 6px; flex-wrap: wrap; gap: 8px;">
                            <span><span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #10b981; margin-right: 4px;"></span>Terbayar: <strong style="color: #0f172a;">Rp <?= number_format($total_paid ?? 0, 0, ',', '.'); ?></strong></span>
                            <span><span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #e11d48; margin-right: 4px;"></span>Outstanding: <strong style="color: #e11d48;">Rp <?= number_format($total_outstanding ?? 0, 0, ',', '.'); ?></strong></span>
                        </div>
                    </div>

                    <!-- Pending Invoices Mini Alert (If any) -->
                    <?php if (!empty($pending_invoices)): ?>
                        <div style="margin-top: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.3px;">
                                    Tagihan Menunggu Pembayaran
                                </span>
                                <a href="<?= site_url('administrator/finance_hub'); ?>" style="font-size: 11px; color: #2563eb; text-decoration: none; font-weight: 500;">
                                    Kelola <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                            <?php foreach ($pending_invoices as $inv): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-top: 1px solid #edf2f7; font-size: 12px;">
                                    <div>
                                        <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($inv->kol_name); ?></div>
                                        <div style="font-size: 11px; color: #64748b;"><?= htmlspecialchars($inv->invoice_no); ?> &bull; <?= htmlspecialchars($inv->campaign_name); ?></div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 700; color: #e11d48;">Rp <?= number_format($inv->unpaid_balance, 0, ',', '.'); ?></div>
                                        <span class="badge-soft <?= ($inv->days_left < 0) ? 'badge-soft-rose' : (($inv->days_left <= 3) ? 'badge-soft-amber' : 'badge-soft-slate'); ?>" style="font-size: 10px;">
                                            <?= ($inv->days_left < 0) ? 'Overdue ' . abs($inv->days_left) . 'H' : (($inv->days_left == 0) ? 'Hari Ini' : ($inv->due_date ? date('d M Y', strtotime($inv->due_date)) : 'Menunggu')); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- B. Tabel Jadwal Posting & Deadline Terdekat -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-calendar-check-o" style="color: #2563eb;"></i> Jadwal Posting &amp; Deadline Terdekat
                    </h3>
                    <a href="<?= site_url('administrator/tasks?view=monitoring'); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; font-weight: 500; font-size: 11px; color: #475569;">
                        Semua Task <i class="fa fa-arrow-right" style="margin-left: 2px;"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th>Target Tanggal</th>
                                <th>KOL &amp; Campaign</th>
                                <th>Task / VT</th>
                                <th>Platform</th>
                                <th>Status</th>
                                <th>Urgensi</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($upcoming_deadlines)): ?>
                                <tr>
                                    <td colspan="7" class="text-center" style="padding: 24px; color: #059669; font-weight: 500;">
                                        <i class="fa fa-check-circle" style="margin-right: 4px;"></i> Semua jadwal posting on-track, tidak ada tugas yang mendekati batas waktu atau terlambat.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($upcoming_deadlines as $d): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: #0f172a; white-space: nowrap;">
                                            <?= date('d M Y', strtotime($d->estimated_posting_date)); ?>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($d->kol_name); ?></div>
                                            <div style="font-size: 11px; color: #64748b;"><?= htmlspecialchars($d->campaign_name); ?></div>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('administrator/tasks/view/' . $d->id); ?>" style="color: #2563eb; font-weight: 500; text-decoration: none;">
                                                <?= htmlspecialchars($d->title); ?>
                                            </a>
                                        </td>
                                        <?php 
                                            $platform_lower = strtolower($d->platform_name ?: '');
                                            $plat_class = 'badge-soft-slate';
                                            if ($platform_lower === 'instagram') $plat_class = 'badge-platform-ig';
                                            if ($platform_lower === 'youtube') $plat_class = 'badge-platform-yt';
                                            if ($platform_lower === 'tiktok') $plat_class = 'badge-platform-tt';
                                        ?>
                                        <td><span class="badge-soft <?= $plat_class; ?>"><?= htmlspecialchars($d->platform_name ?: 'Medsos'); ?></span></td>
                                        <td>
                                            <span class="badge-soft badge-soft-slate">
                                                <?= str_replace('_', ' ', $d->status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($d->days_left < 0): ?>
                                                <span class="badge-soft badge-soft-rose">
                                                    <i class="fa fa-clock-o"></i> Overdue <?= abs($d->days_left); ?>H
                                                </span>
                                            <?php elseif ($d->days_left == 0): ?>
                                                <span class="badge-soft badge-soft-amber">
                                                    <i class="fa fa-exclamation-circle"></i> Hari Ini
                                                </span>
                                            <?php elseif ($d->days_left <= 3): ?>
                                                <span class="badge-soft badge-soft-amber"><?= $d->days_left; ?> hari lagi</span>
                                            <?php else: ?>
                                                <span class="badge-soft badge-soft-slate"><?= $d->days_left; ?> hari lagi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="<?= site_url('administrator/tasks/view/' . $d->id); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; font-size: 11px; color: #2563eb; font-weight: 500;">
                                                Detail &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN (col-md-4): Pipeline & Review Funnel + Konten Live + Kontrak Expired (jika ada) -->
        <div class="col-md-4">
            
            <!-- A. Alur Kerja Produksi (Sesuai Menu Sidebar Production) -->
            <?php
                $c_moni = (int)($prod_monitoring ?? $total_tasks ?? 0);
                $c_rev  = (int)($prod_review_draft ?? 0);
                $c_live = (int)($prod_live_posting ?? 0);
                $c_cal  = (int)($prod_schedule ?? 0);
            ?>
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-cogs" style="color: #2563eb;"></i> Alur Produksi (Production)
                    </h3>
                    <a href="<?= site_url('administrator/tasks?view=monitoring'); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; font-size: 11px; color: #475569;">
                        Lihat Alur <i class="fa fa-arrow-right" style="margin-left: 2px;"></i>
                    </a>
                </div>
                <div class="dash-card-body">
                    
                    <!-- Clean Vertical Step Timeline -->
                    <div class="funnel-timeline">
                        
                        <!-- Step 1: Monitoring Tugas & Brief -->
                        <a href="<?= site_url('administrator/tasks?view=monitoring'); ?>" class="funnel-step step-1" title="Buka Monitoring Tugas &amp; Brief">
                            <div class="funnel-step-left">
                                <span class="funnel-step-badge">1</span>
                                <div>
                                    <div class="funnel-step-name">Monitoring Tugas &amp; Brief</div>
                                    <div style="font-size: 11px; color: #94a3b8; font-weight: normal;">Seluruh penugasan &amp; brief KOL</div>
                                </div>
                            </div>
                            <div class="funnel-step-right">
                                <span class="funnel-step-count"><?= number_format($c_moni); ?></span>
                                <span class="funnel-step-pct">Tasks</span>
                            </div>
                        </a>

                        <!-- Step 2: Review Draft -->
                        <a href="<?= site_url('administrator/tasks?view=review_draft'); ?>" class="funnel-step step-2" title="Buka Review Draft">
                            <div class="funnel-step-left">
                                <span class="funnel-step-badge">2</span>
                                <div>
                                    <div class="funnel-step-name">Review Draft</div>
                                    <div style="font-size: 11px; color: #94a3b8; font-weight: normal;">Menunggu, review &amp; revisi draft</div>
                                </div>
                            </div>
                            <div class="funnel-step-right">
                                <span class="funnel-step-count" style="color: #4338ca;"><?= number_format($c_rev); ?></span>
                                <span class="funnel-step-pct" style="color: #4338ca; background: #e0e7ff;">Tasks</span>
                            </div>
                        </a>

                        <!-- Step 3: Live Posting -->
                        <a href="<?= site_url('administrator/tasks?view=live_posting'); ?>" class="funnel-step step-3" title="Buka Live Posting">
                            <div class="funnel-step-left">
                                <span class="funnel-step-badge">3</span>
                                <div>
                                    <div class="funnel-step-name">Live Posting</div>
                                    <div style="font-size: 11px; color: #94a3b8; font-weight: normal;">Draft disetujui &amp; proses tayang</div>
                                </div>
                            </div>
                            <div class="funnel-step-right">
                                <span class="funnel-step-count" style="color: #c2410c;"><?= number_format($c_live); ?></span>
                                <span class="funnel-step-pct" style="color: #c2410c; background: #fff7ed;">Tasks</span>
                            </div>
                        </a>

                        <!-- Step 4: Schedule Calendar -->
                        <a href="<?= site_url('administrator/kalender'); ?>" class="funnel-step step-4" title="Buka Schedule Calendar">
                            <div class="funnel-step-left">
                                <span class="funnel-step-badge">4</span>
                                <div>
                                    <div class="funnel-step-name">Schedule Calendar</div>
                                    <div style="font-size: 11px; color: #94a3b8; font-weight: normal;">Kalender jadwal tayang KOL</div>
                                </div>
                            </div>
                            <div class="funnel-step-right">
                                <span class="funnel-step-count" style="color: #0284c7;"><?= number_format($c_cal); ?></span>
                                <span class="funnel-step-pct" style="color: #0284c7; background: #e0f2fe;">Jadwal</span>
                            </div>
                        </a>

                    </div>

                </div>
            </div>

            <!-- B. Kontrak Akan Berakhir / Expired (HANYA DITAMPILKAN JIKA ADA DATA) -->
            <?php if (!empty($expiring_contracts)): ?>
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3 class="dash-card-title">
                            <i class="fa fa-file-text-o" style="color: #d97706;"></i> Kontrak Akan Berakhir
                        </h3>
                        <a href="<?= site_url('administrator/campaign_kol_contracts'); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; font-size: 11px;">
                            Semua <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="dash-card-body" style="padding-top: 10px; padding-bottom: 10px;">
                        <?php foreach ($expiring_contracts as $ec): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px;">
                                <div>
                                    <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($ec->kol_name); ?></div>
                                    <div style="font-size: 11px; color: #64748b;"><?= htmlspecialchars($ec->contract_no); ?> &bull; <?= htmlspecialchars($ec->campaign_name); ?></div>
                                </div>
                                <div style="text-align: right;">
                                    <?php if ($ec->days_left < 0): ?>
                                        <span class="badge-soft badge-soft-rose">Expired <?= abs($ec->days_left); ?>H lalu</span>
                                    <?php elseif ($ec->days_left == 0): ?>
                                        <span class="badge-soft badge-soft-amber">Hari Ini</span>
                                    <?php else: ?>
                                        <span class="badge-soft badge-soft-slate"><?= $ec->days_left; ?> hari lagi</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- C. Konten Live Terbaru (Activity Feed) -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-video-camera" style="color: #059669;"></i> Konten Live Terbaru
                    </h3>
                    <a href="<?= site_url('administrator/postings'); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; font-size: 11px; color: #475569;">
                        Semua <i class="fa fa-arrow-right" style="margin-left: 2px;"></i>
                    </a>
                </div>
                <div class="dash-card-body" style="padding-top: 10px; padding-bottom: 10px;">
                    <?php if (empty($recent_postings)): ?>
                        <div style="padding: 16px 0; text-align: center; color: #94a3b8; font-size: 12px;">
                            Belum ada konten live posting.
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_postings as $p): ?>
                            <div class="live-feed-item">
                                <div style="flex: 1; min-width: 0; padding-right: 8px;">
                                    <div style="font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($p->kol_name ?: 'KOL'); ?>
                                    </div>
                                    <div style="font-size: 11px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($p->task_title); ?>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                    <?php 
                                        $platform_lower = strtolower($p->platform_name ?: '');
                                        $plat_class = 'badge-soft-slate';
                                        if ($platform_lower === 'instagram') $plat_class = 'badge-platform-ig';
                                        if ($platform_lower === 'youtube') $plat_class = 'badge-platform-yt';
                                        if ($platform_lower === 'tiktok') $plat_class = 'badge-platform-tt';
                                    ?>
                                    <span class="badge-soft <?= $plat_class; ?>" style="font-size: 10px;"><?= htmlspecialchars($p->platform_name ?: 'Medsos'); ?></span>
                                    <?php if ($p->live_url): ?>
                                        <a href="<?= htmlspecialchars($p->live_url); ?>" target="_blank" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; padding: 2px 6px; font-size: 10px;" title="Buka Konten">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

</section>
