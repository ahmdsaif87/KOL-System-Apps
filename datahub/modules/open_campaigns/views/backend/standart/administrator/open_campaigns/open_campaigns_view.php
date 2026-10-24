<?php
if (!function_exists('get_platform_chip_meta')) {
    function get_platform_chip_meta($name) {
        $n = strtolower($name);
        if (strpos($n, 'tiktok') !== false) {
            return ['icon' => 'fa-video-camera', 'bg' => '#000000', 'color' => '#ffffff', 'border' => '#000000'];
        } elseif (strpos($n, 'instagram') !== false) {
            return ['icon' => 'fa-instagram', 'bg' => '#fff0f5', 'color' => '#e1306c', 'border' => '#fbcfe8'];
        } elseif (strpos($n, 'youtube') !== false) {
            return ['icon' => 'fa-youtube-play', 'bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'];
        } else {
            return ['icon' => 'fa-share-alt', 'bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#cbd5e1'];
        }
    }
}
?>
<style>
/* ==========================================================================
   1. GLOBAL CONTAINER & DESKTOP STYLING
   ========================================================================== */
.open-camp-view-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

/* Hero Header Card */
.open-camp-hero {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    margin-bottom: 20px;
    overflow: hidden;
}

.open-camp-hero-top {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 14px;
}

.camp-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155 !important;
    text-decoration: none !important;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.camp-back-link:hover {
    background: #e2e8f0;
    color: #0f172a !important;
}
.camp-back-link:active {
    transform: scale(0.97);
}

.open-camp-hero-title-area {
    flex: 1;
    min-width: 260px;
}

.camp-meta-badges {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 8px;
}

.camp-type-pill {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.camp-type-pill.paid {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
}
.camp-type-pill.barter {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.camp-type-pill.hybrid {
    background: #f5f3ff;
    color: #7c3aed;
    border: 1px solid #ddd6fe;
}

.camp-status-pill {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.camp-hero-title {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
    margin: 0 0 6px 0;
}

.camp-brand-info {
    font-size: 13px;
    color: #64748b;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.open-camp-hero-body {
    padding: 20px 24px;
}

/* 4-Card Metric Grid */
.metric-tile-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
@media (max-width: 991px) {
    .metric-tile-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 575px) {
    .metric-tile-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
}

.metric-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.metric-tile .tile-label {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.metric-tile .tile-val {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
}
.metric-tile .tile-sub {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 4px;
}

/* Section Header Titles */
.detail-section-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Content Box Cards */
.detail-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 18px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    margin-bottom: 20px;
}

/* Product Cards */
.product-card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}
@media (max-width: 575px) {
    .product-card-grid {
        grid-template-columns: 1fr;
    }
}

.product-card-item {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.product-card-title {
    font-weight: 600;
    color: #0f172a;
    font-size: 13px;
    line-height: 1.25;
}
.product-card-meta {
    font-size: 11px;
    color: #64748b;
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.product-card-price {
    font-weight: 700;
    color: #10b981;
    font-size: 13px;
    text-align: right;
    white-space: nowrap;
}

/* Modern Guidelines Spec Sheet with Line Separators */
.guideline-spec-list {
    display: flex;
    flex-direction: column;
}
.guideline-spec-item {
    background: transparent;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    border-radius: 0;
    padding: 12px 0;
}
.guideline-spec-item:first-child {
    padding-top: 2px;
}
.guideline-spec-item:last-child {
    border-bottom: none;
    padding-bottom: 2px;
}
.guideline-spec-header {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.35px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.guideline-spec-val {
    font-size: 13.5px;
    color: #0f172a;
    line-height: 1.5;
}

/* Hashtags */
.hashtag-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 4px;
}
.hashtag-chip {
    background: #eff6ff;
    color: #0284c7;
    border: 1px solid #bfdbfe;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 6px;
}

/* SOW Attachment Chip */
.sow-chip-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.sow-chip-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    color: #334155 !important;
    text-decoration: none !important;
    transition: all 0.15s ease;
}
.sow-chip-item:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a !important;
}

/* Qualification Item List */
.qc-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.qc-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.qc-item.passed {
    border-left: 3.5px solid #10b981;
}
.qc-item.failed {
    border-left: 3.5px solid #ef4444;
}
.qc-icon-wrap {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
    margin-top: 2px;
}
.qc-icon-wrap.pass {
    background: #ecfdf5;
    color: #10b981;
}
.qc-icon-wrap.fail {
    background: #fef2f2;
    color: #ef4444;
}
.qc-content {
    flex-grow: 1;
    min-width: 0;
}
.qc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}
.qc-title {
    font-weight: 700;
    font-size: 13px;
    color: #0f172a;
}
.qc-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2.5px 7px;
    border-radius: 4px;
    text-transform: uppercase;
    flex-shrink: 0;
}
.qc-badge.pass {
    background: #d1fae5;
    color: #065f46;
}
.qc-badge.fail {
    background: #fee2e2;
    color: #991b1b;
}
.qc-details {
    font-size: 12px;
    line-height: 1.45;
    color: #475569;
}

/* Application Card */
.apply-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    margin-bottom: 20px;
}
.apply-header {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
}
.apply-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
}
.apply-body {
    padding: 18px;
}

/* ==========================================================================
   2. NATIVE MOBILE APP NAVIGATION TABS (<= 768px)
   ========================================================================== */
.mobile-segment-tabs {
    display: none;
    align-items: center;
    gap: 6px;
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding: 0 0 10px 0;
    margin-bottom: 14px;
}
.mobile-segment-tabs::-webkit-scrollbar {
    display: none;
}
.mobile-seg-btn {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 7px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.mobile-seg-btn.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
}

@media (max-width: 768px) {
    /* Hide Desktop Breadcrumb */
    .content-header {
        display: none !important;
    }

    section.content {
        padding: 14px 16px 36px 16px !important;
    }

    .open-camp-hero {
        border-radius: 12px;
        margin-bottom: 14px;
    }
    .open-camp-hero-top {
        padding: 14px 16px;
        gap: 10px;
    }
    .camp-hero-title {
        font-size: 18px;
    }
    .open-camp-hero-body {
        padding: 14px 16px;
    }

    /* Show Mobile Tabs */
    .mobile-segment-tabs {
        display: flex;
    }

    /* Mobile Tab Switching Panels */
    .tab-panel-mobile {
        display: none;
    }
    .tab-panel-mobile.active {
        display: block;
        animation: fadeInMobile 0.2s ease-in-out;
    }
    @keyframes fadeInMobile {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Stack on mobile */
    .col-md-7, .col-lg-8, .col-md-5, .col-lg-4 {
        width: 100% !important;
        padding: 0 !important;
    }
}

@media (min-width: 769px) {
    /* Always show all panels side-by-side on desktop */
    .tab-panel-mobile {
        display: block !important;
    }
}
</style>

<section class="content">
    <div class="open-camp-view-container">

        <!-- Top Hero Card -->
        <div class="open-camp-hero">
            <div class="open-camp-hero-top">
                <div class="open-camp-hero-title-area">
                    <div class="camp-meta-badges">
                        <span class="camp-type-pill <?= strtolower($campaign->campaign_type); ?>">
                            <?= _ent($campaign->campaign_type); ?>
                        </span>
                        <span class="camp-status-pill">
                            <i class="fa fa-bullhorn text-success"></i> Open Post
                        </span>
                        <?php if (in_array($campaign->status, ['OPEN_REGISTRATION', 'ACTIVE'])): ?>
                            <span class="camp-status-pill" style="background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                                Buka Pendaftaran
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="camp-hero-title"><?= _ent($campaign->name); ?></h1>

                    <div class="camp-brand-info">
                        <span><i class="fa fa-tag text-muted"></i> <strong><?= !empty($campaign->brand_name) ? _ent($campaign->brand_name) : 'Brand Sponsor'; ?></strong></span>
                        <?php if (!empty($campaign->brand_category)): ?>
                            <span>&bull; <?= _ent($campaign->brand_category); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($campaign->pic_name)): ?>
                            <span>&bull; PIC: <?= _ent($campaign->pic_name); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <a href="<?= site_url('administrator/open_campaigns'); ?>" class="camp-back-link">
                        <i class="fa fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <div class="open-camp-hero-body">
                <!-- 4-Card Metric Grid -->
                <div class="metric-tile-grid">
                    <div class="metric-tile">
                        <div class="tile-label"><i class="fa fa-calendar-check-o text-primary"></i> Periode</div>
                        <div class="tile-val"><?= date('d M', strtotime($campaign->start_date)); ?> - <?= date('d M Y', strtotime($campaign->end_date)); ?></div>
                        <div class="tile-sub"><?= max(0, (int)((strtotime($campaign->end_date) - time()) / 86400)); ?> hari tersisa</div>
                    </div>

                    <div class="metric-tile">
                        <div class="tile-label"><i class="fa fa-clock-o text-danger"></i> Batas Daftar</div>
                        <div class="tile-val" style="color: <?= !empty($campaign->application_deadline) ? '#dc2626' : '#0f172a'; ?>;">
                            <?= !empty($campaign->application_deadline) ? date('d M Y', strtotime($campaign->application_deadline)) : 'Fleksibel'; ?>
                        </div>
                        <div class="tile-sub">
                            <?php if (!empty($campaign->application_deadline)): ?>
                               <?php 
                                  $days_left = ceil((strtotime($campaign->application_deadline) - strtotime(date('Y-m-d'))) / 86400);
                                  echo $days_left >= 0 ? ($days_left . ' hari lagi') : 'Pendaftaran ditutup';
                               ?>
                            <?php else: ?>
                               Tanpa batas waktu
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="metric-tile">
                        <div class="tile-label"><i class="fa fa-users text-success"></i> Kuota Slot</div>
                        <div class="tile-val" style="color: <?= $campaign->is_slots_full ? '#dc2626' : '#16a34a'; ?>;">
                            <?= (int)$campaign->accepted_count; ?> / <?= !empty($campaign->max_slots) ? (int)$campaign->max_slots : '∞'; ?> Terisi
                        </div>
                        <?php if (!empty($campaign->max_slots)): ?>
                            <?php $slot_pct = min(100, round(((int)$campaign->accepted_count / (int)$campaign->max_slots) * 100)); ?>
                            <div class="progress" style="height: 4px; margin: 6px 0 3px 0; background: #e2e8f0; border-radius: 2px; overflow: hidden;">
                               <div class="progress-bar" style="width: <?= $slot_pct; ?>%; background: <?= $campaign->is_slots_full ? '#dc2626' : '#10b981'; ?>;"></div>
                            </div>
                            <div class="tile-sub">
                               <?= max(0, (int)$campaign->max_slots - (int)$campaign->accepted_count); ?> slot tersedia
                            </div>
                        <?php else: ?>
                            <div class="tile-sub" style="margin-top: 6px;">Slot fleksibel</div>
                        <?php endif; ?>
                    </div>

                    <div class="metric-tile">
                        <div class="tile-label"><i class="fa fa-line-chart text-success"></i> Target ER</div>
                        <div class="tile-val" style="color: #059669;">
                            <?= !empty($campaign->target_er) ? number_format((float)$campaign->target_er * 100, 2) . '%' : '-'; ?>
                        </div>
                        <div class="tile-sub">Engagement rate</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Segmented Tabs Navigation (<= 768px) -->
        <div class="mobile-segment-tabs" id="mobile-tabs">
            <span class="mobile-seg-btn active" data-tab="brief">
                <i class="fa fa-file-text-o"></i> Brief & Produk
            </span>
            <span class="mobile-seg-btn" data-tab="qualification">
                <i class="fa fa-check-circle-o"></i> Kualifikasi
                <?php if ($evaluation): ?>
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: <?= $evaluation['is_qualified'] ? '#10b981' : '#f59e0b'; ?>; margin-left: 2px;"></span>
                <?php endif; ?>
            </span>
            <span class="mobile-seg-btn" data-tab="apply">
                <i class="fa fa-paper-plane-o"></i> Pengajuan Kerjasama
                <?php if (isset($evaluation['existing_application']) && !empty($evaluation['existing_application'])): ?>
                    <span class="label label-success" style="font-size: 9px; padding: 1px 4px; border-radius: 3px; margin-left: 4px;">Applied</span>
                <?php endif; ?>
            </span>
        </div>

        <div class="row">

            <!-- Column 1: Brief Details & Products -->
            <div class="col-md-7 col-lg-8 tab-panel-mobile active" id="panel-brief">
                
                <!-- Deskripsi Campaign -->
                <?php if (!empty($campaign->description)): ?>
                    <div class="detail-card">
                        <div class="detail-section-title">
                            <i class="fa fa-align-left text-primary"></i> Deskripsi Campaign
                        </div>
                        <div style="color: #334155; line-height: 1.6; font-size: 13.5px; white-space: pre-line;">
                            <?= _ent($campaign->description); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Produk Yang Digunakan -->
                <?php if (!empty($campaign->products)): ?>
                    <div class="detail-card">
                        <div class="detail-section-title">
                            <i class="fa fa-cube text-primary"></i> Produk Yang Digunakan (<?= count($campaign->products); ?> Produk)
                        </div>
                        <div class="product-card-grid">
                            <?php foreach ($campaign->products as $p): ?>
                                <div class="product-card-item">
                                    <div>
                                        <div class="product-card-title"><?= _ent($p->name); ?></div>
                                        <div class="product-card-meta">
                                            <span class="label label-default" style="font-size: 9px; padding: 2px 5px;"><?= _ent($p->sku ?: 'PROD'); ?></span>
                                            <span><?= _ent($p->category ?: 'General'); ?></span>
                                        </div>
                                    </div>
                                    <div class="product-card-price">
                                        Rp <?= number_format($p->price, 0, ',', '.'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Panduan Konten & Brief (Native Spec Cards) -->
                <div class="detail-card">
                    <div class="detail-section-title">
                        <i class="fa fa-bookmark text-primary"></i> Panduan Konten & Brief
                    </div>
                    
                    <div class="guideline-spec-list">
                        <?php if (!empty($campaign->objective)): ?>
                            <div class="guideline-spec-item">
                                <div class="guideline-spec-header">
                                    <i class="fa fa-bullseye text-danger"></i> Objective Target
                                </div>
                                <div class="guideline-spec-val">
                                    <?= _ent($campaign->objective); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($campaign->target_audience)): ?>
                            <div class="guideline-spec-item">
                                <div class="guideline-spec-header">
                                    <i class="fa fa-users text-primary"></i> Target Audience
                                </div>
                                <div class="guideline-spec-val">
                                    <?= _ent($campaign->target_audience); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($campaign->key_message)): ?>
                            <div class="guideline-spec-item">
                                <div class="guideline-spec-header">
                                    <i class="fa fa-commenting-o text-success"></i> Key Message
                                </div>
                                <div class="guideline-spec-val">
                                    <?= _ent($campaign->key_message); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($campaign->hashtag)): ?>
                            <div class="guideline-spec-item">
                                <div class="guideline-spec-header">
                                    <i class="fa fa-hashtag text-info"></i> Wajib Hashtag
                                </div>
                                <div class="hashtag-chips-wrap">
                                    <?php foreach (explode(' ', $campaign->hashtag) as $tag): ?>
                                        <?php if (!empty(trim($tag))): ?>
                                            <span class="hashtag-chip"><?= _ent($tag); ?></span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SOW Download Area -->
                <?php if (!empty($campaign->sow)): ?>
                    <div class="detail-card">
                        <div class="detail-section-title">
                            <i class="fa fa-file-text-o text-primary"></i> Dokumen Scope of Work (SOW)
                        </div>
                        <div class="sow-chip-list">
                            <?php foreach (explode(',', $campaign->sow) as $sow_file): ?>
                                <a href="<?= base_url('uploads/campaigns/' . trim($sow_file)); ?>" target="_blank" class="sow-chip-item">
                                    <i class="fa fa-download text-primary"></i>
                                    <span><?= _ent(trim($sow_file)); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Column 2: Qualification Evaluation & Application Form -->
            <div class="col-md-5 col-lg-4">
                
                <!-- Tab Panel 2: Qualification Evaluation Checklist -->
                <div class="tab-panel-mobile" id="panel-qualification">

                    <!-- Syarat & Kualifikasi Resmi Campaign -->
                    <div class="apply-card">
                        <div class="apply-header">
                            <div>
                                <h3>Syarat Kualifikasi</h3>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    Kriteria yang dicari oleh Brand
                                </div>
                            </div>
                            <span class="label label-default" style="font-size: 10px; border-radius: 4px;">Kriteria Resmi</span>
                        </div>
                        <div class="apply-body">
                            <div class="guideline-spec-list">
                                <!-- Platform -->
                                <div class="guideline-spec-item">
                                    <div class="guideline-spec-header">
                                        <i class="fa fa-share-alt text-primary"></i> Platform yang Diwajibkan
                                    </div>
                                    <div class="camp-platform-list" style="margin-top: 4px; display: flex; flex-wrap: wrap; gap: 6px;">
                                        <?php if (!empty($campaign->required_platforms)): ?>
                                            <?php foreach ($campaign->required_platforms as $rp): ?>
                                                <?php $pmeta = get_platform_chip_meta($rp->name); ?>
                                                <span class="camp-platform-chip" style="background-color: <?= $pmeta['bg']; ?>; color: <?= $pmeta['color']; ?>; border: 1px solid <?= $pmeta['border']; ?>; font-size: 11px; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                                                    <i class="fa <?= $pmeta['icon']; ?>"></i>
                                                    <span><?= _ent($rp->name); ?></span>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span style="font-size: 13px; color: #64748b;">Terbuka untuk semua platform</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Minimal Followers & ER (2 cols with vertical divider) -->
                                <div class="guideline-spec-item">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                        <div>
                                            <div class="guideline-spec-header">
                                                <i class="fa fa-users text-info"></i> Min. Followers
                                            </div>
                                            <div style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin-top: 2px;">
                                                <?= !empty($campaign->min_followers) ? number_format($campaign->min_followers, 0, ',', '.') : 'Bebas'; ?>
                                            </div>
                                        </div>
                                        <div style="border-left: 1px solid #f1f5f9; padding-left: 14px;">
                                            <div class="guideline-spec-header">
                                                <i class="fa fa-pie-chart text-success"></i> Min. ER
                                            </div>
                                            <div style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin-top: 2px;">
                                                <?= !empty($campaign->min_er) ? number_format((float)$campaign->min_er * 100, 2) . '%' : 'Bebas'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tier -->
                                <?php if (!empty($campaign->allowed_tiers)): ?>
                                    <div class="guideline-spec-item">
                                        <div class="guideline-spec-header">
                                            <i class="fa fa-star text-warning"></i> Tier KOL yang Memenuhi Syarat
                                        </div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px;">
                                            <?php foreach ($campaign->allowed_tiers as $t): ?>
                                                <span class="label label-info" style="font-size: 11px; padding: 3px 8px; border-radius: 4px;"><?= _ent($t->name); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Kategori -->
                                <?php if (!empty($campaign->allowed_categories)): ?>
                                    <div class="guideline-spec-item">
                                        <div class="guideline-spec-header">
                                            <i class="fa fa-tags text-primary"></i> Kategori KOL
                                        </div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px;">
                                            <?php foreach ($campaign->allowed_categories as $c): ?>
                                                <span class="label label-default" style="font-size: 11px; padding: 3px 8px; border-radius: 4px; background: #e2e8f0; color: #334155;"><?= _ent($c->name); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Kompensasi / Budget -->
                                <?php if (!empty($campaign->budget) && (float)$campaign->budget > 0): ?>
                                    <div class="guideline-spec-item">
                                        <div class="guideline-spec-header">
                                            <i class="fa fa-money text-success"></i> Alokasi Budget Campaign
                                        </div>
                                        <div style="font-size: 14px; font-weight: 700; color: #10b981; margin-top: 2px;">
                                            Rp <?= number_format($campaign->budget, 0, ',', '.'); ?>
                                            <span style="font-size: 11px; font-weight: 500; color: #64748b;">(<?= _ent($campaign->campaign_type); ?>)</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluasi Personal Akun (Jika Akun KOL) -->
                    <?php if ($evaluation): ?>
                        <?php 
                            $passed_count = 0;
                            $total_criteria = count($evaluation['criteria']);
                            foreach ($evaluation['criteria'] as $c) {
                                if (!empty($c['passed'])) $passed_count++;
                            }
                            $pct_qualified = $total_criteria > 0 ? round(($passed_count / $total_criteria) * 100) : 0;
                        ?>
                        <div class="apply-card" style="border-top: 3.5px solid <?= $evaluation['is_qualified'] ? '#10b981' : '#f59e0b'; ?>;">
                            <div class="apply-header">
                                <div>
                                    <h3>Evaluasi Profil Anda</h3>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                        Kecocokan metrik akun Anda
                                    </div>
                                </div>
                                <div>
                                    <span class="badge <?= $evaluation['is_qualified'] ? 'bg-green' : 'bg-yellow'; ?>" style="font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                        <?= $evaluation['summary']; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="apply-body">
                                <!-- Progress Bar -->
                                <div style="margin-bottom: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 11.5px; color: #64748b; margin-bottom: 6px;">
                                        <span>Kesesuaian Syarat:</span>
                                        <strong style="color: <?= $pct_qualified >= 100 ? '#059669' : ($pct_qualified >= 50 ? '#d97706' : '#dc2626'); ?>;">
                                            <?= $passed_count; ?> / <?= $total_criteria; ?> (<?= $pct_qualified; ?>%)
                                        </strong>
                                    </div>
                                    <div class="progress" style="height: 5px; margin-bottom: 0; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                                        <div class="progress-bar" style="width: <?= $pct_qualified; ?>%; background: <?= $pct_qualified >= 100 ? '#10b981' : ($pct_qualified >= 50 ? '#f59e0b' : '#ef4444'); ?>;"></div>
                                    </div>
                                </div>

                                <?php if ($evaluation['is_qualified']): ?>
                                    <div style="padding: 10px 12px; font-size: 12px; margin-bottom: 12px; border-radius: 8px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; display: flex; align-items: center; gap: 8px;">
                                        <i class="fa fa-check-circle text-success" style="font-size: 14px;"></i>
                                        <span><strong>Lulus Kualifikasi:</strong> Profil Anda memenuhi syarat campaign ini.</span>
                                    </div>
                                <?php else: ?>
                                    <div style="padding: 10px 12px; font-size: 12px; margin-bottom: 12px; border-radius: 8px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; display: flex; align-items: center; gap: 8px;">
                                        <i class="fa fa-info-circle text-warning" style="font-size: 14px;"></i>
                                        <span><strong>Catatan:</strong> Beberapa kriteria belum sesuai, namun Anda tetap dapat mengirimkan pitch.</span>
                                    </div>
                                <?php endif; ?>

                                <!-- Itemized Checklist -->
                                <div class="qc-list">
                                    <?php foreach ($evaluation['criteria'] as $crit): ?>
                                        <div class="qc-item <?= $crit['passed'] ? 'passed' : 'failed'; ?>">
                                            <div class="qc-icon-wrap <?= $crit['passed'] ? 'pass' : 'fail'; ?>">
                                                <i class="fa <?= $crit['passed'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                            </div>

                                            <div class="qc-content">
                                                <div class="qc-header">
                                                    <span class="qc-title"><?= _ent($crit['label']); ?></span>
                                                    <span class="qc-badge <?= $crit['passed'] ? 'pass' : 'fail'; ?>">
                                                        <?= $crit['passed'] ? 'Lulus' : 'Belum Sesuai'; ?>
                                                    </span>
                                                </div>

                                                <div class="qc-details">
                                                    <div>Syarat: <strong><?= _ent($crit['required']); ?></strong></div>
                                                    <div>
                                                        Profil Anda: 
                                                        <span style="font-weight: 600; color: <?= $crit['passed'] ? '#059669' : '#dc2626'; ?>;">
                                                            <?= _ent($crit['actual']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Informational Banner for Administrator View -->
                        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 14px; text-align: center; margin-bottom: 20px;">
                            <i class="fa fa-id-badge text-muted" style="font-size: 24px; margin-bottom: 6px;"></i>
                            <div style="font-weight: 600; font-size: 12.5px; color: #1e293b; margin-bottom: 3px;">Pemeriksaan Kualifikasi Akun</div>
                            <div style="font-size: 11.5px; color: #64748b; line-height: 1.4;">
                                Anda saat ini masuk sebagai Administrator. Evaluasi kesesuaian syarat di atas akan otomatis dicocokkan dengan metrik profil masing-masing KOL saat mereka mengakses halaman ini.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Panel 3: Application Action Card -->
                <div class="tab-panel-mobile" id="panel-apply">
                    <div class="apply-card">
                        <div class="apply-header">
                            <h3>Pengajuan Kerjasama</h3>
                            <span class="label label-default" style="font-size: 10px; border-radius: 4px;">Portal KOL</span>
                        </div>

                        <div class="apply-body">
                            <?php if (isset($evaluation['existing_application']) && !empty($evaluation['existing_application'])): ?>
                                <!-- State: Already Applied -->
                                <?php 
                                    $ext_app = $evaluation['existing_application'];
                                    $badge_cls = ($ext_app['status'] === 'ACCEPTED') ? 'label-success' : (($ext_app['status'] === 'REJECTED') ? 'label-danger' : 'label-warning');
                                ?>
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 14px; text-align: center;">
                                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px; margin: 0 auto 10px auto;">
                                        <i class="fa fa-check-circle"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">Anda Sudah Mendaftar</h4>
                                    <p style="color: #64748b; font-size: 12px; margin-bottom: 14px;">
                                        Diajukan pada <strong><?= date('d M Y H:i', strtotime($ext_app['applied_at'])); ?></strong>
                                    </p>

                                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 14px; text-align: left; font-size: 12px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                            <span class="text-muted">Status:</span>
                                            <span class="label <?= $badge_cls; ?>" style="font-size: 11px;"><?= $ext_app['status']; ?></span>
                                        </div>
                                        <?php if (!empty($ext_app['proposed_rate']) && (float)$ext_app['proposed_rate'] > 0): ?>
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                                <span class="text-muted">Tarif Diajukan:</span>
                                                <strong style="color: #10b981;">Rp <?= number_format((float)$ext_app['proposed_rate'], 0, ',', '.'); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($ext_app['pitch_message'])): ?>
                                            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e2e8f0; color: #475569; font-style: italic;">
                                                "<?= _ent($ext_app['pitch_message']); ?>"
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($ext_app['status'] === 'REJECTED' && !empty($ext_app['rejection_reason'])): ?>
                                        <div class="alert alert-danger" style="text-align: left; font-size: 12px; padding: 8px 12px; border-radius: 6px; margin-bottom: 14px;">
                                            <strong>Alasan Penolakan:</strong><br><?= _ent($ext_app['rejection_reason']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <a href="<?= site_url('administrator/open_campaigns/my_applications'); ?>" class="btn btn-default btn-block" style="border-radius: 8px; font-weight: 600; font-size: 12px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                        Lihat Status di Lamaran Saya &rarr;
                                    </a>
                                </div>

                            <?php elseif ($evaluation && !$evaluation['can_apply']): ?>
                                <!-- State: Blocked by Deadline or Slots Full -->
                                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 20px 14px; text-align: center;">
                                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 20px; margin: 0 auto 10px auto;">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #991b1b; margin: 0 0 6px 0;">Pendaftaran Ditutup</h4>
                                    <p style="color: #b91c1c; font-size: 12.5px; line-height: 1.45; margin-bottom: 0;">
                                        <?= _ent($evaluation['cannot_apply_reason']); ?>
                                    </p>
                                </div>

                            <?php else: ?>
                                <!-- State: Can Submit Application -->
                                <?php if (!$kol_id): ?>
                                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 12px; margin-bottom: 14px; font-size: 11.5px; color: #1e40af; display: flex; align-items: center; gap: 8px;">
                                        <i class="fa fa-info-circle" style="font-size: 14px; flex-shrink: 0;"></i>
                                        <span><strong>Pratinjau Administrator:</strong> Ini adalah formulir pengajuan yang digunakan oleh akun KOL untuk melamar campaign.</span>
                                    </div>
                                <?php endif; ?>

                                <form id="form_apply_campaign" method="POST" action="<?= site_url('administrator/open_campaigns/apply/' . $campaign->id); ?>">
                                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                                    <div class="form-group" style="margin-bottom: 14px;">
                                        <label for="proposed_rate" style="font-weight: 600; font-size: 12.5px; color: #0f172a; margin-bottom: 6px;">
                                            Tarif yang Diajukan (Proposed Rate)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #f8fafc; font-weight: 700; color: #475569; border-color: #cbd5e1; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">Rp</span>
                                            <input type="text" class="form-control" name="proposed_rate" id="proposed_rate" placeholder="Contoh: 1.500.000" inputmode="numeric" style="font-size: 14px; font-weight: 600; height: 40px; border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                        </div>
                                        <small class="help-block text-muted" style="font-size: 11px; margin-top: 4px; margin-bottom: 0;">
                                            Opsional. Masukkan tarif penawaran Anda untuk campaign ini.
                                        </small>
                                    </div>

                                    <div class="form-group" style="margin-bottom: 18px;">
                                        <label for="pitch_message" style="font-weight: 600; font-size: 12.5px; color: #0f172a; margin-bottom: 6px;">
                                            Pesan Pitching / Ide Konten
                                        </label>
                                        <textarea class="form-control" name="pitch_message" id="pitch_message" rows="3" placeholder="Ceritakan konsep ide konten atau pengalaman relevan Anda..." style="font-size: 13px; border-color: #cbd5e1; border-radius: 8px; padding: 10px; resize: vertical;"></textarea>
                                        <small class="help-block text-muted" style="font-size: 11px; margin-top: 4px; margin-bottom: 0;">
                                            Pesan singkat dan relevan membantu brand mempertimbangkan pengajuan Anda.
                                        </small>
                                    </div>

                                    <button type="submit" class="btn btn-success btn-block" id="btn_submit_apply" style="font-weight: 600; font-size: 13.5px; height: 42px; border-radius: 8px; background: #10b981; border: none; box-shadow: 0 1px 3px rgba(16, 185, 129, 0.25);">
                                        <i class="fa fa-paper-plane"></i> Kirim Pengajuan Kerjasama
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

<script type="text/javascript">
$(document).ready(function() {
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

    // Mobile Segmented Tabs Switcher (<= 768px)
    $('.mobile-seg-btn').on('click', function() {
        $('.mobile-seg-btn').removeClass('active');
        $(this).addClass('active');

        var targetTab = $(this).data('tab');
        $('.tab-panel-mobile').removeClass('active');

        if (targetTab === 'brief') {
            $('#panel-brief').addClass('active');
        } else if (targetTab === 'qualification') {
            $('#panel-qualification').addClass('active');
        } else if (targetTab === 'apply') {
            $('#panel-apply').addClass('active');
        }
    });

    // Real-time Indonesian thousand formatting (xxx.xxx.xxx) with cursor preservation
    $('#proposed_rate').on('input', function() {
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
    });

    // Form Application Submission
    $('#form_apply_campaign').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#btn_submit_apply');

        swal({
            title: "Kirim Pengajuan?",
            text: "Profil akun dan pesan pitching Anda akan diteruskan kepada tim brand untuk ditinjau.",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#10b981",
            confirmButtonText: "Ya, Kirim Sekarang",
            cancelButtonText: "Batal",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function(isConfirm) {
            if (isConfirm) {
                var formData = form.serializeArray();
                formData = formData.filter(function(item) { return item.name !== csrfTokenName; });
                formData.push({ name: csrfTokenName, value: csrfHash });

                btn.prop('disabled', true).text('Mengirim Pengajuan...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    dataType: 'json',
                    data: $.param(formData),
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Kirim Pengajuan Kerjasama');
                        if (res.csrfHash) csrfHash = res.csrfHash;

                        if (res.success) {
                            swal({
                                title: "Pendaftaran Berhasil!",
                                text: res.message,
                                type: "success"
                            }, function() {
                                if (res.redirect) {
                                    window.location.href = res.redirect;
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            swal("Pendaftaran Gagal", res.message, "error");
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Kirim Pengajuan Kerjasama');
                        swal("Gagal", "Terjadi kesalahan server saat memproses pengajuan.", "error");
                    }
                });
            }
        });
    });
});
</script>
