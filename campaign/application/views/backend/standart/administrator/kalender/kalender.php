<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
<style>
  /* ----------------------------------------------------
     MODERN SAAS CALENDAR STYLES
     ---------------------------------------------------- */
  .cal-container {
    padding: 0;
  }
  
  /* Filter Card */
  .cal-filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }
  .cal-filter-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 6px;
    display: block;
  }
  .cal-filter-card .form-control {
    border-radius: 6px;
    border-color: #cbd5e1;
    font-size: 13px;
    box-shadow: none;
    height: 36px;
  }
  .cal-filter-card .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  /* Legend Container */
  .cal-legend-bar {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    font-size: 12px;
  }
  .cal-legend-group {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
  }
  .cal-legend-label {
    font-weight: 600;
    color: #475569;
    font-size: 11.5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-right: 4px;
  }
  .cal-legend-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
    border: 1px solid transparent;
  }
  .cal-legend-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
  }

  /* Calendar Card */
  .cal-main-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    margin-bottom: 30px;
  }

  /* FullCalendar Toolbar Overrides */
  .fc-toolbar {
    margin-bottom: 20px !important;
    align-items: center;
    display: flex;
    justify-content: space-between;
  }
  .fc-toolbar h2 {
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    letter-spacing: -0.02em;
    margin: 0 !important;
  }
  .fc-button {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #334155 !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    padding: 6px 14px !important;
    height: auto !important;
    border-radius: 6px !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04) !important;
    text-shadow: none !important;
    transition: all 0.15s ease !important;
  }
  .fc-button:hover {
    background: #f8fafc !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
  }
  .fc-state-active, .fc-state-active:hover {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.3) !important;
  }
  .fc-button-group {
    display: inline-flex;
    border-radius: 6px;
  }
  .fc-button-group .fc-button {
    margin: 0 !important;
    border-radius: 0 !important;
  }
  .fc-button-group .fc-button:first-child {
    border-top-left-radius: 6px !important;
    border-bottom-left-radius: 6px !important;
  }
  .fc-button-group .fc-button:last-child {
    border-top-right-radius: 6px !important;
    border-bottom-right-radius: 6px !important;
  }

  /* FullCalendar Grid Table Overrides */
  .fc-head-container {
    border-color: #e2e8f0 !important;
  }
  .fc-day-header {
    background: #f8fafc !important;
    color: #64748b !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    padding: 10px 0 !important;
    border-color: #e2e8f0 !important;
  }
  .fc-view-container {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
  }
  .fc-widget-content {
    border-color: #f1f5f9 !important;
  }
  .fc-day-top {
    padding: 4px 6px !important;
  }
  .fc-day-number {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    padding: 2px 6px !important;
    float: right;
  }
  /* Highlight Hari Ini (Today): Background color pada kolom, tanpa tooltip lingkaran */
  .fc-today,
  td.fc-today,
  td.fc-day.fc-today,
  td.fc-day-top.fc-today {
    background: #e0f2fe !important; /* Background soft sky blue pada sel hari ini */
  }
  .fc-today .fc-day-number {
    background: transparent !important;
    color: #0284c7 !important;
    font-weight: 800 !important;
    border-radius: 0 !important;
    width: auto !important;
    height: auto !important;
    display: inline !important;
    padding: 2px 6px !important;
  }
  th.fc-day-header.fc-today {
    background: #bae6fd !important;
    color: #0369a1 !important;
    font-weight: 800 !important;
  }
  .fc-other-month {
    background: #fafbfc !important;
  }
  .fc-other-month .fc-day-number {
    color: #cbd5e1 !important;
  }

  /* Events Base */
  .fc-event {
    cursor: pointer;
    border-radius: 6px !important;
    padding: 0 !important;
    font-weight: 500;
    border: none !important;
    background: transparent !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    margin-bottom: 4px !important;
  }
  .fc-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important;
  }

  /* Campaign Banner Event (Multi-day span) */
  .fc-event-campaign {
    border-radius: 4px !important;
    padding: 2px 8px !important;
    font-size: 11px !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
    margin-bottom: 3px !important;
    border: 1px solid #c7d2fe !important;
    background: #eef2ff !important;
    color: #4338ca !important;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
  .fc-event-campaign:hover {
    background: #e0e7ff !important;
  }

  /* Task / VT Event Card */
  .fc-task-card {
    background: #ffffff;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px;
    padding: 5px 7px;
    color: #0f172a;
    transition: all 0.15s ease;
  }
  .fc-task-card-posted {
    border-color: #bbf7d0 !important;
    background: #f0fdf4;
  }
  .fc-task-card-pending {
    border-color: #e2e8f0 !important;
    background: #ffffff;
  }
  .fc-task-card-overdue {
    border-color: #fecaca !important;
    background: #fef2f2;
  }
  .fc-task-card-today {
    border-color: #fde68a !important;
    background: #fffbeb;
  }
  .fc-task-card-urgent {
    border-color: #fed7aa !important;
    background: #fff7ed;
  }

  .deadline-pill {
    display: inline-block;
    padding: 1px 5px;
    border-radius: 4px;
    font-size: 9.5px;
    font-weight: 700;
    line-height: 1.2;
    white-space: nowrap;
  }

  /* Modal Enhancements */
  .cal-modal .modal-content {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  }
  .cal-modal .modal-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 20px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
  }
  .cal-modal .modal-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
  }
  .cal-info-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 14px;
  }
  .cal-info-label {
    font-size: 10.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    margin-bottom: 4px;
  }
  .cal-info-val {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
  }

  /* ==========================================================================
     MOBILE (< 768px)
     Tampilan kalender diganti ke daftar agenda lewat JS; CSS berikut hanya
     menata toolbar, tombol, dan panel filter supaya tidak sempit.
     ========================================================================== */
  @media (max-width: 767px) {
    /* Toolbar: Judul bulan dan tombol navigasi diatur rapi di mobile */
    .fc-toolbar {
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      gap: 10px !important;
      margin-bottom: 16px !important;
    }
    .fc-toolbar h2 {
      font-size: 18px !important;
      font-weight: 700 !important;
      color: #0f172a;
      text-align: center;
      margin: 0 !important;
    }
    .fc-toolbar .fc-left {
      display: flex !important;
      justify-content: center !important;
      gap: 6px;
      width: 100%;
    }
    .fc-toolbar .fc-right {
      display: none !important;
    }
    .fc-toolbar .fc-button {
      font-size: 12.5px !important;
      min-height: 36px;
      padding: 6px 14px !important;
      border-radius: 6px !important;
    }

    /* Container Kalender Grid Bulanan: Scroll horizontal halus */
    .cal-main-card {
      padding: 14px 10px !important;
    }
    .fc-view-container {
      overflow-x: auto !important;
      overflow-y: hidden !important;
      -webkit-overflow-scrolling: touch !important;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
      position: relative;
    }
    .fc-view-container::-webkit-scrollbar {
      height: 6px !important;
    }
    .fc-view-container::-webkit-scrollbar-track {
      background: #f1f5f9 !important;
    }
    .fc-view-container::-webkit-scrollbar-thumb {
      background: #cbd5e1 !important;
      border-radius: 3px !important;
    }

    /* Lebar minimum kalender grid bulanan diperkecil agar view lebih luas di HP */
    .fc-month-view,
    .fc-month-view table {
      min-width: 480px !important;
      margin-bottom: 0 !important;
    }

    /* Kerapihan dan tinggi sel tanggal di mobile agar view besar dan menjangkau ke bawah */
    .fc-day-header {
      padding: 7px 2px !important;
      font-size: 11.5px !important;
      font-weight: 700 !important;
    }
    .fc-day-number {
      font-size: 11.5px !important;
      padding: 3px 5px !important;
    }
    .fc-basic-view .fc-body .fc-row {
      min-height: 82px !important;
    }
    .fc-event {
      margin-bottom: 2px !important;
    }

    /* Event Task Card: Format jadi 1 baris chip yang rapi, bersih & teratur di mobile */
    .fc-task-card {
      padding: 3px 4px !important;
      border-radius: 4px !important;
      border-width: 1px !important;
      display: flex !important;
      align-items: center !important;
      gap: 3px !important;
      overflow: hidden !important;
      white-space: nowrap !important;
      text-overflow: ellipsis !important;
      box-shadow: 0 1px 2px rgba(0,0,0,0.04) !important;
      line-height: 1 !important;
    }
    .fc-task-card > div:first-child {
      display: inline-flex !important;
      align-items: center !important;
      gap: 2px !important;
      margin-bottom: 0 !important;
      flex-shrink: 0 !important;
    }
    .fc-task-card > div:first-child span:first-child {
      font-size: 8px !important;
      font-weight: 700 !important;
      padding: 1px 3px !important;
      background: rgba(0,0,0,0.06) !important;
      border-radius: 2px !important;
    }
    .deadline-pill {
      display: none !important; /* Warna border-left sudah mewakili status urgency */
    }
    .fc-task-card > div:nth-child(2) {
      font-size: 9px !important;
      font-weight: 700 !important;
      line-height: 1.1 !important;
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      color: #0f172a !important;
      flex: 1 1 auto !important;
    }
    /* Sembunyikan baris Campaign & Deliverable icons di sel mobile agar tidak sesak */
    .fc-task-card > div:nth-child(3),
    .fc-task-card > div:nth-child(4) {
      display: none !important;
    }

    /* Banner campaign multi-day: Strip aksen tipis 5px tanpa teks berantakan */
    .fc-event-campaign {
      height: 5px !important;
      min-height: 5px !important;
      max-height: 5px !important;
      padding: 0 !important;
      margin: 1px 0 3px 0 !important;
      border-radius: 2.5px !important;
      overflow: hidden !important;
      line-height: 5px !important;
      border-left: none !important;
    }
    .fc-event-campaign .fc-content,
    .fc-event-campaign .fc-title {
      display: none !important;
    }

    /* Hint swipe di bawah kalender */
    .cal-swipe-hint {
      display: flex !important;
      align-items: center;
      justify-content: center;
      gap: 6px;
      color: #64748b;
      font-size: 11.5px;
      margin-top: 10px;
    }

    /* Filter: tombol reset tetap full width di mobile */
    #btn_reset_filter {
      width: 100% !important;
      min-height: 40px;
    }
  }

  @media (min-width: 768px) {
    .cal-swipe-hint {
      display: none !important;
    }
  }
</style>

<!-- Content Header -->
<section class="content-header" style="padding: 15px 15px 0 15px;">
   <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
      Kalender Jadwal <small style="font-size: 13px; color: #64748b; font-weight: normal;">Visualisasi Timeline Campaign &amp; Jadwal Posting VT KOL</small>
   </h1>
   <ol class="breadcrumb" style="background: transparent; padding: 0; margin-top: 4px; font-size: 12px;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>" style="color: #64748b;"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#" style="color: #64748b;">Produksi</a></li>
      <li class="active" style="color: #0f172a; font-weight: 600;">Kalender Jadwal</li>
   </ol>
</section>

<!-- Main Content -->
<section class="content" style="padding: 15px;">
   <div class="row">
      <div class="col-md-12">
         
         <!-- 1. FILTER & QUICK LEGEND BAR (COLLAPSIBLE) -->
         <div class="cal-filter-card" style="padding: 0; overflow: hidden; margin-bottom: 16px;">
            <div class="cal-filter-toggle-header" id="btnToggleCalFilter" data-toggle="collapse" data-target="#calFilterCollapse" aria-expanded="false" style="cursor: pointer; padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; user-select: none; background: #ffffff;">
               <div style="display: flex; align-items: center; gap: 8px;">
                  <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: #eff6ff; color: #2563eb; font-size: 13px;">
                     <i class="fa fa-filter"></i>
                  </span>
                  <div>
                     <strong style="font-size: 13px; color: #0f172a;">Filter &amp; Legenda Kalender</strong>
                  </div>
               </div>
               <div style="display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 12px;">
                  <span class="cal-toggle-text" style="font-weight: 500;">Buka Filter</span>
                  <i class="fa fa-chevron-down cal-toggle-icon" style="transition: transform 0.2s ease;"></i>
               </div>
            </div>

            <div class="collapse" id="calFilterCollapse" style="border-top: 1px solid #f1f5f9; padding: 16px 18px;">
               <div class="row">
                  <div class="col-md-3 col-sm-6" style="margin-bottom: 8px;">
                     <span class="cal-filter-title"><i class="fa fa-bullhorn"></i> Filter Campaign</span>
                     <select id="filter_campaign" class="form-control chosen select2">
                        <option value="">-- Semua Campaign --</option>
                        <?php foreach ($campaigns as $c): ?>
                           <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="col-md-3 col-sm-6" style="margin-bottom: 8px;">
                     <span class="cal-filter-title"><i class="fa fa-user"></i> Filter KOL Talent</span>
                     <select id="filter_kol" class="form-control chosen select2">
                        <option value="">-- Semua KOL --</option>
                        <?php foreach ($kols as $k): ?>
                           <option value="<?= $k->id ?>"><?= htmlspecialchars($k->name) ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="col-md-3 col-sm-6" style="margin-bottom: 8px;">
                     <span class="cal-filter-title"><i class="fa fa-share-alt"></i> Filter Platform</span>
                     <select id="filter_platform" class="form-control chosen select2">
                        <option value="">-- Semua Platform --</option>
                        <?php foreach ($platforms as $p): ?>
                           <option value="<?= $p->id ?>"><?= htmlspecialchars($p->name) ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="col-md-2 col-sm-4" style="margin-bottom: 8px;">
                     <span class="cal-filter-title"><i class="fa fa-tasks"></i> Status Task</span>
                     <select id="filter_status" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="NOT_POSTED">Belum Upload Video Live</option>
                        <option value="POSTED_LIVE">Sudah Post Video Live</option>
                     </select>
                  </div>
                  <div class="col-md-1 col-sm-2" style="margin-bottom: 8px; margin-top: 21px;">
                     <button type="button" id="btn_reset_filter" class="btn btn-default btn-block" title="Reset Semua Filter" style="height: 36px; border-color: #cbd5e1; color: #475569;">
                        <i class="fa fa-undo"></i>
                     </button>
                  </div>
               </div>

               <!-- Legend Info Bar -->
               <div class="cal-legend-bar">
                  <div class="cal-legend-group">
                     <span class="cal-legend-label"><i class="fa fa-tag text-muted"></i> Status Task:</span>
                     <span class="cal-legend-pill" style="background: #f0fdf4; color: #166534; border-color: #bbf7d0;">
                        <span class="cal-legend-dot" style="background: #16a34a;"></span> Sudah Post Live
                     </span>
                     <span class="cal-legend-pill" style="background: #f0f9ff; color: #0369a1; border-color: #bae6fd;">
                        <span class="cal-legend-dot" style="background: #0284c7;"></span> Belum Upload Live
                     </span>
                  </div>

                  <div class="cal-legend-group">
                     <span class="cal-legend-label"><i class="fa fa-clock-o text-muted"></i> Deadline Urgency:</span>
                     <span class="cal-legend-pill" style="background: #fef2f2; color: #991b1b; border-color: #fecaca;">Lewat Deadline</span>
                     <span class="cal-legend-pill" style="background: #fffbeb; color: #92400e; border-color: #fde68a;">Hari Ini</span>
                     <span class="cal-legend-pill" style="background: #fff7ed; color: #9a3412; border-color: #fed7aa;">H-1 s/d H-3</span>
                  </div>

                  <div class="cal-legend-group">
                     <span class="cal-legend-label"><i class="fa fa-calendar text-muted"></i> Periode:</span>
                     <span class="cal-legend-pill" style="background: #f5f3ff; color: #5b21b6; border-color: #ddd6fe;">
                        <i class="fa fa-bullhorn" style="font-size: 10px;"></i> Rentang Campaign
                     </span>
                  </div>
               </div>
            </div>
         </div>

         <!-- 2. MAIN CALENDAR CARD -->
         <div class="cal-main-card">
            <div id="calendar"></div>
            <div class="cal-swipe-hint">
               <i class="fa fa-arrows-h text-primary"></i> <span>Geser kalender ke samping untuk melihat seluruh tanggal</span>
            </div>
         </div>

      </div>
   </div>
</section>

<!-- Modal Detail Jadwal (Ringkas & Bersih) -->
<div class="modal fade cal-modal" id="modalEventDetail" tabindex="-1" role="dialog" aria-labelledby="modalEventTitle">
  <div class="modal-dialog" role="document" style="width: 450px; max-width: 95%;">
    <div class="modal-content">
      
      <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #f1f5f9;">
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
          <h4 class="modal-title" id="modalEventTitle" style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">Detail Jadwal VT</h4>
          <span id="modalStatusBadge" class="cal-legend-pill" style="font-size: 10px; padding: 2px 8px; font-weight: 700;">-</span>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 20px; color: #94a3b8; opacity: 1; margin: 0; padding: 0;">&times;</button>
      </div>

      <div class="modal-body" style="padding: 16px 18px;">
        
        <!-- Info Ringkas: Campaign & Deadline -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 0.03em;"><i class="fa fa-bullhorn" style="margin-right: 4px;"></i> Campaign</span>
            <div id="modalCampaign" style="font-size: 12.5px; font-weight: 700; color: #0f172a; text-align: right; max-width: 65%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">-</div>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 8px;">
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 0.03em;"><i class="fa fa-calendar" style="margin-right: 4px;"></i> Deadline</span>
            <div id="modalPostingDate" style="font-size: 12.5px; font-weight: 700; color: #0f172a;">-</div>
          </div>
        </div>

        <!-- 3 Deliverable Status Chips Horizontal -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
          <div id="modalBriefCard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 6px; text-align: center;">
            <div style="font-size: 10px; font-weight: 600; color: #64748b; margin-bottom: 3px;"><i class="fa fa-file-text-o"></i> Brief</div>
            <div id="modalBriefStatus" style="font-size: 11.5px; font-weight: 700;">-</div>
          </div>
          <div id="modalDraftCard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 6px; text-align: center;">
            <div style="font-size: 10px; font-weight: 600; color: #64748b; margin-bottom: 3px;"><i class="fa fa-video-camera"></i> Draft</div>
            <div id="modalDraftStatus" style="font-size: 11.5px; font-weight: 700;">-</div>
          </div>
          <div id="modalLiveCard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 6px; text-align: center;">
            <div style="font-size: 10px; font-weight: 600; color: #64748b; margin-bottom: 3px;"><i class="fa fa-globe"></i> Live</div>
            <div id="modalLiveStatus" style="font-size: 11.5px; font-weight: 700;">-</div>
          </div>
        </div>

      </div>

      <div class="modal-footer" style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
        <button type="button" class="btn btn-default btn-sm btn-flat" data-dismiss="modal" style="border-radius: 6px; border-color: #cbd5e1; color: #475569;">Tutup</button>
        <a href="#" id="btnModalGoTask" class="btn btn-primary btn-sm btn-flat" style="border-radius: 6px; font-weight: 600; background: #2563eb; border-color: #2563eb; padding: 6px 16px;">
           Buka Detail Task <i class="fa fa-arrow-right" style="margin-left: 4px;"></i>
        </a>
      </div>

    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<script>
$(document).ready(function() {
    function getEventsUrl() {
        var params = [];
        var c = $('#filter_campaign').val();
        var k = $('#filter_kol').val();
        var p = $('#filter_platform').val();
        var s = $('#filter_status').val();

        if (c) params.push('campaign_id=' + encodeURIComponent(c));
        if (k) params.push('kol_id=' + encodeURIComponent(k));
        if (p) params.push('platform_id=' + encodeURIComponent(p));
        if (s) params.push('status=' + encodeURIComponent(s));

        var query = params.length > 0 ? '?' + params.join('&') : '';
        return BASE_URL + 'administrator/kalender/get_events' + query;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return $('<div>').text(str).html();
    }

    var isNarrow = function () {
        return window.innerWidth <= 767;
    };
    var defaultCalView = 'month';

    $('#calendar').fullCalendar({
        defaultView: defaultCalView,
        contentHeight: 'auto',
        header: isNarrow()
            ? { left: 'prev,next today', center: 'title', right: '' }
            : { left: 'prev,next today', center: 'title', right: 'month,agendaWeek,agendaDay' },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari'
        },
        eventLimit: true,
        views: {
            month: {
                eventLimit: 4
            }
        },
        events: getEventsUrl(),
        eventRender: function(event, element) {
            // 1. Campaign Multi-day Banner
            if (event.is_campaign_banner) {
                element.addClass('fc-event-campaign');
                if (event.camp_bg) {
                    element.css({
                        'background-color': event.camp_bg,
                        'border-color': event.camp_border,
                        'border': '1px solid ' + event.camp_border,
                        'color': event.camp_text
                    });
                }
                var campTitle = '<span style="font-size: 9.5px; font-weight: 700; text-transform: uppercase; margin-right: 4px;"><i class="fa fa-bullhorn"></i> Periode:</span>' + escapeHtml(event.campaign_name || event.title.replace('Periode: ', '').replace('Periode Campaign: ', ''));
                element.find('.fc-title').html(campTitle);
                return;
            }

            // 2. Task / VT Event Card Styling
            var cardClass = 'fc-task-card-pending';
            if (event.status_raw === 'POSTED' || event.status_raw === 'DONE' || event.status_raw === 'SUCCESS') {
                cardClass = 'fc-task-card-posted';
            } else if (event.deadline_type === 'overdue') {
                cardClass = 'fc-task-card-overdue';
            } else if (event.deadline_type === 'today') {
                cardClass = 'fc-task-card-today';
            } else if (event.deadline_type === 'urgent') {
                cardClass = 'fc-task-card-urgent';
            }

            var html = '<div class="fc-task-card ' + cardClass + '">';
            
            // Header Row: VT badge & Urgency tag
            html += '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">';
            html += '  <span style="font-size: 9.5px; font-weight: 700; background: rgba(0,0,0,0.06); color: #334155; padding: 1px 4px; border-radius: 3px;">VT ' + escapeHtml(event.task_number || '1') + '</span>';
            if (event.deadline_label) {
                html += '  <span class="deadline-pill" style="background:' + event.deadline_badge_bg + '; color:' + event.deadline_badge_color + ';">' + escapeHtml(event.deadline_label) + '</span>';
            }
            html += '</div>';

            // KOL Name
            html += '<div style="font-weight: 700; font-size: 11px; line-height: 1.25; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 1px;">';
            html += escapeHtml(event.kol_name || 'KOL');
            html += '</div>';

            // Campaign Name
            if (event.campaign_name) {
                html += '<div style="font-size: 9.5px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 3px;">';
                html += escapeHtml(event.campaign_name);
                html += '</div>';
            }
            
            // Deliverable Indicators (Brief, Draft, Live)
            html += '<div style="font-size: 9.5px; display: flex; align-items: center; gap: 5px; color: #94a3b8; border-top: 1px solid rgba(0,0,0,0.04); padding-top: 3px; margin-top: 2px;">';
            html += '<span title="' + (event.brief_file_url ? 'Brief / SOW Ready' : 'No Brief') + '">' + (event.brief_file_url ? '<i class="fa fa-file-text-o" style="color: #059669;"></i>' : '<i class="fa fa-file-text-o" style="opacity: 0.35;"></i>') + '</span>';
            html += '<span title="' + (event.draft_url ? 'Draft Konten Ready' : 'No Draft') + '">' + (event.draft_url ? '<i class="fa fa-video-camera" style="color: #059669;"></i>' : '<i class="fa fa-video-camera" style="opacity: 0.35;"></i>') + '</span>';
            html += '<span title="' + (event.live_url ? 'Live Posted' : 'Not Live') + '">' + (event.live_url ? '<i class="fa fa-globe" style="color: #059669;"></i>' : '<i class="fa fa-globe" style="opacity: 0.35;"></i>') + '</span>';
            html += '</div>';

            html += '</div>';

            element.find('.fc-content').html(html);
        },
        eventClick: function(event) {
            if (event.is_campaign_banner) {
                if (event.campaign_id) {
                    window.open(BASE_URL + 'administrator/campaigns/view/' + event.campaign_id, '_blank');
                }
                return false;
            }

            // Title
            $('#modalEventTitle').html('<i class="fa fa-calendar-check-o text-primary" style="margin-right: 6px;"></i> ' + escapeHtml(event.kol_name || 'KOL') + ' &mdash; VT #' + (event.task_number || '1'));
            
            // Status badge
            var badgeBg = '#f1f5f9';
            var badgeColor = '#334155';
            if (event.status_raw === 'POSTED' || event.status_raw === 'DONE' || event.status_raw === 'SUCCESS') {
                badgeBg = '#dcfce7';
                badgeColor = '#15803d';
            } else if (event.status_raw === 'CANCELLED' || event.status_raw === 'REJECTED') {
                badgeBg = '#fee2e2';
                badgeColor = '#b91c1c';
            } else {
                badgeBg = '#e0f2fe';
                badgeColor = '#0369a1';
            }

            $('#modalStatusBadge')
                .text(event.status_raw || 'DRAFT')
                .css({'background-color': badgeBg, 'color': badgeColor, 'border-color': 'transparent'});

            // Campaign & Deadline info (Ringkas & Bersih)
            $('#modalCampaign').text(event.campaign_name || '-');

            var deadlineHtml = '<i class="fa fa-calendar-o" style="color: #64748b; margin-right: 4px;"></i> ' + escapeHtml(event.start.format('DD MMM YYYY'));
            if (event.deadline_label) {
                deadlineHtml += ' <span style="font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; background:' + (event.deadline_badge_bg || '#f1f5f9') + '; color:' + (event.deadline_badge_color || '#333') + '; margin-left: 4px;">' + escapeHtml(event.deadline_label) + '</span>';
            }
            $('#modalPostingDate').html(deadlineHtml);

            // 3 Deliverables (Brief, Draft, Live) - Ringkas tanpa teks berlebih
            if (event.brief_file_url) {
                $('#modalBriefCard').css({'border-color': '#bbf7d0', 'background': '#f0fdf4'});
                $('#modalBriefStatus').html('<a href="' + event.brief_file_url + '" target="_blank" style="color: #16a34a; font-weight: 700; text-decoration: none;"><i class="fa fa-check-circle"></i> Ada <i class="fa fa-external-link" style="font-size: 9px;"></i></a>');
            } else {
                $('#modalBriefCard').css({'border-color': '#e2e8f0', 'background': '#f8fafc'});
                $('#modalBriefStatus').html('<span style="color: #94a3b8; font-weight: 500;">Belum</span>');
            }

            if (event.draft_url) {
                $('#modalDraftCard').css({'border-color': '#bbf7d0', 'background': '#f0fdf4'});
                $('#modalDraftStatus').html('<a href="' + event.draft_url + '" target="_blank" style="color: #16a34a; font-weight: 700; text-decoration: none;"><i class="fa fa-play-circle"></i> Ada <i class="fa fa-external-link" style="font-size: 9px;"></i></a>');
            } else {
                $('#modalDraftCard').css({'border-color': '#e2e8f0', 'background': '#f8fafc'});
                $('#modalDraftStatus').html('<span style="color: #94a3b8; font-weight: 500;">Belum</span>');
            }

            if (event.live_url) {
                $('#modalLiveCard').css({'border-color': '#bbf7d0', 'background': '#f0fdf4'});
                $('#modalLiveStatus').html('<a href="' + event.live_url + '" target="_blank" style="color: #16a34a; font-weight: 700; text-decoration: none;"><i class="fa fa-globe"></i> Link <i class="fa fa-external-link" style="font-size: 9px;"></i></a>');
            } else {
                $('#modalLiveCard').css({'border-color': '#e2e8f0', 'background': '#f8fafc'});
                $('#modalLiveStatus').html('<span style="color: #94a3b8; font-weight: 500;">Belum</span>');
            }

            // Button to Task
            $('#btnModalGoTask').attr('href', event.url || (BASE_URL + 'administrator/tasks/view/' + event.task_id));

            $('#modalEventDetail').modal('show');
            return false;
        }
    });

    // Handle Collapse Filter & Legend
    $('#calFilterCollapse').on('shown.bs.collapse', function () {
        $('.cal-toggle-text').text('Tutup Filter');
        $('.cal-toggle-icon').css('transform', 'rotate(180deg)');
        if ($.fn.chosen) {
            $('#filter_campaign, #filter_kol, #filter_platform').trigger('chosen:updated');
            $('.chosen-container').css('width', '100%');
        }
    });
    $('#calFilterCollapse').on('hidden.bs.collapse', function () {
        $('.cal-toggle-text').text('Buka Filter');
        $('.cal-toggle-icon').css('transform', 'rotate(0deg)');
    });

    function updateActiveFilterBadge() {
        var hasFilter = $('#filter_campaign').val() || $('#filter_kol').val() || $('#filter_platform').val() || $('#filter_status').val();
        if (hasFilter) {
            $('#calFilterActiveBadge').show();
        } else {
            $('#calFilterActiveBadge').hide();
        }
    }

    // Handle Filters
    $('#filter_campaign, #filter_kol, #filter_platform, #filter_status').on('change', function() {
        updateActiveFilterBadge();
        $('#calendar').fullCalendar('removeEventSources');
        $('#calendar').fullCalendar('addEventSource', getEventsUrl());
    });

    $('#btn_reset_filter').on('click', function() {
        $('#filter_campaign').val('').trigger('chosen:updated');
        $('#filter_kol').val('').trigger('chosen:updated');
        $('#filter_platform').val('').trigger('chosen:updated');
        $('#filter_status').val('');
        updateActiveFilterBadge();
        $('#calendar').fullCalendar('removeEventSources');
        $('#calendar').fullCalendar('addEventSource', getEventsUrl());
    });

    /* Ikuti perubahan lebar layar (rotasi perangkat / resize jendela):
       pindah ke tampilan daftar saat sempit, kembali ke grid bulan saat lebar.
       Dibatasi agar tidak berjalan terus-menerus. */
    var lastNarrow = isNarrow();
    var resizeTimer = null;
    $(window).on('resize', function() {
        if (resizeTimer) { clearTimeout(resizeTimer); }
        resizeTimer = setTimeout(function() {
            var narrow = isNarrow();
            if (narrow === lastNarrow) { return; }
            lastNarrow = narrow;
            var cal = $('#calendar');
            if (!cal.length || !cal.fullCalendar('getView')) { return; }
            cal.fullCalendar('option', 'header', narrow
                ? { left: 'prev,next today', center: 'title', right: '' }
                : { left: 'prev,next today', center: 'title', right: 'month,agendaWeek,agendaDay' });
            cal.fullCalendar('changeView', 'month');
        }, 250);
    });
});
</script>
