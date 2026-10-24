<style>
@media print {
    .main-sidebar, .main-header, .eval-filter-box form, .breadcrumb, .eval-filter-actions, .eval-nav-tabs, .btn-detail-task { display: none !important; }
    .table-responsive { overflow: visible !important; }
    .eval-legend { display: block !important; }
    .eval-legend summary { list-style: none; }
    .eval-legend summary::-webkit-details-marker { display: none; }
    .eval-legend:not([open]) .eval-legend-body { display: block !important; }
    .content-wrapper, .right-side, .main-footer { margin-left: 0 !important; }
    .dash-box, .stat-card { border: none !important; box-shadow: none !important; margin-bottom: 20px !important; }
}

/* General Layout & Typography */
.eval-container {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #1e293b;
    font-size: 13px;
}

/* Section Titles */
.section-title {
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    margin: 24px 0 12px 0;
    padding-bottom: 6px;
    border-bottom: 1px solid #e2e8f0;
}

/* KPI Equal-Height Flexbox Grid */
.eval-kpi-row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -8px;
    margin-right: -8px;
    align-items: stretch;
}
.eval-kpi-col {
    padding-left: 8px;
    padding-right: 8px;
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    float: none !important;
}

/* Stat Cards */
.stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px 18px;
    flex: 1 1 auto;
    width: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}
.stat-card-header {
    min-height: 24px;
    margin-bottom: 8px;
}
.stat-card-title {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    margin: 0;
    line-height: 1.3;
}
.stat-card-body {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-bottom: 10px;
}
.stat-card-value {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: 6px;
}
.stat-card-sub-info {
    min-height: 20px;
    display: flex;
    align-items: center;
}
.stat-card-footer {
    padding-top: 10px;
    margin-top: auto;
    border-top: 1px solid #f1f5f9;
}
.stat-card-sub-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    color: #64748b;
    line-height: 1.4;
}

/* Progress Bar */
.eval-progress {
    height: 6px;
    background-color: #f1f5f9;
    border-radius: 3px;
    overflow: hidden;
    margin: 0;
    width: 100%;
}
.eval-progress-bar {
    height: 100%;
    border-radius: 3px;
    background-color: #2563eb;
}

/* Soft Badges */
.badge-soft {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    line-height: 1.3;
    text-align: center;
    white-space: nowrap;
}
.badge-soft-emerald { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.badge-soft-amber   { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.badge-soft-rose    { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.badge-soft-blue    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.badge-soft-slate   { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

/* Filter Container */
.eval-filter-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}
.eval-filter-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.eval-filter-inputs {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.eval-filter-label {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    height: 32px;
}
.eval-filter-box .chosen-container-single .chosen-single {
    height: 32px !important;
    line-height: 30px !important;
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    box-shadow: none !important;
    font-size: 12px !important;
    color: #334155 !important;
    padding: 0 10px !important;
    display: flex !important;
    align-items: center !important;
}
.eval-filter-box .chosen-container-single .chosen-single span {
    margin-right: 20px !important;
    line-height: 30px !important;
}
.eval-filter-box .chosen-container-single .chosen-single div {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    right: 8px !important;
    height: 100% !important;
    top: 0 !important;
}
.eval-filter-inputs .badge-soft {
    height: 32px;
    display: inline-flex;
    align-items: center;
    padding: 0 12px;
    font-size: 12px;
    border-radius: 6px;
}
.eval-filter-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Custom Styled Buttons */
.eval-btn-primary {
    background: #2563eb;
    color: #ffffff;
    border: 1px solid #1d4ed8;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    height: 32px;
    padding: 0 14px;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.eval-btn-primary:hover {
    background: #1d4ed8;
    color: #ffffff;
}
.eval-btn-secondary {
    background: #ffffff;
    color: #334155;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    height: 32px;
    padding: 0 14px;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.eval-btn-secondary:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}

/* Dashboard Box & Table */
.dash-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}
.dash-box-header {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.dash-box-title {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}
.dash-table {
    width: 100%;
    margin: 0;
    font-size: 13px;
    border-collapse: separate;
    border-spacing: 0;
}
.dash-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 14px;
    border-bottom: 1px solid #e2e8f0;
    border-top: none;
}
.dash-table tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #334155;
}
.dash-table tbody tr:last-child td { border-bottom: none; }
.dash-table tbody tr:hover td { background: #f8fafc; }

/* Scorecard: keep natural column widths, scroll horizontally instead of squeezing */
.eval-score-table th,
.eval-score-table td {
    white-space: nowrap;
    vertical-align: middle;
}

/* Staff Avatar Circle */
.staff-avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #eff6ff;
    color: #2563eb;
    font-weight: 700;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #bfdbfe;
    flex-shrink: 0;
}
</style>

<!-- Content Header -->
<section class="content-header" style="padding: 15px 15px 10px 15px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <div>
            <h1 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0;">
                Evaluasi Kinerja Staff Operasional
                <small style="font-size: 12px; color: #64748b; font-weight: normal; margin-left: 6px;">
                    Penilaian produktivitas, kepatuhan SLA tayang, dan kecepatan eksekusi staf operasional
                </small>
            </h1>
        </div>
        <ol class="breadcrumb" style="font-size: 12px; background: transparent; padding: 0; margin: 0;">
            <li><a href="<?= site_url('administrator'); ?>" style="color: #64748b;">Home</a></li>
            <li class="active" style="color: #0f172a; font-weight: 500;">Evaluasi Kinerja Staff</li>
        </ol>
    </div>
</section>

<!-- Main content -->
<section class="content eval-container" style="padding: 10px 15px;">

    <!-- 1. FILTER BAR (SEJAJAR HORIZONTAL) -->
    <div class="eval-filter-box">
        <form method="GET" action="" id="formFilterStaffEvaluation" style="margin-bottom: 0;">
            <div class="eval-filter-row">
                
                <div class="eval-filter-inputs">
                    <label class="eval-filter-label">Filter Kampanye:</label>
                    <div style="min-width: 320px;">
                        <select name="campaign_id" class="form-control input-sm chosen-select" onchange="document.getElementById('formFilterStaffEvaluation').submit();">
                            <option value="">-- Semua Kampanye (Evaluasi Menyeluruh) --</option>
                            <?php foreach($campaigns as $camp): ?>
                                <option value="<?= $camp->id ?>" <?= ($active_campaign_id == $camp->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($camp->name); ?> (<?= $camp->status ?: 'ACTIVE' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <!-- Action Buttons: Cetak PDF & Export Excel -->
                <div class="eval-filter-actions">
                    <a href="javascript:void(0);" onclick="window.print();" class="btn btn-sm eval-btn-secondary">
                        Cetak PDF
                    </a>
                    <a href="<?= site_url('administrator/internal_evaluation/export_excel?campaign_id='.$active_campaign_id); ?>" class="btn btn-sm eval-btn-primary">
                        Export Excel
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- 1b. LEGEND (dibangun dari config yang sama dengan logika skoring;collapsed by default) -->
    <?php if (!empty($legend)): ?>
    <details class="eval-filter-box eval-legend" style="padding: 8px 18px;">
        <summary style="cursor: pointer; font-size: 12px; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <span style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Legenda</span>
            <span style="color: #94a3b8; margin: 0 8px;">|</span>
            <?php $legend_shorts = ['A', 'B', 'C', 'P']; ?>
            <?php foreach (array_slice($legend['grades'], 0, 4) as $gi => $g): ?>
                <span class="badge-soft <?= $g['badge']; ?>" style="margin-right: 4px;" title="<?= htmlspecialchars($g['label']); ?>"><?= $legend_shorts[$gi]; ?></span>
            <?php endforeach; ?>
            <?php if (!empty($team_summary->provisional_staff)): ?>
                <span style="font-size: 11px; color: #b45309; font-weight: 600;">&middot; <?= $team_summary->provisional_staff; ?> sementara</span>
            <?php endif; ?>
            <span style="font-size: 11px; color: #64748b;">&middot; klik untuk detail</span>
        </summary>
        <div class="eval-legend-body" style="display: flex; flex-wrap: wrap; gap: 6px 24px; margin-top: 8px; font-size: 11px; color: #334155;">
            <div style="min-width: 200px; flex: 1;">
                <?php foreach ($legend['grades'] as $g): ?>
                    <div style="margin-bottom: 3px;">
                        <span class="badge-soft <?= $g['badge']; ?>"><?= htmlspecialchars($g['label']); ?></span>
                        <span style="color: #64748b;"> &mdash; <?= htmlspecialchars($g['rule']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="min-width: 220px; flex: 1;">
                <?php foreach ($legend['groups'] as $g): ?>
                    <div style="margin-bottom: 4px;">
                        <strong><?= htmlspecialchars($g['title']); ?>:</strong>
                        <?= htmlspecialchars($g['desc']); ?>
                    </div>
                <?php endforeach; ?>
                <div style="color: #64748b;">
                    Skor = poin dimensi yang ada (0&ndash;100); tanpa data = 0 poin; "maks" = plafon staf; <strong>-</strong> = belum ada data.
                </div>
            </div>
        </div>
    </details>
    <?php endif; ?>

    <!-- 2. TOP KPI CARDS (RINGKASAN METRIK AGREGAT TIM OPERASIONAL) -->
    <div class="eval-kpi-row">
        
        <!-- Card 1: SLA On-Time Posting Tim -->
        <div class="col-md-3 col-sm-6 eval-kpi-col">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">SLA On-Time Posting Tim</h4>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">
                        <?= $team_summary->team_on_time_rate; ?>%
                    </div>
                    <div class="stat-card-sub-info">
                        <div class="eval-progress">
                            <div class="eval-progress-bar" style="width: <?= min(100, $team_summary->team_on_time_rate); ?>%; background-color: <?= ($team_summary->team_on_time_rate >= 80) ? '#166534' : '#d97706'; ?>;"></div>
                        </div>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <div class="stat-card-sub-row">
                        <span>Pencapaian:</span>
                        <span style="font-weight: 600; color: #0f172a;">
                            <?= $team_summary->on_time_postings; ?> / <?= $team_summary->decided_postings; ?> tepat waktu (dari <?= $team_summary->total_postings; ?> posting)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Respon Review Draft (TAT) -->
        <div class="col-md-3 col-sm-6 eval-kpi-col">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Kecepatan Review Draft (TAT)</h4>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">
                        <?= ($team_summary->team_avg_turnaround_hours > 0) ? $team_summary->team_avg_turnaround_hours . ' <span style="font-size: 13px; font-weight: normal; color: #64748b;">Jam</span>' : '-'; ?>
                    </div>
                    <div class="stat-card-sub-info">
                        <?php if ($team_summary->team_avg_turnaround_hours > 0 && $team_summary->team_avg_turnaround_hours <= 4): ?>
                            <span class="badge-soft badge-soft-emerald">Respon Sangat Cepat</span>
                        <?php elseif ($team_summary->team_avg_turnaround_hours > 4): ?>
                            <span class="badge-soft badge-soft-amber">Perlu Ditingkatkan</span>
                        <?php else: ?>
                            <span class="badge-soft badge-soft-slate">Belum Ada Review</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <div class="stat-card-sub-row">
                        <span>Standar Ideal:</span>
                        <span style="font-weight: 600; color: #0f172a;">&le; 4 Jam</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Lead Time Kirim Sampel Produk -->
        <div class="col-md-3 col-sm-6 eval-kpi-col">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Kirim Sampel Produk</h4>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">
                        <?= ($team_summary->team_avg_dispatch_days > 0) ? $team_summary->team_avg_dispatch_days . ' <span style="font-size: 13px; font-weight: normal; color: #64748b;">Hari</span>' : '-'; ?>
                    </div>
                    <div class="stat-card-sub-info">
                        <?php if ($team_summary->team_avg_dispatch_days > 0 && $team_summary->team_avg_dispatch_days <= 2): ?>
                            <span class="badge-soft badge-soft-emerald">Pengiriman Cepat</span>
                        <?php elseif ($team_summary->team_avg_dispatch_days > 2): ?>
                            <span class="badge-soft badge-soft-amber">Lead Time Sedang</span>
                        <?php else: ?>
                            <span class="badge-soft badge-soft-slate">Belum Ada Pengiriman</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <div class="stat-card-sub-row">
                        <span>Dari Deal ke Resi:</span>
                        <span style="font-weight: 600; color: #0f172a;">Rata-rata hari</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Task Selesai -->
        <div class="col-md-3 col-sm-6 eval-kpi-col">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Task Operasional Tuntas</h4>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">
                        <?= $team_summary->completed_tasks; ?> / <?= $team_summary->total_tasks; ?>
                    </div>
                    <div class="stat-card-sub-info">
                        <div class="eval-progress">
                            <div class="eval-progress-bar" style="width: <?= min(100, $team_summary->task_completion_rate); ?>%; background-color: #2563eb;"></div>
                        </div>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <div class="stat-card-sub-row">
                        <span>Penyelesaian Task:</span>
                        <span style="font-weight: 600; color: #166534;">
                            <?= $team_summary->task_completion_rate; ?>% Selesai
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. MAIN SCORECARD TABLE: EVALUASI KINERJA STAFF OPERASIONAL -->
    <div class="dash-box">
        <div class="dash-box-header">
            <div>
                <h3 class="dash-box-title">Scorecard &amp; Evaluasi Kinerja Staf Operasional</h3>
                <p style="font-size: 12px; color: #64748b; margin: 3px 0 0 0;">
                    Tabel khusus evaluasi pegawai dengan role <strong>Staff (Operational)</strong>. Klik tombol <em>Lihat Bukti Task</em> untuk meninjau rincian tugas staf.
                </p>
            </div>
            <div>
                <span class="badge-soft badge-soft-blue" style="font-size: 12px; padding: 4px 10px;">
                    <?= count($staff_kpis); ?> Staf Operasional
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="dash-table eval-score-table" style="min-width: 1180px;">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">No</th>
                        <th>Staf Operasional</th>
                        <th style="text-align: center;">KOL Di-handle</th>
                        <th style="min-width: 140px;">SLA On-Time Posting</th>
                        <th>Respon Review Draft</th>
                        <th>Putaran Revisi</th>
                        <th>Kirim Sampel</th>
                        <th>Task Tuntas</th>
                        <th>Predikat Kinerja</th>
                        <th style="width: 110px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($staff_kpis)): ?>
                        <?php 
                        $no = 1;
                        foreach ($staff_kpis as $staff): 
                            $initial = strtoupper(substr($staff->full_name, 0, 1));
                        ?>
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-weight: 600;"><?= $no++; ?></td>
                            
                            <!-- Staf Profile -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php 
                                    $staff_avatar = $staff->avatar;
                                    if (!empty($staff_avatar)) {
                                        if (strpos($staff_avatar, 'http://') === 0 || strpos($staff_avatar, 'https://') === 0) {
                                            $staff_avatar_src = $staff_avatar;
                                        } else {
                                            $staff_avatar_src = BASE_URL . 'uploads/user/' . $staff_avatar;
                                        }
                                    } else {
                                        $staff_avatar_src = BASE_URL . 'uploads/user/default.png';
                                    }
                                    ?>
                                    <img src="<?= $staff_avatar_src; ?>" 
                                         onerror="this.onerror=null; this.src='<?= BASE_URL . 'asset/admin-lte/dist/img/avatar.png'; ?>';" 
                                         alt="<?= htmlspecialchars($staff->full_name); ?>" 
                                         style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1; flex-shrink: 0;">
                                    <div>
                                        <div style="font-weight: 600; color: #0f172a; font-size: 13px;">
                                            <?= htmlspecialchars($staff->full_name); ?>
                                        </div>
                                        <div style="font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 4px; margin-top: 1px;">
                                            <span>@<?= htmlspecialchars($staff->username); ?></span>
                                            <span class="badge-soft badge-soft-slate" style="padding: 1px 5px; font-size: 10px;">Staff</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- KOL Di-handle -->
                            <td style="text-align: center;">
                                <span class="badge-soft badge-soft-blue" style="font-weight: 600; font-size: 12px; padding: 3px 8px;">
                                    <?= $staff->kol_count; ?> KOL
                                </span>
                                <?php if ($staff->total_deal_amount > 0): ?>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                                        Rp <?= number_format($staff->total_deal_amount, 0, ',', '.'); ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- SLA On-Time Posting -->
                            <td>
                                <?php if ($staff->total_postings > 0): ?>
                                    <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 3px;">
                                        <span style="font-weight: 600; color: <?= ($staff->on_time_rate >= 80) ? '#166534' : '#b45309'; ?>;">
                                            <?= $staff->on_time_rate; ?>%
                                        </span>
                                        <span style="color: #64748b;">
                                            <?= $staff->on_time_postings; ?>/<?= $staff->decided_postings; ?> Tepat
                                        </span>
                                    </div>
                                    <div class="eval-progress">
                                        <div class="eval-progress-bar" style="width: <?= min(100, $staff->on_time_rate); ?>%; background-color: <?= ($staff->on_time_rate >= 80) ? '#166534' : '#d97706'; ?>;"></div>
                                    </div>
                                    <?php if ($staff->late_postings > 0): ?>
                                        <div style="font-size: 10px; color: #dc2626; margin-top: 2px;">
                                            <?= $staff->late_postings; ?> konten terlambat
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($staff->pending_postings) && $staff->pending_postings > 0): ?>
                                        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
                                            <?= $staff->pending_postings; ?> menunggu validasi
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 12px;">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Respon Review Draft -->
                            <td>
                                <?php if ($staff->avg_turnaround_hours !== null): ?>
                                    <div style="font-weight: 600; color: #0f172a;">
                                        <?= $staff->avg_turnaround_hours; ?> Jam
                                    </div>
                                    <div style="margin-top: 2px;">
                                        <?php if ($staff->avg_turnaround_hours <= 2): ?>
                                            <span class="badge-soft badge-soft-emerald" style="font-size: 10px; padding: 1px 6px;">Sangat Cepat</span>
                                        <?php elseif ($staff->avg_turnaround_hours <= 8): ?>
                                            <span class="badge-soft badge-soft-blue" style="font-size: 10px; padding: 1px 6px;">Normal</span>
                                        <?php else: ?>
                                            <span class="badge-soft badge-soft-rose" style="font-size: 10px; padding: 1px 6px;">Lambat</span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 12px;">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Putaran Revisi -->
                            <td>
                                <?php if ($staff->avg_revision_cycles !== null): ?>
                                    <span style="font-weight: 600; color: #0f172a;">
                                        <?= $staff->avg_revision_cycles; ?>x
                                    </span>
                                    <div style="font-size: 10px; color: #64748b;">
                                        dari <?= $staff->total_drafts; ?> draft
                                    </div>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 12px;">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Kirim Sampel -->
                            <td>
                                <?php if ($staff->avg_dispatch_days !== null): ?>
                                    <div style="font-weight: 600; color: #0f172a;">
                                        <?= $staff->avg_dispatch_days; ?> Hari
                                    </div>
                                    <div style="font-size: 10px; color: #64748b;">
                                        <?= $staff->total_shipments; ?> paket sampel
                                        <?php if (!empty($staff->pending_shipments) && $staff->pending_shipments > 0): ?>
                                            (<?= $staff->pending_shipments; ?> belum kirim)
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 12px;">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Task Tuntas -->
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">
                                    <?= $staff->completed_tasks; ?> / <?= $staff->total_tasks; ?>
                                </div>
                                <div style="font-size: 11px; color: #166534;">
                                    <?= $staff->task_completion_rate; ?>% Selesai
                                </div>
                            </td>

                            <!-- Predikat Kinerja -->
                            <td>
                                <span class="badge-soft <?= $staff->grade_badge; ?>" style="font-weight: 600; padding: 4px 8px;">
                                    <?= $staff->grade_label; ?>
                                </span>
                                <?php if ($staff->score !== null): ?>
                                    <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                                        Skor <?= $staff->score; ?> dari maks <?= $staff->max_earned; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($staff->is_provisional)): ?>
                                    <div style="font-size: 10px; color: #b45309; margin-top: 2px;">
                                        *Sementara &mdash; <?= htmlspecialchars(implode('; ', $staff->provisional_reasons)); ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Aksi (Tombol Lihat Bukti Task) -->
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-xs eval-btn-secondary btn-detail-task" data-toggle="modal" data-target="#modalStaffDetail_<?= $staff->id; ?>" style="padding: 4px 8px; font-size: 11px;">
                                    Lihat Bukti Task
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 24px; color: #94a3b8;">
                                <?php if (!empty($selected_campaign)): ?>
                                    Belum ada staf operasional yang ditugaskan pada kampanye <strong><?= htmlspecialchars($selected_campaign->name); ?></strong>.
                                <?php else: ?>
                                    Belum ada staf operasional yang menangani penugasan kampanye di sistem.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. MODALS DETAIL TUGAS PER-STAF (DRILL-DOWN BUKTI KINERJA) -->
    <?php foreach ($staff_kpis as $staff): ?>
    <div class="modal fade" id="modalStaffDetail_<?= $staff->id; ?>" tabindex="-1" role="dialog" aria-labelledby="labelStaffModal_<?= $staff->id; ?>">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1000px;">
            <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1;">
                
                <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <?php 
                        $m_staff_avatar = $staff->avatar;
                        if (!empty($m_staff_avatar)) {
                            if (strpos($m_staff_avatar, 'http://') === 0 || strpos($m_staff_avatar, 'https://') === 0) {
                                $m_staff_avatar_src = $m_staff_avatar;
                            } else {
                                $m_staff_avatar_src = BASE_URL . 'uploads/user/' . $m_staff_avatar;
                            }
                        } else {
                            $m_staff_avatar_src = BASE_URL . 'uploads/user/default.png';
                        }
                        ?>
                        <img src="<?= $m_staff_avatar_src; ?>" 
                             onerror="this.onerror=null; this.src='<?= BASE_URL . 'asset/admin-lte/dist/img/avatar.png'; ?>';" 
                             alt="<?= htmlspecialchars($staff->full_name); ?>" 
                             style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1; flex-shrink: 0;">
                        <div>
                            <h4 class="modal-title" id="labelStaffModal_<?= $staff->id; ?>" style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">
                                Rapor Bukti Penugasan: <?= htmlspecialchars($staff->full_name); ?>
                            </h4>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                Username: <strong>@<?= htmlspecialchars($staff->username); ?></strong> &bull; Predikat: <strong><?= $staff->grade_label; ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body" style="padding: 18px 20px;">
                    
                    <!-- Quick KPI Badges inside Modal -->
                    <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; background: #f8fafc; padding: 12px 14px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 12px;">
                            <span style="color: #64748b;">KOL Di-handle:</span> <strong><?= $staff->kol_count; ?> Kreator</strong>
                        </div>
                        <div style="font-size: 12px;">
                            <span style="color: #64748b;">SLA On-Time Posting:</span> <strong><?= ($staff->on_time_rate !== null) ? $staff->on_time_rate . '%' : '-'; ?></strong>
                        </div>
                        <div style="font-size: 12px;">
                            <span style="color: #64748b;">Waktu Respon Review:</span> <strong><?= ($staff->avg_turnaround_hours !== null) ? $staff->avg_turnaround_hours . ' Jam' : '-'; ?></strong>
                        </div>
                        <div style="font-size: 12px;">
                            <span style="color: #64748b;">Kirim Sampel:</span> <strong><?= ($staff->avg_dispatch_days !== null) ? $staff->avg_dispatch_days . ' Hari' : '-'; ?></strong>
                        </div>
                    </div>

                    <!-- Tasks Table -->
                    <h5 style="font-size: 13px; font-weight: 700; color: #0f172a; margin: 0 0 10px 0;">
                        Daftar Task &amp; Konten yang Dikawal:
                    </h5>

                    <?php if (!empty($staff->tasks)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" style="font-size: 12px; margin-bottom: 0;">
                                <thead style="background: #f1f5f9;">
                                    <tr>
                                        <th style="width: 35px; text-align: center;">No</th>
                                        <th>Judul Task</th>
                                        <th>Kampanye</th>
                                        <th>Nama KOL</th>
                                        <th>Deadline Posting</th>
                                        <th>Status Tayang</th>
                                        <th>Status Task</th>
                                        <th>Link Konten</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $t_no = 1;
                                    foreach ($staff->tasks as $t): 
                                    ?>
                                    <tr>
                                        <td style="text-align: center; color: #94a3b8;"><?= $t_no++; ?></td>
                                        <td><strong><?= htmlspecialchars($t->task_title ?: 'Task #' . $t->task_id); ?></strong></td>
                                        <td><?= htmlspecialchars($t->campaign_name); ?></td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <?php if (!empty($t->kol_avatar)): ?>
                                                    <img src="<?= $t->kol_avatar; ?>" onerror="this.style.display='none';" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1; flex-shrink: 0;">
                                                <?php endif; ?>
                                                <span><?= htmlspecialchars($t->kol_name); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?= !empty($t->estimated_posting_date) ? date('d M Y', strtotime($t->estimated_posting_date)) : '-'; ?>
                                        </td>
                                        <td>
                                            <?php if ($t->date_validation_status == 'ON_TIME'): ?>
                                                <span class="badge-soft badge-soft-emerald">On-Time</span>
                                            <?php elseif ($t->date_validation_status == 'LATE'): ?>
                                                <span class="badge-soft badge-soft-rose">Terlambat</span>
                                            <?php else: ?>
                                                <span class="badge-soft badge-soft-slate">Menunggu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge-soft badge-soft-blue"><?= htmlspecialchars($t->task_status); ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($t->live_url)): ?>
                                                <a href="<?= htmlspecialchars($t->live_url); ?>" target="_blank" class="btn btn-xs eval-btn-secondary" style="padding: 2px 6px;">
                                                    Buka URL
                                                </a>
                                            <?php else: ?>
                                                <span style="color: #94a3b8;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 20px; text-align: center; color: #94a3b8;">
                            Staf ini belum memiliki riwayat task pada filter kampanye yang dipilih.
                        </div>
                    <?php endif; ?>

                </div>

                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 10px 20px;">
                    <button type="button" class="btn btn-sm eval-btn-secondary" data-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>
    <?php endforeach; ?>

</section>
