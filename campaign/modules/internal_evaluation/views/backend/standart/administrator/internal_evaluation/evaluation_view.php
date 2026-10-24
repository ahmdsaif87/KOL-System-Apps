<!-- Content Header (Page header) -->
<?php
$badge_map = [
    'badge-soft-emerald' => 'badge-soft-emerald label-success',
    'badge-soft-amber'   => 'badge-soft-amber label-warning',
    'badge-soft-rose'    => 'badge-soft-rose label-danger',
    'badge-soft-blue'    => 'badge-soft-blue label-primary',
    'badge-soft-slate'   => 'badge-soft-slate label-default'
];
?>
<section class="content-header">
    <h1>
        Evaluasi Kinerja Staff Operasional
        <small>Penilaian produktivitas, kepatuhan SLA tayang, dan kecepatan eksekusi staf operasional</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Evaluasi Kinerja Staff</li>
    </ol>
</section>

<!-- Main content -->
<section class="content eval-page">

    <!-- Navigasi Evaluasi -->
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li class="active"><a href="<?= site_url('administrator/internal_evaluation/staff'); ?>" style="font-weight: 600;"><i class="fa fa-users"></i> Staff Operasional</a></li>
        <li><a href="<?= site_url('administrator/internal_evaluation/leader'); ?>"><i class="fa fa-user-circle"></i> Leader</a></li>
    </ul>

    <!-- Filter and Actions Box -->
    <div class="box box-primary">
        <div class="box-body">
            <form method="GET" action="" id="formFilterStaffEvaluation" class="form-inline" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div class="form-group" style="flex: 1 1 auto; min-width: 0; max-width: 100%;">
                    <label for="campaign_id" style="margin-right: 10px;">Filter Kampanye:</label>
                    <select name="campaign_id" id="campaign_id" class="form-control chosen-select eval-campaign-select" onchange="document.getElementById('formFilterStaffEvaluation').submit();" style="min-width: 0; width: 100%; max-width: 320px;">
                        <option value="">-- Semua Kampanye (Evaluasi Menyeluruh) --</option>
                        <?php foreach($campaigns as $camp): ?>
                            <option value="<?= $camp->id ?>" <?= ($active_campaign_id == $camp->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($camp->name); ?> (<?= $camp->status ?: 'ACTIVE' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 0 0 auto; white-space: nowrap;">
                    <a href="javascript:void(0);" onclick="preparePrint();" class="btn btn-default btn-sm">
                        <i class="fa fa-print"></i> Cetak PDF
                    </a>
                    <a href="<?= site_url('administrator/internal_evaluation/export_excel?campaign_id='.$active_campaign_id); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-file-excel-o"></i> Export Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Legend (Optional, collapsed by default) -->
    <?php if (!empty($legend)): ?>
    <div class="print-hidden" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; background: #f8fafc; cursor: pointer; user-select: none;" data-toggle="collapse" data-target="#legendCollapse" aria-expanded="false">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-info-circle text-primary" style="font-size: 14px;"></i>
                <span style="font-size: 13px; font-weight: 600; color: #334155;">Panduan & Legenda Penilaian</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <button type="button" class="btn btn-default btn-xs" style="border-radius: 4px; padding: 3px 10px; font-size: 11px; font-weight: 600; color: #64748b; background: #ffffff;">
                    <span id="legendToggleText">Buka Panduan</span>
                    <i class="fa fa-chevron-down" id="legendChevron" style="margin-left: 4px;"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="legendCollapse">
            <div style="padding: 14px 16px 8px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
            <div class="row">
                <div class="col-md-5">
                    <h5 style="font-weight: bold; margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 8px;">Tingkatan Predikat</h5>
                    <div class="eval-legend-list">
                        <?php foreach ($legend['grades'] as $g): ?>
                        <div class="eval-legend-item">
                            <span class="label <?= isset($badge_map[$g['badge']]) ? $badge_map[$g['badge']] : 'label-default'; ?> eval-legend-badge"><?= htmlspecialchars($g['label']); ?></span>
                            <span class="eval-legend-text"><?= htmlspecialchars($g['rule']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-7">
                    <h5 style="font-weight: bold; margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 8px;">Dimensi Penilaian</h5>
                    <div class="eval-legend-list">
                        <?php foreach ($legend['groups'] as $g): ?>
                        <div class="eval-legend-item">
                            <strong class="eval-legend-title"><?= htmlspecialchars($g['title']); ?></strong>
                            <span class="eval-legend-text"><?= htmlspecialchars($g['desc']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd; font-size: 12px; color: #777;">
                        <i class="fa fa-lightbulb-o"></i> <strong>Catatan:</strong> Skor dihitung dari bagian yang ada datanya (0-100). Tanda <strong>-</strong> artinya belum ada data.
                        <?php if (!empty($team_summary->provisional_staff)): ?>
                            <br><span class="text-warning"><i class="fa fa-warning"></i> <?= $team_summary->provisional_staff; ?> bersifat sementara karena data historis belum lengkap.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 2. TOP KPI CARDS (RINGKASAN METRIK AGREGAT TIM OPERASIONAL) -->
    <div class="eval-kpi-row" style="margin-bottom: 20px;">
        
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
                        <?= ($team_summary->team_avg_turnaround_hours > 0) ? $team_summary->team_avg_turnaround_hours . ' <span style="font-size: 12px; font-weight: normal; color: #64748b;">Jam</span>' : '-'; ?>
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
                        <?= ($team_summary->team_avg_dispatch_days > 0) ? $team_summary->team_avg_dispatch_days . ' <span style="font-size: 12px; font-weight: normal; color: #64748b;">Hari</span>' : '-'; ?>
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

    <!-- Main Data Table -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Scorecard & Evaluasi Kinerja Staf Operasional</h3>
            <div class="box-tools pull-right">
                <span class="label label-primary"><?= count($staff_kpis); ?> Staf Operasional</span>
            </div>
        </div>
        <div class="box-body table-responsive mscroll eval-table-wrap">
            <table class="table table-bordered table-striped table-hover dataTable print-table eval-main-table">
                <thead>
                    <tr>
                        <th style="width: 36px; text-align: center;">No</th>
                        <th style="min-width: 170px;">Staf Operasional</th>
                        <th style="text-align: center; min-width: 80px;">KOL Di-handle</th>
                        <th style="min-width: 120px;">SLA On-Time Posting</th>
                        <th style="min-width: 100px;">Respon Review Draft</th>
                        <th style="min-width: 80px;">Putaran Revisi</th>
                        <th style="min-width: 100px;">Kirim Sampel</th>
                        <th style="min-width: 80px;">Task Tuntas</th>
                        <th style="min-width: 95px; text-align: right;">CPV</th>
                        <th style="min-width: 95px; text-align: right;">CPE</th>
                        <th style="min-width: 130px;">Predikat Kinerja</th>
                        <th class="print-hidden" style="width: 100px; min-width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($staff_kpis)): ?>
                        <?php 
                        $no = 1;
                        foreach ($staff_kpis as $staff): 
                        ?>
                        <tr>
                            <td style="text-align: center; vertical-align: middle;"><?= $no++; ?></td>
                            
                            <!-- Staf Profile -->
                            <td style="vertical-align: middle;">
                                <div class="media" style="margin-top: 0;">
                                    <div class="media-left">
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
                                        <img src="<?= $staff_avatar_src; ?>" onerror="this.onerror=null; this.src='<?= BASE_URL . 'asset/admin-lte/dist/img/avatar.png'; ?>';" class="media-object img-circle" style="width: 40px; height: 40px; border: 1px solid #ddd;" alt="<?= htmlspecialchars($staff->full_name); ?>">
                                    </div>
                                    <div class="media-body" style="vertical-align: middle;">
                                        <h4 class="media-heading" style="font-size: 13px; font-weight: bold; margin-bottom: 2px;">
                                            <?= htmlspecialchars($staff->full_name); ?>
                                        </h4>
                                        <span class="text-muted" style="font-size: 12px;">@<?= htmlspecialchars($staff->username); ?></span>
                                        <span class="label label-default" style="font-size: 10px; margin-left: 5px;">Staff</span>
                                    </div>
                                </div>
                            </td>

                            <!-- KOL Di-handle -->
                            <td style="text-align: center; vertical-align: middle;">
                                <span class="badge bg-blue" style="font-size: 12px;">
                                    <?= $staff->kol_count; ?> KOL
                                </span>
                                <?php if ($staff->total_deal_amount > 0): ?>
                                    <div style="font-size: 11px; margin-top: 5px;" class="text-muted">
                                        Rp <?= number_format($staff->total_deal_amount, 0, ',', '.'); ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- SLA On-Time Posting -->
                            <td style="vertical-align: middle;">
                                <?php if ($staff->total_postings > 0): ?>
                                    <div class="clearfix">
                                        <span class="pull-left" style="font-weight: bold; color: <?= ($staff->on_time_rate >= 80) ? '#00a65a' : '#f39c12'; ?>;">
                                            <?= $staff->on_time_rate; ?>%
                                        </span>
                                        <small class="pull-right text-muted">
                                            <?= $staff->on_time_postings; ?>/<?= $staff->decided_postings; ?>
                                        </small>
                                    </div>
                                    <div class="progress xs" style="margin-bottom: 5px;">
                                        <div class="progress-bar <?= ($staff->on_time_rate >= 80) ? 'progress-bar-success' : 'progress-bar-warning'; ?>" style="width: <?= min(100, $staff->on_time_rate); ?>%;"></div>
                                    </div>
                                    <?php if ($staff->late_postings > 0): ?>
                                        <div style="font-size: 11px;" class="text-danger">
                                            <i class="fa fa-warning"></i> <?= $staff->late_postings; ?> konten terlambat
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($staff->pending_postings) && $staff->pending_postings > 0): ?>
                                        <div style="font-size: 11px;" class="text-muted">
                                            <i class="fa fa-clock-o"></i> <?= $staff->pending_postings; ?> menunggu validasi
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Respon Review Draft -->
                            <td style="vertical-align: middle;">
                                <?php if ($staff->avg_turnaround_hours !== null): ?>
                                    <div style="font-weight: bold; font-size: 13px;">
                                        <?= $staff->avg_turnaround_hours; ?> Jam
                                    </div>
                                    <div style="margin-top: 2px;">
                                        <?php if ($staff->avg_turnaround_hours <= 2): ?>
                                            <span class="label label-success">Sangat Cepat</span>
                                        <?php elseif ($staff->avg_turnaround_hours <= 8): ?>
                                            <span class="label label-info">Normal</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Lambat</span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Putaran Revisi -->
                            <td style="vertical-align: middle;">
                                <?php if ($staff->avg_revision_cycles !== null): ?>
                                    <span style="font-weight: bold; font-size: 13px;">
                                        <?= $staff->avg_revision_cycles; ?>x
                                    </span>
                                    <div style="font-size: 11px;" class="text-muted">
                                        dari <?= $staff->total_drafts; ?> draft
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Kirim Sampel -->
                            <td style="vertical-align: middle;">
                                <?php if ($staff->avg_dispatch_days !== null): ?>
                                    <div style="font-weight: bold; font-size: 13px;">
                                        <?= $staff->avg_dispatch_days; ?> Hari
                                    </div>
                                    <div style="font-size: 11px;" class="text-muted">
                                        <?= $staff->total_shipments; ?> paket sampel
                                        <?php if (!empty($staff->pending_shipments) && $staff->pending_shipments > 0): ?>
                                            <br><span class="text-warning">(<?= $staff->pending_shipments; ?> belum kirim)</span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Task Tuntas -->
                            <td style="vertical-align: middle;">
                                <div style="font-weight: bold; font-size: 13px;">
                                    <?= $staff->completed_tasks; ?> / <?= $staff->total_tasks; ?>
                                </div>
                                <div style="font-size: 12px;" class="text-success">
                                    <strong><?= $staff->task_completion_rate; ?>% Selesai</strong>
                                </div>
                            </td>

                            <!-- CPV -->
                            <td style="vertical-align: middle; text-align: right; white-space: nowrap;">
                                <?php if ($staff->actual_cpv > 0): ?>
                                    <div style="font-weight: bold; font-size: 13px; color: #0f172a;">
                                        Rp <?= number_format($staff->actual_cpv, 0, ',', '.'); ?>
                                    </div>
                                    <div style="font-size: 11px; color: #64748b;">
                                        <?= number_format($staff->actual_views, 0, ',', '.'); ?> views
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- CPE -->
                            <td style="vertical-align: middle; text-align: right; white-space: nowrap;">
                                <?php if ($staff->actual_cpe > 0): ?>
                                    <div style="font-weight: bold; font-size: 13px; color: #0284c7;">
                                        Rp <?= number_format($staff->actual_cpe, 0, ',', '.'); ?>
                                    </div>
                                    <div style="font-size: 11px; color: #64748b;">
                                        <?= number_format($staff->actual_engagements, 0, ',', '.'); ?> eng.
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Predikat Kinerja -->
                            <td style="vertical-align: middle; text-align: center;">
                                <?php
                                $popover_content = "<div style='font-size: 12px; line-height: 1.6; min-width: 180px;'>";
                                if ($staff->score !== null) {
                                    $popover_content .= "<div style='margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #eee;'><i class='fa fa-star text-yellow'></i> <strong>Skor Penilaian:</strong><br><span style='color: #333;'>{$staff->score} dari {$staff->max_earned} Poin</span></div>";
                                }
                                if (!empty($staff->is_provisional)) {
                                    $reasons = isset($staff->provisional_reasons) ? htmlspecialchars(implode(', ', $staff->provisional_reasons)) : 'Data historis belum lengkap';
                                    $popover_content .= "<div style='color: #d35400;'><i class='fa fa-warning'></i> <strong>Status Sementara:</strong><br><span style='color: #555;'>{$reasons}</span></div>";
                                }
                                if ($staff->score === null && empty($staff->is_provisional)) {
                                    $popover_content .= "<div style='color: #777;'><i class='fa fa-info-circle'></i> Tidak ada catatan khusus.</div>";
                                }
                                $popover_content .= "</div>";
                                ?>
                                <span tabindex="0" class="label <?= isset($badge_map[$staff->grade_badge]) ? $badge_map[$staff->grade_badge] : 'label-default'; ?>" 
                                   role="button" 
                                   data-toggle="popover" 
                                   data-trigger="focus" 
                                   title="Rincian Predikat Kinerja" 
                                   data-html="true" 
                                   data-placement="left"
                                   data-content="<?= htmlspecialchars($popover_content, ENT_QUOTES, 'UTF-8'); ?>" 
                                   style="font-size: 12px; cursor: pointer; display: inline-block; padding: 5px 10px; white-space: normal; max-width: 160px; line-height: 1.4;">
                                    <?= $staff->grade_label; ?> <i class="fa fa-question-circle" style="margin-left: 3px;"></i>
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="print-hidden" style="text-align: center; vertical-align: middle;">
                                <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modalStaffDetail_<?= $staff->id; ?>">
                                    <i class="fa fa-search"></i> Bukti Task
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted" style="padding: 30px;">
                                <i class="fa fa-info-circle fa-2x" style="margin-bottom: 10px;"></i><br>
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

    <!-- Modals for Task Details -->
    <?php foreach ($staff_kpis as $staff): ?>
    <div class="modal fade" id="modalStaffDetail_<?= $staff->id; ?>" tabindex="-1" role="dialog" aria-labelledby="labelStaffModal_<?= $staff->id; ?>">
        <div class="modal-dialog modal-lg" role="document" style="width: 96%; max-width: 1600px;">
            <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                
                <div class="modal-header" style="background-color: #f4f6f9; border-bottom: 1px solid #d2d6de; padding: 15px 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 5px; font-size: 24px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="media" style="margin: 0;">
                        <div class="media-left">
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
                            <img class="media-object img-circle" src="<?= $m_staff_avatar_src; ?>" onerror="this.onerror=null; this.src='<?= BASE_URL . 'asset/admin-lte/dist/img/avatar.png'; ?>';" style="width: 48px; height: 48px; border: 2px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                        </div>
                        <div class="media-body" style="vertical-align: middle; padding-top: 2px;">
                            <h4 class="modal-title" id="labelStaffModal_<?= $staff->id; ?>" style="font-weight: bold; margin-bottom: 3px; font-size: 15px; color: #333;">
                                Rapor Bukti Penugasan: <?= htmlspecialchars($staff->full_name); ?>
                            </h4>
                            <div style="font-size: 13px; color: #666;">
                                <i class="fa fa-at"></i> <?= htmlspecialchars($staff->username); ?> &nbsp;|&nbsp; 
                                Predikat Kinerja: <span class="label <?= isset($badge_map[$staff->grade_badge]) ? $badge_map[$staff->grade_badge] : 'label-default'; ?>"><?= $staff->grade_label; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body" style="padding: 20px;">
                    
                    <!-- Quick Summary using Description Blocks -->
                    <div class="row" style="margin-bottom: 20px; background: #fafafa; border: 1px solid #eee; border-radius: 4px; padding: 10px 0;">
                        <div class="col-sm-3 col-xs-6" style="border-right: 1px solid #e0e0e0;">
                            <div class="description-block" style="text-align: center; margin: 5px 0;">
                                <h5 class="description-header" style="font-size: 18px; color: #3c8dbc; margin-bottom: 5px;"><?= $staff->kol_count; ?></h5>
                                <span class="description-text" style="font-size: 11px; color: #777; text-transform: uppercase; font-weight: bold;">KOL Di-handle</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-6" style="border-right: 1px solid #e0e0e0;">
                            <div class="description-block" style="text-align: center; margin: 5px 0;">
                                <h5 class="description-header" style="font-size: 18px; color: #00a65a; margin-bottom: 5px;"><?= ($staff->on_time_rate !== null) ? $staff->on_time_rate . '%' : '-'; ?></h5>
                                <span class="description-text" style="font-size: 11px; color: #777; text-transform: uppercase; font-weight: bold;">SLA On-Time</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-6" style="border-right: 1px solid #e0e0e0;">
                            <div class="description-block" style="text-align: center; margin: 5px 0;">
                                <h5 class="description-header" style="font-size: 18px; color: #f39c12; margin-bottom: 5px;"><?= ($staff->avg_turnaround_hours !== null) ? $staff->avg_turnaround_hours . ' Jam' : '-'; ?></h5>
                                <span class="description-text" style="font-size: 11px; color: #777; text-transform: uppercase; font-weight: bold;">Respon Review</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block" style="text-align: center; margin: 5px 0;">
                                <h5 class="description-header" style="font-size: 18px; color: #00c0ef; margin-bottom: 5px;"><?= ($staff->avg_dispatch_days !== null) ? $staff->avg_dispatch_days . ' Hari' : '-'; ?></h5>
                                <span class="description-text" style="font-size: 11px; color: #777; text-transform: uppercase; font-weight: bold;">Kirim Sampel</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Table -->
                    <h5 style="margin-top: 0; font-weight: bold; border-bottom: 2px solid #3c8dbc; padding-bottom: 8px; margin-bottom: 15px; color: #333; font-size: 14px;">
                        <i class="fa fa-list-alt" style="margin-right: 5px;"></i> Daftar Task &amp; Konten yang Dikawal
                    </h5>

                    <?php if (!empty($staff->tasks)): ?>
                        <div class="table-responsive mscroll">
                            <table class="table table-bordered table-striped table-hover table-condensed" style="font-size: 13px;">
                                <thead>
                                    <tr class="bg-gray-light">
                                        <th style="width: 35px; text-align: center; vertical-align: middle;">No</th>
                                        <th style="vertical-align: middle;">Judul Task</th>
                                        <th style="vertical-align: middle;">Kampanye</th>
                                        <th style="vertical-align: middle;">Nama KOL</th>
                                        <th style="vertical-align: middle;">Deadline</th>
                                        <th style="vertical-align: middle;">SLA Tayang</th>
                                        <th style="vertical-align: middle;">Status Task</th>
                                        <th style="text-align: right; vertical-align: middle;">Views</th>
                                        <th style="text-align: right; vertical-align: middle;">Eng.</th>
                                        <th style="text-align: right; vertical-align: middle;">CPV</th>
                                        <th style="text-align: right; vertical-align: middle;">CPE</th>
                                        <th style="text-align: center; vertical-align: middle;">URL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $t_no = 1;
                                    foreach ($staff->tasks as $t): 
                                    ?>
                                    <tr>
                                        <td style="text-align: center; vertical-align: middle; color: #777;"><?= $t_no++; ?></td>
                                        <td style="vertical-align: middle;"><strong><?= htmlspecialchars($t->task_title ?: 'Task #' . $t->task_id); ?></strong></td>
                                        <td style="vertical-align: middle; color: #555;"><?= htmlspecialchars($t->campaign_name); ?></td>
                                        <td style="vertical-align: middle;">
                                            <div style="display: flex; align-items: center;">
                                                <?php if (!empty($t->kol_avatar)): ?>
                                                    <img src="<?= $t->kol_avatar; ?>" onerror="this.style.display='none';" class="img-circle" style="width: 20px; height: 20px; margin-right: 6px; border: 1px solid #ddd; object-fit: cover;">
                                                <?php endif; ?>
                                                <?= htmlspecialchars($t->kol_name); ?>
                                            </div>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?= !empty($t->estimated_posting_date) ? date('d M Y', strtotime($t->estimated_posting_date)) : '<span class="text-muted">-</span>'; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?php if ($t->date_validation_status == 'ON_TIME'): ?>
                                                <span class="label label-success">On-Time</span>
                                            <?php elseif ($t->date_validation_status == 'LATE'): ?>
                                                <span class="label label-danger">Terlambat</span>
                                            <?php else: ?>
                                                <span class="label label-default">Menunggu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <span class="label label-primary"><?= htmlspecialchars($t->task_status); ?></span>
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <?= number_format($t->views ?? 0, 0, ',', '.'); ?>
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <?= number_format($t->engagements ?? 0, 0, ',', '.'); ?>
                                        </td>
                                        <td style="text-align: right; vertical-align: middle; font-weight: 600;">
                                            <?= ($t->cpv ?? 0) > 0 ? 'Rp ' . number_format($t->cpv, 0, ',', '.') : '-'; ?>
                                        </td>
                                        <td style="text-align: right; vertical-align: middle; font-weight: 600; color: #0284c7;">
                                            <?= ($t->cpe ?? 0) > 0 ? 'Rp ' . number_format($t->cpe, 0, ',', '.') : '-'; ?>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <?php if (!empty($t->live_url)): ?>
                                                <a href="<?= htmlspecialchars($t->live_url); ?>" target="_blank" class="btn btn-xs btn-default" title="Buka URL">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="callout callout-warning" style="background-color: #fcf8e3 !important; border-color: #faebcc;">
                            <p class="text-warning text-center" style="margin: 10px 0;"><i class="fa fa-info-circle"></i> Staf ini belum memiliki riwayat task pada filter kampanye yang dipilih.</p>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="modal-footer" style="background-color: #f9fafc; border-top: 1px solid #eee;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>
    <?php endforeach; ?>

</section>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof jQuery !== 'undefined') {
        $('[data-toggle="popover"]').popover();
        // === Legend Collapse Toggle Events (gaya filter-toolbar KOL) ===
        $('#legendCollapse').on('show.bs.collapse', function () {
            $('#legendChevron').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $('#legendToggleText').text('Tutup Panduan');
        });
        $('#legendCollapse').on('hide.bs.collapse', function () {
            $('#legendChevron').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $('#legendToggleText').text('Buka Panduan');
        });
    }
});
</script>
<style>
/* Samakan dengan halaman standar (AdminLTE body 14px, subtitle list 13px) */
.eval-page {
    font-size: 13px;
    overflow-x: hidden;
    max-width: 100%;
}
.eval-page .box {
    max-width: 100%;
}
.eval-page .table {
    font-size: 13px;
}
.eval-page .box-title {
    font-size: 14px;
}
/* Legenda pakai div flex (bukan table) agar kebal dari aturan global modern-ui.css
   (.table td nowrap, .label nowrap) yang memaksa teks satu baris / terpotong */
.eval-legend-list {
    max-width: 100%;
}
.eval-legend-item {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    padding: 5px 0;
    max-width: 100%;
}
.eval-page .eval-legend-badge.label {
    flex: 0 0 130px;
    max-width: 130px;
    text-align: center;
    white-space: normal !important;
    line-height: 1.4;
}
.eval-legend-title {
    flex: 0 0 150px;
    max-width: 150px;
    color: #444;
    font-size: 13px;
    line-height: 1.5;
}
.eval-legend-text {
    flex: 1 1 auto;
    min-width: 0;
    color: #555;
    line-height: 1.6;
}
@media (max-width: 767px) {
    .eval-legend-item {
        flex-direction: column;
        gap: 4px;
    }
    .eval-page .eval-legend-badge.label,
    .eval-legend-title {
        flex: none;
        max-width: 100%;
    }
}
/* KPI Equal-Height Flexbox Grid */
.eval-kpi-row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -8px;
    margin-right: -8px;
    align-items: stretch;
    max-width: calc(100% + 16px);
    box-sizing: border-box;
}
.eval-kpi-col {
    padding-left: 8px;
    padding-right: 8px;
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    float: none !important;
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
}
/* Tabel utama: override aturan global modern-ui.css yang memaksa
   table.dataTable width:max-content, td nowrap + font 14px, dan
   td:last-child min-width:180px (semua !important).
   Specificity di bawah sengaja dibuat lebih tinggi agar menang. */
.eval-page table.eval-main-table.dataTable {
    width: 100% !important;
    min-width: 0 !important;
}
.eval-page .eval-main-table > thead > tr > th {
    padding: 8px 10px !important;
    font-size: 12px !important;
    white-space: normal !important;
    vertical-align: middle;
}
.eval-page .eval-main-table > tbody > tr > td {
    padding: 8px 10px !important;
    font-size: 13px !important;
    white-space: normal !important;
    overflow-wrap: break-word;
    vertical-align: middle;
}
.eval-page .eval-main-table > thead > tr > th:last-child,
.eval-page .eval-main-table > tbody > tr > td:last-child {
    min-width: 0 !important;
}
.eval-page .eval-main-table .label {
    white-space: normal !important;
    line-height: 1.4;
}
/* Tabel utama: bungkus wrap agar tidak bikin scroll browser */
.eval-table-wrap {
    overflow-x: auto;
    max-width: 100%;
}
@media (max-width: 767px) {
    .eval-kpi-row {
        margin-left: 0;
        margin-right: 0;
        max-width: 100%;
    }
    .eval-kpi-col {
        width: 100% !important;
        padding-left: 0;
        padding-right: 0;
    }
}

/* Stat Cards */
.stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 16px;
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
    font-size: 20px;
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

/* Soft Badges & Overrides for Evaluasi Internal (Matching Dashboard & Campaigns pages) */
.badge-soft,
.eval-page .label,
.modal .label,
.eval-page .badge,
.modal .badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    line-height: 1.3;
    text-align: center;
    white-space: nowrap;
    border: 1px solid transparent;
}

.badge-soft-emerald,
.eval-page .label-success,
.modal .label-success {
    background-color: #ecfdf5 !important;
    color: #047857 !important;
    border-color: #a7f3d0 !important;
}

.badge-soft-amber,
.eval-page .label-warning,
.modal .label-warning {
    background-color: #fffbeb !important;
    color: #b45309 !important;
    border-color: #fde68a !important;
}

.badge-soft-rose,
.eval-page .label-danger,
.modal .label-danger {
    background-color: #fef2f2 !important;
    color: #b91c1c !important;
    border-color: #fecaca !important;
}

.badge-soft-blue,
.eval-page .label-primary,
.modal .label-primary {
    background-color: #eff6ff !important;
    color: #1d4ed8 !important;
    border-color: #bfdbfe !important;
}

.eval-page .label-info,
.modal .label-info {
    background-color: #f0fdfa !important;
    color: #0f766e !important;
    border-color: #99f6e4 !important;
}

.badge-soft-slate,
.eval-page .label-default,
.modal .label-default,
.eval-page .bg-gray,
.modal .bg-gray {
    background-color: #f1f5f9 !important;
    color: #475569 !important;
    border-color: #e2e8f0 !important;
}

.eval-page .badge.bg-blue,
.modal .badge.bg-blue {
    background-color: #eff6ff !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe !important;
    font-weight: 600;
}

@media print {
    /* Set layout and colors */
    @page { size: landscape; margin: 1cm; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    body, .content-wrapper, .right-side, .main-footer { 
        margin: 0 !important; 
        padding: 0 !important; 
        background: #fff !important; 
    }
    
    /* Shrink the entire page slightly to ensure everything fits horizontally */
    body {
        zoom: 80%;
    }
    
    /* Hide UI controls */
    .main-sidebar, .main-header, .breadcrumb, .btn, .box-tools, .modal, .modal-backdrop, .dataTables_filter, .dataTables_info, .dataTables_paginate, .print-hidden { 
        display: none !important; 
    }
    
    /* Clean up boxes */
    .box { border: none !important; box-shadow: none !important; margin-bottom: 20px !important; }
    .box-header { border-bottom: 2px solid #ddd !important; padding-left: 0 !important; }
    
    /* Filter form to static text */
    #formFilterStaffEvaluation select { display: inline-block !important; border: none !important; appearance: none; -webkit-appearance: none; background: transparent; font-weight: bold; font-size: 16px; padding: 0; }
    .chosen-container { display: none !important; }
    
    /* KPI Grid for Print */
    .eval-kpi-row { display: block !important; width: 100%; margin: 0 !important; overflow: hidden; }
    .eval-kpi-col { width: 25% !important; float: left !important; padding: 0 10px !important; }
    .stat-card { border-color: #ddd !important; }
    
    /* Tables */
    .table-responsive { overflow: visible !important; border: none !important; }
    .table { width: 100% !important; max-width: 100% !important; border-collapse: collapse !important; font-size: 11px !important; }
    .table-bordered > thead > tr > th, .table-bordered > tbody > tr > td { 
        border: 1px solid #ccc !important; 
        white-space: normal !important; 
        padding: 4px 5px !important;
        word-wrap: break-word !important;
    }
    .table > thead > tr > th { background-color: #f4f6f9 !important; color: #333 !important; min-width: 0 !important; }
    tr, .stat-card { page-break-inside: avoid !important; }
    
    /* Force Background Colors for Labels in Print (Overriding Bootstrap) */
    .label-success { background-color: #00a65a !important; color: #fff !important; border: 1px solid #00a65a !important; }
    .label-warning { background-color: #f39c12 !important; color: #fff !important; border: 1px solid #f39c12 !important; }
    .label-danger  { background-color: #dd4b39 !important; color: #fff !important; border: 1px solid #dd4b39 !important; }
    .label-primary { background-color: #3c8dbc !important; color: #fff !important; border: 1px solid #3c8dbc !important; }
    .label-info    { background-color: #00c0ef !important; color: #fff !important; border: 1px solid #00c0ef !important; }
    .label-default { background-color: #d2d6de !important; color: #444 !important; border: 1px solid #d2d6de !important; }
    .bg-gray       { background-color: #d2d6de !important; color: #444 !important; border: 1px solid #d2d6de !important; }
}
.eval-page .media-body {
    width: auto;
    max-width: 100%;
    overflow: hidden;
}
/* Filter: cegah overlap di layar sempit */
#formFilterStaffEvaluation {
    max-width: 100%;
}
#formFilterStaffEvaluation .chosen-container {
    max-width: 100% !important;
}
@media (max-width: 767px) {
    #formFilterStaffEvaluation {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
    }
    #formFilterStaffEvaluation .form-group {
        flex: 0 0 auto !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    #formFilterStaffEvaluation label {
        display: block !important;
        margin-bottom: 6px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        color: #334155 !important;
    }
    #formFilterStaffEvaluation select,
    #formFilterStaffEvaluation .chosen-container {
        width: 100% !important;
        max-width: 100% !important;
    }
    #formFilterStaffEvaluation .form-group:last-child {
        display: flex !important;
        gap: 8px !important;
        margin-top: 4px !important;
    }
    #formFilterStaffEvaluation .form-group:last-child .btn {
        flex: 1 1 50% !important;
        min-height: 36px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    /* Matikan kolom No sticky agar tidak menimpa/overlap kolom lain saat di-scroll */
    .eval-page .eval-main-table > thead > tr > th:first-child,
    .eval-page .eval-main-table > tbody > tr > td:first-child,
    .eval-table-wrap > table.eval-main-table > thead > tr > th:first-child,
    .eval-table-wrap > table.eval-main-table > tbody > tr > td:first-child {
        position: static !important;
        left: auto !important;
        box-shadow: none !important;
        background-color: transparent !important;
    }
    .eval-table-wrap {
        padding: 0 !important;
    }
    .eval-page .eval-main-table {
        min-width: 820px !important;
        margin-bottom: 0 !important;
    }
}

.eval-campaign-select {
    height: 38px !important;
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    background-color: #ffffff !important;
    color: #1e293b !important;
    font-size: 13px !important;
    font-weight: 500;
    padding: 6px 12px !important;
    cursor: pointer;
}

/* Custom Chosen dropdown styling for campaign filter */
#formFilterStaffEvaluation .chosen-container {
    font-size: 13px !important;
}
#formFilterStaffEvaluation .chosen-container-single .chosen-single {
    height: 38px !important;
    line-height: 36px !important;
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    color: #1e293b !important;
    box-shadow: none !important;
    padding: 0 30px 0 12px !important;
    font-weight: 500 !important;
}
#formFilterStaffEvaluation .chosen-container-single .chosen-single div {
    width: 28px !important;
}
#formFilterStaffEvaluation .chosen-container-single .chosen-single div b {
    background-position: 5px 8px !important;
}
#formFilterStaffEvaluation .chosen-container-active.chosen-with-drop .chosen-single {
    border-color: #3b82f6 !important;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}
#formFilterStaffEvaluation .chosen-drop {
    border: 1px solid #3b82f6 !important;
    border-top: none !important;
    border-radius: 0 0 8px 8px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    z-index: 1060 !important;
}
#formFilterStaffEvaluation .chosen-search {
    padding: 8px 10px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
}
#formFilterStaffEvaluation .chosen-search input {
    height: 36px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 6px 30px 6px 12px !important;
    font-size: 13px !important;
    background-color: #ffffff !important;
    box-sizing: border-box !important;
}
#formFilterStaffEvaluation .chosen-search input:focus {
    border-color: #3b82f6 !important;
    outline: none !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
}
#formFilterStaffEvaluation .chosen-results {
    max-height: 240px !important;
    margin: 0 !important;
    padding: 4px 0 !important;
}
#formFilterStaffEvaluation .chosen-results li {
    padding: 8px 12px !important;
    font-size: 13px !important;
    line-height: 1.4 !important;
    color: #1e293b !important;
}
#formFilterStaffEvaluation .chosen-results li.highlighted {
    background: #3b82f6 !important;
    color: #ffffff !important;
}
</style>

<script>
$(document).ready(function() {
    var $select = $('#campaign_id');
    
    // Inisialisasi chosen agar fitur search keywords aktif di semua ukuran layar
    if ($.fn.chosen) {
        if (!$select.data('chosen')) {
            $select.chosen({
                search_contains: true,
                width: '100%',
                no_results_text: 'Tidak ada kampanye yang cocok:'
            });
        }
    }

    var $container = $select.next('.chosen-container');

    function suppressKeyboard($input) {
        if ($input && $input.length) {
            $input.attr('readonly', 'readonly')
                  .attr('inputmode', 'none')
                  .attr('placeholder', 'Cari kampanye...');
        }
    }

    function enableKeyboard($input) {
        if ($input && $input.length) {
            $input.removeAttr('readonly')
                  .attr('inputmode', 'search');
        }
    }

    // Kondisi awal: kunci search input agar saat dropdown pertama kali dibuka keyboard TIDAK muncul otomatis
    var $search = $container.find('.chosen-search input');
    suppressKeyboard($search);

    // Kunci kembali saat tombol dropdown ditekan
    $container.on('touchstart mousedown', '.chosen-single', function() {
        var $inp = $(this).closest('.chosen-container').find('.chosen-search input');
        suppressKeyboard($inp);
    });

    $select.on('chosen:showing_dropdown', function() {
        var $inp = $(this).next('.chosen-container').find('.chosen-search input');
        suppressKeyboard($inp);
    });

    // Ketika user secara sadar menyentuh / mengklik kolom input pencarian, aktifkan keyboard
    $container.on('touchstart mousedown focus', '.chosen-search input', function(e) {
        var $inp = $(this);
        enableKeyboard($inp);
        if (e.type !== 'focus') {
            $inp.focus();
        }
    });

    // Dukungan input event untuk mobile keyboard (Gboard / swipe / autocomplete)
    $container.on('input', '.chosen-search input', function() {
        var chosenObj = $select.data('chosen');
        if (chosenObj && typeof chosenObj.results_search === 'function') {
            chosenObj.results_search();
        }
    });

    // Jika user desktop langsung mengetik karakter saat dropdown terbuka
    $container.on('keydown', function(e) {
        var $inp = $(this).find('.chosen-search input');
        if ($inp.length && $inp.attr('readonly')) {
            if (e.key && e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
                enableKeyboard($inp);
                $inp.focus();
            }
        }
    });

    // Saat dropdown ditutup atau pilihan berubah, reset ke mode proteksi keyboard
    $select.on('chosen:hiding_dropdown change', function() {
        var $inp = $(this).next('.chosen-container').find('.chosen-search input');
        suppressKeyboard($inp);
    });
});

function preparePrint() {
    // If DataTables is active, show all rows before printing so nothing is cut off
    if (typeof jQuery !== 'undefined' && $.fn.DataTable && $.fn.DataTable.isDataTable('.dataTable')) {
        var dt = $('.dataTable').DataTable();
        var pageLength = dt.page.len();
        
        // Show all rows
        dt.page.len(-1).draw('page');
        
        // Wait for render then print
        setTimeout(function() {
            window.print();
            // Revert back to original pagination after print dialog closes
            setTimeout(function() {
                dt.page.len(pageLength).draw('page');
            }, 1000);
        }, 500);
    } else {
        window.print();
    }
}
</script>
