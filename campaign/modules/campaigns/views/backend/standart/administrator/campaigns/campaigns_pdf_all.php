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
        padding-bottom: 5px;
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
    .data-table {
        width: 100%;
        border: 1px solid #d5dbdb;
        margin-top: 4px;
    }
    .data-table th {
        background-color: #1F4B6E;
        color: #ffffff;
        font-size: 7.5px;
        font-weight: bold;
        text-align: center;
        padding: 5px 2px;
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
        font-size: 6.5px;
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
    .sub-th {
        font-size: 6px;
        font-weight: normal;
        color: #d0e1f9;
    }
    .target-val {
        color: #6c757d;
        font-size: 6.5px;
    }
</style>

<page orientation="l" format="A4" backtop="7mm" backbottom="8mm" backleft="6mm" backright="6mm">
    <!-- Header -->
    <table class="header-table" style="width: 100%;">
        <tr>
            <td style="width: 70%;">
                <div class="main-title">LAPORAN REKAPITULASI CAMPAIGN</div>
                <div class="sub-title">KOL MANAGEMENT SYSTEM &bull; RINGKASAN SELURUH CAMPAIGN &amp; PENCAPAIAN TARGET</div>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: middle;">
                <span style="font-size: 8.5px; color: #7f8c8d;">Dicetak: <?= date('d M Y, H:i'); ?> WIB &bull; Total: <?= count($campaigns); ?> Campaign</span>
            </td>
        </tr>
    </table>

    <!-- All Campaigns Table -->
    <table class="data-table" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 3%;">NO</th>
                <th style="width: 11%;">NAME</th>
                <th style="width: 11%;">PRODUCTS</th>
                <th style="width: 8%;">BUDGET</th>
                <th style="width: 8%;">SISA BUDGET</th>
                <th style="width: 5%;">START</th>
                <th style="width: 5%;">END DATE</th>
                <th style="width: 5%;">STATUS</th>
                <th style="width: 5%;">TYPE</th>
                <th style="width: 8.5%;">VIEWS<br><span class="sub-th">(Actual / Target)</span></th>
                <th style="width: 7.5%;">ER (%)<br><span class="sub-th">(Actual / Target)</span></th>
                <th style="width: 6.5%;">CPV (RP)<br><span class="sub-th">(Actual / Target)</span></th>
                <th style="width: 6.5%;">CPE (RP)<br><span class="sub-th">(Actual / Target)</span></th>
                <th style="width: 5%;">CREATED BY</th>
                <th style="width: 5.5%;">PIC</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($campaigns)): ?>
                <?php $no = 1; foreach ($campaigns as $c): ?>
                <?php 
                    $budget = (float)$c->budget;
                    $spent = isset($spent_by_campaign[$c->id]) ? $spent_by_campaign[$c->id] : 0;
                    $remaining = $budget - $spent;

                    $t_views = (int)$c->target_views;
                    $act_views = isset($metrics_by_campaign[$c->id]) ? (int)$metrics_by_campaign[$c->id]->total_actual_views : 0;

                    $t_er = (float)$c->target_er * 100;
                    $act_er = isset($metrics_by_campaign[$c->id]) ? round((float)$metrics_by_campaign[$c->id]->avg_actual_er * 100, 2) : 0;

                    $t_cpv = isset($c->target_cpv) && $c->target_cpv !== null ? (float)$c->target_cpv : ($t_views > 0 ? round($budget / $t_views, 2) : 0);
                    $act_cpv = ($act_views > 0) ? round($spent / $act_views, 2) : 0;

                    $act_engagements = isset($metrics_by_campaign[$c->id]) ? (int)$metrics_by_campaign[$c->id]->total_actual_engagements : 0;
                    $t_cpe = isset($c->target_cpe) && $c->target_cpe !== null ? (float)$c->target_cpe : ($t_views > 0 && $t_er > 0 ? round($budget / ($t_views * ($t_er / 100)), 2) : 0);
                    $act_cpe = ($act_engagements > 0) ? round($spent / $act_engagements, 2) : 0;

                    $prods = isset($products_by_campaign[$c->id]) ? $products_by_campaign[$c->id] : [];

                    $st = $c->status;
                    $badge_cls = ($st == 'ACTIVE') ? 'badge-success' : (($st == 'COMPLETED') ? 'badge-info' : (($st == 'DRAFT') ? 'badge-warning' : 'badge-danger'));
                ?>
                <tr>
                    <td class="text-center" style="width: 3%;"><?= $no++; ?></td>
                    <td style="width: 11%;"><strong><?= htmlspecialchars($c->name); ?></strong></td>
                    <td style="width: 11%; font-size: 6.5px;">
                        <?php if (!empty($prods)): ?>
                            <?php foreach ($prods as $pi => $pname): ?>
                                <?= ($pi > 0 ? '<br>' : '') . '&bull; ' . htmlspecialchars($pname); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span style="color: #999;">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right" style="width: 8%; font-weight: bold;">Rp <?= number_format($budget, 0, ',', '.'); ?></td>
                    <td class="text-right" style="width: 8%; font-weight: bold; color: <?= ($remaining >= 0) ? '#17a2b8' : '#dc3545' ?>;">Rp <?= number_format($remaining, 0, ',', '.'); ?></td>
                    <td class="text-center" style="width: 5%;"><?= date('d/m/Y', strtotime($c->start_date)); ?></td>
                    <td class="text-center" style="width: 5%;"><?= date('d/m/Y', strtotime($c->end_date)); ?></td>
                    <td class="text-center" style="width: 5%;">
                        <span class="badge <?= $badge_cls ?>"><?= $st ?></span>
                    </td>
                    <td class="text-center" style="width: 5%;"><?= htmlspecialchars($c->campaign_type); ?></td>
                    <td class="text-center" style="width: 8.5%;">
                        <strong><?= number_format($act_views, 0, ',', '.'); ?></strong><br>
                        <span class="target-val">T: <?= number_format($t_views, 0, ',', '.'); ?></span>
                    </td>
                    <td class="text-center" style="width: 7.5%;">
                        <strong><?= number_format($act_er, 2); ?>%</strong><br>
                        <span class="target-val">T: <?= number_format($t_er, 2); ?>%</span>
                    </td>
                    <td class="text-center" style="width: 6.5%;">
                        <strong><?= $act_cpv > 0 ? number_format($act_cpv, 0, ',', '.') : '-'; ?></strong><br>
                        <span class="target-val">T: <?= number_format($t_cpv, 0, ',', '.'); ?></span>
                    </td>
                    <td class="text-center" style="width: 6.5%;">
                        <strong><?= $act_cpe > 0 ? number_format($act_cpe, 0, ',', '.') : '-'; ?></strong><br>
                        <span class="target-val">T: <?= number_format($t_cpe, 0, ',', '.'); ?></span>
                    </td>
                    <td style="width: 5%;"><?= htmlspecialchars($c->created_by_name ?: '-'); ?></td>
                    <td style="width: 5.5%;"><?= htmlspecialchars($c->pic_user_name ?: '-'); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="15" class="text-center" style="padding: 10px; color: #999;">Tidak ada data campaign.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <page_footer>
        <table style="width: 100%; border-top: 1px solid #d5dbdb; padding-top: 3px;">
            <tr>
                <td style="width: 50%;" class="page-footer-text">
                    KOL Management System &bull; Rekapitulasi Seluruh Campaign
                </td>
                <td style="width: 50%; text-align: right;" class="page-footer-text">
                    Halaman [[page_cu]] / [[page_nb]]
                </td>
            </tr>
        </table>
    </page_footer>
</page>
