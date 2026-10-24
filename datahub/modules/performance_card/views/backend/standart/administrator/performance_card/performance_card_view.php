<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
/* Force Browser Vertical Scrolling across AdminLTE */
html, body {
    height: auto !important;
    min-height: 100% !important;
    overflow-y: auto !important;
}

body.fixed,
body.fixed .wrapper {
    overflow-y: visible !important;
    height: auto !important;
    min-height: 100% !important;
}

.content-wrapper {
    height: auto !important;
    min-height: 100% !important;
    overflow: visible !important;
}

<?php if ($this->input->get('is_iframe') == 'true'): ?>
/* Hide sidebar, top header, footer, and breadcrumb header for iframe embedding */
.main-header, .main-sidebar, .content-header, .main-footer { display: none !important; }
.content-wrapper { margin-left: 0 !important; padding-top: 0 !important; }
<?php endif; ?>

/* Print to PDF / Paper Styling */
@media print {
    .main-header, .main-sidebar, .content-header, .main-footer, .pmc-actions, .breadcrumb, #btn_scrape_now {
        display: none !important;
    }
    .content-wrapper, .wrapper {
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }
    body {
        background: #ffffff !important;
    }
    .performance-master-card {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
    .pmc-header {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .pmc-platform-card, .pmc-rate-card {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
}

/* Performance Master Card Styles */
.performance-master-card {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 16px;
    overflow: hidden;
}

.pmc-header {
    background: #1e293b;
    color: #ffffff;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.pmc-profile {
    display: flex;
    align-items: center;
    gap: 16px;
}

.pmc-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.15);
    background: #334155;
    flex-shrink: 0;
}

.pmc-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
}

.pmc-meta {
    font-size: 12px;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 4px;
}

.pmc-badge-tier {
    background: #475569;
    color: #f8fafc;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    text-transform: uppercase;
}

.pmc-badge-cat {
    background: rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 4px;
}

.pmc-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

/* Button Hierarchy */
.pmc-actions .btn-primary { background: #2563eb; border-color: #2563eb; font-weight: 600; padding: 5px 12px; font-size: 12px; border-radius: 4px; }
.pmc-actions .btn-primary:hover { background: #1d4ed8; }
.pmc-actions .btn-outline { background: transparent; border: 1px solid rgba(255,255,255,0.3); color: #f8fafc; padding: 5px 12px; font-size: 12px; border-radius: 4px; }
.pmc-actions .btn-outline:hover { background: rgba(255,255,255,0.1); color: #fff; }
.pmc-actions .btn-ghost { background: transparent; border: none; color: #cbd5e1; padding: 5px 12px; font-size: 12px; }
.pmc-actions .btn-ghost:hover { color: #fff; background: rgba(255,255,255,0.05); }

.pmc-body {
    padding: 24px;
}

.pmc-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 8px;
}

.pmc-section-title i {
    color: #94a3b8;
}

/* Sub-card platform grid */
.pmc-platforms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.pmc-platform-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 16px;
}

.pmc-platform-card.inactive {
    background: #f8fafc;
    border-color: #f1f5f9;
    opacity: 0.6;
    filter: grayscale(100%);
}

.pmc-p-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 12px;
}

.pmc-p-name {
    font-weight: 600;
    font-size: 13px;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.pmc-p-user {
    font-size: 11px;
    color: #64748b;
}

.pmc-p-stats {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.pmc-p-stat-row {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
}

.pmc-p-stat-row .label-text {
    color: #64748b;
}

.pmc-p-stat-row .value-text {
    font-weight: 600;
    color: #0f172a;
    font-variant-numeric: tabular-nums;
}

.pmc-p-stat-row .value-text.er {
    color: #059669;
}

.pmc-p-sync {
    margin-top: 12px;
    font-size: 10px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Rate Card section */
.pmc-rates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.pmc-rate-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 16px;
}

.pmc-r-head {
    padding-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 10px;
}

.pmc-r-name {
    font-weight: 600;
    font-size: 13px;
    color: #0f172a;
}

.pmc-r-user {
    font-size: 11px;
    color: #64748b;
}

.pmc-r-row {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    padding: 3px 0;
}

.pmc-r-row .label-text {
    color: #64748b;
}

.pmc-r-row .value-text {
    font-weight: 600;
    color: #0f172a;
    font-variant-numeric: tabular-nums;
}

.pmc-r-row .value-text.empty {
    color: #94a3b8;
    font-weight: 400;
}

.pmc-archive-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 12px 16px;
    margin-bottom: 16px;
    font-size: 12px;
    color: #475569;
}

/* Chart Container inside Card */
.pmc-chart-wrapper {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 16px;
    position: relative;
    height: 260px;
    width: 100%;
}

.page-footer-meta {
    font-size: 11px;
    color: #94a3b8;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 12px;
    margin-bottom: 20px;
}
</style>

<!-- Content Header -->
<section class="content-header">
    <h1>
        Performance Card
        <small><?= _ent($kol->name); ?></small>
    </h1>
    <ol class="breadcrumb">
        <?php if (is_kol_user() && !$this->aauth->is_admin()): ?>
        <li><a href="<?= site_url('administrator/performance_card/my_card'); ?>"><i class="fa fa-line-chart"></i> Performance Card</a></li>
        <li class="active"><?= _ent($kol->name); ?></li>
        <?php else: ?>
        <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/kols'); ?>">KOLs</a></li>
        <li><a href="<?= site_url('administrator/kols/view/' . $kol->id); ?>">Detail</a></li>
        <li class="active">Performance Card</li>
        <?php endif; ?>
    </ol>
</section>

<!-- Main content -->
<section class="content" style="padding-bottom: 30px;">
    <div class="row">
        <div class="col-md-12">

            <!-- === UNIFIED MASTER PERFORMANCE CARD === -->
            <div class="performance-master-card">
                
                <!-- Card Header -->
                <div class="pmc-header">
                    <div class="pmc-profile">
                        <?php 
                        $default_avatar = BASE_ASSET . 'admin-lte/dist/img/avatar.png';
                        if (isset($kol->gender) && strtoupper($kol->gender) === 'FEMALE') {
                            $default_avatar = BASE_ASSET . 'admin-lte/dist/img/avatar2.png';
                        } elseif (isset($kol->gender) && strtoupper($kol->gender) === 'MALE') {
                            $default_avatar = BASE_ASSET . 'admin-lte/dist/img/avatar5.png';
                        }

                        $avatar = $default_avatar;
                        if (!empty($kol->avatar_url)) {
                            if (preg_match('/^https?:\/\//i', $kol->avatar_url)) {
                                $avatar = base_url('avatar_proxy.php?url=' . urlencode($kol->avatar_url));
                            } elseif (is_file(FCPATH . 'uploads/kols/' . $kol->avatar_url)) {
                                $avatar = base_url('uploads/kols/' . $kol->avatar_url);
                            }
                        }
                        if ($avatar === $default_avatar && !empty($kol->profile_url)) {
                            if (preg_match('/^https?:\/\//i', $kol->profile_url)) {
                                $avatar = base_url('avatar_proxy.php?url=' . urlencode($kol->profile_url));
                            } elseif (is_file(FCPATH . 'uploads/kols/' . $kol->profile_url)) {
                                $avatar = base_url('uploads/kols/' . $kol->profile_url);
                            }
                        }
                        ?>
                        <img class="pmc-avatar" src="<?= $avatar; ?>" alt="<?= _ent($kol->name); ?>" onerror="this.src='<?= $default_avatar; ?>'" referrerpolicy="no-referrer">
                        <div>
                            <h2 class="pmc-title">
                                <?= _ent($kol->name); ?>
                                <?php if (!empty($kol->tier_name)): ?>
                                    <span class="pmc-badge-tier"><?= _ent($kol->tier_name); ?></span>
                                <?php endif; ?>
                            </h2>
                            <div class="pmc-meta">
                                <?php
                                $loc_parts = array_filter([$kol->city ?? '', $kol->province ?? '']);
                                if (!empty($loc_parts)):
                                ?>
                                    <span><i class="fa fa-map-marker"></i> <?= _ent(implode(', ', $loc_parts)); ?></span>
                                <?php endif; ?>
                                
                                <?php if(!empty($kol->categories)): ?>
                                    <span style="opacity: 0.5;">|</span>
                                    <?php foreach ($kol->categories as $cat): ?>
                                        <span class="pmc-badge-cat"><?= _ent($cat); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="pmc-actions">
                        <?php if ($this->input->get('is_iframe') != 'true' && (!is_kol_user() || $this->aauth->is_admin())): ?>
                        <a class="btn btn-ghost" href="<?= site_url('administrator/kols/view/' . $kol->id); ?>">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <?php endif; ?>

                        <?php if ($this->input->get('is_iframe') != 'true'): ?>
                        <a class="btn btn-outline" href="<?= site_url('administrator/open_campaigns'); ?>">
                            <i class="fa fa-bullhorn"></i> Browse Campaigns
                        </a>
                        <?php endif; ?>

                        <?php 
                        $export_url = site_url('administrator/performance_card/export_pdf/' . $kol->id);
                        if ($this->input->get('is_iframe') == 'true') {
                            $export_url .= '?t=' . $this->input->get('t') . '&sig=' . $this->input->get('sig');
                        }
                        ?>
                        <a class="btn btn-outline" href="<?= $export_url; ?>" target="_blank" title="Download dokumen PDF">
                            <i class="fa fa-file-pdf-o"></i> Export PDF
                        </a>

                        <button type="button" class="btn btn-outline" onclick="window.print();" title="Print atau Save as PDF langsung via Browser">
                            <i class="fa fa-print"></i> Print
                        </button>

                        <?php if ($this->input->get('is_iframe') != 'true' && (!is_kol_user() || $this->aauth->is_admin())): ?>
                        <button type="button" class="btn btn-primary" id="btn_scrape_now" data-kol-id="<?= $kol->id; ?>">
                            <i class="fa fa-refresh"></i> Scrape Now
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="pmc-body">
                    
                    <!-- Section 1: Platform Overview -->
                    <div class="pmc-section-title">
                        <i class="fa fa-tachometer"></i> Platform Performance Metrics
                    </div>

                    <?php if (!empty($platform_stats)): ?>
                    <div class="pmc-platforms-grid">
                        <?php foreach ($platform_stats as $ps): ?>
                            <?php
                            $code_lower = strtolower($ps->platform_code);
                            $icon = 'fa-globe';
                            $icon_color = '#3b82f6';
                            if ($code_lower === 'tiktok') { $icon = 'fa-music'; $icon_color = '#000000'; }
                            elseif ($code_lower === 'instagram') { $icon = 'fa-instagram'; $icon_color = '#e1306c'; }
                            elseif ($code_lower === 'youtube') { $icon = 'fa-youtube-play'; $icon_color = '#ef4444'; }
                            elseif ($code_lower === 'twitter' || $code_lower === 'x') { $icon = 'fa-twitter'; $icon_color = '#0ea5e9'; }
                            elseif ($code_lower === 'facebook') { $icon = 'fa-facebook'; $icon_color = '#2563eb'; }
                            
                            $is_inactive = empty($ps->last_synced_at) ? 'inactive' : '';
                            ?>
                            <div class="pmc-platform-card <?= $is_inactive ?>">
                                <div class="pmc-p-head">
                                    <span class="pmc-p-name">
                                        <?php if ($code_lower === 'tiktok'): ?><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 14px; height: 16px; fill: <?= $icon_color; ?>; vertical-align: -1px; margin-right: 4px;"><path d="M448 209.91a210.06 210.06 0 0 1-122.77-39.25V349.38A162.55 162.55 0 1 1 185 188.31V278.2a74.62 74.62 0 1 0 52.23 71.18V0l88 0a121.18 121.18 0 0 0 1.86 22.17h0A122.18 122.18 0 0 0 381 102.39a121.43 121.43 0 0 0 67 20.14z"/></svg><?php else: ?><i class="fa <?= $icon; ?>" style="color: <?= $icon_color; ?>; font-size: 16px;"></i><?php endif; ?>
                                        <?= _ent($ps->platform_name); ?>
                                    </span>
                                     <?php $ps_url = get_kol_profile_url(isset($ps->profile_url) ? $ps->profile_url : '', $ps->username, isset($ps->code) ? $ps->code : '', isset($ps->platform_name) ? $ps->platform_name : ''); ?>
                                     <span class="pmc-p-user"><a href="<?= _ent($ps_url); ?>" target="_blank" style="color: inherit; text-decoration: underline;">@<?= _ent($ps->username); ?></a></span>
                                </div>

                                <div class="pmc-p-stats">
                                    <div class="pmc-p-stat-row">
                                        <span class="label-text">Followers</span>
                                        <span class="value-text"><?= number_format((int)$ps->followers); ?></span>
                                    </div>
                                    <div class="pmc-p-stat-row">
                                        <span class="label-text">Avg Views (30D)</span>
                                        <span class="value-text"><?= number_format((float)$ps->avg_views, 0); ?></span>
                                    </div>
                                    <div class="pmc-p-stat-row">
                                        <span class="label-text">Avg ER (30D)</span>
                                        <span class="value-text <?= $is_inactive ? '' : 'er' ?>"><?= number_format((float)$ps->avg_er * 100, 2); ?>%</span>
                                    </div>
                                </div>

                                <div class="pmc-p-sync">
                                    <?= !empty($ps->last_synced_at) ? '<i class="fa fa-check text-green"></i> Synced ' . date('d M Y', strtotime($ps->last_synced_at)) : '<i class="fa fa-clock-o text-muted"></i> Never synced'; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                        <div class="callout callout-info" style="margin-bottom: 24px;">
                            <p><i class="fa fa-info-circle"></i> No platform accounts linked to this KOL yet.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Section 2: Rate Card -->
                    <div class="pmc-section-title">
                        <i class="fa fa-money"></i> Rate Card Layanan
                    </div>

                    <?php if (!empty($kol->rate_card_file)): ?>
                    <div class="pmc-archive-bar">
                        <span><i class="fa fa-file-pdf-o"></i> <strong>Arsip dokumen rate card</strong> (PDF lama tetap tersimpan sebagai lampiran).</span>
                        <a href="<?= base_url('uploads/kols/' . trim($kol->rate_card_file)); ?>" target="_blank" class="btn btn-xs btn-default">Lihat Dokumen PDF</a>
                    </div>
                    <?php endif; ?>

                    <?php
                    $rm_types = isset($rate_matrix['types']) ? $rate_matrix['types'] : [];
                    $rm_plats = isset($rate_matrix['platforms']) ? $rate_matrix['platforms'] : [];
                    $rm_rates = isset($rate_matrix['rates']) ? $rate_matrix['rates'] : [];
                    ?>
                    <?php if (!empty($rm_plats) && !empty($rm_types)): ?>
                    <div class="pmc-rates-grid">
                        <?php foreach ($rm_plats as $rp): ?>
                        <div class="pmc-rate-card">
                            <div class="pmc-r-head">
                                <div class="pmc-r-name"><?= _ent($rp->platform_name ?? 'Rate'); ?></div>
                                <?php if (!empty($rp->username)): ?>
                                <div class="pmc-r-user">@<?= _ent($rp->username); ?><?php if (!empty($rp->followers)): ?> · <?= number_format((int)$rp->followers); ?> followers<?php endif; ?></div>
                                <?php endif; ?>
                            </div>
                            <?php foreach ($rm_types as $rtype => $rmeta): ?>
                            <?php $rv = isset($rm_rates[$rp->kol_platform_id][$rtype]) ? (float)$rm_rates[$rp->kol_platform_id][$rtype] : null; ?>
                            <div class="pmc-r-row">
                                <span class="label-text"><?= _ent($rtype); ?></span>
                                <?php if ($rv !== null && $rv > 0): ?>
                                <span class="value-text">Rp <?= number_format($rv, 0, ',', '.'); ?></span>
                                <?php else: ?>
                                <span class="value-text empty">-</span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                        <div class="callout callout-info" style="margin-bottom: 24px;">
                            <p><i class="fa fa-info-circle"></i> KOL belum mengisi rate card.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Section 3: Historical Growth Trend -->
                    <div class="pmc-section-title">
                        <i class="fa fa-line-chart"></i> 6-Month Growth Trend (Avg Views)
                    </div>

                    <?php if (!empty($trend_data)): ?>
                        <div class="pmc-chart-wrapper">
                            <canvas id="trendChart"></canvas>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted" style="padding: 30px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px;">
                            <i class="fa fa-bar-chart" style="font-size: 24px; color: #cbd5e1; margin-bottom: 8px;"></i>
                            <p style="margin: 0; font-size: 12px;">No snapshot history available yet. Run a scrape to track trends.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
            <!-- === END UNIFIED MASTER PERFORMANCE CARD === -->

            <!-- Footer Metadata -->
            <div class="page-footer-meta">
                <span><i class="fa fa-database"></i> Source: Apify Platform Scraper</span>
                <span>Snapshot ID: KOL-<?= _ent($kol->id); ?> &bull; <?= date('d M Y'); ?></span>
            </div>

        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    <?php if (!empty($trend_data)): ?>
    var trendData = <?= json_encode($trend_data); ?>;
    var platformColors = {
        'TIKTOK':    { border: '#111827', bg: 'rgba(17, 24, 39, 0.05)' },
        'INSTAGRAM': { border: '#E1306C', bg: 'rgba(225, 48, 108, 0.05)' },
        'YOUTUBE':   { border: '#FF0000', bg: 'rgba(255, 0, 0, 0.05)' },
        'TWITTER':   { border: '#1DA1F2', bg: 'rgba(29, 161, 242, 0.05)' },
        'X':         { border: '#1DA1F2', bg: 'rgba(29, 161, 242, 0.05)' },
        'FACEBOOK':  { border: '#1877F2', bg: 'rgba(24, 119, 242, 0.05)' }
    };

    var allMonths = new Set();
    Object.keys(trendData).forEach(function(code) {
        trendData[code].data.forEach(function(d) { allMonths.add(d.month); });
    });
    var labels = Array.from(allMonths).sort();

    var datasets = [];
    Object.keys(trendData).forEach(function(code) {
        var platform = trendData[code];
        var monthMap = {};
        platform.data.forEach(function(d) { monthMap[d.month] = d.avg_views; });

        var colors = platformColors[code] || { border: '#3b82f6', bg: 'rgba(59, 130, 246, 0.05)' };

        datasets.push({
            label: platform.name + ' Views',
            data: labels.map(function(m) { return monthMap[m] || null; }),
            borderColor: colors.border,
            backgroundColor: colors.bg,
            borderWidth: 2,
            pointRadius: 3, // Thinner points
            pointHoverRadius: 5,
            pointBackgroundColor: colors.border,
            tension: 0.4, // Smoother lines
            fill: true,
            spanGaps: true
        });
    });

    var formattedLabels = labels.map(function(m) {
        var parts = m.split('-');
        var monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return monthNames[parseInt(parts[1]) - 1] + ' ' + parts[0];
    });

    var ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: formattedLabels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 15,
                        font: { size: 11, family: 'sans-serif' }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        font: { size: 11 },
                        callback: function(val) {
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                            if (val >= 1000) return (val / 1000).toFixed(0) + 'K';
                            return val;
                        }
                    }
                }
            }
        }
    });
    <?php endif; ?>

    // Fix layout height in AdminLTE
    setTimeout(function() {
        if ($.AdminLTE && $.AdminLTE.layout) {
            $.AdminLTE.layout.fix();
        }
    }, 200);

    <?php if (!is_kol_user() || $this->aauth->is_admin()): ?>
    // Scraping action
    $('#btn_scrape_now').on('click', function() {
        var btn = $(this);
        var kolId = btn.data('kol-id');
        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Scraping...');
        toastr.info('Scraping profile data from Apify...');

        var postData = {};
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/scraping/scrape_single/' + kolId,
            type: 'POST',
            data: postData,
            dataType: 'json',
            timeout: 300000,
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Scrape Now');
                if (res.success) {
                    toastr.success(res.message);
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    toastr.warning(res.message || 'Some platforms failed');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Scrape Now');
                toastr.error('Terjadi kesalahan jaringan atau request (HTTP ' + xhr.status + ')');
            }
        });
    });
    <?php endif; ?>

    // Notify parent window of height if inside iframe
    if (window.parent && window.parent !== window) {
        function notifyHeight() {
            var card = document.querySelector('.performance-master-card');
            var height = card ? card.offsetHeight + 100 : (document.body.scrollHeight || 1200);
            window.parent.postMessage({ type: 'setIframeHeight', height: height }, '*');
        }
        notifyHeight();
        window.addEventListener('resize', notifyHeight);
        setTimeout(notifyHeight, 400);
        setTimeout(notifyHeight, 1200);
    }
});
</script>
