<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>

<script type="text/javascript">
// Binding hotkeys
function domo() {
    $('*').bind('keydown', 'Ctrl+a', function assets() {
        window.location.href = BASE_URL + '/administrator/brands/add';
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
        <?= cclang('brands'); ?> <small><?= cclang('list_all'); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= cclang('brands'); ?></li>
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
                                <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">
                                    <i class="fa fa-briefcase text-primary"></i> <?= cclang('brands'); ?>
                                </h3>
                                <div style="margin-top: 5px; color: #6b778c; font-size: 13px;">
                                    <?= cclang('list_all', ['Brands']); ?> 
                                    <span style="display: inline-block; margin-left: 6px; padding: 2px 7px; border-radius: 10px; background: #e8f1fb; color: #1f4b6e; font-weight: 700;">
                                        <?= $brands_counts; ?> <?= cclang('items'); ?>
                                    </span>
                                </div>
                            </div>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                                <?php is_allowed('brands_add', function(){ ?>
                                    <a class="btn btn-primary btn-flat btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', ['Brands']); ?>" href="<?= site_url('administrator/brands/add'); ?>">
                                        <i class="fa fa-plus"></i> <?= cclang('add_new_button', ['Brands']); ?>
                                    </a>
                                <?php }) ?>
                            </div>
                        </div>
                    </div>

                    <form name="form_brands" id="form_brands" action="<?= base_url('administrator/brands/index'); ?>">
                        <style>
                          .brand-filter-bar { margin-bottom: 14px; padding: 12px 12px 4px 12px; background: #f8fafc; border: 1px solid #e7edf3; border-radius: 6px; }
                          .brand-filter-bar .form-control { height: 38px; border-radius: 4px; margin-bottom: 8px; }
                          .brand-filter-bar .btn { width: 100%; min-height: 38px; margin-bottom: 8px; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
                          .brand-filter-bar .pf-sep { border-top: 1px dashed #e2e8f0; margin: 4px 0 12px 0; }
                        </style>

                        <div class="row brand-filter-bar" style="margin-left:0; margin-right:0;">
                            <div class="col-xs-12 col-sm-4" style="padding-left:4px; padding-right:4px;">
                                <input type="text" class="form-control" name="q" id="filter" placeholder="Cari nama brand, kode, atau badan hukum..." value="<?= _ent($this->input->get('q')); ?>">
                            </div>
                            <div class="col-xs-12 col-sm-4" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control chosen chosen-select" name="f" id="field" data-placeholder="Kolom pencarian">
                                    <option value=""><?= cclang('all'); ?> kolom</option>
                                    <option <?= $this->input->get('f') == 'name' ? 'selected' : ''; ?> value="name">Brand Name</option>
                                    <option <?= $this->input->get('f') == 'code' ? 'selected' : ''; ?> value="code">Brand Code</option>
                                    <option <?= $this->input->get('f') == 'company_name' ? 'selected' : ''; ?> value="company_name">Badan Hukum (PT)</option>
                                    <option <?= $this->input->get('f') == 'email' ? 'selected' : ''; ?> value="email">Email</option>
                                    <option <?= $this->input->get('f') == 'is_active' ? 'selected' : ''; ?> value="is_active">Status</option>
                                </select>
                            </div>
                            <div class="col-xs-6 col-sm-2" style="padding-left:4px; padding-right:4px;">
                                <button type="submit" class="btn btn-primary btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>" style="font-weight:600;"><i class="fa fa-search"></i> Filter</button>
                            </div>
                            <div class="col-xs-6 col-sm-2" style="padding-left:4px; padding-right:4px;">
                                <a class="btn btn-default btn-flat" name="reset" id="reset" href="<?= base_url('administrator/brands'); ?>" title="<?= cclang('reset_filter'); ?>" style="font-weight:600;"><i class="fa fa-undo"></i> Reset</a>
                            </div>
                            <div class="col-xs-12 pf-sep" style="padding-left:4px; padding-right:4px;"></div>
                            <div class="col-xs-8 col-sm-2" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control chosen chosen-select" name="bulk" id="bulk" data-placeholder="Bulk Action">
                                    <option value="">Bulk</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                            <div class="col-xs-4 col-sm-2" style="padding-left:4px; padding-right:4px;">
                                <button type="button" class="btn btn-default btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>" style="font-weight:600;"><i class="fa fa-check"></i> <?= cclang('apply_button'); ?></button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped" style="margin-bottom: 0; border: 1px solid #e7edf3; border-radius: 6px;">
                                <thead>
                                    <tr style="background: #f6f8fb; color: #52606d; font-size: 11px; text-transform: uppercase; letter-spacing: 0.02em;">
                                        <th width="5" style="border-bottom: 1px solid #e7edf3;">
                                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                                        </th>
                                        <th width="60" style="border-bottom: 1px solid #e7edf3; text-align: center;">Logo</th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;"><?= cclang('name'); ?> (Brand Name)</th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;"><?= cclang('code'); ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap;">Badan Hukum / PT</th>
                                        <th style="border-bottom: 1px solid #e7edf3; text-align: center; white-space: nowrap;">Active Campaigns</th>
                                        <th style="border-bottom: 1px solid #e7edf3; text-align: center; white-space: nowrap;">Products</th>
                                        <th style="border-bottom: 1px solid #e7edf3; text-align: center; white-space: nowrap;">Anggota Tim</th>
                                        <th style="border-bottom: 1px solid #e7edf3; text-align: center; white-space: nowrap;"><?= cclang('status'); ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; text-align: center; white-space: nowrap; min-width: 140px;"><?= cclang('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_brands">
                                    <?php if (!empty($brandss)): ?>
                                    <?php foreach($brandss as $brands): ?>
                                    <tr>
                                        <td width="5" style="vertical-align: middle;">
                                            <input type="checkbox" class="flat-red check" name="id[]" value="<?= $brands->id; ?>">
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <?php 
                                                $logo_src = !empty($brands->logo) && file_exists(FCPATH . $brands->logo) 
                                                    ? BASE_URL . $brands->logo 
                                                    : BASE_URL . 'asset/logo.png';
                                            ?>
                                            <img src="<?= $logo_src; ?>" alt="<?= _ent($brands->name); ?>" class="img-thumbnail" style="max-height: 38px; max-width: 55px; object-fit: contain; background: #fff; padding: 2px;">
                                        </td>
                                        <td style="vertical-align: middle; font-weight: 600;">
                                            <a href="<?= site_url('administrator/brands/view/' . $brands->id); ?>" style="color: #1e293b; font-size: 14px;">
                                                <?= _ent($brands->name); ?>
                                            </a>
                                            <?php if (!empty($brands->website)): ?>
                                                <div style="font-size: 11px; font-weight: normal; color: #64748b;">
                                                    <i class="fa fa-globe"></i> <?= _ent($brands->website); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <span class="label label-primary" style="font-family: monospace; font-size: 11px; letter-spacing: 0.5px;">
                                                <?= _ent($brands->code); ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <div style="font-weight: 600; color: #334155;"><?= _ent($brands->company_name ?: $brands->legal_entity_name ?: '-'); ?></div>
                                            <?php if (!empty($brands->email)): ?>
                                                <small class="text-muted"><i class="fa fa-envelope-o"></i> <?= _ent($brands->email); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <span class="badge bg-green" style="font-size: 12px; font-weight: 700; padding: 4px 8px;">
                                                <?= isset($brands->campaigns_count) ? (int)$brands->campaigns_count : (isset($metrics[$brands->id]['campaigns_count']) ? (int)$metrics[$brands->id]['campaigns_count'] : 0); ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <span class="badge bg-aqua" style="font-size: 12px; font-weight: 700; padding: 4px 8px;">
                                                <?= isset($brands->products_count) ? (int)$brands->products_count : (isset($metrics[$brands->id]['products_count']) ? (int)$metrics[$brands->id]['products_count'] : 0); ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <span class="badge bg-purple" style="font-size: 12px; font-weight: 700; padding: 4px 8px;">
                                                <?= isset($brands->team_members_count) ? (int)$brands->team_members_count : (isset($brands->members_count) ? (int)$brands->members_count : 0); ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <?php if ($brands->is_active): ?>
                                                <span class="label label-success" style="font-size: 11px;"><i class="fa fa-check-circle"></i> Active</span>
                                            <?php else: ?>
                                                <span class="label label-danger" style="font-size: 11px;"><i class="fa fa-ban"></i> Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center; white-space: nowrap;">
                                            <div style="display: inline-flex; gap: 4px;">
                                                <?php is_allowed('brands_view', function() use ($brands){ ?>
                                                    <a href="<?= site_url('administrator/brands/view/' . $brands->id); ?>" class="btn btn-xs btn-default btn-flat" title="View Detail"><i class="fa fa-eye"></i></a>
                                                <?php }) ?>
                                                <?php is_allowed('brands_update', function() use ($brands){ ?>
                                                    <a href="<?= site_url('administrator/brands/edit/' . $brands->id); ?>" class="btn btn-xs btn-primary btn-flat" title="<?= cclang('update_button'); ?>"><i class="fa fa-pencil"></i></a>
                                                <?php }) ?>
                                                <?php is_allowed('brands_delete', function() use ($brands){ ?>
                                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/brands/delete/' . $brands->id); ?>" class="btn btn-xs btn-danger btn-flat remove-data" title="<?= cclang('remove_button'); ?>"><i class="fa fa-trash"></i></a>
                                                <?php }) ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php if ($brands_counts == 0): ?>
                                    <tr>
                                        <td colspan="100" class="text-center text-muted" style="padding: 24px;">
                                            <i class="fa fa-info-circle fa-2x" style="display: block; margin-bottom: 6px; color: #cbd5e1;"></i>
                                            Brands data is not available
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
            </div>
        </div>
    </div>
</section>

<!-- Page script -->
<script>
$(document).ready(function(){
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

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
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function(isConfirm){
            if (!isConfirm) return;

            var postData = {};
            postData[csrfName] = csrfHash;

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: postData,
                success: function(res) {
                    if (res && res.success) {
                        swal({
                            title: "Berhasil!",
                            text: res.message || "Brand berhasil dihapus.",
                            type: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function(){ location.reload(); }, 1200);
                    } else {
                        swal("Gagal!", (res && res.message) ? res.message : "Tidak dapat menghapus brand.", "error");
                    }
                },
                error: function() {
                    window.location.href = url;
                }
            });
        });
        return false;
    });

    $('#check_all').click(function(){
        var isChecked = $(this).is(':checked');
        $('.check').prop('checked', isChecked);
    });

    $('#apply').click(function(){
        var bulkAction = $('#bulk').val();
        if (!bulkAction) {
            toastr['warning']("Pilih tindakan bulk terlebih dahulu.");
            return;
        }

        var checkedIds = [];
        $('.check:checked').each(function(){
            checkedIds.push($(this).val());
        });

        if (checkedIds.length === 0) {
            toastr['warning']("Pilih setidaknya satu brand.");
            return;
        }

        swal({
            title: "Konfirmasi Tindakan Bulk",
            text: "Apakah Anda yakin ingin menjalankan aksi '" + bulkAction + "' untuk " + checkedIds.length + " item?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Jalankan",
            cancelButtonText: "Batal",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function(isConfirm){
            if (bulkAction === 'delete') {
                var postData = {
                    ids: checkedIds,
                    action: bulkAction
                };
                postData[csrfName] = csrfHash;

                $.ajax({
                    url: BASE_URL + 'administrator/brands/bulk_action',
                    type: 'POST',
                    dataType: 'json',
                    data: postData,
                    success: function(res) {
                        if (res && res.success) {
                            swal("Berhasil!", res.message, "success");
                            setTimeout(function(){ location.reload(); }, 1200);
                        } else {
                            swal("Gagal!", (res && res.message) ? res.message : "Gagal menjalankan aksi bulk.", "error");
                        }
                    },
                    error: function() {
                        // Fallback to Cicool standard redirect pattern
                        document.location.href = BASE_URL + 'administrator/brands/delete?' + $('#form_brands').serialize();
                    }
                });
            }
        });
    });
});
</script>
