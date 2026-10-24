<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>

<style>
  .finance-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
  }
  @media (max-width: 991px) {
    .finance-kpi-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
    }
  }
  @media (max-width: 767px) {
    .finance-kpi-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-bottom: 16px;
    }
  }
  .finance-stat-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 16px 18px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
    border: 1px solid #e2e8f0;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 100px;
  }
  .finance-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
  }
  .finance-stat-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
  }
  .finance-stat-label {
    font-size: 11.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #64748b;
    line-height: 1.35;
    margin: 0;
  }
  .finance-stat-icon-wrap {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
  }
  .finance-stat-body {
    margin-top: auto;
  }
  .finance-stat-value {
    font-size: 19px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    letter-spacing: -0.01em;
  }
  .finance-stat-sub {
    font-size: 11.5px;
    font-weight: 500;
    color: #64748b;
    margin-top: 3px;
  }
  .table-finance th {
    background-color: #f8fafc;
    color: #334155;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0 !important;
    vertical-align: middle !important;
  }
  .table-finance td {
    vertical-align: middle !important;
    font-size: 13px;
  }
  .kol-avatar-badge {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #eff6ff;
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin-right: 8px;
    font-size: 14px;
  }
  .badge-status {
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    display: inline-block;
    letter-spacing: 0.3px;
  }
  .badge-contract-draft { background-color: #fef3c7; color: #92400e; }
  .badge-contract-signed { background-color: #dcfce7; color: #166534; }
  .badge-contract-waiting { background-color: #ffe4e6; color: #9f1239; }
  .badge-inv-unpaid { background-color: #fee2e2; color: #991b1b; }
  .badge-inv-partial { background-color: #e0f2fe; color: #075985; }
  .badge-inv-paid { background-color: #dcfce7; color: #166534; }
  .badge-inv-none { background-color: #f1f5f9; color: #64748b; }
  .payment-progress {
    height: 6px;
    border-radius: 3px;
    background: #e2e8f0;
    margin-top: 4px;
    overflow: hidden;
  }
  .payment-progress-bar {
    height: 100%;
    background: #10b981;
    border-radius: 3px;
  }
  
  /* Modern Action Group & Buttons */
  .action-cell-group {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
  }
  .btn-action-pay {
    background: #059669;
    color: #ffffff !important;
    border: 1px solid #047857;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(5, 150, 105, 0.25);
    transition: all 0.15s ease;
    cursor: pointer;
  }
  .btn-action-pay:hover {
    background: #047857;
    box-shadow: 0 3px 8px rgba(5, 150, 105, 0.35);
    transform: translateY(-1px);
  }
  .btn-action-create {
    background: #f8fafc;
    color: #2563eb !important;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s ease;
    text-decoration: none !important;
  }
  .btn-action-create:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    color: #1d4ed8 !important;
    transform: translateY(-1px);
  }
  .btn-action-sign {
    background: #2563eb;
    color: #ffffff !important;
    border: 1px solid #1d4ed8;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.25);
    transition: all 0.15s ease;
    cursor: pointer;
  }
  .btn-action-sign:hover {
    background: #1d4ed8;
    box-shadow: 0 3px 8px rgba(37, 99, 235, 0.35);
    transform: translateY(-1px);
  }
  .btn-action-pending {
    background: #fef3c7;
    color: #b45309 !important;
    border: 1px solid #fde68a;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: default;
  }
  .badge-action-done {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    padding: 5px 10px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    letter-spacing: 0.3px;
  }
  .btn-action-invoice {
    background: #0284c7;
    color: #ffffff !important;
    border: 1px solid #0369a1;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(2, 132, 199, 0.25);
    transition: all 0.15s ease;
    cursor: pointer;
  }
  .btn-action-invoice:hover {
    background: #0369a1;
    box-shadow: 0 3px 8px rgba(2, 132, 199, 0.35);
    transform: translateY(-1px);
  }
  .btn-doc-icon {
    width: 31px;
    height: 31px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    transition: all 0.15s ease;
    text-decoration: none !important;
  }
  .btn-doc-pdf {
    background: #fef2f2;
    color: #dc2626 !important;
    border: 1px solid #fee2e2;
  }
  .btn-doc-pdf:hover {
    background: #dc2626;
    color: #ffffff !important;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
  }
  .btn-doc-inv {
    background: #f0f9ff;
    color: #0284c7 !important;
    border: 1px solid #e0f2fe;
  }
  .btn-doc-inv:hover {
    background: #0284c7;
    color: #ffffff !important;
    border-color: #0284c7;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
  }

  /* Filter Tabs Styling for Finance Hub (Soft Badges / Low Opacity Style) */
  #finance_hub_tabs {
    margin-bottom: 20px;
    background: #f8fafc;
    padding: 6px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }
  #finance_hub_tabs > li {
    margin: 0 !important;
  }
  #finance_hub_tabs > li > a {
    border-radius: 6px;
    font-weight: 600;
    font-size: 12.5px;
    padding: 8px 16px;
    border: 1px solid transparent;
    transition: all 0.15s ease-in-out;
    background: transparent;
  }

  /* 1. Tab: Semua Transaksi - Soft Slate / Blue */
  #finance_hub_tabs > li > a.tab-all,
  #finance_hub_tabs > li > a[data-filter-status="all"] {
    color: #475569;
  }
  #finance_hub_tabs > li > a.tab-all:hover,
  #finance_hub_tabs > li > a[data-filter-status="all"]:hover {
    background: #f1f5f9;
    color: #1e293b;
  }
  #finance_hub_tabs > li.active > a.tab-all,
  #finance_hub_tabs > li.active > a[data-filter-status="all"],
  #finance_hub_tabs > li.active > a.tab-all:hover,
  #finance_hub_tabs > li.active > a[data-filter-status="all"]:hover,
  #finance_hub_tabs > li.active > a.tab-all:focus,
  #finance_hub_tabs > li.active > a[data-filter-status="all"]:focus {
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    border-color: #bfdbfe !important;
    box-shadow: 0 1px 3px rgba(29, 78, 216, 0.08);
  }

  /* 2. Tab: Tagihan Masuk (Review Pajak) - Soft Amber */
  #finance_hub_tabs > li > a.tab-submitted,
  #finance_hub_tabs > li > a[data-filter-status="SUBMITTED"] {
    color: #b45309;
  }
  #finance_hub_tabs > li > a.tab-submitted:hover,
  #finance_hub_tabs > li > a[data-filter-status="SUBMITTED"]:hover {
    background: #fffbeb;
    color: #92400e;
  }
  #finance_hub_tabs > li.active > a.tab-submitted,
  #finance_hub_tabs > li.active > a[data-filter-status="SUBMITTED"],
  #finance_hub_tabs > li.active > a.tab-submitted:hover,
  #finance_hub_tabs > li.active > a[data-filter-status="SUBMITTED"]:hover,
  #finance_hub_tabs > li.active > a.tab-submitted:focus,
  #finance_hub_tabs > li.active > a[data-filter-status="SUBMITTED"]:focus {
    background: #fffbeb !important;
    color: #b45309 !important;
    border-color: #fde68a !important;
    box-shadow: 0 1px 3px rgba(180, 83, 9, 0.08);
  }

  /* 3. Tab: Siap Dibayar - Soft Sky Blue */
  #finance_hub_tabs > li > a.tab-ready,
  #finance_hub_tabs > li > a[data-filter-status="READY"] {
    color: #0284c7;
  }
  #finance_hub_tabs > li > a.tab-ready:hover,
  #finance_hub_tabs > li > a[data-filter-status="READY"]:hover {
    background: #f0f9ff;
    color: #0369a1;
  }
  #finance_hub_tabs > li.active > a.tab-ready,
  #finance_hub_tabs > li.active > a[data-filter-status="READY"],
  #finance_hub_tabs > li.active > a.tab-ready:hover,
  #finance_hub_tabs > li.active > a[data-filter-status="READY"]:hover,
  #finance_hub_tabs > li.active > a.tab-ready:focus,
  #finance_hub_tabs > li.active > a[data-filter-status="READY"]:focus {
    background: #f0f9ff !important;
    color: #0284c7 !important;
    border-color: #bae6fd !important;
    box-shadow: 0 1px 3px rgba(2, 132, 199, 0.08);
  }

  /* 4. Tab: Lunas & Bukti Potong - Soft Emerald Green */
  #finance_hub_tabs > li > a.tab-paid,
  #finance_hub_tabs > li > a[data-filter-status="PAID"] {
    color: #15803d;
  }
  #finance_hub_tabs > li > a.tab-paid:hover,
  #finance_hub_tabs > li > a[data-filter-status="PAID"]:hover {
    background: #f0fdf4;
    color: #166534;
  }
  #finance_hub_tabs > li.active > a.tab-paid,
  #finance_hub_tabs > li.active > a[data-filter-status="PAID"],
  #finance_hub_tabs > li.active > a.tab-paid:hover,
  #finance_hub_tabs > li.active > a[data-filter-status="PAID"]:hover,
  #finance_hub_tabs > li.active > a.tab-paid:focus,
  #finance_hub_tabs > li.active > a[data-filter-status="PAID"]:focus {
    background: #ecfdf5 !important;
    color: #15803d !important;
    border-color: #a7f3d0 !important;
    box-shadow: 0 1px 3px rgba(21, 128, 61, 0.08);
  }

  /* ==========================================================================
     MOBILE (< 768px)
     ========================================================================== */
  @media (max-width: 767px) {
    /* KPI 2-up grid */
    .finance-stat-card {
      padding: 12px 12px !important;
      min-height: 88px !important;
      border-radius: 8px !important;
    }
    .finance-stat-header {
      margin-bottom: 6px !important;
    }
    .finance-stat-label {
      font-size: 10px !important;
      letter-spacing: 0.2px !important;
      line-height: 1.25 !important;
    }
    .finance-stat-icon-wrap {
      width: 26px !important;
      height: 26px !important;
      font-size: 12px !important;
      border-radius: 6px !important;
    }
    .finance-stat-value {
      font-size: 14.5px !important;
      line-height: 1.25 !important;
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
    }
    .finance-stat-sub {
      font-size: 10.5px !important;
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
    }

    /* Tabel Finance Hub: Scroll alami tanpa sticky yang merusak */
    .table-finance {
      min-width: 860px !important;
      width: max-content !important;
    }
    .table-finance th,
    .table-finance td {
      white-space: nowrap !important;
      position: static !important;
    }
    .table-finance th:first-child,
    .table-finance td:first-child {
      position: static !important;
      left: auto !important;
      box-shadow: none !important;
      white-space: normal !important;
      min-width: 200px !important;
      max-width: 240px !important;
    }

    /* Tab filter: boleh scroll horizontal, bukan memaksa wrap tinggi */
    #finance_hub_tabs {
      display: flex !important;
      flex-wrap: nowrap !important;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 4px;
    }
    #finance_hub_tabs > li {
      flex: 0 0 auto;
    }

    /* Kolom filter/search jadi 2-up */
    .finance-hub-filter > [class*="col-"] {
      float: left !important;
      width: 50% !important;
      padding-left: 5px !important;
      padding-right: 5px !important;
      margin-bottom: 8px !important;
    }
    .finance-hub-filter > .col-md-8,
    .finance-hub-filter > .col-md-10,
    .finance-hub-filter > .col-md-12 {
      width: 100% !important;
      float: none !important;
    }

    /* Modal kontrak (mobile): kunci mirip kertas desktop, boleh scroll horizontal */
    #modal_sign_contract .modal-body { padding: 12px !important; }
    #modal_sign_contract .contract-paper-scroll {
      padding: 12px 8px !important;
      max-height: 70vh !important;
      justify-content: flex-start !important;
    }
    #modal_contract_html_preview,
    #kol_contract_preview {
      min-width: 620px !important;
      width: 620px !important;
      max-width: none !important;
      margin: 0 auto !important;
      padding: 30px 25px !important;
      font-size: 11.5px !important;
      line-height: 1.5 !important;
      flex-shrink: 0 !important;
    }
    /* Blok tanda tangan tetap 2 kolom berdampingan seperti kertas */
    #preview-signature-box table { width: 100% !important; table-layout: fixed !important; }
    #preview-signature-box table td {
      display: table-cell !important;
      width: 50% !important;
      text-align: center !important;
      vertical-align: top !important;
      padding: 0 12px !important;
    }
  }
</style>

<section class="content-header">
   <h1 style="font-weight: 700; color: #0f172a;">
      Finance Hub <small style="color: #64748b;">Manajemen Transaksi, Kontrak, Invoice & Pembayaran</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Finance Hub</li>
   </ol>
</section>

<!-- Main content -->
<section class="content">
   
   <!-- TOP SUMMARY KPI CARDS -->
   <div class="finance-kpi-grid">
      <div class="finance-stat-card">
         <div class="finance-stat-header">
            <div class="finance-stat-label">Total Deals Active</div>
            <div class="finance-stat-icon-wrap" style="background: #eff6ff; color: #2563eb;">
               <i class="fa fa-handshake-o"></i>
            </div>
         </div>
         <div class="finance-stat-body">
            <div class="finance-stat-value" style="color: #1e293b;"><?= number_format($stats['total_deals']); ?></div>
            <div class="finance-stat-sub">KOL Deals</div>
         </div>
      </div>
      <div class="finance-stat-card">
         <div class="finance-stat-header">
            <div class="finance-stat-label">Committed Deal Value</div>
            <div class="finance-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
               <i class="fa fa-money"></i>
            </div>
         </div>
         <div class="finance-stat-body">
            <div class="finance-stat-value" style="color: #d97706;">Rp <?= number_format($stats['total_deal_amount'], 0, ',', '.'); ?></div>
            <div class="finance-stat-sub">Nilai Komitmen</div>
         </div>
      </div>
      <div class="finance-stat-card">
         <div class="finance-stat-header">
            <div class="finance-stat-label">Total Invoiced</div>
            <div class="finance-stat-icon-wrap" style="background: #f0f9ff; color: #0284c7;">
               <i class="fa fa-file-text-o"></i>
            </div>
         </div>
         <div class="finance-stat-body">
            <div class="finance-stat-value" style="color: #0284c7;">Rp <?= number_format($stats['total_invoiced'], 0, ',', '.'); ?></div>
            <div class="finance-stat-sub">Total Ditagihkan</div>
         </div>
      </div>
      <div class="finance-stat-card">
         <div class="finance-stat-header">
            <div class="finance-stat-label">Realized Paid</div>
            <div class="finance-stat-icon-wrap" style="background: #ecfdf5; color: #059669;">
               <i class="fa fa-check-circle"></i>
            </div>
         </div>
         <div class="finance-stat-body">
            <div class="finance-stat-value" style="color: #059669;">Rp <?= number_format($stats['total_paid'], 0, ',', '.'); ?></div>
            <div class="finance-stat-sub">Kas Keluar Terbayar</div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box" style="border: 0; border-radius: 10px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);">
            <div class="box-body" style="padding: 24px;">
               
               <div class="mbar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                  <div>
                     <h3 style="margin: 0; color: #0f172a; font-size: 18px; font-weight: 700;">Daftar Transaksi Finance</h3>
                     <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Kelola alur legalitas kontrak, tagihan invoice, dan bukti transfer dalam 1 alur terpadu.</p>
                  </div>

                  <form name="form_finance_hub" id="form_finance_hub" action="<?= base_url('administrator/finance_hub/index'); ?>" method="GET" style="margin: 0; flex: 1 1 100%;">
                     <div style="display: flex; gap: 8px;">
                        <input type="text" class="form-control" name="q" id="filter" placeholder="Cari KOL, Campaign, No Kontrak/Inv..." value="<?= $this->input->get('q'); ?>" style="border-radius: 6px; width: 100%; font-size: 13px;">
                        <button type="submit" class="btn btn-primary btn-flat" style="border-radius: 6px; font-weight: 600; padding: 6px 14px;">
                           <i class="fa fa-search"></i> Cari
                        </button>
                        <?php if ($this->input->get('q')): ?>
                           <a href="<?= base_url('administrator/finance_hub/index'); ?>" class="btn btn-default btn-flat" style="border-radius: 6px;" title="Reset Filter"><i class="fa fa-times"></i></a>
                        <?php endif; ?>
                     </div>
                  </form>
               </div>

                <!-- FILTER TABS -->
                <ul class="nav nav-pills" id="finance_hub_tabs">
                   <li class="active">
                      <a href="javascript:void(0)" data-filter-status="all" class="tab-all">
                         <i class="fa fa-th-list"></i> Semua Transaksi (<?= $stats['total_deals']; ?>)
                      </a>
                   </li>
                   <li>
                      <a href="javascript:void(0)" data-filter-status="SUBMITTED" class="tab-submitted">
                         <i class="fa fa-clock-o"></i> Tagihan Masuk (Review Pajak) (<?= $stats['count_submitted']; ?>)
                      </a>
                   </li>
                   <li>
                      <a href="javascript:void(0)" data-filter-status="READY" class="tab-ready">
                         <i class="fa fa-credit-card"></i> Siap Dibayar (<?= $stats['count_approved']; ?>)
                      </a>
                   </li>
                   <li>
                      <a href="javascript:void(0)" data-filter-status="PAID" class="tab-paid">
                         <i class="fa fa-check-circle"></i> Lunas &amp; Bukti Potong (<?= $stats['count_paid']; ?>)
                      </a>
                   </li>
                </ul>

                <div class="table-responsive mscroll">
                   <table class="table table-hover table-finance" style="margin-bottom: 0;">
                      <thead>
                         <tr>
                            <th>KOL &amp; Campaign</th>
                            <th>Nominal Deal</th>
                            <th>Status Kontrak</th>
                            <th>Status Invoice</th>
                            <th>Realisasi Pembayaran</th>
                            <th>Dokumen</th>
                            <th style="text-align: right;">Aksi Cepat</th>
                         </tr>
                      </thead>
                      <tbody id="tbody_finance_hub">
                         <?php foreach($deals as $deal): ?>
                            <?php 
                               $initial = strtoupper(substr($deal->kol_name ?? 'K', 0, 1)); 
                               $total_paid = floatval($deal->total_paid);
                               $grand_total = floatval($deal->grand_total);
                               $pct_paid = ($grand_total > 0) ? min(100, round(($total_paid / $grand_total) * 100)) : 0;
                               
                               $avatar_url = '';
                               if (!empty($deal->kol_avatar)) {
                                  if (filter_var($deal->kol_avatar, FILTER_VALIDATE_URL)) {
                                     $avatar_url = $deal->kol_avatar;
                                  } else {
                                     $avatar_url = BASE_URL . 'uploads/kols/' . $deal->kol_avatar;
                                  }
                               }
                            ?>
                            <tr class="deal-row" data-invoice-status="<?= $deal->invoice_status ?: 'NONE'; ?>">
                               <!-- KOL & CAMPAIGN -->
                               <td>
                                  <div style="display: flex; align-items: center;">
                                     <?php if ($avatar_url): ?>
                                        <img src="<?= $avatar_url ?>" alt="<?= htmlspecialchars($deal->kol_name); ?>" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; margin-right: 10px; border: 1px solid #e2e8f0;">
                                     <?php else: ?>
                                        <div class="kol-avatar-badge"><?= $initial ?></div>
                                     <?php endif; ?>
                                     <div>
                                        <strong style="color: #0f172a; font-size: 14px; display: block;"><?= htmlspecialchars($deal->kol_name); ?></strong>
                                        <span style="color: #64748b; font-size: 12px;"><i class="fa fa-bullhorn" style="color: #94a3b8; margin-right: 4px;"></i><?= htmlspecialchars($deal->campaign_name); ?></span>
                                     </div>
                                  </div>
                               </td>

                               <!-- NOMINAL DEAL -->
                               <td>
                                  <strong style="color: #0f172a; font-size: 14px;">Rp <?= number_format($deal->deal_amount, 0, ',', '.'); ?></strong>
                                  <div style="font-size: 11px; color: #64748b;">Committed Budget</div>
                                </td>
                               
                               <!-- STATUS KONTRAK -->
                               <td>
                                   <?php if ($deal->contract_id): ?>
                                      <?php if ($deal->contract_status == 'SIGNED' || $deal->contract_status == 'ACTIVE'): ?>
                                         <span class="badge-status badge-contract-signed"><i class="fa fa-check-circle"></i> ACTIVE / SIGNED</span>
                                      <?php elseif ($deal->contract_status == 'PENDING' || $deal->contract_status == 'WAITING_KOL_SIGNATURE'): ?>
                                         <span class="badge-status badge-contract-waiting" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;"><i class="fa fa-clock-o"></i> PENDING KOL SIGN</span>
                                      <?php else: ?>
                                         <span class="badge-status badge-contract-draft"><i class="fa fa-pencil"></i> DRAFT</span>
                                      <?php endif; ?>
                                      <div style="font-size: 11px; color: #64748b; margin-top: 2px; font-family: monospace;"><?= htmlspecialchars($deal->contract_no ?? ''); ?></div>
                                   <?php else: ?>
                                      <span class="badge-status badge-inv-none"><i class="fa fa-minus"></i> TANPA KONTRAK</span>
                                   <?php endif; ?>
                               </td>

                               <!-- STATUS INVOICE -->
                               <td>
                                  <?php if ($deal->invoice_id): ?>
                                     <?php if ($deal->invoice_status == 'PAID'): ?>
                                        <span class="badge-status badge-inv-paid"><i class="fa fa-check"></i> LUNAS</span>
                                     <?php elseif ($deal->invoice_status == 'PARTIAL'): ?>
                                        <span class="badge-status badge-inv-partial"><i class="fa fa-adjust"></i> PARTIAL</span>
                                     <?php elseif ($deal->invoice_status == 'APPROVED'): ?>
                                        <span class="badge-status" style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;"><i class="fa fa-check-circle-o"></i> SIAP BAYAR</span>
                                     <?php elseif ($deal->invoice_status == 'SUBMITTED'): ?>
                                        <span class="badge-status" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;"><i class="fa fa-clock-o"></i> REVIEW PAJAK</span>
                                     <?php elseif ($deal->invoice_status == 'REJECTED'): ?>
                                        <span class="badge-status" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca;"><i class="fa fa-times-circle"></i> DITOLAK</span>
                                     <?php else: ?>
                                        <span class="badge-status badge-inv-unpaid"><?= $deal->invoice_status; ?></span>
                                     <?php endif; ?>
                                     <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                        Net Payable: <strong>Rp <?= number_format($deal->grand_total, 0, ',', '.') ?></strong>
                                     </div>
                                     <?php if ($deal->tax_amount > 0): ?>
                                        <div style="font-size: 10px; color: #dc2626;">
                                           Pot. <?= $deal->tax_type ?: 'PPh'; ?> (<?= (float)$deal->tax_rate_percent; ?>%): -Rp <?= number_format($deal->tax_amount, 0, ',', '.'); ?>
                                        </div>
                                     <?php endif; ?>
                                  <?php else: ?>
                                     <?php if ($deal->contract_id && in_array($deal->contract_status, ['SIGNED', 'ACTIVE'])): ?>
                                        <span class="badge-status badge-inv-none" style="background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1;"><i class="fa fa-hourglass-start"></i> Menunggu Tagihan KOL</span>
                                     <?php else: ?>
                                        <span class="badge-status badge-inv-none"><i class="fa fa-minus"></i> BELUM DITAGIH</span>
                                     <?php endif; ?>
                                  <?php endif; ?>
                               </td>
                               
                               <!-- REALISASI PEMBAYARAN -->
                               <td style="min-width: 160px;">
                                  <?php if ($deal->invoice_id): ?>
                                     <div style="display: flex; justify-content: space-between; font-size: 12px;">
                                        <span>Terbayar: <strong>Rp <?= number_format($total_paid, 0, ',', '.') ?></strong></span>
                                        <span style="color: #64748b; font-weight: 600;"><?= $pct_paid ?>%</span>
                                     </div>
                                     <div class="payment-progress">
                                        <div class="payment-progress-bar" style="width: <?= $pct_paid ?>%;"></div>
                                     </div>
                                     <?php if ($grand_total > $total_paid): ?>
                                        <div style="font-size: 11px; color: #e11d48; margin-top: 2px;">
                                           Sisa: Rp <?= number_format($grand_total - $total_paid, 0, ',', '.') ?>
                                        </div>
                                     <?php else: ?>
                                        <div style="font-size: 11px; color: #059669; margin-top: 2px;">Lunas 100%</div>
                                     <?php endif; ?>
                                  <?php else: ?>
                                     <span style="color: #94a3b8; font-size: 12px;">-</span>
                                  <?php endif; ?>
                                </td>

                               <!-- DOKUMEN -->
                               <td style="white-space: nowrap;">
                                  <div style="display: inline-flex; gap: 6px; align-items: center;">
                                     <?php if (!empty($deal->contract_id)): ?>
                                        <a href="<?= site_url('administrator/campaign_kol_contracts/download_pdf/' . $deal->contract_id) ?>" target="_blank" class="btn-doc-icon btn-doc-pdf" title="Lihat / Unduh Surat Kontrak PDF">
                                           <i class="fa fa-file-pdf-o"></i>
                                        </a>
                                     <?php endif; ?>
                                     <?php if (!empty($deal->invoice_id)): ?>
                                        <a href="<?= site_url('administrator/invoices/print_pdf/' . $deal->invoice_id) ?>" target="_blank" class="btn-doc-icon btn-doc-inv" title="Lihat / Cetak Invoice PDF">
                                           <i class="fa fa-file-text-o"></i>
                                        </a>
                                     <?php endif; ?>
                                     <?php if (empty($deal->contract_id) && empty($deal->invoice_id)): ?>
                                        <span class="text-muted" style="font-size: 12px;">-</span>
                                     <?php endif; ?>
                                  </div>
                               </td>

                               <!-- AKSI CEPAT -->
                                <td style="text-align: right; white-space: nowrap; min-width: 140px;">
                                   <div class="action-cell-group">
                                   <?php if ($deal->invoice_id && $deal->invoice_status === 'SUBMITTED'): ?>
                                      <!-- Tagihan Masuk: Perlu Review & Verifikasi Pajak -->
                                      <button type="button" class="btn-action-pay btn-open-review-invoice"
                                              data-invoice-id="<?= $deal->invoice_id ?>"
                                              data-kol-name="<?= htmlspecialchars($deal->kol_name ?? '') ?>"
                                              data-campaign-name="<?= htmlspecialchars($deal->campaign_name ?? '') ?>"
                                              data-invoice-no="<?= htmlspecialchars($deal->invoice_no ?? '') ?>"
                                              data-subtotal="<?= (float)$deal->subtotal ?>"
                                              data-tax-type="<?= htmlspecialchars($deal->tax_type ?: 'PPH21') ?>"
                                              data-tax-rate="<?= (float)($deal->tax_rate_percent > 0 ? $deal->tax_rate_percent : (!empty($deal->kol_npwp_number) ? 2.50 : 3.00)) ?>"
                                              data-npwp="<?= htmlspecialchars($deal->kol_npwp_number ?? '') ?>"
                                              data-ktp="<?= htmlspecialchars($deal->kol_ktp_number ?? '') ?>"
                                              data-bank-name="<?= htmlspecialchars($deal->kol_bank_name ?? '') ?>"
                                              data-bank-account="<?= htmlspecialchars($deal->kol_bank_account_number ?? '') ?>"
                                              data-bank-account-name="<?= htmlspecialchars($deal->kol_bank_account_name ?? '') ?>"
                                              data-invoice-file="<?= htmlspecialchars($deal->invoice_file ?? '') ?>"
                                              style="background: #0284c7;" title="Review dan sesuaikan potongan PPh sebelum menyetujui">
                                         <i class="fa fa-check-square-o"></i> Review &amp; Pajak
                                      </button>

                                   <?php elseif ($deal->invoice_id && in_array($deal->invoice_status, ['APPROVED', 'PARTIAL'])): ?>
                                      <!-- Tagihan Disetujui: Siap Dicatat Pembayaran -->
                                      <button type="button" class="btn-action-pay btn-open-payment-modal"
                                              data-invoice-id="<?= $deal->invoice_id ?>"
                                              data-kol-name="<?= htmlspecialchars($deal->kol_name ?? '') ?>"
                                              data-campaign-name="<?= htmlspecialchars($deal->campaign_name ?? '') ?>"
                                              data-invoice-no="<?= htmlspecialchars($deal->invoice_no ?? '') ?>"
                                              data-balance="<?= max(0, $grand_total - $total_paid) ?>"
                                              data-bank-name="<?= htmlspecialchars($deal->kol_bank_name ?? '') ?>"
                                              data-bank-account="<?= htmlspecialchars($deal->kol_bank_account_number ?? '') ?>"
                                              data-bank-account-name="<?= htmlspecialchars($deal->kol_bank_account_name ?? '') ?>">
                                         <i class="fa fa-credit-card"></i> Bayar Invoice
                                      </button>

                                   <?php elseif ($deal->invoice_id && $deal->invoice_status === 'PAID'): ?>
                                      <!-- Invoice Lunas: Upload/Lihat Bukti Potong -->
                                      <?php if (!empty($deal->bupot_file)): ?>
                                         <a href="<?= base_url($deal->bupot_file); ?>" target="_blank" class="btn btn-xs btn-success" style="border-radius: 4px; font-weight: 600; padding: 4px 8px;" title="Bukti Potong No: <?= htmlspecialchars($deal->bupot_no ?? ''); ?>">
                                            <i class="fa fa-certificate"></i> Bupot
                                         </a>
                                      <?php else: ?>
                                         <button type="button" class="btn btn-xs btn-warning btn-open-bupot-modal"
                                                 data-invoice-id="<?= $deal->invoice_id ?>"
                                                 data-invoice-no="<?= htmlspecialchars($deal->invoice_no ?? '') ?>"
                                                 data-kol-name="<?= htmlspecialchars($deal->kol_name ?? '') ?>"
                                                 data-tax-type="<?= htmlspecialchars($deal->tax_type ?? 'PPh') ?>"
                                                 data-tax-amount="<?= number_format($deal->tax_amount, 0, ',', '.') ?>"
                                                 style="border-radius: 4px; font-weight: 600; padding: 4px 8px;">
                                            <i class="fa fa-certificate"></i> Upload Bupot
                                         </button>
                                      <?php endif; ?>

                                   <?php elseif ($deal->invoice_id && $deal->invoice_status === 'REJECTED'): ?>
                                      <!-- Invoice Ditolak -->
                                      <span class="badge-action-done" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;" title="<?= htmlspecialchars($deal->rejection_reason ?? ''); ?>">
                                         <i class="fa fa-times-circle"></i> Ditolak
                                      </span>

                                   <?php elseif ($deal->contract_id && in_array($deal->contract_status, ['SIGNED', 'ACTIVE']) && !$deal->invoice_id): ?>
                                      <!-- Kontrak Aktif: Menunggu Tagihan dari KOL -->
                                      <div class="btn-action-pending" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 11px; padding: 4px 8px; border-radius: 4px;" title="Kontrak telah aktif. Menunggu KOL menerbitkan dan mengajukan tagihan melalui Portal KOL.">
                                         <i class="fa fa-hourglass-start"></i> Tunggu Tagihan KOL
                                      </div>

                                   <?php elseif ($deal->contract_id && in_array($deal->contract_status, ['PENDING', 'WAITING_KOL_SIGNATURE'])): ?>
                                      <!-- Kontrak Menunggu TTD KOL -->
                                      <div class="btn-action-pending" title="Kontrak telah ditandatangani Admin, menunggu tanda tangan KOL">
                                         <i class="fa fa-clock-o"></i> Menunggu KOL
                                      </div>

                                   <?php elseif ($deal->contract_id): ?>
                                      <!-- Kontrak Draft (Admin belum TTD) -->
                                      <button class="btn-action-sign" onclick="openSignModal(<?= $deal->contract_id; ?>)">
                                         <i class="fa fa-pencil-square-o"></i> Sign Kontrak
                                      </button>

                                    <?php else: ?>
                                       <!-- Belum ada kontrak: buat DRAFT satu klik, tetap di Finance Hub -->
                                       <button type="button" class="btn-action-create btn-create-contract" data-campaign-kol-id="<?= $deal->campaign_kol_id; ?>">
                                          <i class="fa fa-plus-circle"></i> Buat Kontrak
                                       </button>
                                    <?php endif; ?>
                                   </div>
                                </td>
                            </tr>
                         <?php endforeach; ?>
                        
                        <?php if (empty($deals)): ?>
                           <tr>
                              <td colspan="7" class="text-center" style="padding: 40px; color: #94a3b8;">
                                 <i class="fa fa-folder-open-o" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                                 Belum ada data transaksi finance. Tambahkan Deal KOL di modul Dealing.
                              </td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL CATAT PEMBAYARAN -->
<div class="modal fade" id="modalRecordPayment" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content" style="border-radius: 8px; border: 0; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
         <div class="modal-header" style="background-color: #0f172a; color: #fff; border-top-left-radius: 8px; border-top-right-radius: 8px; padding: 14px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="font-weight: 700; font-size: 16px;"><i class="fa fa-credit-card" style="margin-right: 6px; color: #38bdf8;"></i> Catat Pembayaran Invoice</h4>
         </div>
         <form id="formRecordPayment" enctype="multipart/form-data">
            <div class="modal-body" style="padding: 20px;">
               <input type="hidden" name="invoice_id" id="modal_payment_invoice_id">
               
               <!-- SUMMARY INFO BOX -->
               <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 16px; font-size: 13px;">
                  <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                     <span style="color: #64748b;">KOL & Campaign:</span>
                     <strong style="color: #0f172a;" id="modal_payment_kol_campaign">-</strong>
                  </div>
                  <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                     <span style="color: #64748b;">No. Invoice:</span>
                     <strong style="color: #0f172a; font-family: monospace;" id="modal_payment_invoice_no">-</strong>
                  </div>
                  <div style="display: flex; justify-content: space-between;">
                     <span style="color: #64748b;">Sisa Unpaid Balance:</span>
                     <strong style="color: #e11d48;" id="modal_payment_balance_text">Rp 0</strong>
                  </div>
               </div>
               
               <!-- REKENING INFO BOX -->
               <div id="modal_payment_bank_info_box" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-left: 4px solid #10b981; border-radius: 6px; padding: 12px; margin-bottom: 16px; font-size: 13px;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                     <span style="color: #065f46; font-weight: 600;">Tujuan Transfer (Bank KOL):</span>
                  </div>
                  <div style="color: #064e3b; margin-top: 6px;">
                     Bank: <strong id="modal_payment_bank_name">-</strong><br>
                     No. Rekening: <strong id="modal_payment_bank_account" style="font-family: monospace; font-size: 14px;">-</strong> 
                     <button type="button" class="btn btn-xs btn-default" onclick="copyBankAccount()" style="padding: 1px 6px; border-radius: 4px; margin-left: 6px; border: 1px solid #a7f3d0;" title="Copy Nomor Rekening"><i class="fa fa-copy" style="color: #10b981;"></i></button><br>
                     A.n: <strong id="modal_payment_bank_account_name">-</strong>
                  </div>
               </div>
               
               <!-- REKENING WARNING BOX -->
               <div id="modal_payment_bank_warning_box" style="display: none; background-color: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #ef4444; border-radius: 6px; padding: 12px; margin-bottom: 16px; font-size: 13px;">
                  <span style="color: #991b1b; font-weight: 600;"><i class="fa fa-exclamation-triangle"></i> Rekening belum diisi oleh KOL.</span>
                  <div style="color: #7f1d1d; margin-top: 4px;">Silakan hubungi KOL untuk melengkapi data rekening sebelum memproses pembayaran bank transfer.</div>
               </div>

               <div class="form-group">
                  <label style="font-weight: 600; color: #334155;">Tanggal Pembayaran <span class="text-danger">*</span></label>
                  <input type="datetime-local" class="form-control" name="payment_date" id="modal_payment_date" required style="border-radius: 4px;">
               </div>

                <div class="form-group">
                   <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                      <label style="font-weight: 600; color: #334155; margin-bottom: 0;">Nominal Transfer <span class="text-danger">*</span></label>
                      <button type="button" class="btn btn-xs btn-default btn-flat" id="btn_pay_full_balance" style="border-radius: 4px; color: #0284c7; border-color: #bae6fd; background-color: #f0f9ff; font-weight: 600;" title="Set sesuai sisa unpaid balance">
                         <i class="fa fa-check-circle"></i> Bayar Lunas
                      </button>
                   </div>
                   <div class="input-group">
                      <span class="input-group-addon" style="background-color: #f1f5f9; font-weight: 600; color: #475569; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">Rp</span>
                      <input type="text" class="form-control" name="amount" id="modal_payment_amount" required placeholder="Contoh: 5.000.000" autocomplete="off" style="border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-weight: 600;">
                   </div>
                   <small id="modal_payment_amount_help" class="help-block" style="margin-top: 5px; margin-bottom: 0; font-size: 12px;"></small>
                </div>

               <div class="form-group">
                  <label style="font-weight: 600; color: #334155;">Metode Pembayaran</label>
                  <select class="form-control" name="payment_method" id="modal_payment_method" style="border-radius: 4px;">
                     <option value="Bank Transfer (BCA)">Bank Transfer (BCA)</option>
                     <option value="Bank Transfer (Mandiri)">Bank Transfer (Mandiri)</option>
                     <option value="Bank Transfer (BRI)">Bank Transfer (BRI)</option>
                     <option value="Bank Transfer (BNI)">Bank Transfer (BNI)</option>
                     <option value="Cash / Tunai">Cash / Tunai</option>
                     <option value="E-Wallet">E-Wallet</option>
                  </select>
               </div>

               <div class="form-group">
                  <label style="font-weight: 600; color: #334155;">Upload Bukti Transfer <small style="color: #64748b;">(JPG, PNG, PDF)</small></label>
                  <input type="file" class="form-control" name="payment_proof_file" id="modal_payment_proof_file" accept="image/*,.pdf" style="border-radius: 4px; padding: 4px 10px;">
               </div>

               <div class="form-group" style="margin-bottom: 0;">
                  <label style="font-weight: 600; color: #334155;">Catatan / Keterangan <small style="color: #64748b;">(Opsional)</small></label>
                  <textarea class="form-control" name="notes" id="modal_payment_notes" rows="2" placeholder="Contoh: Pelunasan 100% atau Termin 1" style="border-radius: 4px;"></textarea>
               </div>
            </div>
            <div class="modal-footer" style="background-color: #f8fafc; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; padding: 12px 20px;">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 4px;">Batal</button>
               <button type="submit" class="btn btn-success btn-flat" id="btnSubmitPayment" style="border-radius: 4px; font-weight: 600;">
                  <i class="fa fa-save"></i> Simpan Pembayaran
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- MODAL REVIEW & PERSETUJUAN TAGIHAN (WITHHOLDING TAX VERIFICATION) -->
<div class="modal fade" id="modalReviewInvoice" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="border-radius: 8px; border: 0; box-shadow: 0 4px 20px rgba(0,0,0,0.15); overflow: hidden;">
         <div class="modal-header" style="background-color: #0f172a; color: #fff; padding: 14px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="font-weight: 700; font-size: 16px;">
               <i class="fa fa-balance-scale" style="margin-right: 6px; color: #38bdf8;"></i> Review &amp; Verifikasi Pajak Tagihan (Vendor Invoice)
            </h4>
         </div>
         
         <form id="formReviewInvoice">
            <div class="modal-body" style="padding: 24px;">
               <input type="hidden" name="invoice_id" id="review_invoice_id">
               
               <!-- Legal Note Alert -->
               <div class="alert alert-info" style="border-radius: 6px; font-size: 12.5px; margin-bottom: 20px;">
                  <i class="fa fa-info-circle" style="margin-right: 5px;"></i>
                  <b>Kewajiban Pemotong Pajak (Brand):</b> Sebagai pemotong resmi PPh 21/23, Finance berwenang memeriksa validitas data NPWP/KTP KOL dan menentukan tarif pemotongan yang sah sebelum menyetujui invoice.
               </div>

               <!-- Party & Deal Information -->
               <div class="row" style="margin-bottom: 15px;">
                  <div class="col-md-6">
                     <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; font-size: 13px;">
                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 8px;">Penyedia Jasa (KOL):</div>
                        <div style="margin-bottom: 4px;">Nama: <strong id="rv_kol_name" style="color: #0f172a;">-</strong></div>
                        <div style="margin-bottom: 4px;">NPWP: <span id="rv_kol_npwp" class="label label-default">-</span></div>
                        <div style="margin-bottom: 4px;">NIK/KTP: <strong id="rv_kol_ktp" style="font-family: monospace;">-</strong></div>
                        <div style="margin-top: 8px; border-top: 1px solid #e2e8f0; padding-top: 6px;">
                           Rekening: <span id="rv_bank_info" style="font-weight: 600; color: #0284c7;">-</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; font-size: 13px;">
                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 8px;">Detail Kerjasama:</div>
                        <div style="margin-bottom: 4px;">Campaign: <strong id="rv_campaign_name" style="color: #0f172a;">-</strong></div>
                        <div style="margin-bottom: 4px;">Nomor Tagihan: <strong id="rv_invoice_no" style="font-family: monospace; color: #0284c7;">-</strong></div>
                        <div style="margin-bottom: 4px;">Nilai Jasa (Gross): <strong id="rv_subtotal_text" style="color: #0f172a; font-size: 14px;">Rp 0</strong></div>
                        <div style="margin-top: 8px; border-top: 1px solid #e2e8f0; padding-top: 6px;">
                           Lampiran KOL: <span id="rv_invoice_file_link">-</span>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Withholding Tax Adjustment Form -->
               <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 18px; margin-bottom: 20px;">
                  <div style="font-weight: 700; font-size: 14px; color: #1e3a8a; margin-bottom: 12px;">
                     <i class="fa fa-sliders"></i> Konfirmasi / Penyesuaian Pemotongan Pajak (PPh)
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label style="font-size: 12.5px; font-weight: 600; color: #1e3a8a;">Jenis Pajak Penghasilan <span class="text-danger">*</span></label>
                           <select name="tax_type" id="rv_tax_type" class="form-control" style="border-radius: 4px;">
                              <option value="PPH21">PPh Pasal 21 (Orang Pribadi / Bukan Pegawai)</option>
                              <option value="PPH23">PPh Pasal 23 (Badan Usaha / Agency / PT / CV)</option>
                              <option value="NONE">NONE (Tanpa Pemotongan Pajak)</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label style="font-size: 12.5px; font-weight: 600; color: #1e3a8a;">Tarif Efektif Pajak (%) <span class="text-danger">*</span></label>
                           <div class="input-group">
                              <input type="number" step="0.01" min="0" max="100" name="tax_rate_percent" id="rv_tax_rate_percent" class="form-control" value="2.50" style="border-radius: 4px 0 0 4px; font-weight: 700;">
                              <span class="input-group-addon" style="border-radius: 0 4px 4px 0; font-weight: 700;">%</span>
                           </div>
                           <small class="help-block" id="rv_tax_rate_help" style="margin-bottom: 0; font-size: 11px; color: #64748b;">
                              PPh 21 NPWP: 2.50% &bull; Non-NPWP: 3.00% &bull; PPh 23: 2.00% / 4.00%
                           </small>
                        </div>
                     </div>
                  </div>

                  <!-- Live Calculation Box -->
                  <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 16px; margin-top: 10px;">
                     <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                        <span style="color: #64748b;">Subtotal (Gross Fee):</span>
                        <strong style="color: #0f172a;" id="rv_calc_subtotal">Rp 0</strong>
                     </div>
                     <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                        <span style="color: #dc2626;">Potongan Pajak yang Disetor ke Kas Negara:</span>
                        <strong style="color: #dc2626;" id="rv_calc_tax">- Rp 0</strong>
                     </div>
                     <div style="border-top: 1px solid #e2e8f0; margin: 6px 0;"></div>
                     <div style="display: flex; justify-content: space-between; font-size: 15px;">
                        <span style="font-weight: 700; color: #0f172a;">Net Transfer Diterima KOL (Grand Total):</span>
                        <strong style="font-weight: 800; color: #16a34a;" id="rv_calc_grand_total">Rp 0</strong>
                     </div>
                  </div>
               </div>

               <!-- Rejection Section (Collapsed by default) -->
               <div id="rv_reject_box" style="display: none; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 16px;">
                  <label style="font-weight: 700; color: #991b1b; margin-bottom: 6px;">
                     <i class="fa fa-exclamation-triangle"></i> Masukkan Alasan Penolakan Tagihan <span class="text-danger">*</span>
                  </label>
                  <textarea name="rejection_reason" id="rv_rejection_reason" class="form-control" rows="3" placeholder="Jelaskan alasan tagihan ditolak (misal: nominal tidak sesuai scope, deliverable belum tuntas, atau rekening salah)..."></textarea>
               </div>
            </div>

            <div class="modal-footer" style="background-color: #f8fafc; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center;">
               <div>
                  <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 4px;">Batal</button>
               </div>
               <div style="display: flex; gap: 8px;">
                  <button type="button" class="btn btn-danger btn-flat" id="btn_toggle_reject" style="border-radius: 4px; font-weight: 600;">
                     <i class="fa fa-times-circle"></i> Tolak Tagihan
                  </button>
                  <button type="button" class="btn btn-danger btn-flat" id="btn_confirm_reject" style="display: none; border-radius: 4px; font-weight: 700;">
                     <i class="fa fa-ban"></i> Konfirmasi Tolak Tagihan
                  </button>
                  <button type="submit" class="btn btn-primary btn-flat" id="btn_approve_invoice" style="border-radius: 4px; font-weight: 700;">
                     <i class="fa fa-check-circle"></i> Setujui Tagihan (Approve)
                  </button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- MODAL UPLOAD BUKTI POTONG PAJAK (BUPOT) -->
<div class="modal fade" id="modalUploadBupot" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content" style="border-radius: 8px; border: 0; box-shadow: 0 4px 20px rgba(0,0,0,0.15); overflow: hidden;">
         <div class="modal-header" style="background-color: #0f172a; color: #fff; padding: 14px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="font-weight: 700; font-size: 16px;">
               <i class="fa fa-certificate text-warning" style="margin-right: 6px;"></i> Unggah Bukti Pemotongan Pajak (e-Bupot)
            </h4>
         </div>

         <form id="formUploadBupot" enctype="multipart/form-data">
            <div class="modal-body" style="padding: 20px;">
               <input type="hidden" name="invoice_id" id="bupot_invoice_id">

               <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 16px; font-size: 13px;">
                  <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                     <span style="color: #64748b;">No. Invoice:</span>
                     <strong id="bupot_lbl_invoice_no" style="font-family: monospace; color: #0284c7;">-</strong>
                  </div>
                  <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                     <span style="color: #64748b;">Nama KOL:</span>
                     <strong id="bupot_lbl_kol_name" style="color: #0f172a;">-</strong>
                  </div>
                  <div style="display: flex; justify-content: space-between;">
                     <span style="color: #64748b;">Pajak yang Dipotong:</span>
                     <strong id="bupot_lbl_tax_amount" style="color: #dc2626;">-</strong>
                  </div>
               </div>

               <div class="form-group">
                  <label style="font-weight: 600; color: #334155;">Nomor Bukti Potong Resmi (DJP) <span class="text-danger">*</span></label>
                  <input type="text" name="bupot_no" id="in_bupot_no" class="form-control" placeholder="Contoh: 1721-VI-2026-000123" required style="border-radius: 4px; font-weight: 600;">
                  <small class="help-block" style="font-size: 11px; color: #64748b; margin-bottom: 0;">Nomor bukti pemotongan sesuai Formulir 1721-VI atau e-Bupot Unifikasi DJP.</small>
               </div>

               <div class="form-group" style="margin-bottom: 0;">
                  <label style="font-weight: 600; color: #334155;">Unggah Berkas Bukti Potong (PDF / Scan) <span class="text-danger">*</span></label>
                  <input type="file" name="bupot_file" id="in_bupot_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="border-radius: 4px; padding: 4px 10px;">
                  <small class="help-block" style="font-size: 11px; color: #64748b; margin-bottom: 0;">Format didukung: PDF, JPG, PNG (Maks 5 MB).</small>
               </div>
            </div>

            <div class="modal-footer" style="background-color: #f8fafc; padding: 12px 20px;">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 4px;">Batal</button>
               <button type="submit" class="btn btn-warning btn-flat" id="btnSubmitBupot" style="border-radius: 4px; font-weight: 700; color: #78350f;">
                  <i class="fa fa-upload"></i> Unggah &amp; Terbitkan ke KOL
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal Sign Contract -->
<div class="modal fade" id="modal_sign_contract" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document" style="width: 95%; max-width: 1150px;">
    <div class="modal-content" style="border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 8px 8px 0 0; padding: 15px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 24px;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" style="font-weight: 700; color: #1e293b; font-size: 16px;"><i class="fa fa-pencil-square-o" style="color: #0284c7;"></i> Contract Generator & Sign</h4>
      </div>
      
      <div class="modal-body" style="background: #f1f5f9; padding: 20px;">
        <input type="hidden" id="sign_contract_id" value="">
        
        <div class="row">
          <!-- Left: A4 Portrait Contract Document Preview -->
          <div class="col-md-7">
            <div class="contract-paper-scroll" style="background: #334155; padding: 20px 15px; border-radius: 8px; max-height: 640px; overflow-y: auto; overflow-x: auto; display: flex; justify-content: center; align-items: flex-start; box-shadow: inset 0 2px 6px rgba(0,0,0,0.4);">
               <div id="modal_contract_html_preview" style="background: #ffffff; width: 100%; max-width: 100%; height: auto; min-height: auto; padding: 30px 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); border-radius: 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; box-sizing: border-box; color: #1e293b; font-size: 11.5px; line-height: 1.5; word-wrap: break-word; overflow-wrap: break-word;">
                  <div style="text-align: center; color: #64748b; padding: 60px 20px;">
                     <i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Memuat pratinjau kontrak...
                  </div>
               </div>
            </div>
          </div>
          
          <!-- Right: Signature Mode Switch (Tab 1 vs Tab 2) -->
          <div class="col-md-5">
            <!-- Step 1: Syarat & Ketentuan Khusus Brand (MoU) -->
            <div style="background: #ffffff; padding: 15px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; margin-bottom: 15px;">
               <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                  <label style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0;">
                     <i class="fa fa-file-text-o" style="color: #0284c7;"></i> S&amp;K Khusus Brand (MoU):
                  </label>
                  <button type="button" class="btn btn-xs btn-info btn-flat" id="btn_save_snk" style="border-radius: 4px; font-weight: 600;">
                     <i class="fa fa-refresh"></i> Update S&amp;K ke MoU
                  </button>
               </div>
               <p style="font-size: 11.5px; color: #64748b; margin-bottom: 8px;">Kustomisasi Syarat &amp; Ketentuan kerja sama sebelum menandatangani kontrak:</p>
               <textarea id="modal_snk_content" class="form-control" rows="5" style="font-size: 11px; line-height: 1.4; border-radius: 4px; resize: vertical;" placeholder="Tuliskan klausul S&K Brand di sini..."></textarea>
               
               <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 4px;">
                  <span style="font-size: 10px; color: #64748b; align-self: center; margin-right: 4px;">Klausul Cepat:</span>
                  <button type="button" class="btn btn-default btn-xs btn-snk-quick" data-text="Konten dilarang dihapus/diarsipkan minimal 30 hari kalender." style="font-size: 9.5px; border-radius: 3px; padding: 2px 6px;">+ 30 Hari Tayang</button>
                  <button type="button" class="btn btn-default btn-xs btn-snk-quick" data-text="Hak whitelisting materi untuk digital ads selama 90 hari." style="font-size: 9.5px; border-radius: 3px; padding: 2px 6px;">+ Whitelisting 90 Hari</button>
                  <button type="button" class="btn btn-default btn-xs btn-snk-quick" data-text="KOL bersedia melakukan maksimal 2x revisi minor materi sesuai brief." style="font-size: 9.5px; border-radius: 3px; padding: 2px 6px;">+ 2x Revisi Minor</button>
                  <button type="button" class="btn btn-default btn-xs btn-snk-quick" data-text="Honorarium diproses sesuai invoice yang diterbitkan setelah deliverable tayang dan link tervalidasi." style="font-size: 9.5px; border-radius: 3px; padding: 2px 6px;">+ Validasi Insight</button>
               </div>
            </div>

            <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
               <label style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 12px; display: block;">
                  <i class="fa fa-hand-o-up" style="color: #0284c7;"></i> Tanda Tangan Brand / Admin (E-Sign):
               </label>
               
               <!-- Nav Pills for Option 1 vs Option 2 -->
               <ul class="nav nav-pills nav-justified" id="sign_option_tabs" style="margin-bottom: 18px; background: #f1f5f9; padding: 4px; border-radius: 6px;">
                  <li class="active">
                     <a data-toggle="pill" href="#tab_draw_sign" style="border-radius: 5px; font-weight: 600; font-size: 12px; padding: 8px 10px;">
                        <i class="fa fa-paint-brush"></i> Option 1: Draw E-Sign
                     </a>
                  </li>
                  <li>
                     <a data-toggle="pill" href="#tab_upload_sign" style="border-radius: 5px; font-weight: 600; font-size: 12px; padding: 8px 10px;">
                        <i class="fa fa-cloud-upload"></i> Option 2: Upload File
                     </a>
                  </li>
               </ul>

               <div class="tab-content">
                  <!-- Option 1: Draw Signature -->
                  <div id="tab_draw_sign" class="tab-pane fade in active">
                     <p style="font-size: 12px; color: #64748b; margin-bottom: 8px;">Goreskan tanda tangan Anda di bawah ini:</p>
                     
                     <div style="text-align: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 10px;">
                        <canvas id="modal-signature-pad" width="320" height="150" style="border: 1px solid #cbd5e1; border-radius: 4px; cursor: crosshair; background: #fff; max-width: 100%; touch-action: none;"></canvas>
                        <div style="margin-top: 8px; text-align: right;">
                           <button type="button" class="btn btn-default btn-xs" id="modal-clear-signature" style="border-radius: 4px;">
                              <i class="fa fa-eraser"></i> Hapus / Clear
                           </button>
                        </div>
                     </div>
                     
                     <button type="button" class="btn btn-primary btn-block btn-flat" id="btn_submit_draw_sign" style="margin-top: 18px; font-weight: 600; border-radius: 6px; padding: 12px; font-size: 13.5px; background-color: #0284c7; border-color: #0284c7;">
                        <i class="fa fa-check-circle"></i> Save & Sign Contract (Generate PDF)
                     </button>
                  </div>

                  <!-- Option 2: Upload File -->
                  <div id="tab_upload_sign" class="tab-pane fade">
                     <p style="font-size: 12px; color: #64748b; margin-bottom: 8px;">Upload dokumen kontrak yang sudah ditandatangani manual atau scan tanda tangan:</p>
                     
                     <form id="form_sign_contract_upload" enctype="multipart/form-data">
                        <div class="form-group" style="margin-bottom: 18px;">
                           <label style="font-size: 12px; font-weight: 600; color: #334155;">Pilih File (JPG, PNG, atau PDF):</label>
                           <input type="file" name="contract_file" id="sign_contract_file" class="form-control" accept="image/*,application/pdf" required style="padding: 6px; height: auto; border-radius: 4px;">
                        </div>
                        <button type="submit" class="btn btn-success btn-block btn-flat" id="btn_submit_upload_sign" style="font-weight: 600; border-radius: 6px; padding: 12px; font-size: 13.5px; background-color: #059669; border-color: #059669;">
                           <i class="fa fa-cloud-upload"></i> Save & Upload Signed Contract
                        </button>
                     </form>
                  </div>
               </div>
            </div>
            
            <div style="margin-top: 15px; padding: 12px 15px; background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 6px; font-size: 11.5px; color: #0369a1;">
               <i class="fa fa-info-circle"></i> Tanda tangan yang Anda masukkan akan langsung diperbarui di pratinjau dokumen sebelah kiri.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var signatureCanvas, signatureCtx, isDrawing = false, signPos = { x: 0, y: 0 };
var contractKolName = 'KOL / Talent';
var contractAdminName = '<?= htmlspecialchars($this->aauth->get_user()->full_name ?? 'KOL-System Team'); ?>';

function initSignaturePad() {
    if (signatureCanvas) return;
    signatureCanvas = document.getElementById('modal-signature-pad');
    signatureCtx = signatureCanvas.getContext('2d');
    
    function setSignPos(e) {
        var rect = signatureCanvas.getBoundingClientRect();
        var clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
        var clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0);
        signPos.x = clientX - rect.left;
        signPos.y = clientY - rect.top;
    }
    
    function startDrawing(e) {
        if(e.type === 'mousedown' || e.type === 'touchstart') {
            setSignPos(e);
            isDrawing = true;
        }
    }
    
    function draw(e) {
        if (!isDrawing) return;
        e.preventDefault();
        signatureCtx.beginPath();
        signatureCtx.lineWidth = 2;
        signatureCtx.lineCap = 'round';
        signatureCtx.strokeStyle = '#000';
        signatureCtx.moveTo(signPos.x, signPos.y);
        setSignPos(e);
        signatureCtx.lineTo(signPos.x, signPos.y);
        signatureCtx.stroke();
    }
    
    function stopDrawing() {
        if (isDrawing) {
            isDrawing = false;
            updateSignaturePreview(signatureCanvas.toDataURL('image/png'));
        }
    }
    
    signatureCanvas.addEventListener('mousedown', startDrawing);
    signatureCanvas.addEventListener('mousemove', draw);
    signatureCanvas.addEventListener('mouseup', stopDrawing);
    signatureCanvas.addEventListener('mouseleave', stopDrawing);
    
    signatureCanvas.addEventListener('touchstart', startDrawing, {passive: false});
    signatureCanvas.addEventListener('touchmove', draw, {passive: false});
    signatureCanvas.addEventListener('touchend', stopDrawing);
}

function updateSignaturePreview(dataUrl) {
    if ($('#preview-admin-sign-slot').length > 0) {
        $('#preview-admin-sign-slot').html(
            '<img id="preview-admin-sign" src="' + dataUrl + '" style="height: 52px; max-width: 140px; object-fit: contain; display: block; margin: 0 auto 4px auto;">' +
            '<div style="font-size: 9px; color: #16a34a; font-weight: 600;"><i class="fa fa-pencil"></i> Pratinjau Tanda Tangan</div>'
        );
    } else if ($('#preview-admin-sign').length > 0) {
        $('#preview-admin-sign').attr('src', dataUrl);
    }
}

function clearSignaturePad() {
    if (signatureCtx && signatureCanvas) {
        signatureCtx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
    }
    if ($('#preview-admin-sign-slot').length > 0) {
        $('#preview-admin-sign-slot').html(
            '<div style="height: 52px; border: 1px dashed #cbd5e1; border-radius: 4px; background: #f8fafc; color: #94a3b8; font-size: 10px; font-style: italic; line-height: 52px; text-align: center;">(Menunggu tanda tangan)</div>'
        );
    }
}

function isCanvasBlank() {
    if (!signatureCanvas) return true;
    const blank = document.createElement('canvas');
    blank.width = signatureCanvas.width;
    blank.height = signatureCanvas.height;
    return signatureCanvas.toDataURL() === blank.toDataURL();
}

function openSignModal(id) {
    $('#sign_contract_id').val(id);
    $('#sign_contract_file').val('');
    $('#modal_contract_html_preview').html('<div style="text-align: center; color: #64748b; padding: 60px 20px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Memuat pratinjau kontrak...</div>');
    
    $('#modal_sign_contract').modal('show');
    initSignaturePad();
    clearSignaturePad();
    
    // Switch to tab 1 by default
    $('#sign_option_tabs a[href="#tab_draw_sign"]').tab('show');
    
    // Fetch HTML Preview
    $.ajax({
        url: BASE_URL + 'administrator/campaign_kol_contracts/get_contract_html_ajax/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.success && res.html) {
                if (res.kol_name) contractKolName = res.kol_name;
                if (res.admin_name) contractAdminName = res.admin_name;
                if (res.terms_and_conditions) {
                    $('#modal_snk_content').val(res.terms_and_conditions);
                } else {
                    $('#modal_snk_content').val('');
                }
                $('#modal_contract_html_preview').html(res.html);
            } else {
                $('#modal_contract_html_preview').html('<div style="color: #e11d48; text-align: center; padding: 50px 20px;">Failed to load contract preview.</div>');
            }
        },
        error: function() {
            $('#modal_contract_html_preview').html('<div style="color: #e11d48; text-align: center; padding: 50px 20px;">Network error while loading contract preview.</div>');
        }
    });
}

$(document).ready(function() {
   // Quick Insert S&K Clauses
   $(document).on('click', '.btn-snk-quick', function(e) {
       e.preventDefault();
       var txt = $(this).data('text');
       var current = $('#modal_snk_content').val().trim();
       if (current.length > 0) {
           var lines = current.split('\n');
           var nextNo = lines.length + 1;
           $('#modal_snk_content').val(current + '\n' + nextNo + '. ' + txt);
       } else {
           $('#modal_snk_content').val('1. ' + txt);
       }
   });

   // Save S&K and Refresh Preview
   $('#btn_save_snk').click(function(e) {
       e.preventDefault();
       var btn = $(this);
       var id = $('#sign_contract_id').val();
       var snk = $('#modal_snk_content').val();
       btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

       var postData = {
           id: id,
           terms_and_conditions: snk
       };
       if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
           postData[csrf] = token;
       }

       $.ajax({
           url: BASE_URL + 'administrator/campaign_kol_contracts/save_contract_snk_ajax',
           type: 'POST',
           dataType: 'json',
           data: postData,
           success: function(res) {
               btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Update S&amp;K ke MoU');
               if (res.success && res.html) {
                   $('#modal_contract_html_preview').html(res.html);
                   alert(res.message);
               } else {
                   alert(res.message || 'Gagal menyimpan S&K.');
               }
           },
           error: function() {
               btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Update S&amp;K ke MoU');
               alert('Terjadi kesalahan jaringan.');
           }
       });
   });

   // Manual Trigger to Generate Initial Invoice
   $(document).on('click', '.btn-generate-initial-invoice', function(e) {
       e.preventDefault();
       var btn = $(this);
       var contractId = btn.data('contract-id');
       var dealId = btn.data('deal-id');

       if (!confirm('Terbitkan invoice awal untuk deal ini?')) {
           return;
       }

       btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
       var postData = {
           contract_id: contractId,
           campaign_kol_id: dealId
       };
       if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
           postData[csrf] = token;
       }

       $.ajax({
           url: BASE_URL + 'administrator/campaign_kol_contracts/generate_initial_invoice_ajax',
           type: 'POST',
           dataType: 'json',
           data: postData,
           success: function(res) {
               if (res.success) {
                   alert(res.message);
                   window.location.reload();
               } else {
                   alert('Gagal: ' + res.message);
                   btn.prop('disabled', false).html('<i class="fa fa-bolt"></i> Terbitkan Invoice Awal');
               }
           },
           error: function() {
               alert('Terjadi kesalahan jaringan.');
               btn.prop('disabled', false).html('<i class="fa fa-bolt"></i> Terbitkan Invoice Awal');
           }
       });
   });

   $('#modal-clear-signature').click(function(e) {
       e.preventDefault();
       clearSignaturePad();
   });

   // Auto-update preview when file is uploaded
   $('#sign_contract_file').on('change', function(e) {
       var file = e.target.files[0];
       if (file && file.type.match('image.*')) {
           var reader = new FileReader();
           reader.onload = function(e) {
               updateSignaturePreview(e.target.result);
           };
           reader.readAsDataURL(file);
       }
   });

   // Option 1: Draw Signature Submit
   $('#btn_submit_draw_sign').click(function(e) {
       e.preventDefault();
       if (isCanvasBlank()) {
           alert('Silakan goreskan tanda tangan Anda terlebih dahulu pada kanvas.');
           return;
       }
       
       var signatureData = signatureCanvas.toDataURL('image/png');
       var btn = $(this);
       btn.prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Menyimpan...');

       var postData = {
           id: $('#sign_contract_id').val(),
           signature: signatureData,
           terms_and_conditions: $('#modal_snk_content').val()
       };
       if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
           postData[csrf] = token;
       }

       $.ajax({
           url: BASE_URL + 'administrator/campaign_kol_contracts/admin_counter_sign_action',
           type: 'POST',
           dataType: 'json',
           data: postData,
           success: function(res) {
               if (res.success) {
                   alert(res.message || 'Tanda tangan Brand berhasil disimpan.');
                   window.location.reload();
               } else {
                   alert('Gagal: ' + res.message);
                   btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Save & Sign Contract (Generate PDF)');
               }
           },
           error: function(xhr) {
               var msg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : ((xhr && xhr.responseText) ? xhr.responseText.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim().substring(0, 250) : 'Terjadi kesalahan jaringan.');
               alert('Gagal: ' + msg);
               btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Save & Sign Contract (Generate PDF)');
           }
       });
   });

   // Option 2: Upload File Submit
   $('#form_sign_contract_upload').on('submit', function(e) {
       e.preventDefault();
       var formData = new FormData(this);
       formData.append('id', $('#sign_contract_id').val());
       formData.append('terms_and_conditions', $('#modal_snk_content').val());
       if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
          formData.append(csrf, token);
       }
       var btn = $('#btn_submit_upload_sign');
       btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
       
       $.ajax({
           url: BASE_URL + 'administrator/campaign_kol_contracts/sign_contract_action',
           type: 'POST',
           data: formData,
           processData: false,
           contentType: false,
           dataType: 'json',
           success: function(res) {
               if (res.success) {
                   alert(res.message);
                   window.location.reload();
               } else {
                   alert(res.message);
                   btn.prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Upload & Save Contract');
               }
           },
           error: function() {
               alert('Terjadi kesalahan jaringan.');
               btn.prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Upload & Save Contract');
           }
       });
   });
});

$(document).ready(function() {
   // Tab Filtering
   $('#finance_hub_tabs a').on('click', function(e) {
      e.preventDefault();
      $('#finance_hub_tabs li').removeClass('active');
      $(this).parent('li').addClass('active');
      var filter = $(this).data('filter-status');
      if (filter === 'all') {
         $('.deal-row').show();
      } else if (filter === 'READY') {
         $('.deal-row').hide();
         $('.deal-row[data-invoice-status="APPROVED"], .deal-row[data-invoice-status="PARTIAL"]').show();
      } else {
         $('.deal-row').hide();
         $('.deal-row[data-invoice-status="' + filter + '"]').show();
      }
   });

   var currentReviewSubtotal = 0;

   function recalcReviewTax() {
      var taxType = $('#rv_tax_type').val();
      var rate = parseFloat($('#rv_tax_rate_percent').val()) || 0;
      if (taxType === 'NONE') {
         rate = 0;
         $('#rv_tax_rate_percent').val(0);
      }
      var taxAmount = Math.round((currentReviewSubtotal * (rate / 100)) * 100) / 100;
      var grandTotal = Math.max(0, currentReviewSubtotal - taxAmount);

      $('#rv_calc_subtotal').text('Rp ' + Math.round(currentReviewSubtotal).toLocaleString('id-ID'));
      $('#rv_calc_tax').text('- Rp ' + Math.round(taxAmount).toLocaleString('id-ID') + ' (' + rate + '%)');
      $('#rv_calc_grand_total').text('Rp ' + Math.round(grandTotal).toLocaleString('id-ID'));
   }

   $(document).on('input change', '#rv_tax_type, #rv_tax_rate_percent', function() {
      recalcReviewTax();
   });

   $('.btn-open-review-invoice').on('click', function(e) {
      e.preventDefault();
      var btn = $(this);
      var invoiceId = btn.data('invoice-id');
      var kolName = btn.data('kol-name');
      var campaignName = btn.data('campaign-name');
      var invoiceNo = btn.data('invoice-no');
      var subtotal = parseFloat(btn.data('subtotal')) || 0;
      var taxType = btn.data('tax-type') || 'PPH21';
      var taxRate = parseFloat(btn.data('tax-rate')) || 2.50;
      var npwp = btn.data('npwp');
      var ktp = btn.data('ktp');
      var bankName = btn.data('bank-name');
      var bankAccount = btn.data('bank-account');
      var bankAccountName = btn.data('bank-account-name');
      var invoiceFile = btn.data('invoice-file');

      currentReviewSubtotal = subtotal;

      $('#review_invoice_id').val(invoiceId);
      $('#rv_kol_name').text(kolName || '-');
      $('#rv_kol_npwp').text(npwp ? 'NPWP: ' + npwp : 'Non-NPWP').toggleClass('label-success', !!npwp).toggleClass('label-default', !npwp);
      $('#rv_kol_ktp').text(ktp || '-');
      $('#rv_bank_info').text((bankName || '-') + ' ' + (bankAccount || '-') + ' a/n ' + (bankAccountName || '-'));

      $('#rv_campaign_name').text(campaignName || '-');
      $('#rv_invoice_no').text(invoiceNo || '-');
      $('#rv_subtotal_text').text('Rp ' + Math.round(subtotal).toLocaleString('id-ID'));

      if (invoiceFile) {
         $('#rv_invoice_file_link').html('<a href="<?= site_url('administrator/finance_hub/view_invoice_file/'); ?>' + invoiceId + '" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-paperclip"></i> Buka Lampiran Berkas</a>');
      } else {
         $('#rv_invoice_file_link').html('<span style="color: #94a3b8; font-style: italic;">Standar Sistem (Tanpa Berkas Fisik)</span>');
      }

      $('#rv_tax_type').val(taxType);
      $('#rv_tax_rate_percent').val(taxRate);
      recalcReviewTax();

      // Reset reject box
      $('#rv_reject_box').hide();
      $('#btn_toggle_reject').show();
      $('#btn_confirm_reject').hide();
      $('#btn_approve_invoice').show();
      $('#rv_rejection_reason').val('');

      $('#modalReviewInvoice').modal('show');
   });

   // Toggle reject input
   $('#btn_toggle_reject').on('click', function() {
      $('#rv_reject_box').slideDown(200);
      $('#btn_toggle_reject').hide();
      $('#btn_confirm_reject').show();
      $('#btn_approve_invoice').hide();
      $('#rv_rejection_reason').focus();
   });

   // Confirm reject submit
   $('#btn_confirm_reject').on('click', function() {
      var invoiceId = $('#review_invoice_id').val();
      var reason = $.trim($('#rv_rejection_reason').val());
      if (!reason) {
         alert('Wajib menuliskan alasan penolakan tagihan!');
         $('#rv_rejection_reason').focus();
         return;
      }

      if (!confirm('Apakah Anda yakin ingin menolak tagihan ini? Notifikasi dan alasan akan dikirimkan ke KOL.')) {
         return;
      }

      var postData = {
         invoice_id: invoiceId,
         rejection_reason: reason
      };
      if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
         postData[csrf] = token;
      }

      var $btn = $('#btn_confirm_reject');
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menolak...');

      $.ajax({
         url: BASE_URL + 'administrator/finance_hub/reject_invoice',
         type: 'POST',
         dataType: 'json',
         data: postData,
         success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-ban"></i> Konfirmasi Tolak Tagihan');
            if (res.success) {
               alert(res.message);
               window.location.reload();
            } else {
               alert(res.message || 'Gagal menolak invoice.');
            }
         },
         error: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-ban"></i> Konfirmasi Tolak Tagihan');
            alert('Terjadi kesalahan jaringan.');
         }
      });
   });

   // Approve invoice submit
   $('#formReviewInvoice').on('submit', function(e) {
      e.preventDefault();
      var invoiceId = $('#review_invoice_id').val();
      var taxType = $('#rv_tax_type').val();
      var taxRate = $('#rv_tax_rate_percent').val();

      if (!confirm('Apakah Anda yakin ingin menyetujui invoice ini? Status akan menjadi APPROVED (Siap Dibayar).')) {
         return;
      }

      var postData = {
         invoice_id: invoiceId,
         tax_type: taxType,
         tax_rate_percent: taxRate
      };
      if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
         postData[csrf] = token;
      }

      var $btn = $('#btn_approve_invoice');
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyetujui...');

      $.ajax({
         url: BASE_URL + 'administrator/finance_hub/approve_invoice',
         type: 'POST',
         dataType: 'json',
         data: postData,
         success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Setujui Tagihan (Approve)');
            if (res.success) {
               alert(res.message);
               window.location.reload();
            } else {
               alert(res.message || 'Gagal memproses persetujuan invoice.');
            }
         },
         error: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Setujui Tagihan (Approve)');
            alert('Terjadi kesalahan jaringan.');
         }
      });
   });

   // Open Bupot Modal
   $('.btn-open-bupot-modal').on('click', function(e) {
      e.preventDefault();
      var btn = $(this);
      $('#bupot_invoice_id').val(btn.data('invoice-id'));
      $('#bupot_lbl_invoice_no').text(btn.data('invoice-no') || '-');
      $('#bupot_lbl_kol_name').text(btn.data('kol-name') || '-');
      $('#bupot_lbl_tax_amount').text('Rp ' + (btn.data('tax-amount') || '0') + ' (' + (btn.data('tax-type') || 'PPh') + ')');
      $('#in_bupot_no').val('');
      $('#in_bupot_file').val('');
      $('#modalUploadBupot').modal('show');
   });

   // Submit Bupot Form
   $('#formUploadBupot').on('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
         formData.append(csrf, token);
      }

      var $btn = $('#btnSubmitBupot');
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Mengunggah...');

      $.ajax({
         url: BASE_URL + 'administrator/finance_hub/upload_bupot',
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         dataType: 'json',
         success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Unggah & Terbitkan ke KOL');
            if (res.success) {
               alert(res.message);
               window.location.reload();
            } else {
               alert(res.message || 'Gagal mengunggah bukti potong.');
            }
         },
         error: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Unggah & Terbitkan ke KOL');
            alert('Terjadi kesalahan jaringan.');
         }
      });
   });

   // Helper: Validate Payment Amount
   function validatePaymentAmount() {
      var raw = $('#modal_payment_amount').val() || '';
      var val = parseFloat(raw.toString().replace(/\./g, '').replace(/,/g, '.')) || 0;
      var max = parseFloat($('#modal_payment_amount').data('max-balance')) || 0;
      var $help = $('#modal_payment_amount_help');
      var $input = $('#modal_payment_amount');

      if (!raw || val <= 0) {
         $input.css('border-color', '#ef4444');
         $help.html('<span style="color: #dc2626;"><i class="fa fa-exclamation-circle"></i> Nominal transfer harus lebih besar dari Rp 0</span>');
         return false;
      } else if (val > max) {
         $input.css('border-color', '#ef4444');
         $help.html('<span style="color: #dc2626;"><i class="fa fa-exclamation-circle"></i> Nominal melebihi sisa unpaid balance (Maksimal: Rp ' + Math.round(max).toLocaleString('id-ID') + ')</span>');
         return false;
      } else {
         $input.css('border-color', '#10b981');
         $help.html('<span style="color: #059669;"><i class="fa fa-check-circle"></i> Nominal valid (Sisa balance: Rp ' + Math.round(max).toLocaleString('id-ID') + ')</span>');
         return true;
      }
   }

   // Live Format & Validate as user types without jumping cursor to the end
   $(document).on('input', '#modal_payment_amount', function() {
      var raw = this.value || '';
      var cursorPos = this.selectionStart || 0;
      var digitsBeforeCursor = raw.substring(0, cursorPos).replace(/[^0-9]/g, '').length;
      var val = raw.replace(/[^0-9]/g, '');

      if (val) {
         var formatted = parseInt(val, 10).toLocaleString('id-ID');
         this.value = formatted;

         var newCursor = 0;
         var digitCount = 0;
         for (var i = 0; i < formatted.length; i++) {
            if (/[0-9]/.test(formatted[i])) {
               digitCount++;
            }
            newCursor = i + 1;
            if (digitCount >= digitsBeforeCursor) {
               break;
            }
         }
         if (digitsBeforeCursor === 0) newCursor = 0;
         this.setSelectionRange(newCursor, newCursor);
      } else {
         this.value = '';
      }
      validatePaymentAmount();
   });

   // Quick Action: Bayar Lunas (100% Unpaid Balance)
   $('#btn_pay_full_balance').click(function() {
      var max = parseFloat($('#modal_payment_amount').data('max-balance')) || 0;
      if (max > 0) {
         $('#modal_payment_amount').val(Math.round(max).toLocaleString('id-ID'));
         validatePaymentAmount();
      }
   });

   // Open Payment Modal
   $('.btn-open-payment-modal').click(function(e) {
      e.preventDefault();
      var btn = $(this);
      var invoiceId = btn.data('invoice-id');
      var kolName = btn.data('kol-name');
      var campaignName = btn.data('campaign-name');
      var invoiceNo = btn.data('invoice-no');
      var balance = parseFloat(btn.data('balance')) || 0;
      
      var bankName = btn.data('bank-name');
      var bankAccount = btn.data('bank-account');
      var bankAccountName = btn.data('bank-account-name');

      $('#modal_payment_invoice_id').val(invoiceId);
      $('#modal_payment_kol_campaign').text(kolName + ' (' + campaignName + ')');
      $('#modal_payment_invoice_no').text(invoiceNo);
      $('#modal_payment_balance_text').text('Rp ' + balance.toLocaleString('id-ID'));
      
      // Store raw maximum balance and initialize formatted amount
      $('#modal_payment_amount').data('max-balance', balance);
      $('#modal_payment_amount').val(balance > 0 ? Math.round(balance).toLocaleString('id-ID') : '');
      validatePaymentAmount();
      
      if (bankName || bankAccount) {
         $('#modal_payment_bank_warning_box').hide();
         $('#modal_payment_bank_info_box').show();
         $('#modal_payment_bank_name').text(bankName || '-');
         $('#modal_payment_bank_account').text(bankAccount || '-');
         $('#modal_payment_bank_account_name').text(bankAccountName || '-');
      } else {
         $('#modal_payment_bank_info_box').hide();
         $('#modal_payment_bank_warning_box').show();
      }
      
      // Default payment_date to current datetime-local
      var now = new Date();
      now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
      $('#modal_payment_date').val(now.toISOString().slice(0, 16));

      $('#modalRecordPayment').modal('show');
   });

   // Submit Payment Form with Validation & Confirmation Logic
   $('#formRecordPayment').submit(function(e) {
      e.preventDefault();

      var rawAmount = $('#modal_payment_amount').val() || '';
      var numAmount = parseFloat(rawAmount.toString().replace(/\./g, '').replace(/,/g, '.')) || 0;
      var maxBalance = parseFloat($('#modal_payment_amount').data('max-balance')) || 0;

      if (numAmount <= 0) {
         alert('Nominal transfer harus lebih besar dari Rp 0.');
         $('#modal_payment_amount').focus();
         validatePaymentAmount();
         return false;
      }

      if (numAmount > maxBalance) {
         alert('Nominal transfer (Rp ' + Math.round(numAmount).toLocaleString('id-ID') + ') melebihi sisa unpaid balance (Rp ' + Math.round(maxBalance).toLocaleString('id-ID') + ').');
         $('#modal_payment_amount').focus();
         validatePaymentAmount();
         return false;
      }

      var invoiceNo = $('#modal_payment_invoice_no').text();
      var kolCampaign = $('#modal_payment_kol_campaign').text();
      var confirmMsg = 'Konfirmasi Pencatatan Pembayaran:\n\n' +
         '• No. Invoice: ' + invoiceNo + '\n' +
         '• Penerima: ' + kolCampaign + '\n' +
         '• Nominal Transfer: Rp ' + Math.round(numAmount).toLocaleString('id-ID') + '\n\n' +
         'Apakah Anda yakin data pembayaran ini sudah benar dan ingin menyimpannya?';

      if (!confirm(confirmMsg)) {
         return false;
      }

      var formData = new FormData(this);
      formData.set('amount', numAmount);
      
      if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
         formData.append(csrf, token);
      }

      var btnSubmit = $('#btnSubmitPayment');

      $.ajax({
         url: BASE_URL + 'administrator/finance_hub/save_payment',
         type: 'POST',
         data: formData,
         contentType: false,
         processData: false,
         dataType: 'json',
         beforeSend: function() {
            btnSubmit.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Menyimpan...');
         },
         success: function(res) {
            if (res.csrf_token && typeof csrf !== 'undefined') {
               token = res.csrf_token;
            }
            if (res.success) {
               alert(res.message);
               window.location.reload();
            } else {
               alert(res.message || 'Gagal menyimpan pembayaran.');
               btnSubmit.attr('disabled', false).html('<i class="fa fa-save"></i> Simpan Pembayaran');
            }
         },
         error: function() {
            alert('Terjadi kesalahan jaringan saat menyimpan.');
            btnSubmit.attr('disabled', false).html('<i class="fa fa-save"></i> Simpan Pembayaran');
         }
      });
   });
    // Buat Kontrak DRAFT satu klik (tetap di Finance Hub)
    $(document).on('click', '.btn-create-contract', function(e) {
       e.preventDefault();
       var btn = $(this);
       var campaignKolId = btn.data('campaign-kol-id');
       if (!campaignKolId) {
          alert('Dealing KOL tidak valid.');
          return;
       }
       if (!confirm('Buatkan kontrak DRAFT untuk dealing ini?')) {
          return;
       }
       var postData = { campaign_kol_id: campaignKolId };
       if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
          postData[csrf] = token;
       }
       $.ajax({
          url: BASE_URL + 'administrator/campaign_kol_contracts/create_draft_ajax',
          type: 'POST',
          dataType: 'json',
          data: postData,
          beforeSend: function() {
             btn.prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Membuat...');
          },
          success: function(res) {
             if (res.success) {
                alert(res.message);
                window.location.reload();
             } else {
                alert(res.message || 'Gagal membuat kontrak.');
                btn.prop('disabled', false).html('<i class="fa fa-plus-circle"></i> Buat Kontrak');
             }
          },
          error: function() {
             alert('Terjadi kesalahan jaringan saat membuat kontrak.');
             btn.prop('disabled', false).html('<i class="fa fa-plus-circle"></i> Buat Kontrak');
          }
       });
    });
});

function copyBankAccount() {
   var copyText = document.getElementById("modal_payment_bank_account").innerText;
   if(copyText && copyText !== '-') {
      var tempInput = document.createElement("input");
      tempInput.style = "position: absolute; left: -1000px; top: -1000px";
      tempInput.value = copyText;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);
      
      toastr['success']('Nomor Rekening berhasil disalin!');
   }
}
</script>
