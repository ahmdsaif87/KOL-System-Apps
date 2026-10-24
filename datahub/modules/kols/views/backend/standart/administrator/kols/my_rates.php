<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<style>
/* Modern Full-Width UI matching My Tasks */
.rates-header-area {
    margin-bottom: 20px;
}

.rates-header-area h1 {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin: 0;
}

.rates-header-area p {
    color: #64748b;
    font-size: 13px;
    margin: 3px 0 0 0;
}

/* Card Container */
.rates-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 24px;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.rates-archive-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 20px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.rates-archive-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.rates-archive-sub {
    font-size: 12px;
    color: #64748b;
    margin: 2px 0 0 0;
}

/* Rate Item Box */
.rate-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 18px 20px;
    margin-bottom: 16px;
    transition: all 0.2s ease;
}

.rate-box:hover, .rate-box:focus-within {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(15,23,42,0.04);
}

.rate-label-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
    display: block;
}

.rate-label-desc {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 12px;
    line-height: 1.4;
    display: block;
}

.rate-input-group {
    display: flex;
    align-items: stretch;
}

.rate-input-addon {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-right: none;
    border-radius: 6px 0 0 6px;
    font-weight: 700;
    color: #475569;
    font-size: 13px;
    padding: 8px 14px;
    display: flex;
    align-items: center;
}

.rate-box:focus-within .rate-input-addon {
    border-color: #2563eb;
}

.rate-control {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 0 6px 6px 0;
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    height: 40px;
    padding: 6px 12px;
    outline: none;
    transition: all 0.15s ease;
}

.rate-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}

.rate-control::placeholder {
    color: #94a3b8;
    font-weight: 400;
    font-size: 13px;
}

.rate-help-note {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 6px;
    display: block;
}

/* Platform Accordion */
.rates-platform-panel {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.rates-platform-toggle {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    cursor: pointer;
    text-decoration: none !important;
    color: inherit;
    flex-wrap: wrap;
}

.rates-platform-toggle:hover {
    background: #f8fafc;
}

.rates-platform-caret {
    color: #94a3b8;
    font-size: 13px;
    transition: transform 0.2s ease;
}

.rates-platform-name {
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
}

.rates-platform-user {
    font-size: 12px;
    color: #64748b;
}

.rates-platform-meta {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.rates-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 12px;
    white-space: nowrap;
}

.rates-badge.done {
    background: #e6f6ec;
    color: #16803c;
}

.rates-badge.partial {
    background: #fdf3e7;
    color: #b54708;
}

.rates-badge.empty {
    background: #f1f5f9;
    color: #64748b;
}

.rates-badge.nosync {
    background: #f1f5f9;
    color: #94a3b8;
    font-weight: 600;
}

.rates-min-hint {
    font-size: 12px;
    color: #0f172a;
    font-weight: 700;
    white-space: nowrap;
}
/* Action Bar */
.rates-action-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
</style>

<!-- Content Header -->
<section class="content-header">
    <ol class="breadcrumb">
        <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Rate Saya</li>
    </ol>
</section>

<!-- Main content (Full Width) -->
<section class="content">
    <div class="row">
        <div class="col-md-12">

            <!-- Flash Notification (hanya error/warning; sukses tidak ditampilkan) -->
            <?php if ($this->session->flashdata('f_message') && $this->session->flashdata('f_type') !== 'success'): ?>
                <div class="alert alert-<?= $this->session->flashdata('f_type') ?: 'info'; ?> alert-dismissible" style="border-radius: 8px;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?= $this->session->flashdata('f_message'); ?>
                </div>
            <?php endif; ?>

            <!-- Page Title Area (Style My Tasks) -->
            <div class="rates-header-area">
                <h1>Rate Saya</h1>
                <p>Kelola dan perbarui tarif layanan endorsement & kolaborasi Anda. Harga langsung aktif untuk kebutuhan dealing campaign admin.</p>
            </div>

            <!-- Arsip PDF Lama (Jika Ada) -->
            <?php if (!empty($kol->rate_card_file)): ?>
            <div class="rates-archive-bar">
                <div>
                    <h4 class="rates-archive-title">Arsip Dokumen PDF Tersimpan</h4>
                    <p class="rates-archive-sub">File PDF rate card sebelumnya tetap tersimpan sebagai lampiran arsip akun Anda.</p>
                </div>
                <div>
                    <a href="<?= base_url('uploads/kols/' . trim($kol->rate_card_file)); ?>" target="_blank" class="btn btn-sm btn-default btn-flat" style="border-radius: 6px; font-weight: 600;">
                        Lihat Dokumen PDF
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Form Card -->
            <form action="<?= site_url('administrator/kols/my_rates_save'); ?>" method="POST" id="form_my_rates">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="rates-card">
                    <?php if (empty($rate_platforms)): ?>
                        <div class="callout callout-info" style="border-radius: 8px; margin-bottom: 0;">
                            <?php if (!empty($has_any_platform)): ?>
                            <h4 style="margin-top: 0;">Platform belum tersinkron</h4>
                            <p style="margin-bottom: 0;">Akun platform Anda belum memiliki data followers (belum pernah di-scrape). Minta admin menjalankan scrape dulu, setelah itu form rate per platform akan muncul di sini.</p>
                            <?php else: ?>
                            <h4 style="margin-top: 0;">Belum ada platform tertaut</h4>
                            <p style="margin-bottom: 0;">Tautkan dulu akun platform Anda (TikTok, Instagram, YouTube, dll.) agar bisa mengisi rate per platform. Hubungi admin jika butuh bantuan.</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                    <p style="color: #64748b; font-size: 12px; margin: 0 0 14px 0;">
                        Klik tiap platform untuk membuka dan mengisi tarif per tipe konten. Kosongkan sel jika tidak menyediakan layanan tersebut di platform itu.
                    </p>
                    <div class="panel-group" id="rates_platform_accordion" role="tablist" aria-multiselectable="true">
                        <?php foreach ($rate_platforms as $idx => $pf): ?>
                        <?php
                            $badge_class = $pf->rates_filled >= $pf->rates_total ? 'done' : ($pf->rates_filled > 0 ? 'partial' : 'empty');
                            $badge_text = $pf->rates_filled . '/' . $pf->rates_total . ' terisi';
                            $followers_label = (int)$pf->followers >= 1000000
                                ? rtrim(rtrim(number_format($pf->followers / 1000000, 1, ',', '.'), '0'), ',') . ' jt'
                                : number_format((int)$pf->followers, 0, ',', '.');
                        ?>
                        <div class="rates-platform-panel">
                            <a class="rates-platform-toggle" role="button" data-toggle="collapse"
                               data-parent="#rates_platform_accordion"
                               href="#rates_pf_<?= (int)$pf->kol_platform_id; ?>"
                               aria-expanded="<?= $idx === 0 ? 'true' : 'false'; ?>"
                               aria-controls="rates_pf_<?= (int)$pf->kol_platform_id; ?>"
                               data-pf="<?= (int)$pf->kol_platform_id; ?>">
                                <i class="fa fa-chevron-down rates-platform-caret"></i>
                                <span>
                                    <span class="rates-platform-name"><?= _ent($pf->platform_name ?? 'Platform'); ?></span><br>
                                    <span class="rates-platform-user">@<?= _ent($pf->username); ?> · <?= $followers_label; ?> followers</span>
                                </span>
                                <span class="rates-platform-meta">
                                    <?php if (empty($pf->last_synced_at)): ?>
                                    <span class="rates-badge nosync">belum sync</span>
                                    <?php endif; ?>
                                    <span class="rates-badge <?= $badge_class; ?>"><?= $badge_text; ?></span>
                                    <?php if ($pf->rates_min !== null): ?>
                                    <span class="rates-min-hint">mulai Rp <?= number_format($pf->rates_min, 0, ',', '.'); ?></span>
                                    <?php endif; ?>
                                </span>
                            </a>
                            <div id="rates_pf_<?= (int)$pf->kol_platform_id; ?>" class="panel-collapse collapse <?= $idx === 0 ? 'in' : ''; ?>" role="tabpanel">
                                <div style="padding: 4px 18px 18px 18px;">
                                    <div class="row">
                                        <?php $type_slugs = kol_rate_type_slugs(); ?>
                                        <?php foreach ($rate_types as $rtype => $rmeta): ?>
                                        <?php $rslug = array_search($rtype, $type_slugs); $cur_val = isset($rate_map[$pf->kol_platform_id][$rtype]) ? $rate_map[$pf->kol_platform_id][$rtype] : ''; ?>
                                        <div class="col-md-6">
                                            <div class="rate-box">
                                                <label class="rate-label-title"><?= _ent($rtype); ?></label>
                                                <span class="rate-label-desc"><?= _ent($rmeta['desc']); ?></span>
                                                <div class="rate-input-group">
                                                    <span class="rate-input-addon">Rp</span>
                                                    <input type="text" name="rates[<?= (int)$pf->kol_platform_id; ?>][<?= _ent($rslug); ?>]" class="rate-control money"
                                                           value="<?= $cur_val !== '' ? number_format((float)$cur_val, 0, ',', '.') : '' ?>"
                                                           placeholder="Contoh: 4.500.000">
                                                </div>
                                                <span class="rate-help-note">Kosongkan jika tidak menyediakan di <?= _ent($pf->platform_name ?? 'platform ini'); ?>.</span>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Bottom Action Bar -->
                <div class="rates-action-bar">
                    <a href="<?= site_url('administrator/dashboard'); ?>" class="btn btn-default btn-flat" style="border-radius: 6px; font-weight: 600;">
                        Kembali ke Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary btn-flat" id="btn_submit" style="border-radius: 6px; padding: 8px 24px; font-weight: 700; background: #0f172a; border-color: #0f172a;">
                        Simpan Perubahan Rate
                    </button>
                </div>
            </form>

        </div>
    </div>
</section>

<script>
$(document).ready(function(){
    // Masking input uang (rupiah)
    $('.money').mask('000.000.000.000', {reverse: true});

    // Remember expanded platform panels across reloads
    var storeKey = 'my_rates_open_panels';
    function readOpen() {
        try { return JSON.parse(localStorage.getItem(storeKey) || '[]'); }
        catch (e) { return []; }
    }
    var openIds = readOpen();
    if (openIds.length) {
        $('#rates_platform_accordion .panel-collapse').removeClass('in');
        $.each(openIds, function(i, pid) {
            $('#rates_pf_' + pid).addClass('in');
        });
    }
    $('#rates_platform_accordion').on('shown.bs.collapse hidden.bs.collapse', function() {
        var ids = [];
        $('#rates_platform_accordion .panel-collapse.in').each(function() {
            var id = $(this).attr('id') || '';
            if (id.indexOf('rates_pf_') === 0) ids.push(id.replace('rates_pf_', ''));
        });
        try { localStorage.setItem(storeKey, JSON.stringify(ids)); } catch (e) {}
    });

    // Indikator loading saat submit
    $('#form_my_rates').on('submit', function() {
        $('#btn_submit').prop('disabled', true).text('Menyimpan...');
    });
});
</script>
