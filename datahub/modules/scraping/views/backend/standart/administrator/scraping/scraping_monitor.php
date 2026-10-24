
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<style>
    .platform-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }
    .platform-badge.tiktok { background: #00f2ea; color: #000; }
    .platform-badge.instagram { background: #E4405F; }
    .platform-badge.youtube { background: #FF0000; }
    .platform-badge.twitter, .platform-badge.x { background: #1DA1F2; }
    .platform-badge.facebook { background: #1877F2; }

    .stat-number {
        font-weight: 700;
        font-size: 14px;
        color: #0f172a;
    }
    .stat-er {
        font-weight: 700;
        color: #059669;
    }
    .filter-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 10px;
    }
    .filter-row .form-group {
        margin-bottom: 0;
        min-width: 160px;
    }
    .filter-row .form-control {
        height: 34px !important;
        padding: 6px 12px !important;
        box-sizing: border-box !important;
    }
    .filter-row label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 4px;
    }
    .btn-scrape-stale {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        color: #fff !important;
        border: none !important;
    }
    .btn-scrape-stale:hover {
        background: linear-gradient(135deg, #d97706, #b45309) !important;
    }
</style>

<!-- Content Header -->
<section class="content-header">
    <h1>
        Scraping Hub <small>Active Platforms per KOL</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Scraping Hub</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
                <div class="box-body">
                    <div>
                        <div class="box-header with-border" style="padding-bottom: 20px;">
                            <div class="row pull-right" style="display: flex; gap: 10px;">
                                <button type="button" class="btn btn-flat" id="btn_bulk_scrape" style="background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; border: none; border-radius: 4px;" title="Scrape selected KOLs via Apify">
                                    <i class="fa fa-refresh"></i> Bulk Scrape
                                </button>
                            </div>
                            <h3 class="box-title" style="font-size: 22px; font-weight: 500; margin-top: 0;">Scraping Hub</h3>
                            <p class="text-muted">Active Platforms per KOL <i class="label bg-yellow"><?= $total; ?> platforms</i></p>
                        </div>

                        <!-- Filters -->
                        <form method="get" action="<?= site_url('administrator/scraping'); ?>">
                            <div class="filter-row">
                                <div class="form-group">
                                    <label>KOL Name</label>
                                    <input type="text" class="form-control" name="q" value="<?= _ent(isset($filter['kol_name']) ? $filter['kol_name'] : ''); ?>" placeholder="Search...">
                                </div>
                                <div class="form-group">
                                    <label>Platform</label>
                                    <select class="form-control" name="platform">
                                        <option value="">All Platforms</option>
                                        <?php foreach ($platforms as $p): ?>
                                        <option value="<?= $p->code; ?>" <?= (isset($filter['platform_code']) && $filter['platform_code'] === $p->code) ? 'selected' : ''; ?>>
                                            <?= $p->name; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>From</label>
                                    <input type="date" class="form-control" name="date_from" value="<?= _ent(isset($filter['date_from']) ? $filter['date_from'] : ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>To</label>
                                    <input type="date" class="form-control" name="date_to" value="<?= _ent(isset($filter['date_to']) ? $filter['date_to'] : ''); ?>">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-search"></i> Filter</button>
                                    <a href="<?= site_url('administrator/scraping'); ?>" class="btn btn-flat btn-default"><i class="fa fa-times"></i> Reset</a>
                                </div>
                            </div>
                        </form>

                        <!-- Snapshot Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th width="20">
                                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                                        </th>
                                        <th>KOL Name</th>
                                        <th>Platform</th>
                                        <th>Username / Link</th>
                                        <th>Followers</th>
                                        <th>Avg Views (30)</th>
                                        <th>Avg ER (30)</th>
                                        <th>Posts Analyzed</th>
                                        <th>Last Synced At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($snapshots)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center" style="padding: 40px; color: #94a3b8;">
                                            <i class="fa fa-database" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                                            No snapshots yet. Run a scrape to populate data.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($snapshots as $s): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="flat-red check" name="kol_platform_id[]" value="<?= $s->id; ?>">
                                        </td>
                                        <td>
                                            <a href="<?= site_url('administrator/kols/view/' . $s->kol_id); ?>" style="font-weight: 600; color: #4F46E5;">
                                                <?= _ent($s->kol_name); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php
                                            $code_lower = strtolower($s->platform_code);
                                            $icon = 'fa-globe';
                                            if ($code_lower === 'tiktok') $icon = 'fa-music';
                                            elseif ($code_lower === 'instagram') $icon = 'fa-instagram';
                                            elseif ($code_lower === 'youtube') $icon = 'fa-youtube-play';
                                            ?>
                                            <span class="platform-badge <?= $code_lower; ?>">
                                                <?php if ($code_lower === 'tiktok'): ?><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 11px; height: 13px; fill: currentColor; vertical-align: -1px; margin-right: 3px;"><path d="M448 209.91a210.06 210.06 0 0 1-122.77-39.25V349.38A162.55 162.55 0 1 1 185 188.31V278.2a74.62 74.62 0 1 0 52.23 71.18V0l88 0a121.18 121.18 0 0 0 1.86 22.17h0A122.18 122.18 0 0 0 381 102.39a121.43 121.43 0 0 0 67 20.14z"/></svg><?php else: ?><i class="fa <?= $icon; ?>"></i><?php endif; ?> <?= _ent($s->platform_name); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($s->profile_url): ?>
                                                <a href="<?= _ent($s->profile_url) ?>" target="_blank"><?= _ent($s->username ? $s->username : $s->profile_url) ?></a>
                                            <?php else: ?>
                                                <?= _ent($s->username) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="stat-number"><?= number_format($s->followers_count); ?></td>
                                        <td class="stat-number"><?= number_format($s->average_views_last_30, 0); ?></td>
                                        <td class="stat-er"><?= number_format($s->average_er_last_30 * 100, 2); ?>%</td>
                                        <td><?= $s->total_posts; ?></td>
                                        <td><?= $s->snapshot_date ? date('d M Y, H:i', strtotime($s->snapshot_date)) : '<span class="label label-warning">Never</span>'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="box-footer text-center">
                            <?= isset($pagination) ? $pagination : ''; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $('.flat-red, .flat-red.check').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass: 'iradio_minimal-red'
    });

    $('#check_all').on('ifChecked', function(event) {
        $('.check').iCheck('check');
    });
    $('#check_all').on('ifUnchecked', function(event) {
        $('.check').iCheck('uncheck');
    });
    $('#check_all').on('click', function() {
        if(this.checked) {
            $('.check').each(function() {
                this.checked = true;
                $('.check').iCheck('update');
            });
        } else {
            $('.check').each(function() {
                this.checked = false;
                $('.check').iCheck('update');
            });
        }
    });

    // === BULK SCRAPE SELECTED (BACKGROUND JOB) ===
    $('#btn_bulk_scrape').on('click', function() {
        var checked = $('input.check:checked');
        if (checked.length === 0) {
            swal({ title: 'No KOLs Selected', text: 'Please select at least one KOL to scrape.', type: 'warning' });
            return;
        }

        var kolPlatformIds = [];
        checked.each(function() { kolPlatformIds.push($(this).val()); });

        var btn = $(this);
        swal({
            title: 'Bulk Scrape selected?',
            text: 'This will spawn a background job to scrape profile data. You can safely close the page and check the Inbox later.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4F46E5',
            confirmButtonText: 'Yes, scrape them!',
            closeOnConfirm: true
        }, function() {
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Spawning Job...');
            toastr.info('Spawning background job for selected KOL(s)...');

            var postData = { kol_platform_ids: kolPlatformIds };
            postData[csrf] = token;

            $.ajax({
                url: BASE_URL + 'administrator/scraping/bulk',
                type: 'POST',
                data: postData,
                dataType: 'json',
                timeout: 30000,
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Bulk Scrape');
                    if (res.success) {
                        swal({ title: 'Job Started!', text: res.message, type: 'success' });
                        if(typeof refreshScrapingInbox === 'function') refreshScrapingInbox();
                    } else {
                        toastr.error(res.message || 'Scraping failed');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Bulk Scrape');
                    toastr.error('Network error or timeout.');
                }
            });
        });
    });
});
</script>
