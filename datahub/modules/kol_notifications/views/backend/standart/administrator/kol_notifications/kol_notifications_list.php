<?php
if (!function_exists('kol_notif_category_meta')) {
    function kol_notif_category_meta($category) {
        switch ($category) {
            case 'contract':
                return [
                    'icon'   => 'fa-file-text-o',
                    'bg'     => '#ecfdf5',
                    'color'  => '#059669',
                    'border' => '#a7f3d0',
                    'label'  => 'Kontrak'
                ];
            case 'vt':
                return [
                    'icon'   => 'fa-video-camera',
                    'bg'     => '#f5f3ff',
                    'color'  => '#7c3aed',
                    'border' => '#ddd6fe',
                    'label'  => 'Draft VT'
                ];
            case 'deadline':
                return [
                    'icon'   => 'fa-hourglass-half',
                    'bg'     => '#fff1f2',
                    'color'  => '#e11d48',
                    'border' => '#fecdd3',
                    'label'  => 'Deadline'
                ];
            case 'application':
                return [
                    'icon'   => 'fa-paper-plane-o',
                    'bg'     => '#eff6ff',
                    'color'  => '#2563eb',
                    'border' => '#bfdbfe',
                    'label'  => 'Pendaftaran'
                ];
            case 'shipment':
                return [
                    'icon'   => 'fa-truck',
                    'bg'     => '#fffbeb',
                    'color'  => '#d97706',
                    'border' => '#fde68a',
                    'label'  => 'Pengiriman'
                ];
            case 'invoice':
                return [
                    'icon'   => 'fa-credit-card',
                    'bg'     => '#ecfeff',
                    'color'  => '#0891b2',
                    'border' => '#a5f3fc',
                    'label'  => 'Tagihan'
                ];
            default:
                return [
                    'icon'   => 'fa-bell-o',
                    'bg'     => '#f8fafc',
                    'color'  => '#475569',
                    'border' => '#e2e8f0',
                    'label'  => 'Pemberitahuan'
                ];
        }
    }
}
?>

<style>
/* ==========================================================================
   1. GLOBAL CONTAINER & DESKTOP ARCHITECTURE
   ========================================================================== */
.notif-page-container {
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
}

/* Header Area (Always 2 Dedicated Lines: Line 1 Title, Line 2 Actions) */
.notif-header-title-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 14px;
}
.notif-main-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.notif-unread-count-pill {
    background: #fee2e2;
    color: #dc2626;
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
}
.notif-unread-count-pill.is-zero {
    display: none;
}

/* Top Action Controls */
.notif-actions-bar {
    display: flex;
    align-items: center;
    gap: 8px;
}
.notif-btn-top {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none !important;
}
.notif-btn-top:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}
.notif-btn-top:active {
    transform: scale(0.97);
}

/* Integrated Search Bar */
.notif-search-container {
    position: relative;
    width: 100%;
    margin-bottom: 12px;
}
.notif-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
    pointer-events: none;
}
.notif-search-input {
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
.notif-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
}
.notif-search-input::placeholder {
    color: #94a3b8;
}

/* Horizontal Swipeable Filter Chips Carousel */
.notif-filter-carousel-wrapper {
    position: relative;
    width: 100%;
    margin-bottom: 16px;
}
.notif-filter-pills {
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
.notif-filter-pills::-webkit-scrollbar {
    display: none;
}
.notif-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 6px 13px;
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
.notif-pill:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
}
.notif-pill:active {
    transform: scale(0.96);
}
.notif-pill.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
}
.notif-pill .pill-count {
    background: #f1f5f9;
    color: #475569;
    padding: 2px 7px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
}
.notif-pill.active .pill-count {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

/* Notification Feed Container */
.notif-rows-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
}

/* ==========================================================================
   2. RESTRUCTURED, SPACIOUS & COHESIVE CARD ARCHITECTURE
   ========================================================================== */
.notif-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 18px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    transition: all 0.15s ease;
    display: block;
    cursor: pointer;
    position: relative;
    user-select: none;
}
.notif-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    transform: translateY(-1px);
}
.notif-card:active {
    transform: scale(0.995);
}

/* Unread Card Styling with Soft Tint Wash */
.notif-card.is-unread {
    background: #f8faff;
    border-color: #bfdbfe;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.05);
}
.notif-card.is-unread:hover {
    background: #f0f7ff;
    border-color: #93c5fd;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08);
}

/* Tier 1: Top Context & Metadata Bar */
.notif-card-meta-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 10px;
}
.notif-card-meta-left {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    min-width: 0;
}

/* Category Chip */
.notif-cat-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 6px;
    line-height: 1.2;
    flex-shrink: 0;
}
.notif-cat-chip i {
    font-size: 11px;
}

/* Status Badge */
.notif-status-badge {
    font-size: 10.5px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 5px;
    line-height: 1.2;
    flex-shrink: 0;
}

/* Relative Timestamp */
.notif-card-time-ago {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    color: #64748b;
    font-weight: 500;
    flex-shrink: 0;
    white-space: nowrap;
}

/* Tier 2: Main Content Area (Spacious & Clean) */
.notif-card-main {
    margin-bottom: 2px;
}
.notif-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.notif-unread-dot {
    width: 8px;
    height: 8px;
    background: #2563eb;
    border-radius: 50%;
    flex-shrink: 0;
    display: inline-block;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
}
.notif-card-msg {
    font-size: 13.5px;
    color: #475569;
    line-height: 1.55;
    margin: 0;
    word-break: break-word;
}
.notif-card-msg strong {
    color: #0f172a;
    font-weight: 600;
}

/* Tier 3: Connected Action Footer */
.notif-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}
.notif-card-date {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #94a3b8;
    white-space: nowrap;
}
.notif-btn-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    height: 34px;
    padding: 0 14px;
    border-radius: 8px;
    text-decoration: none !important;
    transition: all 0.12s ease;
    flex-shrink: 0;
}
.notif-btn-action.btn-primary {
    background: #2563eb;
    color: #ffffff !important;
    border: none;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.25);
}
.notif-btn-action.btn-primary:hover {
    background: #1d4ed8;
}
.notif-btn-action.btn-default {
    background: #f8fafc;
    color: #334155 !important;
    border: 1px solid #e2e8f0;
}
.notif-btn-action.btn-default:hover {
    background: #e2e8f0;
    color: #0f172a !important;
}

/* Empty State */
.notif-empty-state {
    padding: 48px 20px;
    text-align: center;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 14px;
    margin: 12px 0;
}
.notif-empty-icon {
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
.notif-empty-title {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 4px;
}
.notif-empty-subtitle {
    font-size: 12px;
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
        padding: 12px 10px 30px 10px !important;
    }

    /* Header Compact Floating Native Bar */
    .notif-header-title-wrap {
        margin-bottom: 12px;
        gap: 8px;
    }
    .notif-main-title {
        font-size: 18px;
    }
    .notif-btn-top {
        padding: 6px 11px;
        font-size: 11.5px;
    }

    /* Full width search input */
    .notif-search-container {
        margin-bottom: 10px;
    }
    .notif-search-input {
        height: 40px;
        font-size: 12.5px;
        border-radius: 20px;
        background: #f8fafc;
    }
    .notif-search-input:focus {
        background: #ffffff;
    }

    /* Chips Carousel Spacing */
    .notif-filter-carousel-wrapper {
        margin-left: -10px;
        margin-right: -10px;
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 12px;
    }
    .notif-pill {
        padding: 5px 12px;
        font-size: 11.5px;
    }

    /* Native Mobile Cards */
    .notif-card {
        padding: 14px 15px;
        border-radius: 12px;
        margin-bottom: 2px;
    }
    .notif-card-title {
        font-size: 14px;
    }
    .notif-card-msg {
        font-size: 13px;
        line-height: 1.5;
    }
    .notif-card-footer {
        margin-top: 12px;
        padding-top: 10px;
    }
    .notif-btn-action {
        height: 32px;
        padding: 0 12px;
        font-size: 11.5px;
    }
}

/* Extra small devices (<= 360px) */
@media (max-width: 360px) {
    .notif-card-meta-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    .notif-card-time-ago {
        font-size: 11px;
    }
    .notif-card-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    .notif-btn-action {
        width: 100%;
        justify-content: center;
    }
}
</style>

<section class="content">
    <div class="notif-page-container">

        <!-- Top Header & Actions Bar -->
        <div class="notif-header-title-wrap">
            <div>
                <h1 class="notif-main-title">
                    Pemberitahuan
                    <span class="notif-unread-count-pill <?= $unread_count == 0 ? 'is-zero' : ''; ?>" id="badge-top-unread">
                        <?= $unread_count; ?> Belum Dibaca
                    </span>
                </h1>
            </div>
            <div class="notif-actions-bar">
                <button type="button" class="notif-btn-top" id="btn-mark-all-read" title="Tandai semua sebagai sudah dibaca">
                    <i class="fa fa-check-square-o"></i>
                    <span>Tandai Semua</span>
                </button>
                <button type="button" class="notif-btn-top" onclick="location.reload();" title="Muat ulang notifikasi">
                    <i class="fa fa-refresh"></i>
                </button>
            </div>
        </div>

        <!-- Integrated Search Bar -->
        <div class="notif-search-container">
            <i class="fa fa-search notif-search-icon"></i>
            <input type="text" id="notif-search-input" class="notif-search-input" placeholder="Cari notifikasi berdasarkan nama campaign, tugas, atau kata kunci..." autocomplete="off">
        </div>

        <!-- Horizontal Swipeable Filter Chips Carousel -->
        <div class="notif-filter-carousel-wrapper">
            <div class="notif-filter-pills" id="notif-filter-pills">
                <span class="notif-pill active" data-filter="all">
                    Semua <span class="pill-count" id="pill-all-count"><?= $total_count; ?></span>
                </span>
                <span class="notif-pill" data-filter="unread">
                    Belum Dibaca <span class="pill-count" id="pill-unread-count"><?= $unread_count; ?></span>
                </span>
                <span class="notif-pill" data-filter="actionable">
                    Perlu Tindakan <span class="pill-count"><?= $actionable_count; ?></span>
                </span>
                <span class="notif-pill" data-filter="contract">
                    Kontrak <span class="pill-count"><?= $contract_count; ?></span>
                </span>
                <span class="notif-pill" data-filter="vt">
                    Draft VT <span class="pill-count"><?= $vt_count; ?></span>
                </span>
                <span class="notif-pill" data-filter="deadline">
                    Deadline <span class="pill-count"><?= $deadline_count; ?></span>
                </span>
                <span class="notif-pill" data-filter="application">
                    Pendaftaran <span class="pill-count"><?= $app_count; ?></span>
                </span>
                <span class="notif-pill" data-filter="shipment">
                    Pengiriman <span class="pill-count"><?= $shipment_count; ?></span>
                </span>
                <?php if ($invoice_count > 0): ?>
                <span class="notif-pill" data-filter="invoice">
                    Tagihan <span class="pill-count"><?= $invoice_count; ?></span>
                </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notification Feed Cards List -->
        <div class="notif-rows-container" id="notif-rows-container">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $item): ?>
                    <?php 
                        $cat_meta = kol_notif_category_meta($item['category']);
                        $is_unread = !empty($item['is_unread']);
                    ?>
                    <div class="notif-card <?= $is_unread ? 'is-unread' : 'is-read'; ?>"
                         data-category="<?= $item['category']; ?>"
                         data-unread="<?= $is_unread ? '1' : '0'; ?>"
                         data-actionable="<?= !empty($item['is_actionable']) ? '1' : '0'; ?>"
                         data-url="<?= !empty($item['url']) ? $item['url'] : ''; ?>"
                         data-search="<?= htmlspecialchars(strtolower($item['title'] . ' ' . strip_tags($item['message']))); ?>">
                        
                        <!-- Tier 1: Top Context Bar (Category Tag + Status Badge + Time Ago) -->
                        <div class="notif-card-meta-bar">
                            <div class="notif-card-meta-left">
                                <span class="notif-cat-chip" style="background-color: <?= $cat_meta['bg']; ?>; color: <?= $cat_meta['color']; ?>; border: 1px solid <?= $cat_meta['border']; ?>;">
                                    <i class="fa <?= $cat_meta['icon']; ?>"></i>
                                    <span><?= $cat_meta['label']; ?></span>
                                </span>
                                <span class="notif-status-badge label <?= $item['badge_class']; ?>">
                                    <?= $item['badge']; ?>
                                </span>
                            </div>
                            <div class="notif-card-time-ago">
                                <i class="fa fa-clock-o"></i>
                                <span><?= $item['time_ago']; ?></span>
                            </div>
                        </div>

                        <!-- Tier 2: Main Content Body (Full-Width Title & Message) -->
                        <div class="notif-card-main">
                            <h3 class="notif-card-title">
                                <?php if ($is_unread): ?>
                                    <span class="notif-unread-dot" title="Belum dibaca"></span>
                                <?php endif; ?>
                                <span><?= $item['title']; ?></span>
                            </h3>
                            <div class="notif-card-msg">
                                <?= $item['message']; ?>
                            </div>
                        </div>

                        <!-- Tier 3: Connected Action Footer (Date & Button) -->
                        <div class="notif-card-footer">
                            <div class="notif-card-date">
                                <i class="fa fa-calendar-o"></i>
                                <span><?= date('d M Y', strtotime($item['timestamp'])); ?></span>
                            </div>
                            <?php if (!empty($item['url'])): ?>
                                <a href="<?= $item['url']; ?>" class="notif-btn-action <?= !empty($item['is_actionable']) ? 'btn-primary' : 'btn-default'; ?>">
                                    <span><?= !empty($item['action_text']) ? $item['action_text'] : 'Buka'; ?></span>
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="notif-empty-state">
                    <div class="notif-empty-icon">
                        <i class="fa fa-bell-slash-o"></i>
                    </div>
                    <div class="notif-empty-title">Tidak Ada Pemberitahuan</div>
                    <div class="notif-empty-subtitle">Semua tugas dan aktivitas kontrak Anda sudah terkini.</div>
                </div>
            <?php endif; ?>

            <!-- Filtered Empty State -->
            <div class="notif-empty-state" id="notif-filter-empty" style="display: none;">
                <div class="notif-empty-icon">
                    <i class="fa fa-search"></i>
                </div>
                <div class="notif-empty-title">Tidak Ditemukan</div>
                <div class="notif-empty-subtitle">Tidak ada pemberitahuan yang cocok dengan kata kunci atau filter yang dipilih.</div>
            </div>
        </div>

    </div>
</section>

<script>
$(document).ready(function() {
    var activeFilter = 'all';

    // Highlight active sidebar item
    $('.sidebar-menu li').removeClass('active');
    $('.sidebar-menu li a[href*="kol_notifications"]').parents('li').addClass('active');

    // Filter Chips Click Event
    $('.notif-pill').on('click', function() {
        $('.notif-pill').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        applyFilters();
    });

    // Instant Search Input
    $('#notif-search-input').on('keyup input', function() {
        applyFilters();
    });

    // Card Tap Event: Click anywhere on the card to navigate if url exists
    $(document).on('click', '.notif-card', function(e) {
        if ($(e.target).closest('a, button').length) {
            return; // let native link/button execute
        }
        var url = $(this).data('url');
        if (url) {
            window.location.href = url;
        }
    });

    // Core Filter Functionality
    function applyFilters() {
        var keyword = $.trim($('#notif-search-input').val()).toLowerCase();
        var visibleCount = 0;

        $('.notif-card').each(function() {
            var $item = $(this);
            var category = $item.data('category');
            var isUnread = $item.data('unread') == '1';
            var isActionable = $item.data('actionable') == '1';
            var searchData = $item.data('search') || '';

            var matchesCategory = true;
            if (activeFilter === 'all') {
                matchesCategory = true;
            } else if (activeFilter === 'unread') {
                matchesCategory = isUnread;
            } else if (activeFilter === 'actionable') {
                matchesCategory = isActionable;
            } else {
                matchesCategory = (category === activeFilter);
            }

            var matchesKeyword = true;
            if (keyword.length > 0) {
                matchesKeyword = searchData.indexOf(keyword) !== -1;
            }

            if (matchesCategory && matchesKeyword) {
                $item.show();
                visibleCount++;
            } else {
                $item.hide();
            }
        });

        if (visibleCount === 0 && $('.notif-card').length > 0) {
            $('#notif-filter-empty').show();
        } else {
            $('#notif-filter-empty').hide();
        }
    }

    // Mark All Read AJAX
    $('#btn-mark-all-read').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <span>Menyimpan...</span>');

        $.ajax({
            url: BASE_URL + 'administrator/kol_notifications/mark_all_read',
            type: 'POST',
            dataType: 'json',
            data: {
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            },
            success: function(res) {
                if (res.success) {
                    $('.notif-card').removeClass('is-unread').addClass('is-read');
                    $('.notif-card').attr('data-unread', '0');
                    $('.notif-unread-dot').fadeOut(200, function() { $(this).remove(); });
                    $('#pill-unread-count').text('0');
                    $('#badge-top-unread').addClass('is-zero').text('0 Belum Dibaca');

                    if (typeof refreshKolNotifications === 'function') {
                        refreshKolNotifications();
                    } else {
                        $('#kol-notif-badge').empty().hide();
                    }

                    $btn.html('<i class="fa fa-check text-success"></i> <span>Selesai</span>');
                    setTimeout(function() {
                        $btn.prop('disabled', false).html('<i class="fa fa-check-square-o"></i> <span>Tandai Semua</span>');
                        if (activeFilter === 'unread') {
                            applyFilters();
                        }
                    }, 1200);
                } else {
                    alert('Gagal menandai notifikasi.');
                    $btn.prop('disabled', false).html('<i class="fa fa-check-square-o"></i> <span>Tandai Semua</span>');
                }
            },
            error: function() {
                alert('Terjadi kesalahan jaringan.');
                $btn.prop('disabled', false).html('<i class="fa fa-check-square-o"></i> <span>Tandai Semua</span>');
            }
        });
    });
});
</script>
