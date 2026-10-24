<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
function reset_side_menu() {
   $('.sidebar-menu li').removeClass('active');
   $('.sidebar-menu li a[href*="my_invoices"]').parents('li').addClass('active');
}
$(document).ready(function() {
   reset_side_menu();
});
</script>

<style>
.invoice-card-stat {
   background: #ffffff;
   border-radius: 8px;
   padding: 18px 20px;
   box-shadow: 0 2px 10px rgba(0,0,0,0.05);
   border: 1px solid #e2e8f0;
   margin-bottom: 20px;
   display: flex;
   align-items: center;
}
.invoice-card-stat .icon-box {
   width: 48px;
   height: 48px;
   border-radius: 8px;
   display: flex;
   align-items: center;
   justify-content: center;
   font-size: 22px;
   margin-right: 15px;
}
.invoice-card-stat .stat-label {
   font-size: 12px;
   font-weight: 600;
   color: #64748b;
   text-transform: uppercase;
   letter-spacing: 0.5px;
}
.invoice-card-stat .stat-value {
   font-size: 20px;
   font-weight: 700;
   color: #0f172a;
   margin-top: 2px;
}
.badge-status {
   padding: 5px 10px;
   border-radius: 4px;
   font-size: 11.5px;
   font-weight: 600;
   display: inline-block;
}
.badge-status-SUBMITTED { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.badge-status-APPROVED { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.badge-status-PARTIAL { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
.badge-status-PAID { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.badge-status-REJECTED { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.badge-status-CANCELLED { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

.timeline-audit {
   list-style: none;
   padding: 0;
   position: relative;
}
.timeline-audit:before {
   content: '';
   position: absolute;
   top: 0;
   bottom: 0;
   left: 15px;
   width: 2px;
   background: #e2e8f0;
}
.timeline-audit li {
   position: relative;
   margin-bottom: 15px;
   padding-left: 36px;
}
.timeline-audit li .circle-dot {
   position: absolute;
   left: 8px;
   top: 3px;
   width: 16px;
   height: 16px;
   border-radius: 50%;
   background: #0284c7;
   border: 3px solid #ffffff;
   box-shadow: 0 0 0 1px #0284c7;
}
.timeline-audit li .time {
   font-size: 11px;
   color: #94a3b8;
}
.timeline-audit li .action-title {
   font-size: 13px;
   font-weight: 600;
   color: #1e293b;
}
.timeline-audit li .notes {
   font-size: 12px;
   color: #475569;
   margin-top: 2px;
}

/* Responsive visibility */
@media (min-width: 769px) {
   .mobile-only-section { display: none !important; }
}

@media (max-width: 768px) {
   .desktop-only-section { display: none !important; }
   
   /* Mobile Container & Spacing: strictly 16px gutter */
   .content {
      padding: 12px 16px !important;
   }
   .content-header {
      padding: 14px 16px 6px 16px !important;
   }
   .content-header > h1 {
      font-size: 20px !important;
      font-weight: 800 !important;
      letter-spacing: -0.3px;
      color: #0f172a;
      display: flex;
      flex-direction: column;
   }
   .content-header > h1 small {
      font-size: 13px !important;
      color: #64748b;
      margin-top: 4px;
      display: block;
   }
   .content-header > .breadcrumb {
      display: none !important;
   }

   /* Mobile Action Bar */
   .mobile-action-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 14px;
   }
   .mobile-action-bar .bar-title {
      font-size: 16px;
      font-weight: 700;
      color: #0f172a;
      display: flex;
      align-items: center;
      gap: 8px;
   }
   .mobile-action-bar .bar-title .inv-count-badge {
      font-size: 11px;
      font-weight: 700;
      background: #e2e8f0;
      color: #475569;
      padding: 2px 8px;
      border-radius: 999px;
   }
   .btn-mobile-create {
      background: #2563eb;
      color: #ffffff !important;
      border: none;
      padding: 9px 15px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 2px 6px rgba(37,99,235,0.25);
      white-space: nowrap;
      cursor: pointer;
   }

   /* Search Box */
   .mobile-search-wrapper {
      position: relative;
      margin-bottom: 12px;
   }
   .mobile-search-wrapper .search-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 14px;
      pointer-events: none;
   }
   .mobile-search-wrapper input {
      width: 100%;
      height: 42px;
      padding: 8px 36px 8px 38px;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      background: #ffffff;
      font-size: 13.5px;
      color: #1e293b;
      outline: none;
      transition: border-color 0.15s, box-shadow 0.15s;
   }
   .mobile-search-wrapper input:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
   }
   .mobile-search-wrapper .search-clear {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      background: none;
      border: none;
      padding: 4px;
      cursor: pointer;
      display: none;
   }

   /* Horizontal Pill Filter */
   .mobile-pills-carousel {
      display: flex;
      gap: 8px;
      overflow-x: auto;
      padding-bottom: 8px;
      margin-bottom: 14px;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
   }
   .mobile-pills-carousel::-webkit-scrollbar {
      display: none;
   }
   .mobile-pill {
      flex: 0 0 auto;
      padding: 7px 14px;
      border-radius: 999px;
      font-size: 12.5px;
      font-weight: 600;
      border: 1px solid #e2e8f0;
      background: #ffffff;
      color: #475569;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.15s ease;
      white-space: nowrap;
   }
   .mobile-pill.active {
      background: #0f172a;
      color: #ffffff;
      border-color: #0f172a;
   }
   .mobile-pill .pill-count {
      font-size: 11px;
      padding: 1px 6px;
      border-radius: 999px;
      background: #f1f5f9;
      color: #475569;
   }
   .mobile-pill.active .pill-count {
      background: rgba(255,255,255,0.2);
      color: #ffffff;
   }

   /* Mobile Invoices Feed */
   .mobile-invoices-feed {
      display: flex;
      flex-direction: column;
      gap: 14px;
   }

   .mobile-inv-card {
      background: #ffffff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 2px 8px rgba(15,23,42,0.04);
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      transition: transform 0.1s ease;
   }
   .mobile-inv-card-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 8px;
   }
   .mobile-inv-number {
      font-size: 14px;
      font-weight: 700;
      color: #0284c7;
      line-height: 1.3;
   }
   .mobile-inv-revision {
      font-size: 11px;
      color: #64748b;
      margin-top: 2px;
      font-weight: 500;
   }
   .mobile-inv-campaign {
      font-size: 14px;
      font-weight: 700;
      color: #0f172a;
      line-height: 1.35;
   }
   .mobile-inv-brand {
      font-size: 12px;
      color: #64748b;
      margin-top: 2px;
      display: flex;
      align-items: center;
      gap: 4px;
   }

   /* Financial Box within Card */
   .mobile-inv-finance-box {
      background: #f8fafc;
      border-radius: 8px;
      padding: 10px 12px;
      border: 1px solid #f1f5f9;
      display: flex;
      flex-direction: column;
      gap: 6px;
   }
   .mobile-inv-finance-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 12px;
   }
   .mobile-inv-finance-row.net {
      border-top: 1px dashed #e2e8f0;
      padding-top: 6px;
      margin-top: 2px;
      font-size: 13.5px;
      font-weight: 700;
   }
   .mobile-inv-finance-row.net .net-value {
      color: #16a34a;
      font-weight: 800;
      font-size: 15px;
   }

   /* Card Dates */
   .mobile-inv-dates {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 11.5px;
      color: #64748b;
      border-top: 1px solid #f8fafc;
      padding-top: 8px;
   }
   .mobile-inv-date-item {
      display: flex;
      align-items: center;
      gap: 4px;
   }

   /* Actions Bar */
   .mobile-inv-actions {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 8px;
      margin-top: 2px;
   }
   .mobile-inv-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      padding: 8px 10px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      border: 1px solid #e2e8f0;
      background: #ffffff;
      color: #334155;
      text-decoration: none !important;
      cursor: pointer;
      text-align: center;
   }
   .mobile-inv-btn.primary {
      background: #f0fdf4;
      border-color: #bbf7d0;
      color: #166534;
   }
   .mobile-inv-btn.disabled {
      opacity: 0.45;
      pointer-events: none;
      background: #f8fafc;
   }

   /* Empty State */
   .mobile-empty-state {
      background: #ffffff;
      border-radius: 12px;
      border: 1px dashed #cbd5e1;
      padding: 40px 20px;
      text-align: center;
      color: #64748b;
   }
   .mobile-empty-state i {
      font-size: 40px;
      color: #94a3b8;
      margin-bottom: 12px;
      display: block;
   }

   /* Modal Full Width on Mobile */
   .modal-dialog {
      margin: 10px !important;
      width: calc(100% - 20px) !important;
   }
   .modal-body {
      padding: 16px !important;
   }
}
</style>

<section class="content-header">
   <h1>
      Tagihan Saya
      <small>Portal Pengajuan &amp; Pelacakan Invoice KOL ke Brand</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Tagihan Saya</li>
   </ol>
</section>

<section class="content">
   <?php if ($this->session->flashdata('f_message')): ?>
      <div class="alert alert-<?= $this->session->flashdata('f_type') ?: 'info'; ?> alert-dismissible" style="border-radius: 6px;">
         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
         <?= $this->session->flashdata('f_message'); ?>
      </div>
   <?php endif; ?>

   <!-- 1. Stats Counter Cards -->
   <?php
      $cnt_submitted = 0;
      $cnt_approved = 0;
      $cnt_paid = 0;
      $total_paid_rp = 0;
      if (!empty($invoices)) {
         foreach ($invoices as $inv) {
            if ($inv->status == 'SUBMITTED') $cnt_submitted++;
            if ($inv->status == 'APPROVED') $cnt_approved++;
            if ($inv->status == 'PAID' || $inv->status == 'PARTIAL') $cnt_paid++;
            $total_paid_rp += (float)($inv->amount_paid ?? 0);
         }
      }
   ?>
   <!-- 1. Stats Counter Cards (Desktop Only) -->
   <div class="row desktop-only-section">
      <div class="col-md-3 col-sm-6">
         <div class="invoice-card-stat">
            <div class="icon-box" style="background: #fef3c7; color: #d97706;">
               <i class="fa fa-clock-o"></i>
            </div>
            <div>
               <div class="stat-label">Menunggu Review</div>
               <div class="stat-value"><?= $cnt_submitted; ?></div>
            </div>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="invoice-card-stat">
            <div class="icon-box" style="background: #e0f2fe; color: #0284c7;">
               <i class="fa fa-check-square-o"></i>
            </div>
            <div>
               <div class="stat-label">Disetujui / Siap Bayar</div>
               <div class="stat-value"><?= $cnt_approved; ?></div>
            </div>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="invoice-card-stat">
            <div class="icon-box" style="background: #dcfce7; color: #16a34a;">
               <i class="fa fa-check-circle"></i>
            </div>
            <div>
               <div class="stat-label">Tagihan Selesai</div>
               <div class="stat-value"><?= $cnt_paid; ?></div>
            </div>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="invoice-card-stat">
            <div class="icon-box" style="background: #f1f5f9; color: #334155;">
               <i class="fa fa-money"></i>
            </div>
            <div>
               <div class="stat-label">Total Diterima</div>
               <div class="stat-value" style="font-size: 16px;">Rp <?= number_format($total_paid_rp, 0, ',', '.'); ?></div>
            </div>
         </div>
      </div>
   </div>

   <!-- 2. Main Invoices Table Card (Desktop Only) -->
   <div class="row desktop-only-section">
      <div class="col-md-12">
         <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.08);">
            <div class="box-header with-border" style="padding: 16px 20px; background: #fafbfc; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
               <h3 class="box-title" style="font-size: 16px; font-weight: 700; color: #1e293b;">
                  <i class="fa fa-file-text text-primary" style="margin-right: 6px;"></i> Daftar Tagihan Saya (Invoices)
               </h3>
               <button type="button" class="btn btn-primary btn-flat pull-right" id="btn-open-create-invoice" style="border-radius: 6px; font-weight: 600;">
                  <i class="fa fa-plus-circle" style="margin-right: 5px;"></i> Buat Tagihan Baru
               </button>
            </div>
            
            <div class="box-body" style="padding: 20px;">
               <div class="table-responsive">
                  <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                     <thead>
                        <tr style="background: #f1f5f9; color: #334155; font-size: 12.5px;">
                           <th style="width: 40px; text-align: center;">No</th>
                           <th>Nomor Tagihan</th>
                           <th>Campaign &amp; Brand</th>
                           <th>Tgl Pengajuan</th>
                           <th>Jatuh Tempo</th>
                           <th style="text-align: right;">Nilai Jasa (Gross)</th>
                           <th style="text-align: right;">Pajak (PPh)</th>
                           <th style="text-align: right;">Net Transfer</th>
                           <th style="text-align: center;">Status</th>
                           <th style="text-align: center; width: 200px;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($invoices)): ?>
                           <?php $no = 1; foreach ($invoices as $inv): ?>
                              <tr>
                                 <td style="text-align: center; vertical-align: middle;"><?= $no++; ?></td>
                                 <td style="vertical-align: middle; font-weight: 700; color: #0284c7;">
                                    <?= htmlspecialchars($inv->invoice_no); ?>
                                    <?php if (!empty($inv->parent_invoice_id)): ?>
                                       <div style="font-size: 10.5px; color: #64748b;"><i class="fa fa-history"></i> Revisi #<?= $inv->parent_invoice_id; ?></div>
                                    <?php endif; ?>
                                 </td>
                                 <td style="vertical-align: middle;">
                                    <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($inv->campaign_name ?? 'N/A'); ?></div>
                                    <div style="font-size: 11.5px; color: #64748b;">Client: <?= htmlspecialchars($inv->brand_name ?? 'Brand'); ?></div>
                                 </td>
                                 <td style="vertical-align: middle; font-size: 12px; color: #475569;">
                                    <?= date('d M Y', strtotime($inv->created_at)); ?>
                                 </td>
                                 <td style="vertical-align: middle; font-size: 12px; color: #475569;">
                                    <?= !empty($inv->due_date) ? date('d M Y', strtotime($inv->due_date)) : '-'; ?>
                                 </td>
                                 <td style="vertical-align: middle; text-align: right; font-weight: 600; color: #1e293b;">
                                    Rp <?= number_format($inv->subtotal, 0, ',', '.'); ?>
                                 </td>
                                 <td style="vertical-align: middle; text-align: right; font-size: 12px; color: #dc2626;">
                                    <?php if ($inv->tax_amount > 0): ?>
                                       - Rp <?= number_format($inv->tax_amount, 0, ',', '.'); ?>
                                       <div style="font-size: 10px; color: #64748b;"><?= $inv->tax_type ?: 'PPh'; ?> (<?= (float)$inv->tax_rate_percent; ?>%)</div>
                                    <?php else: ?>
                                       <span style="color: #94a3b8;">Rp 0</span>
                                    <?php endif; ?>
                                 </td>
                                 <td style="vertical-align: middle; text-align: right; font-weight: 700; color: #16a34a;">
                                    Rp <?= number_format($inv->grand_total, 0, ',', '.'); ?>
                                    <?php if ($inv->status == 'PARTIAL'): ?>
                                       <div style="font-size: 10.5px; color: #d97706;">Terbayar: Rp <?= number_format($inv->amount_paid, 0, ',', '.'); ?></div>
                                    <?php endif; ?>
                                 </td>
                                 <td style="text-align: center; vertical-align: middle;">
                                    <span class="badge-status badge-status-<?= $inv->status; ?>">
                                       <?php
                                          if ($inv->status == 'SUBMITTED') echo '<i class="fa fa-clock-o"></i> Menunggu Review';
                                          elseif ($inv->status == 'APPROVED') echo '<i class="fa fa-check-circle-o"></i> Disetujui (Siap Bayar)';
                                          elseif ($inv->status == 'PARTIAL') echo '<i class="fa fa-adjust"></i> Dibayar Sebagian';
                                          elseif ($inv->status == 'PAID') echo '<i class="fa fa-check-circle"></i> Lunas';
                                          elseif ($inv->status == 'REJECTED') echo '<i class="fa fa-times-circle"></i> Ditolak';
                                          elseif ($inv->status == 'CANCELLED') echo '<i class="fa fa-ban"></i> Dibatalkan';
                                          else echo $inv->status;
                                       ?>
                                    </span>
                                 </td>
                                 <td style="text-align: center; vertical-align: middle;">
                                    <div class="btn-group">
                                       <!-- Detail Button -->
                                       <button type="button" class="btn btn-xs btn-default btn-view-detail" data-id="<?= $inv->id; ?>" title="Lihat Riwayat & Detail" style="border-radius: 4px 0 0 4px; padding: 4px 8px;">
                                          <i class="fa fa-eye text-primary"></i> Detail
                                       </button>

                                       <!-- PDF Download -->
                                       <a href="<?= site_url('administrator/my_invoices/download_pdf/' . $inv->id); ?>" target="_blank" class="btn btn-xs btn-default" title="Download Invoice PDF" style="padding: 4px 8px;">
                                          <i class="fa fa-file-pdf-o text-danger"></i> PDF
                                       </a>

                                       <!-- Bukti Potong Button -->
                                       <?php if (!empty($inv->bupot_file)): ?>
                                          <a href="<?= site_url('administrator/my_invoices/download_bupot/' . $inv->id); ?>" target="_blank" class="btn btn-xs btn-success" title="Download Bukti Potong Pajak (e-Bupot)" style="border-radius: 0 4px 4px 0; padding: 4px 8px; font-weight: 600;">
                                             <i class="fa fa-certificate"></i> Bupot
                                          </a>
                                       <?php else: ?>
                                          <button type="button" class="btn btn-xs btn-default disabled" title="Bukti potong belum diterbitkan" style="border-radius: 0 4px 4px 0; padding: 4px 8px; color: #cbd5e1;">
                                             <i class="fa fa-certificate"></i> Bupot
                                          </button>
                                       <?php endif; ?>
                                    </div>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="10" style="text-align: center; padding: 40px; color: #94a3b8;">
                                 <i class="fa fa-folder-open-o" style="font-size: 36px; margin-bottom: 10px; display: block;"></i>
                                 Belum ada tagihan yang diajukan.<br>
                                 Silakan klik tombol <b>"Buat Tagihan Baru"</b> untuk menagih pekerjaan yang telah selesai.
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

   <!-- 3. Mobile View Feed (Mobile Only) -->
   <div class="mobile-only-section">
      <!-- Mobile Action Bar -->
      <div class="mobile-action-bar">
         <div class="bar-title">
            Daftar Tagihan
            <span class="inv-count-badge" id="mobile-badge-count"><?= !empty($invoices) ? count($invoices) : 0; ?></span>
         </div>
         <button type="button" class="btn-mobile-create" id="btn-mobile-create-invoice">
            <i class="fa fa-plus-circle"></i> Buat Tagihan
         </button>
      </div>

      <!-- Search Bar -->
      <div class="mobile-search-wrapper">
         <i class="fa fa-search search-icon"></i>
         <input type="text" id="mobile-inv-search" placeholder="Cari nomor tagihan, campaign, brand..." autocomplete="off">
         <button type="button" class="search-clear" id="mobile-search-clear"><i class="fa fa-times-circle"></i></button>
      </div>

      <!-- Horizontal Pill Filter Carousel -->
      <div class="mobile-pills-carousel" id="mobile-filter-pills">
         <button type="button" class="mobile-pill active" data-filter="ALL">
            Semua <span class="pill-count"><?= !empty($invoices) ? count($invoices) : 0; ?></span>
         </button>
         <button type="button" class="mobile-pill" data-filter="SUBMITTED">
            Menunggu <span class="pill-count"><?= $cnt_submitted; ?></span>
         </button>
         <button type="button" class="mobile-pill" data-filter="APPROVED">
            Disetujui <span class="pill-count"><?= $cnt_approved; ?></span>
         </button>
         <button type="button" class="mobile-pill" data-filter="PAID">
            Lunas <span class="pill-count"><?= $cnt_paid; ?></span>
         </button>
         <button type="button" class="mobile-pill" data-filter="REJECTED">
            Ditolak
         </button>
      </div>

      <!-- Invoices Cards Feed -->
      <div class="mobile-invoices-feed" id="mobile-invoices-container">
         <?php if (!empty($invoices)): ?>
            <?php foreach ($invoices as $inv): ?>
               <div class="mobile-inv-card" 
                    data-id="<?= $inv->id; ?>"
                    data-status="<?= $inv->status; ?>"
                    data-invoice-no="<?= strtolower(htmlspecialchars($inv->invoice_no)); ?>"
                    data-campaign="<?= strtolower(htmlspecialchars($inv->campaign_name ?? '')); ?>"
                    data-brand="<?= strtolower(htmlspecialchars($inv->brand_name ?? '')); ?>">
                  
                  <div class="mobile-inv-card-header">
                     <div>
                        <div class="mobile-inv-number"><?= htmlspecialchars($inv->invoice_no); ?></div>
                        <?php if (!empty($inv->parent_invoice_id)): ?>
                           <div class="mobile-inv-revision"><i class="fa fa-history"></i> Revisi #<?= $inv->parent_invoice_id; ?></div>
                        <?php endif; ?>
                     </div>
                     <span class="badge-status badge-status-<?= $inv->status; ?>">
                        <?php
                           if ($inv->status == 'SUBMITTED') echo '<i class="fa fa-clock-o"></i> Menunggu';
                           elseif ($inv->status == 'APPROVED') echo '<i class="fa fa-check-circle-o"></i> Disetujui';
                           elseif ($inv->status == 'PARTIAL') echo '<i class="fa fa-adjust"></i> Sebagian';
                           elseif ($inv->status == 'PAID') echo '<i class="fa fa-check-circle"></i> Lunas';
                           elseif ($inv->status == 'REJECTED') echo '<i class="fa fa-times-circle"></i> Ditolak';
                           elseif ($inv->status == 'CANCELLED') echo '<i class="fa fa-ban"></i> Batal';
                           else echo $inv->status;
                        ?>
                     </span>
                  </div>

                  <div>
                     <div class="mobile-inv-campaign"><?= htmlspecialchars($inv->campaign_name ?? 'N/A'); ?></div>
                     <div class="mobile-inv-brand"><i class="fa fa-building-o"></i> Client: <?= htmlspecialchars($inv->brand_name ?? 'Brand'); ?></div>
                  </div>

                  <div class="mobile-inv-finance-box">
                     <div class="mobile-inv-finance-row">
                        <span style="color: #64748b;">Gross Fee</span>
                        <span style="font-weight: 600; color: #1e293b;">Rp <?= number_format($inv->subtotal, 0, ',', '.'); ?></span>
                     </div>
                     <div class="mobile-inv-finance-row">
                        <span style="color: #64748b;">Potongan PPh</span>
                        <span style="color: #dc2626; font-weight: 600;">
                           <?php if ($inv->tax_amount > 0): ?>
                              - Rp <?= number_format($inv->tax_amount, 0, ',', '.'); ?> <span style="font-size: 10px; color: #64748b;">(<?= (float)$inv->tax_rate_percent; ?>%)</span>
                           <?php else: ?>
                              Rp 0
                           <?php endif; ?>
                        </span>
                     </div>
                     <div class="mobile-inv-finance-row net">
                        <span style="color: #0f172a;">Net Transfer</span>
                        <span class="net-value">Rp <?= number_format($inv->grand_total, 0, ',', '.'); ?></span>
                     </div>
                     <?php if ($inv->status == 'PARTIAL'): ?>
                        <div style="font-size: 11px; color: #d97706; text-align: right; margin-top: 2px;">
                           Terbayar: Rp <?= number_format($inv->amount_paid, 0, ',', '.'); ?>
                        </div>
                     <?php endif; ?>
                  </div>

                  <div class="mobile-inv-dates">
                     <div class="mobile-inv-date-item">
                        <i class="fa fa-calendar-o"></i> Diajukan: <?= date('d M Y', strtotime($inv->created_at)); ?>
                     </div>
                     <div class="mobile-inv-date-item">
                        <i class="fa fa-clock-o"></i> Jatuh Tempo: <?= !empty($inv->due_date) ? date('d M Y', strtotime($inv->due_date)) : '-'; ?>
                     </div>
                  </div>

                  <div class="mobile-inv-actions">
                     <button type="button" class="mobile-inv-btn btn-view-detail" data-id="<?= $inv->id; ?>">
                        <i class="fa fa-eye text-primary"></i> Detail
                     </button>
                     <a href="<?= site_url('administrator/my_invoices/download_pdf/' . $inv->id); ?>" target="_blank" class="mobile-inv-btn">
                        <i class="fa fa-file-pdf-o text-danger"></i> PDF
                     </a>
                     <?php if (!empty($inv->bupot_file)): ?>
                        <a href="<?= site_url('administrator/my_invoices/download_bupot/' . $inv->id); ?>" target="_blank" class="mobile-inv-btn primary">
                           <i class="fa fa-certificate"></i> Bupot
                        </a>
                     <?php else: ?>
                        <button type="button" class="mobile-inv-btn disabled" title="Bukti potong belum tersedia">
                           <i class="fa fa-certificate"></i> Bupot
                        </button>
                     <?php endif; ?>
                  </div>

               </div>
            <?php endforeach; ?>
         <?php else: ?>
            <div class="mobile-empty-state">
               <i class="fa fa-folder-open-o"></i>
               <div style="font-weight: 700; color: #1e293b; margin-bottom: 4px;">Belum Ada Tagihan</div>
               <div style="font-size: 13px;">Silakan buat tagihan baru untuk menagih pekerjaan yang sudah selesai.</div>
            </div>
         <?php endif; ?>

         <div class="mobile-empty-state" id="mobile-no-search-results" style="display: none;">
            <i class="fa fa-search"></i>
            <div style="font-weight: 700; color: #1e293b; margin-bottom: 4px;">Tidak Ada Tagihan Ditemukan</div>
            <div style="font-size: 13px;">Coba gunakan kata kunci pencarian atau filter status yang lain.</div>
         </div>
      </div>
   </div>
</section>

<!-- ============================================================================== -->
<!-- MODAL: BUAT TAGIHAN BARU (Create New Invoice) -->
<!-- ============================================================================== -->
<div class="modal fade" id="modal-create-invoice" tabindex="-1" role="dialog" data-backdrop="static">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
         <div class="modal-header" style="background: #0f172a; color: #ffffff; padding: 16px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;">
               <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" style="font-weight: 700;">
               <i class="fa fa-plus-circle text-primary" style="margin-right: 6px;"></i> Buat &amp; Ajukan Tagihan Baru (Invoice)
            </h4>
         </div>

         <form id="form-create-invoice" enctype="multipart/form-data">
            <input type="hidden" class="csrf_token" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="modal-body" style="padding: 24px;">
               <!-- Notice Banner -->
               <div class="alert alert-info" style="border-radius: 6px; background-color: #f0f9ff; border-color: #bae6fd; color: #0369a1; font-size: 12.5px; margin-bottom: 20px;">
                  <i class="fa fa-info-circle" style="margin-right: 5px;"></i>
                  Sesuai alur penagihan vendor, Anda menerbitkan tagihan resmi kepada Brand. Brand bertindak sebagai Pemotong Pajak (PPh 21/23) dan akan memverifikasi tagihan sebelum mencairkan dana.
               </div>

               <!-- Step 1: Select Deal -->
               <div class="form-group">
                  <label style="font-weight: 700; color: #1e293b;">1. Pilih Pekerjaan / Campaign yang Ingin Ditagihkan <span class="text-danger">*</span></label>
                  <select name="campaign_kol_id" id="sel_deal" class="form-control" style="height: 42px; border-radius: 6px;" required>
                     <option value="">-- Pilih Kerja Sama yang Memenuhi Syarat --</option>
                     <?php if (!empty($billable_deals)): ?>
                        <?php foreach ($billable_deals as $bd): ?>
                           <option value="<?= $bd->campaign_kol_id; ?>" 
                              data-amount="<?= (float)$bd->deal_amount; ?>"
                              data-campaign="<?= htmlspecialchars($bd->campaign_name); ?>"
                              data-brand="<?= htmlspecialchars($bd->brand_name); ?>">
                              <?= htmlspecialchars($bd->campaign_name); ?> (<?= htmlspecialchars($bd->brand_name); ?>) — Rp <?= number_format($bd->deal_amount, 0, ',', '.'); ?>
                           </option>
                        <?php endforeach; ?>
                     <?php else: ?>
                        <option value="" disabled>Tidak ada kerja sama aktif yang siap ditagihkan</option>
                     <?php endif; ?>
                  </select>
               </div>

               <!-- Tax Simulation Card -->
               <div id="box-calculation-preview" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                  <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">
                     <i class="fa fa-calculator text-primary"></i> Rincian Finansial &amp; Estimasi Pajak:
                  </div>
                  <table style="width: 100%; font-size: 13px;">
                     <tr>
                        <td style="color: #64748b; padding: 4px 0;">Nilai Kerja Sama (Gross Fee):</td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;" id="lbl_subtotal">Rp 0</td>
                     </tr>
                     <tr>
                        <td style="color: #64748b; padding: 4px 0;">
                           Estimasi Potongan Pajak: <span id="lbl_tax_tag" class="label label-default" style="font-size: 10.5px;">-</span>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #dc2626;" id="lbl_tax_amount">- Rp 0</td>
                     </tr>
                     <tr>
                        <td colspan="2"><div style="border-top: 1px solid #cbd5e1; margin: 8px 0;"></div></td>
                     </tr>
                     <tr style="font-size: 14px;">
                        <td style="font-weight: 700; color: #0f172a;">Estimasi Net Transfer Diterima:</td>
                        <td style="text-align: right; font-weight: 800; color: #16a34a;" id="lbl_net_amount">Rp 0</td>
                     </tr>
                  </table>
                  <div style="font-size: 11px; color: #94a3b8; margin-top: 8px; font-style: italic;">
                     * Brand akan memotong dan menyetorkan pajak penghasilan resmi ke DJP dan mengunggah Bukti Potong ke portal ini.
                  </div>
               </div>

               <!-- Step 2: Bank Account Details -->
               <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 15px; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                  2. Informasi Rekening Bank Penerima Dana
               </div>
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Nama Bank <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" id="in_bank_name" class="form-control" placeholder="Contoh: BCA, Mandiri, BRI" value="<?= htmlspecialchars($kol->bank_name ?? ''); ?>" required>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Nomor Rekening <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account_number" id="in_bank_account_number" class="form-control" placeholder="Nomor rekening" value="<?= htmlspecialchars($kol->bank_account_number ?? ''); ?>" required>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Atas Nama (Pemilik Rekening) <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account_name" id="in_bank_account_name" class="form-control" placeholder="Nama pemilik rekening" value="<?= htmlspecialchars($kol->bank_account_name ?? ''); ?>" required>
                     </div>
                  </div>
               </div>

               <!-- Step 3: Optional Physical / E-Meterai Signed Invoice Attachment -->
               <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 15px; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                  3. Berkas Invoice Fisik / Bertanda Tangan (Opsional)
               </div>
               <div class="form-group">
                  <label style="font-size: 12.5px; color: #475569;">
                     Jika Anda memiliki berkas invoice mandiri (PDF/Scan bertanda tangan/e-meterai), unggah di sini. Bila tidak diunggah, sistem akan otomatis menghasilkan PDF invoice standar resmi.
                  </label>
                  <input type="file" name="invoice_file" id="in_invoice_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                  <span class="help-block" style="font-size: 11px; color: #94a3b8;">Format: PDF, JPG, PNG (Maks 5 MB)</span>
               </div>
            </div>

            <div class="modal-footer" style="background: #fafbfc; border-top: 1px solid #e2e8f0; padding: 14px 20px;">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 6px;">Batal</button>
               <button type="submit" class="btn btn-primary btn-flat" id="btn-submit-invoice" style="border-radius: 6px; font-weight: 700;">
                  <i class="fa fa-send" style="margin-right: 5px;"></i> Ajukan Tagihan Sekarang
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- ============================================================================== -->
<!-- MODAL: DETAIL INVOICE & AUDIT TIMELINE -->
<!-- ============================================================================== -->
<div class="modal fade" id="modal-invoice-detail" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
         <div class="modal-header" style="background: #0f172a; color: #ffffff; padding: 16px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;">
               <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" style="font-weight: 700;">
               <i class="fa fa-file-text-o text-primary" style="margin-right: 6px;"></i> Detail Tagihan: <span id="dt_invoice_no"></span>
            </h4>
         </div>

         <div class="modal-body" style="padding: 24px;">
            <!-- Rejection Alert Banner -->
            <div id="dt_box_rejected" class="alert alert-danger" style="display: none; border-radius: 6px;">
               <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px;">
                  <i class="fa fa-times-circle"></i> Tagihan Ditolak oleh Tim Finance
               </div>
               <div id="dt_rejection_reason" style="font-size: 12.5px; margin-bottom: 10px;"></div>
               <button type="button" class="btn btn-sm btn-danger btn-flat btn-recreate-from-rejected" style="border-radius: 4px; font-weight: 700;">
                  <i class="fa fa-refresh"></i> Ajukan Revisi Tagihan Baru
               </button>
            </div>

            <!-- Summary Info -->
            <div class="row" style="margin-bottom: 20px;">
               <div class="col-md-6">
                  <table class="table table-bordered" style="font-size: 12.5px;">
                     <tr>
                        <td style="width: 40%; background: #f8fafc; font-weight: 600;">Campaign</td>
                        <td id="dt_campaign_name" style="font-weight: 700; color: #1e293b;"></td>
                     </tr>
                     <tr>
                        <td style="background: #f8fafc; font-weight: 600;">Brand (Buyer)</td>
                        <td id="dt_brand_name"></td>
                     </tr>
                     <tr>
                        <td style="background: #f8fafc; font-weight: 600;">Tgl Diajukan</td>
                        <td id="dt_created_at"></td>
                     </tr>
                     <tr>
                        <td style="background: #f8fafc; font-weight: 600;">Status</td>
                        <td id="dt_status_badge"></td>
                     </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-bordered" style="font-size: 12.5px;">
                     <tr>
                        <td style="width: 40%; background: #f8fafc; font-weight: 600;">Gross (Subtotal)</td>
                        <td id="dt_subtotal" style="font-weight: 700;"></td>
                     </tr>
                     <tr>
                        <td style="background: #f8fafc; font-weight: 600;">Potongan Pajak</td>
                        <td id="dt_tax" style="color: #dc2626; font-weight: 600;"></td>
                     </tr>
                     <tr>
                        <td style="background: #f8fafc; font-weight: 600;">Net Transfer</td>
                        <td id="dt_grand_total" style="font-weight: 800; color: #16a34a; font-size: 14px;"></td>
                     </tr>
                     <tr>
                        <td style="background: #f8fafc; font-weight: 600;">Bukti Potong (Bupot)</td>
                        <td id="dt_bupot_status"></td>
                     </tr>
                  </table>
               </div>
            </div>

            <!-- Bank Destination Info -->
            <div style="background: #f1f5f9; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; font-size: 12.5px;">
               <div style="font-weight: 700; color: #334155; margin-bottom: 4px;">
                  <i class="fa fa-university"></i> Rekening Tujuan Pembayaran:
               </div>
               <div id="dt_bank_info" style="color: #0f172a; font-weight: 600;"></div>
            </div>

            <!-- Payment Records -->
            <div id="dt_box_payments" style="display: none; margin-bottom: 20px;">
               <div style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                  <i class="fa fa-check-circle text-success"></i> Riwayat Pembayaran Masuk:
               </div>
               <table class="table table-bordered table-striped" style="font-size: 12px;">
                  <thead>
                     <tr style="background: #f8fafc;">
                        <th>Tgl Pembayaran</th>
                        <th>Nominal</th>
                        <th>Metode Transfer</th>
                        <th>Catatan</th>
                     </tr>
                  </thead>
                  <tbody id="dt_payments_list"></tbody>
               </table>
            </div>

            <!-- Audit Trail Timeline -->
            <div style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">
               <i class="fa fa-history text-primary"></i> Riwayat &amp; Status Log (Audit Trail):
            </div>
            <ul class="timeline-audit" id="dt_histories_list"></ul>
         </div>

         <div class="modal-footer" style="background: #fafbfc; border-top: 1px solid #e2e8f0; padding: 14px 20px;">
            <a href="#" id="dt_btn_download_pdf" target="_blank" class="btn btn-default btn-flat" style="border-radius: 6px;">
               <i class="fa fa-file-pdf-o text-danger"></i> Unduh Invoice PDF
            </a>
            <a href="#" id="dt_btn_download_bupot" target="_blank" class="btn btn-success btn-flat" style="border-radius: 6px; display: none;">
               <i class="fa fa-certificate"></i> Unduh Bukti Potong Pajak
            </a>
            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 6px;">Tutup</button>
         </div>
      </div>
   </div>
</div>

<script>
$(document).ready(function() {
   // 1. Open create invoice modal (Desktop & Mobile)
   $('#btn-open-create-invoice, #btn-mobile-create-invoice').on('click', function() {
      $('#form-create-invoice')[0].reset();
      $('#box-calculation-preview').hide();
      $('#modal-create-invoice').modal('show');
   });

   // 1b. Mobile Client-Side Filter & Search
   var activeFilter = 'ALL';

   function filterMobileInvoices() {
      var query = ($('#mobile-inv-search').val() || '').toLowerCase().trim();
      var visibleCount = 0;

      $('.mobile-inv-card').each(function() {
         var $card = $(this);
         var status = $card.data('status');
         var invNo = ($card.data('invoice-no') || '').toString();
         var camp = ($card.data('campaign') || '').toString();
         var brand = ($card.data('brand') || '').toString();

         var matchFilter = (activeFilter === 'ALL') || (status === activeFilter);
         var matchQuery = !query || invNo.indexOf(query) !== -1 || camp.indexOf(query) !== -1 || brand.indexOf(query) !== -1;

         if (matchFilter && matchQuery) {
            $card.show();
            visibleCount++;
         } else {
            $card.hide();
         }
      });

      if (visibleCount === 0 && $('.mobile-inv-card').length > 0) {
         $('#mobile-no-search-results').show();
      } else {
         $('#mobile-no-search-results').hide();
      }
   }

   $('#mobile-filter-pills .mobile-pill').on('click', function() {
      $('#mobile-filter-pills .mobile-pill').removeClass('active');
      $(this).addClass('active');
      activeFilter = $(this).data('filter');
      filterMobileInvoices();
   });

   $('#mobile-inv-search').on('input', function() {
      var q = $(this).val();
      if (q.length > 0) {
         $('#mobile-search-clear').show();
      } else {
         $('#mobile-search-clear').hide();
      }
      filterMobileInvoices();
   });

   $('#mobile-search-clear').on('click', function() {
      $('#mobile-inv-search').val('');
      $(this).hide();
      filterMobileInvoices();
   });

   // 2. Deal selection change -> Calculate tax simulation
   $('#sel_deal').on('change', function() {
      var dealId = $(this).val();
      if (!dealId) {
         $('#box-calculation-preview').hide();
         return;
      }

      $.ajax({
         url: '<?= site_url("administrator/my_invoices/get_deal_detail_ajax/"); ?>' + dealId,
         type: 'GET',
         dataType: 'json',
         success: function(res) {
            if (res.success && res.deal) {
               var d = res.deal;
               $('#lbl_subtotal').text('Rp ' + d.subtotal_fmt);
               $('#lbl_tax_tag').text(d.tax_type + ' ' + d.tax_rate + '% (' + (d.has_npwp ? 'NPWP: ' + d.npwp_number : 'Non-NPWP') + ')');
               $('#lbl_tax_amount').text('- Rp ' + d.tax_amount_fmt);
               $('#lbl_net_amount').text('Rp ' + d.net_amount_fmt);
               
               if (d.bank_name) $('#in_bank_name').val(d.bank_name);
               if (d.bank_account_number) $('#in_bank_account_number').val(d.bank_account_number);
               if (d.bank_account_name) $('#in_bank_account_name').val(d.bank_account_name);

               $('#box-calculation-preview').slideDown(200);
            } else {
               $('#box-calculation-preview').hide();
               alert(res.message || 'Gagal memuat rincian kerja sama.');
            }
         },
         error: function() {
            $('#box-calculation-preview').hide();
         }
      });
   });

   // 3. Submit invoice form
   $('#form-create-invoice').on('submit', function(e) {
      e.preventDefault();

      var $btn = $('#btn-submit-invoice');
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

      var formData = new FormData(this);
      if (typeof csrf !== 'undefined' && typeof token !== 'undefined') {
         if (!formData.has(csrf)) {
            formData.append(csrf, token);
         }
      }

      $.ajax({
         url: '<?= site_url("administrator/my_invoices/submit_invoice"); ?>',
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         dataType: 'json',
         success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-send"></i> Ajukan Tagihan Sekarang');
            if (res.csrf_token && typeof csrf !== 'undefined') {
               token = res.csrf_token;
               $('input[name="' + csrf + '"]').val(res.csrf_token);
            }
            if (res.success) {
               alert(res.message);
               window.location.reload();
            } else {
               alert(res.message || 'Terjadi kesalahan saat mengajukan tagihan.');
            }
         },
         error: function(xhr, status, err) {
            $btn.prop('disabled', false).html('<i class="fa fa-send"></i> Ajukan Tagihan Sekarang');
            if (xhr.status === 403) {
               alert('Sesi form kedaluwarsa atau token keamanan tidak valid. Halaman akan dimuat ulang.');
               window.location.reload();
               return;
            }
            var msg = 'Gagal menghubungi server. Silakan periksa koneksi Anda.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
               msg = xhr.responseJSON.message;
            } else if (xhr.responseText) {
               try {
                  var p = JSON.parse(xhr.responseText);
                  if (p.message) msg = p.message;
               } catch(e) {}
            }
            alert(msg);
         }
      });
   });

   // 4. View Invoice Detail Modal
   $(document).on('click', '.btn-view-detail', function() {
      var id = $(this).data('id');
      if (!id) return;

      $.ajax({
         url: '<?= site_url("administrator/my_invoices/get_invoice_detail_ajax/"); ?>' + id,
         type: 'GET',
         dataType: 'json',
         success: function(res) {
            if (!res.success || !res.invoice) {
               alert(res.message || 'Gagal memuat detail invoice.');
               return;
            }

            var inv = res.invoice;
            var hist = res.histories || [];
            var pmts = res.payments || [];

            $('#dt_invoice_no').text(inv.invoice_no);
            $('#dt_campaign_name').text(inv.campaign_name || '-');
            $('#dt_brand_name').text(inv.brand_name || 'Brand');
            $('#dt_created_at').text(inv.created_at);
            
            var statusMap = {
               'SUBMITTED': '<span class="badge-status badge-status-SUBMITTED">Menunggu Review</span>',
               'APPROVED': '<span class="badge-status badge-status-APPROVED">Disetujui (Siap Bayar)</span>',
               'PARTIAL': '<span class="badge-status badge-status-PARTIAL">Dibayar Sebagian</span>',
               'PAID': '<span class="badge-status badge-status-PAID">Lunas</span>',
               'REJECTED': '<span class="badge-status badge-status-REJECTED">Ditolak</span>',
               'CANCELLED': '<span class="badge-status badge-status-CANCELLED">Dibatalkan</span>'
            };
            $('#dt_status_badge').html(statusMap[inv.status] || inv.status);

            $('#dt_subtotal').text('Rp ' + Number(inv.subtotal).toLocaleString('id-ID'));
            if (Number(inv.tax_amount) > 0) {
               $('#dt_tax').text('- Rp ' + Number(inv.tax_amount).toLocaleString('id-ID') + ' (' + (inv.tax_type || 'PPh') + ' ' + Number(inv.tax_rate_percent) + '%)');
            } else {
               $('#dt_tax').text('Rp 0');
            }
            $('#dt_grand_total').text('Rp ' + Number(inv.grand_total).toLocaleString('id-ID'));

            if (inv.bupot_file) {
               $('#dt_bupot_status').html('<span class="label label-success"><i class="fa fa-check"></i> Tersedia (' + (inv.bupot_no || 'No. Bupot') + ')</span>');
               $('#dt_btn_download_bupot').attr('href', '<?= site_url("administrator/my_invoices/download_bupot/"); ?>' + inv.id).show();
            } else {
               $('#dt_bupot_status').html('<span class="label label-default">Belum Diterbitkan</span>');
               $('#dt_btn_download_bupot').hide();
            }

            $('#dt_btn_download_pdf').attr('href', '<?= site_url("administrator/my_invoices/download_pdf/"); ?>' + inv.id);

            $('#dt_bank_info').text((inv.bank_name || '-') + ' — ' + (inv.bank_account_number || '-') + ' a/n ' + (inv.bank_account_name || '-'));

            // Rejection banner
            if (inv.status === 'REJECTED') {
               $('#dt_box_rejected').show();
               $('#dt_rejection_reason').text('Alasan Penolakan: ' + (inv.rejection_reason || 'Tidak ada alasan khusus dicantumkan.'));
               $('.btn-recreate-from-rejected').data('deal-id', inv.campaign_kol_id);
            } else {
               $('#dt_box_rejected').hide();
            }

            // Payment records table
            if (pmts.length > 0) {
               var pHtml = '';
               for (var i = 0; i < pmts.length; i++) {
                  pHtml += '<tr>' +
                     '<td>' + pmts[i].payment_date + '</td>' +
                     '<td style="font-weight: 700; color: #16a34a;">Rp ' + Number(pmts[i].amount).toLocaleString('id-ID') + '</td>' +
                     '<td>' + (pmts[i].payment_method || '-') + '</td>' +
                     '<td>' + (pmts[i].notes || '-') + '</td>' +
                  '</tr>';
               }
               $('#dt_payments_list').html(pHtml);
               $('#dt_box_payments').show();
            } else {
               $('#dt_box_payments').hide();
            }

            // History timeline
            var hHtml = '';
            if (hist.length > 0) {
               for (var j = 0; j < hist.length; j++) {
                  hHtml += '<li>' +
                     '<div class="circle-dot"></div>' +
                     '<div class="time">' + hist[j].created_at + ' &bull; ' + (hist[j].actor_name || 'Sistem') + '</div>' +
                     '<div class="action-title">' + hist[j].action + '</div>' +
                     '<div class="notes">' + (hist[j].notes || '') + '</div>' +
                  '</li>';
               }
            } else {
               hHtml = '<li style="color: #94a3b8; font-style: italic;">Belum ada catatan log.</li>';
            }
            $('#dt_histories_list').html(hHtml);

            $('#modal-invoice-detail').modal('show');
         },
         error: function() {
            alert('Gagal mengambil data detail invoice.');
         }
      });
   });

   // Re-create from rejected
   $(document).on('click', '.btn-recreate-from-rejected', function() {
      var dealId = $(this).data('deal-id');
      $('#modal-invoice-detail').modal('hide');
      $('#btn-open-create-invoice').trigger('click');
      if (dealId) {
         $('#sel_deal').val(dealId).trigger('change');
      }
   });
});
</script>
