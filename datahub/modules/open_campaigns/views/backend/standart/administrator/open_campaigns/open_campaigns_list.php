<?php
// Helper for platform branding
if (!function_exists('get_platform_chip_meta')) {
    function get_platform_chip_meta($name) {
        $n = strtolower($name);
        if (strpos($n, 'tiktok') !== false) {
            return ['icon' => 'fa-music', 'color' => '#0f172a', 'bg' => '#f1f5f9', 'border' => '#cbd5e1'];
        } elseif (strpos($n, 'instagram') !== false) {
            return ['icon' => 'fa-instagram', 'color' => '#c026d3', 'bg' => '#fdf4ff', 'border' => '#f5d0fe'];
        } elseif (strpos($n, 'youtube') !== false) {
            return ['icon' => 'fa-youtube-play', 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca'];
        } elseif (strpos($n, 'facebook') !== false) {
            return ['icon' => 'fa-facebook', 'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#bfdbfe'];
        } elseif (strpos($n, 'twitter') !== false || strpos($n, 'x') !== false) {
            return ['icon' => 'fa-twitter', 'color' => '#0284c7', 'bg' => '#f0f9ff', 'border' => '#bae6fd'];
        }
        return ['icon' => 'fa-globe', 'color' => '#475569', 'bg' => '#f8fafc', 'border' => '#e2e8f0'];
    }
}
?>

<style>
/* ==========================================================================
   1. GLOBAL CONTAINER & DESKTOP STYLING
   ========================================================================== */
.open-camp-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

/* Header & Context Bar */
.open-camp-header-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
}
.open-camp-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    flex-wrap: wrap;
    gap: 10px;
}
.open-camp-main-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.open-camp-count-pill {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.open-camp-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.open-camp-top-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none !important;
}
.open-camp-top-btn:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}
.open-camp-top-btn:active {
    transform: scale(0.97);
}

/* Search Bar */
.open-camp-search-container {
    position: relative;
    width: 100%;
    margin-bottom: 12px;
}
.open-camp-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
    pointer-events: none;
}
.open-camp-search-input {
    width: 100%;
    height: 42px;
    padding: 8px 14px 8px 38px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 13px;
    color: #0f172a;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: all 0.15s ease;
}
.open-camp-search-input:focus {
    outline: none;
    border-color: #10b981;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
}
.open-camp-search-input::placeholder {
    color: #94a3b8;
}

/* Horizontal Swipeable Filter Chips Carousel */
.open-camp-filter-carousel-wrapper {
    position: relative;
    width: 100%;
    margin-bottom: 18px;
}
.open-camp-filter-pills {
    display: flex;
    align-items: center;
    gap: 7px;
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding: 2px 2px 6px 2px;
}
.open-camp-filter-pills::-webkit-scrollbar {
    display: none;
}
.open-camp-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.open-camp-pill:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
}
.open-camp-pill:active {
    transform: scale(0.96);
}
.open-camp-pill.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
}

/* Grid Layout */
.open-camp-grid {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -8px;
}
.open-camp-col {
    width: 33.333%;
    padding: 0 8px;
    margin-bottom: 16px;
}
@media (max-width: 991px) {
    .open-camp-col {
        width: 50%;
    }
}
@media (max-width: 768px) {
    .open-camp-col {
        width: 100%;
        padding: 0;
        margin-bottom: 12px;
    }
}

/* ==========================================================================
   2. RESTRUCTURED NATIVE CAMPAIGN CARD
   ========================================================================== */
.open-camp-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 18px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    transition: all 0.15s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    min-height: 380px;
    cursor: pointer;
    position: relative;
    user-select: none;
}
.open-camp-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    transform: translateY(-2px);
}
.open-camp-card:active {
    transform: scale(0.995);
}

/* Tier 1: Top Context Badges */
.camp-meta-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 12px;
}
.camp-badge-group {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.camp-type-chip {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}
.camp-type-chip.paid {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
}
.camp-type-chip.barter {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.camp-type-chip.hybrid {
    background: #f5f3ff;
    color: #7c3aed;
    border: 1px solid #ddd6fe;
}
.camp-slots-chip {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
}
.camp-slots-chip.slots-flexible {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
}
.camp-slots-chip.slots-available {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.camp-slots-chip.slots-full {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}
.camp-deadline-indicator {
    font-size: 11px;
    color: #e11d48;
    font-weight: 600;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Tier 2: Campaign Identity */
.camp-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
    margin: 0 0 4px 0;
}
.camp-title a {
    color: inherit;
    text-decoration: none;
}
.camp-brand-row {
    font-size: 12.5px;
    color: #64748b;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Products preview */
.camp-products-row {
    font-size: 12px;
    color: #334155;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    padding: 6px 10px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.camp-products-row i {
    color: #2563eb;
}

/* Tier 3: Requirements Cardlet */
.camp-specs-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 12px;
    font-size: 12px;
}
.camp-spec-item {
    margin-bottom: 6px;
    display: flex;
    align-items: flex-start;
    gap: 6px;
}
.camp-spec-item:last-child {
    margin-bottom: 0;
}
.camp-spec-label {
    font-weight: 600;
    color: #475569;
    min-width: 68px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.camp-platform-list {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 4px;
}
.camp-platform-chip {
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    line-height: 1.2;
}

/* Tier 4: Qualification Banner */
.camp-qual-banner {
    border-radius: 8px;
    padding: 7px 11px;
    font-size: 11.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
}
.camp-qual-banner.is-qualified {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}
.camp-qual-banner.is-unqualified {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
}

/* Tier 5: Action Footer */
.camp-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
    margin-top: auto;
}
.camp-period {
    font-size: 11.5px;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.camp-btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    height: 36px;
    padding: 0 16px;
    border-radius: 8px;
    text-decoration: none !important;
    transition: all 0.12s ease;
    flex-shrink: 0;
}
.camp-btn-action.btn-apply {
    background: #10b981;
    color: #ffffff !important;
    border: none;
    box-shadow: 0 1px 3px rgba(16, 185, 129, 0.25);
}
.camp-btn-action.btn-apply:hover {
    background: #059669;
}
.camp-btn-action.btn-applied {
    background: #ecfdf5;
    color: #059669 !important;
    border: 1px solid #a7f3d0;
}
.camp-btn-action.btn-disabled {
    background: #f1f5f9;
    color: #94a3b8 !important;
    border: 1px solid #e2e8f0;
    cursor: not-allowed;
}

/* Empty State */
.open-camp-empty {
    width: 100%;
    padding: 48px 20px;
    text-align: center;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 14px;
    margin: 12px 0;
}
.open-camp-empty-icon {
    width: 52px;
    height: 52px;
    margin: 0 auto 12px auto;
    background: #f1f5f9;
    color: #94a3b8;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
.open-camp-empty-title {
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 4px;
}
.open-camp-empty-sub {
    font-size: 12.5px;
    color: #64748b;
    max-width: 320px;
    margin: 0 auto;
}

/* ==========================================================================
   3. NATIVE MOBILE APP ADAPTATIONS (<= 768px)
   ========================================================================== */
@media (max-width: 768px) {
    /* Hide Desktop Breadcrumb */
    .content-header {
        display: none !important;
    }

    /* Page Outer Spacing */
    section.content {
        padding: 14px 16px 36px 16px !important;
    }

    /* Grid Layout Mobile Override (Ensure cards don't touch edges) */
    .open-camp-grid {
        margin: 0 !important;
    }
    .open-camp-col {
        width: 100% !important;
        padding: 0 !important;
        margin-bottom: 14px !important;
    }

    /* Native Mobile Header */
    .open-camp-main-title {
        font-size: 18px;
    }
    .open-camp-top-btn {
        padding: 6px 11px;
        font-size: 11.5px;
    }

    /* Mobile Search Input */
    .open-camp-search-container {
        margin-bottom: 12px;
    }
    .open-camp-search-input {
        height: 40px;
        font-size: 12.5px;
        border-radius: 20px;
        background: #f8fafc;
    }
    .open-camp-search-input:focus {
        background: #ffffff;
    }

    /* Filter Carousel (Edge-to-Edge swipe with 16px container counter) */
    .open-camp-filter-carousel-wrapper {
        margin-left: -16px;
        margin-right: -16px;
        padding-left: 16px;
        padding-right: 16px;
        margin-bottom: 14px;
    }
    .open-camp-pill {
        padding: 5px 12px;
        font-size: 11.5px;
    }

    /* Native Mobile Cards */
    .open-camp-card {
        padding: 14px 15px;
        border-radius: 12px;
        min-height: auto;
        height: auto;
    }
    .camp-title {
        font-size: 15px;
    }
    .camp-card-footer {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    .camp-btn-action {
        height: 34px;
        padding: 0 14px;
        font-size: 11.5px;
    }
}

@media (max-width: 360px) {
    .camp-card-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    .camp-btn-action {
        width: 100%;
        justify-content: center;
    }
}
</style>

<section class="content">
    <div class="open-camp-container">

        <!-- Flash Message -->
        <?php if ($this->session->flashdata('f_message')): ?>
            <div class="alert alert-<?= $this->session->flashdata('f_type') ?: 'info'; ?> alert-dismissible" style="border-radius: 8px;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?= $this->session->flashdata('f_message'); ?>
            </div>
        <?php endif; ?>

        <!-- Native Header & Quick Action Row (Always 2 Clean Rows) -->
        <div class="open-camp-header-wrap">
            <div class="open-camp-title-row">
                <h1 class="open-camp-main-title">
                    <i class="fa fa-bullhorn text-success"></i>
                    <span>Open Campaigns</span>
                    <span class="open-camp-count-pill">
                        <?= count($campaigns); ?> Aktif
                    </span>
                </h1>
                <div class="open-camp-actions">
                    <a href="<?= site_url('administrator/open_campaigns/my_applications'); ?>" class="open-camp-top-btn" title="Lihat pendaftaran campaign Anda">
                        <i class="fa fa-list-alt text-primary"></i>
                        <span>Pendaftaran Saya</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Integrated Search Bar -->
        <div class="open-camp-search-container">
            <i class="fa fa-search open-camp-search-icon"></i>
            <input type="text" id="open-camp-search" class="open-camp-search-input" placeholder="Cari nama campaign, brand, produk, atau tier..." autocomplete="off">
        </div>

        <!-- Horizontal Swipeable Filter Chips Carousel -->
        <div class="open-camp-filter-carousel-wrapper">
            <div class="open-camp-filter-pills" id="open-camp-filters">
                <span class="open-camp-pill active" data-filter="all">Semua</span>
                <span class="open-camp-pill" data-filter="qualified">
                    <i class="fa fa-check-circle text-success"></i> Memenuhi Syarat
                </span>
                <span class="open-camp-pill" data-filter="under-qualified">
                    <i class="fa fa-exclamation-triangle text-warning"></i> Under-qualified
                </span>
                <span class="open-camp-pill" data-filter="paid">Paid</span>
                <span class="open-camp-pill" data-filter="barter">Barter</span>
                <span class="open-camp-pill" data-filter="tiktok">
                    <i class="fa fa-music"></i> TikTok
                </span>
                <span class="open-camp-pill" data-filter="instagram">
                    <i class="fa fa-instagram" style="color: #c026d3;"></i> Instagram
                </span>
                <span class="open-camp-pill" data-filter="youtube">
                    <i class="fa fa-youtube-play text-danger"></i> YouTube
                </span>
                <span class="open-camp-pill" data-filter="applied">Sudah Mendaftar</span>
            </div>
        </div>

        <!-- Campaign Feed Cards Grid -->
        <?php if (empty($campaigns)): ?>
            <div class="open-camp-empty">
                <div class="open-camp-empty-icon">
                    <i class="fa fa-folder-open-o"></i>
                </div>
                <div class="open-camp-empty-title">Saat Ini Belum Ada Campaign Terbuka</div>
                <div class="open-camp-empty-sub">Silakan cek kembali secara berkala untuk melihat campaign baru yang dapat Anda ikuti.</div>
            </div>
        <?php else: ?>
            <div class="open-camp-grid" id="open-camp-grid">
                <?php foreach ($campaigns as $camp): ?>
                    <?php 
                        $type_cls = strtolower($camp->campaign_type);
                        $is_qualified = isset($camp->evaluation) && !empty($camp->evaluation['is_qualified']);
                        $has_applied = !empty($camp->has_applied);
                        
                        $platform_names = [];
                        if (!empty($camp->required_platforms)) {
                            foreach ($camp->required_platforms as $rp) {
                                $platform_names[] = strtolower($rp->platform_name);
                            }
                        }
                        $platforms_str = implode(' ', $platform_names);
                        
                        $search_corpus = strtolower(
                            $camp->name . ' ' . 
                            ($camp->brand_name ?? '') . ' ' . 
                            ($camp->brand_category ?? '') . ' ' . 
                            $camp->campaign_type . ' ' .
                            $platforms_str . ' ' .
                            implode(' ', $camp->allowed_tiers ?? [])
                        );
                    ?>
                    <div class="open-camp-col"
                         data-type="<?= $type_cls; ?>"
                         data-qualified="<?= $is_qualified ? '1' : '0'; ?>"
                         data-applied="<?= $has_applied ? '1' : '0'; ?>"
                         data-platforms="<?= htmlspecialchars($platforms_str); ?>"
                         data-search="<?= htmlspecialchars($search_corpus); ?>">
                        
                        <div class="open-camp-card" data-url="<?= site_url('administrator/open_campaigns/view/' . $camp->id); ?>">
                            
                            <div>
                                <!-- Tier 1: Context & Badges -->
                                <div class="camp-meta-top">
                                    <div class="camp-badge-group">
                                        <span class="camp-type-chip <?= $type_cls; ?>">
                                            <?= _ent($camp->campaign_type); ?>
                                        </span>

                                        <?php if (!empty($camp->max_slots)): ?>
                                            <span class="camp-slots-chip <?= $camp->is_slots_full ? 'slots-full' : 'slots-available'; ?>">
                                                <?= (int)$camp->accepted_count; ?>/<?= (int)$camp->max_slots; ?> Slots
                                            </span>
                                        <?php else: ?>
                                            <span class="camp-slots-chip slots-flexible">
                                                Kuota Fleksibel
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($camp->application_deadline)): ?>
                                        <div class="camp-deadline-indicator">
                                            <i class="fa fa-clock-o"></i>
                                            <span>Batas: <?= date('d M Y', strtotime($camp->application_deadline)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Tier 2: Campaign Identity -->
                                <h3 class="camp-title">
                                    <a href="<?= site_url('administrator/open_campaigns/view/' . $camp->id); ?>">
                                        <?= _ent($camp->name); ?>
                                    </a>
                                </h3>

                                <div class="camp-brand-row">
                                    <i class="fa fa-tag text-muted"></i>
                                    <span><?= !empty($camp->brand_name) ? _ent($camp->brand_name) : 'Brand Sponsor'; ?></span>
                                    <?php if (!empty($camp->brand_category)): ?>
                                        <span>&bull; <?= _ent($camp->brand_category); ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Products preview -->
                                <?php if (!empty($camp->products)): ?>
                                    <div class="camp-products-row">
                                        <i class="fa fa-cube"></i>
                                        <span>
                                            <?= implode(', ', array_map(function($p){ return htmlspecialchars($p->product_name); }, array_slice($camp->products, 0, 2))); ?>
                                            <?= count($camp->products) > 2 ? ' + ' . (count($camp->products) - 2) . ' lainnya' : ''; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- Tier 3: Requirements Specs Box -->
                                <div class="camp-specs-box">
                                    <div class="camp-spec-item">
                                        <span class="camp-spec-label"><i class="fa fa-globe text-info"></i> Platform:</span>
                                        <div class="camp-platform-list">
                                            <?php if (!empty($camp->required_platforms)): ?>
                                                <?php foreach ($camp->required_platforms as $rp): ?>
                                                    <?php $pmeta = get_platform_chip_meta($rp->platform_name); ?>
                                                    <span class="camp-platform-chip" style="background-color: <?= $pmeta['bg']; ?>; color: <?= $pmeta['color']; ?>; border: 1px solid <?= $pmeta['border']; ?>;">
                                                        <i class="fa <?= $pmeta['icon']; ?>"></i>
                                                        <span><?= _ent($rp->platform_name); ?></span>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Semua Platform</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="camp-spec-item">
                                        <span class="camp-spec-label"><i class="fa fa-star text-warning"></i> Tier:</span>
                                        <span style="color: #334155; font-weight: 500;">
                                            <?= !empty($camp->allowed_tiers) ? implode(', ', array_map(function($t){ return htmlspecialchars($t); }, $camp->allowed_tiers)) : 'Semua Tier'; ?>
                                        </span>
                                    </div>

                                    <div class="camp-spec-item">
                                        <span class="camp-spec-label"><i class="fa fa-sliders text-success"></i> Min Metrik:</span>
                                        <span style="color: #334155; font-weight: 500;">
                                            <?= !empty($camp->min_followers) ? number_format($camp->min_followers) . '+ Foll' : 'No min foll'; ?> &bull; 
                                            <?= !empty($camp->min_er) ? number_format($camp->min_er, 2) . '% ER' : 'No min ER'; ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Tier 4: Qualification Status Banner -->
                                <?php if (isset($camp->evaluation)): ?>
                                    <?php if ($camp->evaluation['is_qualified']): ?>
                                        <div class="camp-qual-banner is-qualified">
                                            <i class="fa fa-check-circle text-success"></i>
                                            <span>Profil Memenuhi Kualifikasi</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="camp-qual-banner is-unqualified" title="<?= htmlspecialchars($camp->evaluation['summary'] ?? ''); ?>">
                                            <i class="fa fa-info-circle text-warning"></i>
                                            <span>Sebagian Kriteria Belum Sesuai (<?= $camp->evaluation['summary'] ?? ''; ?>)</span>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Tier 5: Connected Action Footer -->
                            <div class="camp-card-footer">
                                <div class="camp-period">
                                    <i class="fa fa-calendar-check-o"></i>
                                    <span><?= date('d M', strtotime($camp->start_date)); ?> - <?= date('d M Y', strtotime($camp->end_date)); ?></span>
                                </div>

                                <?php if ($camp->has_applied): ?>
                                    <?php 
                                        $app_status = $camp->my_application->application_status;
                                    ?>
                                    <a href="<?= site_url('administrator/open_campaigns/my_applications'); ?>" class="camp-btn-action btn-applied" title="Lihat status pendaftaran Anda">
                                        <i class="fa fa-check"></i>
                                        <span>Sudah Mendaftar (<?= $app_status; ?>)</span>
                                    </a>
                                <?php elseif ($camp->is_slots_full): ?>
                                    <span class="camp-btn-action btn-disabled">
                                        <i class="fa fa-lock"></i>
                                        <span>Kuota Penuh</span>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= site_url('administrator/open_campaigns/view/' . $camp->id); ?>" class="camp-btn-action btn-apply">
                                        <span>Lihat Detail & Daftar</span>
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Filter Empty State -->
                <div class="open-camp-empty" id="open-camp-filter-empty" style="display: none;">
                    <div class="open-camp-empty-icon">
                        <i class="fa fa-search"></i>
                    </div>
                    <div class="open-camp-empty-title">Tidak Ada Campaign yang Cocok</div>
                    <div class="open-camp-empty-sub">Coba ubah kata kunci pencarian atau pilih filter yang lain.</div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<script>
$(document).ready(function() {
    var activeFilter = 'all';

    // Highlight active sidebar menu
    $('.sidebar-menu li').removeClass('active');
    $('.sidebar-menu li a[href*="open_campaigns"]').parents('li').addClass('active');

    // Horizontal Filter Pills Event
    $('.open-camp-pill').on('click', function() {
        $('.open-camp-pill').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        applyFilters();
    });

    // Instant Search Input Event
    $('#open-camp-search').on('keyup input', function() {
        applyFilters();
    });

    // Tap anywhere on card to navigate on mobile/desktop
    $(document).on('click', '.open-camp-card', function(e) {
        if ($(e.target).closest('a, button').length) {
            return; // let native button or link handle it
        }
        var url = $(this).data('url');
        if (url) {
            window.location.href = url;
        }
    });

    // Filter Logic
    function applyFilters() {
        var keyword = $.trim($('#open-camp-search').val()).toLowerCase();
        var visibleCount = 0;

        $('.open-camp-col').each(function() {
            var $col = $(this);
            var type = $col.data('type') || '';
            var isQualified = $col.data('qualified') == '1';
            var hasApplied = $col.data('applied') == '1';
            var platforms = ($col.data('platforms') || '').toLowerCase();
            var searchData = $col.data('search') || '';

            var matchesFilter = true;
            if (activeFilter === 'all') {
                matchesFilter = true;
            } else if (activeFilter === 'qualified') {
                matchesFilter = isQualified;
            } else if (activeFilter === 'under-qualified') {
                matchesFilter = !isQualified;
            } else if (activeFilter === 'paid') {
                matchesFilter = (type === 'paid');
            } else if (activeFilter === 'barter') {
                matchesFilter = (type === 'barter');
            } else if (activeFilter === 'applied') {
                matchesFilter = hasApplied;
            } else if (activeFilter === 'tiktok') {
                matchesFilter = platforms.indexOf('tiktok') !== -1;
            } else if (activeFilter === 'instagram') {
                matchesFilter = platforms.indexOf('instagram') !== -1;
            } else if (activeFilter === 'youtube') {
                matchesFilter = platforms.indexOf('youtube') !== -1;
            }

            var matchesKeyword = true;
            if (keyword.length > 0) {
                matchesKeyword = searchData.indexOf(keyword) !== -1;
            }

            if (matchesFilter && matchesKeyword) {
                $col.show();
                visibleCount++;
            } else {
                $col.hide();
            }
        });

        if (visibleCount === 0 && $('.open-camp-col').length > 0) {
            $('#open-camp-filter-empty').show();
        } else {
            $('#open-camp-filter-empty').hide();
        }
    }
});
</script>
