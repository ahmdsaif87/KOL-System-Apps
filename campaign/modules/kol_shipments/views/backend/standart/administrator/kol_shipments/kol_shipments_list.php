<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>

<script type="text/javascript">
// Binding hotkeys
function domo() {
    $('*').bind('keydown', 'Ctrl+a', function assets() {
        window.location.href = BASE_URL + '/administrator/Kol_shipments/add';
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
        <?= cclang('kol_shipments') ?> <small><?= cclang('list_all'); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= cclang('kol_shipments') ?></li>
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
                                <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;"><?= cclang('kol_shipments') ?></h3>
                                <div style="margin-top: 5px; color: #6b778c; font-size: 13px;">
                                    <?= cclang('list_all', [cclang('kol_shipments')]); ?> 
                                    <span style="display: inline-block; margin-left: 6px; padding: 2px 7px; border-radius: 10px; background: #e8f1fb; color: #1f4b6e; font-weight: 700;">
                                        <?= $kol_shipments_counts; ?> <?= cclang('items'); ?>
                                    </span>
                                </div>
                            </div>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                                <?php is_allowed('kol_shipments_add', function(){ ?>
                                    <a class="btn btn-primary btn-flat btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('kol_shipments')]); ?>" href="<?= site_url('administrator/kol_shipments/add'); ?>">
                                        <i class="fa fa-plus"></i> <?= cclang('add_new_button', [cclang('kol_shipments')]); ?>
                                    </a>
                                <?php }) ?>
                                <?php is_allowed('kol_shipments_export', function(){ ?>
                                    <a class="btn btn-default btn-flat" title="<?= cclang('export'); ?> <?= cclang('kol_shipments') ?>" href="<?= site_url('administrator/kol_shipments/export'); ?>">
                                        <i class="fa fa-file-excel-o"></i> <?= cclang('export'); ?> XLS
                                    </a>
                                <?php }) ?>
                                <?php is_allowed('kol_shipments_export', function(){ ?>
                                    <a class="btn btn-default btn-flat" title="<?= cclang('export'); ?> PDF <?= cclang('kol_shipments') ?>" href="<?= site_url('administrator/kol_shipments/export_pdf'); ?>">
                                        <i class="fa fa-file-pdf-o"></i> <?= cclang('export'); ?> PDF
                                    </a>
                                <?php }) ?>
                            </div>
                        </div>
                    </div>

                    <form name="form_kol_shipments" id="form_kol_shipments" action="<?= base_url('administrator/kol_shipments/index'); ?>">

                        <style>
                          .prod-filter-bar { margin-bottom: 14px; padding: 12px 12px 4px 12px; background: #f8fafc; border: 1px solid #e7edf3; border-radius: 6px; }
                          .prod-filter-bar .form-control { height: 38px; border-radius: 4px; margin-bottom: 8px; }
                          .prod-filter-bar .btn { width: 100%; min-height: 38px; margin-bottom: 8px; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
                          .prod-filter-bar .pf-sep { border-top: 1px dashed #e2e8f0; margin: 4px 0 12px 0; }
                          @media (max-width: 768px) {
                            #form_kol_shipments .prod-filter-bar > .pf-half { width: calc(50% - 3px) !important; max-width: calc(50% - 3px) !important; float: left !important; display: block !important; }
                            #form_kol_shipments .prod-filter-bar > .pf-half + .pf-half { margin-left: 6px !important; }
                            #form_kol_shipments .prod-filter-bar > .pf-bulkmain { width: calc(100% - 50px) !important; max-width: calc(100% - 50px) !important; float: left !important; display: block !important; }
                            #form_kol_shipments .prod-filter-bar > .pf-bulkbtn { width: 44px !important; max-width: 44px !important; float: left !important; display: block !important; margin-left: 6px !important; }
                            #form_kol_shipments button[name="sbtn"] { width: 100% !important; min-width: 0 !important; max-width: none !important; font-size: 13px !important; }
                            #form_kol_shipments button[name="sbtn"]::before { content: none !important; }
                            #form_kol_shipments a[name="reset"] { width: 100% !important; min-width: 0 !important; max-width: none !important; }
                            #form_kol_shipments a[name="reset"] .reset-txt { display: none !important; }
                          }
                        </style>

                        <div class="row prod-filter-bar" style="margin-left:0; margin-right:0;">
                            <div class="col-xs-12 col-sm-4" style="padding-left:4px; padding-right:4px;">
                                <input type="text" class="form-control" name="q" id="filter" placeholder="Cari penerima / resi / KOL / campaign..." value="<?= $this->input->get('q'); ?>">
                            </div>
                            <div class="col-xs-12 col-sm-4" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control chosen chosen-select" name="f" id="field" data-placeholder="Pilih Kolom">
                                    <option value=""><?= cclang('all'); ?> kolom</option>
                                    <option <?= $this->input->get('f') == 'campaign_kol_id' ? 'selected' : ''; ?> value="campaign_kol_id">Campaign / KOL</option>
                                    <option <?= $this->input->get('f') == 'recipient_name' ? 'selected' : ''; ?> value="recipient_name">Recipient Name</option>
                                    <option <?= $this->input->get('f') == 'shipping_address' ? 'selected' : ''; ?> value="shipping_address">Shipping Address</option>
                                    <option <?= $this->input->get('f') == 'courier' ? 'selected' : ''; ?> value="courier">Courier</option>
                                    <option <?= $this->input->get('f') == 'tracking_no' ? 'selected' : ''; ?> value="tracking_no">Tracking No</option>
                                    <option <?= $this->input->get('f') == 'status' ? 'selected' : ''; ?> value="status">Status</option>
                                    <option <?= $this->input->get('f') == 'shipped_at' ? 'selected' : ''; ?> value="shipped_at">Shipped At</option>
                                    <option <?= $this->input->get('f') == 'delivered_at' ? 'selected' : ''; ?> value="delivered_at">Delivered At</option>
                                </select>
                            </div>
                            <div class="col-xs-6 col-sm-2 pf-half" style="padding-left:4px; padding-right:4px;">
                                <button type="submit" class="btn btn-primary btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>" style="font-weight:600;"><i class="fa fa-search"></i> Filter</button>
                            </div>
                            <div class="col-xs-6 col-sm-2 pf-half" style="padding-left:4px; padding-right:4px;">
                                <a class="btn btn-default btn-flat" name="reset" id="reset" href="<?= base_url('administrator/kol_shipments'); ?>" title="<?= cclang('reset_filter'); ?>" style="font-weight:600;"><i class="fa fa-undo"></i><span class="reset-txt"> Reset</span></a>
                            </div>
                            <div class="col-xs-12 pf-sep" style="padding-left:4px; padding-right:4px;"></div>
                            <div class="col-xs-8 col-sm-3 pf-bulkmain" style="padding-left:4px; padding-right:4px;">
                                <select class="form-control chosen chosen-select" name="bulk" id="bulk" data-placeholder="Aksi Massal">
                                    <option value="">Aksi Massal</option>
                                    <optgroup label="Ubah Status Pengiriman">
                                        <option value="status_PENDING">Set Status: PENDING</option>
                                        <option value="status_PROCESSING">Set Status: PROCESSING</option>
                                        <option value="status_SHIPPED">Set Status: SHIPPED (Dikirim)</option>
                                        <option value="status_DELIVERED">Set Status: DELIVERED (Diterima)</option>
                                        <option value="status_RETURN_PENDING">Set Status: RETURN PENDING</option>
                                        <option value="status_RETURNED">Set Status: RETURNED</option>
                                    </optgroup>
                                    <optgroup label="Lainnya">
                                        <option value="delete">Hapus Terpilih</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-xs-4 col-sm-2 pf-bulkbtn" style="padding-left:4px; padding-right:4px;">
                                <button type="button" class="btn btn-default btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>" style="font-weight:600;"><i class="fa fa-check"></i> <?= cclang('apply_button'); ?></button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped" style="margin-bottom: 0; border: 1px solid #e7edf3; border-radius: 6px;">
                                <thead>
                                    <tr style="background: #f6f8fb; color: #52606d; font-size: 11px; text-transform: uppercase; letter-spacing: 0.02em;">
                                        <th width="5" style="border-bottom: 1px solid #e7edf3; vertical-align: middle;">
                                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="Pilih Semua">
                                        </th>
                                        <th style="border-bottom: 1px solid #e7edf3; min-width: 150px; white-space: nowrap; vertical-align: middle;">Campaign / KOL</th>
                                        <th style="border-bottom: 1px solid #e7edf3; min-width: 200px; white-space: nowrap; vertical-align: middle;">Barang Dikirim</th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap; vertical-align: middle;"><?= cclang('recipient_name') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap; vertical-align: middle;"><?= cclang('shipping_address') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap; vertical-align: middle;"><?= cclang('courier') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap; vertical-align: middle;"><?= cclang('tracking_no') ?></th>
                                        <th class="th-status" style="border-bottom: 1px solid #e7edf3; white-space: nowrap; vertical-align: middle; min-width: 150px;"><?= cclang('status') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap; vertical-align: middle;"><?= cclang('shipped_at') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap; vertical-align: middle;"><?= cclang('delivered_at') ?></th>
                                        <th style="border-bottom: 1px solid #e7edf3; white-space: nowrap; vertical-align: middle;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_kol_shipments">
                                    <?php foreach($kol_shipmentss as $kol_shipments): ?>
                                    <tr>
                                        <td width="5" style="vertical-align: middle;">
                                            <input type="checkbox" class="flat-red check" name="id[]" value="<?= $kol_shipments->id; ?>">
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?php if ($kol_shipments->campaign_kol_id): ?>
                                                <a href="<?= site_url('administrator/campaign_kols/view/' . $kol_shipments->campaign_kol_id . '?popup=show'); ?>" class="popup-view" style="text-decoration: none;" title="Lihat Detail KOL Campaign #<?= $kol_shipments->campaign_kol_id; ?>">
                                                    <div style="font-weight: 700; color: #0070ba; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                                                        <i class="fa fa-user-circle" style="font-size: 13px;"></i>
                                                        <?= _ent(!empty($kol_shipments->kol_name) ? $kol_name = $kol_shipments->kol_name : 'KOL #' . $kol_shipments->campaign_kols_kol_id); ?>
                                                    </div>
                                                    <?php if (!empty($kol_shipments->campaign_name)): ?>
                                                        <div style="font-size: 11px; color: #6b778c; margin-top: 2px;">
                                                            <i class="fa fa-bullhorn" style="font-size: 10px;"></i> <?= _ent($kol_shipments->campaign_name); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?php if (!empty($items_by_shipment[$kol_shipments->id])): ?>
                                                <?php 
                                                    $items = $items_by_shipment[$kol_shipments->id];
                                                    $total_items = count($items);
                                                    $total_qty = array_sum(array_map(function($i){ return (int)$i->qty; }, $items));
                                                    $first_item = $items[0];
                                                    
                                                    // Build popover html content for all products
                                                    $popover_content = '<div style="font-size:11px; text-align:left;">';
                                                    $popover_content .= '<div style="font-weight:700; border-bottom:1px solid #e7edf3; padding-bottom:5px; margin-bottom:8px; color:#172b4d;">Total: ' . $total_qty . ' pcs (' . $total_items . ' SKU)</div>';
                                                    foreach ($items as $it) {
                                                        $popover_content .= '<div style="margin-bottom:8px; padding-bottom:6px; border-bottom:1px dashed #e7edf3; line-height:1.3;">';
                                                        $popover_content .= '<div style="display:flex; justify-content:space-between; align-items:center; gap:6px; margin-bottom:3px;">';
                                                        $popover_content .= '<span class="label label-info" style="font-size:9px; font-weight:700;">' . htmlspecialchars($it->product_sku ?: 'PROD') . '</span>';
                                                        $popover_content .= '<span class="badge" style="background:#e8f1fb; color:#1f4b6e; font-size:10px; font-weight:600;">' . (int)$it->qty . ' pcs</span>';
                                                        $popover_content .= '</div>';
                                                        $popover_content .= '<div style="font-weight:600; color:#333; font-size:11px;">' . htmlspecialchars($it->product_name) . '</div>';
                                                        $popover_content .= '</div>';
                                                    }
                                                    $popover_content .= '</div>';
                                                ?>
                                                <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                                                    <span class="label label-default" style="background: #f4f7f9; color: #172b4d; border: 1px solid #d2d6de; font-size: 11px; font-weight: 600; padding: 3px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;" title="<?= htmlspecialchars($first_item->product_name); ?>">
                                                        <i class="fa fa-cube text-primary" style="font-size: 10px;"></i>
                                                        <?= _ent($first_item->product_sku ?: 'PROD'); ?>
                                                        <span class="badge" style="background: #e8f1fb; color: #1f4b6e; font-size: 10px; margin-left: 2px; padding: 1px 4px;"><?= (int)$first_item->qty; ?>x</span>
                                                    </span>

                                                    <?php if ($total_items > 1): ?>
                                                        <button type="button" class="btn btn-xs btn-default btn-flat shipment-items-popover" 
                                                                data-toggle="popover" 
                                                                data-html="true" 
                                                                data-trigger="hover focus" 
                                                                data-placement="top" 
                                                                data-title="<i class='fa fa-cubes text-primary'></i> Rincian Barang Dikirim" 
                                                                data-content="<?= htmlspecialchars($popover_content); ?>"
                                                                style="font-size: 10px; font-weight: 700; color: #0070ba; background: #e8f1fb; border-color: #b8daff; border-radius: 4px; padding: 2px 6px;">
                                                            +<?= ($total_items - 1); ?> lainnya (<?= $total_qty; ?> pcs)
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 11px;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle; font-weight: 600;"><?= _ent($kol_shipments->recipient_name); ?></td>
                                        <td style="vertical-align: middle; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 12px;" title="<?= htmlspecialchars($kol_shipments->shipping_address ?? '', ENT_QUOTES); ?>"><?= _ent($kol_shipments->shipping_address); ?></td>
                                        <td style="vertical-align: middle; white-space: nowrap;"><span class="label label-default" style="background:#f4f5f7; color:#333; border:1px solid #dcdcdc;"><?= _ent($kol_shipments->courier ?: '-'); ?></span></td>
                                        <td style="vertical-align: middle; white-space: nowrap; font-family: monospace; font-size: 12px;"><?= _ent($kol_shipments->tracking_no ?: '-'); ?></td>
                                        <td class="td-status" style="white-space: nowrap; vertical-align: middle; min-width: 150px;">
                                            <?php if ($this->aauth->is_allowed('kol_shipments_update')): ?>
                                                <select class="form-control input-sm quick-shipment-status" 
                                                        data-id="<?= $kol_shipments->id; ?>" 
                                                        data-original-status="<?= $kol_shipments->status; ?>"
                                                        style="border-radius: 4px; font-weight: 600; font-size: 11px; padding: 2px 22px 2px 6px; height: 28px; width: 145px; min-width: 145px; display: inline-block;">
                                                    <option value="PENDING" <?= $kol_shipments->status == 'PENDING' ? 'selected' : ''; ?>>PENDING</option>
                                                    <option value="PROCESSING" <?= $kol_shipments->status == 'PROCESSING' ? 'selected' : ''; ?>>PROCESSING</option>
                                                    <option value="SHIPPED" <?= $kol_shipments->status == 'SHIPPED' ? 'selected' : ''; ?>>SHIPPED</option>
                                                    <option value="DELIVERED" <?= $kol_shipments->status == 'DELIVERED' ? 'selected' : ''; ?>>DELIVERED</option>
                                                    <option value="RETURN_PENDING" <?= $kol_shipments->status == 'RETURN_PENDING' ? 'selected' : ''; ?>>RETURN PENDING</option>
                                                    <option value="RETURNED" <?= $kol_shipments->status == 'RETURNED' ? 'selected' : ''; ?>>RETURNED</option>
                                                </select>
                                            <?php else: ?>
                                                <?= render_status_badge($kol_shipments->status); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="col-shipped-at-<?= $kol_shipments->id; ?>" style="vertical-align: middle; white-space: nowrap; font-size: 11px; color: #555;"><?= _ent($kol_shipments->shipped_at ?: '-'); ?></td>
                                        <td class="col-delivered-at-<?= $kol_shipments->id; ?>" style="vertical-align: middle; white-space: nowrap; font-size: 11px; color: #555;"><?= _ent($kol_shipments->delivered_at ?: '-'); ?></td>
                                        <td width="140" style="vertical-align: middle; white-space: nowrap;">
                                            <div style="display: flex; gap: 4px; align-items: center; white-space: nowrap;">
                                                <?php is_allowed('kol_shipments_view', function() use ($kol_shipments){ ?>
                                                    <a href="<?= site_url('administrator/kol_shipments/single_pdf/' . $kol_shipments->id); ?>" class="btn btn-xs btn-default btn-flat" title="Unduh PDF Surat Jalan" target="_blank">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                    </a>
                                                    <a href="<?= site_url('administrator/kol_shipments/view/' . $kol_shipments->id); ?>" class="btn btn-xs btn-default btn-flat" title="Lihat Detail Pengiriman">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                <?php }) ?>
                                                <?php is_allowed('kol_shipments_update', function() use ($kol_shipments){ ?>
                                                    <?php if ($kol_shipments->status !== 'DELIVERED'): ?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-default btn-flat btn-quick-delivered" data-id="<?= $kol_shipments->id; ?>" title="Konfirmasi Diterima (DELIVERED)" style="color: #10b981; border-color: #10b981; padding: 7px 11px;">
                                                        <i class="fa fa-check-circle" style="font-size: 19px;"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <a href="<?= site_url('administrator/kol_shipments/edit/' . $kol_shipments->id); ?>" class="btn btn-xs btn-default btn-flat" title="Edit Data Pengiriman">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php }) ?>
                                                <?php is_allowed('kol_shipments_delete', function() use ($kol_shipments){ ?>
                                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/kol_shipments/delete/' . $kol_shipments->id); ?>" class="btn btn-xs btn-default btn-flat remove-data" title="Hapus Pengiriman">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php }) ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if ($kol_shipments_counts == 0): ?>
                                    <tr>
                                        <td colspan="100" class="text-center" style="padding: 20px; color: #8c9ba5;">
                                            <i class="fa fa-inbox fa-2x" style="display: block; margin-bottom: 8px;"></i>
                                            Belum ada data pengiriman produk (Kol Shipments).
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
    // Initialize bootstrap popover for shipped products
    $('.shipment-items-popover, [data-toggle="popover"]').popover({
        html: true,
        trigger: 'hover focus',
        container: 'body',
        template: '<div class="popover popover-custom-products" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    // Delete confirmation
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

    // Bulk actions
    $('#apply').click(function(){
        var bulk = $('#bulk');
        var bulkVal = bulk.val();
        var serialize_bulk = $('#form_kol_shipments').serialize();
        var selectedChecked = $('input.check:checked');

        if (bulkVal == '') {
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

        if (selectedChecked.length === 0) {
            swal({
                title: "Perhatian",
                text: "Silakan centang setidaknya satu data pengiriman terlebih dahulu.",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#3c8dbc",
                confirmButtonText: "OK",
                closeOnConfirm: true
            });
            return false;
        }

        if (bulkVal == 'delete') {
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
                    document.location.href = BASE_URL + '/administrator/kol_shipments/delete?' + serialize_bulk;      
                }
            });

            return false;
        } else if (bulkVal.indexOf('status_') === 0) {
            var targetStatus = bulkVal.replace('status_', '');
            var count = selectedChecked.length;

            swal({
                title: "Ubah Status Massal?",
                text: "Apakah Anda yakin ingin mengubah status " + count + " pengiriman terpilih menjadi " + targetStatus + "?",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#3c8dbc",
                confirmButtonText: "Ya, Ubah Status!",
                cancelButtonText: "Batal",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            },
            function(isConfirm){
                if (isConfirm) {
                    var selectedIds = [];
                    selectedChecked.each(function(){
                        selectedIds.push($(this).val());
                    });

                    var postData = {
                        id: selectedIds,
                        status: targetStatus
                    };
                    postData[csrfName] = csrfHash;

                    $.ajax({
                        url: BASE_URL + 'administrator/kol_shipments/bulk_update_status',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function(response) {
                            if (response.csrfHash) {
                                csrfHash = response.csrfHash;
                                $('input[name="' + csrfName + '"]').val(csrfHash);
                            }
                            if (response.success) {
                                swal({
                                    title: "Berhasil!",
                                    text: response.message,
                                    type: "success"
                                }, function() {
                                    window.location.reload();
                                });
                            } else {
                                swal("Gagal", response.message || "Gagal mengubah status massal.", "error");
                            }
                        },
                        error: function(xhr, status, err) {
                            swal("Error", "Terjadi kesalahan saat memproses permintaan: " + err, "error");
                        }
                    });
                }
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

    // Quick Status Dropdown Handler
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

    function updateShipmentSelectStyle($sel) {
        var val = $sel.val();
        var colors = {
            'PENDING': '#f39c12',
            'PROCESSING': '#00c0ef',
            'SHIPPED': '#3c8dbc',
            'DELIVERED': '#00a65a',
            'RETURN_PENDING': '#e08e0b',
            'RETURNED': '#dd4b39'
        };
        var color = colors[val] || '#333333';
        $sel.css({
            'border-left': '4px solid ' + color,
            'color': color,
            'background-color': '#fafbfc'
        });
    }

    $('.quick-shipment-status').each(function() {
        updateShipmentSelectStyle($(this));
    });

    $(document).on('change', '.quick-shipment-status', function() {
        var $select = $(this);
        var shipmentId = $select.data('id');
        var newStatus = $select.val();
        var oldStatus = $select.data('original-status');

        updateShipmentSelectStyle($select);
        $select.prop('disabled', true);

        var postData = {
            id: shipmentId,
            status: newStatus
        };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/kol_shipments/update_status',
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function(res) {
                $select.prop('disabled', false);
                if (res.csrfHash) {
                    csrfHash = res.csrfHash;
                }
                if (res.success) {
                    $select.data('original-status', newStatus);
                    updateShipmentSelectStyle($select);
                    if (typeof res.shipped_at !== 'undefined') {
                        $('.col-shipped-at-' + shipmentId).text(res.shipped_at || '');
                    }
                    if (typeof res.delivered_at !== 'undefined') {
                        $('.col-delivered-at-' + shipmentId).text(res.delivered_at || '');
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr['success'](res.message);
                    }
                } else {
                    $select.val(oldStatus);
                    updateShipmentSelectStyle($select);
                    if (typeof toastr !== 'undefined') {
                        toastr['error'](res.message || 'Gagal mengubah status pengiriman.');
                    } else {
                        alert(res.message);
                    }
                }
            },
            error: function(xhr) {
                $select.prop('disabled', false);
                $select.val(oldStatus);
                updateShipmentSelectStyle($select);
                if (typeof toastr !== 'undefined') {
                    toastr['error']('Terjadi kesalahan koneksi server.');
                } else {
                    alert('Terjadi kesalahan koneksi server.');
                }
            }
        });
    });

    // Phase 6: Quick Mark as DELIVERED
    $(document).on('click', '.btn-quick-delivered', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        swal({
            title: "Konfirmasi Paket Diterima?",
            text: "Status pengiriman #" + id + " akan diubah menjadi DELIVERED dan tanggal penerimaan dicatat otomatis.",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#10b981",
            confirmButtonText: "Ya, Paket Sudah Diterima",
            cancelButtonText: "Batal",
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                var postData = { id: id, status: 'DELIVERED' };
                postData[csrfName] = csrfHash;
                $.ajax({
                    url: BASE_URL + 'administrator/kol_shipments/update_status',
                    type: 'POST',
                    dataType: 'json',
                    data: postData,
                    success: function(res) {
                        if (res.success) {
                            swal({
                                title: "Terkonfirmasi!",
                                text: res.message,
                                type: "success"
                            }, function() {
                                location.reload();
                            });
                        } else {
                            swal("Gagal", res.message, "error");
                        }
                    }
                });
            }
        });
        return false;
    });
});
</script>