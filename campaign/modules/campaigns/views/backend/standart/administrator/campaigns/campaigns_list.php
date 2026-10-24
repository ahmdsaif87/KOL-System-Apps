<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>

<script type="text/javascript">
// Binding hotkeys
function domo() {
    $('*').bind('keydown', 'Ctrl+a', function assets() {
        window.location.href = BASE_URL + '/administrator/Campaigns/add';
        return false;
    });

    $('*').bind('keydown', 'Ctrl+f', function assets() {
        $('#sbtn').trigger('click');
        return false;
    });

    $('*').bind('keydown', 'Ctrl+x', function assets() {
        $('#reset').trigger('click');
        return false;
    });

    $('*').bind('keydown', 'Ctrl+b', function assets() {
        $('#reset').trigger('click');
        return false;
    });
}

jQuery(document).ready(domo);
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= cclang('campaigns') ?> <small><?= cclang('list_all'); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= cclang('campaigns') ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
                <div class="box-body" style="padding: 20px;">
                    <!-- Widget Header -->
                    <div class="box box-widget widget-user-2" style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px;">
                            <div>
                                <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;"><?= cclang('campaigns') ?></h3>
                                <div style="margin-top: 5px; color: #6b778c; font-size: 13px;">
                                    <?= cclang('list_all', [cclang('campaigns')]); ?> 
                                    <span style="display: inline-block; margin-left: 6px; padding: 2px 7px; border-radius: 10px; background: #e8f1fb; color: #1f4b6e; font-weight: 700;">
                                        <?= $campaigns_counts; ?> <?= cclang('items'); ?>
                                    </span>
                                </div>
                            </div>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                                <?php is_allowed('campaigns_add', function(){ ?>
                                    <a class="btn btn-primary btn-flat btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('campaigns')]); ?>" href="<?= site_url('administrator/campaigns/add'); ?>">
                                        <i class="fa fa-plus"></i> <?= cclang('add_new_button', [cclang('campaigns')]); ?>
                                    </a>
                                <?php }) ?>
                                <a class="btn btn-default btn-flat" title="<?= cclang('export'); ?> PDF <?= cclang('campaigns') ?>" href="<?= site_url('administrator/campaigns/export_pdf'); ?>">
                                    <i class="fa fa-file-pdf-o"></i> <?= cclang('export'); ?> PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <form name="form_campaigns" id="form_campaigns" action="<?= base_url('administrator/campaigns/index'); ?>">

                        <?php 
                            $total_pending_all = 0;
                            if (!empty($applications_by_campaign)) {
                                foreach ($applications_by_campaign as $app_stat) {
                                    $total_pending_all += (int)$app_stat->pending_applicants;
                                }
                            }
                        ?>
                        <?php if ($total_pending_all > 0): ?>
                            <div class="alert alert-warning" style="background-color: #fffbeb !important; border: 1px solid #fde68a !important; color: #92400e !important; border-radius: 6px; padding: 10px 16px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="background: #f59e0b; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                        <i class="fa fa-bell"></i>
                                    </span>
                                    <span style="font-size: 13px;">
                                        <strong>Pemberitahuan:</strong> Terdapat <strong><?= $total_pending_all; ?> pelamar KOL</strong> yang menunggu review.
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <style>
                          .prod-filter-bar { margin-bottom: 14px; padding: 12px 12px 4px 12px; background: #f8fafc; border: 1px solid #e7edf3; border-radius: 6px; }
                          .prod-filter-bar .form-control { height: 38px; border-radius: 4px; margin-bottom: 8px; }
                          .prod-filter-bar .btn { width: 100%; min-height: 38px; margin-bottom: 8px; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
                          .prod-filter-bar .pf-sep { border-top: 1px dashed #e2e8f0; margin: 4px 0 12px 0; }
                          @media (max-width: 768px) {
                            #form_campaigns .prod-filter-bar > .pf-half { width: calc(50% - 3px) !important; max-width: calc(50% - 3px) !important; float: left !important; display: block !important; }
                            #form_campaigns .prod-filter-bar > .pf-half + .pf-half { margin-left: 6px !important; }
                            #form_campaigns .prod-filter-bar > .pf-bulkmain { width: calc(100% - 50px) !important; max-width: calc(100% - 50px) !important; float: left !important; display: block !important; }
                            #form_campaigns .prod-filter-bar > .pf-bulkbtn { width: 44px !important; max-width: 44px !important; float: left !important; display: block !important; margin-left: 6px !important; }
                            #form_campaigns button[name="sbtn"] { width: 100% !important; min-width: 0 !important; max-width: none !important; font-size: 13px !important; }
                            #form_campaigns button[name="sbtn"]::before { content: none !important; }
                            #form_campaigns a[name="reset"] { width: 100% !important; min-width: 0 !important; max-width: none !important; }
                            #form_campaigns a[name="reset"] .reset-txt { display: none !important; }
                          }
                        </style>

                        <div class="row prod-filter-bar" style="margin-left:0; margin-right:0;">
                            <div class="col-xs-12 col-sm-4" style="padding-left:4px; padding-right:4px;">
                                <input type="text" class="form-control" name="q" id="filter" placeholder="Cari nama campaign..." value="<?= $this->input->get('q'); ?>">
                            </div>
                            <div class="col-xs-12 col-sm-4" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control chosen chosen-select" name="f" id="field" data-placeholder="Kolom pencarian">
                                    <option value=""><?= cclang('all'); ?> kolom</option>
                                    <option <?= $this->input->get('f') == 'name' ? 'selected' : ''; ?> value="name">Name</option>
                                    <option <?= $this->input->get('f') == 'status' ? 'selected' : ''; ?> value="status">Status</option>
                                    <option <?= $this->input->get('f') == 'campaign_type' ? 'selected' : ''; ?> value="campaign_type">Type Campaign</option>
                                    <option <?= $this->input->get('f') == 'pic_user_id' ? 'selected' : ''; ?> value="pic_user_id">Penanggung Jawab (PIC)</option>
                                    <option <?= $this->input->get('f') == 'products' ? 'selected' : ''; ?> value="products">Products</option>
                                </select>
                            </div>
                            <div class="col-xs-6 col-sm-2 pf-half" style="padding-left:4px; padding-right:4px;">
                                <button type="submit" class="btn btn-primary btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>" style="font-weight:600;"><i class="fa fa-search"></i> Filter</button>
                            </div>
                            <div class="col-xs-6 col-sm-2 pf-half" style="padding-left:4px; padding-right:4px;">
                                <a class="btn btn-default btn-flat" name="reset" id="reset" href="<?= base_url('administrator/campaigns'); ?>" title="<?= cclang('reset_filter'); ?>" style="font-weight:600;"><i class="fa fa-undo"></i><span class="reset-txt"> Reset</span></a>
                            </div>
                            <div class="col-xs-12 pf-sep" style="padding-left:4px; padding-right:4px;"></div>
                            <div class="col-xs-8 col-sm-2 pf-bulkmain" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control chosen chosen-select" name="bulk" id="bulk" data-placeholder="Bulk Action">
                                    <option value="">Bulk</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                            <div class="col-xs-4 col-sm-2 pf-bulkbtn" style="padding-left:4px; padding-right:4px;">
                                <button type="button" class="btn btn-default btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>" style="font-weight:600;"><i class="fa fa-check"></i> <?= cclang('apply_button'); ?></button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" style="margin-bottom: 0; border: 1px solid #e7edf3; border-radius: 6px;">
                                <thead>
                                    <tr style="background: #f6f8fb; color: #52606d; font-size: 11px; text-transform: uppercase; letter-spacing: 0.02em;">
                                        <th width="5" style="border-bottom: 1px solid #e7edf3;">
                                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                                        </th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;"><?= cclang('name') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; min-width: 200px; white-space: nowrap;">Products</th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;"><?= cclang('budget') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;">Sisa Budget</th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;"><?= cclang('start_date') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;"><?= cclang('end_date') ?></th>
                                        <th class="th-status" style="border-bottom: 1px solid #e7edf3; white-space: nowrap; min-width: 175px;"><?= cclang('status') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;"><?= cclang('campaign_type') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; min-width: 170px; white-space: nowrap;">Views (Actual / Target)</th>
                                        <th style="border-bottom: 1px solid #e7edf3; min-width: 160px; white-space: nowrap;">ER (Actual / Target)</th>
                                        <th style="border-bottom: 1px solid #e7edf3; min-width: 160px; white-space: nowrap;">CPV (Actual / Target)</th>
                                        <th style="border-bottom: 1px solid #e7edf3; min-width: 160px; white-space: nowrap;">CPE (Actual / Target)</th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;"><?= cclang('created_by') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;"><?= cclang('pic_user_id') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap; min-width: 180px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_campaigns">
                                    <?php foreach($campaignss as $campaigns): ?>
                                    <tr>
                                        <td width="5">
                                            <input type="checkbox" class="flat-red check" name="id[]" value="<?= $campaigns->id; ?>">
                                        </td>
                                        <td style="font-weight: 600;">
                                            <div><?= _ent($campaigns->name); ?></div>
                                            <div class="visible-xs text-muted" style="font-size: 10px; font-weight: normal; margin-top: 2px;">
                                                <i class="fa fa-calendar"></i> <?= date('d M', strtotime($campaigns->start_date)); ?> - <?= date('d M Y', strtotime($campaigns->end_date)); ?>
                                            </div>
                                            <div style="margin-top: 5px; display: flex; align-items: center;">
                                                <?php if ($campaigns->sourcing_model === 'OPEN_POST'): ?>
                                                    <?php 
                                                        $c_app = isset($applications_by_campaign[$campaigns->id]) ? $applications_by_campaign[$campaigns->id] : null;
                                                        $pending_cnt = $c_app ? (int)$c_app->pending_applicants : 0;
                                                        $total_app_cnt = $c_app ? (int)$c_app->total_applicants : 0;
                                                        $slots_text = !empty($campaigns->max_slots) ? ' · ' . (int)$campaigns->max_slots . ' Slots' : '';
                                                    ?>
                                                    <div class="sourcing-pill sourcing-pill-open">
                                                        <span class="pill-label">
                                                            <i class="fa fa-bullhorn" style="color: #059669; font-size: 10px;"></i> Open Post<?= $slots_text; ?>
                                                        </span>
                                                        <?php if ($pending_cnt > 0): ?>
                                                            <a href="<?= site_url('administrator/campaigns/view/' . $campaigns->id . '#box_applicants'); ?>" 
                                                               class="pill-counter-pending" 
                                                               title="<?= $pending_cnt; ?> KOL mendaftar & menunggu review PIC">
                                                                <i class="fa fa-bell"></i> <?= $pending_cnt; ?>
                                                            </a>
                                                        <?php elseif ($total_app_cnt > 0): ?>
                                                            <a href="<?= site_url('administrator/campaigns/view/' . $campaigns->id . '#box_applicants'); ?>" 
                                                               class="pill-counter-total" 
                                                               title="<?= $total_app_cnt; ?> total pelamar">
                                                                <i class="fa fa-users"></i> <?= $total_app_cnt; ?>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="sourcing-pill sourcing-pill-appointed">
                                                        <i class="fa fa-user-plus" style="color: #2563eb; font-size: 10px;"></i> Appointed
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="min-width: 160px; vertical-align: middle;">
                                            <?php if (!empty($products_by_campaign[$campaigns->id])): ?>
                                                <?php 
                                                    $c_prods = $products_by_campaign[$campaigns->id];
                                                    $total_prods = count($c_prods);
                                                    $first_prod = $c_prods[0];

                                                    // Build popover HTML for all products in this campaign
                                                    $popover_content = '<div style="font-size:11px; text-align:left;">';
                                                    $popover_content .= '<div style="font-weight:700; border-bottom:1px solid #e7edf3; padding-bottom:5px; margin-bottom:8px; color:#172b4d;">Total: ' . $total_prods . ' Produk</div>';
                                                    foreach ($c_prods as $cp) {
                                                        $popover_content .= '<div style="margin-bottom:8px; padding-bottom:6px; border-bottom:1px dashed #e7edf3; line-height:1.3;">';
                                                        $popover_content .= '<div style="display:flex; justify-content:space-between; align-items:center; gap:6px; margin-bottom:3px;">';
                                                        $popover_content .= '<span class="label label-info" style="font-size:9px; font-weight:700;">' . htmlspecialchars($cp->product_sku ?: 'PROD') . '</span>';
                                                        if (!empty($cp->product_category)) {
                                                            $popover_content .= '<span class="badge" style="background:#e8f1fb; color:#1f4b6e; font-size:10px; font-weight:600;">' . htmlspecialchars($cp->product_category) . '</span>';
                                                        }
                                                        $popover_content .= '</div>';
                                                        $popover_content .= '<div style="font-weight:600; color:#333; font-size:11px;">' . htmlspecialchars($cp->product_name) . '</div>';
                                                        $popover_content .= '</div>';
                                                    }
                                                    $popover_content .= '</div>';
                                                ?>
                                                <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                                                    <span class="label label-default" style="background: #f4f7f9; color: #172b4d; border: 1px solid #d2d6de; font-size: 11px; font-weight: 600; padding: 3px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;" title="<?= htmlspecialchars($first_prod->product_name); ?>">
                                                        <i class="fa fa-cube text-primary" style="font-size: 10px;"></i>
                                                        <?= _ent($first_prod->product_sku ?: 'PROD'); ?>
                                                    </span>

                                                    <?php if ($total_prods > 1): ?>
                                                        <button type="button" class="btn btn-xs btn-default btn-flat campaign-products-popover" 
                                                                data-toggle="popover" 
                                                                data-html="true" 
                                                                data-trigger="hover focus" 
                                                                data-placement="top" 
                                                                data-title="<i class='fa fa-cubes text-primary'></i> Daftar Produk Campaign" 
                                                                data-content="<?= htmlspecialchars($popover_content); ?>"
                                                                style="font-size: 10px; font-weight: 700; color: #0070ba; background: #e8f1fb; border-color: #b8daff; border-radius: 4px; padding: 2px 6px;">
                                                            +<?= ($total_prods - 1); ?> lainnya
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 11px;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="white-space: nowrap; font-weight: 600; color: #00a65a;">
                                            Rp <?= number_format($campaigns->budget, 0, ',', '.'); ?>
                                        </td>
                                        <?php 
                                            $c_budget = (float)$campaigns->budget;
                                            $c_spent = isset($spent_by_campaign[$campaigns->id]) ? (float)$spent_by_campaign[$campaigns->id] : 0;
                                            $c_remaining = $c_budget - $c_spent;
                                            $rem_color = $c_remaining >= 0 ? '#16803c' : '#c53030';
                                            $rem_bg = $c_remaining >= 0 ? '#e6f7ec' : '#fdeeed';
                                            $rem_border = $c_remaining >= 0 ? '#b7eb8f' : '#ffa39e';
                                        ?>
                                        <td style="white-space: nowrap;">
                                            <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; background: <?= $rem_bg; ?>; color: <?= $rem_color; ?>; border: 1px solid <?= $rem_border; ?>;">
                                                Rp <?= number_format($c_remaining, 0, ',', '.'); ?>
                                            </span>
                                            <div style="font-size: 10px; color: #6b778c; margin-top: 2px;">
                                                Terpakai: Rp <?= number_format($c_spent, 0, ',', '.'); ?>
                                            </div>
                                        </td>
                                        <td style="white-space: nowrap;"><?= _ent($campaigns->start_date); ?></td>
                                        <td style="white-space: nowrap;"><?= _ent($campaigns->end_date); ?></td>
                                        <td class="td-status" style="white-space: nowrap; min-width: 175px;">
                                            <?php 
                                                $can_edit_status = $this->aauth->is_admin() || $this->aauth->is_member('Admin') || $this->aauth->is_member('Leader KOL');
                                            ?>
                                            <?php if ($can_edit_status && !in_array($campaigns->status, ['COMPLETED', 'CANCELLED'])): ?>
                                                <select class="form-control input-sm select-campaign-status"
                                                        data-id="<?= $campaigns->id; ?>"
                                                        data-current-status="<?= $campaigns->status; ?>"
                                                        style="width: 175px; min-width: 175px; height: 30px; font-weight: 700; font-size: 11px; border-radius: 6px; padding: 2px 22px 2px 6px; cursor: pointer; text-transform: uppercase;">
                                                    <option value="DRAFT" <?= $campaigns->status === 'DRAFT' ? 'selected' : ''; ?>>DRAFT</option>
                                                    <option value="OPEN_REGISTRATION" <?= $campaigns->status === 'OPEN_REGISTRATION' ? 'selected' : ''; ?>>OPEN_REGISTRATION</option>
                                                    <option value="ACTIVE" <?= $campaigns->status === 'ACTIVE' ? 'selected' : ''; ?>>ACTIVE</option>
                                                    <option value="COMPLETED" <?= $campaigns->status === 'COMPLETED' ? 'selected' : ''; ?>>COMPLETED</option>
                                                    <option value="CANCELLED" <?= $campaigns->status === 'CANCELLED' ? 'selected' : ''; ?>>CANCELLED</option>
                                                </select>
                                            <?php else: ?>
                                                <?= render_status_badge($campaigns->status); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td style="white-space: nowrap;"><?= render_status_badge($campaigns->campaign_type); ?></td>
                                        <td style="min-width: 170px; white-space: nowrap;">
                                            <?php 
                                                $t_views = (int)$campaigns->target_views;
                                                $act_views = isset($metrics_by_campaign[$campaigns->id]) ? (int)$metrics_by_campaign[$campaigns->id]->total_actual_views : 0;
                                                $views_pct = ($t_views > 0) ? round(($act_views / $t_views) * 100, 1) : 0;
                                                
                                                if ($t_views <= 0) {
                                                    $v_bar_color = '#adb5bd';
                                                    $v_badge_color = '#6c757d';
                                                    $v_badge_bg = '#f1f3f5';
                                                    $v_bar_pct = 0;
                                                } elseif ($views_pct >= 100) {
                                                    $v_bar_color = '#28a745';
                                                    $v_badge_color = '#155724';
                                                    $v_badge_bg = '#d4edda';
                                                    $v_bar_pct = 100;
                                                } elseif ($views_pct >= 50) {
                                                    $v_bar_color = '#007bff';
                                                    $v_badge_color = '#004085';
                                                    $v_badge_bg = '#cce5ff';
                                                    $v_bar_pct = $views_pct;
                                                } elseif ($views_pct > 0) {
                                                    $v_bar_color = '#fd7e14';
                                                    $v_badge_color = '#856404';
                                                    $v_badge_bg = '#fff3cd';
                                                    $v_bar_pct = $views_pct;
                                                } else {
                                                    $v_bar_color = '#dc3545';
                                                    $v_badge_color = '#721c24';
                                                    $v_badge_bg = '#f8d7da';
                                                    $v_bar_pct = 0;
                                                }
                                            ?>
                                            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 3px;">
                                                <span style="font-size: 13px; font-weight: 700; color: #172b4d;">
                                                    <?= number_format($act_views, 0, ',', '.'); ?>
                                                    <span style="font-size: 11px; font-weight: 500; color: #6b778c;">/ <?= $t_views > 0 ? number_format($t_views, 0, ',', '.') : '-'; ?></span>
                                                </span>
                                                <span style="font-size: 10px; font-weight: 700; color: <?= $v_badge_color; ?>; background: <?= $v_badge_bg; ?>; padding: 1px 5px; border-radius: 3px; margin-left: 6px;">
                                                    <?= $views_pct; ?>%
                                                </span>
                                            </div>
                                            <div style="height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden; border: 1px solid #dee2e6;">
                                                <div style="width: <?= $v_bar_pct; ?>%; height: 100%; background: <?= $v_bar_color; ?>; border-radius: 2px;"></div>
                                            </div>
                                            <div style="margin-top: 2px; font-size: 10px; color: #8c9ba5;">
                                                Actual / Target Views
                                            </div>
                                        </td>
                                        <td style="min-width: 160px; white-space: nowrap;">
                                            <?php 
                                                $t_er_pct = (float)$campaigns->target_er * 100;
                                                $act_er_dec = isset($metrics_by_campaign[$campaigns->id]) ? (float)$metrics_by_campaign[$campaigns->id]->avg_actual_er : 0;
                                                $act_er_pct = $act_er_dec * 100;
                                                $er_pct = ($t_er_pct > 0) ? round(($act_er_pct / $t_er_pct) * 100, 1) : 0;

                                                if ($t_er_pct <= 0) {
                                                    $er_bar_color = '#adb5bd';
                                                    $er_badge_color = '#6c757d';
                                                    $er_badge_bg = '#f1f3f5';
                                                    $er_bar_pct = 0;
                                                } elseif ($er_pct >= 100) {
                                                    $er_bar_color = '#28a745';
                                                    $er_badge_color = '#155724';
                                                    $er_badge_bg = '#d4edda';
                                                    $er_bar_pct = 100;
                                                } elseif ($er_pct >= 50) {
                                                    $er_bar_color = '#007bff';
                                                    $er_badge_color = '#004085';
                                                    $er_badge_bg = '#cce5ff';
                                                    $er_bar_pct = $er_pct;
                                                } elseif ($er_pct > 0) {
                                                    $er_bar_color = '#fd7e14';
                                                    $er_badge_color = '#856404';
                                                    $er_badge_bg = '#fff3cd';
                                                    $er_bar_pct = $er_pct;
                                                } else {
                                                    $er_bar_color = '#dc3545';
                                                    $er_badge_color = '#721c24';
                                                    $er_badge_bg = '#f8d7da';
                                                    $er_bar_pct = 0;
                                                }
                                            ?>
                                            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 3px;">
                                                <span style="font-size: 13px; font-weight: 700; color: #172b4d;">
                                                    <?= number_format($act_er_pct, 2); ?>%
                                                    <span style="font-size: 11px; font-weight: 500; color: #6b778c;">/ <?= $t_er_pct > 0 ? number_format($t_er_pct, 2) . '%' : '-'; ?></span>
                                                </span>
                                                <span style="font-size: 10px; font-weight: 700; color: <?= $er_badge_color; ?>; background: <?= $er_badge_bg; ?>; padding: 1px 5px; border-radius: 3px; margin-left: 6px;">
                                                    <?= $er_pct; ?>%
                                                </span>
                                            </div>
                                            <div style="height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden; border: 1px solid #dee2e6;">
                                                <div style="width: <?= $er_bar_pct; ?>%; height: 100%; background: <?= $er_bar_color; ?>; border-radius: 2px;"></div>
                                            </div>
                                            <div style="margin-top: 2px; font-size: 10px; color: #8c9ba5;">
                                                Avg Actual / Target ER
                                            </div>
                                        </td>
                                        <td style="min-width: 160px; white-space: nowrap;">
                                            <?php 
                                                $t_cpv = isset($campaigns->target_cpv) && $campaigns->target_cpv !== null ? (float)$campaigns->target_cpv : ($t_views > 0 ? round($c_budget / $t_views, 2) : 0);
                                                $act_cpv = ($act_views > 0) ? round($c_spent / $act_views, 2) : 0;
                                                
                                                $cpv_efficient = ($t_cpv > 0 && $act_cpv > 0 && $act_cpv <= $t_cpv);
                                                $cpv_badge_color = $cpv_efficient ? '#155724' : ($act_cpv > 0 ? '#856404' : '#6c757d');
                                                $cpv_badge_bg = $cpv_efficient ? '#d4edda' : ($act_cpv > 0 ? '#fff3cd' : '#f1f3f5');
                                            ?>
                                            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 3px;">
                                                <span style="font-size: 13px; font-weight: 700; color: #172b4d;">
                                                    <?= $act_cpv > 0 ? 'Rp ' . number_format($act_cpv, 0, ',', '.') : '-'; ?>
                                                    <span style="font-size: 11px; font-weight: 500; color: #6b778c;">/ <?= $t_cpv > 0 ? 'Rp ' . number_format($t_cpv, 0, ',', '.') : '-'; ?></span>
                                                </span>
                                                <?php if ($act_cpv > 0 && $t_cpv > 0): ?>
                                                <span style="font-size: 10px; font-weight: 700; color: <?= $cpv_badge_color; ?>; background: <?= $cpv_badge_bg; ?>; padding: 1px 5px; border-radius: 3px; margin-left: 6px;">
                                                    <?= $cpv_efficient ? '✓ ON-TARGET' : '▲ OVER'; ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="margin-top: 2px; font-size: 10px; color: #8c9ba5;">
                                                Actual / Target CPV
                                            </div>
                                        </td>
                                        <td style="min-width: 160px; white-space: nowrap;">
                                            <?php 
                                                $act_engagements = isset($metrics_by_campaign[$campaigns->id]) ? (int)$metrics_by_campaign[$campaigns->id]->total_actual_engagements : 0;
                                                $t_er_dec = (float)$campaigns->target_er;
                                                $t_cpe = isset($campaigns->target_cpe) && $campaigns->target_cpe !== null ? (float)$campaigns->target_cpe : ($t_views > 0 && $t_er_dec > 0 ? round($c_budget / ($t_views * $t_er_dec), 2) : 0);
                                                $act_cpe = ($act_engagements > 0) ? round($c_spent / $act_engagements, 2) : 0;

                                                $cpe_efficient = ($t_cpe > 0 && $act_cpe > 0 && $act_cpe <= $t_cpe);
                                                $cpe_badge_color = $cpe_efficient ? '#155724' : ($act_cpe > 0 ? '#856404' : '#6c757d');
                                                $cpe_badge_bg = $cpe_efficient ? '#d4edda' : ($act_cpe > 0 ? '#fff3cd' : '#f1f3f5');
                                            ?>
                                            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 3px;">
                                                <span style="font-size: 13px; font-weight: 700; color: #172b4d;">
                                                    <?= $act_cpe > 0 ? 'Rp ' . number_format($act_cpe, 0, ',', '.') : '-'; ?>
                                                    <span style="font-size: 11px; font-weight: 500; color: #6b778c;">/ <?= $t_cpe > 0 ? 'Rp ' . number_format($t_cpe, 0, ',', '.') : '-'; ?></span>
                                                </span>
                                                <?php if ($act_cpe > 0 && $t_cpe > 0): ?>
                                                <span style="font-size: 10px; font-weight: 700; color: <?= $cpe_badge_color; ?>; background: <?= $cpe_badge_bg; ?>; padding: 1px 5px; border-radius: 3px; margin-left: 6px;">
                                                    <?= $cpe_efficient ? '✓ ON-TARGET' : '▲ OVER'; ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="margin-top: 2px; font-size: 10px; color: #8c9ba5;">
                                                Actual / Target CPE
                                            </div>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <?php if ($campaigns->created_by): ?>
                                                <?= anchor('administrator/user/view/' . $campaigns->created_by . '?popup=show', $campaigns->created_by_name ? $campaigns->created_by_name : $campaigns->created_by, ['class' => 'popup-view', 'title' => 'Lihat Detail Pembuat']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <?php if ($campaigns->pic_user_id): ?>
                                                <?= anchor('administrator/user/view/' . $campaigns->pic_user_id . '?popup=show', $campaigns->pic_user_name ? $campaigns->pic_user_name : $campaigns->pic_user_id, ['class' => 'popup-view', 'title' => 'Lihat Detail PIC']); ?>
                                            <?php endif; ?>
                                        </td>
                                         <td style="vertical-align: middle; white-space: nowrap; min-width: 180px;">
                                             <div style="display: flex; gap: 4px; align-items: center; white-space: nowrap;">
                                                  <?php if ($campaigns->status === 'DRAFT'): ?>
                                                      <?php is_allowed('campaigns_update', function() use ($campaigns){ ?>
                                                          <?php if ($campaigns->sourcing_model === 'OPEN_POST'): ?>
                                                              <a href="javascript:void(0);" class="btn btn-xs btn-primary btn-flat btn-campaign-action" data-action="open_reg" data-id="<?= $campaigns->id; ?>" data-name="<?= htmlspecialchars($campaigns->name); ?>" data-sourcing-model="<?= htmlspecialchars($campaigns->sourcing_model); ?>" title="Buka pendaftaran KOL (tanpa approval)"><i class="fa fa-bullhorn"></i> Open Regist</a>
                                                          <?php else: ?>
                                                              <a href="javascript:void(0);" class="btn btn-xs btn-success btn-flat btn-campaign-action" data-action="activate" data-id="<?= $campaigns->id; ?>" data-name="<?= htmlspecialchars($campaigns->name); ?>" data-sourcing-model="<?= htmlspecialchars($campaigns->sourcing_model); ?>" title="Aktifkan campaign internal (tanpa approval)"><i class="fa fa-play"></i> Activate</a>
                                                          <?php endif; ?>
                                                          <a href="javascript:void(0);" class="btn btn-xs btn-warning btn-flat btn-campaign-action" data-action="cancel" data-id="<?= $campaigns->id; ?>" data-name="<?= htmlspecialchars($campaigns->name); ?>" title="Batalkan Campaign"><i class="fa fa-ban"></i></a>
                                                      <?php }) ?>
                                                 <?php elseif (in_array($campaigns->status, ['ACTIVE', 'OPEN_REGISTRATION'])): ?>
                                                     <?php is_allowed('campaigns_update', function() use ($campaigns){ ?>
                                                         <a href="javascript:void(0);" class="btn btn-xs btn-warning btn-flat btn-campaign-action" data-action="cancel" data-id="<?= $campaigns->id; ?>" data-name="<?= htmlspecialchars($campaigns->name); ?>" title="Batalkan Campaign"><i class="fa fa-ban"></i></a>
                                                     <?php }) ?>
                                                 <?php endif; ?>
                                                 <?php is_allowed('campaigns_view', function() use ($campaigns){ ?>
                                                     <a href="<?= site_url('administrator/campaigns/analytics/' . $campaigns->id); ?>" class="btn btn-xs btn-primary btn-flat" title="Campaign Financial & Performance Analytics"><i class="fa fa-line-chart"></i></a>
                                                 <?php }) ?>
                                                 <a href="<?= site_url('administrator/campaigns/single_pdf/' . $campaigns->id); ?>" class="btn btn-xs btn-default btn-flat" title="Export PDF"><i class="fa fa-file-pdf-o"></i></a>
                                                 <?php is_allowed('campaigns_view', function() use ($campaigns){ ?>
                                                     <a href="<?= site_url('administrator/campaigns/view/' . $campaigns->id); ?>" class="btn btn-xs btn-default btn-flat" title="View"><i class="fa fa-eye"></i></a>
                                                 <?php }) ?>
                                                 <?php is_allowed('campaigns_update', function() use ($campaigns){ ?>
                                                     <a href="<?= site_url('administrator/campaigns/edit/' . $campaigns->id); ?>" class="btn btn-xs btn-default btn-flat" title="<?= cclang('update_button'); ?>"><i class="fa fa-pencil"></i></a>
                                                 <?php }) ?>
                                                 <?php is_allowed('campaigns_delete', function() use ($campaigns){ ?>
                                                     <a href="javascript:void(0);" data-href="<?= site_url('administrator/campaigns/delete/' . $campaigns->id); ?>" class="btn btn-xs btn-default btn-flat remove-data" title="<?= cclang('remove_button'); ?>"><i class="fa fa-trash"></i></a>
                                                 <?php }) ?>
                                             </div>
                                         </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if ($campaigns_counts == 0): ?>
                                    <tr>
                                        <td colspan="100">
                                            Campaigns data is not available
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    <div class="row" style="margin-top: 16px;">
                        <div class="col-md-12 text-right">
                            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                <?= $pagination; ?>
                            </div>
                        </div>
                    </div>


                    </form>
                </div>
                <!--/box body -->
            </div>
            <!--/box -->
        </div>
    </div>
</section>
<!-- /.content -->

<style>
.popover-custom-products {
    max-width: 320px !important;
    min-width: 280px !important;
    width: auto !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
    border: 1px solid #d2d6de !important;
    border-radius: 6px !important;
    z-index: 1060 !important;
}
.popover-custom-products .popover-title {
    background: #f8fafc;
    font-weight: 700;
    color: #172b4d;
    border-bottom: 1px solid #e7edf3;
    font-size: 12px;
    padding: 8px 12px;
}
.popover-custom-products .popover-content {
    padding: 10px 12px;
    max-height: 280px;
    overflow-y: auto;
}
</style>

<!-- Page script -->
<script>
$(document).ready(function(){
    // Initialize bootstrap popover with custom template and container
    $('.campaign-products-popover, .shipment-items-popover, [data-toggle="popover"]').popover({
        html: true,
        trigger: 'hover focus',
        container: 'body',
        template: '<div class="popover popover-custom-products" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('.remove-data').click(function(){
        var url = $(this).attr('data-href');

        swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
            cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm){
            if (isConfirm) {
                document.location.href = url;            
            }
        });

        return false;
    });

    $('#apply').click(function(){
        var bulk = $('#bulk');
        var serialize_bulk = $('#form_campaigns').serialize();

        if (bulk.val() == 'delete') {
            swal({
                title: "<?= cclang('are_you_sure'); ?>",
                text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
                cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm){
                if (isConfirm) {
                    document.location.href = BASE_URL + '/administrator/campaigns/delete?' + serialize_bulk;      
                }
            });

            return false;
        } else if (bulk.val() == '') {
            swal({
                title: "Upss",
                text: "<?= cclang('please_choose_bulk_action_first'); ?>",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Okay!",
                closeOnConfirm: true,
                closeOnCancel: true
            });

            return false;
        }

        return false;
    });

    // Check all
    var checkAll = $('#check_all');
    var checkboxes = $('input.check');

    checkAll.on('ifChecked ifUnchecked', function(event) {   
        if (event.type == 'ifChecked') {
            checkboxes.iCheck('check');
        } else {
            checkboxes.iCheck('uncheck');
        }
    });

    checkboxes.on('ifChanged', function(event){
        if (checkboxes.filter(':checked').length == checkboxes.length) {
            checkAll.prop('checked', 'checked');
        } else {
            checkAll.removeProp('checked');
        }
        checkAll.iCheck('update');
    });

    function updateCampaignStatusStyle($elem, status) {
        var bg = '#f1f5f9', color = '#475569', border = '#cbd5e1'; // DRAFT
        if (status === 'OPEN_REGISTRATION') {
            bg = '#eff6ff'; color = '#2563eb'; border = '#bfdbfe';
        } else if (status === 'ACTIVE') {
            bg = '#e6f7ec'; color = '#16803c'; border = '#b7eb8f';
        } else if (status === 'COMPLETED') {
            bg = '#e8f1fb'; color = '#1f4b6e'; border = '#91d5ff';
        } else if (status === 'CANCELLED') {
            bg = '#fff1f0'; color = '#cf1322'; border = '#ffa39e';
        }
        $elem.css({
            'background-color': bg,
            'color': color,
            'border-color': border
        });
    }

    $('.select-campaign-status').each(function() {
        updateCampaignStatusStyle($(this), $(this).val());
    });

    $(document).on('change', '.select-campaign-status', function(){
        var $select = $(this);
        var campaignId = $select.data('id');
        var newStatus = $select.val();
        var prevStatus = $select.data('current-status') || newStatus;

        $select.prop('disabled', true);

        var postData = {
            id: campaignId,
            status: newStatus
        };
        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/campaigns/update_status/' + campaignId,
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function(res) {
                $select.prop('disabled', false);
                if (res.success) {
                    $select.data('current-status', newStatus);
                    updateCampaignStatusStyle($select, newStatus);
                    if (typeof toastr !== 'undefined') {
                        toastr['success'](res.message);
                    } else {
                        swal({
                            title: "Berhasil!",
                            text: res.message,
                            type: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } else {
                    $select.val(prevStatus);
                    updateCampaignStatusStyle($select, prevStatus);
                    if (typeof toastr !== 'undefined') {
                        toastr['error'](res.message || 'Gagal mengubah status campaign.');
                    } else {
                        swal("Gagal", res.message || 'Gagal mengubah status campaign.', "error");
                    }
                }
            },
            error: function(xhr) {
                $select.prop('disabled', false);
                $select.val(prevStatus);
                updateCampaignStatusStyle($select, prevStatus);
                if (typeof toastr !== 'undefined') {
                    toastr['error']('Terjadi kesalahan koneksi server.');
                } else {
                    swal("Error", "Terjadi kesalahan koneksi server.", "error");
                }
            }
        });
    });

    // Approve / Reject / Cancel Campaign Actions
    $(document).on('click', '.btn-campaign-action', function(e){
        e.preventDefault();
        var $btn = $(this);
        var campaignId = $btn.data('id');
        var campaignName = $btn.data('name') || 'Campaign';
        var action = $btn.data('action');

        var title = '';
        var text = '';
        var targetStatus = '';
        var confirmColor = '#28a745';
        var confirmText = 'Ya, Lanjutkan!';

        if (action === 'open_reg' || action === 'approve') {
            title = 'Buka Pendaftaran Campaign?';
            text = 'Campaign "' + campaignName + '" akan dibuka untuk pendaftaran KOL (tanpa approval). Status akan menjadi OPEN_REGISTRATION.';
            targetStatus = 'OPEN_REGISTRATION';
            confirmColor = '#2563eb';
            confirmText = 'Ya, Buka Pendaftaran!';
        } else if (action === 'activate') {
            title = 'Aktifkan Campaign Internal?';
            text = 'Campaign internal "' + campaignName + '" akan langsung diaktifkan (tanpa pendaftaran). Status akan menjadi ACTIVE.';
            targetStatus = 'ACTIVE';
            confirmColor = '#28a745';
            confirmText = 'Ya, Aktifkan!';
        } else if (action === 'reject') {
            title = 'Reject Campaign?';
            text = 'Apakah Anda yakin ingin menolak (Reject) campaign "' + campaignName + '"? Status akan diubah menjadi CANCELLED.';
            targetStatus = 'CANCELLED';
            confirmColor = '#dc3545';
            confirmText = 'Ya, Reject!';
        } else if (action === 'cancel') {
            title = 'Batalkan Campaign?';
            text = 'Apakah Anda yakin ingin membatalkan campaign "' + campaignName + '"? Status akan diubah menjadi CANCELLED.';
            targetStatus = 'CANCELLED';
            confirmColor = '#f39c12';
            confirmText = 'Ya, Batalkan!';
        } else {
            return;
        }

        swal({
            title: title,
            text: text,
            type: (action === 'open_reg' || action === 'activate' || action === 'approve') ? "info" : "warning",
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            confirmButtonText: confirmText,
            cancelButtonText: "Batal",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function(isConfirm) {
            if (!isConfirm) return;

            var postData = {
                id: campaignId,
                status: targetStatus
            };
            var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
            var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
            postData[csrfName] = csrfHash;

            $.ajax({
                url: BASE_URL + 'administrator/campaigns/update_status/' + campaignId,
                type: 'POST',
                dataType: 'json',
                data: postData,
                success: function(res) {
                    if (res.success) {
                        swal({
                            title: "Berhasil!",
                            text: res.message,
                            type: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function(){
                            location.reload();
                        }, 1200);
                    } else {
                        swal("Gagal", res.message || 'Gagal memperbarui status campaign.', "error");
                    }
                },
                error: function(xhr) {
                    swal("Error", "Terjadi kesalahan koneksi server.", "error");
                }
            });
        });
    });
});
</script>