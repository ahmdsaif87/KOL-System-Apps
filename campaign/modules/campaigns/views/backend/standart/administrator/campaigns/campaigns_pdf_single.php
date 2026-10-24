<style type="text/css">
    * {
        font-family: Arial, Helvetica, sans-serif;
        color: #2c3e50;
    }
    table {
        border-collapse: collapse;
    }
    .header-table {
        width: 100%;
        border-bottom: 2px solid #1F4B6E;
        padding-bottom: 6px;
        margin-bottom: 8px;
    }
    .main-title {
        font-size: 15px;
        font-weight: bold;
        color: #1F4B6E;
    }
    .sub-title {
        font-size: 9px;
        color: #7f8c8d;
        margin-top: 2px;
    }
    .section-header {
        font-size: 10px;
        font-weight: bold;
        color: #1F4B6E;
        background-color: #eaf2f8;
        padding: 4px 6px;
        border-left: 3px solid #1F4B6E;
        margin-top: 8px;
        margin-bottom: 4px;
    }
    .info-table {
        width: 100%;
    }
    .info-table td {
        font-size: 8.5px;
        padding: 3px 5px;
        vertical-align: top;
        border-bottom: 1px solid #ebedef;
    }
    .info-label {
        font-weight: bold;
        color: #5d6d7e;
        background-color: #f8f9fa;
    }
    .kpi-table {
        width: 100%;
        margin-top: 4px;
        margin-bottom: 6px;
    }
    .kpi-cell {
        background-color: #fdfefe;
        border: 1px solid #d5dbdb;
        text-align: center;
        padding: 6px 4px;
        vertical-align: middle;
    }
    .data-table {
        width: 100%;
        border: 1px solid #d5dbdb;
        margin-top: 4px;
    }
    .data-table th {
        background-color: #1F4B6E;
        color: #ffffff;
        font-size: 8px;
        font-weight: bold;
        text-align: center;
        padding: 5px 3px;
        border: 1px solid #1a3e5c;
    }
    .data-table td {
        font-size: 7.5px;
        padding: 4px 3px;
        border: 1px solid #eaeded;
        vertical-align: middle;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .badge {
        font-size: 7px;
        font-weight: bold;
        padding: 1px 3px;
        border-radius: 2px;
    }
    .badge-success { background-color: #d4edda; color: #155724; }
    .badge-warning { background-color: #fff3cd; color: #856404; }
    .badge-info { background-color: #d1ecf1; color: #0c5460; }
    .badge-danger { background-color: #f8d7da; color: #721c24; }
    .badge-default { background-color: #e2e3e5; color: #383d41; }
    .page-footer-text {
        font-size: 7.5px;
        color: #95a5a6;
    }
</style>

<page orientation="l" format="A4" backtop="7mm" backbottom="8mm" backleft="7mm" backright="7mm">
    <!-- Header -->
    <table class="header-table" style="width: 100%;">
        <tr>
            <td style="width: 72%;">
                <div class="main-title"><?= htmlspecialchars($campaign->name); ?></div>
                <div class="sub-title">LAPORAN DETAIL &amp; REALISASI KINERJA CAMPAIGN</div>
            </td>
            <td style="width: 28%; text-align: right; vertical-align: middle;">
                <span style="font-size: 8.5px; color: #7f8c8d;">Dicetak: <?= date('d M Y, H:i'); ?> WIB</span>
            </td>
        </tr>
    </table>

    <!-- Info & Overview Grid -->
    <table class="info-table" style="width: 100%;">
        <tr>
            <td class="info-label" style="width: 15%;">Brand</td>
            <td style="width: 35%;"><?= htmlspecialchars($campaign->brand_name ?: '-'); ?></td>
            <td class="info-label" style="width: 15%;">Tipe Campaign</td>
            <td style="width: 35%;"><strong><?= htmlspecialchars($campaign->campaign_type); ?></strong></td>
        </tr>
        <tr>
            <td class="info-label" style="width: 15%;">Client</td>
            <td style="width: 35%;"><?= htmlspecialchars($campaign->client_name ?: '-'); ?></td>
            <td class="info-label" style="width: 15%;">Status</td>
            <td style="width: 35%;">
                <?php 
                    $st = $campaign->status;
                    $badge_cls = ($st == 'ACTIVE') ? 'badge-success' : (($st == 'COMPLETED') ? 'badge-info' : (($st == 'DRAFT') ? 'badge-warning' : 'badge-danger'));
                ?>
                <span class="badge <?= $badge_cls ?>"><?= $st ?></span>
            </td>
        </tr>
        <tr>
            <td class="info-label" style="width: 15%;">Periode</td>
            <td style="width: 35%;"><?= date('d M Y', strtotime($campaign->start_date)); ?> s/d <?= date('d M Y', strtotime($campaign->end_date)); ?></td>
            <td class="info-label" style="width: 15%;">PIC Operasional</td>
            <td style="width: 35%;"><?= htmlspecialchars((isset($campaign->pic_user_name) && $campaign->pic_user_name) ? $campaign->pic_user_name : '-'); ?></td>
        </tr>
        <tr>
            <td class="info-label" style="width: 15%;">Produk Terkait</td>
            <td style="width: 85%;" colspan="3">
                <?php if (!empty($products)): ?>
                    <?php 
                        $prod_names = array_map(function($p) {
                            return htmlspecialchars($p->product_name . ' (' . ($p->product_sku ?: 'SKU') . ')');
                        }, $products);
                        echo implode(', ', $prod_names);
                    ?>
                <?php else: ?>
                    <span style="color: #999;">-</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php 
            $msg_parts = [];
            if (!empty($campaign->objective)) $msg_parts[] = 'Objective: ' . $campaign->objective;
            if (!empty($campaign->key_message)) $msg_parts[] = 'Key Message: ' . $campaign->key_message;
        ?>
        <?php if (!empty($msg_parts)): ?>
        <tr>
            <td class="info-label" style="width: 15%;">Objective / Key Msg</td>
            <td style="width: 85%;" colspan="3">
                <?= htmlspecialchars(implode(' | ', $msg_parts)); ?>
            </td>
        </tr>
        <?php endif; ?>
    </table>

    <!-- Financial & KPI Summary Cards -->
    <div class="section-header">RINGKASAN ANGGARAN &amp; PENCAPAIAN KPI</div>
    <table class="kpi-table" style="width: 100%;">
        <tr>
            <td class="kpi-cell" style="width: 25%; border-top: 3px solid #007bff;">
                <span style="font-size: 7.5px; font-weight: bold; color: #7f8c8d;">TOTAL BUDGET</span><br>
                <span style="font-size: 11px; font-weight: bold; color: #1F4B6E;">Rp <?= number_format((float)$campaign->budget, 0, ',', '.'); ?></span><br>
                <span style="font-size: 7px; color: #95a5a6;">Alokasi Plafon Anggaran</span>
            </td>
            <td class="kpi-cell" style="width: 25%; border-top: 3px solid #28a745;">
                <span style="font-size: 7.5px; font-weight: bold; color: #7f8c8d;">REALISASI DEAL (SPENT)</span><br>
                <span style="font-size: 11px; font-weight: bold; color: #28a745;">Rp <?= number_format($total_spent, 0, ',', '.'); ?></span><br>
                <span style="font-size: 7px; color: #95a5a6;"><?= $campaign->budget > 0 ? round(($total_spent / (float)$campaign->budget) * 100, 1) . '% Terpakai' : '-' ?></span>
            </td>
            <td class="kpi-cell" style="width: 25%; border-top: 3px solid <?= ($remaining_budget >= 0) ? '#17a2b8' : '#dc3545' ?>;">
                <span style="font-size: 7.5px; font-weight: bold; color: #7f8c8d;">SISA ANGGARAN</span><br>
                <span style="font-size: 11px; font-weight: bold; color: <?= ($remaining_budget >= 0) ? '#17a2b8' : '#dc3545' ?>;">Rp <?= number_format($remaining_budget, 0, ',', '.'); ?></span><br>
                <span style="font-size: 7px; color: #95a5a6;"><?= ($remaining_budget >= 0) ? 'Tersedia' : 'Over Budget' ?></span>
            </td>
            <td class="kpi-cell" style="width: 25%; border-top: 3px solid #6f42c1;">
                <span style="font-size: 7.5px; font-weight: bold; color: #7f8c8d;">TOTAL DELIVERABLES</span><br>
                <span style="font-size: 11px; font-weight: bold; color: #6f42c1;"><?= $completed_vts; ?> / <?= $total_vts; ?> VT</span><br>
                <span style="font-size: 7px; color: #95a5a6;"><?= count($deals); ?> KOL Terlibat</span>
            </td>
        </tr>
    </table>

    <!-- Target vs Realization Performance -->
    <table class="data-table" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 28%;">METRIK PERFORMA</th>
                <th style="width: 24%;">TARGET CAMPAIGN</th>
                <th style="width: 24%;">AKTUAL / REALISASI</th>
                <th style="width: 24%;">PENCAPAIAN / STATUS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold; background: #fafafa; width: 28%;">Total Views</td>
                <td class="text-center" style="width: 24%;"><?= number_format((int)$campaign->target_views, 0, ',', '.'); ?></td>
                <td class="text-center" style="font-weight: bold; color: #1F4B6E; width: 24%;"><?= number_format($total_actual_views, 0, ',', '.'); ?></td>
                <td class="text-center" style="width: 24%;">
                    <?php $vpct = ($campaign->target_views > 0) ? round(($total_actual_views / (int)$campaign->target_views) * 100, 1) : 0; ?>
                    <span class="badge <?= $vpct >= 100 ? 'badge-success' : ($vpct >= 50 ? 'badge-info' : 'badge-warning') ?>"><?= $vpct ?>%</span>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #fafafa; width: 28%;">Average Engagement Rate (ER)</td>
                <td class="text-center" style="width: 24%;"><?= number_format((float)$campaign->target_er * 100, 2); ?>%</td>
                <td class="text-center" style="font-weight: bold; color: #1F4B6E; width: 24%;"><?= number_format($avg_actual_er, 2); ?>%</td>
                <td class="text-center" style="width: 24%;">
                    <?php 
                        $t_er_pct = (float)$campaign->target_er * 100;
                        $er_pct = ($t_er_pct > 0) ? round(($avg_actual_er / $t_er_pct) * 100, 1) : 0;
                    ?>
                    <span class="badge <?= $er_pct >= 100 ? 'badge-success' : ($er_pct >= 50 ? 'badge-info' : 'badge-warning') ?>"><?= $er_pct ?>%</span>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #fafafa; width: 28%;">Cost Per View (CPV)</td>
                <td class="text-center" style="width: 24%;">Rp <?= number_format($target_cpv, 0, ',', '.'); ?></td>
                <td class="text-center" style="font-weight: bold; color: #1F4B6E; width: 24%;"><?= $actual_cpv > 0 ? 'Rp ' . number_format($actual_cpv, 0, ',', '.') : '-'; ?></td>
                <td class="text-center" style="width: 24%;">
                    <?php if ($target_cpv > 0 && $actual_cpv > 0): ?>
                        <span class="badge <?= $actual_cpv <= $target_cpv ? 'badge-success' : 'badge-warning' ?>"><?= $actual_cpv <= $target_cpv ? 'ON-TARGET' : 'OVER BUDGET' ?></span>
                    <?php else: ?>
                        <span style="color: #999;">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #fafafa; width: 28%;">Cost Per Engagement (CPE)</td>
                <td class="text-center" style="width: 24%;">Rp <?= number_format($target_cpe, 0, ',', '.'); ?></td>
                <td class="text-center" style="font-weight: bold; color: #1F4B6E; width: 24%;"><?= $actual_cpe > 0 ? 'Rp ' . number_format($actual_cpe, 0, ',', '.') : '-'; ?></td>
                <td class="text-center" style="width: 24%;">
                    <?php if ($target_cpe > 0 && $actual_cpe > 0): ?>
                        <span class="badge <?= $actual_cpe <= $target_cpe ? 'badge-success' : 'badge-warning' ?>"><?= $actual_cpe <= $target_cpe ? 'ON-TARGET' : 'OVER BUDGET' ?></span>
                    <?php else: ?>
                        <span style="color: #999;">-</span>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Assigned KOLs Table -->
    <div class="section-header">DAFTAR KOL YANG TERLIBAT (ASSIGNED KOLS)</div>
    <table class="data-table" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>
                <th style="width: 22%;">NAMA KOL</th>
                <th style="width: 12%;">TIER</th>
                <th style="width: 18%;">KATEGORI</th>
                <th style="width: 16%;">DEAL AMOUNT</th>
                <th style="width: 7%;">TOTAL VT</th>
                <th style="width: 7%;">DONE VT</th>
                <th style="width: 7%;">STATUS</th>
                <th style="width: 7%;">SCORE</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($deals)): ?>
                <?php $kno = 1; foreach ($deals as $d): ?>
                <tr>
                    <td class="text-center" style="width: 4%;"><?= $kno++; ?></td>
                    <td style="font-weight: bold; width: 22%;"><?= htmlspecialchars($d->kol_name ?: '-'); ?></td>
                    <td class="text-center" style="width: 12%;"><?= htmlspecialchars($d->tier_name ?: '-'); ?></td>
                    <td style="width: 18%;"><?= htmlspecialchars($d->kol_categories ?: '-'); ?></td>
                    <td class="text-right" style="font-weight: bold; width: 16%;">Rp <?= number_format((float)$d->deal_amount, 0, ',', '.'); ?></td>
                    <td class="text-center" style="width: 7%;"><?= (int)$d->jumlah_vt; ?></td>
                    <td class="text-center" style="width: 7%;"><?= (int)$d->task_completed_count; ?></td>
                    <td class="text-center" style="width: 7%;">
                        <span class="badge <?= ($d->status == 'ACTIVE' || $d->status == 'DONE') ? 'badge-success' : 'badge-default' ?>"><?= $d->status ?></span>
                    </td>
                    <td class="text-center" style="width: 7%;"><?= $d->collaboration_score !== null ? number_format((float)$d->collaboration_score, 1) : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center" style="padding: 8px; color: #999;">Belum ada KOL yang ditugaskan pada campaign ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Video & Task Deliverables Performance Table -->
    <div class="section-header">RINCIAN KONTEN &amp; REALISASI METRIK VIDEO (DELIVERABLES)</div>
    <table class="data-table" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 3%;">NO</th>
                <th style="width: 4%;">#</th>
                <th style="width: 21%;">JUDUL TASK / KONTEN</th>
                <th style="width: 14%;">KOL</th>
                <th style="width: 8%;">PLATFORM</th>
                <th style="width: 8%;">EST. DATE</th>
                <th style="width: 8%;">STATUS</th>
                <th style="width: 9%;">VIEWS</th>
                <th style="width: 7%;">LIKES</th>
                <th style="width: 6%;">ER (%)</th>
                <th style="width: 6%;">CPV</th>
                <th style="width: 6%;">CPE</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tasks)): ?>
                <?php $tno = 1; foreach ($tasks as $t): ?>
                <?php 
                    $tb = (float)preg_replace('/[^0-9]/', '', (string)$t->budget);
                    $tv = (int)($t->views ?? 0);
                    $tl = (int)($t->likes ?? 0);
                    $tc = (int)($t->comments ?? 0);
                    $tsh = (int)($t->shares ?? 0);
                    $tsa = (int)($t->saves ?? 0);
                    $teng = $tl + $tc + $tsh + $tsa;
                    $ter = ($t->engagement_rate !== null) ? round((float)$t->engagement_rate * 100, 2) : ($tv > 0 ? round(($teng / $tv) * 100, 2) : 0);
                    $tcpv = ($tv > 0 && $tb > 0) ? round($tb / $tv, 2) : 0;
                    $tcpe = ($teng > 0 && $tb > 0) ? round($tb / $teng, 2) : 0;
                ?>
                <tr>
                    <td class="text-center" style="width: 3%;"><?= $tno++; ?></td>
                    <td class="text-center" style="font-weight: bold; width: 4%;"><?= htmlspecialchars($t->task_number); ?></td>
                    <td style="width: 21%;">
                        <strong><?= htmlspecialchars($t->title); ?></strong>
                        <?php if (!empty($t->variant_name)): ?>
                            <br><span style="font-size: 6.5px; color: #7f8c8d;"><?= htmlspecialchars($t->variant_name); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="width: 14%;"><?= htmlspecialchars($t->kol_name ?: '-'); ?></td>
                    <td class="text-center" style="width: 8%;"><?= htmlspecialchars($t->platform_name ?: '-'); ?></td>
                    <td class="text-center" style="width: 8%;"><?= date('d/m/Y', strtotime($t->estimated_posting_date)); ?></td>
                    <td class="text-center" style="width: 8%;">
                        <span class="badge <?= ($t->status == 'DONE') ? 'badge-success' : (($t->status == 'POSTED') ? 'badge-info' : 'badge-warning') ?>"><?= $t->status ?></span>
                    </td>
                    <td class="text-right" style="font-weight: bold; width: 9%;"><?= number_format($tv, 0, ',', '.'); ?></td>
                    <td class="text-right" style="width: 7%;"><?= number_format($tl, 0, ',', '.'); ?></td>
                    <td class="text-center" style="font-weight: bold; width: 6%;"><?= number_format($ter, 2); ?>%</td>
                    <td class="text-right" style="width: 6%;"><?= $tcpv > 0 ? number_format($tcpv, 0, ',', '.') : '-'; ?></td>
                    <td class="text-right" style="width: 6%;"><?= $tcpe > 0 ? number_format($tcpe, 0, ',', '.') : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12" class="text-center" style="padding: 8px; color: #999;">Belum ada konten / task yang terdaftar pada campaign ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <page_footer>
        <table style="width: 100%; border-top: 1px solid #d5dbdb; padding-top: 3px;">
            <tr>
                <td style="width: 50%;" class="page-footer-text">
                    KOL Management System &bull; <?= htmlspecialchars($campaign->name); ?>
                </td>
                <td style="width: 50%; text-align: right;" class="page-footer-text">
                    Halaman [[page_cu]] / [[page_nb]]
                </td>
            </tr>
        </table>
    </page_footer>
</page>
