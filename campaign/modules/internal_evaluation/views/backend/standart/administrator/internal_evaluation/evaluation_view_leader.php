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
        Evaluasi Kinerja Leader
        <small>Penilaian performa kampanye, efisiensi anggaran, pencapaian views, dan TAT Leader</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Evaluasi Kinerja Leader</li>
    </ol>
</section>

<!-- Main content -->
<section class="content eval-page">

        <!-- Navigasi Evaluasi -->
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li><a href="<?= site_url('administrator/internal_evaluation/staff'); ?>"><i class="fa fa-users"></i> Staff Operasional</a></li>
        <li class="active"><a href="<?= site_url('administrator/internal_evaluation/leader'); ?>" style="font-weight: 600;"><i class="fa fa-user-circle"></i> Leader</a></li>
    </ul>

    <!-- Filter and Actions Box -->
    <div class="box box-primary">
        <div class="box-body">
            <form method="GET" action="" id="formFilterLeaderEvaluation" class="form-inline" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div class="form-group" style="flex: 1 1 auto; min-width: 0; max-width: 100%;">
                    <label for="campaign_id" style="margin-right: 10px;">Filter Kampanye:</label>
                    <select name="campaign_id" id="campaign_id" class="form-control chosen-select eval-campaign-select" onchange="document.getElementById('formFilterLeaderEvaluation').submit();" style="min-width: 0; width: 100%; max-width: 320px;">
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
                    <a href="<?= site_url('administrator/internal_evaluation/leader_export_excel?campaign_id='.$active_campaign_id); ?>" class="btn btn-success btn-sm">
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

    <!-- 2. TOP KPI CARDS (RINGKASAN METRIK LEADER) -->
    <div class="eval-kpi-row" style="margin-bottom: 20px;">
        
        <div class="col-md-3 col-sm-6 eval-kpi-col">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Views Attainment (Tim)</h4>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">
                        <?= $team_summary->team_view_attainment; ?>%
                    </div>
                    <div class="stat-card-sub-info">
                        <div class="eval-progress">
                            <div class="eval-progress-bar" style="width: <?= min(100, $team_summary->team_view_attainment); ?>%; background-color: <?= ($team_summary->team_view_attainment >= 80) ? '#166534' : '#d97706'; ?>;"></div>
                        </div>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <div class="stat-card-sub-row">
                        <span>Aktual:</span>
                        <span style="font-weight: 600; color: #0f172a;">
                            <?= number_format($team_summary->total_views, 0, ',', '.'); ?> / <?= number_format($team_summary->target_views, 0, ',', '.'); ?> Views
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 eval-kpi-col">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Budget Efficiency</h4>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">
                        <?= $team_summary->team_budget_efficiency; ?>%
                    </div>
                    <div class="stat-card-sub-info">
                        <span class="badge-soft badge-soft-blue">Avg CPV: Rp <?= number_format($team_summary->team_avg_cpv, 0, ',', '.'); ?><?= (!empty($team_summary->team_target_cpv) && $team_summary->team_target_cpv > 0) ? ' / Rp ' . number_format($team_summary->team_target_cpv, 0, ',', '.') : ''; ?></span>
                        <span class="badge-soft badge-soft-cyan" style="background: #e0f2fe; color: #0369a1; margin-left: 4px;">Avg CPE: Rp <?= number_format($team_summary->team_avg_cpe, 0, ',', '.'); ?></span>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <div class="stat-card-sub-row">
                        <span>Spent:</span>
                        <span style="font-weight: 600; color: #0f172a;">
                            Rp <?= number_format($team_summary->total_spent, 0, ',', '.'); ?> / Rp <?= number_format($team_summary->total_budget, 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 eval-kpi-col">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Deal Conversion</h4>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">
                        <?= $team_summary->team_deal_conversion; ?>%
                    </div>
                    <div class="stat-card-sub-info">
                        <div class="eval-progress">
                            <div class="eval-progress-bar" style="width: <?= min(100, $team_summary->team_deal_conversion); ?>%; background-color: #2563eb;"></div>
                        </div>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <div class="stat-card-sub-row">
                        <span>Deals Closing:</span>
                        <span style="font-weight: 600; color: #0f172a;">
                            <?= number_format($team_summary->deal_success, 0, ',', '.'); ?> / <?= number_format($team_summary->deal_total, 0, ',', '.'); ?> KOL
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 eval-kpi-col">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Approval TAT</h4>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">
                        <?= ($team_summary->team_avg_approval_tat > 0) ? $team_summary->team_avg_approval_tat . ' <span style="font-size: 12px; font-weight: normal; color: #64748b;">Jam</span>' : '-'; ?>
                    </div>
                    <div class="stat-card-sub-info">
                        <span class="badge-soft badge-soft-emerald">Respon Rata-rata Tim</span>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <div class="stat-card-sub-row">
                        <span>Ideal TAT:</span>
                        <span style="font-weight: 600; color: #166534;">&le; 2 Jam</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Data Table -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Scorecard &amp; Evaluasi Kinerja Leader</h3>
            <div class="box-tools pull-right">
                <span class="label label-primary"><?= count($leader_kpis); ?> Leader</span>
            </div>
        </div>
        <div class="box-body table-responsive mscroll eval-table-wrap">
            <table class="table table-bordered table-striped table-hover dataTable print-table eval-main-table">
                <thead>
                    <tr>
                        <th style="width: 36px; text-align: center;">No</th>
                        <th style="min-width: 170px;">Leader</th>
                        <th style="text-align: center; min-width: 80px;">Campaigns</th>
                        <th style="min-width: 120px;">Views Attainment</th>
                        <th style="min-width: 100px;">CPV Efficiency</th>
                        <th style="min-width: 95px;">CPE</th>
                        <th style="min-width: 80px;">Deal Conv.</th>
                        <th style="min-width: 100px;">Approval TAT</th>
                        <th style="min-width: 130px;">Predikat Kinerja</th>
                        <th class="print-hidden" style="width: 100px; min-width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($leader_kpis)): ?>
                        <?php 
                        $no = 1;
                        foreach ($leader_kpis as $leader): 
                        ?>
                        <tr>
                            <td style="text-align: center; vertical-align: middle;"><?= $no++; ?></td>
                            
                            <!-- Leader Profile -->
                            <td style="vertical-align: middle;">
                                <div class="media" style="margin-top: 0;">
                                    <div class="media-left">
                                        <img src="<?= BASE_URL . 'uploads/user/' . ($leader->avatar ?: 'default.png'); ?>" alt="Avatar" class="media-object img-circle" style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #e2e8f0;">
                                    </div>
                                    <div class="media-body" style="vertical-align: middle;">
                                        <h5 class="media-heading" style="margin: 0; font-weight: 600; color: #0f172a; font-size: 14px;"><?= htmlspecialchars($leader->full_name); ?></h5>
                                        <div style="font-size: 11px; color: #64748b;">@<?= htmlspecialchars($leader->username); ?></div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Campaigns -->
                            <td style="text-align: center; vertical-align: middle;">
                                <span class="badge-soft badge-soft-slate"><?= $leader->total_campaigns; ?> Campaign</span>
                            </td>

                            <!-- Views Attainment -->
                            <td style="vertical-align: middle;">
                                <div style="font-weight: bold; color: <?= ($leader->view_attainment_pct >= 80) ? '#166534' : '#0f172a'; ?>; font-size: 14px;"><?= $leader->view_attainment_pct; ?>%</div>
                                <div style="font-size: 11px; color: #64748b;"><?= number_format($leader->actual_views, 0, ',', '.'); ?> / <?= number_format($leader->total_target_views, 0, ',', '.'); ?> Views</div>
                            </td>

                            <!-- CPV Efficiency -->
                            <td style="vertical-align: middle;">
                                <div style="font-weight: bold; color: <?= ($leader->actual_cpv <= 50) ? '#166534' : '#0f172a'; ?>; font-size: 14px;">
                                    Rp <?= number_format($leader->actual_cpv, 0, ',', '.'); ?>
                                    <?php if (!empty($leader->total_target_cpv) && $leader->total_target_cpv > 0): ?>
                                        <span style="font-weight: normal; color: #64748b; font-size: 11px;">/ Rp <?= number_format($leader->total_target_cpv, 0, ',', '.'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 11px; color: #64748b;"><?= $leader->budget_efficiency_pct; ?>% Budget Terpakai</div>
                            </td>

                            <!-- CPE -->
                            <td style="vertical-align: middle;">
                                <?php if ($leader->actual_cpe > 0): ?>
                                    <div style="font-weight: bold; color: #0284c7; font-size: 14px;">Rp <?= number_format($leader->actual_cpe, 0, ',', '.'); ?></div>
                                    <div style="font-size: 11px; color: #64748b;"><?= number_format($leader->actual_engagements, 0, ',', '.'); ?> Engagements</div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Deal Conversion -->
                            <td style="vertical-align: middle;">
                                <div style="font-weight: bold; color: <?= ($leader->deal_conversion_pct >= 50) ? '#166534' : '#0f172a'; ?>; font-size: 14px;"><?= $leader->deal_conversion_pct; ?>%</div>
                                <div style="font-size: 11px; color: #64748b;"><?= $leader->deal_success; ?> / <?= $leader->total_negotiated; ?> Deals</div>
                            </td>

                            <!-- Approval TAT -->
                            <td style="vertical-align: middle;">
                                <?php if ($leader->avg_turnaround_hours !== null): ?>
                                    <div style="font-weight: bold; font-size: 14px; color: <?= ($leader->avg_turnaround_hours <= 4) ? '#166534' : '#0f172a'; ?>;"><?= $leader->avg_turnaround_hours; ?> Jam</div>
                                    <div style="font-size: 11px; color: #64748b;"><?= $leader->reviewed_items; ?> Draft Disetujui</div>
                                <?php else: ?>
                                    <div style="color: #94a3b8; font-style: italic;">Belum ada review</div>
                                <?php endif; ?>
                            </td>

                            <!-- Predikat Kinerja -->
                            <td style="vertical-align: middle;">
                                <?php if (isset($leader->excluded) && $leader->excluded): ?>
                                    <span class="label <?= $badge_map[$leader->grade_badge] ?? 'label-default'; ?>" style="font-size: 12px; padding: 4px 8px;"><?= htmlspecialchars($leader->grade_label); ?></span>
                                <?php else: ?>
                                    <span class="label <?= $badge_map[$leader->grade_badge] ?? 'label-default'; ?>" style="font-size: 12px; padding: 4px 8px; margin-bottom: 5px; display: inline-block;"<?php if (!empty($leader->is_provisional) && !empty($leader->provisional_reasons)): ?> title="<?= htmlspecialchars(implode(', ', $leader->provisional_reasons)); ?>"<?php endif; ?>><?= htmlspecialchars($leader->grade_label); ?><?php if (!empty($leader->is_provisional) && !empty($leader->provisional_reasons)): ?> <i class="fa fa-question-circle" title="<?= htmlspecialchars(implode(', ', $leader->provisional_reasons)); ?>"></i><?php endif; ?></span>
                                    <div style="font-size: 12px; font-weight: 600; color: #334155; margin-top: 4px;">Skor: <?= $leader->score; ?> <span style="font-weight: normal; color: #94a3b8;">/<?= $leader->max_earned; ?></span></div>
                                <?php endif; ?>
                            </td>

                            <!-- Aksi (Menampilkan Modal Drilldown) -->
                            <td class="print-hidden" style="text-align: center; vertical-align: middle;">
                                <?php if (!isset($leader->excluded) || !$leader->excluded): ?>
                                <button type="button" class="btn btn-default btn-xs" onclick="showLeaderDrilldown(this)" data-leader-name="<?= htmlspecialchars($leader->full_name); ?>" data-campaigns="<?= htmlspecialchars(json_encode($leader->campaigns), ENT_QUOTES, 'UTF-8'); ?>" title="Lihat daftar campaign"><i class="fa fa-search"></i> Detail</button>
                                <?php else: ?>
                                <button type="button" class="btn btn-default btn-xs" disabled><i class="fa fa-search"></i> Detail</button>
                                <?php endif; ?>
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
    <?php foreach ($leader_kpis as $leader): ?>
    <div class="modal fade" id="modalStaffDetail_<?= $leader->id; ?>" tabindex="-1" role="dialog" aria-labelledby="labelStaffModal_<?= $leader->id; ?>">
        <div class="modal-dialog modal-lg" role="document" style="width: 96%; max-width: 1600px;">
            <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                
                <div class="modal-header" style="background-color: #f4f6f9; border-bottom: 1px solid #d2d6de; padding: 15px 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 5px; font-size: 24px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="media" style="margin: 0;">
                        <div class="media-left">
                            <?php 
                            $m_staff_avatar = $leader->avatar;
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
                            <h4 class="modal-title" id="labelStaffModal_<?= $leader->id; ?>" style="font-weight: bold; margin-bottom: 3px; font-size: 15px; color: #333;">
                                Rapor Bukti Penugasan: <?= htmlspecialchars($leader->full_name); ?>
                            </h4>
                            <div style="font-size: 13px; color: #666;">
                                <i class="fa fa-at"></i> <?= htmlspecialchars($leader->username); ?> &nbsp;|&nbsp; 
                                Predikat Kinerja: <span class="label <?= isset($badge_map[$leader->grade_badge]) ? $badge_map[$leader->grade_badge] : 'label-default'; ?>"><?= $leader->grade_label; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body" style="padding: 20px;">
                    
                    <!-- Quick Summary using Description Blocks -->
                    <div class="row" style="margin-bottom: 20px; background: #fafafa; border: 1px solid #eee; border-radius: 4px; padding: 10px 0;">
                        <div class="col-sm-3 col-xs-6" style="border-right: 1px solid #e0e0e0;">
                            <div class="description-block" style="text-align: center; margin: 5px 0;">
                                <h5 class="description-header" style="font-size: 18px; color: #3c8dbc; margin-bottom: 5px;"><?= $leader->kol_count; ?></h5>
                                <span class="description-text" style="font-size: 11px; color: #777; text-transform: uppercase; font-weight: bold;">KOL Di-handle</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-6" style="border-right: 1px solid #e0e0e0;">
                            <div class="description-block" style="text-align: center; margin: 5px 0;">
                                <h5 class="description-header" style="font-size: 18px; color: #00a65a; margin-bottom: 5px;"><?= ($leader->on_time_rate !== null) ? $leader->on_time_rate . '%' : '-'; ?></h5>
                                <span class="description-text" style="font-size: 11px; color: #777; text-transform: uppercase; font-weight: bold;">SLA On-Time</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-6" style="border-right: 1px solid #e0e0e0;">
                            <div class="description-block" style="text-align: center; margin: 5px 0;">
                                <h5 class="description-header" style="font-size: 18px; color: #f39c12; margin-bottom: 5px;"><?= ($leader->avg_turnaround_hours !== null) ? $leader->avg_turnaround_hours . ' Jam' : '-'; ?></h5>
                                <span class="description-text" style="font-size: 11px; color: #777; text-transform: uppercase; font-weight: bold;">Respon Review</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block" style="text-align: center; margin: 5px 0;">
                                <h5 class="description-header" style="font-size: 18px; color: #00c0ef; margin-bottom: 5px;"><?= ($leader->avg_dispatch_days !== null) ? $leader->avg_dispatch_days . ' Hari' : '-'; ?></h5>
                                <span class="description-text" style="font-size: 11px; color: #777; text-transform: uppercase; font-weight: bold;">Kirim Sampel</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Table -->
                    <h5 style="margin-top: 0; font-weight: bold; border-bottom: 2px solid #3c8dbc; padding-bottom: 8px; margin-bottom: 15px; color: #333; font-size: 14px;">
                        <i class="fa fa-list-alt" style="margin-right: 5px;"></i> Daftar Task &amp; Konten yang Dikawal
                    </h5>

                    <?php if (!empty($leader->tasks)): ?>
                        <div class="table-responsive mscroll">
                            <table class="table table-bordered table-striped table-hover table-condensed" style="font-size: 13px;">
                                <thead>
                    <tr>
                        <th style="width: 36px; text-align: center;">No</th>
                        <th style="min-width: 170px;">Leader</th>
                        <th style="text-align: center; min-width: 80px;">Campaigns</th>
                        <th style="min-width: 120px;">Views Attainment</th>
                        <th style="min-width: 100px;">CPV Efficiency</th>
                        <th style="min-width: 80px;">Deal Conv.</th>
                        <th style="min-width: 100px;">Approval TAT</th>
                        <th style="min-width: 130px;">Predikat Kinerja</th>
                        <th class="print-hidden" style="width: 100px; min-width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                                <tbody>
                                    <?php 
                                    $t_no = 1;
                                    foreach ($leader->tasks as $t): 
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
    #formFilterLeaderEvaluation select { display: inline-block !important; border: none !important; appearance: none; -webkit-appearance: none; background: transparent; font-weight: bold; font-size: 16px; padding: 0; }
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
#formFilterLeaderEvaluation {
    max-width: 100%;
}
#formFilterLeaderEvaluation .chosen-container {
    max-width: 100% !important;
}
@media (max-width: 767px) {
    #formFilterLeaderEvaluation {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
    }
    #formFilterLeaderEvaluation .form-group {
        flex: 0 0 auto !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    #formFilterLeaderEvaluation label {
        display: block !important;
        margin-bottom: 6px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        color: #334155 !important;
    }
    #formFilterLeaderEvaluation select,
    #formFilterLeaderEvaluation .chosen-container {
        width: 100% !important;
        max-width: 100% !important;
    }
    #formFilterLeaderEvaluation .form-group:last-child {
        display: flex !important;
        gap: 8px !important;
        margin-top: 4px !important;
    }
    #formFilterLeaderEvaluation .form-group:last-child .btn {
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
#formFilterLeaderEvaluation .chosen-container {
    font-size: 13px !important;
}
#formFilterLeaderEvaluation .chosen-container-single .chosen-single {
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
#formFilterLeaderEvaluation .chosen-container-single .chosen-single div {
    width: 28px !important;
}
#formFilterLeaderEvaluation .chosen-container-single .chosen-single div b {
    background-position: 5px 8px !important;
}
#formFilterLeaderEvaluation .chosen-container-active.chosen-with-drop .chosen-single {
    border-color: #3b82f6 !important;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}
#formFilterLeaderEvaluation .chosen-drop {
    border: 1px solid #3b82f6 !important;
    border-top: none !important;
    border-radius: 0 0 8px 8px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    z-index: 1060 !important;
}
#formFilterLeaderEvaluation .chosen-search {
    padding: 8px 10px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
}
#formFilterLeaderEvaluation .chosen-search input {
    height: 36px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 6px 30px 6px 12px !important;
    font-size: 13px !important;
    background-color: #ffffff !important;
    box-sizing: border-box !important;
}
#formFilterLeaderEvaluation .chosen-search input:focus {
    border-color: #3b82f6 !important;
    outline: none !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
}
#formFilterLeaderEvaluation .chosen-results {
    max-height: 240px !important;
    margin: 0 !important;
    padding: 4px 0 !important;
}
#formFilterLeaderEvaluation .chosen-results li {
    padding: 8px 12px !important;
    font-size: 13px !important;
    line-height: 1.4 !important;
    color: #1e293b !important;
}
#formFilterLeaderEvaluation .chosen-results li.highlighted {
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


    <!-- 3. MODAL DRILL-DOWN LEADER -->
    <div class="modal fade" id="leaderDrilldownModal" tabindex="-1" role="dialog" aria-labelledby="leaderDrilldownModalLabel">
      <div class="modal-dialog modal-lg" role="document" style="width: 95%; max-width: 1300px;">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="leaderDrilldownModalLabel" style="font-weight: 600; color: #0f172a;">
                Detail Campaign: <span id="drilldownLeaderName" style="color: #2563eb;"></span>
            </h4>
          </div>
          <div class="modal-body" style="padding: 15px 20px; max-height: 75vh; overflow-y: auto; overflow-x: auto;">
            <table class="table table-striped table-hover" style="margin: 0; min-width: 900px; border-bottom: 1px solid #e2e8f0;">
                <thead style="background-color: #f1f5f9; position: sticky; top: -15px; z-index: 10;">
                    <tr>
                        <th style="width: 40px; text-align: center;">No</th>
                        <th>Nama Campaign</th>
                        <th style="text-align: center; width: 120px;">Status</th>
                        <th style="text-align: right; width: 220px;">Views (Aktual / Target)</th>
                        <th style="text-align: right; width: 220px;">Budget (Spent / Target)</th>
                        <th style="text-align: right; width: 210px;">CPV (Aktual / Target)</th>
                        <th style="text-align: right; width: 210px;">CPE (Aktual / Target)</th>
                    </tr>
                </thead>
                <tbody id="leaderDrilldownBody">
                    <!-- Populated via JS -->
                </tbody>
            </table>
          </div>
          <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <script>
    function showLeaderDrilldown(btn) {
        var leaderName = btn.getAttribute("data-leader-name");
        var campaigns = JSON.parse(btn.getAttribute("data-campaigns") || "[]");
        
        $("#drilldownLeaderName").text(leaderName);
        var tbody = $("#leaderDrilldownBody");
        tbody.empty();
        
        if (campaigns.length === 0) {
            tbody.append("<tr><td colspan='7' class='text-center'>Tidak ada data campaign</td></tr>");
        } else {
            campaigns.forEach(function(c, idx) {
                var targetV = parseInt(c.target_views) || 0;
                var actV = parseInt(c.actual_views) || 0;
                var actE = parseInt(c.actual_engagements) || 0;
                var viewPct = targetV > 0 ? ((actV / targetV) * 100).toFixed(1) : 0;
                var viewColor = viewPct >= 80 ? "#166534" : (viewPct > 0 ? "#d97706" : "#64748b");
                
                var targetB = parseInt(c.budget) || 0;
                var actB = parseInt(c.actual_spent) || 0;
                
                // CPV Calculation (Actual & Target)
                var actCpv = actV > 0 ? Math.round(actB / actV) : 0;
                var targetCpv = parseFloat(c.target_cpv) || 0;
                if (targetCpv <= 0 && targetV > 0 && targetB > 0) {
                    targetCpv = Math.round(targetB / targetV);
                } else {
                    targetCpv = Math.round(targetCpv);
                }

                // CPE Calculation (Actual & Target)
                var actCpe = actE > 0 ? Math.round(actB / actE) : 0;
                var targetCpe = parseFloat(c.target_cpe) || 0;
                if (targetCpe <= 0 && parseFloat(c.target_er) > 0 && targetV > 0 && targetB > 0) {
                    var estEng = targetV * (parseFloat(c.target_er) / 100);
                    if (estEng > 0) targetCpe = Math.round(targetB / estEng);
                } else {
                    targetCpe = Math.round(targetCpe);
                }

                var viewHtml = "<div style='color: " + viewColor + "; font-weight: bold;'>" + actV.toLocaleString("id-ID") + " <span style='font-weight: normal; color: #64748b; font-size: 11px;'>/ " + targetV.toLocaleString("id-ID") + " (" + viewPct + "%)</span></div>";
                var budgetHtml = "<div style='font-weight: bold;'>Rp " + actB.toLocaleString("id-ID") + " <span style='font-weight: normal; color: #64748b; font-size: 11px;'>/ Rp " + targetB.toLocaleString("id-ID") + "</span></div>";

                // Format CPV (Aktual / Target)
                var cpvHtml = "-";
                if (actCpv > 0 && targetCpv > 0) {
                    var cpvColor = actCpv <= targetCpv ? "#166534" : "#dc2626";
                    var cpvDiffPct = Math.round(((actCpv - targetCpv) / targetCpv) * 100);
                    var cpvBadge = "";
                    if (actCpv <= targetCpv) {
                        cpvBadge = " <span class='badge' style='background-color: #dcfce7; color: #166534; font-size: 10px; font-weight: 600; padding: 2px 4px;'>-" + Math.abs(cpvDiffPct) + "%</span>";
                    } else {
                        cpvBadge = " <span class='badge' style='background-color: #fee2e2; color: #dc2626; font-size: 10px; font-weight: 600; padding: 2px 4px;'>+" + cpvDiffPct + "%</span>";
                    }
                    cpvHtml = "<div style='color: " + cpvColor + "; font-weight: bold;'>Rp " + actCpv.toLocaleString("id-ID") + " <span style='font-weight: normal; color: #64748b; font-size: 11px;'>/ Rp " + targetCpv.toLocaleString("id-ID") + "</span>" + cpvBadge + "</div>";
                } else if (actCpv > 0) {
                    cpvHtml = "<div style='font-weight: bold;'>Rp " + actCpv.toLocaleString("id-ID") + " <span style='font-weight: normal; color: #64748b; font-size: 11px;'>/ -</span></div>";
                } else if (targetCpv > 0) {
                    cpvHtml = "<div style='color: #64748b;'>- <span style='font-weight: normal; font-size: 11px;'>/ Rp " + targetCpv.toLocaleString("id-ID") + "</span></div>";
                }

                // Format CPE (Aktual / Target)
                var cpeHtml = "-";
                if (actCpe > 0 && targetCpe > 0) {
                    var cpeColor = actCpe <= targetCpe ? "#0284c7" : "#dc2626";
                    var cpeDiffPct = Math.round(((actCpe - targetCpe) / targetCpe) * 100);
                    var cpeBadge = "";
                    if (actCpe <= targetCpe) {
                        cpeBadge = " <span class='badge' style='background-color: #e0f2fe; color: #0284c7; font-size: 10px; font-weight: 600; padding: 2px 4px;'>-" + Math.abs(cpeDiffPct) + "%</span>";
                    } else {
                        cpeBadge = " <span class='badge' style='background-color: #fee2e2; color: #dc2626; font-size: 10px; font-weight: 600; padding: 2px 4px;'>+" + cpeDiffPct + "%</span>";
                    }
                    cpeHtml = "<div style='color: " + cpeColor + "; font-weight: bold;'>Rp " + actCpe.toLocaleString("id-ID") + " <span style='font-weight: normal; color: #64748b; font-size: 11px;'>/ Rp " + targetCpe.toLocaleString("id-ID") + "</span>" + cpeBadge + "</div>";
                } else if (actCpe > 0) {
                    cpeHtml = "<div style='color: #0284c7; font-weight: bold;'>Rp " + actCpe.toLocaleString("id-ID") + " <span style='font-weight: normal; color: #64748b; font-size: 11px;'>/ -</span></div>";
                } else if (targetCpe > 0) {
                    cpeHtml = "<div style='color: #64748b;'>- <span style='font-weight: normal; font-size: 11px;'>/ Rp " + targetCpe.toLocaleString("id-ID") + "</span></div>";
                }
                
                var statusLabel = "label-default";
                if(c.status === "ACTIVE") statusLabel = "label-success";
                else if(c.status === "COMPLETED") statusLabel = "label-primary";
                else if(c.status === "OPEN_REGISTRATION") statusLabel = "label-warning";

                var tr = $("<tr></tr>");
                tr.append("<td style='text-align: center; vertical-align: middle;'>" + (idx + 1) + "</td>");
                tr.append("<td style='vertical-align: middle;'><strong>" + c.campaign_name + "</strong></td>");
                tr.append("<td style='text-align: center; vertical-align: middle;'><span class='label " + statusLabel + "'>" + c.status + "</span></td>");
                tr.append("<td style='text-align: right; vertical-align: middle;'>" + viewHtml + "</td>");
                tr.append("<td style='text-align: right; vertical-align: middle;'>" + budgetHtml + "</td>");
                tr.append("<td style='text-align: right; vertical-align: middle;'>" + cpvHtml + "</td>");
                tr.append("<td style='text-align: right; vertical-align: middle;'>" + cpeHtml + "</td>");
                tbody.append(tr);
            });
        }
        
        $("#leaderDrilldownModal").modal("show");
    }
    </script>
