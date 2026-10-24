<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style type="text/css">
   /* ----------------------------------------------------
      MODERN LEADERBOARD & PERFORMANCE ANALYTICS STYLES
      ---------------------------------------------------- */
   .lb-container {
      padding: 0;
   }

   /* Stat KPI Cards */
   .lb-stat-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 14px 16px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.02);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
   }
   .lb-stat-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
   }
   .lb-stat-title {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #64748b;
   }
   .lb-stat-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      background: #f8fafc;
      color: #3b82f6;
   }
   .lb-stat-value {
      font-size: 22px;
      font-weight: 700;
      color: #0f172a;
      letter-spacing: -0.02em;
      line-height: 1.2;
   }
   .lb-stat-desc {
      font-size: 11.5px;
      color: #64748b;
      margin-top: 6px;
   }

   /* Content Cards */
   .lb-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.02);
   }
   .lb-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid #f1f5f9;
   }
   .lb-card-title {
      font-size: 14px;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
      display: inline-flex;
      align-items: center;
      gap: 8px;
   }

   /* Badges & Pills */
   .badge-soft {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 8px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 600;
   }
   .badge-soft-blue { background: #eff6ff; color: #2563eb; }
   .badge-soft-emerald { background: #f0fdf4; color: #166534; }
   .badge-soft-amber { background: #fffbeb; color: #b45309; }
   .badge-soft-purple { background: #faf5ff; color: #7e22ce; }
   .badge-soft-slate { background: #f1f5f9; color: #475569; }

   /* Rank Badges */
   .rank-badge {
      width: 26px;
      height: 26px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      font-weight: 700;
      font-size: 12px;
   }
   .rank-1 { background: linear-gradient(135deg, #fef08a, #f59e0b); color: #fff; border: none; box-shadow: 0 2px 4px rgba(245,158,11,0.2); text-shadow: 0 1px 1px rgba(0,0,0,0.1); }
   .rank-2 { background: linear-gradient(135deg, #e2e8f0, #94a3b8); color: #fff; border: none; box-shadow: 0 2px 4px rgba(148,163,184,0.2); text-shadow: 0 1px 1px rgba(0,0,0,0.1); }
   .rank-3 { background: linear-gradient(135deg, #fed7aa, #d97706); color: #fff; border: none; box-shadow: 0 2px 4px rgba(217,119,6,0.2); text-shadow: 0 1px 1px rgba(0,0,0,0.1); }
   .rank-other { background: #f8fafc; color: #94a3b8; font-size: 11px; }

   /* Table Clean */
   .table-clean {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
   }
   .table-clean th {
      background: #f8fafc;
      color: #64748b;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      padding: 10px 14px;
      border-bottom: 1px solid #e2e8f0;
      border-top: none;
   }
   .table-clean td {
      padding: 12px 14px;
      vertical-align: middle;
      border-bottom: 1px solid #f1f5f9;
      color: #334155;
      font-size: 12.5px;
   }
   .table-clean tbody tr:hover {
      background: #f8faff;
   }

   /* Compare UI Bar */
   .compare-action-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      padding: 12px 16px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      margin-bottom: 16px;
   }
   .btn-compare-main {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      font-weight: 600;
      font-size: 12.5px;
      border-radius: 6px;
      background: #2563eb;
      color: #ffffff;
      border: none;
      cursor: pointer;
      transition: all 0.15s ease;
   }
   .btn-compare-main:hover:not([disabled]) {
      background: #1d4ed8;
      box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
   }
   .btn-compare-main[disabled] {
      background: #cbd5e1;
      color: #64748b;
      cursor: not-allowed;
      box-shadow: none;
   }

   /* Comparison Modal Grid */
   .kol-compare-grid {
      display: flex;
      gap: 12px;
      overflow-x: auto;
      padding-bottom: 6px;
   }
   .kol-compare-card {
      flex: 1;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 16px 14px;
      min-width: 160px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
   }
   .kol-compare-card .kol-avatar-circle {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: #f1f5f9;
      color: #334155;
      font-size: 16px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 10px;
      border: 1px solid #e2e8f0;
   }
   .kol-compare-card .kol-cname {
      font-weight: 700;
      font-size: 13px;
      color: #0f172a;
      text-align: center;
      margin-bottom: 2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
   }
   .kol-compare-card .kol-ctier {
      text-align: center;
      margin-bottom: 12px;
   }
   .kol-compare-metric {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 5px 0;
      font-size: 12px;
   }
   .kol-compare-metric .metric-label { color: #64748b; font-size: 11.5px; }
   .kol-compare-metric .metric-value { font-weight: 700; color: #0f172a; }
   .metric-best { color: #059669 !important; font-weight: 800 !important; }

   .compare-chart-card {
      margin-top: 18px;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      background: #ffffff;
      padding: 16px;
   }
   .chart-nav-btn {
      padding: 5px 12px;
      font-size: 11.5px;
      font-weight: 600;
      border-radius: 6px;
      border: 1px solid #cbd5e1;
      background: #ffffff;
      color: #475569;
      cursor: pointer;
      transition: all 0.15s ease;
   }
   .chart-nav-btn:hover {
      background: #f8fafc;
      color: #0f172a;
   }
   .chart-nav-btn.active {
      background: #2563eb;
      color: #ffffff;
      border-color: #2563eb;
   }

   /* ==========================================================================
      MOBILE (< 768px)
      ========================================================================== */
   @media (max-width: 767px) {
      /* Pencari tabel: full width, bukan 230px fixed */
      #table-search {
         width: 100% !important;
         height: 38px !important;
         font-size: 13px !important;
      }
      #table-search + .fa,
      .fa-search {
         top: 13px !important;
      }

      /* Header kartu: judul dan pencarian jadi dua baris */
      .lb-card > div:first-child {
         flex-direction: column !important;
         align-items: stretch !important;
         padding: 14px !important;
      }

      /* Baris compare: tombol & hint jadi bertumpuk */
      .compare-action-bar,
      #compare-action-bar {
         flex-direction: column !important;
         align-items: stretch !important;
      }
      .compare-action-bar > div,
      #compare-action-bar > div {
         flex-wrap: wrap !important;
      }
      .btn-compare-main {
         width: 100%;
         justify-content: center;
         min-height: 40px;
      }
      #compare-action-bar > span.badge-soft {
         text-align: center;
      }

      /* Kartu perbandingan KOL: 1 kolom, bukan scroll horizontal */
      .kol-compare-grid {
         flex-direction: column !important;
         overflow-x: visible !important;
      }
      .kol-compare-card {
         min-width: 0 !important;
      }

      /* Header halaman */
      .content-header {
         padding: 12px 12px 0 12px !important;
      }
      .content-header > div {
         flex-direction: column !important;
         align-items: stretch !important;
      }
      /* Navigasi periode (chart) */
      .chart-nav-btn {
         flex: 1 1 auto;
         min-height: 36px;
      }
   }
</style>

<!-- Header -->
<section class="content-header" style="padding: 15px 15px 0 15px;">
   <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px;">
      <div>
         <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
            Leaderboard &amp; Evaluasi KOL <small style="font-size: 13px; color: #64748b; font-weight: normal;">Peringkat Performa, Skor Historis, dan Benchmarking Talent</small>
         </h1>
         <ol class="breadcrumb" style="background: transparent; padding: 0; margin-top: 4px; font-size: 12px;">
            <li><a href="<?= site_url('administrator/dashboard'); ?>" style="color: #64748b;"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#" style="color: #64748b;">Evaluasi</a></li>
            <li class="active" style="color: #0f172a; font-weight: 600;">Leaderboard KOL</li>
         </ol>
      </div>
      <div>
         <a href="<?= site_url('administrator/kols'); ?>" class="btn btn-default btn-sm btn-flat" style="border-radius: 6px; border-color: #cbd5e1; color: #334155; font-weight: 600;">
            <i class="fa fa-users" style="margin-right: 4px; color: #2563eb;"></i> Database Profil KOL
         </a>
      </div>
   </div>
</section>

<!-- Main Content -->
<section class="content" style="padding: 15px;">

   <!-- 1. TOP 4 KPI CARDS -->
   <div class="row">
      
      <!-- Card 1: Top Performer #1 -->
      <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
         <div class="lb-stat-card">
            <div class="lb-stat-header">
               <span class="lb-stat-title">Top Performer #1</span>
               <?php if (!empty($top_kol->avatar_url)): ?>
                   <img src="<?= htmlspecialchars($top_kol->avatar_url); ?>" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #fde68a; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
               <?php else: ?>
                   <div class="lb-stat-icon" style="background: #fef3c7; color: #b45309; border-radius: 50%;"><i class="fa fa-trophy"></i></div>
               <?php endif; ?>
            </div>
            <div>
               <div class="lb-stat-value" style="font-size: 18px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  <?= $top_kol ? htmlspecialchars($top_kol->name) : '-'; ?>
               </div>
               <div class="lb-stat-desc">
                  <span class="badge-soft badge-soft-blue"><?= $top_kol ? htmlspecialchars($top_kol->tier_name ?: 'Standard') : '-'; ?></span>
                  &bull; Score: <strong style="color: #2563eb;"><?= $top_kol ? number_format($top_kol->lifetime_score, 1) : '0'; ?></strong>
               </div>
            </div>
         </div>
      </div>

      <!-- Card 2: Total Active Talents -->
      <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
         <div class="lb-stat-card">
            <div class="lb-stat-header">
               <span class="lb-stat-title">Total Talent Aktif</span>
               <div class="lb-stat-icon" style="background: #eff6ff; color: #2563eb;"><i class="fa fa-users"></i></div>
            </div>
            <div>
               <div class="lb-stat-value"><?= number_format($total_active_kols); ?></div>
               <div class="lb-stat-desc">Talent KOL berstatus aktif</div>
            </div>
         </div>
      </div>

      <!-- Card 3: Total Completed Tasks (Live) -->
      <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
         <div class="lb-stat-card">
            <div class="lb-stat-header">
               <span class="lb-stat-title">Total VT Selesai (Live)</span>
               <div class="lb-stat-icon" style="background: #f0fdf4; color: #059669;"><i class="fa fa-video-camera"></i></div>
            </div>
            <div>
               <div class="lb-stat-value"><?= number_format($total_completed_tasks); ?> <span style="font-size: 13px; font-weight: normal; color: #64748b;">Konten</span></div>
               <div class="lb-stat-desc">Telah berhasil diposting</div>
            </div>
         </div>
      </div>

      <!-- Card 4: Platform Avg Score -->
      <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
         <div class="lb-stat-card">
            <div class="lb-stat-header">
               <span class="lb-stat-title">Rata-rata Skor Platform</span>
               <div class="lb-stat-icon" style="background: #faf5ff; color: #7e22ce;"><i class="fa fa-star"></i></div>
            </div>
            <div>
               <div class="lb-stat-value"><?= number_format($avg_score, 1); ?> <span style="font-size: 13px; font-weight: normal; color: #64748b;">/ 100</span></div>
               <div class="lb-stat-desc">Rerata seluruh talent terdaftar</div>
            </div>
         </div>
      </div>

   </div>

   <!-- 2. CHARTS SECTION -->
   <div class="row">
      
      <!-- Chart 1: Top 10 KOL Lifetime Score Ranking -->
      <div class="col-md-8">
         <div class="lb-card">
            <div class="lb-card-header">
               <h4 class="lb-card-title">
                  <i class="fa fa-bar-chart" style="color: #2563eb;"></i> Top 10 Peringkat Skor &amp; Produksi VT KOL
               </h4>
               <span class="badge-soft badge-soft-slate">Lifetime Ranking</span>
            </div>
             <div style="position: relative; height: 240px;">
               <canvas id="chartTopKolRanking"></canvas>
            </div>
         </div>
      </div>

      <!-- Chart 2: Tier Distribution -->
      <div class="col-md-4">
         <div class="lb-card">
            <div class="lb-card-header">
               <h4 class="lb-card-title">
                  <i class="fa fa-pie-chart" style="color: #7e22ce;"></i> Distribusi Tier Talent
               </h4>
            </div>
            <div style="position: relative; height: 180px;">
               <canvas id="chartTierDistribution"></canvas>
            </div>
            <div style="margin-top: 14px; padding-top: 10px; border-top: 1px solid #f1f5f9; font-size: 11.5px; color: #64748b; display: flex; justify-content: space-around; text-align: center;">
               <?php foreach ($tier_distribution as $td): ?>
                  <div>
                     <strong style="color: #0f172a;"><?= number_format($td->total_kols); ?></strong>
                     <div style="font-size: 10.5px;"><?= htmlspecialchars($td->tier_name); ?></div>
                  </div>
               <?php endforeach; ?>
            </div>
         </div>
      </div>

   </div>

   <!-- 3. MAIN LEADERBOARD TABLE & COMPARE TOOL -->
   <div class="lb-card" style="padding: 0; overflow: hidden; margin-bottom: 30px;">
      
      <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
         <div>
            <h4 class="lb-card-title" style="font-size: 15px;">
               <i class="fa fa-trophy" style="color: #d97706;"></i> Leaderboard Performa Talent KOL
            </h4>
            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
               Daftar peringkat berdasarkan skor performa historis dan jumlah tugas terselesaikan.
            </div>
         </div>

         <!-- Quick Search Table -->
         <div style="display: flex; gap: 8px; align-items: center;">
            <div style="position: relative;">
               <input type="text" id="table-search" class="form-control input-sm" placeholder="Cari nama KOL / kota..." style="border-radius: 6px; padding-left: 32px; width: 230px; height: 34px; border: 1px solid #cbd5e1; font-size: 12.5px;">
               <i class="fa fa-search" style="position: absolute; left: 11px; top: 10px; color: #94a3b8; font-size: 12px; pointer-events: none;"></i>
            </div>
         </div>
      </div>

      <!-- Action Bar for Multi-KOL Comparison -->
      <div id="compare-action-bar" style="display: none; padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
         <div style="display: flex; align-items: center; gap: 10px;">
            <button id="btn-compare" class="btn-compare-main" disabled>
               <i class="fa fa-columns"></i> Bandingkan KOL yang Dipilih
            </button>
            <span id="compare-hint" style="font-size: 12px; color: #64748b;">
               Pilih minimal 2 KOL untuk membandingkan metrik secara berdampingan.
            </span>
         </div>
         <span class="badge-soft badge-soft-slate"><?= count($leaderboard); ?> KOL Terdaftar</span>
      </div>

      <!-- CSRF Token for Compare AJAX -->
      <input type="hidden" class="csrf-token-field" id="compare-csrf-field"
         name="<?= $this->security->get_csrf_token_name(); ?>"
         value="<?= $this->security->get_csrf_hash(); ?>" />

      <!-- Table -->
      <div class="table-responsive mscroll" style="margin: 0;">
         <table class="table-clean" id="table-leaderboard">
            <thead>
               <tr>
                  <th width="40" class="text-center">
                     <input type="checkbox" id="select-all-compare" title="Pilih Semua" />
                  </th>
                  <th width="65" class="text-center">Rank</th>
                  <th>KOL Talent</th>
                  <th>Tier</th>
                  <th>Kontak &amp; Lokasi</th>
                  <th class="text-center">VT Selesai</th>
                  <th class="text-center">Lifetime Score</th>
                  <th class="text-center">Status</th>
                  <th class="text-center" width="100">Aksi</th>
               </tr>
            </thead>
            <tbody>
               <?php if (empty($leaderboard)): ?>
                  <tr>
                     <td colspan="9" class="text-center text-muted" style="padding: 40px;">
                        Belum ada data performa KOL terdaftar.
                     </td>
                  </tr>
               <?php else: ?>
                  <?php $rank = 1; foreach ($leaderboard as $k): ?>
                     <tr class="kol-row">
                        <td class="text-center">
                           <input type="checkbox" class="kol-compare-cb" value="<?= $k->id ?>" />
                        </td>
                        <td class="text-center">
                           <?php if ($rank == 1): ?>
                              <span class="rank-badge rank-1" title="Rank 1">1</span>
                           <?php elseif ($rank == 2): ?>
                              <span class="rank-badge rank-2" title="Rank 2">2</span>
                           <?php elseif ($rank == 3): ?>
                              <span class="rank-badge rank-3" title="Rank 3">3</span>
                           <?php else: ?>
                              <span class="rank-badge rank-other"><?= $rank; ?></span>
                           <?php endif; ?>
                        </td>
                        <td>
                           <div style="display: flex; align-items: center; gap: 10px;">
                              <?php if (!empty($k->avatar_url)): ?>
                                 <img src="<?= htmlspecialchars($k->avatar_url); ?>" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;">
                              <?php else: ?>
                                 <div style="width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">
                                    <?= strtoupper(substr($k->name ?: 'K', 0, 1)); ?>
                                 </div>
                              <?php endif; ?>
                              <div>
                                 <div class="kol-name-text" style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($k->name); ?></div>
                                 <div style="font-size: 11px; color: #94a3b8;">ID: #<?= $k->id; ?></div>
                              </div>
                           </div>
                        </td>
                        <td>
                           <span class="badge-soft badge-soft-blue"><?= htmlspecialchars($k->tier_name ?: 'Standard'); ?></span>
                        </td>
                        <td>
                           <?php 
                             if (!empty($k->phone)): 
                                 $ph = preg_replace('/[^0-9]/', '', $k->phone);
                                 if (substr($ph, 0, 2) == '62') {
                                     $ph_display = '+62 ' . substr($ph, 2, 3) . '-' . substr($ph, 5, 4) . '-' . substr($ph, 9);
                                 } else {
                                     $ph_display = $k->phone;
                                 }
                           ?>
                               <div style="font-size: 12.5px; font-weight: 500; color: #334155;">
                                   <i class="fa fa-whatsapp" style="color: #10b981; font-size: 14px; margin-right: 2px;"></i> <?= htmlspecialchars($ph_display); ?>
                               </div>
                           <?php elseif (!empty($k->email)): ?>
                               <div style="font-size: 12px; color: #334155;"><i class="fa fa-envelope-o" style="color: #94a3b8;"></i> <?= htmlspecialchars($k->email); ?></div>
                           <?php else: ?>
                               <div style="font-size: 12px; color: #94a3b8;">-</div>
                           <?php endif; ?>

                           <?php if (!empty($k->city)): ?>
                              <div class="kol-city-text" style="font-size: 11px; color: #94a3b8; margin-top: 2px;"><i class="fa fa-map-marker" style="color: #cbd5e1; width: 10px; text-align: center;"></i> <?= htmlspecialchars($k->city); ?></div>
                           <?php else: ?>
                              <div class="kol-city-text" style="font-size: 11px; color: #cbd5e1; margin-top: 2px; font-style: italic;"><i class="fa fa-map-marker" style="width: 10px; text-align: center;"></i> Lokasi tidak diketahui</div>
                           <?php endif; ?>
                        </td>
                        <td class="text-center" style="font-variant-numeric: tabular-nums;">
                           <span class="badge-soft badge-soft-emerald" style="font-weight: 700; font-size: 12px;">
                              <i class="fa fa-check-circle"></i> <?= number_format($k->completed_tasks); ?> <small style="font-weight: normal; opacity: 0.8;">VT</small>
                           </span>
                        </td>
                        <td class="text-center" style="font-variant-numeric: tabular-nums;">
                           <strong style="font-size: 15px; color: #2563eb;">
                              <?= number_format($k->lifetime_score, 1); ?>
                           </strong>
                        </td>
                        <td class="text-center">
                           <?php if (strtoupper($k->status ?? '') === 'ACTIVE'): ?>
                              <span class="badge-soft badge-soft-emerald">Active</span>
                           <?php else: ?>
                              <span class="badge-soft badge-soft-slate"><?= htmlspecialchars($k->status ?? 'Draft'); ?></span>
                           <?php endif; ?>
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                           <a href="<?= site_url('administrator/kols/view/' . $k->id); ?>" class="btn btn-xs btn-default btn-flat" style="border-radius: 4px; border-color: #cbd5e1; color: #334155; font-weight: 500;" title="Lihat Profil">
                              <i class="fa fa-eye"></i> Detail
                           </a>
                        </td>
                     </tr>
                  <?php $rank++; endforeach; ?>
               <?php endif; ?>
            </tbody>
         </table>
      </div>

   </div>

</section>

<!-- COMPARISON MODAL -->
<div class="modal fade" id="modal-kol-compare" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 950px; width: 92%;">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 16px 20px;">
                <button type="button" class="close" data-dismiss="modal" style="font-size: 20px; color: #94a3b8; opacity: 1;">&times;</button>
                <h4 class="modal-title" style="font-size: 16px; font-weight: 700; color: #0f172a;">
                   <i class="fa fa-columns" style="color: #2563eb; margin-right: 6px;"></i> Perbandingan Performa KOL
                </h4>
            </div>
            <div class="modal-body" id="compare-modal-body" style="padding: 20px; min-height: 180px;">
                <div id="compare-loading" class="text-center text-muted" style="padding: 40px 0;">
                    <i class="fa fa-spinner fa-spin fa-2x" style="color: #2563eb;"></i>
                    <div style="margin-top: 8px; font-size: 13px;">Memuat data perbandingan...</div>
                </div>

                <!-- Cards Grid Container -->
                <div id="compare-cards-content" style="display:none;"></div>

                <!-- Visual Chart Section -->
                <div id="compare-chart-section" class="compare-chart-card" style="display:none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><i class="fa fa-bar-chart" style="color: #2563eb;"></i> Grafik Perbandingan Visual</strong>
                            <div class="text-muted" id="chart-sub-label" style="font-size: 11px;">Pilih metrik di samping untuk membandingkan grafik</div>
                        </div>
                        <div style="display: flex; gap: 4px; flex-wrap: wrap;" id="chart-metric-nav">
                            <button type="button" class="chart-nav-btn active" data-metric="views"><i class="fa fa-eye"></i> Views</button>
                            <button type="button" class="chart-nav-btn" data-metric="er"><i class="fa fa-percent"></i> ER (%)</button>
                            <button type="button" class="chart-nav-btn" data-metric="cpv"><i class="fa fa-money"></i> CPV (Rp)</button>
                            <button type="button" class="chart-nav-btn" data-metric="score"><i class="fa fa-star"></i> Score</button>
                            <button type="button" class="chart-nav-btn" data-metric="radar"><i class="fa fa-dot-circle-o"></i> Multi-Metrik (Radar)</button>
                        </div>
                    </div>

                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="kolComparisonCanvas"></canvas>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 12px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-default btn-sm btn-flat" data-dismiss="modal" style="border-radius: 6px; border-color: #cbd5e1; color: #475569;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT LOGIC & CHARTS -->
<script type="text/javascript">
$(document).ready(function() {
    // ----------------------------------------------------
    // 1. PAGE CHARTS: TOP 10 RANKING & TIER DONUT
    // ----------------------------------------------------
    <?php
       $top10 = array_slice($leaderboard, 0, 10);
       $top10_names = array_map(function($k) { return $k->name; }, $top10);
       $top10_scores = array_map(function($k) { return (float)$k->lifetime_score; }, $top10);
       $top10_vts = array_map(function($k) { return (int)$k->completed_tasks; }, $top10);

       $tier_labels = array_map(function($t) { return $t->tier_name; }, $tier_distribution);
       $tier_counts = array_map(function($t) { return (int)$t->total_kols; }, $tier_distribution);
    ?>

    // Top 10 Ranking Bar & Line Chart
    var ctxRank = document.getElementById('chartTopKolRanking');
    if (ctxRank) {
        new Chart(ctxRank, {
            type: 'bar',
            data: {
                labels: <?= json_encode($top10_names); ?>,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Lifetime Score',
                        data: <?= json_encode($top10_scores); ?>,
                        backgroundColor: '#2563eb',
                        borderRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: 'VT Selesai',
                        data: <?= json_encode($top10_vts); ?>,
                        borderColor: '#10b981',
                        backgroundColor: '#10b981',
                        borderWidth: 2,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#64748b' }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: '#f1f5f9' },
                        title: { display: true, text: 'Score (0-100)', color: '#64748b', font: {size: 10} },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'VT Selesai', color: '#64748b', font: {size: 10} },
                        ticks: { font: { size: 10 }, color: '#10b981' }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 10, padding: 10, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // Tier Distribution Donut Chart
    var ctxTier = document.getElementById('chartTierDistribution');
    if (ctxTier) {
        new Chart(ctxTier, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($tier_labels); ?>,
                datasets: [{
                    data: <?= json_encode($tier_counts); ?>,
                    backgroundColor: ['#2563eb', '#7c3aed', '#059669', '#f59e0b', '#ec4899', '#64748b'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 8, font: { size: 11 } }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // ----------------------------------------------------
    // 2. REALTIME TABLE SEARCH
    // ----------------------------------------------------
    $('#table-search').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        $('.kol-row').each(function() {
            var name = $(this).find('.kol-name-text').text().toLowerCase();
            var city = $(this).find('.kol-city-text').text().toLowerCase();
            if (name.indexOf(query) !== -1 || city.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // ----------------------------------------------------
    // 3. MULTI-KOL COMPARISON LOGIC
    // ----------------------------------------------------
    var MAX_COMPARE = 5;
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var comparisonChart = null;
    var currentChartData = null;
    var currentMetric = 'views';

    var chartPalette = [
        { bg: 'rgba(37, 99, 235, 0.8)', border: '#2563eb' },
        { bg: 'rgba(5, 150, 105, 0.8)', border: '#059669' },
        { bg: 'rgba(217, 119, 6, 0.8)', border: '#d97706' },
        { bg: 'rgba(124, 58, 237, 0.8)', border: '#7c3aed' },
        { bg: 'rgba(219, 39, 119, 0.8)', border: '#db2777' }
    ];

    function getChecked() {
        return $('.kol-compare-cb:checked').map(function() { return $(this).val(); }).get();
    }

    function updateCompareBar() {
        var ids = getChecked();
        var count = ids.length;

        if (count >= MAX_COMPARE) {
            $('.kol-compare-cb:not(:checked)').prop('disabled', true);
            $('#select-all-compare').prop('disabled', true);
        } else {
            $('.kol-compare-cb').prop('disabled', false);
            $('#select-all-compare').prop('disabled', false);
        }

        if (count > 0) {
            $('#compare-action-bar').slideDown(150).css('display', 'flex');
        } else {
            $('#compare-action-bar').slideUp(150);
        }

        if (count < 2) {
            $('#btn-compare').prop('disabled', true);
            $('#compare-hint').text('Pilih minimal 2 KOL untuk dibandingkan.');
        } else {
            $('#btn-compare').prop('disabled', false);
            $('#compare-hint').html('<strong style="color: #2563eb;">' + count + ' KOL dipilih.</strong> Klik tombol di samping untuk membandingkan.');
        }
    }

    $(document).on('change', '.kol-compare-cb', function() {
        var checked = getChecked();
        if (checked.length > MAX_COMPARE) {
            $(this).prop('checked', false);
            alert('Maksimal ' + MAX_COMPARE + ' KOL dapat dibandingkan sekaligus.');
            return;
        }
        updateCompareBar();
    });

    $('#select-all-compare').on('change', function() {
        if ($(this).is(':checked')) {
            var allCbs = $('.kol-compare-cb');
            allCbs.each(function(i) {
                $(this).prop('checked', i < MAX_COMPARE);
            });
        } else {
            $('.kol-compare-cb').prop('checked', false);
        }
        updateCompareBar();
    });

    function renderComparisonChart(metric) {
        if (!currentChartData || !currentChartData.labels) return;
        var canvas = document.getElementById('kolComparisonCanvas');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');

        if (comparisonChart) {
            comparisonChart.destroy();
            comparisonChart = null;
        }

        var labels = currentChartData.labels;
        var numKols = labels.length;

        var bgColors = [];
        var borderColors = [];
        for (var i = 0; i < numKols; i++) {
            bgColors.push(chartPalette[i % chartPalette.length].bg);
            borderColors.push(chartPalette[i % chartPalette.length].border);
        }

        if (metric === 'radar') {
            $('#chart-sub-label').text('Visualisasi multi-dimensi perbandingan normalisasi (0-100)');
            
            var maxV = Math.max.apply(null, currentChartData.views.concat([1]));
            var maxE = Math.max.apply(null, currentChartData.er.concat([1]));
            var maxS = Math.max.apply(null, currentChartData.score.concat([1]));
            var maxT = Math.max.apply(null, currentChartData.tasks.concat([1]));
            var validCpvs = currentChartData.cpv.filter(function(v){ return v > 0; });
            var minC = validCpvs.length ? Math.min.apply(null, validCpvs) : 1;

            var radarDatasets = [];
            for (var k = 0; k < numKols; k++) {
                var vNorm = Math.round((currentChartData.views[k] / maxV) * 100);
                var eNorm = Math.round((currentChartData.er[k] / maxE) * 100);
                var sNorm = Math.round((currentChartData.score[k] / maxS) * 100);
                var tNorm = Math.round((currentChartData.tasks[k] / maxT) * 100);
                var cNorm = currentChartData.cpv[k] > 0 ? Math.round(Math.min(100, (minC / currentChartData.cpv[k]) * 100)) : 0;

                radarDatasets.push({
                    label: labels[k],
                    data: [vNorm, eNorm, cNorm, sNorm, tNorm],
                    backgroundColor: chartPalette[k % chartPalette.length].bg.replace('0.8', '0.2'),
                    borderColor: chartPalette[k % chartPalette.length].border,
                    borderWidth: 2,
                    pointBackgroundColor: chartPalette[k % chartPalette.length].border
                });
            }

            comparisonChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Views Reach', 'Engage Rate (ER)', 'Cost Efficiency (CPV)', 'Lifetime Score', 'Tasks (VT)'],
                    datasets: radarDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            min: 0,
                            max: 100,
                            ticks: { display: false }
                        }
                    },
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
            return;
        }

        // Single metric bar chart
        var datasetLabel = '';
        var dataValues = [];

        if (metric === 'views') {
            datasetLabel = 'Total Views';
            dataValues = currentChartData.views;
            $('#chart-sub-label').text('Perbandingan akumulasi jumlah tayangan (Total Views)');
        } else if (metric === 'er') {
            datasetLabel = 'Average ER (%)';
            dataValues = currentChartData.er;
            $('#chart-sub-label').text('Perbandingan Engagement Rate rata-rata (%)');
        } else if (metric === 'cpv') {
            datasetLabel = 'Cost Per View (IDR)';
            dataValues = currentChartData.cpv;
            $('#chart-sub-label').text('Perbandingan efisiensi biaya per tayangan (CPV)');
        } else if (metric === 'score') {
            datasetLabel = 'Lifetime Score';
            dataValues = currentChartData.score;
            $('#chart-sub-label').text('Perbandingan skor performa historis (Score)');
        }

        comparisonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: datasetLabel,
                    data: dataValues,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 1.5,
                    borderRadius: 4
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
                                var val = context.parsed.y;
                                if (metric === 'views') return ' Total Views: ' + val.toLocaleString('id-ID');
                                if (metric === 'er') return ' Avg ER: ' + val + '%';
                                if (metric === 'cpv') return ' CPV: Rp ' + val.toLocaleString('id-ID');
                                if (metric === 'score') return ' Lifetime Score: ' + val;
                                return ' ' + val;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                if (metric === 'views') {
                                    if (value >= 1000000) return (value / 1000000) + 'M';
                                    if (value >= 1000) return (value / 1000) + 'k';
                                    return value;
                                }
                                if (metric === 'er') return value + '%';
                                if (metric === 'cpv') return 'Rp ' + value.toLocaleString('id-ID');
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    $(document).on('click', '.chart-nav-btn', function() {
        $('.chart-nav-btn').removeClass('active');
        $(this).addClass('active');
        currentMetric = $(this).data('metric');
        renderComparisonChart(currentMetric);
    });

    $('#btn-compare').on('click', function() {
        var ids = getChecked();
        if (ids.length < 2 || ids.length > MAX_COMPARE) {
            alert('Pilih 2–5 KOL untuk dibandingkan.');
            return;
        }

        $('#compare-loading').show();
        $('#compare-cards-content').hide().html('');
        $('#compare-chart-section').hide();
        $('#modal-kol-compare').modal('show');

        var postData = { kol_ids: ids };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/dashboard/compare_kol',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(res) {
                if (res.csrf_hash) {
                    csrfHash = res.csrf_hash;
                    $('#compare-csrf-field').val(csrfHash);
                }
                $('#compare-loading').hide();

                if (res.success) {
                    $('#compare-cards-content').html(res.html).show();
                    currentChartData = res.chart_data;

                    $('.chart-nav-btn').removeClass('active');
                    $('.chart-nav-btn[data-metric="views"]').addClass('active');
                    currentMetric = 'views';

                    $('#compare-chart-section').show();

                    setTimeout(function() {
                        renderComparisonChart(currentMetric);
                    }, 150);
                } else {
                    $('#compare-cards-content').html(
                        '<div class="alert alert-danger">' + (res.message || 'Gagal memuat data.') + '</div>'
                    ).show();
                }
            },
            error: function() {
                $('#compare-loading').hide();
                $('#compare-cards-content').html(
                    '<div class="alert alert-danger">Terjadi kesalahan saat memuat data perbandingan.</div>'
                ).show();
            }
        });
    });
});
</script>