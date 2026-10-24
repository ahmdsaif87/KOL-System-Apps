<page backtop="0" backbottom="0" backleft="0" backright="0">

    <!-- ══ PAGE FOOTER ═══════════════════════════════════════════════ -->
    <page_footer>
        <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; border-top: 1px solid #cbd5e1; padding-top: 5px;">
            <tr>
                <td style="width: 70%; text-align: left; font-size: 7.5px; color: #94a3b8;">
                    <b>CONFIDENTIAL</b> &bull; Dokumen Resmi KOL Performance Card &bull; Dicetak: <?= date('d M Y, H:i'); ?> WIB
                </td>
                <td style="width: 30%; text-align: right; font-size: 7.5px; color: #64748b;">
                    Halaman [[page_cu]] dari [[page_nb]]
                </td>
            </tr>
        </table>
    </page_footer>

    <style type="text/css">
        * {
            font-family: helvetica;
            font-size: 9px;
            color: #334155;
        }

        table {
            border-collapse: collapse;
        }

        /* ── Top Brand Bar ───────────────────── */
        .brand-bar {
            width: 100%;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        /* ── Profile Header Card (Flat 4-Column Table) ── */
        .profile-card {
            width: 100%;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            margin-bottom: 10px;
        }

        .kol-name {
            font-size: 13.5px;
            font-weight: bold;
            color: #0f172a;
        }

        .kol-tier-badge {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            font-size: 7.5px;
            font-weight: bold;
            padding: 1px 5px;
            text-transform: uppercase;
        }

        .kol-meta-row {
            font-size: 8px;
            color: #64748b;
            margin-top: 3px;
        }

        .kol-badge-cat {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 7.5px;
            padding: 1px 5px;
        }

        /* ── Section Title Bar ───────────────── */
        .section-bar {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .section-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 3.5px solid #0f172a;
            padding-left: 6px;
        }

        /* ── Data Tables ─────────────────────── */
        .data-table {
            width: 100%;
            margin-bottom: 6px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 8px;
            letter-spacing: 0.3px;
            border: 1px solid #0f172a;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 8.5px;
            vertical-align: middle;
        }

        .platform-name {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
        }
        .platform-handle {
            font-size: 8px;
            color: #64748b;
        }
        .sync-tag {
            font-size: 7px;
            color: #94a3b8;
        }

        .stat-val {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
        }
        .stat-er {
            font-size: 9.5px;
            font-weight: bold;
            color: #059669;
        }

        .no-data {
            font-size: 8.5px;
            color: #94a3b8;
            text-align: center;
            padding: 10px;
            border: 1px dashed #cbd5e1;
            background-color: #f8fafc;
            margin-bottom: 6px;
        }
    </style>

    <!-- ══ 1. TOP BRAND & DOCUMENT BAR ═══════════════════════════════ -->
    <table border="0" cellspacing="0" cellpadding="0" class="brand-bar">
        <tr>
            <td style="width: 65%; text-align: left; vertical-align: middle;">
                <?php $logo_path = FCPATH . 'asset/logo.png'; ?>
                <?php if (file_exists($logo_path)): ?>
                    <img src="<?= $logo_path; ?>" height="18" style="vertical-align: middle;" />
                <?php else: ?>
                    <span style="font-size: 13px; font-weight: bold; color: #0f172a;">KOLAB<span style="color:#2563eb;">.ID</span></span>
                <?php endif; ?>
                <span style="font-size: 8px; color: #64748b; padding-left: 6px;">| &nbsp; CREATOR PERFORMANCE &amp; RATE CARD</span>
            </td>
            <td style="width: 35%; text-align: right; vertical-align: middle; font-size: 8px; color: #64748b;">
                <b>KOL ID:</b> #<?= _ent($kol->id); ?> &nbsp;&bull;&nbsp; <?= date('d F Y'); ?>
            </td>
        </tr>
    </table>

    <!-- ══ 2. CREATOR PROFILE CARD (FLAT TABLE, NO NESTING) ══════════ -->
    <?php
    $total_reach = 0;
    if (!empty($platform_stats)) {
        foreach ($platform_stats as $ps) {
            $total_reach += (int)$ps->followers;
        }
    }
    $has_avatar = false;
    $avatar_file = !empty($kol->avatar_url) ? FCPATH . 'uploads/kols/' . $kol->avatar_url : '';
    if (!empty($kol->avatar_url) && is_file($avatar_file)) {
        $has_avatar = true;
    }
    ?>
    <table border="0" cellspacing="0" cellpadding="0" class="profile-card">
        <tr>
            <!-- Col 1: Avatar (10%) -->
            <td style="width: 10%; text-align: center; vertical-align: middle; padding: 7px 5px;">
                <?php if ($has_avatar): ?>
                    <img src="<?= $avatar_file; ?>" width="38" height="38" style="border: 1px solid #cbd5e1;" />
                <?php else: ?>
                    <table border="0" cellspacing="0" cellpadding="0" align="center" style="width: 36px; height: 36px;">
                        <tr>
                            <td align="center" bgcolor="#f1f5f9" style="width: 36px; height: 36px; text-align: center; vertical-align: middle; color: #334155; font-size: 12px; font-weight: bold; border: 1px solid #cbd5e1;">
                                <?= strtoupper(substr($kol->name, 0, 2)); ?>
                            </td>
                        </tr>
                    </table>
                <?php endif; ?>
            </td>

            <!-- Col 2: Profile Info (60%) -->
            <td style="width: 60%; text-align: left; vertical-align: middle; padding: 7px 8px;">
                <div class="kol-name">
                    <?= _ent($kol->name); ?>
                    <?php if (!empty($kol->tier_name)): ?>
                        &nbsp;<span class="kol-tier-badge"><?= _ent($kol->tier_name); ?></span>
                    <?php endif; ?>
                </div>

                <div class="kol-meta-row">
                    <?php
                    $meta_parts = [];
                    $loc_parts = array_filter([$kol->city ?? '', $kol->province ?? '']);
                    if (!empty($loc_parts)) {
                        $meta_parts[] = 'Lokasi: <b style="color:#0f172a;">' . _ent(implode(', ', $loc_parts)) . '</b>';
                    }
                    if (!empty($kol->gender)) {
                        $meta_parts[] = 'Gender: <b style="color:#0f172a;">' . _ent($kol->gender) . '</b>';
                    }
                    $status_label = !empty($kol->status) ? strtoupper($kol->status) : 'ACTIVE';
                    $meta_parts[] = 'Status: <b style="color:#059669;">' . _ent($status_label) . '</b>';
                    echo implode('&nbsp;&nbsp;&bull;&nbsp;&nbsp;', $meta_parts);
                    ?>
                </div>

                <?php if (!empty($kol->categories)): ?>
                    <div style="margin-top: 3px;">
                        <span style="color:#64748b; font-size:7.5px;">Kategori:</span>&nbsp;
                        <?php foreach ($kol->categories as $c): ?>
                            <span class="kol-badge-cat"><?= _ent($c); ?></span>&nbsp;
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </td>

            <!-- Col 3: Total Reach Metric (15%) -->
            <td align="center" style="width: 15%; text-align: center; vertical-align: middle; padding: 7px 4px; border-left: 1px solid #e2e8f0; background-color: #fafbfc;">
                <span style="font-size: 7px; color: #64748b; text-transform: uppercase; font-weight: bold;">Total Reach</span><br/>
                <span style="font-size: 11px; font-weight: bold; color: #0f172a;"><?= number_format($total_reach); ?></span><br/>
                <span style="font-size: 6.5px; color: #94a3b8;">Followers / Subs</span>
            </td>

            <!-- Col 4: Platform Count Metric (15%) -->
            <td align="center" style="width: 15%; text-align: center; vertical-align: middle; padding: 7px 4px; border-left: 1px solid #e2e8f0; background-color: #fafbfc;">
                <span style="font-size: 7px; color: #64748b; text-transform: uppercase; font-weight: bold;">Akun Aktif</span><br/>
                <span style="font-size: 11px; font-weight: bold; color: #2563eb;"><?= count($platform_stats); ?> Platform</span><br/>
                <span style="font-size: 6.5px; color: #94a3b8;">Terhubung</span>
            </td>
        </tr>
    </table>

    <!-- ══ 3. PLATFORM PERFORMANCE METRICS ═══════════════════════════ -->
    <table border="0" cellspacing="0" cellpadding="0" class="section-bar">
        <tr>
            <td style="width: 70%; text-align: left;" class="section-title">
                Ringkasan Metrik Sosial Media (30 Hari Terakhir)
            </td>
            <td style="width: 30%; text-align: right; font-size: 7.5px; color: #64748b; vertical-align: middle;">
                Sumber: Apify Live Engine
            </td>
        </tr>
    </table>

    <?php if (!empty($platform_stats)): ?>
    <table border="0" cellspacing="0" cellpadding="0" class="data-table">
        <thead>
            <tr>
                <th align="left" style="width: 37%; text-align: left;">Platform & Username</th>
                <th align="right" style="width: 21%; text-align: right;">Followers / Subs</th>
                <th align="right" style="width: 21%; text-align: right;">Rata-rata Views (30D)</th>
                <th align="right" style="width: 21%; text-align: right;">Engagement Rate (ER)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($platform_stats as $i => $ps): ?>
                <?php
                $code_lower = strtolower($ps->platform_code);
                $brand_color = '#2563eb';
                if ($code_lower === 'tiktok')                              { $brand_color = '#111827'; }
                elseif ($code_lower === 'instagram')                       { $brand_color = '#e1306c'; }
                elseif ($code_lower === 'youtube')                         { $brand_color = '#ef4444'; }
                elseif ($code_lower === 'twitter' || $code_lower === 'x') { $brand_color = '#0ea5e9'; }
                elseif ($code_lower === 'facebook')                        { $brand_color = '#2563eb'; }

                $row_bg = ($i % 2 === 1) ? ' bgcolor="#f8fafc"' : '';
                ?>
                <tr<?= $row_bg; ?>>
                    <td align="left" style="width: 37%; border-left: 3.5px solid <?= $brand_color; ?>;">
                        <span class="platform-name"><?= _ent($ps->platform_name); ?></span><br/>
                        <span class="platform-handle">@<?= _ent($ps->username); ?></span><br/>
                        <span class="sync-tag">
                            <?= !empty($ps->last_synced_at) ? 'Tersinkron ' . date('d M Y', strtotime($ps->last_synced_at)) : 'Belum tersinkron'; ?>
                        </span>
                    </td>
                    <td align="right" style="width: 21%; text-align: right;">
                        <span class="stat-val"><?= number_format((int)$ps->followers); ?></span>
                    </td>
                    <td align="right" style="width: 21%; text-align: right;">
                        <span class="stat-val"><?= number_format((float)$ps->avg_views, 0); ?></span>
                    </td>
                    <td align="right" style="width: 21%; text-align: right;">
                        <span class="stat-er"><?= number_format((float)$ps->avg_er * 100, 2); ?>%</span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="no-data">Belum ada akun sosial media yang terhubung pada profil KOL ini.</div>
    <?php endif; ?>

    <!-- ══ 4. RATE CARD LAYANAN (COMMERCIAL RATES) ═══════════════════ -->
    <table border="0" cellspacing="0" cellpadding="0" class="section-bar">
        <tr>
            <td style="width: 70%; text-align: left;" class="section-title">
                Rate Card Layanan Komersial
            </td>
            <td style="width: 30%; text-align: right; font-size: 7.5px; color: #64748b; vertical-align: middle;">
                Tarif resmi per konten
            </td>
        </tr>
    </table>

    <?php
    $rm_types = isset($rate_matrix['types']) ? $rate_matrix['types'] : [];
    $rm_plats = isset($rate_matrix['platforms']) ? $rate_matrix['platforms'] : [];
    $rm_rates = isset($rate_matrix['rates']) ? $rate_matrix['rates'] : [];
    ?>
    <?php if (!empty($rm_plats) && !empty($rm_types)): ?>
    <?php
    $col_count = count($rm_types);
    $plat_col_w = 37;
    $rate_col_w = $col_count > 0 ? (63 / $col_count) : 63;
    ?>
    <table border="0" cellspacing="0" cellpadding="0" class="data-table">
        <thead>
            <tr>
                <th align="left" style="width: <?= $plat_col_w; ?>%; text-align: left;">Platform Akun</th>
                <?php foreach ($rm_types as $rtype => $rmeta): ?>
                <th align="right" style="width: <?= $rate_col_w; ?>%; text-align: right;"><?= _ent($rtype); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rm_plats as $i => $rp): ?>
            <tr<?= ($i % 2 === 1) ? ' bgcolor="#f8fafc"' : ''; ?>>
                <td style="width: <?= $plat_col_w; ?>%;">
                    <span style="font-weight: bold; color: #0f172a; font-size: 9.5px;"><?= _ent($rp->platform_name ?? 'Rate'); ?></span>
                    <?php if (!empty($rp->username)): ?>
                        <br/><span style="color: #64748b; font-size: 8px;">@<?= _ent($rp->username); ?></span>
                    <?php endif; ?>
                </td>
                <?php foreach ($rm_types as $rtype => $rmeta): ?>
                <?php $rv = isset($rm_rates[$rp->kol_platform_id][$rtype]) ? (float)$rm_rates[$rp->kol_platform_id][$rtype] : null; ?>
                <td align="right" style="width: <?= $rate_col_w; ?>%; text-align: right; font-size: 8.5px; <?= ($rv !== null && $rv > 0) ? 'font-weight: bold; color: #0f172a;' : 'color: #94a3b8;'; ?>">
                    <?= ($rv !== null && $rv > 0) ? 'Rp ' . number_format($rv, 0, ',', '.') : '-'; ?>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="no-data">KOL belum mengatur tarif rate card komersial.</div>
    <?php endif; ?>

    <!-- ══ 5. 6-MONTH GROWTH TREND ═══════════════════════════════════ -->
    <table border="0" cellspacing="0" cellpadding="0" class="section-bar">
        <tr>
            <td style="width: 70%; text-align: left;" class="section-title">
                Riwayat Pertumbuhan 6 Bulan Terakhir
            </td>
            <td style="width: 30%; text-align: right; font-size: 7.5px; color: #64748b; vertical-align: middle;">
                Tren bulanan
            </td>
        </tr>
    </table>

    <?php if (!empty($trend_data)): ?>
        <?php foreach ($trend_data as $platform_code => $platform): ?>
            <div style="font-size: 8.5px; font-weight: bold; color: #1e293b; margin-top: 4px; margin-bottom: 2px;">
                Platform: <?= _ent($platform['name']); ?>
            </div>
            <table border="0" cellspacing="0" cellpadding="0" class="data-table">
                <thead>
                    <tr>
                        <th align="left" style="width: 37%; text-align: left;">Bulan</th>
                        <th align="right" style="width: 21%; text-align: right;">Followers</th>
                        <th align="right" style="width: 21%; text-align: right;">Rata-rata Views</th>
                        <th align="right" style="width: 21%; text-align: right;">Rata-rata ER</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($platform['data'] as $i => $point): ?>
                        <?php $month_label = date('M Y', strtotime($point['month'] . '-01')); ?>
                        <tr<?= ($i % 2 === 1) ? ' bgcolor="#f8fafc"' : ''; ?>>
                            <td align="left" style="width: 37%; font-weight: bold; color: #334155;"><?= $month_label; ?></td>
                            <td align="right" style="width: 21%; text-align: right; color: #334155;"><?= number_format($point['followers']); ?></td>
                            <td align="right" style="width: 21%; text-align: right; color: #334155;"><?= number_format($point['avg_views'], 0); ?></td>
                            <td align="right" style="width: 21%; text-align: right; font-weight: bold; color: #059669;"><?= number_format($point['avg_er'], 2); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-data">Belum ada riwayat snapshot berkala. Data akan otomatis terakumulasi setiap cron scraping berjalan.</div>
    <?php endif; ?>

</page>