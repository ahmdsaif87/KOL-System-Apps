<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
if (!function_exists('dash_format_short')) {
    function dash_format_short($num) {
        $num = (float)$num;
        if ($num >= 1000000000) return number_format($num / 1000000000, 1) . 'B';
        if ($num >= 1000000) return number_format($num / 1000000, 1) . 'M';
        if ($num >= 1000) return number_format($num / 1000, 1) . 'K';
        return number_format($num);
    }
}
?>

<style>
    .dash-stats-grid {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -6px 16px -6px;
    }
    .dash-stats-grid .dash-widget-col {
        flex: 1 1 calc(25% - 12px);
        width: 25%;
        padding: 0 6px;
        margin-bottom: 8px;
    }
    .dash-widget {
        background: #fff;
        border-radius: 10px;
        padding: 18px 12px;
        min-height: 105px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        border: 1px solid #E2E8F0;
        margin-bottom: 0px;
        position: relative;
        text-align: center;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .dash-widget:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.06);
    }
    .widget-title {
        font-size: 12px;
        color: #94A3B8;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-top: 4px;
        text-align: center;
    }
    .widget-number {
        font-size: 26px;
        font-weight: 800;
        color: #0F172A;
        line-height: 1.1;
        text-align: center;
    }
    .widget-subtext {
        font-size: 11px;
        color: #64748B;
        margin-top: 4px;
        font-weight: 500;
    }
    .text-success { color: #059669; }
    .text-danger { color: #DC2626; }
    .text-primary-blue { color: #2563EB; }
    .text-purple { color: #7C3AED; }
    .text-amber { color: #D97706; }

    @media (max-width: 768px) {
        /* Header Button Swap: Hamburger first, Logo second */
        .main-header {
            position: relative !important;
        }
        .main-header .navbar {
            position: static !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
        }
        .main-header .sidebar-toggle {
            position: absolute !important;
            left: 6px !important;
            top: 8px !important;
            z-index: 1040 !important;
            margin: 0 !important;
        }
        .main-header .logo {
            position: static !important;
            margin: 0 0 0 46px !important;
            display: inline-flex !important;
        }

        .dash-stats-grid {
            display: flex !important;
            flex-wrap: wrap !important;
            margin: 0 -4px !important;
        }
        .dash-stats-grid .dash-widget-col {
            width: 50% !important;
            padding: 0 4px !important;
            float: none !important;
            flex: none !important;
        }
        .dash-widget {
            min-height: 96px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            padding: 18px 10px !important;
            margin-bottom: 8px !important;
            border-radius: 12px !important;
            text-align: center !important;
        }
        .dash-widget .widget-icon-wrap,
        .dash-widget .widget-icon {
            display: none !important;
        }
        .dash-widget .widget-number {
            font-size: 24px !important;
            line-height: 1.1 !important;
            margin-bottom: 4px !important;
            text-align: center !important;
        }
        .dash-widget .widget-title {
            font-size: 11px !important;
            letter-spacing: 0.3px !important;
            color: #94A3B8 !important;
            text-align: center !important;
            margin-top: 0 !important;
        }

        /* Remove double-nested container padding and border */
        .box-body.table-responsive.no-padding {
            padding: 6px 6px !important;
            border: none !important;
            margin-bottom: 0 !important;
        }

        /* Card List Transformation */
        table.dash-card-table thead,
        .dash-card-table thead {
            display: none !important;
        }
        table.dash-card-table,
        table.dash-card-table > tbody,
        .dash-card-table tbody {
            display: block !important;
            width: 100% !important;
        }
        table.dash-card-table > tbody > tr,
        .dash-card-table tbody tr {
            display: block !important;
            background: #FFFFFF !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 10px !important;
            padding: 10px 12px !important;
            margin-bottom: 8px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
            position: relative !important;
        }
        table.dash-card-table > tbody > tr:last-child {
            margin-bottom: 0 !important;
        }

        /* High specificity overrides over .table > tbody > tr > td */
        table.dash-card-table > tbody > tr > td,
        .dash-card-table tbody td {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 2px 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            line-height: 1.3 !important;
            border: none !important;
            border-top: none !important;
            border-bottom: none !important;
            font-size: 13px !important;
            color: #334155 !important;
            min-height: auto !important;
            min-width: auto !important;
        }
        table.dash-card-table > tbody > tr > td:last-child,
        .dash-card-table tbody td:last-child {
            padding-right: 0 !important;
            min-width: auto !important;
        }
        table.dash-card-table > tbody > tr > td::before,
        .dash-card-table tbody td::before {
            content: attr(data-label) !important;
            font-weight: 600 !important;
            font-size: 11px !important;
            color: #94A3B8 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            flex-shrink: 0 !important;
            margin-right: 12px !important;
            line-height: 1.3 !important;
        }
        /* First cell (KOL Name header) */
        table.dash-card-table > tbody > tr > td:first-child,
        .dash-card-table tbody td:first-child {
            padding-top: 0 !important;
            padding-bottom: 7px !important;
            margin-bottom: 6px !important;
            border-bottom: 1px solid #F1F5F9 !important;
        }
        table.dash-card-table > tbody > tr > td:first-child strong,
        .dash-card-table tbody td:first-child strong {
            font-size: 14px !important;
            color: #0F172A !important;
        }

        /* Stale KOL profiles */
        table.dash-stale-table > tbody > tr > td[data-label="KOL"],
        .dash-stale-table tbody td[data-label="KOL"] {
            justify-content: flex-start !important;
            gap: 0 !important;
        }
        table.dash-stale-table > tbody > tr > td[data-label="KOL"]::before,
        .dash-stale-table tbody td[data-label="KOL"]::before {
            display: none !important;
        }
        table.dash-stale-table > tbody > tr > td[data-label="Action"],
        .dash-stale-table tbody td[data-label="Action"] {
            padding-top: 8px !important;
            margin-top: 6px !important;
            border-top: 1px solid #F1F5F9 !important;
        }
        table.dash-stale-table > tbody > tr > td[data-label="Action"] .btn,
        .dash-stale-table tbody td[data-label="Action"] .btn {
            width: 100% !important;
            text-align: center !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
            min-height: 36px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
        }
        .desktop-only { display: none !important; }
        .mobile-only { display: inline !important; }

        #stale-pagination-box {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            padding: 12px 10px !important;
            text-align: center !important;
        }
        #stale-pagination-box .stale-page-info {
            float: none !important;
            line-height: normal !important;
            font-size: 12px !important;
        }
        #stale-pagination-box .pagination {
            float: none !important;
            display: inline-flex !important;
            margin: 0 !important;
        }
        #stale-pagination-box .pagination > li > a {
            min-width: 34px !important;
            min-height: 34px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 13px !important;
            border-radius: 6px !important;
            margin: 0 2px !important;
            padding: 6px 10px !important;
        }
    }
    table.dash-card-table > tbody > tr.stale-row-hidden,
    .dash-card-table tbody tr.stale-row-hidden,
    tr.stale-row-hidden,
    .stale-row-hidden {
        display: none !important;
    }
    #stale-pagination-box .pagination > li > a {
        cursor: pointer;
    }
</style>

<section class="content-header">
    <h1>KOL Data Hub <small>Dashboard</small></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>

<section class="content">
    <!-- Row 1: Key Performance Indicators (4 Cards) -->
    <div class="row dash-stats-grid">
        <div class="dash-widget-col">
            <div class="dash-widget">
                <div class="widget-number"><?= number_format($total_kols) ?></div>
                <div class="widget-title">Active KOLs</div>
                <div class="widget-subtext">Verified Roster</div>
            </div>
        </div>
        <div class="dash-widget-col">
            <div class="dash-widget">
                <div class="widget-number text-primary-blue"><?= dash_format_short($total_reach) ?></div>
                <div class="widget-title">Network Reach</div>
                <div class="widget-subtext">Total Followers</div>
            </div>
        </div>
        <div class="dash-widget-col">
            <div class="dash-widget">
                <div class="widget-number text-success"><?= number_format($campaign_stats->active_campaigns ?? 0) ?></div>
                <div class="widget-title">Active Campaigns</div>
                <div class="widget-subtext">IDR <?= dash_format_short($campaign_stats->active_budget ?? 0) ?> budget</div>
            </div>
        </div>
        <div class="dash-widget-col">
            <div class="dash-widget">
                <div class="widget-number text-purple"><?= dash_format_short($content_stats->total_views ?? 0) ?></div>
                <div class="widget-title">Delivered Views</div>
                <div class="widget-subtext"><?= number_format(($content_stats->avg_er ?? 0) * 100, 2) ?>% Avg ER</div>
            </div>
        </div>
    </div>

    <!-- Section 1: Urgent Action Center (Stacked Full Width) -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger" style="border-radius: 10px; border-top-width: 3px; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                <div class="box-header with-border" style="padding: 14px 16px;">
                    <h3 class="box-title" style="font-weight: 700; font-size: 15px; color: #0F172A;">
                        <i class="fa fa-bolt text-danger" style="margin-right: 6px;"></i> Urgent Action Center
                    </h3>
                    <span class="label label-danger pull-right" style="border-radius: 10px; font-weight: 600; padding: 4px 8px;">
                        <?= $action_items['total_actions'] ?> Attention Needed
                    </span>
                </div>
                <div class="box-body" style="padding: 14px 16px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border-radius:8px; background:#F8FAFC; border:1px solid #E2E8F0; margin-bottom:10px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#EFF6FF; color:#2563EB; font-size:15px; flex-shrink:0;">
                                <i class="fa fa-file-video-o"></i>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:13px; color:#1E293B;">Content Drafts to Review</div>
                                <div style="font-size:11px; color:#64748B;">Submitted content drafts awaiting approval</div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span class="badge" style="background:<?= $action_items['pending_drafts'] > 0 ? '#EF4444' : '#10B981' ?>; font-size:12px; padding:4px 8px; border-radius:6px;"><?= $action_items['pending_drafts'] ?></span>
                            <a href="<?= site_url('administrator/my_tasks') ?>" class="btn btn-xs btn-default" style="border-radius:6px; font-weight:600;">Open &rarr;</a>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border-radius:8px; background:#F8FAFC; border:1px solid #E2E8F0; margin-bottom:10px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#FFFBEB; color:#D97706; font-size:15px; flex-shrink:0;">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:13px; color:#1E293B;">Overdue Posting Deadlines</div>
                                <div style="font-size:11px; color:#64748B;">Tasks past estimated date without live post</div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span class="badge" style="background:<?= $action_items['overdue_tasks'] > 0 ? '#F59E0B' : '#10B981' ?>; font-size:12px; padding:4px 8px; border-radius:6px;"><?= $action_items['overdue_tasks'] ?></span>
                            <a href="<?= site_url('administrator/my_tasks') ?>" class="btn btn-xs btn-default" style="border-radius:6px; font-weight:600;">Open &rarr;</a>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border-radius:8px; background:#F8FAFC; border:1px solid #E2E8F0; margin-bottom:10px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#F5F3FF; color:#7C3AED; font-size:15px; flex-shrink:0;">
                                <i class="fa fa-file-text-o"></i>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:13px; color:#1E293B;">Briefs Pending Approval</div>
                                <div style="font-size:11px; color:#64748B;">Campaign briefs awaiting review / revision</div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span class="badge" style="background:<?= $action_items['pending_briefs'] > 0 ? '#8B5CF6' : '#10B981' ?>; font-size:12px; padding:4px 8px; border-radius:6px;"><?= $action_items['pending_briefs'] ?></span>
                            <a href="<?= site_url('administrator/my_tasks') ?>" class="btn btn-xs btn-default" style="border-radius:6px; font-weight:600;">Open &rarr;</a>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border-radius:8px; background:#F8FAFC; border:1px solid #E2E8F0;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#ECFDF5; color:#059669; font-size:15px; flex-shrink:0;">
                                <i class="fa fa-truck"></i>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:13px; color:#1E293B;">Seeding Packages Pending</div>
                                <div style="font-size:11px; color:#64748B;">Product packages ready for courier dispatch</div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span class="badge" style="background:<?= $action_items['pending_shipments'] > 0 ? '#059669' : '#10B981' ?>; font-size:12px; padding:4px 8px; border-radius:6px;"><?= $action_items['pending_shipments'] ?></span>
                            <a href="<?= site_url('administrator/kol_shipments') ?>" class="btn btn-xs btn-default" style="border-radius:6px; font-weight:600;">Ship &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Top Delivered Postings (Stacked Full Width) -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" style="border-radius: 10px; border-top-width: 3px; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                <div class="box-header with-border" style="padding: 14px 16px;">
                    <h3 class="box-title" style="font-weight: 700; font-size: 15px; color: #0F172A;">
                        <i class="fa fa-play-circle text-primary" style="margin-right: 6px;"></i> Top Delivered Postings
                    </h3>
                    <span class="label label-primary pull-right" style="border-radius: 10px; font-weight: 600; padding: 4px 8px;">
                        <?= count($top_postings) ?> Live Posts
                    </span>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover dash-card-table">
                        <thead>
                        <tr>
                            <th>KOL</th>
                            <th>Platform</th>
                            <th>Live Link</th>
                            <th>Views</th>
                            <th>ER</th>
                            <th>CPV</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($top_postings)): ?>
                        <tr><td colspan="6" class="text-center" style="padding: 24px; color: #94A3B8;">No live postings recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($top_postings as $tp): ?>
                            <tr>
                                <td data-label="KOL">
                                    <div style="display:flex; align-items:center;">
                                        <?php 
                                        $avatar = base_url('asset/img/avatar.png');
                                        if (!empty($tp->avatar_url)) {
                                            if (preg_match('/^https?:\/\//i', $tp->avatar_url)) {
                                                $avatar = base_url('avatar_proxy.php?url=' . urlencode($tp->avatar_url));
                                            } else {
                                                $avatar = base_url('uploads/kols/' . $tp->avatar_url);
                                            }
                                        }
                                        ?>
                                        <img src="<?= $avatar ?>" style="width:28px;height:28px;border-radius:50%;margin-right:8px;object-fit:cover;" onerror="this.src='<?= base_url('asset/img/avatar.png') ?>'" referrerpolicy="no-referrer">
                                        <strong><?= htmlspecialchars($tp->kol_name) ?></strong>
                                    </div>
                                </td>
                                <td data-label="Platform">
                                    <span class="label label-default" style="font-weight: 600;"><?= htmlspecialchars($tp->platform_name) ?></span>
                                </td>
                                <td data-label="Live Link">
                                    <?php if (!empty($tp->live_url)): ?>
                                        <a href="<?= htmlspecialchars($tp->live_url) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-default" style="border-radius: 4px;" title="Open live URL">
                                            <i class="fa fa-external-link text-primary"></i> <span class="desktop-only">View</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Views"><strong><?= number_format($tp->views) ?></strong></td>
                                <td data-label="ER" class="text-success font-weight-bold">
                                    <?= is_null($tp->engagement_rate) ? '-' : number_format($tp->engagement_rate * 100, 2) . '%' ?>
                                </td>
                                <td data-label="CPV">
                                    <?= empty($tp->cpv) ? '-' : 'Rp ' . number_format($tp->cpv, 0, ',', '.') ?>
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

    <!-- Section 3: Network Composition (Stacked Full Width) -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success" style="border-radius: 10px; border-top-width: 3px; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                <div class="box-header with-border" style="padding: 14px 16px;">
                    <h3 class="box-title" style="font-weight: 700; font-size: 15px; color: #0F172A;">
                        <i class="fa fa-pie-chart text-success" style="margin-right: 6px;"></i> Network Composition
                    </h3>
                </div>
                <div class="box-body" style="padding: 20px 16px;">
                    <div class="row">
                        <div class="col-md-5 text-center">
                            <h4 style="font-size: 13px; font-weight: 700; color: #334155; margin-top: 0; margin-bottom: 14px;">KOLs by Tier</h4>
                            <div style="height: 220px; position: relative; max-width: 320px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                <?php if (empty($kols_by_tier)): ?>
                                    <p class="text-muted small" style="margin: 0;">No active KOL tier data</p>
                                <?php else: ?>
                                    <canvas id="tierChart"></canvas>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h4 style="font-size: 13px; font-weight: 700; color: #334155; margin-top: 0; margin-bottom: 14px;">Platform Distribution</h4>
                            <div style="padding: 4px 0;">
                                <?php 
                                $max_plat = 1;
                                if (!empty($platform_distribution)) {
                                    foreach ($platform_distribution as $pd) {
                                        if ($pd->total > $max_plat) $max_plat = $pd->total;
                                    }
                                }
                                ?>
                                <?php if (empty($platform_distribution)): ?>
                                <p class="text-center text-muted" style="padding: 20px 0;">No platform data found.</p>
                                <?php else: ?>
                                    <?php foreach ($platform_distribution as $pd): ?>
                                    <div style="margin-bottom: 12px;">
                                        <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:600; margin-bottom:4px; color:#334155;">
                                            <span><?= htmlspecialchars($pd->platform_name) ?></span>
                                            <span class="text-muted"><?= number_format($pd->total) ?> accounts</span>
                                        </div>
                                        <div class="progress" style="height: 8px; margin-bottom: 0; background: #F1F5F9; border-radius: 4px; box-shadow: none;">
                                            <div class="progress-bar progress-bar-primary" role="progressbar" style="width: <?= round(($pd->total / $max_plat) * 100) ?>%; border-radius: 4px; background: #4F46E5;"></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 4: Stale KOL Profiles (Stacked Full Width) -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning" style="border-radius: 10px; border-top-width: 3px; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                <div class="box-header with-border" style="padding: 14px 16px;">
                    <h3 class="box-title" style="font-weight: 700; font-size: 15px; color: #0F172A;">
                        <i class="fa fa-refresh text-warning" style="margin-right: 6px;"></i> Stale KOL Profiles (Needs Sync)
                    </h3>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover dash-card-table dash-stale-table">
                        <thead>
                        <tr>
                            <th>KOL</th>
                            <th>Platform</th>
                            <th>Last Synced</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($stale_kols)): ?>
                        <tr><td colspan="4" class="text-center text-success" style="padding: 24px;"><i class="fa fa-check-circle"></i> All KOLs are up to date!</td></tr>
                        <?php else: ?>
                            <?php foreach($stale_kols as $stale): ?>
                            <tr>
                                <td data-label="KOL">
                                    <div style="display:flex; align-items:center;">
                                        <?php 
                                        $avatar = base_url('asset/img/avatar.png');
                                        if (!empty($stale->avatar_url)) {
                                            if (preg_match('/^https?:\/\//i', $stale->avatar_url)) {
                                                $avatar = base_url('avatar_proxy.php?url=' . urlencode($stale->avatar_url));
                                            } else {
                                                $avatar = base_url('uploads/kols/' . $stale->avatar_url);
                                            }
                                        }
                                        ?>
                                        <img src="<?= $avatar ?>" style="width:28px;height:28px;border-radius:50%;margin-right:8px;object-fit:cover;" onerror="this.src='<?= base_url('asset/img/avatar.png') ?>'" referrerpolicy="no-referrer">
                                        <?= htmlspecialchars($stale->kol_name) ?>
                                    </div>
                                </td>
                                <td data-label="Platform"><?= htmlspecialchars($stale->platform_name) ?></td>
                                <td data-label="Last Synced">
                                    <span class="label label-danger">
                                        <?= $stale->last_synced_at ? date('d M Y', strtotime($stale->last_synced_at)) : 'Never' ?>
                                    </span>
                                </td>
                                <td data-label="Action">
                                    <a href="<?= site_url('administrator/kols') ?>" class="btn btn-xs btn-primary"><span class="desktop-only">Go to KOL List</span><span class="mobile-only"><i class="fa fa-refresh"></i> Sync</span></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(!empty($stale_kols) && count($stale_kols) > 5): ?>
                <div class="box-footer clearfix" id="stale-pagination-box" style="background:#fff; border-top:1px solid #F1F5F9; padding: 10px 15px;">
                    <div class="pull-left text-muted small stale-page-info" style="line-height: 30px;">
                        Showing <span id="stale-start">1</span>–<span id="stale-end">5</span> of <span id="stale-total"><?= count($stale_kols) ?></span> profiles
                    </div>
                    <ul class="pagination pagination-sm no-margin pull-right" id="stale-pagination"></ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function initDashboardCharts() {
    const tierData = <?= json_encode($kols_by_tier) ?>;
    const canvas = document.getElementById('tierChart');
    
    if (canvas && tierData && tierData.length > 0) {
        const labels = tierData.map(d => d.tier_name || 'Unassigned');
        const counts = tierData.map(d => d.total);
        const bgColors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

        const ctx = canvas.getContext('2d');
        if (window.tierChartInstance) {
            window.tierChartInstance.destroy();
        }
        window.tierChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: bgColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    }

    // Stale KOL Profiles Pagination (5 items per page)
    const staleRows = document.querySelectorAll('.dash-stale-table tbody tr');
    const itemsPerPage = 5;
    const totalItems = staleRows.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    function renderStalePage(page) {
        if (totalPages <= 1) return;
        const start = (page - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, totalItems);

        staleRows.forEach((row, idx) => {
            if (idx >= start && idx < end) {
                row.classList.remove('stale-row-hidden');
            } else {
                row.classList.add('stale-row-hidden');
            }
        });

        const startEl = document.getElementById('stale-start');
        const endEl = document.getElementById('stale-end');
        if (startEl) startEl.textContent = start + 1;
        if (endEl) endEl.textContent = end;

        const pagContainer = document.getElementById('stale-pagination');
        if (!pagContainer) return;
        pagContainer.innerHTML = '';

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = page === 1 ? 'disabled' : '';
        prevLi.innerHTML = '<a href="javascript:void(0)" aria-label="Previous">&laquo;</a>';
        if (page > 1) {
            prevLi.addEventListener('click', function(e) {
                e.preventDefault();
                renderStalePage(page - 1);
            });
        }
        pagContainer.appendChild(prevLi);

        // Numeric buttons
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = i === page ? 'active' : '';
            li.innerHTML = '<a href="javascript:void(0)">' + i + '</a>';
            (function(p) {
                li.addEventListener('click', function(e) {
                    e.preventDefault();
                    renderStalePage(p);
                });
            })(i);
            pagContainer.appendChild(li);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = page === totalPages ? 'disabled' : '';
        nextLi.innerHTML = '<a href="javascript:void(0)" aria-label="Next">&raquo;</a>';
        if (page < totalPages) {
            nextLi.addEventListener('click', function(e) {
                e.preventDefault();
                renderStalePage(page + 1);
            });
        }
        pagContainer.appendChild(nextLi);
    }

    if (totalItems > itemsPerPage) {
        renderStalePage(1);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
} else {
    initDashboardCharts();
}
</script>
